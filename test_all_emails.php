<?php
/**
 * Script de test pentru trimiterea tuturor template-urilor de email
 * Rulează acest script din browser: https://fleet.daserdesign.ro/test_all_emails.php
 */

require_once __DIR__ . '/fleetlog/core/EnvLoader.php';
require_once __DIR__ . '/fleetlog/core/DB.php';
require_once __DIR__ . '/fleetlog/core/Auth.php';
require_once __DIR__ . '/fleetlog/core/Mailer.php';

use FleetLog\Core\DB;
use FleetLog\Core\Mailer;
use FleetLog\Core\EnvLoader;

header('Content-Type: text/plain; charset=utf-8');

// Initialize Environment
EnvLoader::load(__DIR__ . '/fleetlog/.env');

$targetEmail = 'serbanraiescu@yahoo.com';
echo "Începere test trimitere catre: $targetEmail\n";
echo str_repeat("=", 50) . "\n\n";

// Luăm toate template-urile din DB
$templates = DB::fetchAll("SELECT * FROM email_templates");

if (empty($templates)) {
    die("Eroare: Nu s-au găsit template-uri în baza de date.");
}

foreach ($templates as $t) {
    echo "Trimitere template: " . $t['name'] . " (" . $t['slug'] . ")... ";
    
    // Placeholder-e de test
    $placeholders = [
        'vehicle' => 'DACIA DUSTER (B-123-ABC)',
        'document' => 'Asigurare RCA',
        'date' => date('d.m.Y', strtotime('+3 days')),
        'tenant_name' => 'Companie Test SRL',
        'days' => $t['alert_days'] ?? '3'
    ];
    
    $subject = $t['subject'];
    $body = $t['body'];
    
    foreach ($placeholders as $key => $value) {
        $subject = str_replace('{' . $key . '}', $value, $subject);
        $body = str_replace('{' . $key . '}', $value, $body);
    }
    
    // Trimitere forțată către adresa de test, ignorând tenant-ul
    if (Mailer::send($targetEmail, "[TEST] " . $subject, $body, true)) {
        echo "SUCCES\n";
    } else {
        echo "EȘUAT (Verifică log-urile în aplicație)\n";
    }
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "Test finalizat. Verifică Inbox/Spam în Yahoo.";
