<?php
use FleetLog\Core\DB;

/**
 * Migration 023: Repair Vehicles Archive Schema
 * Ensures the 'archived' status and 'archive_notes' column exist.
 */

try {
    // 1. Ensure 'archived' is in the ENUM
    DB::query("ALTER TABLE vehicles MODIFY COLUMN status ENUM('active', 'inactive', 'service', 'archived') DEFAULT 'active'");
    
    // 2. Ensure archive_notes column exists
    $columns = DB::fetchAll("SHOW COLUMNS FROM vehicles");
    $hasArchiveNotes = false;
    foreach ($columns as $col) {
        if ($col['Field'] === 'archive_notes') $hasArchiveNotes = true;
    }
    
    if (!$hasArchiveNotes) {
        DB::query("ALTER TABLE vehicles ADD COLUMN archive_notes TEXT DEFAULT NULL AFTER status");
    }
    
    error_log("Migration 023 (Repair) executed successfully.");
} catch (\Exception $e) {
    error_log("Migration 023 (Repair) failed: " . $e->getMessage());
}

return "SELECT 'Migration 023 (Repair) handled internally' as result;";
