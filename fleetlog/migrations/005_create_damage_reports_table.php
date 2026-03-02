<?php

return "CREATE TABLE IF NOT EXISTS damage_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    vehicle_id INT NOT NULL,
    driver_id INT NOT NULL,
    datetime DATETIME NOT NULL,
    category ENUM('zgarietura', 'lovitura', 'parbriz', 'roata', 'mecanic', 'altul') NOT NULL,
    severity ENUM('low', 'med', 'high') NOT NULL,
    description TEXT,
    status ENUM('new', 'seen', 'in_repair', 'closed') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (tenant_id),
    INDEX (vehicle_id),
    INDEX (datetime),
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
    FOREIGN KEY (driver_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;";
