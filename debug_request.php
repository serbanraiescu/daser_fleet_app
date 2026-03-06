<?php
/**
 * REQUEST LOGGER - See exactly what the Android app sends
 */
$logFile = __DIR__ . '/fleetlog/storage/request_log.txt';

if (!is_dir(dirname($logFile))) {
    mkdir(dirname($logFile), 0755, true);
}

$data = [
    'timestamp' => date('Y-m-d H:i:s'),
    'method' => $_SERVER['REQUEST_METHOD'],
    'url' => $_SERVER['REQUEST_URI'],
    'headers' => getallheaders(),
    'get' => $_GET,
    'post' => $_POST,
    'body' => file_get_contents('php://input'),
    'ip' => $_SERVER['REMOTE_ADDR']
];

$entry = "==========================================\n";
$entry .= "TIME: " . $data['timestamp'] . "\n";
$entry .= "METHOD: " . $data['method'] . "\n";
$entry .= "URL: " . $data['url'] . "\n";
$entry .= "HEADERS: " . json_encode($data['headers'], JSON_PRETTY_PRINT) . "\n";
$entry .= "GET: " . json_encode($data['get'], JSON_PRETTY_PRINT) . "\n";
$entry .= "BODY: " . $data['body'] . "\n";
$entry .= "==========================================\n\n";

file_put_contents($logFile, $entry, FILE_APPEND);

header('Content-Type: application/json');
echo json_encode(['status' => 'logged', 'message' => 'Request captured by Antigravity Debugger']);
