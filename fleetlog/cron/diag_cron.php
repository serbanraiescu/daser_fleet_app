<?php
/**
 * Diagnostic script for Cron Environment
 * Upload to fleetlog/cron/diag_cron.php and run from browser or CLI
 */

header('Content-Type: text/plain');

echo "--- FLEETLOG CRON DIAGNOSTIC ---\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "SAPI: " . PHP_SAPI . "\n";
echo "Current Dir: " . __DIR__ . "\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

// 1. Check Autoloader
echo "1. Testing Autoloader... ";
if (file_exists(__DIR__ . '/../core/Autoloader.php')) {
    require_once __DIR__ . '/../core/Autoloader.php';
    \FleetLog\Core\Autoloader::register();
    echo "OK\n";
} else {
    echo "FAILED: Autoloader.php not found at " . realpath(__DIR__ . '/../core/Autoloader.php') . "\n";
}

// 2. Check Env
echo "2. Testing EnvLoader... ";
if (file_exists(__DIR__ . '/../core/EnvLoader.php')) {
    require_once __DIR__ . '/../core/EnvLoader.php';
    $envPath = __DIR__ . '/../.env';
    if (file_exists($envPath)) {
        \FleetLog\Core\EnvLoader::load($envPath);
        echo "OK (File exists)\n";
        echo "   DB_NAME in ENV/SERVER: " . (($_ENV['DB_NAME'] ?? $_SERVER['DB_NAME'] ?? 'NOT FOUND')) . "\n";
    } else {
        echo "FAILED: .env not found at " . realpath($envPath) . "\n";
    }
} else {
    echo "FAILED: EnvLoader.php not found\n";
}

// 3. Check DB
echo "3. Testing Database Connection... ";
try {
    $db = \FleetLog\Core\DB::getInstance();
    $result = \FleetLog\Core\DB::fetch("SELECT 1 as test");
    if ($result && $result['test'] == 1) {
        echo "OK\n";
    } else {
        echo "FAILED: Query returned unexpected result\n";
    }
} catch (\Throwable $e) {
    echo "FAILED: " . $e->getMessage() . "\n";
}

// 4. Check Tables
echo "4. Checking Tables... ";
$tables = ['email_queue', 'sms_queue', 'email_logs', 'system_settings'];
foreach ($tables as $table) {
    try {
        \FleetLog\Core\DB::query("SELECT 1 FROM $table LIMIT 1");
        echo "[$table: OK] ";
    } catch (\Throwable $e) {
        echo "[$table: MISSING/ERROR] ";
    }
}
echo "\n\n";

// 5. Check Queue Status
echo "5. Queue Status:\n";
try {
    $pendingEmail = \FleetLog\Core\DB::fetch("SELECT COUNT(*) as count FROM email_queue WHERE status = 'pending'")['count'];
    $pendingSms = \FleetLog\Core\DB::fetch("SELECT COUNT(*) as count FROM sms_queue WHERE status = 'pending'")['count'];
    echo "   Pending Emails: $pendingEmail\n";
    echo "   Pending SMS: $pendingSms\n";
} catch (\Throwable $e) {
    echo "   Error fetching status: " . $e->getMessage() . "\n";
}

echo "\n--- DIAGNOSTIC COMPLETE ---\n";
