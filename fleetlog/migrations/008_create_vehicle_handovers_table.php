<?php

return "CREATE TABLE IF NOT EXISTS vehicle_handovers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    vehicle_id INT NOT NULL,
    from_user_id INT,
    to_user_id INT NOT NULL,
    datetime DATETIME NOT NULL,
    odometer_km INT NOT NULL,
    has_damage BOOLEAN DEFAULT FALSE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (tenant_id),
    INDEX (vehicle_id),
    INDEX (datetime),
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
    FOREIGN KEY (from_user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (to_user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;";
