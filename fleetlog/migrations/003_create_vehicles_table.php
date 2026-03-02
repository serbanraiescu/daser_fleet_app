<?php

return "CREATE TABLE IF NOT EXISTS vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    license_plate VARCHAR(50) NOT NULL,
    make VARCHAR(100),
    model VARCHAR(100),
    expiry_rca DATE,
    expiry_itp DATE,
    expiry_rovigneta DATE,
    is_active BOOLEAN DEFAULT TRUE,
    current_odometer INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (tenant_id),
    UNIQUE (tenant_id, license_plate),
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
) ENGINE=InnoDB;";
