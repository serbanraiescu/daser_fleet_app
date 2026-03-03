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
 * Helper to process expiries for a specific type
 */
function checkType(array $vehicle, string $dateField, string $templateSlug) {
    if (empty($vehicle[$dateField])) return;

    // Fetch alert config
    $template = DB::fetch("SELECT alert_days FROM email_templates WHERE slug = ?", [$templateSlug]);
    if (!$template) return;

    $alertDays = array_map('intval', explode(',', $template['alert_days']));
    
    $expiryDate = new DateTime($vehicle[$dateField]);
    $today = new DateTime();
    $today->setTime(0, 0, 0);
    $expiryDate->setTime(0, 0, 0);

    $interval = $today->diff($expiryDate);
    $diffDays = (int)$interval->format('%r%a');

    // Only alert for future or today expiries (or very recently expired if we want, but let's stick to reminders)
    foreach ($alertDays as $day) {
        if ($diffDays == $day) {
            // Check if already sent for this day
            $alreadySent = DB::fetch("SELECT id FROM email_sent_track WHERE vehicle_id = ? AND template_slug = ? AND alert_day = ?", [
                $vehicle['id'], $templateSlug, $day
            ]);

            if (!$alreadySent) {
                echo " - Queuing alert for vehicle {$vehicle['license_plate']}, template $templateSlug, day $day\n";
                
                $placeholders = [
                    'vehicle' => $vehicle['make'] . ' ' . $vehicle['model'] . ' (' . $vehicle['license_plate'] . ')',
                    'vehicle_plate' => $vehicle['license_plate'],
                    'expiry_date' => date('d.m.Y', strtotime($vehicle[$dateField])),
                    'days' => $day
                ];

                if (Mailer::sendTemplate((int)$vehicle['tenant_id'], $templateSlug, $placeholders)) {
                    DB::query("INSERT INTO email_sent_track (tenant_id, vehicle_id, template_slug, alert_day) VALUES (?, ?, ?, ?)", [
                        $vehicle['tenant_id'], $vehicle['id'], $templateSlug, $day
                    ]);
                }
            }
        }
    }
}

// Fetch all active vehicles
$vehicles = DB::fetchAll("SELECT * FROM vehicles WHERE status = 'active'");

foreach ($vehicles as $v) {
    checkType($v, 'expiry_rca', 'expiry_alert_rca');
    checkType($v, 'expiry_itp', 'expiry_alert_itp');
    checkType($v, 'expiry_rovigneta', 'expiry_alert_rovigneta');
}

echo "Done.\n";
