<?php
/**
 * DEBUG KEY - Verify SMS Gateway Configuration
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/fleetlog/core/Autoloader.php';
require_once __DIR__ . '/fleetlog/core/EnvLoader.php';

\FleetLog\Core\Autoloader::register();
\FleetLog\Core\EnvLoader::load(__DIR__ . '/fleetlog/.env');

use FleetLog\Core\DB;

echo "<h1>SMS Gateway Key Debug</h1>";

try {
    // 1. Check DB
    $setting = DB::fetch("SELECT value FROM system_settings WHERE `key` = 'sms_gateway_key'");
    echo "<b>Key in Database:</b> ";
    if ($setting) {
        echo "<code style='background:#eee; padding:2px 5px;'>[" . $setting['value'] . "]</code> (Length: " . strlen($setting['value']) . ")<br>";
    } else {
        echo "<span style='color:red'>NOT FOUND IN DB!</span><br>";
    }

    // 2. Check ENV
    $envKey = getenv('SMS_GATEWAY_KEY');
    echo "<b>Key in .env / Process:</b> ";
    if ($envKey) {
        echo "<code style='background:#eee; padding:2px 5px;'>[" . $envKey . "]</code> (Length: " . strlen($envKey) . ")<br>";
    } else {
        echo "<i>Not set in .env</i><br>";
    }

    echo "<h3>Instructions:</h3>";
    echo "1. Verify that the key above (including any spaces) matches EXACTLY what you put in the URL: <code>?key=...</code><br>";
    echo "2. If the length is different than what you expect, check for accidental spaces.<br>";
    echo "3. <b>DELETE THIS FILE</b> after checking for security.";

} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage();
}
