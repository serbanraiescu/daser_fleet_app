<?php

return "CREATE TABLE IF NOT EXISTS damage_photos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    damage_report_id INT NOT NULL,
    path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (tenant_id),
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (damage_report_id) REFERENCES damage_reports(id) ON DELETE CASCADE
) ENGINE=InnoDB;";
