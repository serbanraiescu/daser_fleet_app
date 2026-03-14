return new class {
    public function up()
    {
        $pdo = \FleetLog\Core\DB::getInstance();
        
        // Create vehicle_events table
        $sql = "CREATE TABLE IF NOT EXISTS vehicle_events (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            vehicle_id INT NOT NULL,
            event_type ENUM('service', 'damage', 'expense', 'inspection', 'insurance', 'itp') NOT NULL,
            event_subtype VARCHAR(255) DEFAULT NULL,
            event_date DATE NOT NULL,
            odometer INT DEFAULT NULL,
            cost DECIMAL(10,2) DEFAULT NULL,
            description TEXT DEFAULT NULL,
            status VARCHAR(50) DEFAULT 'open',
            created_by INT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
            FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
            INDEX idx_vehicle_date (vehicle_id, event_date),
            INDEX idx_tenant_type (tenant_id, event_type)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
        $pdo->exec($sql);

        // Create vehicle_event_photos table
        $sqlPhotos = "CREATE TABLE IF NOT EXISTS vehicle_event_photos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            event_id INT NOT NULL,
            path VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (event_id) REFERENCES vehicle_events(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
        $pdo->exec($sqlPhotos);
    }
};
