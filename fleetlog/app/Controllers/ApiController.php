<?php

namespace FleetLog\App\Controllers;

use FleetLog\Core\DB;
use FleetLog\Core\Auth;
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

        $user = Auth::login($email, $password);

        if ($user) {
            // Check if user is a driver
            if ($user['role'] !== 'driver') {
                $this->jsonResponse(['error' => 'Only driver accounts can access the mobile app'], 403);
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
                'token' => session_id() // In a real app we would use JWT, but for now session_id works
            ]);
        } else {
            $this->jsonResponse(['error' => 'Invalid credentials'], 401);
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
     * Helper for JSON responses
     */
    private function jsonResponse(array $data, int $code = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data);
        exit;
    }
}
