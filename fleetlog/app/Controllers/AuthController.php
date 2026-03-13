<?php

namespace FleetLog\App\Controllers;

use FleetLog\Core\Auth;

class AuthController extends BaseController
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            $this->redirectByRole();
        }
        $this->render('auth/login', ['title' => 'Login - FleetLog']);
    }

    public function login(): void
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $pin = $_POST['pin'] ?? '';
        $remember = isset($_POST['remember']);

        $success = false;
        if (!empty($pin)) {
            $success = Auth::loginWithPin($email, $pin, $remember);
        } else {
            $success = Auth::login($email, $password, $remember);
        }

        if ($success) {
            $this->redirectByRole();
        } else {
            $this->render('auth/login', [
                'title' => 'Login - FleetLog',
                'error' => 'Invalid email, password or PIN.'
            ]);
        }
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('/login');
    }

    public function stopImpersonating(): void
    {
        Auth::stopImpersonating();
        $this->redirect('/admin/tenants');
    }

    public function suspended(): void
    {
        $this->render('auth/suspended', ['title' => 'Account Suspended']);
    }

    private function redirectByRole(): void
    {
        $role = Auth::role();
        if ($role === 'super_admin') {
            $this->redirect('/admin/dashboard');
        } elseif ($role === 'tenant_admin') {
            $this->redirect('/tenant/dashboard');
        } else {
            $this->redirect('/driver/dashboard');
        }
    }
}
