<?php

require_once __DIR__ . '/../core/Autoloader.php';
require_once __DIR__ . '/../core/EnvLoader.php';

use FleetLog\Core\Autoloader;
use FleetLog\Core\EnvLoader;
use FleetLog\Core\DB;

Autoloader::register();
EnvLoader::load(__DIR__ . '/../.env');

$reportsTo = getenv('REPORTS_TO_EMAIL');
if (!$reportsTo) {
    die("REPORTS_TO_EMAIL not set in .env\n");
}

$tenants = DB::fetchAll("SELECT * FROM tenants WHERE status = 'active'");
$summary = "Weekly FleetLog Summary Report - " . date('Y-m-d') . "\n";
$summary .= str_repeat("=", 40) . "\n\n";

foreach ($tenants as $tenant) {
    $tenantId = $tenant['id'];
    $tripsCount = DB::fetch("SELECT COUNT(*) as count FROM trips WHERE tenant_id = ? AND created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)", [$tenantId])['count'];
    $kmTotal = DB::fetch("SELECT SUM(end_km - start_km) as km FROM trips WHERE tenant_id = ? AND status = 'closed' AND created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)", [$tenantId])['km'] ?: 0;
    $fuelLiters = DB::fetch("SELECT SUM(liters) as liters FROM fuel_entries WHERE tenant_id = ? AND created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)", [$tenantId])['liters'] ?: 0;
    $damages = DB::fetch("SELECT COUNT(*) as count FROM damage_reports WHERE tenant_id = ? AND created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)", [$tenantId])['count'];

    $summary .= "TENANT: {$tenant['name']}\n";
    $summary .= "- Trips: $tripsCount\n";
    $summary .= "- Total KM: $kmTotal\n";
    $summary .= "- Fuel: $fuelLiters liters\n";
    $summary .= "- New Incidents: $damages\n";
    $summary .= str_repeat("-", 20) . "\n\n";
}

$headers = 'From: ' . getenv('MAIL_FROM') . "\r\n" .
           'X-Mailer: PHP/' . phpversion();

if (mail($reportsTo, "Weekly FleetLog Report", $summary, $headers)) {
    echo "Report sent to $reportsTo\n";
} else {
    echo "Failed to send report.\n";
}
