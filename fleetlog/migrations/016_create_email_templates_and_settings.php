<?php

use FleetLog\Core\DB;

// 1. System Settings Table
DB::query("CREATE TABLE IF NOT EXISTS system_settings (
    `key` VARCHAR(50) PRIMARY KEY,
    `value` TEXT NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// 2. Email Templates Table
DB::query("CREATE TABLE IF NOT EXISTS email_templates (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `slug` VARCHAR(50) UNIQUE NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `body` TEXT NOT NULL,
    `placeholders` TEXT NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// 3. Seed Initial Settings
$settings = [
    'smtp_host' => '',
    'smtp_port' => '587',
    'smtp_user' => '',
    'smtp_pass' => '',
    'smtp_enc' => 'tls',
    'smtp_from_email' => '',
    'smtp_from_name' => 'FleetLog Notifications'
];

foreach ($settings as $key => $val) {
    DB::query("INSERT IGNORE INTO system_settings (`key`, `value`) VALUES (?, ?)", [$key, $val]);
}

// 4. Seed Initial Templates
$templates = [
    [
        'slug' => 'new_damage',
        'name' => 'New Damage Reported',
        'subject' => 'Dauna NOUA - {vehicle_plate}',
        'body' => "Buna ziua,\n\nA fost raportata o dauna noua pentru vehiculul {vehicle_plate}.\n\nSofer: {driver_name}\nData: {datetime}\n\nVa rugam sa verificati admin panel-ul pentru detalii.",
        'placeholders' => '{vehicle_plate}, {driver_name}, {datetime}'
    ],
    [
        'slug' => 'expiry_alert_rca',
        'name' => 'RCA Expiry Alert',
        'subject' => 'ALERTA: RCA expira pentru {vehicle_plate}',
        'body' => "Buna ziua,\n\nVa informam ca asigurarea RCA pentru vehiculul {vehicle_plate} va expira la data de {expiry_date}.\n\nVa rugam sa luati masurile necesare.",
        'placeholders' => '{vehicle_plate}, {expiry_date}'
    ],
    [
        'slug' => 'expiry_alert_itp',
        'name' => 'ITP Expiry Alert',
        'subject' => 'ALERTA: ITP expira pentru {vehicle_plate}',
        'body' => "Buna ziua,\n\nVa informam ca inspectia ITP pentru vehiculul {vehicle_plate} va expira la data de {expiry_date}.\n\nVa rugam sa programati vehiculul pentru inspectie.",
        'placeholders' => '{vehicle_plate}, {expiry_date}'
    ],
    [
        'slug' => 'expiry_alert_rovigneta',
        'name' => 'Rovigneta Expiry Alert',
        'subject' => 'ALERTA: Rovigneta expira pentru {vehicle_plate}',
        'body' => "Buna ziua,\n\nVa informam ca Rovigneta pentru vehiculul {vehicle_plate} va expira la data de {expiry_date}.\n\nVa rugam sa reinnoiti taxa de drum.",
        'placeholders' => '{vehicle_plate}, {expiry_date}'
    ]
];

foreach ($templates as $t) {
    DB::query("INSERT IGNORE INTO email_templates (slug, name, subject, body, placeholders) VALUES (:slug, :name, :subject, :body, :placeholders)", $t);
}
