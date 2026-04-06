<?php

namespace FleetLog\Core;

use Exception;

/**
 * SMSService - Handles the SMS Queue for the Gateway Pattern
 */
class SMSService
{
    /**
     * Removes or replaces characters not compatible with basic GSM-7 encoding (like emojis)
     * to ensure reliable delivery and optimal message length.
     */
    public static function cleanForSms(string $text): string
    {
        // Replace common emojis used in the app with text/symbols
        $replacements = [
            '🛣️' => '[RUTE]',
            '⚡' => '[OPEN]',
            '⛽' => '[FUEL]',
            '📏' => '[DIST]',
            '👥' => '[DRIVERS]',
            '⚠️' => '[!!!]',
            '✅' => '[OK]',
            '❌' => '[X]',
            '📅' => '[DATE]',
            '🔔' => '[!]',
            '🚗' => '[CAR]',
            '🚚' => '[TRUCK]',
            '📱' => '[SMS]'
        ];

        $text = str_replace(array_keys($replacements), array_values($replacements), $text);

        // Transliterate Romanian diacritics to ASCII
        $diacritics = [
            'ă' => 'a', 'â' => 'a', 'î' => 'i', 'ș' => 's', 'ț' => 't',
            'Ă' => 'A', 'Â' => 'A', 'Î' => 'I', 'Ș' => 'S', 'Ț' => 'T'
        ];
        $text = strtr($text, $diacritics);

        // Remove any other remaining emoji-like or non-ASCII characters
        // We keep basic punctuation and letters. 
        // This is the safest approach for GSM-7.
        $text = preg_replace('/[^\x20-\x7E\n\r]/u', '', $text);
        
        return trim($text);
    }

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

    /**
     * Process daily reminders based on scheduled hour
     */
    public static function processDailyReminders(): void
    {
        $currentHour = (int)date('H');
        $currentDate = date('Y-m-d');

        // Find active reminders for this hour that haven't run today
        $reminders = DB::fetchAll("
            SELECT r.*, t.name as tenant_name 
            FROM sms_reminders r
            JOIN tenants t ON r.tenant_id = t.id
            WHERE r.is_active = 1 
              AND r.scheduled_hour = ? 
              AND (r.last_run_date IS NULL OR r.last_run_date != ?)
        ", [$currentHour, $currentDate]);

        foreach ($reminders as $rem) {
            try {
                // Fetch drivers for this tenant
                $drivers = DB::fetchAll("
                    SELECT phone, name 
                    FROM users 
                    WHERE tenant_id = ? 
                      AND role = 'driver' 
                      AND active = 1 
                      AND is_archived = 0 
                      AND phone IS NOT NULL 
                      AND phone != ''
                    GROUP BY phone
                ", [$rem['tenant_id']]);

                foreach ($drivers as $driver) {
                    $personalized = TemplateService::replace($rem['message'], [
                        'driver_name' => $driver['name'],
                        'company_name' => $rem['tenant_name']
                    ]);
                    self::enqueue($driver['phone'], self::cleanForSms($personalized));
                }

                // Mark as run
                DB::query("UPDATE sms_reminders SET last_run_date = ? WHERE id = ?", [$currentDate, $rem['id']]);
                
                error_log("SMSService::processDailyReminders - Executed reminder ID: {$rem['id']} for tenant: {$rem['tenant_id']}");
            } catch (Exception $e) {
                error_log("SMSService::processDailyReminders Error (ID: {$rem['id']}): " . $e->getMessage());
            }
        }
    }

    /**
     * Send daily summary reports to all enabled tenants
     * @param int|null $targetTenantId Optional ID for manual testing
     */
    public static function sendDailyTenantReports(?int $targetTenantId = null): void
    {
        $today = date('Y-m-d');
        
        $whereClause = "WHERE status = 'active'";
        $params = [];

        if ($targetTenantId) {
            $whereClause .= " AND id = ?";
            $params[] = $targetTenantId;
        } else {
            $whereClause .= " AND daily_report_enabled = 1 AND (daily_report_last_sent IS NULL OR daily_report_last_sent != ?)";
            $params[] = $today;
        }

        // Fetch tenants
        $tenants = DB::fetchAll("
            SELECT id, name, notification_phone, contact_phone 
            FROM tenants 
            $whereClause
        ", $params);

        foreach ($tenants as $t) {
            try {
                $tenantId = (int)$t['id'];
                $phone = !empty($t['notification_phone']) ? $t['notification_phone'] : $t['contact_phone'];
                
                if (empty($phone)) continue;

                // 1. Trips Stats
                $opened = DB::fetch("SELECT COUNT(*) as count FROM trips WHERE tenant_id = ? AND DATE(start_time) = ?", [$tenantId, $today])['count'];
                $closed = DB::fetch("SELECT COUNT(*) as count FROM trips WHERE tenant_id = ? AND DATE(end_time) = ? AND status = 'closed'", [$tenantId, $today])['count'];
                $unfinished = DB::fetch("SELECT COUNT(*) as count FROM trips WHERE tenant_id = ? AND status = 'open'", [$tenantId])['count'];
                
                // 2. Fuel Stats
                $fuel = DB::fetch("SELECT SUM(total_price) as total, SUM(liters) as liters FROM fuelings WHERE tenant_id = ? AND DATE(created_at) = ?", [$tenantId, $today]);
                $fuelCost = number_format($fuel['total'] ?? 0, 2);
                $liters = number_format($fuel['liters'] ?? 0, 2);
                $avgPrice = ($fuel['liters'] > 0) ? number_format($fuel['total'] / $fuel['liters'], 2) : "0.00";

                // 3. KM Stats
                $km = DB::fetch("SELECT SUM(end_km - start_km) as total FROM trips WHERE tenant_id = ? AND DATE(end_time) = ? AND status = 'closed'", [$tenantId, $today])['total'] ?? 0;
                $avgKm = ($closed > 0) ? number_format($km / $closed, 1) : "0";

                // 4. Driver Presence
                $activeDrivers = DB::fetch("SELECT COUNT(DISTINCT driver_id) as count FROM trips WHERE tenant_id = ? AND DATE(start_time) = ?", [$tenantId, $today])['count'];
                $totalDrivers = DB::fetch("SELECT COUNT(*) as count FROM users WHERE tenant_id = ? AND role = 'driver' AND active = 1 AND is_archived = 0", [$tenantId])['count'];
                $inactiveCount = $totalDrivers - $activeDrivers;

                // 5. Message Formatting (Ultra-Concise for reliability)
                $msg = "DASER Raport ".date('d.m', strtotime($today)).": "
                     . "Curse {$opened}/{$closed} ({$unfinished} open). "
                     . "Comb: {$fuelCost}L ({$liters}L). "
                     . "KM: {$km}. "
                     . "Drivers: {$activeDrivers}/{$totalDrivers} (Attn: {$inactiveCount}!)";

                if (self::enqueue($phone, self::cleanForSms($msg))) {
                    DB::query("UPDATE tenants SET daily_report_last_sent = ? WHERE id = ?", [$today, $tenantId]);
                    error_log("SMSService::sendDailyTenantReports - Sent to tenant: {$t['name']} ($tenantId)");
                }

            } catch (Exception $e) {
                error_log("SMSService::sendDailyTenantReports Error (Tenant: {$t['id']}): " . $e->getMessage());
            }
        }
    }
}
