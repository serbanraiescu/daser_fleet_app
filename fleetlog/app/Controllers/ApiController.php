<?php

namespace FleetLog\App\Controllers;

use FleetLog\Core\DB;
use FleetLog\Core\Auth;
use FleetLog\Core\SMSService;
use FleetLog\App\Repositories\VehicleRepository;
use FleetLog\App\Repositories\DriverRepository;

class ApiController extends BaseController
{
    /**
     * Mobile Login Endpoint
     * POST /api/login
     */
    public function login(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        if (empty($email) || empty($password)) {
            $this->jsonResponse(['error' => 'Email and password are required'], 400);
            return;
        }

        $success = Auth::login($email, $password);

        if ($success) {
            $user = Auth::user();
            
            // Check if user is a driver
            if ($user['role'] !== 'driver') {
                $this->jsonResponse(['error' => 'Contul tau nu este de tip Sofer (Mobile App)'], 403);
                return;
            }

            $this->jsonResponse([
                'success' => true,
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'tenant_id' => $user['tenant_id']
                ],
                'token' => session_id()
            ]);
        } else {
            $this->jsonResponse(['error' => 'Date de autentificare invalide'], 401);
        }
    }

    /**
     * Driver Dashboard Data
     * GET /api/driver/dashboard
     */
    public function driverDashboard(): void
    {
        // Simple Auth check (token in header/session)
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['error' => 'Unauthorized'], 401);
            return;
        }

        $userId = $_SESSION['user_id'];
        $tenantId = $_SESSION['tenant_id'];

        // Get active trip if any
        $activeTrip = DB::fetch("SELECT * FROM trips WHERE driver_id = ? AND end_time IS NULL ORDER BY start_time DESC LIMIT 1", [$userId]);

        // Get vehicles for selection
        $vehicleRepo = new VehicleRepository();
        $vehicles = $vehicleRepo->getActiveByTenant($tenantId);

        $this->jsonResponse([
            'active_trip' => $activeTrip,
            'vehicles' => $vehicles
        ]);
    }

    /**
     * Start Trip
     * POST /api/driver/trip/start
     */
    public function startTrip(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['error' => 'Unauthorized'], 401);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $vehicleId = $data['vehicle_id'] ?? null;
        $startOdometer = $data['start_odometer'] ?? null;

        if (!$vehicleId || !$startOdometer) {
            $this->jsonResponse(['error' => 'Vehicle ID and Start Odometer are required'], 400);
            return;
        }

        $userId = $_SESSION['user_id'];
        
        // Check if there's an active trip
        $active = DB::fetch("SELECT id FROM trips WHERE driver_id = ? AND end_time IS NULL", [$userId]);
        if ($active) {
            $this->jsonResponse(['error' => 'You already have an active trip'], 400);
            return;
        }

        $sql = "INSERT INTO trips (tenant_id, driver_id, vehicle_id, start_time, start_odometer) 
                VALUES (:tenant_id, :driver_id, :vehicle_id, NOW(), :start_odometer)";
        
        DB::query($sql, [
            'tenant_id' => $_SESSION['tenant_id'],
            'driver_id' => $userId,
            'vehicle_id' => $vehicleId,
            'start_odometer' => $startOdometer
        ]);

        $this->jsonResponse(['success' => true, 'message' => 'Trip started']);
    }

    /**
     * End Trip
     * POST /api/driver/trip/end
     */
    public function endTrip(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['error' => 'Unauthorized'], 401);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $endOdometer = $data['end_odometer'] ?? null;

        if (!$endOdometer) {
            $this->jsonResponse(['error' => 'End Odometer is required'], 400);
            return;
        }

        $userId = $_SESSION['user_id'];
        $activeTrip = DB::fetch("SELECT * FROM trips WHERE driver_id = ? AND end_time IS NULL ORDER BY start_time DESC LIMIT 1", [$userId]);

        if (!$activeTrip) {
            $this->jsonResponse(['error' => 'No active trip found'], 404);
            return;
        }

        if ($endOdometer < $activeTrip['start_odometer']) {
            $this->jsonResponse(['error' => 'End odometer cannot be less than start odometer'], 400);
            return;
        }

        $sql = "UPDATE trips SET end_time = NOW(), end_odometer = :end_odometer WHERE id = :id";
        DB::query($sql, [
            'end_odometer' => $endOdometer,
            'id' => $activeTrip['id']
        ]);

        // Update vehicle odometer
        $sql = "UPDATE vehicles SET current_odometer = :odometer WHERE id = :id";
        DB::query($sql, [
            'odometer' => $endOdometer,
            'id' => $activeTrip['vehicle_id']
        ]);

        $this->jsonResponse(['success' => true, 'message' => 'Trip ended']);
    }

    /**
     * Log Fueling
     * POST /api/driver/fueling
     */
    public function logFueling(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['error' => 'Unauthorized'], 401);
            return;
        }

        // Handle both JSON and Form-Data (for photos)
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            $data = $_POST;
        }

        $vehicleId = $data['vehicle_id'] ?? null;
        $liters = $data['liters'] ?? null;
        $cost = $data['cost'] ?? null;
        $odometer = $data['odometer'] ?? null;

        if (!$vehicleId || !$liters || !$cost || !$odometer) {
            $this->jsonResponse(['error' => 'All fields (vehicle, liters, cost, odometer) are required'], 400);
            return;
        }

        $receiptPhoto = null;

        // Unified image upload handling (identical to FuelingController)
        if (isset($_FILES['receipt_photo']) && $_FILES['receipt_photo']['error'] === UPLOAD_ERR_OK) {
            $tenantId = $_SESSION['tenant_id'];
            $publicRelativePath = "uploads/tenants/{$tenantId}/fuelings/" . date('Y-m');
            
            $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\');
            $uploadDir = $docRoot . '/' . $publicRelativePath;
            
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $extension = pathinfo($_FILES['receipt_photo']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('receipt_') . '.' . $extension;
            
            if (move_uploaded_file($_FILES['receipt_photo']['tmp_name'], $uploadDir . '/' . $filename)) {
                $receiptPhoto = $publicRelativePath . '/' . $filename;
            }
        }

        $sql = "INSERT INTO fuelings (tenant_id, user_id, vehicle_id, liters, cost, odometer, created_at, is_full, receipt_photo) 
                VALUES (:tenant_id, :user_id, :vehicle_id, :liters, :cost, :odometer, NOW(), :is_full, :receipt_photo)";
        
        DB::query($sql, [
            'tenant_id' => $_SESSION['tenant_id'],
            'user_id' => $_SESSION['user_id'],
            'vehicle_id' => (int)$vehicleId,
            'liters' => (float)$liters,
            'cost' => (float)$cost,
            'odometer' => (int)$odometer,
            'is_full' => isset($data['is_full']) ? (int)$data['is_full'] : 1,
            'receipt_photo' => $receiptPhoto
        ]);

        $this->jsonResponse(['success' => true, 'message' => 'Fueling logged']);
    }

    /**
     * Report Damage
     * POST /api/driver/damage
     */
    public function reportDamage(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['error' => 'Unauthorized'], 401);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $vehicleId = $data['vehicle_id'] ?? null;
        $description = $data['description'] ?? null;

        if (!$vehicleId || !$description) {
            $this->jsonResponse(['error' => 'Vehicle and Description are required'], 400);
            return;
        }

        $sql = "INSERT INTO damage_reports (tenant_id, reported_by, vehicle_id, description, status, reported_at) 
                VALUES (:tenant_id, :reported_by, :vehicle_id, :description, 'pending', NOW())";
        
        DB::query($sql, [
            'tenant_id' => $_SESSION['tenant_id'],
            'reported_by' => $_SESSION['user_id'],
            'vehicle_id' => $vehicleId,
            'description' => $description
        ]);

        $this->jsonResponse(['success' => true, 'message' => 'Damage reported']);
    }


    /**
     * Get pending SMS for Gateway
     * GET /api/sms/pending?key=...
     */
    public function getPendingSMS(): void
    {
        $key = $_GET['key'] ?? '';
        
        // If not in GET, check Authorization header (common in some apps)
        if (empty($key)) {
            $headers = getallheaders();
            $key = $headers['Authorization'] ?? $headers['authorization'] ?? '';
            // Handle "Bearer <key>" or just "<key>"
            if (str_starts_with($key, 'Bearer ')) {
                $key = substr($key, 7);
            }
        }
        
        // Try DB first so UI settings override .env defaults
        $setting = DB::fetch("SELECT value FROM system_settings WHERE `key` = 'sms_gateway_key'");
        $gatewayKey = $setting['value'] ?? '';
        
        if (empty($gatewayKey)) {
            $gatewayKey = getenv('SMS_GATEWAY_KEY');
        }

        if (empty($gatewayKey) || $key !== $gatewayKey) {
            error_log("SMS Gateway Auth Failed: Key provided: [$key], Expected: [$gatewayKey]");
            $this->jsonResponse(['error' => 'Unauthorized Gateway Key'], 403);
            return;
        }

        try {
            $messages = SMSService::getPending(5);
            $this->jsonResponse($messages);
        } catch (\Exception $e) {
            error_log("SMS Gateway Fetch Error: " . $e->getMessage());
            $this->jsonResponse(['error' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Confirm SMS sent
     * POST /api/sms/confirm?key=...
     */
    public function confirmSMS(): void
    {
        $key = $_GET['key'] ?? '';
        
        if (empty($key)) {
            $headers = getallheaders();
            $key = $headers['Authorization'] ?? $headers['authorization'] ?? '';
            if (str_starts_with($key, 'Bearer ')) {
                $key = substr($key, 7);
            }
        }
        
        $setting = DB::fetch("SELECT value FROM system_settings WHERE `key` = 'sms_gateway_key'");
        $gatewayKey = $setting['value'] ?? '';
        
        if (empty($gatewayKey)) {
            $gatewayKey = getenv('SMS_GATEWAY_KEY');
        }

        if (empty($gatewayKey)) {
            $setting = DB::fetch("SELECT value FROM system_settings WHERE `key` = 'sms_gateway_key'");
            $gatewayKey = $setting['value'] ?? '';
        }

        if (empty($gatewayKey) || $key !== $gatewayKey) {
            $this->jsonResponse(['error' => 'Unauthorized Gateway Key'], 403);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;

        if ($id) {
            // Some apps send success/fail status, some just hit confirm on success.
            // If status is provided and is 'failed', mark as failed, otherwise assume 'sent'.
            $status = $data['status'] ?? 'sent';
            if ($status === 'failed') {
                SMSService::fail((int)$id);
            } else {
                SMSService::confirm((int)$id);
            }
            $this->jsonResponse(['success' => true]);
        } else {
            $this->jsonResponse(['error' => 'ID missing'], 400);
        }
    }

    /**
     * Helper for JSON responses
     */
    private function jsonResponse(array $data, int $code = 200): void
    {
        // Add CORS headers for Mobile App / Development (Browser simulation)
        header("Access-Control-Allow-Origin: *"); 
        header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-Update-Key");
        header("Content-Type: application/json; charset=UTF-8");

        // Handle preflight OPTIONS request
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }

        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data);
        exit;
    }
}
