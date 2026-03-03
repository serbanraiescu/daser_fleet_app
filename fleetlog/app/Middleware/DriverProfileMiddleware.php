<?php

namespace FleetLog\App\Middleware;

use FleetLog\Core\Auth;

class DriverProfileMiddleware
{
    public function handle(): void
    {
        if (Auth::check() && Auth::role() === 'driver') {
            $user = Auth::user();
            $required = ['cnp', 'id_expiry', 'license_series', 'license_expiry'];
            $missing = false;
            
            foreach ($required as $field) {
                if (empty($user[$field])) {
                    $missing = true;
                    break;
                }
            }

            $currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            
            // If missing data and not already on the completion page, redirect
            if ($missing && strpos($currentUri, '/driver/complete-profile') === false) {
                header('Location: /driver/complete-profile');
                exit;
            }
        }
    }
}
