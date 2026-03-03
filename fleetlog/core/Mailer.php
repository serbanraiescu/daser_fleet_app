<?php

namespace FleetLog\Core;

use FleetLog\Core\DB;
use FleetLog\Core\EmailService;

class Mailer
{
    private static ?array $settings = null;

    public static function getSettings(): array
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

    public static function sendTemplate(int $tenantId, string $templateSlug, array $placeholders = [], bool $instant = false): bool
    {
        $template = DB::fetch("SELECT * FROM email_templates WHERE slug = ?", [$templateSlug]);
        if (!$template) return false;

        $recipients = self::getRecipientEmail($tenantId, $templateSlug);
        if (empty($recipients)) return false;

        $subject = $template['subject'];
        $body = $template['body'];

        foreach ($placeholders as $key => $value) {
            $subject = \str_replace('{' . $key . '}', (string)$value, $subject);
            $body = \str_replace('{' . $key . '}', (string)$value, $body);
        }

        $success = true;
        foreach ($recipients as $to) {
            if (!self::send($to, $subject, $body, true, $instant)) {
                $success = false;
            }
        }

        return $success;
    }

    public static function send(string $to, string $subject, string $body, bool $isHtml = true, bool $instant = false): bool
    {
        if ($isHtml) {
            $body = EmailService::wrapHtml($subject, $body);
        }
        
        if ($instant) {
            return EmailService::sendDirect($to, $subject, $body);
        }
        
        return EmailService::queue($to, $subject, $body);
    }
}
