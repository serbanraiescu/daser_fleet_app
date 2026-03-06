<?php

/** @var \FleetLog\Core\Router $router */

$router->add('GET', '/', 'HomeController@index');
$router->add('GET', '/login', 'AuthController@showLogin');
$router->add('POST', '/login', 'AuthController@login');
$router->add('GET', '/logout', 'AuthController@logout');
$router->add('GET', '/account-suspended', 'AuthController@suspended');
$router->add('GET', '/admin/stop-impersonation', 'AuthController@stopImpersonating', [\FleetLog\App\Middleware\AuthMiddleware::class]);

// Super Admin Routes
$router->add('GET', '/admin/dashboard', 'SuperAdminController@dashboard', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\SuperAdminMiddleware::class]);
$router->add('GET', '/admin/status', 'SuperAdminController@status', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\SuperAdminMiddleware::class]);
$router->add('GET', '/admin/presentation', 'SuperAdminController@presentation', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\SuperAdminMiddleware::class]);
$router->add('POST', '/admin/run-self-test', 'SuperAdminController@runSelfTest', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\SuperAdminMiddleware::class]);

$router->add('GET', '/admin/tenants', 'SuperAdminController@tenants', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\SuperAdminMiddleware::class]);
$router->add('GET', '/admin/tenants/add', 'SuperAdminController@showAddTenant', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\SuperAdminMiddleware::class]);
$router->add('POST', '/admin/tenants/add', 'SuperAdminController@storeTenant', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\SuperAdminMiddleware::class]);
$router->add('GET', '/admin/tenants/edit/{id}', 'SuperAdminController@showEditTenant', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\SuperAdminMiddleware::class]);
$router->add('POST', '/admin/tenants/edit/{id}', 'SuperAdminController@updateTenant', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\SuperAdminMiddleware::class]);
$router->add('GET', '/admin/tenants/delete/{id}', 'SuperAdminController@deleteTenant', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\SuperAdminMiddleware::class]);
$router->add('GET', '/admin/tenants/impersonate/{id}', 'SuperAdminController@impersonate', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\SuperAdminMiddleware::class]);
$router->add('GET', '/admin/settings', 'SuperAdminController@settings', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\SuperAdminMiddleware::class]);
$router->add('POST', '/admin/settings', 'SuperAdminController@updateSettings', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\SuperAdminMiddleware::class]);
$router->add('POST', '/admin/settings/test-email', 'SuperAdminController@sendTestEmail', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\SuperAdminMiddleware::class]);
$router->add('GET', '/admin/email-templates', 'SuperAdminController@emailTemplates', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\SuperAdminMiddleware::class]);
$router->add('GET', '/admin/email-templates/edit/{id}', 'SuperAdminController@editEmailTemplate', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\SuperAdminMiddleware::class]);
$router->add('POST', '/admin/email-templates/edit/{id}', 'SuperAdminController@updateEmailTemplate', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\SuperAdminMiddleware::class]);
$router->add('GET', '/admin/email-templates/preview/{id}', 'SuperAdminController@previewTemplate', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\SuperAdminMiddleware::class]);
$router->add('GET', '/admin/email-templates/run-check', 'SuperAdminController@runExpirationCheck', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\SuperAdminMiddleware::class]);

// Super Admin SMS Gateway Routes
$router->add('GET', '/admin/sms-logs', 'SuperAdminController@smsLogs', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\SuperAdminMiddleware::class]);
$router->add('POST', '/admin/sms/test-send', 'SuperAdminController@sendTestSms', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\SuperAdminMiddleware::class]);

