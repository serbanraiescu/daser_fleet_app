<?php

namespace FleetLog\Core;

class Autoloader
{
    public static function register(): void
    {
        // Include Composer Autoloader
        $vendorAuto = dirname(__DIR__, 2) . '/vendor/autoload.php';
        if (file_exists($vendorAuto)) {
            require_once $vendorAuto;
        }

        spl_autoload_register(function ($class) {
            $prefix = 'FleetLog\\';
            $base_dir = dirname(__DIR__) . '/';

            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                return;
            }

            $relative_class = substr($class, $len);
            $path = str_replace('\\', '/', $relative_class);
            $file = $base_dir . $path . '.php';

            // Handle Linux case-sensitivity (Core -> core, App -> app)
            if (!file_exists($file)) {
                $parts = explode('/', $path);
                if (count($parts) > 1) {
                    $parts[0] = strtolower($parts[0]);
                    $file = $base_dir . implode('/', $parts) . '.php';
                }
            }

            if (file_exists($file)) {
                require $file;
            }
        });
    }
}
