<?php
use FleetLog\Core\DB;

/**
 * Migration 040: Update expiry_alerts_track for driver support
 */
DB::query("ALTER TABLE expiry_alerts_track MODIFY vehicle_id INT NULL");
DB::query("ALTER TABLE expiry_alerts_track ADD COLUMN user_id INT NULL AFTER vehicle_id");
DB::query("ALTER TABLE expiry_alerts_track ADD INDEX idx_user (user_id)");

return "Migration 040 complete: expiry_alerts_track now supports driver alerts.";
