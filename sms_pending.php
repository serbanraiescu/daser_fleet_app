<?php
/**
 * sms_pending.php - Polling endpoint for Android SMS Gateway
 */
$config = require_once 'sms_config.php';
logSmsRequest('sms_pending');
validateSmsKey($config);
$pdo = getSmsPdo($config);

try {
    $pdo->beginTransaction();
    // Select messages in 'pending' status
    $stmt = $pdo->query("SELECT id, phone, message FROM sms_queue WHERE status = 'pending' ORDER BY id ASC LIMIT 5 FOR UPDATE");
    $messages = $stmt->fetchAll();

    if (!empty($messages)) {
        $ids = array_column($messages, 'id');
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $update = $pdo->prepare("UPDATE sms_queue SET status = 'sending' WHERE id IN ($placeholders)");
        $update->execute($ids);
    }
    $pdo->commit();
    
    // Output exactly the JSON list expected by the app
    echo json_encode($messages);
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
