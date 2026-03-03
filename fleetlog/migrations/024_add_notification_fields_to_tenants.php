<?php
use FleetLog\Core\DB;

/**
 * Migration 024: Add notification fields to tenants
 * Adds contact_phone, notification_phone, and notification_emails.
 */

try {
    $columns = DB::fetchAll("SHOW COLUMNS FROM tenants");
    $fields = array_column($columns, 'Field');

    if (!in_array('contact_phone', $fields)) {
        DB::query("ALTER TABLE tenants ADD COLUMN contact_phone VARCHAR(255) DEFAULT NULL AFTER email");
    }
    
    if (!in_array('notification_phone', $fields)) {
        DB::query("ALTER TABLE tenants ADD COLUMN notification_phone VARCHAR(255) DEFAULT NULL AFTER contact_phone");
    }

    if (!in_array('notification_emails', $fields)) {
        DB::query("ALTER TABLE tenants ADD COLUMN notification_emails TEXT DEFAULT NULL AFTER notification_phone");
    }

    error_log("Migration 024 executed successfully.");
} catch (\Exception $e) {
    error_log("Migration 024 failed: " . $e->getMessage());
}

return "SELECT 'Migration 024 handled internally' as result;";
