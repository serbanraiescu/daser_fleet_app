<?php
/**
 * Script de forțare a update-ului pentru alerte multiple
 * Rulează acest script din browser: https://fleet.daserdesign.ro/fix_multi_day.php
 */

require_once __DIR__ . '/fleetlog/core/Autoloader.php';
require_once __DIR__ . '/fleetlog/core/EnvLoader.php';

use FleetLog\Core\Autoloader;
use FleetLog\Core\EnvLoader;
use FleetLog\Core\DB;

Autoloader::register();
EnvLoader::load(__DIR__ . '/fleetlog/.env');

header('Content-Type: text/plain; charset=utf-8');

try {
    echo "1. Creare tabel email_queue...\n";
    DB::query("CREATE TABLE IF NOT EXISTS email_queue (
        id INT AUTO_INCREMENT PRIMARY KEY,
        recipient VARCHAR(255) NOT NULL,
        subject VARCHAR(255) NOT NULL,
        body_html LONGTEXT,
        body_text LONGTEXT,
        attempts INT DEFAULT 0,
        status ENUM('pending', 'processing', 'sent', 'failed') DEFAULT 'pending',
        error_message TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        scheduled_at TIMESTAMP NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo " - [OK] Tabelul email_queue creat.\n";

    echo "2. Creare tabel email_logs...\n";
    DB::query("CREATE TABLE IF NOT EXISTS email_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        recipient VARCHAR(255) NOT NULL,
        subject VARCHAR(255) NOT NULL,
        status ENUM('success', 'failed') NOT NULL,
        error_message TEXT,
        provider_response TEXT,
        sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo " - [OK] Tabelul email_logs creat.\n";

    echo "3. Forțare conversie alert_days în VARCHAR...\n";
    DB::query("ALTER TABLE email_templates MODIFY COLUMN alert_days VARCHAR(255) DEFAULT '7'");
    echo " - [OK] Coloana alert_days convertită.\n";

    echo "4. Creare tabel email_sent_track...\n";
    DB::query("CREATE TABLE IF NOT EXISTS email_sent_track (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tenant_id INT NOT NULL,
        vehicle_id INT NOT NULL,
        template_slug VARCHAR(50) NOT NULL,
        alert_day INT NOT NULL,
        sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY vehicle_template_day (vehicle_id, template_slug, alert_day)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo " - [OK] Tabelul email_sent_track creat.\n";

    echo "\nTOATE REPARAȚIILE DE INFRASTRUCTURĂ AU FOST APLICATE! ✅\n";
    echo "Acum email-urile automate vor putea fi puse în coadă și trimise.";

} catch (Exception $e) {
    echo "\nEROARE: " . $e->getMessage() . "\n";
}
