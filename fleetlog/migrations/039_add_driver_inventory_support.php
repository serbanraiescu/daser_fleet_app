<?php
use FleetLog\Core\DB;

try {
    // 1. Add equipment_config to tenants
    $columns = DB::fetchAll("SHOW COLUMNS FROM tenants");
    $existing = array_column($columns, 'Field');
    if (!in_array('equipment_config', $existing)) {
        // Default: all items assigned to vehicle for zero disruption
        $defaultConfig = json_encode([
            'triangles' => 'vehicle',
            'vest' => 'vehicle',
            'jack' => 'vehicle',
            'medical_kit' => 'vehicle',
            'tow_rope' => 'vehicle',
            'jumper_cables' => 'vehicle',
            'extinguisher' => 'vehicle',
            'spare_wheel' => 'vehicle'
        ]);
        DB::query("ALTER TABLE tenants ADD COLUMN equipment_config JSON NULL");
        DB::query("UPDATE tenants SET equipment_config = ?", [$defaultConfig]);
    }

    // 2. Add equipment columns to users (drivers)
    $columns = DB::fetchAll("SHOW COLUMNS FROM users");
    $existing = array_column($columns, 'Field');
    
    $equipmentFields = [
        'has_triangles' => 'INT DEFAULT 0',
        'has_vest' => 'INT DEFAULT 0',
        'has_jack' => 'BOOLEAN DEFAULT FALSE',
        'medical_kit_expiry' => 'DATE DEFAULT NULL',
        'has_tow_rope' => 'BOOLEAN DEFAULT FALSE',
        'has_jumper_cables' => 'BOOLEAN DEFAULT FALSE',
        'extinguisher_expiry' => 'DATE DEFAULT NULL',
        'has_spare_wheel' => 'BOOLEAN DEFAULT FALSE'
    ];

    foreach ($equipmentFields as $field => $attr) {
        if (!in_array($field, $existing)) {
            DB::query("ALTER TABLE users ADD COLUMN $field $attr");
        }
    }

    // 3. Update vehicle_handover_reports for standalone protocols
    $columns = DB::fetchAll("SHOW COLUMNS FROM vehicle_handover_reports");
    $existing = array_column($columns, 'Field');
    
    if (!in_array('report_type', $existing)) {
        DB::query("ALTER TABLE vehicle_handover_reports ADD COLUMN report_type ENUM('handover', 'inventory') DEFAULT 'handover' AFTER id");
    }
    
    // Make vehicle_id nullable if it isn't (MySQL specific check might be needed, but usually ALTER works)
    DB::query("ALTER TABLE vehicle_handover_reports MODIFY COLUMN vehicle_id INT NULL");

} catch (\Exception $e) {
    error_log("Migration 039 failed: " . $e->getMessage());
}

return "SELECT 'Migration 039 handled internally' as result;";
