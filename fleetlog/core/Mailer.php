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
        
        $fromEmail = $settings['smtp_from_email'] ?: 'noreply@' . ($_SERVER['HTTP_HOST'] ?? 'fleetlog.com');
        $fromName = $settings['smtp_from_name'] ?: 'FleetLog';

        $headers = [
            'From' => "$fromName <$fromEmail>",
            'Reply-To' => $fromEmail,
            'X-Mailer' => 'PHP/' . phpversion(),
            'Content-Type' => 'text/plain; charset=utf-8'
        ];

        // For now, use basic PHP mail()
        // In a production environment with PHPMailer, we would use the SMTP settings here.
        $headerString = "";
        foreach ($headers as $k => $v) {
            $headerString .= "$k: $v\r\n";
        }

        return mail($to, $subject, $body, $headerString);
    }
}
