<?php

namespace FleetLog\Core;

use DateTime;
use Exception;

class ExpirationService
{
    /**
     * Run all email-based documentation expiry checks
     */
    public static function runEmailExpiryChecks(): int
    {
        $count = 0;
        
        // 1. VEHICLE EXPIRATIONS
        $vehicles = DB::fetchAll("SELECT * FROM vehicles WHERE status = 'active'");
        foreach ($vehicles as $v) {
            $count += self::checkType($v, 'expiry_rca', 'expiry_alert_rca');
            $count += self::checkType($v, 'expiry_itp', 'expiry_alert_itp');
            $count += self::checkType($v, 'expiry_rovigneta', 'expiry_alert_rovigneta');
            $count += self::checkType($v, 'medical_kit_expiry', 'expiry_alert_medical_kit');
            $count += self::checkType($v, 'extinguisher_expiry', 'expiry_alert_extinguisher');
        }

        // 2. DRIVER EXPIRATIONS
        $drivers = DB::fetchAll("SELECT * FROM users WHERE role = 'driver' AND active = 1");
        foreach ($drivers as $d) {
            $count += self::checkType($d, 'id_expiry', 'expiry_alert_id', 'user_id');
            $count += self::checkType($d, 'license_expiry', 'expiry_alert_license', 'user_id');
        }

        return $count;
    }

    /**
     * Internal helper to process expiries for a specific type
     */
    private static function checkType(array $record, string $dateField, string $templateSlug, string $idType = 'vehicle_id'): int
    {
        if (empty($record[$dateField])) return 0;

        $template = DB::fetch("SELECT alert_days, recipient_type FROM email_templates WHERE slug = ?", [$templateSlug]);
        if (!$template) return 0;

        $alertDays = array_map('intval', explode(',', $template['alert_days']));
        
        $expiryDate = new DateTime($record[$dateField]);
        $today = new DateTime();
        $today->setTime(0, 0, 0);
        $expiryDate->setTime(0, 0, 0);

        $interval = $today->diff($expiryDate);
        $diffDays = (int)$interval->format('%r%a');

        $queued = 0;

        foreach ($alertDays as $day) {
            if ($diffDays == $day) {
                // Check if already sent
                $where = ($idType === 'vehicle_id') ? "vehicle_id = ?" : "user_id = ?";
                $alreadySent = DB::fetch("SELECT id FROM email_sent_track WHERE $where AND template_slug = ? AND alert_day = ?", [
                    $record['id'], $templateSlug, $day
                ]);

                if (!$alreadySent) {
                    $placeholders = [
                        'vehicle' => ($record['make'] ?? '') . ' ' . ($record['model'] ?? '') . ' (' . ($record['license_plate'] ?? '') . ')',
                        'vehicle_plate' => $record['license_plate'] ?? '',
                        'driver_name' => $record['name'] ?? 'Unknown',
                        'expiry_date' => date('d.m.Y', strtotime($record[$dateField])),
                        'days' => $day
                    ];

                    $explicitTo = null;
                    if ($idType === 'user_id' && !empty($record['email'])) {
                        $explicitTo = $record['email'];
                    }

                    if (Mailer::sendTemplate((int)$record['tenant_id'], $templateSlug, $placeholders, false, $explicitTo)) {
                        $col = ($idType === 'vehicle_id') ? 'vehicle_id' : 'user_id';
                        DB::query("INSERT INTO email_sent_track (tenant_id, $col, template_slug, alert_day) VALUES (?, ?, ?, ?)", [
                            $record['tenant_id'], $record['id'], $templateSlug, $day
                        ]);
                        $queued++;
                    }
                }
            }
        }
        return $queued;
    }
}
