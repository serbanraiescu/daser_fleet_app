<?php

use FleetLog\Core\DB;

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

return [
    'up' => "SELECT 1;", // Already seeded in the script above for simpler logic
    'down' => "DELETE FROM email_templates WHERE slug IN ('expiry_alert_id', 'expiry_alert_license');"
];
