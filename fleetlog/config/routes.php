<?php

/** @var \FleetLog\Core\Router $router */

$router->add('GET', '/', 'HomeController@index');
$router->add('GET', '/login', 'AuthController@showLogin');
$router->add('POST', '/login', 'AuthController@login');
$router->add('GET', '/logout', 'AuthController@logout');
$router->add('GET', '/account-suspended', 'AuthController@suspended');
$router->add('GET', '/admin/stop-impersonation', 'AuthController@stopImpersonating', [\FleetLog\App\Middleware\AuthMiddleware::class]);

// Super Admin Routes
$router->add('GET', '/admin/tenants', 'SuperAdminController@tenants', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\SuperAdminMiddleware::class]);
$router->add('GET', '/admin/tenants/impersonate/{id}', 'SuperAdminController@impersonate', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\SuperAdminMiddleware::class]);

// Tenant Admin Routes
$router->add('GET', '/tenant/dashboard', 'TenantController@dashboard', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('GET', '/tenant/vehicles', 'TenantController@vehicles', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('GET', '/tenant/vehicles/add', 'TenantController@showAddVehicle', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('POST', '/tenant/vehicles/add', 'TenantController@storeVehicle', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('GET', '/tenant/drivers', 'TenantController@drivers', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('GET', '/tenant/drivers/add', 'TenantController@showAddDriver', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('POST', '/tenant/drivers/add', 'TenantController@storeDriver', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('GET', '/tenant/trips', 'TenantController@trips', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('GET', '/tenant/damages', 'TenantController@damages', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('GET', '/tenant/settings', 'TenantController@settings', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('POST', '/tenant/settings', 'TenantController@updateSettings', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('GET', '/tenant/reports', 'ReportController@index', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);

// Driver Routes
$router->add('GET', '/driver/dashboard', 'DriverController@dashboard', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('GET', '/driver/start-trip', 'TripController@showStartTrip', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('POST', '/driver/start-trip', 'TripController@start', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('GET', '/driver/end-trip', 'TripController@showEndTrip', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('POST', '/driver/end-trip', 'TripController@end', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('GET', '/driver/handover', 'HandoverController@show', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('POST', '/driver/handover', 'HandoverController@store', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('GET', '/driver/report-damage', 'DamageController@showReport', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('POST', '/driver/report-damage', 'DamageController@store', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);

$router->add('GET', '/', 'HomeController@index');