// Tenant Admin Routes
$router->add('GET', '/tenant/dashboard', 'TenantController@dashboard', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('GET', '/tenant/vehicles', 'TenantController@vehicles', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('GET', '/tenant/vehicles/add', 'TenantController@showAddVehicle', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('POST', '/tenant/vehicles/add', 'TenantController@storeVehicle', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('GET', '/tenant/vehicles/edit/{id}', 'TenantController@showEditVehicle', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('POST', '/tenant/vehicles/edit/{id}', 'TenantController@updateVehicle', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('GET', '/tenant/vehicles/status/{id}/{status}', 'TenantController@quickStatusVehicle', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('GET', '/tenant/vehicles/archive/{id}', 'TenantController@showArchiveVehicle', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('POST', '/tenant/vehicles/archive/{id}', 'TenantController@archiveVehicle', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('GET', '/tenant/drivers', 'TenantController@drivers', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('GET', '/tenant/drivers/add', 'TenantController@showAddDriver', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('POST', '/tenant/drivers/add', 'TenantController@storeDriver', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('GET', '/tenant/drivers/edit/{id}', 'TenantController@showEditDriver', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('POST', '/tenant/drivers/edit/{id}', 'TenantController@updateDriver', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('GET', '/tenant/trips', 'TenantController@trips', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('GET', '/tenant/fuelings', 'TenantController@fuelings', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('GET', '/tenant/damages', 'TenantController@damages', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('GET', '/tenant/damages/edit/{id}', 'TenantController@showDamage', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('POST', '/tenant/damages/edit/{id}', 'TenantController@updateDamage', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('GET', '/tenant/settings', 'TenantController@settings', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('POST', '/tenant/settings', 'TenantController@updateSettings', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);

$router->add('GET', '/tenant/expenses', 'TenantController@expenses', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('GET', '/tenant/expenses/add', 'TenantController@showAddExpenseGeneral', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('POST', '/tenant/expenses/add', 'TenantController@storeExpenseGeneral', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('GET', '/tenant/expenses/add/{id}', 'TenantController@showAddExpense', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('POST', '/tenant/expenses/add/{id}', 'TenantController@storeExpense', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('GET', '/tenant/vehicles/mechanic-report/{id}', 'TenantController@mechanicReport', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);

$router->add('GET', '/tenant/reports', 'ReportController@index', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('GET', '/tenant/reports/vehicle', 'ReportController@vehicleReport', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);
$router->add('GET', '/tenant/reports/driver', 'ReportController@driverReport', [\FleetLog\App\Middleware\AuthMiddleware::class, \FleetLog\App\Middleware\TenantStatusMiddleware::class]);

// Driver Routes
$driverMiddleware = [
    \FleetLog\App\Middleware\AuthMiddleware::class, 
    \FleetLog\App\Middleware\TenantStatusMiddleware::class,
    \FleetLog\App\Middleware\DriverProfileMiddleware::class
];

$router->add('GET', '/driver/complete-profile', 'DriverController@showCompleteProfile', [\FleetLog\App\Middleware\AuthMiddleware::class]);
$router->add('POST', '/driver/complete-profile', 'DriverController@updateProfile', [\FleetLog\App\Middleware\AuthMiddleware::class]);

$router->add('GET', '/driver/dashboard', 'DriverController@dashboard', $driverMiddleware);
$router->add('GET', '/driver/start-trip', 'TripController@showStartTrip', $driverMiddleware);
$router->add('POST', '/driver/start-trip', 'TripController@start', $driverMiddleware);
$router->add('GET', '/driver/end-trip', 'TripController@showEndTrip', $driverMiddleware);
$router->add('POST', '/driver/end-trip', 'TripController@end', $driverMiddleware);
$router->add('GET', '/driver/fueling', 'FuelingController@show', $driverMiddleware);
$router->add('POST', '/driver/fueling', 'FuelingController@store', $driverMiddleware);
$router->add('GET', '/driver/report-damage', 'DamageController@showReport', $driverMiddleware);
$router->add('POST', '/driver/report-damage', 'DamageController@store', $driverMiddleware);

// QR Generation
$router->add('GET', '/qr/generate', 'QrController@generate');

// API Routes for Mobile App
$router->add('POST', '/api/login', 'ApiController@login');
$router->add('GET', '/api/driver/dashboard', 'ApiController@driverDashboard');
$router->add('POST', '/api/driver/trip/start', 'ApiController@startTrip');
$router->add('POST', '/api/driver/trip/end', 'ApiController@endTrip');
$router->add('POST', '/api/driver/fueling', 'ApiController@logFueling');
$router->add('POST', '/api/driver/damage', 'ApiController@reportDamage');

// SMS Gateway API
$router->add('GET', '/api/sms', 'ApiController@getPendingSMS'); // Alias for easier config
$router->add('GET', '/api/sms/pending', 'ApiController@getPendingSMS');
$router->add('POST', '/api/sms/confirm', 'ApiController@confirmSMS');

$router->add('GET', '/', 'HomeController@index');
