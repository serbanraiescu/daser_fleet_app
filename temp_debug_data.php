<?php
require_once __DIR__ . '/fleetlog/core/DB.php';
require_once __DIR__ . '/fleetlog/core/Auth.php';

use FleetLog\Core\DB;

try {
    // We'll check for all vehicles in the first tenant (usually ID 1) since we can't easily get Auth status here
    $vehicles = DB::fetchAll("SELECT id, license_plate, medical_kit_expiry, extinguisher_expiry FROM vehicles WHERE status != 'archived'");
    
    echo "--- VEHICLE EQUIPMENT DATA ---\n";
    foreach ($vehicles as $v) {
        echo "ID: " . $v['id'] . " | Plate: " . $v['license_plate'] . "\n";
        echo "  Medical Kit Expiry: " . ($v['medical_kit_expiry'] ?? 'NULL') . "\n";
        echo "  Extinguisher Expiry: " . ($v['extinguisher_expiry'] ?? 'NULL') . "\n";
        echo "-----------------------------\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
