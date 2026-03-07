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

        // 2. Find expiring docs grouped by tenant
        $expiring = DB::fetchAll("
            SELECT v.id as vehicle_id, v.license_plate, v.expiry_rca, v.expiry_itp, v.expiry_rovigneta,
                   t.id as tenant_id, t.name as tenant_name, t.contact_phone, t.notification_phone
            FROM vehicles v
            JOIN tenants t ON v.tenant_id = t.id
            WHERE v.status != 'archived'
            AND (
                (expiry_rca IS NOT NULL AND expiry_rca <= DATE_ADD(CURRENT_DATE(), INTERVAL ? DAY)) OR 
                (expiry_itp IS NOT NULL AND expiry_itp <= DATE_ADD(CURRENT_DATE(), INTERVAL ? DAY)) OR 
                (expiry_rovigneta IS NOT NULL AND expiry_rovigneta <= DATE_ADD(CURRENT_DATE(), INTERVAL ? DAY))
            )
        ", [$maxDays, $maxDays, $maxDays]);

        foreach ($expiring as $v) {
            $recipientPhone = !empty($v['notification_phone']) ? $v['notification_phone'] : $v['contact_phone'];

            if (empty($recipientPhone)) {
                $skippedTenants[$v['tenant_id']] = $v['tenant_name'];
                continue;
            }

            $docTypes = [
                'RCA' => $v['expiry_rca'],
                'ITP' => $v['expiry_itp'],
                'Rovigneta' => $v['expiry_rovigneta']
            ];

            foreach ($docTypes as $type => $date) {
                if (!$date) continue;

                $expiryTimestamp = strtotime($date);
                $daysLeft = ceil(($expiryTimestamp - time()) / 86400);

                // Check each milestone
                foreach ($alertDays as $milestone) {
                    if ($daysLeft <= (int)$milestone && $daysLeft >= 0) {
                        // Check if already tracked for this SPECIFIC milestone + date
                        $tracked = DB::fetch(
                            "SELECT id FROM expiry_alerts_track WHERE vehicle_id = ? AND expiry_type = ? AND expiry_date = ? AND alert_day = ?",
                            [$v['vehicle_id'], $type, $date, (int)$milestone]
                        );

                        if (!$tracked) {
                            $data = [
                                'expiry_type' => $type,
                                'vehicle_plate' => $v['license_plate'],
                                'expiry_date' => date('d.m.Y', $expiryTimestamp),
                                'days_left' => $daysLeft,
                                'vehicle_id' => $v['vehicle_id'],
                                'driver_name' => $v['driver_name'] ?? 'N/A',
                                'company_name' => $v['tenant_name'],
                                'asset_name' => $v['license_plate'],
                                'asset_type' => 'Vehicul',
                                'phone_number' => $recipientPhone
                            ];

                            if (self::enqueueFromTemplate($recipientPhone, 'universal_expiry', $data)) {
                                DB::query(
                                    "INSERT INTO expiry_alerts_track (tenant_id, vehicle_id, expiry_type, expiry_date, alert_day) VALUES (?, ?, ?, ?, ?)",
                                    [$v['tenant_id'], $v['vehicle_id'], $type, $date, (int)$milestone]
                                );
                                $enqueuedCount++;
                                break; // Only send the closest milestone SMS once per scan
                            }
                        }
                    }
                }
            }
        }

        return [$enqueuedCount, array_unique($skippedTenants)];
    }
}
