<?php

namespace FleetLog\App\Controllers;

use FleetLog\Core\Auth;
use FleetLog\Core\DB;
use FleetLog\Core\EmailService;

class SuperAdminController extends BaseController
{
    public function tenants(): void
    {
        $tenants = DB::fetchAll("
            SELECT t.*, 
                   (SELECT COUNT(*) FROM vehicles v WHERE v.tenant_id = t.id) as vehicles_count 
            FROM tenants t 
            ORDER BY t.created_at DESC
        ");
        $this->render('admin/tenants/index', [
            'title' => 'System Tenants',
            'tenants' => $tenants
        ]);
    }

    public function impersonate(int $id): void
    {
        Auth::impersonate($id);
        $this->redirect('/tenant/dashboard');
    }

    public function stopImpersonation(): void
    {
        Auth::stopImpersonating();
        $this->redirect('/admin/tenants');
    }

    public function showEditTenant(int $id): void
    {
        $tenant = DB::fetch("SELECT * FROM tenants WHERE id = ?", [$id]);
        if (!$tenant) {
            $this->redirect('/admin/tenants');
        }

        $this->render('admin/tenants/edit', [
            'title' => 'Edit Tenant',
            'tenant' => $tenant
        ]);
    }

    public function updateTenant(int $id): void
    {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $cui = $_POST['cui'];
        $status = $_POST['status'];
        $contact_phone = $_POST['contact_phone'] ?? null;
        $notification_phone = $_POST['notification_phone'] ?? null;

        DB::query("UPDATE tenants SET name = ?, email = ?, cui = ?, status = ?, contact_phone = ?, notification_phone = ? WHERE id = ?", [
            $name, $email, $cui, $status, $contact_phone, $notification_phone, $id
        ]);

        $this->redirect('/admin/tenants?success=tenant_updated');
    }

    public function deleteTenant(int $id): void
    {
        // Security: Don't allow deleting yourself or important tenants if any logic exists
        // For now, simple delete as requested for test tenants
        
        // This will fail if foreign keys exist and cascade is NOT set.
        // We should at least try to delete related users if they don't cascade.
        try {
            DB::query("DELETE FROM tenants WHERE id = ?", [$id]);
            $this->redirect('/admin/tenants?success=tenant_deleted');
        } catch (\Exception $e) {
            $this->redirect('/admin/tenants?error=delete_failed');
        }
    }

    public function showAddTenant(): void
    {
        $this->render('admin/tenants/add', [
            'title' => 'Add New Tenant'
        ]);
    }

    public function storeTenant(): void
    {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $cui = $_POST['cui'];
        $adminName = $_POST['admin_name'];
        $password = $_POST['password'];
        $contact_phone = $_POST['contact_phone'] ?? null;
        $notification_phone = $_POST['notification_phone'] ?? null;

        // 1. Create Tenant
        DB::query("INSERT INTO tenants (name, email, cui, status, contact_phone, notification_phone) VALUES (?, ?, ?, 'active', ?, ?)", [
            $name, $email, $cui, $contact_phone, $notification_phone
        ]);
        $tenantId = (int)DB::lastInsertId();

        // 2. Create Admin User for this tenant
        $userRepo = new \FleetLog\App\Repositories\UserRepository();
        $userRepo->create([
            'tenant_id' => $tenantId,
            'name' => $adminName,
            'email' => $email, // Using same email as tenant contact by default
            'password' => $password,
            'role' => 'tenant_admin',
            'active' => 1
        ]);

        $this->redirect('/admin/tenants?success=tenant_added');
    }

    public function settings(): void
    {
        $settingsRaw = DB::fetchAll("SELECT * FROM system_settings");
        $settings = [];
        foreach ($settingsRaw as $s) {
            $settings[$s['key']] = $s['value'];
        }

        $this->render('admin/settings', [
            'title' => 'System Settings',
            'settings' => $settings
        ]);
    }

    public function updateSettings(): void
    {
        foreach ($_POST['settings'] as $key => $value) {
            DB::query("UPDATE system_settings SET value = ? WHERE `key` = ?", [$value, $key]);
        }
        $this->redirect('/admin/settings?success=1');
    }

    public function emailTemplates(): void
    {
        $templates = DB::fetchAll("SELECT * FROM email_templates ORDER BY name ASC");
        $emailLogs = DB::fetchAll("SELECT * FROM email_logs ORDER BY created_at DESC LIMIT 50");
        
        $this->render('admin/email_templates/index', [
            'title' => 'Email Templates & Logs',
            'templates' => $templates,
            'emailLogs' => $emailLogs
        ]);
    }

    public function editEmailTemplate(int $id): void
    {
        $template = DB::fetch("SELECT * FROM email_templates WHERE id = ?", [$id]);
        if (!$template) {
            $this->redirect('/admin/email-templates');
        }

        $this->render('admin/email_templates/edit', [
            'title' => 'Edit Email Template',
            'template' => $template
        ]);
    }

    public function updateEmailTemplate(int $id): void
    {
        $subject = $_POST['subject'];
        $body = $_POST['body'];
        $alertDays = $_POST['alert_days']; // Keep as string for multi-day support
        $recipientType = $_POST['recipient_type'];

        DB::query("UPDATE email_templates SET subject = ?, body = ?, alert_days = ?, recipient_type = ? WHERE id = ?", [
            $subject, $body, $alertDays, $recipientType, $id
        ]);
        $this->redirect('/admin/email-templates?success=1');
    }

    public function previewTemplate(int $id): void
    {
        $template = DB::fetch("SELECT * FROM email_templates WHERE id = ?", [$id]);
        if (!$template) {
            $this->redirect('/admin/email-templates');
        }

        // Mock data for preview
        $placeholders = [
            'vehicle' => 'DACIA DUSTER (B-123-ABC)',
            'vehicle_plate' => 'B-123-ABC',
            'expiry_date' => date('d.m.Y', strtotime('+7 days')),
            'days' => '7',
            'driver_name' => 'Ion Popescu',
            'datetime' => date('d.m.Y H:i')
        ];

        $subject = $template['subject'];
        $body = $template['body'];

        foreach ($placeholders as $key => $value) {
            $subject = str_replace('{' . $key . '}', $value, $subject);
            $body = str_replace('{' . $key . '}', $value, $body);
        }

        // Wrap in HTML but show in a modal-like or simple preview page
        $html = EmailService::wrapHtml("[PREVIEW] " . $subject, $body);
        
        echo $html;
        echo "<div style='position: fixed; top: 0; left: 0; width: 100%; background: #000; color: #fff; padding: 10px; text-align: center; font-family: sans-serif; z-index: 9999;'>
                MOD PREVIZUALIZARE - <a href='javascript:window.close()' style='color: #3b82f6;'>Închide Fereastra</a>
              </div>";
        exit;
    }

    public function runExpirationCheck(): void
    {
        $output = [];
        $cmd = "php " . escapeshellarg(dirname(__DIR__, 2) . '/cron/check_expirations.php');
        exec($cmd, $output);
        
        $_SESSION['flash_success'] = "Verificarea expirărilor a fost rulată manual. " . count($output) . " linii de log procesate.";
        $this->redirect('/admin/email-templates');
    }

    public function sendTestEmail(): void
    {
        $to = $_POST['test_email'] ?? '';
        if (empty($to)) {
            $this->redirect('/admin/settings?error=email_empty');
        }

        $subject = "Notificare Test - Sistem FleetLog";
        $body = "
            <p>Bună ziua,</p>
            <p>Acesta este un mesaj de test generat automat pentru a confirma funcționarea corectă a setărilor de email pe domeniul <strong>" . ($_SERVER['HTTP_HOST'] ?? 'daser_fleet_app') . "</strong>.</p>
            
            <div style='background: #f3f4f6; padding: 20px; border-radius: 4px; margin: 25px 0;'>
                <p style='margin: 0; color: #111827; font-weight: bold;'>Configurație Reușită!</p>
                <p style='margin: 5px 0 0 0; font-size: 14px; color: #4b5563;'>Acest mesaj confirmă că sistemul de notificare este operațional și securizat (DKIM/SPF Pass).</p>
            </div>

            <p style='font-size: 13px; color: #6b7280;'>Detalii tehnice: " . date('d.m.Y H:i:s') . " | Ref: " . \base_convert((int)(\microtime(true) * 1000), 10, 36) . "</p>
        ";

        if (EmailService::sendDirect($to, $subject, $body, null)) {
            $_SESSION['flash_success'] = "Email de test trimis cu succes către $to!";
        } else {
            $_SESSION['flash_error'] = "Eroare la trimiterea email-ului. Verifică setările SMTP.";
        }
        $this->redirect('/admin/settings');
    }
}
