<?php
require_once __DIR__ . '/fleetlog/core/Autoloader.php';
require_once __DIR__ . '/fleetlog/core/EnvLoader.php';

use FleetLog\Core\Autoloader;
use FleetLog\Core\EnvLoader;
use FleetLog\Core\DB;

Autoloader::register();
EnvLoader::load(__DIR__ . '/fleetlog/.env');

header('Content-Type: text/plain');

echo "PHP Time: " . date('Y-m-d H:i:s') . " (" . time() . ")\n";

try {
    $dbTime = DB::fetch("SELECT NOW() as now")['now'];
    echo "DB Time (NOW()): $dbTime\n\n";

    echo "--- email_logs ---\n";
    $lastLog = DB::fetch("SELECT * FROM email_logs ORDER BY created_at DESC LIMIT 1");
    if ($lastLog) {
        print_r($lastLog);
        $diff = time() - strtotime($lastLog['created_at']);
        echo "Diff: $diff seconds\n";
    } else {
        echo "No logs found in email_logs\n";
    }

    echo "\n--- email_sent_track ---\n";
    $lastSent = DB::fetch("SELECT * FROM email_sent_track ORDER BY created_at DESC LIMIT 1");
    if ($lastSent) {
        print_r($lastSent);
        $diff = time() - strtotime($lastSent['created_at']);
        echo "Diff: $diff seconds\n";
    } else {
        echo "No records found in email_sent_track\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
