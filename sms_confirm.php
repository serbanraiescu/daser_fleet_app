<?php
/**
 * sms_confirm.php - Confirmation endpoint for Android SMS Gateway
 */
try {
    $config = require_once 'sms_config.php';
    logSmsRequest('sms_confirm');
    validateSmsKey($config);
    $pdo = getSmsPdo($config);

    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? null;

    if ($id) {
        $status = ($data['status'] ?? 'sent') === 'failed' ? 'failed' : 'sent';
        $stmt = $pdo->prepare("UPDATE sms_queue SET status = ?, sent_at = NOW() WHERE id = ?");
        $stmt->execute([$status, $id]);
        echo json_encode(["success" => true]);
    } else {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "ID missing"]);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    header("Content-Type: application/json");
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
