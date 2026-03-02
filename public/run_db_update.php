<?php
// Simple script to run migrations from the browser
// MAKE SURE TO DELETE THIS AFTER RUNNING!

// Allow script to run long enough
set_time_limit(300);
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Running Database Migrations...</h1>";
echo "<pre style='background:#f4f4f4; padding:20px; border:1px solid #ccc; border-radius:5px;'>";

// Determine the path to the app root (assumes this file is in public_html)
$appRoot = dirname(__DIR__) . '/fleetlog'; 

if (!is_dir($appRoot)) {
    // Try the local structure where public is inside the main folder
    $appRoot = dirname(__DIR__);
}

$migrationScript = $appRoot . '/cron/run_migrations.php';

if (!file_exists($migrationScript)) {
    echo "Error: Cannot find migration script at {$migrationScript}\n";
    echo "App Root resolved to: {$appRoot}\n";
} else {
    echo "Found migration script. Executing...\n\n";
    
    // Use output buffering to capture the echo from run_migrations.php
    ob_start();
    try {
        // Change working directory to app root so relative paths in require work
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
echo "<strong>Security Warning:</strong> For security reasons, please delete this file (<code>/public/run_db_update.php</code>) from your server immediately after use.";
echo "</div>";
echo "<br><a href='/' style='display:inline-block; padding:10px 15px; background:#007bff; color:white; text-decoration:none; border-radius:5px;'>Go back to App</a>";
