<?php
/**
 * READ LOG - View all diagnostic logs in the browser
 */
$requestLog = __DIR__ . '/fleetlog/storage/request_log.txt';
$smsLog = __DIR__ . '/fleetlog/storage/sms_debug.txt';

echo "<h1>Diagnostic Logs</h1>";

if (isset($_POST['clear'])) {
    if (file_exists($requestLog)) file_put_contents($requestLog, "");
    if (file_exists($smsLog)) file_put_contents($smsLog, "");
    header("Location: read_log.php");
    exit;
}

echo "<h2>1. SMS API Debug Log (Latest refined scripts)</h2>";
if (file_exists($smsLog) && filesize($smsLog) > 0) {
    echo "<pre style='background:#eef; padding:15px; border:1px solid #99b; overflow:auto; max-height:40vh;'>";
    $lines = file($smsLog);
    foreach (array_reverse($lines) as $line) {
        $data = json_decode($line, true);
        if ($data) {
            echo "[" . $data['time'] . "] " . $data['source'] . " | " . $data['method'] . " | " . $data['url'] . " | IP: " . $data['ip'] . "\n";
            echo "GET: " . json_encode($data['get']) . "\n";
            echo "--------------------------------------------------\n";
        } else {
            echo htmlspecialchars($line);
        }
    }
    echo "</pre>";
} else {
    echo "<p><i>No entries in SMS debug log yet.</i></p>";
}

echo "<h2>2. Raw Request Log (debug_request.php)</h2>";
if (file_exists($requestLog) && filesize($requestLog) > 0) {
    echo "<pre style='background:#f4f4f4; padding:15px; border:1px solid #ccc; overflow:auto; max-height:40vh;'>";
    echo htmlspecialchars(file_get_contents($requestLog));
    echo "</pre>";
} else {
    echo "<p><i>No entries in raw request log.</i></p>";
}

echo "<hr><form method='POST'><button type='submit' name='clear' style='background:red; color:white; padding:12px 24px; border:none; border-radius:6px; cursor:pointer;'>Clear All Logs</button></form>";
