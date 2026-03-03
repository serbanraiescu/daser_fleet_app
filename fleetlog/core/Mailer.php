<?php

namespace FleetLog\Core;

class Mailer
{
    private static ?array $settings = null;

    private static function getSettings(): array
    {
        if (self::$settings === null) {
            $raw = DB::fetchAll("SELECT * FROM system_settings");
            self::$settings = [];
            foreach ($raw as $s) {
                self::$settings[$s['key']] = $s['value'];
            }
            
            // Sensitive data from .env overrides DB
            if (\getenv('SMTP_PASS')) {
                self::$settings['smtp_pass'] = \getenv('SMTP_PASS');
            }
            if (\getenv('SMTP_USER')) {
                self::$settings['smtp_user'] = \getenv('SMTP_USER');
            }
            if (\getenv('SMTP_HOST')) {
                self::$settings['smtp_host'] = \getenv('SMTP_HOST');
            }
        }
        return self::$settings;
    }

    public static function getRecipientEmail(int $tenantId, string $templateSlug): array
    {
        $emails = [];
        $template = DB::fetch("SELECT recipient_type FROM email_templates WHERE slug = ?", [$templateSlug]);
        if (!$template) return [];

        if ($template['recipient_type'] === 'admin') {
            $admin = DB::fetch("SELECT email FROM users WHERE tenant_id = ? AND role = 'tenant_admin' AND active = 1 LIMIT 1", [$tenantId]);
            if ($admin) $emails[] = $admin['email'];
        } else {
            $tenant = DB::fetch("SELECT email, notification_emails FROM tenants WHERE id = ?", [$tenantId]);
            if ($tenant) {
                if ($tenant['email']) $emails[] = $tenant['email'];
                if ($tenant['notification_emails']) {
                    $others = explode(',', $tenant['notification_emails']);
                    foreach ($others as $email) {
                        $email = trim($email);
                        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $emails[] = $email;
                        }
                    }
                }
            }
        }

