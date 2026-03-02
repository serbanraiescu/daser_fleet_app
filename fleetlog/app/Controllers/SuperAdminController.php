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

        DB::query("UPDATE tenants SET name = ?, email = ?, cui = ?, status = ? WHERE id = ?", [
            $name, $email, $cui, $status, $id
        ]);

        $this->redirect('/admin/tenants?success=tenant_updated');
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

        // 1. Create Tenant
        DB::query("INSERT INTO tenants (name, email, cui, status) VALUES (?, ?, ?, 'active')", [
            $name, $email, $cui
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
}
