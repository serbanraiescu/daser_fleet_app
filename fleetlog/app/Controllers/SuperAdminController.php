<?php

namespace FleetLog\App\Controllers;

use FleetLog\Core\Auth;
use FleetLog\Core\DB;

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
        $alertDays = (int)$_POST['alert_days'];
        $recipientType = $_POST['recipient_type'];

        DB::query("UPDATE email_templates SET subject = ?, body = ?, alert_days = ?, recipient_type = ? WHERE id = ?", [
            $subject, $body, $alertDays, $recipientType, $id
        ]);
        $this->redirect('/admin/email-templates?success=1');
    }

    public function sendTestEmail(): void
    {
        $to = $_POST['test_email'] ?? '';
        if (empty($to)) {
            $this->redirect('/admin/settings?error=email_empty');
        }

        $subject = "Notificare Test - Sistem FleetLog (" . date('H:i') . ")";
        $body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 12px; color: #334155;'>
                <div style='text-align: center; margin-bottom: 20px;'>
                    <h1 style='color: #1e40af; margin: 0;'>FleetLog</h1>
                    <p style='color: #64748b; font-size: 14px;'>Sistem Gestiune Flotă Auto</p>
                </div>
                
                <h2 style='color: #1e293b; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;'>Verificare Configurație Email</h2>
                
                <p>Bună ziua,</p>
                <p>Acesta este un mesaj de test generat automat pentru a confirma funcționarea corectă a setărilor SMTP pe domeniul <strong>" . ($_SERVER['HTTP_HOST'] ?? 'daser_fleet_app') . "</strong>.</p>
                
                <div style='background: #eff6ff; padding: 20px; border-radius: 8px; border-left: 4px solid #3b82f6; margin: 25px 0;'>
                    <p style='margin: 0; color: #1e40af; font-weight: bold;'>Configurație Reușită!</p>
                    <p style='margin: 5px 0 0 0; font-size: 14px; color: #1e3a8a;'>Dacă citiți acest mesaj în Inbox, înseamnă că serverul de mail este securizat și autorizat corect.</p>
                </div>

                <p style='font-size: 13px;'>Detalii tehnice trimitere:</p>
                <ul style='font-size: 12px; color: #64748b; background: #f8fafc; padding: 15px 15px 15px 35px; border-radius: 6px;'>
                    <li>Data: " . date('d.m.Y H:i:s') . "</li>
                    <li>Sursă: " . ($_SERVER['REMOTE_ADDR'] ?? 'Server Local') . "</li>
                    <li>Referință: " . \base_convert(\microtime(true), 10, 36) . "</li>
                </ul>

                <hr style='border: 0; border-top: 1px solid #e2e8f0; margin: 30px 0;'>
                <p style='font-size: 11px; color: #94a3b8; text-align: center;'>Acesta este un mesaj tehnic. Vă rugăm să nu răspundeți direct la acest email.<br>&copy; " . date('Y') . " Daser Fleet App</p>
            </div>
        ";

        if (\FleetLog\Core\Mailer::send($to, $subject, $body, true)) {
            $this->redirect('/admin/settings?success=test_sent');
        } else {
            $this->redirect('/admin/settings?error=test_failed');
        }
    }
}
