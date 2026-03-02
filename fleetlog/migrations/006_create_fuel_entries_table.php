<?php

return "CREATE TABLE IF NOT EXISTS fuel_entries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    vehicle_id INT NOT NULL,
    driver_id INT NOT NULL,
    datetime DATETIME NOT NULL,
    liters DECIMAL(10, 2) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    odometer_km INT,
    receipt_photo_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (tenant_id),
    INDEX (vehicle_id),
    INDEX (datetime),
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
    FOREIGN KEY (driver_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;";
