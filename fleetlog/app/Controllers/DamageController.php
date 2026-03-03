<?php

namespace FleetLog\App\Controllers;

use FleetLog\Core\Auth;
use FleetLog\App\Repositories\DamageReportRepository;
use FleetLog\App\Repositories\VehicleRepository;

class DamageController extends BaseController
{
    private DamageReportRepository $damageRepo;
    private VehicleRepository $vehicleRepo;

    public function __construct()
    {
        $this->damageRepo = new DamageReportRepository();
        $this->vehicleRepo = new VehicleRepository();
    }

    public function showReport(): void
    {
        $vehicles = $this->vehicleRepo->all();
        $vehicleId = $_GET['vehicle_id'] ?? null;
        $isLocked = false;

        if (isset($_GET['qr'])) {
            $qrCode = $_GET['qr'];
            $vehicleByQr = $this->vehicleRepo->findByQrCode($qrCode);
            if ($vehicleByQr && $vehicleByQr['status'] === 'active') {
                $vehicleId = $vehicleByQr['id'];
            }
        }

        // Lock vehicle if driver has an active trip
        $tripRepo = new \FleetLog\App\Repositories\TripRepository();
        $activeTrip = $tripRepo->getOpenTrip(Auth::user()['id']);
        if ($activeTrip) {
            $vehicleId = $activeTrip['vehicle_id'];
            $isLocked = true;
        }

        $this->render('driver/damage/create', [
            'title' => 'Report Damage',
            'vehicles' => $vehicles,
            'selectedVehicleId' => $vehicleId,
            'isLocked' => $isLocked
        ]);
    }

    public function store(): void
    {
        $driverId = Auth::user()['id'];
        $vehicleId = (int) $_POST['vehicle_id'];
        $category = $_POST['category'];
        $severity = $_POST['severity'];
        $description = $_POST['description'];
        $datetime = date('Y-m-d H:i:s');

        $reportId = $this->damageRepo->create([
            'vehicle_id' => $vehicleId,
            'driver_id' => $driverId,
            'datetime' => $datetime,
            'category' => $category,
            'severity' => $severity,
            'description' => $description
        ]);

        if ($reportId && isset($_FILES['photos'])) {
            $this->handlePhotoUploads($reportId, $_FILES['photos']);
        }

        // Send Email Notification
        if ($reportId) {
            $vehicle = $this->vehicleRepo->find($vehicleId);
            $driver = Auth::user();
            $tenantId = Auth::tenantId();

            \FleetLog\Core\Mailer::sendTemplate($tenantId, 'new_damage', [
                'vehicle_plate' => $vehicle['license_plate'] ?? 'Unknown',
                'driver_name' => $driver['name'],
                'datetime' => $datetime
            ], true);
        }

        $this->redirect('/driver/dashboard?success=damage_reported');
    }

    private function handlePhotoUploads(int $reportId, array $files): void
    {
        $tenantId = Auth::tenantId();
        // Path relative to the public directory
        $publicRelativePath = "uploads/tenants/{$tenantId}/damages/" . date('Y-m');
        
        // Use DOCUMENT_ROOT to ensure we hit the actual public folder (public_html on cPanel)
        $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\');
        $uploadDir = $docRoot . '/' . $publicRelativePath;
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        foreach ($files['tmp_name'] as $key => $tmpName) {
            if ($files['error'][$key] === UPLOAD_ERR_OK && $files['size'][$key] <= 2 * 1024 * 1024) {
                $ext = pathinfo($files['name'][$key], PATHINFO_EXTENSION);
                if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'webp'])) {
                    $filename = uniqid('dmg_') . '.' . $ext;
                    $targetPath = $uploadDir . '/' . $filename;
                    
                    if (move_uploaded_file($tmpName, $targetPath)) {
                        $dbPath = $publicRelativePath . '/' . $filename;
                        $this->damageRepo->addPhoto($reportId, $dbPath);
                    }
                }
            }
            if ($key >= 5) break; // Max 6 photos
        }
    }
}
