<?php

use FleetLog\Core\DB;

try {
    // Add language column to tenants
    DB::query("ALTER TABLE tenants ADD COLUMN language VARCHAR(10) DEFAULT 'ro' AFTER notification_emails");
    echo "Migration 040 (Add Language to Tenants) - SUCCESS\n";
} catch (Exception $e) {
    echo "Migration 040 (Add Language to Tenants) - ERROR: " . $e->getMessage() . "\n";
}
