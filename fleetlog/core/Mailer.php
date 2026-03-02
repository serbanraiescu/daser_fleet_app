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

    public static function getRecipientEmail(int $tenantId, string $templateSlug): ?string
    {
        $template = DB::fetch("SELECT recipient_type FROM email_templates WHERE slug = ?", [$templateSlug]);
        if (!$template) return null;

        if ($template['recipient_type'] === 'admin') {
            $admin = DB::fetch("SELECT email FROM users WHERE tenant_id = ? AND role = 'tenant_admin' AND active = 1 LIMIT 1", [$tenantId]);
            return $admin['email'] ?? null;
        }

        $tenant = DB::fetch("SELECT email FROM tenants WHERE id = ?", [$tenantId]);
        return $tenant['email'] ?? null;
    }

    public static function sendTemplate(int $tenantId, string $templateSlug, array $placeholders = []): bool
    {
        $template = DB::fetch("SELECT * FROM email_templates WHERE slug = ?", [$templateSlug]);
        if (!$template) return false;

        $to = self::getRecipientEmail($tenantId, $templateSlug);
        if (!$to) return false;

        $subject = $template['subject'];
        $body = $template['body'];

        foreach ($placeholders as $key => $value) {
            $subject = \str_replace('{' . $key . '}', $value, $subject);
            $body = \str_replace('{' . $key . '}', $value, $body);
        }

        return self::send($to, $subject, $body, true);
    }

    private static function wrapHtml(string $title, string $content): string
    {
        $body = \nl2br(\htmlspecialchars($content));
        return "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #334155; margin: 0; padding: 0; background-color: #f8fafc; }
        .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .header { background: #2563eb; padding: 30px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; }
        .content { padding: 40px; }
        .content h2 { color: #1e293b; margin-top: 0; font-size: 20px; }
        .content p { margin-bottom: 20px; font-size: 16px; color: #475569; }
        .footer { background: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #f1f5f9; }
        .btn { display: inline-block; padding: 12px 28px; background-color: #2563eb; color: #ffffff !important; text-decoration: none; border-radius: 10px; font-weight: 600; font-size: 14px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>FleetLog Alerts</h1>
        </div>
        <div class='content'>
            <h2>$title</h2>
            <p>$body</p>
            <div style='margin-top: 30px; border-top: 1px solid #f1f5f9; padding-top: 25px;'>
                <a href='https://fleet.daserdesign.ro' class='btn'>Connect to Dashboard</a>
            </div>
        </div>
        <div class='footer'>
            &copy; " . \date('Y') . " FleetLog Management System.
            <br>This is an automated notification. Please do not reply.
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

        if ($isHtml) {
            $body = self::wrapHtml($subject, $body);
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
        
        $msgId = \sprintf("<%s.%s@%s>", \base_convert(\microtime(), 10, 36), \base_convert(\bin2hex(\random_bytes(8)), 16, 36), ($_SERVER['HTTP_HOST'] ?? 'fleetlog.com'));

        $headers = [
            'Date: ' . \date('r'),
            'To: ' . $to,
            'From: ' . "$fromName <$fromEmail>",
            'Subject: ' . "=?UTF-8?B?" . \base64_encode($subject) . "?=",
            'Message-ID: ' . $msgId,
            'MIME-Version: 1.0',
            'Content-Type: ' . ($isHtml ? 'text/html' : 'text/plain') . '; charset=utf-8',
            'X-Priority: 3 (Normal)',
            'X-Mailer: FleetLog-Custom-SMTP'
        ];

        $fullMsg = \implode("\r\n", $headers) . "\r\n\r\n" . $body . "\r\n.";
        \fputs($socket, $fullMsg . "\r\n");
        $res = $getResponse($socket);

        $sendCommand($socket, "QUIT");
        \fclose($socket);

        return (\strpos($res, '250') === 0);
    }
}
