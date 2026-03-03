<?php
require_once __DIR__ . '/fleetlog/core/Autoloader.php';
require_once __DIR__ . '/fleetlog/core/EnvLoader.php';

use FleetLog\Core\Autoloader;
use FleetLog\Core\EnvLoader;
use FleetLog\Core\DB;

Autoloader::register();
EnvLoader::load(__DIR__ . '/fleetlog/.env');

$templates = [
    'rca_expiry' => [
        'subject' => 'Notificare expirare RCA - {vehicle}',
        'body' => "Bună ziua,\n\nVă informăm că polița de asigurare RCA pentru vehiculul {vehicle} va expira la data de {date}.\n\nPentru a asigura continuitatea protecției, vă recomandăm reînnoirea acesteia în timp util.\n\nCu respect,\nEchipa FleetLog"
    ],
    'itp_expiry' => [
        'subject' => 'Notificare ITP - {vehicle}',
        'body' => "Bună ziua,\n\nVă informăm că inspecția tehnică periodică (ITP) pentru vehiculul {vehicle} expiră la data de {date}.\n\nPentru a evita eventuale sancțiuni, vă recomandăm programarea pentru o nouă inspecție.\n\nCu respect,\nEchipa FleetLog"
    ],
    'rovinieta_expiry' => [
        'subject' => 'Notificare rovinietă - {vehicle}',
        'body' => "Bună ziua,\n\nVă informăm că rovinieta pentru vehiculul {vehicle} va expira la data de {date}.\n\nVă rugăm să aveți în vedere achiziționarea unei noi roviniete pentru a circula legal pe drumurile publice.\n\nCu respect,\nEchipa FleetLog"
    ],
    'insurance_expiry' => [
        'subject' => 'Notificare asigurare CASCO/Altele - {vehicle}',
        'body' => "Bună ziua,\n\nVă informăm că asigurarea ({document}) pentru vehiculul {vehicle} expiră la data de {date}.\n\nVă rugăm să analizați necesitatea reînnoirii acesteia.\n\nCu respect,\nEchipa FleetLog"
    ],
    'permit_expiry' => [
        'subject' => 'Notificare expirare permis conducere',
        'body' => "Bună ziua,\n\nVă informăm că valabilitatea permisului dumneavoastră de conducere expiră la data de {date}.\n\nVă recomandăm să inițiați procedurile de preschimbare din timp.\n\nCu respect,\nEchipa FleetLog"
    ],
    'tahograf_expiry' => [
        'subject' => 'Notificare verificare tahograf - {vehicle}',
        'body' => "Bună ziua,\n\nVă informăm că verificarea periodică a tahografului de pe vehiculul {vehicle} expiră la data de {date}.\n\nCu respect,\nEchipa FleetLog"
    ],
    'vignette_expiry' => [
        'subject' => 'Notificare vinietă externă - {vehicle}',
        'body' => "Bună ziua,\n\nVă informăm că vinieta pentru vehiculul {vehicle} ({document}) expiră la data de {date}.\n\nCu respect,\nEchipa FleetLog"
    ]
];

foreach ($templates as $slug => $data) {
    echo "Updating $slug... ";
    DB::query("UPDATE email_templates SET subject = ?, body = ? WHERE slug = ?", [
        $data['subject'],
        $data['body'],
        $slug
    ]);
    echo "Done.\n";
}

unlink(__FILE__);
