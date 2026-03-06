<?php

namespace FleetLog\Core;

use Exception;

/**
 * SMSService - Handles the SMS Queue for the Gateway Pattern
 */
class SMSService
{
    /**
     * Enqueue an SMS message
     * 
     * @param string $to Recipient phone number
     * @param string $message Message content
     * @return int|bool ID of the enqueued SMS or false on failure
     */
    public static function enqueue(string $to, string $message): int|bool
    {
        // Simple numeric normalization (optional, can be expanded)
        $to = trim($to);
        if (str_starts_with($to, '07') && strlen($to) === 10) {
            $to = '+40' . substr($to, 1);
        }

        // Avoid exact duplicates pending today
        $existing = DB::fetch(
            "SELECT id FROM sms_queue WHERE phone = ? AND message = ? AND status = 'pending' AND DATE(created_at) = CURDATE()",
            [$to, $message]
        );

        if ($existing) {
            return (int)$existing['id'];
        }

        try {
            DB::query(
                "INSERT INTO sms_queue (phone, message, status, created_at) VALUES (?, ?, 'pending', NOW())",
                [$to, $message]
            );
            return (int)DB::lastInsertId();
        } catch (Exception $e) {
            error_log("SMSService Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get pending messages for the gateway
     * 
     * @param int $limit Max messages to fetch
     * @return array
     */
    public static function getPending(int $limit = 5): array
    {
        try {
            // Use a transaction to mark as 'sending' immediately
            $pdo = \FleetLog\Core\DB::getInstance();
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("SELECT id, phone, message FROM sms_queue WHERE status = 'pending' ORDER BY id ASC LIMIT ? FOR UPDATE");
            $stmt->execute([$limit]);
            $messages = $stmt->fetchAll();

            if (!empty($messages)) {
                $ids = array_column($messages, 'id');
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $update = $pdo->prepare("UPDATE sms_queue SET status = 'sending' WHERE id IN ($placeholders)");
                $update->execute($ids);
            }

            $pdo->commit();
            return $messages;
        } catch (Exception $e) {
            if (isset($pdo)) $pdo->rollBack();
            error_log("SMSService getPending Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Confirm SMS was sent
     */
    public static function confirm(int $id): bool
    {
        try {
            DB::query("UPDATE sms_queue SET status = 'sent', sent_at = NOW() WHERE id = ?", [$id]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Mark SMS as failed
     */
    public static function fail(int $id): bool
    {
        try {
            DB::query("UPDATE sms_queue SET status = 'failed' WHERE id = ?", [$id]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
