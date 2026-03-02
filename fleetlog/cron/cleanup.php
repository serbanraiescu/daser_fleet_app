<?php

require_once __DIR__ . '/../core/Autoloader.php';
require_once __DIR__ . '/../core/EnvLoader.php';

use FleetLog\Core\Autoloader;
use FleetLog\Core\EnvLoader;
use FleetLog\Core\DB;

Autoloader::register();
EnvLoader::load(__DIR__ . '/../.env');

echo "Starting photo cleanup...\n";

$tenants = DB::fetchAll("SELECT id, settings FROM tenants");

foreach ($tenants as $tenant) {
    $settings = json_decode($tenant['settings'] ?? '{}', true);
    $retentionDays = $settings['retention_days_photos'] ?? 365;
    $tenantId = $tenant['id'];

    $oldPhotos = DB::fetchAll("SELECT * FROM damage_photos WHERE tenant_id = ? AND created_at < DATE_SUB(NOW(), INTERVAL ? DAY)", [$tenantId, $retentionDays]);

    foreach ($oldPhotos as $photo) {
        $filePath = dirname(__DIR__) . '/storage/' . $photo['path'];
        if (file_exists($filePath)) {
            unlink($filePath);
            echo "Deleted file: {$photo['path']}\n";
        }
        DB::query("DELETE FROM damage_photos WHERE id = ?", [$photo['id']]);
        echo "Deleted DB record for photo #{$photo['id']}\n";
    }
}

echo "Cleanup finished.\n";