        return array_unique($emails);
    }

    public static function sendTemplate(int $tenantId, string $templateSlug, array $placeholders = []): bool
    {
        $template = DB::fetch("SELECT * FROM email_templates WHERE slug = ?", [$templateSlug]);
        if (!$template) return false;

        $recipients = self::getRecipientEmail($tenantId, $templateSlug);
        if (empty($recipients)) return false;

        $subject = $template['subject'];
        $body = $template['body'];

        foreach ($placeholders as $key => $value) {
            $subject = \str_replace('{' . $key . '}', $value, $subject);
            $body = \str_replace('{' . $key . '}', $value, $body);
        }

        $success = true;
        foreach ($recipients as $to) {
            if (!self::send($to, $subject, $body, true)) {
                $success = false;
            }
        }

        return $success;
    }

    private static function wrapHtml(string $title, string $content): string
    {
        $year = \date('Y');
        $host = $_SERVER['HTTP_HOST'] ?? 'daserdesign.ro';
        
        return "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; line-height: 1.6; color: #334155; margin: 0; padding: 0; background-color: #f8fafc; }
        .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .header { background: #ffffff; padding: 30px 30px 20px 30px; text-align: center; border-bottom: 1px solid #f1f5f9; }
        .header h1 { color: #1e40af; margin: 0; font-size: 28px; font-weight: 800; letter-spacing: -0.025em; }
        .header p { color: #64748b; margin: 5px 0 0 0; font-size: 14px; }
        .content { padding: 40px; }
        .content h2 { color: #1e293b; margin-top: 0; font-size: 20px; font-weight: 700; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; margin-bottom: 20px; }
        .content p { margin-bottom: 20px; font-size: 16px; color: #475569; }
        .footer { background: #f8fafc; padding: 30px; text-align: center; font-size: 11px; color: #94a3b8; border-top: 1px solid #f1f5f9; }
        .btn-wrapper { margin-top: 30px; text-align: center; border-top: 1px solid #f1f5f9; padding-top: 25px; }
        .btn { display: inline-block; padding: 12px 32px; background-color: #2563eb; color: #ffffff !important; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; transition: background-color 0.2s; }
        .tech-details { font-size: 12px; color: #64748b; background: #f8fafc; padding: 15px; border-radius: 8px; margin: 20px 0; list-style: none; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>FleetLog</h1>
            <p>Sistem Gestiune Flotă Auto</p>
        </div>
        <div class='content'>
            <h2>$title</h2>
            $content
            <div class='btn-wrapper'>
                <a href='https://$host' class='btn'>Accesează Panoul de Control</a>
            </div>
        </div>
        <div class='footer'>
            &copy; $year Daser Fleet App - Gestiune Flotă.
            <br>Acesta este un mesaj tehnic automat generat pentru $host. 
            <br>Vă rugăm să nu răspundeți direct la acest email.
        </div>
    </div>
</body>
</html>";
    }

    public static function send(string $to, string $subject, string $body, bool $isHtml = true): bool
    {
        $settings = self::getSettings();
        
        $host = $settings['smtp_host'] ?: 'localhost';
        $port = (int)($settings['smtp_port'] ?: 25);
        $user = $settings['smtp_user'] ?: '';
        $pass = $settings['smtp_pass'] ?: '';
        $encryption = $settings['smtp_enc'] ?: 'none';
        
        $fromEmail = $settings['smtp_from_email'] ?: ($user ?: 'noreply@' . ($_SERVER['HTTP_HOST'] ?? 'fleetlog.com'));
        $fromName = $settings['smtp_from_name'] ?: 'FleetLog';

        $textVersion = "";
        if ($isHtml) {
            // Generate Text version BEFORE wrapping with style-heavy layout
            $textVersion = \preg_replace('/<(style|script)\b[^>]*>.*?<\/\1>/is', '', $body);
            $textVersion = \str_replace(['<br>', '<br/>', '<br />', '</div>', '</p>', '</h1>', '</h2>', '</h3>', '</h4>', '</h5>', '</h6>'], "\n", $textVersion);
            $textVersion = \strip_tags($textVersion);
            $textVersion = \trim(\html_entity_decode($textVersion, ENT_QUOTES, 'UTF-8'));
            
            $body = self::wrapHtml($subject, $body);
        } else {
            $textVersion = $body;
        }

        $timeout = 10;
        $socketHost = $host;
        if ($port === 465 || $encryption === 'ssl') {
            $socketHost = "ssl://" . $host;
        }

        $socket = @\fsockopen($socketHost, $port, $errno, $errstr, $timeout);
        if (!$socket) {
            \error_log("SMTP Connection Error: $errstr ($errno)");
            return false;
        }

        $getResponse = function($socket) {
            $response = "";
            while ($line = \fgets($socket, 515)) {
                $response .= $line;
                if (\substr($line, 3, 1) == " ") break;
            }
            return $response;
        };

        $sendCommand = function($socket, $cmd) use ($getResponse) {
            \fputs($socket, $cmd . "\r\n");
            return $getResponse($socket);
        };

        $getResponse($socket);
        $sendCommand($socket, "EHLO " . ($_SERVER['HTTP_HOST'] ?? 'localhost'));

        if (($port === 587 || $encryption === 'tls') && ($encryption !== 'ssl')) {
            $res = $sendCommand($socket, "STARTTLS");
            if (\strpos($res, '220') === 0) {
                if (!\stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                    \error_log("SMTP STARTTLS failed");
                    return false;
                }
                $sendCommand($socket, "EHLO " . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
            }
        }

        if (!empty($user) && !empty($pass)) {
            $res = $sendCommand($socket, "AUTH LOGIN");
            $sendCommand($socket, \base64_encode($user));
            $res = $sendCommand($socket, \base64_encode($pass));
            if (\strpos($res, '235') !== 0) {
                \error_log("SMTP Auth Failed: $res");
                return false;
            }
        }

        $sendCommand($socket, "MAIL FROM:<$fromEmail>");
        $sendCommand($socket, "RCPT TO:<$to>");
        
        $res = $sendCommand($socket, "DATA");
        if (\strpos($res, '354') !== 0) {
            \error_log("SMTP DATA Command Rejected: $res");
            return false;
        }
        
        $timestamp = (string)\round(\microtime(true) * 1000);
        $senderDomain = \substr(\strrchr($fromEmail, "@"), 1) ?: 'fleetlog.com';
        $msgId = \sprintf("<%s.%s@%s>", \base_convert($timestamp, 10, 36), \base_convert(\bin2hex(\random_bytes(8)), 16, 36), $senderDomain);
        $boundary = "----=_Part_" . \bin2hex(\random_bytes(12));

        $headers = [
            'Date: ' . \date('r'),
            'To: ' . $to,
            'From: ' . "$fromName <$fromEmail>",
            'Sender: ' . "$fromName <$fromEmail>",
            'Reply-To: ' . "$fromName <$fromEmail>",
            'Return-Path: ' . "<$fromEmail>",
            'Subject: ' . "=?UTF-8?B?" . \base64_encode($subject) . "?=",
            'Message-ID: ' . $msgId,
            'MIME-Version: 1.0',
            'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
            'Auto-Submitted: auto-generated',
            'X-Auto-Response-Suppress: All',
            'X-Priority: 3 (Normal)',
            'X-Mailer: FleetLog-Custom-SMTP'
        ];

        // Ensure CRLF for both
        $body = \str_replace(["\r\n", "\r", "\n"], "\r\n", $body);
        $textVersion = \str_replace(["\r\n", "\r", "\n"], "\r\n", $textVersion);

        // Build Multipart Body
        $fullMsg = \implode("\r\n", $headers) . "\r\n\r\n";
        $fullMsg .= "--$boundary\r\n";
        $fullMsg .= "Content-Type: text/plain; charset=utf-8\r\n";
        $fullMsg .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $fullMsg .= $textVersion . "\r\n\r\n";
        $fullMsg .= "--$boundary\r\n";
        $fullMsg .= "Content-Type: text/html; charset=utf-8\r\n";
        $fullMsg .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $fullMsg .= $body . "\r\n\r\n";
        $fullMsg .= "--$boundary--\r\n.";

        \fputs($socket, $fullMsg . "\r\n");
        $res = $getResponse($socket);

        $sendCommand($socket, "QUIT");
        \fclose($socket);

        $success = (\strpos($res, '250') === 0);
        self::logDelivery($to, $subject, $success, "Server: " . \trim($res));

        return $success;
    }

    private static function logDelivery(string $to, string $subject, bool $success, ?string $error = null): void
    {
        try {
            DB::query("INSERT INTO email_logs (recipient, subject, status, error_message) VALUES (?, ?, ?, ?)", [
                $to, $subject, $success ? 'success' : 'failed', $error
            ]);
        } catch (\Exception $e) {
            \error_log("Failed to log email delivery: " . $e->getMessage());
        }
    }
}
