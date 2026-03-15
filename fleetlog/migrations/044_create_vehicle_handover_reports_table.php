<?php

return "CREATE TABLE IF NOT EXISTS vehicle_handover_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    document_number VARCHAR(50) NOT NULL,
    vehicle_id INT NOT NULL,
    driver_id INT NOT NULL,
    vehicle_plate VARCHAR(50) NOT NULL,
    vehicle_model VARCHAR(100) NOT NULL,
    odometer INT NOT NULL,
    fuel_level ENUM('empty', '25', '50', '75', 'full') DEFAULT '50',
    doc_registration BOOLEAN DEFAULT FALSE,
    doc_insurance BOOLEAN DEFAULT FALSE,
    doc_itp BOOLEAN DEFAULT FALSE,
    doc_rovinieta BOOLEAN DEFAULT FALSE,
    aesthetic_condition ENUM('good', 'minor_wear', 'damages') DEFAULT 'good',
    mechanical_condition ENUM('ok', 'needs_check', 'issue') DEFAULT 'ok',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (tenant_id),
    INDEX (vehicle_id),
    INDEX (driver_id),
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
    FOREIGN KEY (driver_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;";
