<?php
use FleetLog\Core\DB;

/**
 * Migration 048: Add signup_token and signup_enabled to tenants
 */
$columns = DB::fetchAll("SHOW COLUMNS FROM tenants");
$columnNames = array_column($columns, 'Field');

if (!in_array('signup_token', $columnNames)) {
    DB::query("ALTER TABLE tenants ADD COLUMN signup_token VARCHAR(64) NULL AFTER equipment_config");
    DB::query("ALTER TABLE tenants ADD INDEX idx_signup_token (signup_token)");
}

if (!in_array('signup_enabled', $columnNames)) {
    DB::query("ALTER TABLE tenants ADD COLUMN signup_enabled TINYINT(1) DEFAULT 0 AFTER signup_token");
}

return "SELECT 'Migration 048 complete' as result;";
