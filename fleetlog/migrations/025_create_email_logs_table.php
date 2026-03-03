<?php
use FleetLog\Core\DB;

/**
 * Migration 025: Create email_logs table
 */

try {
    DB::query("CREATE TABLE IF NOT EXISTS email_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        recipient VARCHAR(255) NOT NULL,
        subject VARCHAR(255) NOT NULL,
        status ENUM('success', 'failed') NOT NULL,
        error_message TEXT DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    error_log("Migration 025 executed successfully.");
} catch (\Exception $e) {
    error_log("Migration 025 failed: " . $e->getMessage());
}

return "SELECT 'Migration 025' as result;";
