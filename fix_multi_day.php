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
    echo "1. Forțare conversie coloană alert_days în VARCHAR...\n";
    DB::query("ALTER TABLE email_templates MODIFY COLUMN alert_days VARCHAR(255) DEFAULT '7'");
    echo " - [OK] Coloana alert_days a fost convertită.\n";

    echo "2. Creare tabel email_sent_track (dacă lipsește)...\n";
    DB::query("CREATE TABLE IF NOT EXISTS email_sent_track (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tenant_id INT NOT NULL,
        vehicle_id INT NOT NULL,
        template_slug VARCHAR(50) NOT NULL,
        alert_day INT NOT NULL,
        sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY vehicle_template_day (vehicle_id, template_slug, alert_day)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo " - [OK] Tabelul email_sent_track este pregătit.\n";

    echo "3. Marcare migrație 027 ca finalizată în DB...\n";
    DB::query("INSERT IGNORE INTO migrations (migration) VALUES ('027_multi_day_alerts')");
    
    echo "\nTOATE REPARAȚIILE AU FOST APLICATE! ✅\n";
    echo "Acum poți salva template-ul cu multiple zile (ex: 30, 7, 3).";

} catch (Exception $e) {
    echo "\nEROARE: " . $e->getMessage() . "\n";
}
