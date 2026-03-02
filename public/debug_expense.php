<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'fleetlog/core/EnvLoader.php';
require 'fleetlog/core/DB.php';
require 'fleetlog/core/Auth.php';

\FleetLog\Core\EnvLoader::load(__DIR__ . '/fleetlog/.env');
session_start();

try {
    echo "<h1>Debug Expense DB</h1>";

    // Check table structure
    $columns = \FleetLog\Core\DB::fetchAll("SHOW COLUMNS FROM vehicle_expenses");
    echo "<h3>Columns in vehicle_expenses:</h3><pre>";
    print_r($columns);
    echo "</pre>";

    if (empty($columns)) {
        echo "<h2 style='color:red'>TABLE DOES NOT EXIST! RUN run_db_update.php FIRST!</h2>";
        exit;
    }

    // Try a dummy insert
    $vehicleId = \FleetLog\Core\DB::fetch('SELECT id, tenant_id FROM vehicles LIMIT 1');
    
    if (!$vehicleId) {
        echo "No vehicles to test with.";
        exit;
    }

    $sql = "INSERT INTO vehicle_expenses (tenant_id, vehicle_id, expense_type, name, cost, odometer_at_expense, expense_date, notes) 
            VALUES (:tenant_id, :vehicle_id, :expense_type, :name, :cost, :odometer_at_expense, :expense_date, :notes)";

    $data = [
        'tenant_id' => $vehicleId['tenant_id'],
        'vehicle_id' => $vehicleId['id'],
        'expense_type' => 'other',
        'name' => 'DEBUG TEST ENTRY',
        'cost' => 12.50,
        'odometer_at_expense' => null,
        'expense_date' => date('Y-m-d'),
        'notes' => 'This is a test'
    ];

    echo "<h3>Attempting Insert:</h3><pre>";
    print_r($data);
    echo "</pre>";

    $stmt = \FleetLog\Core\DB::query($sql, $data);
    
    echo "<h2 style='color:green'>Insert Successful! Row Count: " . $stmt->rowCount() . "</h2>";
    
    // Clean up
    \FleetLog\Core\DB::query("DELETE FROM vehicle_expenses WHERE name = 'DEBUG TEST ENTRY'");

} catch (\Exception $e) {
    echo "<h2 style='color:red'>Exception Caught:</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
    echo "<h3>Stack Trace:</h3><pre>" . $e->getTraceAsString() . "</pre>";
}
