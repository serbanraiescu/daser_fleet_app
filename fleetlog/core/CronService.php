<?php

namespace FleetLog\Core;

use Exception;

class CronService
{
    /**
     * Handle photo cleanup based on retention settings
     */
    public static function handlePhotoCleanup(): int
    {
        $deletedCount = 0;
        $tenants = DB::fetchAll("SELECT id, settings FROM tenants");

        foreach ($tenants as $tenant) {
            $settings = json_decode($tenant['settings'] ?? '{}', true);
            $retentionDays = $settings['retention_days_photos'] ?? 365;
            $tenantId = $tenant['id'];

            $oldPhotos = DB::fetchAll("SELECT * FROM damage_photos WHERE tenant_id = ? AND created_at < DATE_SUB(NOW(), INTERVAL ? DAY)", [$tenantId, $retentionDays]);

            foreach ($oldPhotos as $photo) {
                $filePath = dirname(__DIR__, 2) . '/storage/' . $photo['path'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                DB::query("DELETE FROM damage_photos WHERE id = ?", [$photo['id']]);
                $deletedCount++;
            }
        }
        return $deletedCount;
    }

    /**
     * Generate and queue weekly summary reports
     */
    public static function handleWeeklyReport(): bool
    {
        $reportsTo = getenv('REPORTS_TO_EMAIL') ?: ($_ENV['REPORTS_TO_EMAIL'] ?? $_SERVER['REPORTS_TO_EMAIL'] ?? null);
        if (!$reportsTo) {
            return false;
        }

        $tenants = DB::fetchAll("SELECT * FROM tenants WHERE status = 'active'");
        $summary = "Weekly FleetLog Summary Report - " . date('Y-m-d') . "\n";
        $summary .= str_repeat("=", 40) . "\n\n";

        foreach ($tenants as $tenant) {
            $tenantId = $tenant['id'];
            $tripsCount = DB::fetch("SELECT COUNT(*) as count FROM trips WHERE tenant_id = ? AND created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)", [$tenantId])['count'];
            $kmTotal = DB::fetch("SELECT SUM(end_km - start_km) as km FROM trips WHERE tenant_id = ? AND status = 'closed' AND created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)", [$tenantId])['km'] ?: 0;
            $fuelLiters = DB::fetch("SELECT SUM(liters) as liters FROM fuel_entries WHERE tenant_id = ? AND created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)", [$tenantId])['liters'] ?: 0;
            $damages = DB::fetch("SELECT COUNT(*) as count FROM damage_reports WHERE tenant_id = ? AND created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)", [$tenantId])['count'];

            $summary .= "TENANT: {$tenant['name']}\n";
            $summary .= "- Trips: $tripsCount\n";
            $summary .= "- Total KM: $kmTotal\n";
            $summary .= "- Fuel: $fuelLiters liters\n";
            $summary .= "- New Incidents: $damages\n";
            $summary .= str_repeat("-", 20) . "\n\n";
        }

        $htmlReport = "<h2>Raport Săptămânal FleetLog</h2><pre style='background:#f4f4f4; padding:15px; border-radius:4px; font-family:monospace;'>" . htmlspecialchars($summary) . "</pre>";
        $subject = "Raport Săptămânal FleetLog - " . date('d.m.Y');

        return EmailService::queue($reportsTo, $subject, EmailService::wrapHtml($subject, $htmlReport), $summary);
    }
}
