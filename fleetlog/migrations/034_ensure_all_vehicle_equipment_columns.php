<?php
use FleetLog\Core\DB;

try {
    $columns = DB::fetchAll("SHOW COLUMNS FROM vehicles");
    $existing = array_column($columns, 'Field');

    $missing = [];
    
    // Check for all equipment columns introduced in Phase 11
    if (!in_array('has_triangles', $existing)) {
        DB::query("ALTER TABLE vehicles ADD COLUMN has_triangles INT DEFAULT 0 AFTER status");
        $missing[] = 'has_triangles';
    }
    if (!in_array('has_vest', $existing)) {
        DB::query("ALTER TABLE vehicles ADD COLUMN has_vest INT DEFAULT 0 AFTER has_triangles");
        $missing[] = 'has_vest';
    }
    if (!in_array('has_jack', $existing)) {
        DB::query("ALTER TABLE vehicles ADD COLUMN has_jack BOOLEAN DEFAULT FALSE AFTER has_vest");
        $missing[] = 'has_jack';
    }
    if (!in_array('medical_kit_expiry', $existing)) {
        DB::query("ALTER TABLE vehicles ADD COLUMN medical_kit_expiry DATE DEFAULT NULL AFTER has_jack");
        $missing[] = 'medical_kit_expiry';
    }
    if (!in_array('has_tow_rope', $existing)) {
        DB::query("ALTER TABLE vehicles ADD COLUMN has_tow_rope BOOLEAN DEFAULT FALSE AFTER medical_kit_expiry");
        $missing[] = 'has_tow_rope';
    }
    if (!in_array('has_jumper_cables', $existing)) {
        DB::query("ALTER TABLE vehicles ADD COLUMN has_jumper_cables BOOLEAN DEFAULT FALSE AFTER has_tow_rope");
        $missing[] = 'has_jumper_cables';
    }
    if (!in_array('extinguisher_expiry', $existing)) {
        DB::query("ALTER TABLE vehicles ADD COLUMN extinguisher_expiry DATE DEFAULT NULL AFTER has_jumper_cables");
        $missing[] = 'extinguisher_expiry';
    }
    if (!in_array('has_spare_wheel', $existing)) {
        DB::query("ALTER TABLE vehicles ADD COLUMN has_spare_wheel BOOLEAN DEFAULT TRUE AFTER extinguisher_expiry");
        $missing[] = 'has_spare_wheel';
    }

    if (empty($missing)) {
        return "SELECT 'All columns already exist' as result;";
    } else {
        return "SELECT 'Added missing columns: " . implode(', ', $missing) . "' as result;";
    }

} catch (\Exception $e) {
    error_log("Migration 034 failed: " . $e->getMessage());
    return "SELECT 'Error: " . $e->getMessage() . "' as result;";
}
