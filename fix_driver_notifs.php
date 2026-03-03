<?php
/**
 * Script de activare notificări Buletin/Permis pentru Șoferi
 * Rulează acest script din browser: https://fleet.daserdesign.ro/fix_driver_notifs.php
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
    echo "1. Actualizare email_sent_track pentru suport șoferi...\n";
    // Check if user_id already exists to avoid errors on re-run
    $cols = DB::fetchAll("SHOW COLUMNS FROM email_sent_track LIKE 'user_id'");
    if (empty($cols)) {
        DB::query("ALTER TABLE email_sent_track 
            MODIFY COLUMN vehicle_id INT NULL,
            ADD COLUMN user_id INT NULL AFTER vehicle_id,
            DROP INDEX vehicle_template_day,
            ADD UNIQUE KEY sent_tracking_unique (vehicle_id, user_id, template_slug, alert_day)");
        echo " - [OK] Schema email_sent_track a fost actualizată.\n";
    } else {
        echo " - [SKIP] email_sent_track este deja actualizat.\n";
    }

    echo "2. Adăugare template-uri noi (CI / Permis)...\n";
    $templates = [
        [
            'slug' => 'expiry_alert_id',
            'name' => 'Identity Card Expiry Alert',
            'subject' => 'ALERTA: Expira cartea de identitate pentru {driver_name}',
            'body' => "Buna ziua,\n\nVa informam ca documentul de identitate (CI) pentru {driver_name} va expira la data de {expiry_date}.\n\nVa rugam sa va programati pentru reinnoirea buletinului.",
            'placeholders' => '{driver_name}, {expiry_date}, {days}',
            'alert_days' => '30, 7, 3',
            'recipient_type' => 'tenant'
        ],
        [
            'slug' => 'expiry_alert_license',
            'name' => 'Driver License Expiry Alert',
            'subject' => 'ALERTA: Expira permisul de conducere pentru {driver_name}',
            'body' => "Buna ziua,\n\nVa informam ca permisul de conducere pentru {driver_name} va expira la data de {expiry_date}.\n\nVa rugam sa luati masurile necesare pentru preschimbarea permisului.",
            'placeholders' => '{driver_name}, {expiry_date}, {days}',
            'alert_days' => '30, 7, 3',
            'recipient_type' => 'tenant'
        ]
    ];

    foreach ($templates as $t) {
        DB::query("INSERT IGNORE INTO email_templates (slug, name, subject, body, placeholders, alert_days, recipient_type) VALUES (:slug, :name, :subject, :body, :placeholders, :alert_days, :recipient_type)", $t);
    }
    echo " - [OK] Template-uri adăugate.\n";

    echo "3. Marcare migrări ca finalizate...\n";
    DB::query("INSERT IGNORE INTO migrations (migration) VALUES ('029_add_driver_email_templates')");
    DB::query("INSERT IGNORE INTO migrations (migration) VALUES ('030_update_sent_track_for_drivers')");
    
    echo "\nNOTIFICĂRILE PENTRU ȘOFERI SUNT ACTIVE! ✅\n";
    echo "Coordonatele de expirare vor fi scanate la următoarea rulare a cron-ului (ora 08:00).";

} catch (Exception $e) {
    echo "\nEROARE: " . $e->getMessage() . "\n";
}
