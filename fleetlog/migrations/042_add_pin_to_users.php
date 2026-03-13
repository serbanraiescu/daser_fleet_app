<?php

use FleetLog\Core\DB;

try {
    // Add pin column to users table
    DB::query("ALTER TABLE users ADD COLUMN pin VARCHAR(255) NULL AFTER password;");
    
    echo "Migration 042 (Add PIN to Users) - SUCCESS\n";
} catch (Exception $e) {
    // Check if column already exists to be idempotent
    if (strpos($e->getMessage(), "Duplicate column name") !== false) {
        echo "Migration 042 (Add PIN to Users) - ALREADY APPLIED\n";
    } else {
        echo "Migration 042 (Add PIN to Users) - ERROR: " . $e->getMessage() . "\n";
    }
}
