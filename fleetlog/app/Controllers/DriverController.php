<?php

namespace FleetLog\App\Controllers;

use FleetLog\Core\Auth;
use FleetLog\Core\RBAC;
use FleetLog\App\Repositories\VehicleRepository;
use FleetLog\App\Repositories\TripRepository;

class DriverController extends BaseController
{
    public function dashboard(): void
    {
        $tripRepo = new TripRepository();
        $vehicleRepo = new VehicleRepository();
        
        $driverId = Auth::user()['id'];
        $activeTrip = $tripRepo->getOpenTrip($driverId);
        $vehicles = $vehicleRepo->all();

        $this->render('driver/dashboard', [
            'title' => 'Driver Dashboard',
            'hasOpenTrip' => (bool)$activeTrip,
            'activeTrip' => $activeTrip,
            'vehicles' => $vehicles
        ]);
    }

    public function showCompleteProfile(): void
    {
        $this->render('driver/complete_profile', [
            'title' => 'Complete your Profile',
            'user' => Auth::user()
        ]);
    }

    public function updateProfile(): void
    {
        $repo = new \FleetLog\App\Repositories\UserRepository();
        $user = Auth::user();
        
        $data = [
            'name' => $user['name'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'active' => 1,
            'cnp' => $_POST['cnp'] ?? null,
            'id_expiry' => !empty($_POST['id_expiry']) ? $_POST['id_expiry'] : null,
            'license_series' => $_POST['license_series'] ?? null,
            'license_expiry' => !empty($_POST['license_expiry']) ? $_POST['license_expiry'] : null
        ];

        if ($repo->update($user['id'], $data)) {
            $this->redirect('/driver/dashboard?success=profile_completed');
        } else {
            $this->render('driver/complete_profile', [
                'title' => 'Complete your Profile',
                'user' => array_merge($user, $data),
                'error' => 'Failed to save profile. Please try again.'
            ]);
        }
    }
}
