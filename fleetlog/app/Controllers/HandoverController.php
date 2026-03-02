<?php

namespace FleetLog\App\Controllers;

use FleetLog\Core\Auth;
use FleetLog\App\Repositories\VehicleRepository;
use FleetLog\App\Repositories\HandoverRepository;
use FleetLog\App\Repositories\UserRepository;

class HandoverController extends BaseController
{
    private HandoverRepository $handoverRepo;
    private VehicleRepository $vehicleRepo;

    public function __construct()
    {
        $this->handoverRepo = new HandoverRepository();
        $this->vehicleRepo = new VehicleRepository();
    }

    public function show(): void
    {
        $vehicles = $this->vehicleRepo->all();
        $userRepo = new UserRepository();
        $drivers = $userRepo->getDrivers();

        $this->render('driver/handovers/create', [
            'title' => 'Vehicle Handover',
            'vehicles' => $vehicles,
            'drivers' => $drivers
        ]);
    }

    public function store(): void
    {
        $fromUserId = Auth::user()['id'];
        $toUserId = (int) $_POST['to_user_id'];
        $vehicleId = (int) $_POST['vehicle_id'];
        $odometerKm = (int) $_POST['odometer_km'];
        $hasDamage = isset($_POST['has_damage']) ? 1 : 0;
        $notes = $_POST['notes'] ?? '';

        $handoverId = $this->handoverRepo->recordHandover([
            'vehicle_id' => $vehicleId,
            'from_user_id' => $fromUserId,
            'to_user_id' => $toUserId,
            'datetime' => date('Y-m-d H:i:s'),
            'odometer_km' => $odometerKm,
            'has_damage' => $hasDamage,
            'notes' => $notes
        ]);

        if ($handoverId) {
            if ($hasDamage) {
                // Redirect to damage report if damage was flagged
                $this->redirect('/driver/report-damage?vehicle_id=' . $vehicleId . '&from_handover=' . $handoverId);
            } else {
                $this->redirect('/driver/dashboard?success=handover');
            }
        } else {
            $this->redirect('/driver/handover?error=failed');
        }
    }
}
