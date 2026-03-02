<?php
// Simple script to run migrations from the browser
// MAKE SURE TO DELETE THIS AFTER RUNNING!

set_time_limit(300);
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Running Database Migrations...</h1>";
echo "<pre style='background:#f4f4f4; padding:20px; border:1px solid #ccc; border-radius:5px;'>";

// The path to fleetlog is right here
$appRoot = __DIR__ . '/fleetlog';
$migrationScript = $appRoot . '/cron/run_migrations.php';

if (!file_exists($migrationScript)) {
    echo "Error: Cannot find migration script at {$migrationScript}\n";
} else {
    echo "Found migration script. Executing...\n\n";
    
    ob_start();
    try {
        chdir($appRoot);
        require $migrationScript;
        
        $output = ob_get_clean();
        echo htmlspecialchars($output);
        echo "\n\n<strong style='color:green;'>Migrations completed successfully!</strong>";
        
    } catch (Exception $e) {
        $output = ob_get_clean();
        echo htmlspecialchars($output);
        echo "\n\n<strong style='color:red;'>Error running migrations:</strong> " . $e->getMessage();
    }
}

echo "</pre>";
echo "<div style='margin-top:20px; padding:15px; background:#fff3cd; color:#856404; border:1px solid #ffeeba; border-radius:5px;'>";
echo "<strong>Security Warning:</strong> For security reasons, please delete this file (<code>run_db_update.php</code>) from your server immediately after use.";
echo "</div>";
echo "<br><a href='/' style='display:inline-block; padding:10px 15px; background:#007bff; color:white; text-decoration:none; border-radius:5px;'>Go back to App</a>";
