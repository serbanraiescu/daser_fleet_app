<?php
use FleetLog\Core\DB;

try {
    // 1. Add is_archived column if it doesn't exist
    $columns = DB::fetchAll("SHOW COLUMNS FROM users");
    $hasIsArchived = false;
    $hasArchiveNotes = false;

    foreach ($columns as $col) {
        if ($col['Field'] === 'is_archived') $hasIsArchived = true;
        if ($col['Field'] === 'archive_notes') $hasArchiveNotes = true;
    }

    if (!$hasIsArchived) {
        DB::query("ALTER TABLE users ADD COLUMN is_archived TINYINT(1) DEFAULT 0 AFTER active");
    }

    if (!$hasArchiveNotes) {
        DB::query("ALTER TABLE users ADD COLUMN archive_notes TEXT DEFAULT NULL AFTER is_archived");
    }

} catch (\Exception $e) {
    error_log("Migration 049 failed: " . $e->getMessage());
}

return "SELECT 'Migration 049 (User Archiving) handled internally' as result;";
