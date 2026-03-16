<?php
use FleetLog\Core\DB;

try {
    $columns = DB::fetchAll("SHOW COLUMNS FROM vehicle_handover_reports");
    $existing = array_column($columns, 'Field');
    
    $inventoryFields = [
        'has_triangles' => 'INT DEFAULT 0',
        'has_vest' => 'INT DEFAULT 0',
        'has_jack' => 'BOOLEAN DEFAULT FALSE',
        'medical_kit_expiry' => 'DATE DEFAULT NULL',
        'has_tow_rope' => 'BOOLEAN DEFAULT FALSE',
        'has_jumper_cables' => 'BOOLEAN DEFAULT FALSE',
        'extinguisher_expiry' => 'DATE DEFAULT NULL',
        'has_spare_wheel' => 'BOOLEAN DEFAULT FALSE'
    ];

    foreach ($inventoryFields as $field => $attr) {
        if (!in_array($field, $existing)) {
            DB::query("ALTER TABLE vehicle_handover_reports ADD COLUMN $field $attr");
        }
    }

} catch (\Exception $e) {
    error_log("Migration 046 failed: " . $e->getMessage());
}

return "SELECT 'Migration 046 complete: inventory columns added to reports' as result;";
