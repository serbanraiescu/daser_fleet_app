<?php

namespace FleetLog\App\Repositories;

use FleetLog\Core\DB;
use FleetLog\Core\Auth;

class VehicleRepository extends BaseRepository
{
    protected string $table = 'vehicles';

    public function create(array $data): bool
    {
        $data = $this->prepareData($data);
        $sql = "INSERT INTO vehicles (tenant_id, license_plate, make, model, expiry_rca, expiry_itp, expiry_rovigneta, status, current_odometer) 
                VALUES (:tenant_id, :license_plate, :make, :model, :expiry_rca, :expiry_itp, :expiry_rovigneta, :status, :current_odometer)";
        
        return DB::query($sql, $data)->rowCount() > 0;
    }

    public function updateOdometer(int $vehicleId, int $newOdometer): bool
    {
        $tenantId = Auth::tenantId();
        $sql = "UPDATE vehicles SET current_odometer = :odometer WHERE id = :id AND tenant_id = :tenant_id";
        return DB::query($sql, [
            'odometer' => $newOdometer,
            'id' => $vehicleId,
            'tenant_id' => $tenantId
        ])->rowCount() > 0;
    }

    public function findByLicensePlate(string $plate): ?array
    {
        $tenantId = Auth::tenantId();
        return DB::fetch("SELECT * FROM vehicles WHERE license_plate = ? AND tenant_id = ?", [$plate, $tenantId]);
    }

    public function update(int $id, array $data): bool
    {
        $data['id'] = $id;
        $data['tenant_id'] = Auth::tenantId();
        
        $sql = "UPDATE vehicles SET 
                license_plate = :license_plate,
                make = :make,
                model = :model,
                expiry_rca = :expiry_rca,
                expiry_itp = :expiry_itp,
                expiry_rovigneta = :expiry_rovigneta,
                status = :status,
                current_odometer = :current_odometer
                WHERE id = :id AND tenant_id = :tenant_id";
        
        return DB::query($sql, $data)->rowCount() > 0;
    }

    public function getActiveByTenant(int $tenantId): array
    {
        return DB::fetchAll("SELECT * FROM vehicles WHERE tenant_id = ? AND status = 'active'", [$tenantId]);
    }
}
