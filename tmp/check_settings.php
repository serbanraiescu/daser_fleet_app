<?php
require_once 'fleetlog/core/Autoloader.php';
require_once 'fleetlog/core/EnvLoader.php';
FleetLog\Core\Autoloader::register();
FleetLog\Core\EnvLoader::load('fleetlog/.env');

$admin = FleetLog\Core\DB::fetch("SELECT email FROM users WHERE role = 'superadmin' LIMIT 1");
print_r($admin);
