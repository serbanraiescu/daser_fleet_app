<?php

namespace FleetLog\Migrations;

use FleetLog\Core\DB;
use FleetLog\Core\Migration;

class Migration022 extends Migration
{
    public function up(): void
    {
        // Add next_service_km to vehicles table
        DB::query("ALTER TABLE vehicles ADD COLUMN next_service_km INT DEFAULT 0 AFTER current_odometer");

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
    }

    public function down(): void
    {
        DB::query("DROP TABLE IF EXISTS vehicle_expenses");
        
        try {
            DB::query("ALTER TABLE vehicles DROP COLUMN next_service_km");
        } catch (\Exception $e) {
            // Might fail if column doesn't exist
        }
    }
}
