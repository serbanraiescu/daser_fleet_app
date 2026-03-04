<?php
require_once __DIR__ . '/fleetlog/core/Autoloader.php';
require_once __DIR__ . '/fleetlog/core/EnvLoader.php';
use FleetLog\Core\Autoloader;
use FleetLog\Core\EnvLoader;
use FleetLog\Core\DB;
Autoloader::register();
EnvLoader::load(__DIR__ . '/fleetlog/.env');

header('Content-Type: text/plain');

echo "--- TEMPLATES ---\n";
$templates = DB::fetchAll("SELECT slug, alert_days FROM email_templates WHERE slug LIKE 'expiry_alert_%'");
foreach ($templates as $t) {
    echo "{$t['slug']}: [{$t['alert_days']}]\n";
}

echo "\n--- VEHICLE 1 DATA ---\n";
$v = DB::fetch("SELECT id, license_plate, expiry_rca, expiry_itp, expiry_rovigneta FROM vehicles WHERE id = 1");
print_r($v);

echo "\n--- SENT TRACK FOR VEHICLE 1 ---\n";
$track = DB::fetchAll("SELECT * FROM email_sent_track WHERE vehicle_id = 1");
print_r($track);

$today = new DateTime();
$today->setTime(0, 0, 0);
echo "\nToday: " . $today->format('Y-m-d') . "\n";

foreach (['expiry_rca', 'expiry_itp', 'expiry_rovigneta'] as $field) {
    $expiry = new DateTime($v[$field]);
    $expiry->setTime(0, 0, 0);
    $diff = $today->diff($expiry);
    echo "$field ({$v[$field]}) diff: " . $diff->format('%r%a') . " days\n";
}
