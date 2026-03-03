<?php

use FleetLog\Core\DB;

return [
    'up' => "
        ALTER TABLE email_templates MODIFY COLUMN alert_days VARCHAR(255) DEFAULT '7';
        
        CREATE TABLE IF NOT EXISTS email_sent_track (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            vehicle_id INT NOT NULL,
            template_slug VARCHAR(50) NOT NULL,
            alert_day INT NOT NULL,
            sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY vehicle_template_day (vehicle_id, template_slug, alert_day)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ",
    'down' => "
        DROP TABLE IF EXISTS email_sent_track;
    "
];
