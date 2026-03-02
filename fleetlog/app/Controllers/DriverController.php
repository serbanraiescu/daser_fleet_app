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
}
