<?php
use FleetLog\Core\DB;

/**
 * Migration 040: Update expiry_alerts_track for driver support
 */
$columns = FleetLog\Core\DB::fetchAll("SHOW COLUMNS FROM expiry_alerts_track");
$columnNames = array_column($columns, 'Field');

if (!in_array('user_id', $columnNames)) {
    DB::query("ALTER TABLE expiry_alerts_track ADD COLUMN user_id INT NULL AFTER vehicle_id");
    DB::query("ALTER TABLE expiry_alerts_track ADD INDEX idx_user (user_id)");
}

// Ensure vehicle_id is nullable
DB::query("ALTER TABLE expiry_alerts_track MODIFY vehicle_id INT NULL");

return "Migration 040 complete: expiry_alerts_track now supports driver alerts (idempotent).";
