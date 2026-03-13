<?php

namespace FleetLog\App\Repositories;

use FleetLog\Core\DB;
use FleetLog\Core\Auth;

class DamageReportRepository extends BaseRepository
{
    protected string $table = 'damage_reports';

    public function create(array $input): int|false
    {
        $input = $this->prepareData($input);
        
        $data = [
            'tenant_id'   => $input['tenant_id'],
            'vehicle_id'  => $input['vehicle_id'],
            'driver_id'   => $input['driver_id'],
            'datetime'    => $input['datetime'] ?? date('Y-m-d H:i:s'),
            'category'    => $input['category'] ?? 'others',
            'severity'    => $input['severity'] ?? 'low',
            'description' => $input['description'] ?? ''
        ];

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

    public function getNewCount(int $tenantId): int
    {
        $result = DB::fetch("SELECT COUNT(*) as total FROM damage_reports WHERE tenant_id = ? AND status = 'new'", [$tenantId]);
        return (int) ($result['total'] ?? 0);
    }

    public function markAllAsSeen(int $tenantId): bool
    {
        return DB::query("UPDATE damage_reports SET status = 'seen' WHERE tenant_id = ? AND status = 'new'", [$tenantId])->rowCount() > 0;
    }

    public function update(int $id, array $input): bool
    {
        $tenantId = Auth::tenantId();
        $input['id'] = $id;
        $input['tenant_id'] = $tenantId;

        $current = DB::fetch("SELECT * FROM damage_reports WHERE id = ? AND tenant_id = ?", [$id, $tenantId]);
        if (!$current) return false;

        $data = [
            'id'          => $id,
            'tenant_id'   => $tenantId,
            'status'      => $input['status'] ?? $current['status'],
            'repair_cost' => $input['repair_cost'] ?? $current['repair_cost'],
            'admin_notes' => $input['admin_notes'] ?? $current['admin_notes']
        ];

        $sql = "UPDATE damage_reports SET 
                status = :status,
                repair_cost = :repair_cost,
                admin_notes = :admin_notes
                WHERE id = :id AND tenant_id = :tenant_id";
        
        return DB::query($sql, $data)->rowCount() > 0;
    }

    public function getActiveByVehicle(int $vehicleId): array
    {
        $tenantId = Auth::tenantId();
        return DB::fetchAll("
            SELECT * FROM damage_reports 
            WHERE vehicle_id = ? AND tenant_id = ? AND status != 'fixed'
            ORDER BY datetime DESC
        ", [$vehicleId, $tenantId]);
    }
}
