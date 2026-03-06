<?php
use FleetLog\Core\DB;

/**
 * Migration 037: Create sms_templates table
 */
DB::query("CREATE TABLE IF NOT EXISTS sms_queue_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    template_key VARCHAR(50) UNIQUE NOT NULL,
    template_name VARCHAR(100) NOT NULL,
    message_body TEXT NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Seed the universal expiry template
$defaultBody = "Alerta: {expiry_type} pentru {vehicle_plate} expira la {expiry_date}. Daca a fost reinnoit, actualizati in sistem. - Daser Technologies";

DB::query("INSERT IGNORE INTO sms_queue_templates (template_key, template_name, message_body) 
           VALUES ('universal_expiry', 'Universal Expiry Alert', ?)", [$defaultBody]);

return "SELECT 'Migration 037 handled internally' as result;";
