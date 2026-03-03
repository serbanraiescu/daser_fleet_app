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
    
    echo "2. Rulare migrație corectată (queue și logs)...\n";
    $runner = new MigrationRunner();
    $runner->run();
    
    echo "3. Verificare și actualizare structură email_logs...\n";
    // Verificăm dacă coloana provider_response există
    $columns = DB::fetchAll("SHOW COLUMNS FROM email_logs");
    $hasProviderResponse = false;
    foreach ($columns as $col) {
        if ($col['Field'] === 'provider_response') {
            $hasProviderResponse = true;
            break;
        }
    }

    if (!$hasProviderResponse) {
        echo " - Adăugare coloană 'provider_response' în email_logs...\n";
        DB::query("ALTER TABLE email_logs ADD COLUMN provider_response TEXT AFTER error_message");
        echo " - [OK] Coloană adăugată.\n";
    } else {
        echo " - [OK] Coloana 'provider_response' există deja.\n";
    }

    echo "4. Verificare email_queue...\n";
    $queueCheck = DB::fetch("SHOW TABLES LIKE 'email_queue'");
    if ($queueCheck) {
        echo " - [OK] Tabelul email_queue există.\n";
    } else {
        echo " - [!] Tabelul email_queue lipsește (ceva a mers prost la migrație).\n";
    }

    echo "5. Curățare tabel vechi (singular) dacă există...\n";
    DB::query("DROP TABLE IF EXISTS email_log");

    echo "\nREPARARE FINALIZATĂ! ✅\n";
    echo "Acum poți încerca din nou testul.";
    
} catch (Exception $e) {
    echo "\nEROARE: " . $e->getMessage() . "\n";
}

// Ștergem scriptul după rulare pentru securitate (facultativ, dar am pus deja în instrucțiuni)
// unlink(__FILE__);
