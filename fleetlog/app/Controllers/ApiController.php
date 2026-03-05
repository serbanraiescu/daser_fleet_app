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
