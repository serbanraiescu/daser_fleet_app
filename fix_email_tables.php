<?php
/**
 * Script de reparare pentru tabelele de email
 * Rulează acest script din browser: https://fleet.daserdesign.ro/fix_email_tables.php
 */

require_once __DIR__ . '/fleetlog/core/Autoloader.php';
require_once __DIR__ . '/fleetlog/core/EnvLoader.php';

use FleetLog\Core\Autoloader;
use FleetLog\Core\EnvLoader;
use FleetLog\Core\DB;
use FleetLog\Core\MigrationRunner;

Autoloader::register();
EnvLoader::load(__DIR__ . '/fleetlog/.env');

header('Content-Type: text/plain; charset=utf-8');

try {
    echo "1. Curățare stare migrație 026...\n";
    DB::query("DELETE FROM migrations WHERE migration = '026_create_email_delivery_system'");
    
    echo "2. Rulare migrație corectată (026)...\n";
    $runner = new MigrationRunner();
    $runner->run();
    
    echo "3. Verificare tabele...\n";
    $queueCheck = DB::fetch("SHOW TABLES LIKE 'email_queue'");
    $logsCheck = DB::fetch("SHOW TABLES LIKE 'email_logs'");
    
    if ($queueCheck) echo " - [OK] email_queue există\n"; else echo " - [!] email_queue lipsește\n";
    if ($logsCheck) echo " - [OK] email_logs există\n"; else echo " - [!] email_logs lipsește\n";

    echo "4. Curățare singular 'email_log' dacă a apucat să fie creat...\n";
    DB::query("DROP TABLE IF EXISTS email_log");

    echo "\nREPARARE FINALIZATĂ CU SUCCES! ✅\n";
    echo "Acum poți încerca din nou scriptul de test sau butonul de Test Email.";
    
} catch (Exception $e) {
    echo "\nEROARE: " . $e->getMessage() . "\n";
}

// Ștergem scriptul după rulare pentru securitate (facultativ, dar am pus deja în instrucțiuni)
// unlink(__FILE__);
