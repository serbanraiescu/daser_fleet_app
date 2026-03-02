<?php

use FleetLog\Core\DB;

try {
    // Add next_service_km to vehicles table
    // We check if it exists first to avoid errors
    $columns = DB::fetchAll("SHOW COLUMNS FROM vehicles");
    $hasNextServiceKm = false;
    foreach ($columns as $col) {
        if ($col['Field'] === 'next_service_km') $hasNextServiceKm = true;
    }

    if (!$hasNextServiceKm) {
        DB::query("ALTER TABLE vehicles ADD COLUMN next_service_km INT DEFAULT 0 AFTER current_odometer");
    }

    // Create vehicle_expenses table
    DB::query("
        CREATE TABLE IF NOT EXISTS vehicle_expenses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            vehicle_id INT NOT NULL,
            expense_type ENUM('maintenance', 'insurance', 'tax', 'consumable', 'other') NOT NULL DEFAULT 'other',
            name VARCHAR(255) NOT NULL,
            cost DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
            odometer_at_expense INT DEFAULT NULL,
            expense_date DATE NOT NULL,
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
            FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

} catch (\Exception $e) {
    error_log("Migration 022 failed: " . $e->getMessage());
}

return "SELECT 'Migration 022 handled internally' as result;";
