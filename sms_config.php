<?php
/**
 * Internal Config Helper for Standalone SMS Scripts
 */
$envFile = __DIR__ . '/fleetlog/.env';
$config = [];
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $config[trim($key)] = trim($value, " \t\n\r\0\x0B\"");
        }
    }
}

// Security Check helper
function validateSmsKey($config) {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-Update-Key");
    header("Content-Type: application/json; charset=UTF-8");

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }

    $key = $_GET['key'] ?? '';
    // Also check Authorization header for extra compatibility
    if (empty($key)) {
        $headers = getallheaders();
        $auth = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        if (str_starts_with($auth, 'Bearer ')) $key = substr($auth, 7);
        else $key = $auth;
    }

    $gatewayKey = $config['SMS_GATEWAY_KEY'] ?? '';
    if (empty($gatewayKey) || $key !== $gatewayKey) {
        http_response_code(403);
        echo json_encode(["status" => "error", "message" => "Unauthorized"]);
        exit;
    }
}

// DB Helper
function getSmsPdo($config) {
    try {
        $dsn = "mysql:host=" . ($config['DB_HOST'] ?? 'localhost') . ";dbname=" . ($config['DB_NAME'] ?? '') . ";charset=utf8mb4";
        return new PDO($dsn, $config['DB_USER'] ?? '', $config['DB_PASS'] ?? '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "DB Connection Failed"]);
        exit;
    }
}

// Logging Helper
function logSmsRequest($source) {
    $logFile = __DIR__ . '/fleetlog/storage/sms_debug.txt';
    $data = [
        'time' => date('Y-m-d H:i:s'),
        'source' => $source,
        'method' => $_SERVER['REQUEST_METHOD'],
        'url' => $_SERVER['REQUEST_URI'],
        'ip' => $_SERVER['REMOTE_ADDR'],
        'ua' => $_SERVER['HTTP_USER_AGENT'] ?? 'N/A',
        'get' => $_GET,
        'body' => file_get_contents('php://input')
    ];
    file_put_contents($logFile, json_encode($data) . "\n", FILE_APPEND);
}

return $config;
