<?php

return "CREATE TABLE IF NOT EXISTS trips (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    vehicle_id INT NOT NULL,
    driver_id INT NOT NULL,
    type ENUM('NAVETA', 'CURSE', 'LIVRARE_SPECIALA', 'SERVICE', 'ALTE') NOT NULL,
    start_time DATETIME NOT NULL,
    start_km INT NOT NULL,
    end_time DATETIME,
    end_km INT,
    status ENUM('open', 'closed') DEFAULT 'open',
    needs_review BOOLEAN DEFAULT FALSE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (tenant_id),
    INDEX (vehicle_id),
    INDEX (driver_id),
    INDEX (start_time),
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
    FOREIGN KEY (driver_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;";
