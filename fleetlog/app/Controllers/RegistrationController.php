<?php

namespace FleetLog\App\Controllers;

use FleetLog\Core\DB;
use FleetLog\Core\Auth;
use FleetLog\App\Repositories\UserRepository;

class RegistrationController extends BaseController
{
    /**
     * Show the driver signup form
     */
    public function showJoinForm(string $token): void
    {
        $tenant = DB::fetch("SELECT id, name, signup_enabled FROM tenants WHERE signup_token = ?", [$token]);

        if (!$tenant || !(int)$tenant['signup_enabled']) {
            $this->render('auth/join_error', [
                'title' => 'Link Nevalid',
                'message' => 'Acest link de înregistrare nu mai este valid sau a fost dezactivat.'
            ]);
            return;
        }

        $this->render('auth/join_form', [
            'title' => 'Înregistrare Șofer - ' . $tenant['name'],
            'tenant' => $tenant,
            'token' => $token
        ]);
    }

    /**
     * Process the driver signup
     */
    public function processJoin(string $token): void
    {
        $tenant = DB::fetch("SELECT id, name, signup_enabled FROM tenants WHERE signup_token = ?", [$token]);

        if (!$tenant || !(int)$tenant['signup_enabled']) {
            $this->redirect('/login?error=invalid_link');
            return;
        }

        $userRepo = new UserRepository();

        // Basic validation
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $cnp = trim($_POST['cnp'] ?? '');

        if (empty($name) || empty($email) || empty($password)) {
             $_SESSION['flash_error'] = "Te rugăm să completezi toate câmpurile obligatorii.";
             $this->redirect("/join/$token");
             return;
        }

        // Check if email already exists
        $existing = $userRepo->findByEmail($email);
        if ($existing) {
            $_SESSION['flash_error'] = "Această adresă de email este deja înregistrată.";
            $this->redirect("/join/$token");
            return;
        }

        $data = [
            'tenant_id' => (int)$tenant['id'],
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => $password,
            'role' => 'driver',
            'active' => 0, // Requires approval
            'cnp' => $cnp,
            'id_expiry' => !empty($_POST['id_expiry']) ? $_POST['id_expiry'] : null,
            'license_series' => $_POST['license_series'] ?? null,
            'license_expiry' => !empty($_POST['license_expiry']) ? $_POST['license_expiry'] : null,
        ];

        if ($userRepo->create($data)) {
            $this->render('auth/join_success', [
                'title' => 'Înregistrare Reușită',
                'tenant_name' => $tenant['name']
            ]);
        } else {
            $_SESSION['flash_error'] = "A apărut o eroare la salvarea datelor. Te rugăm să încerci din nou.";
            $this->redirect("/join/$token");
        }
    }
}
