<?php

namespace FleetLog\App\Controllers;

use FleetLog\Core\Auth;
use FleetLog\Core\DB;
use FleetLog\App\Repositories\TripRepository;
use FleetLog\App\Repositories\VehicleRepository;

class TripController extends BaseController
{
    private TripRepository $tripRepo;
    private VehicleRepository $vehicleRepo;

    public function __construct()
    {
        $this->tripRepo = new TripRepository();
        $this->vehicleRepo = new VehicleRepository();
    }

    public function showStartTrip(): void
    {
        $driverId = Auth::user()['id'];
        if ($this->tripRepo->hasOpenTrip($driverId)) {
            $this->redirect('/driver/dashboard');
        }

        $vehicles = $this->vehicleRepo->all();
        
        $selectedVehicleId = null;
        if (isset($_GET['qr'])) {
            $qrCode = $_GET['qr'];
            $vehicleByQr = $this->vehicleRepo->findByQrCode($qrCode);
            if ($vehicleByQr && $vehicleByQr['status'] === 'active') {
                $selectedVehicleId = $vehicleByQr['id'];
            }
        }

        // Fetch Tenant custom trip types
        $tenantId = Auth::tenantId();
        $tenant = DB::fetch("SELECT trip_types FROM tenants WHERE id = ?", [$tenantId]);
        $rawTypes = $tenant['trip_types'] ?? 'CURSE,NAVETA,LIVRARE SPECIALA,SERVICE,ALTE';
        $tripTypes = array_map('trim', explode(',', $rawTypes));

        $this->render('driver/trips/start', [
            'title' => 'Start Trip',
            'vehicles' => $vehicles,
            'tripTypes' => $tripTypes,
            'selectedVehicleId' => $selectedVehicleId
        ]);
    }

    public function start(): void
    {
        $driverId = Auth::user()['id'];
        $vehicleId = (int) $_POST['vehicle_id'];
        $startKm = (int) $_POST['start_km'];
        $type = $_POST['type'] ?? 'CURSE';
        $notes = $_POST['notes'] ?? '';

        $vehicle = $this->vehicleRepo->find($vehicleId);
        
        if (!$vehicle) {
            $this->redirect('/driver/start-trip?error=invalid_vehicle');
        }

        // Strict validation: cannot start with fewer KM than the vehicle currently has
        if ($startKm < $vehicle['current_odometer']) {
            $this->redirect('/driver/start-trip?error=invalid_km&min=' . $vehicle['current_odometer']);
        }

        $needsReview = false;
        if (abs($vehicle['current_odometer'] - $startKm) > 5) {
            $needsReview = true;
        }

        $tripId = $this->tripRepo->startTrip([
            'vehicle_id' => $vehicleId,
            'driver_id' => $driverId,
            'type' => $type,
            'start_time' => date('Y-m-d H:i:s'),
            'start_km' => $startKm,
            'needs_review' => $needsReview ? 1 : 0,
            'notes' => $notes
        ]);

        if ($tripId) {
            $this->redirect('/driver/dashboard');
        } else {
            $this->redirect('/driver/start-trip?error=failed');
        }
    }

    public function showEndTrip(): void
    {
        $driverId = Auth::user()['id'];
        $trip = DB::fetch("SELECT t.*, v.license_plate FROM trips t JOIN vehicles v ON t.vehicle_id = v.id WHERE t.driver_id = ? AND t.status = 'open' LIMIT 1", [$driverId]);
        
        if (!$trip) {
            $this->redirect('/driver/dashboard');
        }

        $this->render('driver/trips/end', [
            'title' => 'End Trip',
            'trip' => $trip
        ]);
    }

    public function end(): void
    {
        $driverId = Auth::user()['id'];
        $tripId = (int) $_POST['trip_id'];
        $endKm = (int) $_POST['end_km'];
        $notes = $_POST['notes'] ?? '';

        if ($this->tripRepo->closeTrip($tripId, $endKm, date('Y-m-d H:i:s'), $notes)) {
            $this->redirect('/driver/dashboard');
        } else {
            $this->redirect('/driver/end-trip?error=invalid_km');
        }
    }
}
