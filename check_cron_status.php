<?php
require_once __DIR__ . '/fleetlog/core/Autoloader.php';
require_once __DIR__ . '/fleetlog/core/EnvLoader.php';
use FleetLog\Core\Autoloader;
use FleetLog\Core\EnvLoader;
use FleetLog\Core\DB;
Autoloader::register();
EnvLoader::load(__DIR__ . '/fleetlog/.env');

header('Content-Type: text/plain');
$lastRun = DB::fetch("SELECT * FROM system_settings WHERE `key` = 'last_cron_run'");
echo "Last Cron Run in DB: " . ($lastRun['value'] ?? 'NEVER') . "\n";

$logs = DB::fetchAll("SELECT * FROM email_logs ORDER BY created_at DESC LIMIT 5");
echo "\nLast 5 Email Logs:\n";
print_r($logs);

$track = DB::fetchAll("SELECT * FROM email_sent_track ORDER BY id DESC LIMIT 5");
echo "\nLast 5 Sent Track Records:\n";
print_r($track);
