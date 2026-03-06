<?php

use FleetLog\Core\DB;

/**
 * Migration: Create sms_queue table for Gateway Pattern
 */
return new class {
    public function up(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS sms_queue (
            id INT AUTO_INCREMENT PRIMARY KEY,
            phone VARCHAR(20) NOT NULL,
            message TEXT NOT NULL,
            status ENUM('pending', 'sending', 'sent', 'failed') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            sent_at TIMESTAMP NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        DB::query($sql);
    }

    public function down(): void
    {
        DB::query("DROP TABLE IF EXISTS sms_queue;");
    }
};
