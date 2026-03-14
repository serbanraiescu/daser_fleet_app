<?php

namespace FleetLog\App\Controllers;

use FleetLog\Core\Auth;
use FleetLog\Core\DB;
use FleetLog\Core\RBAC;
use FleetLog\App\Repositories\VehicleRepository;
use FleetLog\App\Repositories\VehicleEventRepository;

class VehicleEventController extends BaseController
{
    private VehicleEventRepository $eventRepo;
    private VehicleRepository $vehicleRepo;

    public function __construct()
    {
        parent::__construct();
        RBAC::requireRole(['tenant_admin', 'super_admin']);
        $this->eventRepo = new VehicleEventRepository();
        $this->vehicleRepo = new VehicleRepository();
    }

    public function index(): void
    {
        $tenantId = Auth::tenantId();
        $vehicles = $this->vehicleRepo->getByTenant($tenantId);
        
        $selectedVehicleId = $_GET['vehicle_id'] ?? null;
        $events = [];
        $selectedVehicle = null;

        if ($selectedVehicleId) {
            $selectedVehicle = $this->vehicleRepo->find((int)$selectedVehicleId);
            // Ensure vehicle belongs to tenant
            if ($selectedVehicle && $selectedVehicle['tenant_id'] == $tenantId) {
                $events = $this->eventRepo->getByVehicle((int)$selectedVehicleId);
                
                // Fetch photos for each event
                foreach ($events as &$event) {
                    $event['photos'] = $this->eventRepo->getPhotos($event['id']);
                }
            } else {
                $selectedVehicle = null;
            }
        }

        $this->render('tenant/events/index', [
            'title' => 'Vehicle Timeline (BETA)',
            'vehicles' => $vehicles,
            'selectedVehicle' => $selectedVehicle,
            'events' => $events
        ]);
    }

    public function store(): void
    {
        $tenantId = Auth::tenantId();
        $vehicleId = (int) $_POST['vehicle_id'];
        
        // Verify ownership
        $vehicle = $this->vehicleRepo->find($vehicleId);
        if (!$vehicle || $vehicle['tenant_id'] != $tenantId) {
            $this->redirect('/tenant/vehicle-events?error=invalid_vehicle');
            return;
        }

        $data = [
            'vehicle_id' => $vehicleId,
            'event_type' => $_POST['event_type'],
            'event_subtype' => $_POST['event_subtype'] ?? '',
            'event_date' => $_POST['event_date'],
            'odometer' => $_POST['odometer'] ?? '',
            'cost' => $_POST['cost'] ?? '',
            'description' => $_POST['description'] ?? '',
            'status' => $_POST['status'] ?? 'open'
        ];

        $eventId = $this->eventRepo->create($data);

        // Handle Service specific update (next_service_km)
        if ($data['event_type'] === 'service' && !empty($_POST['next_service_km'])) {
            $nextKm = (int) $_POST['next_service_km'];
            DB::query("UPDATE vehicles SET next_service_km = ? WHERE id = ? AND tenant_id = ?", [$nextKm, $vehicleId, $tenantId]);
        }

        // Handle Photos
        if ($eventId && isset($_FILES['photos'])) {
            $this->handlePhotoUploads($eventId, $_FILES['photos']);
        }

        $this->redirect('/tenant/vehicle-events?vehicle_id=' . $vehicleId . '&success=event_added');
    }

    public function update(): void
    {
        $tenantId = Auth::tenantId();
        $eventId = (int) $_POST['event_id'];
        $vehicleId = (int) $_POST['vehicle_id'];

        $data = [
            'vehicle_id' => $vehicleId,
            'event_type' => $_POST['event_type'],
            'event_subtype' => $_POST['event_subtype'] ?? '',
            'event_date' => $_POST['event_date'],
            'odometer' => $_POST['odometer'] ?? '',
            'cost' => $_POST['cost'] ?? '',
            'description' => $_POST['description'] ?? '',
            'status' => $_POST['status'] ?? 'open'
        ];

        // Ensure update() repository method is secure (it uses tenant_id in WHERE)
        $this->eventRepo->update($eventId, $data);

        if ($data['event_type'] === 'service' && !empty($_POST['next_service_km'])) {
            $nextKm = (int) $_POST['next_service_km'];
            DB::query("UPDATE vehicles SET next_service_km = ? WHERE id = ? AND tenant_id = ?", [$nextKm, $vehicleId, $tenantId]);
        }

        if (isset($_FILES['photos'])) {
            $this->handlePhotoUploads($eventId, $_FILES['photos']);
        }

        $this->redirect('/tenant/vehicle-events?vehicle_id=' . $vehicleId . '&success=event_updated');
    }

    public function destroy(): void
    {
        $eventId = (int) $_POST['event_id'];
        $vehicleId = (int) $_POST['vehicle_id'];
        $this->eventRepo->delete($eventId);
        
        $this->redirect('/tenant/vehicle-events?vehicle_id=' . $vehicleId . '&success=event_deleted');
    }

    private function handlePhotoUploads(int $eventId, array $files): void
    {
        $tenantId = Auth::tenantId();
        $publicRelativePath = "uploads/tenants/{$tenantId}/events/" . date('Y-m');
        
        $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\');
        $uploadDir = $docRoot . '/' . $publicRelativePath;
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        foreach ($files['tmp_name'] as $key => $tmpName) {
            if ($files['error'][$key] === UPLOAD_ERR_OK && $files['size'][$key] <= 5 * 1024 * 1024) { // 5MB limit
                $ext = pathinfo($files['name'][$key], PATHINFO_EXTENSION);
                if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'webp'])) {
                    $filename = uniqid('evt_') . '.' . $ext;
                    $targetPath = $uploadDir . '/' . $filename;
                    
                    if (move_uploaded_file($tmpName, $targetPath)) {
                        $dbPath = $publicRelativePath . '/' . $filename;
                        $this->eventRepo->addPhoto($eventId, $dbPath);
                    }
                }
            }
            if ($key >= 5) break; // Max 6 photos
        }
    }
}
