<?php

return "CREATE TABLE IF NOT EXISTS tenants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    cui VARCHAR(50),
    address TEXT,
    email VARCHAR(255),
    phone VARCHAR(50),
    status ENUM('active', 'suspended') DEFAULT 'active',
    suspension_reason TEXT,
    suspended_at DATETIME,
    settings JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;";
