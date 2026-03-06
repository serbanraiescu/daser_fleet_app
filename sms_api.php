<?php
/**
 * STANDALONE SMS GATEWAY API
 * This file is independent of the framework to ensure maximum compatibility.
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Load DB credentials from .env
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

// CORS Headers - Exactly as requested
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-Update-Key");
header("Content-Type: application/json; charset=UTF-8");

// Handle OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Security Check
$key = $_GET['key'] ?? '';
$gatewayKey = $config['SMS_GATEWAY_KEY'] ?? '';

if (empty($gatewayKey) || $key !== $gatewayKey) {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized", "provided" => $key]);
    exit;
}

// DB Connection
try {
    $dsn = "mysql:host=" . ($config['DB_HOST'] ?? 'localhost') . ";dbname=" . ($config['DB_NAME'] ?? '') . ";charset=utf8mb4";
    $pdo = new PDO($dsn, $config['DB_USER'] ?? '', $config['DB_PASS'] ?? '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "DB Connection Failed: " . $e->getMessage()]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

// POLLING (GET)
if ($method === 'GET') {
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->query("SELECT id, phone, message FROM sms_queue WHERE status = 'pending' ORDER BY id ASC LIMIT 5 FOR UPDATE");
        $messages = $stmt->fetchAll();

        if (!empty($messages)) {
            $ids = array_column($messages, 'id');
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $update = $pdo->prepare("UPDATE sms_queue SET status = 'sending' WHERE id IN ($placeholders)");
            $update->execute($ids);
        }
        $pdo->commit();
        echo json_encode($messages);
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
} 
// CONFIRMATION (POST)
else if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? null;
    
    if ($id) {
        $status = ($data['status'] ?? 'sent') === 'failed' ? 'failed' : 'sent';
        $stmt = $pdo->prepare("UPDATE sms_queue SET status = ?, sent_at = NOW() WHERE id = ?");
        $stmt->execute([$status, $id]);
        echo json_encode(["success" => true]);
    } else {
        http_response_code(400);
        echo json_encode(["error" => "ID missing"]);
    }
}
