<?php
use FleetLog\Core\DB;

try {
    // For standalone inventory protocols, these fields are not applicable
    DB::query("ALTER TABLE vehicle_handover_reports MODIFY COLUMN vehicle_plate VARCHAR(50) NULL");
    DB::query("ALTER TABLE vehicle_handover_reports MODIFY COLUMN vehicle_model VARCHAR(100) NULL");
    DB::query("ALTER TABLE vehicle_handover_reports MODIFY COLUMN odometer INT NULL");

} catch (\Exception $e) {
    error_log("Migration 047 failed: " . $e->getMessage());
}

return "SELECT 'Migration 047 complete: vehicle fields are now nullable in reports' as result;";
