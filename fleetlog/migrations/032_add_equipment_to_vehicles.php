<?php
use FleetLog\Core\DB;

try {
    $columns = DB::fetchAll("SHOW COLUMNS FROM vehicles");
    $existing = array_column($columns, 'Field');

    if (!in_array('has_triangles', $existing)) {
        DB::query("ALTER TABLE vehicles ADD COLUMN has_triangles INT DEFAULT 0 AFTER status");
    }
    if (!in_array('has_vest', $existing)) {
        DB::query("ALTER TABLE vehicles ADD COLUMN has_vest INT DEFAULT 0 AFTER has_triangles");
    }
    if (!in_array('has_jack', $existing)) {
        DB::query("ALTER TABLE vehicles ADD COLUMN has_jack BOOLEAN DEFAULT FALSE AFTER has_vest");
    }
    if (!in_array('medical_kit_expiry', $existing)) {
        DB::query("ALTER TABLE vehicles ADD COLUMN medical_kit_expiry DATE DEFAULT NULL AFTER has_jack");
    }
    if (!in_array('has_tow_rope', $existing)) {
        DB::query("ALTER TABLE vehicles ADD COLUMN has_tow_rope BOOLEAN DEFAULT FALSE AFTER medical_kit_expiry");
    }
    if (!in_array('has_jumper_cables', $existing)) {
        DB::query("ALTER TABLE vehicles ADD COLUMN has_jumper_cables BOOLEAN DEFAULT FALSE AFTER has_tow_rope");
    }

} catch (\Exception $e) {
    error_log("Migration 032 failed: " . $e->getMessage());
}

return "SELECT 'Migration 032 handled internally' as result;";
