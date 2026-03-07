<?php
use FleetLog\Core\DB;

/**
 * Migration 039: Expand SMS system for multi-milestone alerts
 */

// Add alert_days to templates if it doesn't exist
$columns = DB::fetchAll("SHOW COLUMNS FROM sms_queue_templates");
$fields = array_column($columns, 'Field');

if (!in_array('alert_days', $fields)) {
    DB::query("ALTER TABLE sms_queue_templates ADD COLUMN alert_days VARCHAR(255) DEFAULT '30,7,3,1' AFTER message_body");
}

// Add alert_day to track table if it doesn't exist
$trackColumns = DB::fetchAll("SHOW COLUMNS FROM expiry_alerts_track");
$trackFields = array_column($trackColumns, 'Field');

if (!in_array('alert_day', $trackFields)) {
    DB::query("ALTER TABLE expiry_alerts_track ADD COLUMN alert_day INT DEFAULT 30 AFTER expiry_type");
    // Update index to include alert_day
    DB::query("ALTER TABLE expiry_alerts_track DROP INDEX idx_check");
    DB::query("ALTER TABLE expiry_alerts_track ADD INDEX idx_check_v2 (vehicle_id, expiry_type, expiry_date, alert_day)");
}

return "SELECT 'Migration 039 handled internally' as result;";
