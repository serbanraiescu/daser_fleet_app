<?php
/**
 * REPAIR SCRIPT V2 - Diagnostics & Force Fix
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/fleetlog/core/Autoloader.php';
require_once __DIR__ . '/fleetlog/core/EnvLoader.php';

\FleetLog\Core\Autoloader::register();
\FleetLog\Core\EnvLoader::load(__DIR__ . '/fleetlog/.env');

use FleetLog\Core\DB;

echo "<h1>System Repair: Deep Diagnostic</h1>";

try {
    echo "<h3>1. Database Info</h3>";
    $dbName = DB::fetch("SELECT DATABASE() as db")['db'];
    echo "Connected to Database: <b>$dbName</b><br>";

    echo "<h3>2. Tables in Database</h3>";
    $tables = DB::fetchAll("SHOW TABLES");
    $tableList = [];
    foreach ($tables as $row) {
        $name = array_values($row)[0];
        $tableList[] = $name;
        echo "- $name<br>";
    }

    if (in_array('sms_queue', $tableList)) {
        echo "<b style='color:green'>Table 'sms_queue' EXISTS.</b><br>";
    } else {
        echo "<b style='color:red'>Table 'sms_queue' is MISSING!</b><br>";
    }

    echo "<h3>3. Migration Status</h3>";
    try {
        $executed = DB::fetchAll("SELECT * FROM migrations");
        foreach ($executed as $m) {
            echo "- " . $m['migration'] . " (at " . $m['executed_at'] . ")<br>";
        }
    } catch (\Exception $e) {
        echo "Could not read migrations table: " . $e->getMessage() . "<br>";
    }

    echo "<h3>4. Force Execution</h3>";
    if (isset($_GET['force_sms'])) {
        echo "Attempting to create table explicitly...<br>";
        $sql = "CREATE TABLE IF NOT EXISTS sms_queue (
            id INT AUTO_INCREMENT PRIMARY KEY,
            phone VARCHAR(20) NOT NULL,
            message TEXT NOT NULL,
            status ENUM('pending', 'sending', 'sent', 'failed') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            sent_at TIMESTAMP NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        
        DB::query($sql);
        echo "<b style='color:green'>SQL Executed.</b> Checking again...<br>";
        
        $tables2 = DB::fetchAll("SHOW TABLES");
        $found = false;
        foreach ($tables2 as $row) {
            if (array_values($row)[0] === 'sms_queue') $found = true;
        }
        
        if ($found) echo "<h2 style='color:green'>SUCCESS: Table now exists!</h2>";
        else echo "<h2 style='color:red'>FAILURE: Table STILL missing after SQL run! Check DB permissions.</h2>";
    } else {
        echo "<a href='?force_sms=1' style='padding:10px 20px; background:blue; color:white; text-decoration:none; border-radius:5px;'>FORCE CREATE SMS TABLE</a>";
    }

} catch (\Throwable $e) {
    echo "<b style='color:red'>Fatal Error:</b> " . $e->getMessage();
}
