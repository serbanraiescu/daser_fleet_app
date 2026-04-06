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
        $fromEmail = $settings['smtp_from_email'] ?: 'fleet@daserdesign.ro';
        $fromName  = $settings['smtp_from_name'] ?: 'FleetLog Notifications';
        
        $mail->setFrom($fromEmail, $fromName);
        $mail->addReplyTo($fromEmail, $fromName);
        $mail->Sender = $fromEmail; // Set Return-Path (Critical for SPF/Yahoo)

        // Content
        $mail->isHTML(true);
        
        // Yahoo & Gmail deliverability headers
        $mail->Priority = 3; // 3 = Normal, explicitly set
        $mail->XMailer  = ' '; // Mask the PHP origin to avoid spam hits
        
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
        $settings = Mailer::getSettings();
        $host = $_SERVER['HTTP_HOST'] ?? 'fleet.daserdesign.ro';
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

    /**
     * Send a detailed daily report to the admin for a specific tenant
     */
    public static function sendDetailedDailyReport(int $tenantId, string $date, string $adminEmail): bool
    {
        $tenant = DB::fetch("SELECT name FROM tenants WHERE id = ?", [$tenantId]);
        if (!$tenant) return false;

        $trips = DB::fetchAll("
            SELECT t.*, v.license_plate, u.name as driver_name 
            FROM trips t
            JOIN vehicles v ON t.vehicle_id = v.id
            JOIN users u ON t.driver_id = u.id
            WHERE t.tenant_id = ? AND DATE(t.start_time) = ?
            ORDER BY t.start_time ASC
        ", [$tenantId, $date]);

        $fuelings = DB::fetchAll("
            SELECT f.*, v.license_plate 
            FROM fuelings f
            JOIN vehicles v ON f.vehicle_id = v.id
            WHERE f.tenant_id = ? AND DATE(f.created_at) = ?
            ORDER BY f.created_at ASC
        ", [$tenantId, $date]);

        $driversCount = DB::fetch("SELECT COUNT(*) as count FROM users WHERE tenant_id = ? AND role = 'driver' AND active = 1 AND is_archived = 0", [$tenantId])['count'];
        $activeDrivers = DB::fetch("
            SELECT COUNT(DISTINCT driver_id) as count 
            FROM trips 
            WHERE tenant_id = ? AND DATE(start_time) = ?
        ", [$tenantId, $date])['count'];

        $html = "<h3 style='color: #1e3a8a; margin-top: 0;'>Rezumat Zilnic: {$tenant['name']}</h3>";
        $html .= "<p style='font-size: 14px; color: #64748b;'>Data: <strong>" . date('d.m.Y', strtotime($date)) . "</strong></p>";
        
        $html .= "<div style='margin: 20px 0; padding: 15px; background: #f0f9ff; border-radius: 8px; border: 1px solid #bae6fd;'>";
        $html .= "<span style='font-size: 14px; color: #0369a1;'><strong>Activitate Șoferi:</strong> {$activeDrivers} activi din {$driversCount} total.</span>";
        $html .= "</div>";

        // Trips Table
        $html .= "<h4 style='margin-bottom: 10px; color: #334155;'>Curse ({$activeDrivers} Curse)</h4>";
        if (empty($trips)) {
            $html .= "<p style='color: #6b7280; font-style: italic; font-size: 13px;'>Nicio cursă înregistrată astăzi.</p>";
        } else {
            $html .= "<table style='width: 100%; border-collapse: collapse; font-size: 13px;'>
                <thead><tr style='background: #f8fafc; text-align: left; color: #475569;'>
                <th style='padding: 10px; border: 1px solid #e5e7eb;'>Șofer</th>
                <th style='padding: 10px; border: 1px solid #e5e7eb;'>Vehicul</th>
                <th style='padding: 10px; border: 1px solid #e5e7eb;'>Ruta / Scop</th>
                <th style='padding: 10px; border: 1px solid #e5e7eb;'>KM</th>
                <th style='padding: 10px; border: 1px solid #e5e7eb;'>Status</th>
                </tr></thead><tbody>";
            foreach ($trips as $t) {
                $km = $t['distance'] ? number_format($t['distance'], 2) . " KM" : "-";
                $statusColor = $t['status'] === 'open' ? "#ef4444" : "#10b981";
                $html .= "<tr>
                    <td style='padding: 10px; border: 1px solid #e5e7eb; font-weight: 500;'>{$t['driver_name']}</td>
                    <td style='padding: 10px; border: 1px solid #e5e7eb;'>{$t['license_plate']}</td>
                    <td style='padding: 10px; border: 1px solid #e5e7eb;'>{$t['route_details']}</td>
                    <td style='padding: 10px; border: 1px solid #e5e7eb;'>{$km}</td>
                    <td style='padding: 10px; border: 1px solid #e5e7eb; color: {$statusColor}; font-weight: bold;'>" . strtoupper($t['status']) . "</td>
                </tr>";
            }
            $html .= "</tbody></table>";
        }

        // Fuelings Table
        $html .= "<h4 style='margin-top: 25px; margin-bottom: 10px; color: #334155;'>Alimentări</h4>";
        if (empty($fuelings)) {
            $html .= "<p style='color: #6b7280; font-style: italic; font-size: 13px;'>Nicio alimentare înregistrată astăzi.</p>";
        } else {
            $html .= "<table style='width: 100%; border-collapse: collapse; font-size: 13px;'>
                <thead><tr style='background: #f8fafc; text-align: left; color: #475569;'>
                <th style='padding: 10px; border: 1px solid #e5e7eb;'>Vehicul</th>
                <th style='padding: 10px; border: 1px solid #e5e7eb;'>Litri</th>
                <th style='padding: 10px; border: 1px solid #e5e7eb;'>Sumă</th>
                <th style='padding: 10px; border: 1px solid #e5e7eb;'>Stație</th>
                </tr></thead><tbody>";
            foreach ($fuelings as $f) {
                $html .= "<tr>
                    <td style='padding: 10px; border: 1px solid #e5e7eb; font-weight: 500;'>{$f['license_plate']}</td>
                    <td style='padding: 10px; border: 1px solid #e5e7eb;'>" . number_format($f['liters'], 2) . " L</td>
                    <td style='padding: 10px; border: 1px solid #e5e7eb;'>" . number_format($f['total_price'], 2) . " Lei</td>
                    <td style='padding: 10px; border: 1px solid #e5e7eb;'>{$f['gas_station']}</td>
                </tr>";
            }
            $html .= "</tbody></table>";
        }

        return self::queue($adminEmail, "Raport Zilnic Detaliat: {$tenant['name']}", self::wrapHtml("Raport Detaliat - {$tenant['name']}", $html));
    }

    /**
     * Send a monthly summary report to the admin
     */
    public static function sendDetailedMonthlyReport(int $tenantId, string $month, string $adminEmail): bool
    {
        $tenant = DB::fetch("SELECT name FROM tenants WHERE id = ?", [$tenantId]);
        if (!$tenant) return false;

        $stats = DB::fetch("
            SELECT 
                COUNT(*) as total_trips,
                SUM(distance) as total_km,
                (SELECT SUM(liters) FROM fuelings WHERE tenant_id = ? AND DATE_FORMAT(created_at, '%Y-%m') = ?) as total_liters,
                (SELECT SUM(total_price) FROM fuelings WHERE tenant_id = ? AND DATE_FORMAT(created_at, '%Y-%m') = ?) as total_fuel_cost
            FROM trips 
            WHERE tenant_id = ? AND DATE_FORMAT(start_time, '%Y-%m') = ?
        ", [$tenantId, $month, $tenantId, $month, $tenantId, $month]);

        $html = "<h3 style='color: #1e3a8a; margin-top: 0;'>Raport Lunar: {$tenant['name']}</h3>";
        $html .= "<p style='font-size: 14px; color: #64748b;'>Luna: <strong>" . date('F Y', strtotime($month . "-01")) . "</strong></p>";

        $html .= "<table style='width: 100%; border-collapse: separate; border-spacing: 10px; margin: 20px -10px;'>";
        $html .= "<tr>";
        $html .= "<td style='width: 50%; padding: 20px; background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 12px;'>
                    <div style='font-size: 11px; color: #64748b; font-weight: bold; text-transform: uppercase; margin-bottom: 5px;'>Total Distanță</div>
                    <div style='font-size: 24px; font-weight: bold; color: #1e293b;'>" . number_format($stats['total_km'] ?? 0, 2) . " <span style='font-size: 14px; font-weight: normal;'>KM</span></div>
                  </td>";
        $html .= "<td style='width: 50%; padding: 20px; background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 12px;'>
                    <div style='font-size: 11px; color: #64748b; font-weight: bold; text-transform: uppercase; margin-bottom: 5px;'>Total Curse</div>
                    <div style='font-size: 24px; font-weight: bold; color: #1e293b;'>{$stats['total_trips']}</div>
                  </td>";
        $html .= "</tr><tr>";
        $html .= "<td style='width: 50%; padding: 20px; background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 12px;'>
                    <div style='font-size: 11px; color: #64748b; font-weight: bold; text-transform: uppercase; margin-bottom: 5px;'>Consum Total</div>
                    <div style='font-size: 24px; font-weight: bold; color: #1e293b;'>" . number_format($stats['total_liters'] ?? 0, 2) . " <span style='font-size: 14px; font-weight: normal;'>L</span></div>
                  </td>";
        $html .= "<td style='width: 50%; padding: 20px; background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 12px;'>
                    <div style='font-size: 11px; color: #64748b; font-weight: bold; text-transform: uppercase; margin-bottom: 5px;'>Cost Combustibil</div>
                    <div style='font-size: 24px; font-weight: bold; color: #1e293b;'>" . number_format($stats['total_fuel_cost'] ?? 0, 2) . " <span style='font-size: 14px; font-weight: normal;'>Lei</span></div>
                  </td>";
        $html .= "</tr></table>";

        return self::queue($adminEmail, "Raport Lunar: {$tenant['name']} (" . date('M Y', strtotime($month . "-01")) . ")", self::wrapHtml("Rezumat Lunar - {$tenant['name']}", $html));
    }
}
