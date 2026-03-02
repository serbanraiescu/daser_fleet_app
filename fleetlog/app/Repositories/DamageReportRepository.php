<?php

namespace FleetLog\App\Repositories;

use FleetLog\Core\DB;
use FleetLog\Core\Auth;

class DamageReportRepository extends BaseRepository
{
    protected string $table = 'damage_reports';

    public function create(array $data): int|false
    {
        $data = $this->prepareData($data);
        $sql = "INSERT INTO damage_reports (tenant_id, vehicle_id, driver_id, datetime, category, severity, description, status) 
                VALUES (:tenant_id, :vehicle_id, :driver_id, :datetime, :category, :severity, :description, 'new')";
        
        DB::query($sql, $data);
        return (int) DB::lastInsertId();
    }

    public function addPhoto(int $reportId, string $path): bool
    {
        $tenantId = Auth::tenantId();
        $sql = "INSERT INTO damage_photos (tenant_id, damage_report_id, path) VALUES (?, ?, ?)";
        return DB::query($sql, [$tenantId, $reportId, $path])->rowCount() > 0;
    }

    public function getPhotos(int $reportId): array
    {
        $tenantId = Auth::tenantId();
        return DB::fetchAll("SELECT * FROM damage_photos WHERE damage_report_id = ? AND tenant_id = ?", [$reportId, $tenantId]);
    }
}
