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
            if (getenv('SMTP_PASS')) {
                self::$settings['smtp_pass'] = getenv('SMTP_PASS');
            }
            if (getenv('SMTP_USER')) {
                self::$settings['smtp_user'] = getenv('SMTP_USER');
            }
            if (getenv('SMTP_HOST')) {
                self::$settings['smtp_host'] = getenv('SMTP_HOST');
            }
        }
        return self::$settings;
    }

    public static function getRecipientEmail(int $tenantId, string $templateSlug): ?string
    {
        $template = DB::fetch("SELECT recipient_type FROM email_templates WHERE slug = ?", [$templateSlug]);
        if (!$template) return null;

        if ($template['recipient_type'] === 'admin') {
            // Find the primary tenant admin
            $admin = DB::fetch("SELECT email FROM users WHERE tenant_id = ? AND role = 'tenant_admin' AND active = 1 LIMIT 1", [$tenantId]);
            return $admin['email'] ?? null;
        }

        // Default to tenant company email
        $tenant = DB::fetch("SELECT email FROM tenants WHERE id = ?", [$tenantId]);
        return $tenant['email'] ?? null;
    }

    public static function sendTemplate(int $tenantId, string $templateSlug, array $placeholders = []): bool
    {
        $template = DB::fetch("SELECT * FROM email_templates WHERE slug = ?", [$templateSlug]);
        if (!$template) {
            return false;
        }

        $to = self::getRecipientEmail($tenantId, $templateSlug);
        if (!$to) {
            return false;
        }

        $subject = $template['subject'];
        $body = $template['body'];

        foreach ($placeholders as $key => $value) {
            $subject = str_replace('{' . $key . '}', $value, $subject);
            $body = str_replace('{' . $key . '}', $value, $body);
        }

        return self::send($to, $subject, $body);
    }

    public static function send(string $to, string $subject, string $body): bool
    {
        $settings = self::getSettings();
        
        $host = $settings['smtp_host'] ?: 'localhost';
        $port = (int)($settings['smtp_port'] ?: 25);
        $user = $settings['smtp_user'] ?: '';
        $pass = $settings['smtp_pass'] ?: '';
        $encryption = $settings['smtp_enc'] ?: 'none';
        
        $fromEmail = $settings['smtp_from_email'] ?: ($user ?: 'noreply@' . ($_SERVER['HTTP_HOST'] ?? 'fleetlog.com'));
        $fromName = $settings['smtp_from_name'] ?: 'FleetLog';

        $timeout = 10;
        $socketHost = $host;
        
        // Port 465 is usually implicit SSL
        if ($port === 465 || $encryption === 'ssl') {
            $socketHost = "ssl://" . $host;
        }

        $socket = @\fsockopen($socketHost, $port, $errno, $errstr, $timeout);
        if (!$socket) {
            \error_log("Mailer Connection Failed: $errstr ($errno)");
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

        // Initial response
        $getResponse($socket);

        // HELO/EHLO
        $sendCommand($socket, "EHLO " . ($_SERVER['HTTP_HOST'] ?? 'localhost'));

        // STARTTLS if port 587 and not already SSL
        if ($port === 587 || $encryption === 'tls') {
            $res = $sendCommand($socket, "STARTTLS");
            if (\strpos($res, '220') === 0) {
                if (!\stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                    \error_log("Mailer STARTTLS failed");
                    return false;
                }
                $sendCommand($socket, "EHLO " . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
            }
        }

        // Authentication
        if (!empty($user) && !empty($pass)) {
            $sendCommand($socket, "AUTH LOGIN");
            $sendCommand($socket, \base64_encode($user));
            $res = $sendCommand($socket, \base64_encode($pass));
            if (\strpos($res, '235') !== 0) {
                \error_log("Mailer Auth Failed: " . $res);
                return false;
            }
        }

        // MAIL FROM
        $sendCommand($socket, "MAIL FROM:<$fromEmail>");
        
        // RCPT TO
        $res = $sendCommand($socket, "RCPT TO:<$to>");
        if (\strpos($res, '250') !== 0 && \strpos($res, '251') !== 0) {
            \error_log("Mailer Recipient Rejected: " . $res);
            return false;
        }

        // DATA
        $sendCommand($socket, "DATA");
        
        $headers = [
            'Date: ' . \date('r'),
            'To: ' . $to,
            'From: ' . "$fromName <$fromEmail>",
            'Subject: ' . "=?UTF-8?B?" . \base64_encode($subject) . "?=",
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=utf-8',
            'Content-Transfer-Encoding: 8bit',
            'X-Mailer: FleetLog-Custom-SMTP'
        ];

        $message = \implode("\r\n", $headers) . "\r\n\r\n" . $body . "\r\n.";
        $res = $sendCommand($socket, $message);

        // QUIT
        $sendCommand($socket, "QUIT");
        \fclose($socket);

        return (\strpos($res, '250') === 0);
    }
}
