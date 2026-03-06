<?php
use FleetLog\Core\DB;

/**
 * Migration 038: Create expiry_alerts_track table
 */
DB::query("CREATE TABLE IF NOT EXISTS expiry_alerts_track (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    vehicle_id INT NOT NULL,
    expiry_type VARCHAR(50) NOT NULL,
    expiry_date DATE NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20) DEFAULT 'sent',
    INDEX idx_check (vehicle_id, expiry_type, expiry_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

return "SELECT 'Migration 038 handled internally' as result;";
