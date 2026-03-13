<?php

use FleetLog\Core\DB;

try {
    // Create user_remember_tokens table
    DB::query("CREATE TABLE IF NOT EXISTS user_remember_tokens (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        selector CHAR(12) NOT NULL,
        hashed_validator CHAR(64) NOT NULL,
        expires_at DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (selector)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    
    echo "Migration 041 (Create Remember Me Tokens Table) - SUCCESS\n";
} catch (Exception $e) {
    echo "Migration 041 (Create Remember Me Tokens Table) - ERROR: " . $e->getMessage() . "\n";
}
