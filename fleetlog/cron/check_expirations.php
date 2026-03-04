<?php

require_once __DIR__ . '/../core/Autoloader.php';
require_once __DIR__ . '/../core/EnvLoader.php';

use FleetLog\Core\Autoloader;
use FleetLog\Core\EnvLoader;
use FleetLog\Core\DB;
use FleetLog\Core\Mailer;

Autoloader::register();
EnvLoader::load(__DIR__ . '/../.env');

echo "Checking for expiring vehicle documentation...\n";

/**
 * Helper to process expiries for a specific type (Vehicle or User)
 */
function checkType(array $record, string $dateField, string $templateSlug, string $idType = 'vehicle_id') {
    if (empty($record[$dateField])) return;

    // Fetch alert config
    $template = DB::fetch("SELECT alert_days, recipient_type FROM email_templates WHERE slug = ?", [$templateSlug]);
    if (!$template) return;

    $alertDays = array_map('intval', explode(',', $template['alert_days']));
    
    $expiryDate = new DateTime($record[$dateField]);
    $today = new DateTime();
    $today->setTime(0, 0, 0);
    $expiryDate->setTime(0, 0, 0);

    $interval = $today->diff($expiryDate);
    $diffDays = (int)$interval->format('%r%a');

    foreach ($alertDays as $day) {
        if ($diffDays == $day) {
            // Check if already sent
            $where = ($idType === 'vehicle_id') ? "vehicle_id = ?" : "user_id = ?";
            $alreadySent = DB::fetch("SELECT id FROM email_sent_track WHERE $where AND template_slug = ? AND alert_day = ?", [
                $record['id'], $templateSlug, $day
            ]);

            if (!$alreadySent) {
                echo " - Queuing alert for " . ($idType === 'vehicle_id' ? "vehicle {$record['license_plate']}" : "driver {$record['name']}") . ", template $templateSlug, day $day\n";
                
                $placeholders = [
                    'vehicle' => ($record['make'] ?? '') . ' ' . ($record['model'] ?? '') . ' (' . ($record['license_plate'] ?? '') . ')',
                    'vehicle_plate' => $record['license_plate'] ?? '',
                    'driver_name' => $record['name'] ?? 'Unknown',
                    'expiry_date' => date('d.m.Y', strtotime($record[$dateField])),
                    'days' => $day
                ];

                // Determine recipient
                $explicitTo = null;
                if ($idType === 'user_id' && !empty($record['email'])) {
                    $explicitTo = $record['email'];
                }

                if (Mailer::sendTemplate((int)$record['tenant_id'], $templateSlug, $placeholders, false, $explicitTo)) {
                    $col = ($idType === 'vehicle_id') ? 'vehicle_id' : 'user_id';
                    DB::query("INSERT INTO email_sent_track (tenant_id, $col, template_slug, alert_day) VALUES (?, ?, ?, ?)", [
                        $record['tenant_id'], $record['id'], $templateSlug, $day
                    ]);
                }
            }
        }
    }
}

// 1. VEHICLE EXPIRATIONS
$vehicles = DB::fetchAll("SELECT * FROM vehicles WHERE status = 'active'");
foreach ($vehicles as $v) {
    checkType($v, 'expiry_rca', 'expiry_alert_rca');
    checkType($v, 'expiry_itp', 'expiry_alert_itp');
    checkType($v, 'expiry_rovigneta', 'expiry_alert_rovigneta');
    checkType($v, 'medical_kit_expiry', 'expiry_alert_medical_kit');
    checkType($v, 'extinguisher_expiry', 'expiry_alert_extinguisher');
}

// 2. DRIVER EXPIRATIONS
$drivers = DB::fetchAll("SELECT * FROM users WHERE role = 'driver' AND active = 1");
foreach ($drivers as $d) {
    checkType($d, 'id_expiry', 'expiry_alert_id', 'user_id');
    checkType($d, 'license_expiry', 'expiry_alert_license', 'user_id');
}

// 3. Record last run time
DB::query("INSERT INTO system_settings (`key`, `value`) VALUES ('last_cron_run', NOW()) ON DUPLICATE KEY UPDATE `value` = NOW()");

echo "Done.\n";
