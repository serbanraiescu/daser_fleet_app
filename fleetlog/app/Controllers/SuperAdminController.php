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

        $emailLogs = DB::fetchAll("SELECT * FROM email_logs ORDER BY created_at DESC LIMIT 50");

        $this->render('admin/settings', [
            'title' => 'System Settings',
            'settings' => $settings,
            'emailLogs' => $emailLogs
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
        $this->render('admin/email_templates/index', [
            'title' => 'Email Templates',
            'templates' => $templates
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

        $subject = "FleetLog - Test Email Configuration";
        $body = "Congratulations! If you are reading this, your email configuration (SMTP) is working correctly.\n\nTime sent: " . date('Y-m-d H:i:s');

        if (\FleetLog\Core\Mailer::send($to, $subject, $body)) {
            $this->redirect('/admin/settings?success=test_sent');
        } else {
            $this->redirect('/admin/settings?error=test_failed');
        }
    }
}
