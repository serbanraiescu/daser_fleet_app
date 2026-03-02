<?php
use FleetLog\Core\DB;

$columns = DB::fetchAll("SHOW COLUMNS FROM vehicles");
$hasStatus = false;
$hasIsActive = false;

foreach ($columns as $col) {
    if ($col['Field'] === 'status') $hasStatus = true;
    if ($col['Field'] === 'is_active') $hasIsActive = true;
}

if ($hasIsActive && !$hasStatus) {
    return "ALTER TABLE vehicles 
            ADD COLUMN status ENUM('active', 'inactive', 'service') DEFAULT 'active' AFTER is_active,
            DROP COLUMN is_active;";
}

if ($hasIsActive && $hasStatus) {
    return "ALTER TABLE vehicles DROP COLUMN is_active;";
}

if (!$hasIsActive && !$hasStatus) {
    return "ALTER TABLE vehicles ADD COLUMN status ENUM('active', 'inactive', 'service') DEFAULT 'active' AFTER model";
}

return "SELECT 'Migration 011 already applied or skipped' as result;";
