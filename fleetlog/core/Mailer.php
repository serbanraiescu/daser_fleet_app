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
        }
        return self::$settings;
    }

    public static function sendTemplate(string $to, string $templateSlug, array $placeholders = []): bool
    {
        $template = DB::fetch("SELECT * FROM email_templates WHERE slug = ?", [$templateSlug]);
        if (!$template) {
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
