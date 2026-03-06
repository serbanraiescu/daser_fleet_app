<?php
/**
 * READ LOG - View the request log in the browser
 */
$logFile = __DIR__ . '/fleetlog/storage/request_log.txt';

echo "<h1>Request Log</h1>";

if (file_exists($logFile)) {
    echo "<pre style='background:#f4f4f4; padding:20px; border:1px solid #ccc; overflow:auto; max-height:80vh;'>";
    echo htmlspecialchars(file_get_contents($logFile));
    echo "</pre>";
    echo "<form method='POST'><button type='submit' name='clear' style='background:red; color:white; padding:10px;'>Clear Log</button></form>";
    
    if (isset($_POST['clear'])) {
        file_put_contents($logFile, "");
        header("Location: read_log.php");
        exit;
    }
} else {
    echo "No requests logged yet. Try connecting from the Android app to <code>/debug_request.php</code>";
}
