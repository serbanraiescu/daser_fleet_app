<?php

namespace FleetLog\Core;

use Exception;

/**
 * SMSService - Handles the SMS Queue for the Gateway Pattern
 */
class SMSService
{
    /**
     * Enqueue an SMS message
     * 
     * @param string $to Recipient phone number
     * @param string $message Message content
     * @return int|bool ID of the enqueued SMS or false on failure
     */
    public static function enqueue(string $to, string $message): int|bool
    {
        // Simple numeric normalization (optional, can be expanded)
        $to = trim($to);
        if (str_starts_with($to, '07') && strlen($to) === 10) {
            $to = '+40' . substr($to, 1);
        }

        // Avoid exact duplicates pending today
        $existing = DB::fetch(
            "SELECT id FROM sms_queue WHERE phone = ? AND message = ? AND status = 'pending' AND DATE(created_at) = CURDATE()",
            [$to, $message]
        );

        if ($existing) {
            return (int)$existing['id'];
        }

        try {
            DB::query(
                "INSERT INTO sms_queue (phone, message, status, created_at) VALUES (?, ?, 'pending', NOW())",
                [$to, $message]
            );
            return (int)DB::lastInsertId();
        } catch (Exception $e) {
            error_log("SMSService Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get pending messages for the gateway
     * 
     * @param int $limit Max messages to fetch
     * @return array
     */
    public static function getPending(int $limit = 5): array
    {
        try {
            // Use a transaction to mark as 'sending' immediately
            $pdo = \FleetLog\Core\DB::getInstance();
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("SELECT id, phone, message FROM sms_queue WHERE status = 'pending' ORDER BY id ASC LIMIT ? FOR UPDATE");
            $stmt->execute([$limit]);
            $messages = $stmt->fetchAll();

            if (!empty($messages)) {
                $ids = array_column($messages, 'id');
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $update = $pdo->prepare("UPDATE sms_queue SET status = 'sending' WHERE id IN ($placeholders)");
                $update->execute($ids);
            }

            $pdo->commit();
            return $messages;
        } catch (Exception $e) {
            if (isset($pdo)) $pdo->rollBack();
            error_log("SMSService getPending Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Confirm SMS was sent
     */
    public static function confirm(int $id): bool
    {
        try {
            DB::query("UPDATE sms_queue SET status = 'sent', sent_at = NOW() WHERE id = ?", [$id]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Mark SMS as failed
     */
    public static function fail(int $id): bool
    {
        try {
            DB::query("UPDATE sms_queue SET status = 'failed' WHERE id = ?", [$id]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    /**
     * Enqueue an SMS using a template key
     */
    public static function enqueueFromTemplate(string $to, string $templateKey, array $data): int|bool
    {
        $template = TemplateService::getTemplate($templateKey);
        if (!$template) {
            // Fallback if template doesn't exist
            return false;
        }

        $message = TemplateService::replace($template['message_body'], $data);
        return self::enqueue($to, $message);
    }

    /**
     * Scan for expirations and enqueue SMS based on template milestones
     * 
     * @return array [enqueuedCount, skippedTenants]
     */
    public static function processExpiryAlerts(): array
    {
        $enqueuedCount = 0;
        $skippedTenants = [];

        // 1. Get universal template and its alert days
        $template = TemplateService::getTemplate('universal_expiry');
        if (!$template) return [0, []];

        $alertDays = array_map('trim', explode(',', $template['alert_days'] ?? '30,7,3,1'));
        $maxDays = max($alertDays);

        // Fetch Tenant Configs
        $tenantsRaw = DB::fetchAll("SELECT id, name, contact_phone, notification_phone, equipment_config FROM tenants");
        $tenants = [];
        foreach ($tenantsRaw as $t) {
            $tenants[$t['id']] = [
                'id' => (int)$t['id'],
                'name' => $t['name'],
                'phone' => !empty($t['notification_phone']) ? $t['notification_phone'] : $t['contact_phone'],
                'config' => json_decode($t['equipment_config'] ?? '[]', true)
            ];
        }

        // 2. Scan Vehicles
        $expiringVehicles = DB::fetchAll("
            SELECT v.id as vehicle_id, v.license_plate, v.expiry_rca, v.expiry_itp, v.expiry_rovigneta, 
                   v.medical_kit_expiry, v.extinguisher_expiry, v.tenant_id
            FROM vehicles v
            WHERE v.status != 'archived'
        ");

        foreach ($expiringVehicles as $v) {
            $t = $tenants[$v['tenant_id']] ?? null;
            if (!$t || empty($t['phone'])) {
                if ($t) $skippedTenants[$t['name']] = true;
                continue;
            }

            $docTypes = [
                'RCA' => $v['expiry_rca'],
                'ITP' => $v['expiry_itp'],
                'Rovigneta' => $v['expiry_rovigneta']
            ];

            // Add equipment IF assigned to vehicle
            if (($t['config']['medical_kit'] ?? 'vehicle') === 'vehicle') {
                $docTypes['Trusă Medicală'] = $v['medical_kit_expiry'];
            }
            if (($t['config']['extinguisher'] ?? 'vehicle') === 'vehicle') {
                $docTypes['Stingător'] = $v['extinguisher_expiry'];
            }

            foreach ($docTypes as $type => $date) {
                if (!$date) continue;
                self::processAlertMilestones($v['vehicle_id'], null, $type, $date, $v['license_plate'], 'Vehicul', $t, $alertDays, $enqueuedCount);
            }
        }

        // 3. Scan Drivers
        $expiringDrivers = DB::fetchAll("
            SELECT u.id as user_id, u.name as driver_name, u.medical_kit_expiry, u.extinguisher_expiry, u.tenant_id
            FROM users u
            WHERE u.role = 'driver' AND u.active = 1
        ");

        foreach ($expiringDrivers as $u) {
            $t = $tenants[$u['tenant_id']] ?? null;
            if (!$t || empty($t['phone'])) {
                if ($t) $skippedTenants[$t['name']] = true;
                continue;
            }

            $docTypes = [];
            if (($t['config']['medical_kit'] ?? 'vehicle') === 'driver') {
                $docTypes['Trusă Medicală'] = $u['medical_kit_expiry'];
            }
            if (($t['config']['extinguisher'] ?? 'vehicle') === 'driver') {
                $docTypes['Stingător'] = $u['extinguisher_expiry'];
            }

            foreach ($docTypes as $type => $date) {
                if (!$date) continue;
                self::processAlertMilestones(null, $u['user_id'], $type, $date, $u['driver_name'], 'Șofer', $t, $alertDays, $enqueuedCount);
            }
        }

        return [$enqueuedCount, array_keys($skippedTenants)];
    }

    private static function processAlertMilestones($vehicleId, $userId, $type, $date, $assetName, $assetType, $tenant, $alertDays, &$enqueuedCount): void
    {
        $expiryTimestamp = strtotime($date);
        $daysLeft = ceil(($expiryTimestamp - time()) / 86400);

        foreach ($alertDays as $milestone) {
            if ($daysLeft == (int)$milestone) {
                $sql = $vehicleId 
                    ? "SELECT id FROM expiry_alerts_track WHERE vehicle_id = ? AND expiry_type = ? AND expiry_date = ? AND alert_day = ?"
                    : "SELECT id FROM expiry_alerts_track WHERE user_id = ? AND expiry_type = ? AND expiry_date = ? AND alert_day = ?";
                
                $id = $vehicleId ?: $userId;
                $tracked = DB::fetch($sql, [$id, $type, $date, (int)$milestone]);

                if (!$tracked) {
                    $data = [
                        'expiry_type' => $type,
                        'asset_name' => $assetName,
                        'expiry_date' => date('d.m.Y', $expiryTimestamp),
                        'days_left' => $daysLeft,
                        'vehicle_id' => $vehicleId,
                        'driver_name' => $assetType === 'Șofer' ? $assetName : 'N/A',
                        'company_name' => $tenant['name'],
                        'asset_type' => $assetType,
                        'phone_number' => $tenant['phone'],
                        'vehicle_plate' => $assetType === 'Vehicul' ? $assetName : 'N/A'
                    ];

                    if (self::enqueueFromTemplate($tenant['phone'], 'universal_expiry', $data)) {
                        DB::query(
                            "INSERT INTO expiry_alerts_track (tenant_id, vehicle_id, user_id, expiry_type, expiry_date, alert_day) VALUES (?, ?, ?, ?, ?, ?)",
                            [$tenant['id'], $vehicleId, $userId, $type, $date, (int)$milestone]
                        );
                        $enqueuedCount++;
                        break;
                    }
                }
            }
        }
    }
}
