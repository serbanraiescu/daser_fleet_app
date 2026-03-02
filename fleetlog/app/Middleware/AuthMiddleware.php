<?php

namespace FleetLog\App\Middleware;

use FleetLog\Core\Auth;

class AuthMiddleware
{
    public function handle(): void
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }
    }
}
