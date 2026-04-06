<?php
require_once __DIR__ . '/../fleetlog/core/Autoloader.php';
require_once __DIR__ . '/../fleetlog/core/EnvLoader.php';
\FleetLog\Core\Autoloader::register();
\FleetLog\Core\EnvLoader::load(__DIR__ . '/../fleetlog/.env');

use FleetLog\Core\DB;
use FleetLog\Core\SMSService;

try {
    // 1. Force daily_report_enabled for the first tenant to test
    $t = DB::fetch("SELECT id, name FROM tenants LIMIT 1");
    if ($t) {
        echo "Testing for Tenant: {$t['name']} (ID: {$t['id']})\n";
        DB::query("UPDATE tenants SET daily_report_enabled = 1, daily_report_last_sent = NULL WHERE id = ?", [$t['id']]);
        
        // 2. Run report
        SMSService::sendDailyTenantReports();
        
        // 3. Check queue
        $sms = DB::fetch("SELECT * FROM sms_queue ORDER BY id DESC LIMIT 1");
        if ($sms) {
            echo "SUCCESS: SMS enqueued!\n";
            echo "Recipient: {$sms['phone']}\n";
            echo "Message: {$sms['message']}\n";
        } else {
            echo "ERROR: No SMS in queue.\n";
        }
    } else {
        echo "ERROR: No tenants found.\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
