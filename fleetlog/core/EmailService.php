<?php

namespace FleetLog\Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use FleetLog\Core\DB;

class EmailService
{
    private static function getSmtpSettings(): array
    {
        return DB::fetch("SELECT * FROM settings WHERE `key` LIKE 'smtp_%'");
        // Wait, the settings table usually has key/value pairs. 
        // Let's check the Mailer.php implementation to see how it fetches settings.
    }

    /**
     * Hardened PHPMailer configuration
     */
    private static function createMailer(): PHPMailer
    {
        $mail = new PHPMailer(true);
        $settings = Mailer::getSettings();

        // Server settings
        $mail->isSMTP();
        $mail->Host       = $settings['smtp_host'] ?: 'localhost';
        $mail->SMTPAuth   = !empty($settings['smtp_user']) && !empty($settings['smtp_pass']);
        $mail->Username   = $settings['smtp_user'] ?: '';
        $mail->Password   = $settings['smtp_pass'] ?: '';
        $mail->SMTPSecure = $settings['smtp_enc'] === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : 
                           ($settings['smtp_enc'] === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : '');
        $mail->Port       = (int)($settings['smtp_port'] ?: 25);
        $mail->CharSet    = 'UTF-8';
        $mail->Encoding   = 'quoted-printable';

        // Recipients
        $fromEmail = $settings['smtp_from_email'] ?: 'noreply@' . ($_SERVER['HTTP_HOST'] ?? 'daserdesign.ro');
        $fromName  = $settings['smtp_from_name'] ?: 'FleetLog';
        $mail->setFrom($fromEmail, $fromName);
        $mail->Sender = $fromEmail; // Explicit Return-Path

        // Content
        $mail->isHTML(true);
        
        // Remove ALL priority headers
        $mail->Priority = null;
        $mail->XMailer  = ' '; // Neutral X-Mailer
        
        // Add List-Unsubscribe
        $mail->addCustomHeader('List-Unsubscribe', '<mailto:' . $fromEmail . '?subject=unsubscribe>, <https://' . ($_SERVER['HTTP_HOST'] ?? 'daserdesign.ro') . '/unsubscribe>');

        return $mail;
    }

    /**
     * Validate that no placeholders remain in the content
     */
    public static function validateContent(string $content): void
    {
        if (preg_match('/\{[a-zA-Z0-9_-]+\}/', $content)) {
            throw new \Exception("Unreplaced placeholders detected in email content.");
        }
    }

    /**
     * Add an email to the queue
     */
    public static function queue(string $to, string $subject, string $htmlBody, ?string $textBody = null): bool
    {
        self::validateContent($subject);
        self::validateContent($htmlBody);
        if ($textBody) {
            self::validateContent($textBody);
        } else {
            // Generate text version if missing
            $textBody = strip_tags(str_replace(['<br>', '</div>', '</p>'], "\n", $htmlBody));
            $textBody = html_entity_decode($textBody, ENT_QUOTES, 'UTF-8');
        }

        return DB::query("INSERT INTO email_queue (recipient, subject, body_html, body_text) VALUES (?, ?, ?, ?)", [
            $to, $subject, $htmlBody, $textBody
        ])->rowCount() > 0;
    }

    /**
     * Send an email immediately (use with caution)
     */
    public static function sendDirect(string $to, string $subject, string $htmlBody, ?string $textBody = null): bool
    {
        $mail = self::createMailer();
        $status = 'failed';
        $error = '';
        $response = '';

        try {
            self::validateContent($subject);
            self::validateContent($htmlBody);

            $mail->addAddress($to);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            
            if (!$textBody) {
                $textBody = strip_tags(str_replace(['<br>', '</div>', '</p>'], "\n", $htmlBody));
                $textBody = html_entity_decode($textBody, ENT_QUOTES, 'UTF-8');
            }
            $mail->AltBody = $textBody;

            $mail->send();
            $status = 'success';
            $response = 'Message sent successfully';
            return true;
        } catch (Exception $e) {
            $error = $mail->ErrorInfo;
            $response = $e->getMessage();
            return false;
        } finally {
            self::log($to, $subject, $status, $error, $response);
        }
    }

    /**
     * Process the email queue
     */
    public static function processQueue(int $limit = 5): int
    {
        $processed = 0;
        $pending = DB::fetchAll("SELECT * FROM email_queue WHERE status = 'pending' AND attempts < 3 ORDER BY created_at ASC LIMIT ?", [$limit]);

        foreach ($pending as $item) {
            // Mark as processing
            DB::query("UPDATE email_queue SET status = 'processing', attempts = attempts + 1 WHERE id = ?", [$item['id']]);

            $mail = self::createMailer();
            try {
                $mail->addAddress($item['recipient']);
                $mail->Subject = $item['subject'];
                $mail->Body    = $item['body_html'];
                $mail->AltBody = $item['body_text'];

                $mail->send();
                
                // Success
                DB::query("UPDATE email_queue SET status = 'sent', updated_at = NOW() WHERE id = ?", [$item['id']]);
                self::log($item['recipient'], $item['subject'], 'success', '', 'Sent via Queue');
                $processed++;

            } catch (Exception $e) {
                $error = $mail->ErrorInfo ?: $e->getMessage();
                DB::query("UPDATE email_queue SET status = 'pending', error_message = ? WHERE id = ?", [$error, $item['id']]);
                self::log($item['recipient'], $item['subject'], 'failed', $error, 'Queue retry later');
            }

            // Rate limiting delay
            usleep(rand(1000000, 3000000)); 
        }

        return $processed;
    }

    /**
     * Log email attempt
     */
    public static function log(string $to, string $subject, string $status, string $error = '', string $response = ''): void
    {
        DB::query("INSERT INTO email_logs (recipient, subject, status, error_message, provider_response) VALUES (?, ?, ?, ?, ?)", [
            $to, $subject, $status, $error, $response
        ]);
    }

    /**
     * Professional minimalist HTML wrapper
     */
    public static function wrapHtml(string $title, string $content): string
    {
        $host = $_SERVER['HTTP_HOST'] ?? 'daserdesign.ro';
        return "
<!DOCTYPE html>
<html lang='ro'>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; line-height: 1.5; color: #1f2937; margin: 0; padding: 0; background-color: #f9fafb; }
        .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 4px; overflow: hidden; }
        .header { padding: 24px; text-align: center; border-bottom: 1px solid #f3f4f6; }
        .header h1 { margin: 0; font-size: 20px; color: #111827; }
        .content { padding: 32px; font-size: 16px; }
        .footer { padding: 24px; text-align: center; font-size: 12px; color: #6b7280; border-top: 1px solid #f3f4f6; background-color: #f9fafb; }
        .btn { display: inline-block; padding: 10px 20px; background-color: #2563eb; color: #ffffff !important; text-decoration: none; border-radius: 6px; font-weight: 500; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>FleetLog</h1>
        </div>
        <div class='content'>
            <h2 style='font-size: 18px; margin-top: 0;'>$title</h2>
            $content
        </div>
        <div class='footer'>
            &copy; " . date('Y') . " Daser Fleet App. Aceasta este o notificare tranzacțională.<br>
            Pentru dezabonare sau setări alertă, accesați panoul de control.
        </div>
    </div>
</body>
</html>";
    }
}
