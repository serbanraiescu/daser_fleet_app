<?php
/**
 * sms_confirm.php - Confirmation endpoint for Android SMS Gateway
 */
$config = require_once 'sms_config.php';
logSmsRequest('sms_confirm');
validateSmsKey($config);
$pdo = getSmsPdo($config);

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? null;

if ($id) {
    $status = ($data['status'] ?? 'sent') === 'failed' ? 'failed' : 'sent';
    try {
        $stmt = $pdo->prepare("UPDATE sms_queue SET status = ?, sent_at = NOW() WHERE id = ?");
        $stmt->execute([$status, $id]);
        echo json_encode(["success" => true]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Update failed"]);
    }
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "ID missing"]);
}
