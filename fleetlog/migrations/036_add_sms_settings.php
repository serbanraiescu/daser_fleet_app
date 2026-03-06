<?php

use FleetLog\Core\DB;

/**
 * Migration: Add SMS Gateway Key to system_settings
 */
return new class {
    public function up(): void
    {
        // First, ensure system_settings exists (it should, but for safety)
        DB::query("CREATE TABLE IF NOT EXISTS system_settings (
            `key` VARCHAR(50) PRIMARY KEY,
            `value` TEXT,
            `description` TEXT,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // Add SMS key if not exists
        $exists = DB::fetch("SELECT `key` FROM system_settings WHERE `key` = 'sms_gateway_key'");
        if (!$exists) {
            DB::query("INSERT INTO system_settings (`key`, `value`, `description`) VALUES ('sms_gateway_key', 'fleetlog_secret_123', 'Cheia de securitate pentru Gateway-ul SMS Android')");
        }
    }

    public function down(): void
    {
        // We usually don't delete settings in down, but for completeness:
        // DB::query("DELETE FROM system_settings WHERE `key` = 'sms_gateway_key'");
    }
};
