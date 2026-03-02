<?php

use FleetLog\Core\DB;

// Add qr_code column
DB::query("ALTER TABLE vehicles ADD COLUMN qr_code VARCHAR(20) UNIQUE NULL AFTER status");

// Populate existing vehicles with unique random codes
$vehicles = DB::fetchAll("SELECT id FROM vehicles WHERE qr_code IS NULL");
foreach ($vehicles as $v) {
    $code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
    DB::query("UPDATE vehicles SET qr_code = ? WHERE id = ?", [$code, $v['id']]);
}

// Make it NOT NULL after population if desired, but NULL is safer for now
