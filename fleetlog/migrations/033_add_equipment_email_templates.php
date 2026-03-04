<?php
use FleetLog\Core\DB;

$templates = [
    [
        'slug' => 'expiry_alert_medical_kit',
        'name' => 'Alertă Expirare Trusă Medicală',
        'subject' => '⚠️ Expirare Trusă Medicală - {vehicle_plate}',
        'body' => 'Buna ziua,<br><br>Trusa medicala pentru vehiculul <strong>{vehicle}</strong> va expira la data de <strong>{expiry_date}</strong> (in {days} zile).<br><br>Va rugam sa verificati si sa inlocuiti trusa daca este necesar.',
        'alert_days' => '30,7,3'
    ],
    [
        'slug' => 'expiry_alert_extinguisher',
        'name' => 'Alertă Expirare Stingător',
        'subject' => '⚠️ Expirare Stingător Incendiu - {vehicle_plate}',
        'body' => 'Buna ziua,<br><br>Stingatorul de incendiu pentru vehiculul <strong>{vehicle}</strong> va expira la data de <strong>{expiry_date}</strong> (in {days} zile).<br><br>Va rugam sa verificati si sa reincarcati sau inlocuiti stingatorul.',
        'alert_days' => '30,7,3'
    ]
];

foreach ($templates as $t) {
    $exists = DB::fetch("SELECT id FROM email_templates WHERE slug = ?", [$t['slug']]);
    if (!$exists) {
        DB::query("INSERT INTO email_templates (slug, name, subject, body, alert_days) VALUES (?, ?, ?, ?, ?)", [
            $t['slug'], $t['name'], $t['subject'], $t['body'], $t['alert_days']
        ]);
    }
}

return "SELECT 'Migration 033 completed' as result;";
