<?php

namespace FleetLog\App\Repositories;

use FleetLog\Core\DB;

class FuelingRepository extends BaseRepository
{
    protected string $table = 'fuelings';

    public function create(array $input): bool
    {
        $input = $this->prepareData($input);
        
        $data = [
            'tenant_id'     => $input['tenant_id'],
            'vehicle_id'    => $input['vehicle_id'],
            'user_id'       => $input['user_id'],
            'odometer'      => $input['odometer'] ?? 0,
            'liters'        => $input['liters'] ?? 0,
            'total_price'   => $input['total_price'] ?? 0,
            'is_full'       => $input['is_full'] ?? 0,
            'receipt_photo' => $input['receipt_photo'] ?? null
        ];

        $sql = "INSERT INTO fuelings (tenant_id, vehicle_id, user_id, odometer, liters, total_price, is_full, receipt_photo) 
                VALUES (:tenant_id, :vehicle_id, :user_id, :odometer, :liters, :total_price, :is_full, :receipt_photo)";
        
        return DB::query($sql, $data)->rowCount() > 0;
    }

    public function getByTenant(?int $tenantId): array
    {
        if ($tenantId === null) return [];
        return DB::fetchAll("SELECT f.*, v.license_plate, u.name as driver_name 
                            FROM fuelings f 
                            JOIN vehicles v ON f.vehicle_id = v.id 
                            JOIN users u ON f.user_id = u.id 
                            WHERE f.tenant_id = ? 
                            ORDER BY f.created_at DESC", [$tenantId]);
    }

    public function getByVehicle(int $vehicleId, int $tenantId): array
    {
        return DB::fetchAll("SELECT f.*, v.license_plate, u.name as driver_name 
                            FROM fuelings f 
                            JOIN vehicles v ON f.vehicle_id = v.id 
                            JOIN users u ON f.user_id = u.id 
                            WHERE f.vehicle_id = ? AND f.tenant_id = ? 
                            ORDER BY f.created_at DESC", [$vehicleId, $tenantId]);
    }

    public function getByPeriod(int $tenantId, int $month, int $year): array
    {
        return DB::fetchAll("SELECT f.*, v.license_plate, u.name as driver_name 
                            FROM fuelings f 
                            JOIN vehicles v ON f.vehicle_id = v.id 
                            JOIN users u ON f.user_id = u.id 
                            WHERE f.tenant_id = ? 
                            AND MONTH(f.created_at) = ? 
                            AND YEAR(f.created_at) = ?
                            ORDER BY f.created_at DESC", [$tenantId, $month, $year]);
    }
    public function update(int $id, array $data): bool
    {
        $tenantId = Auth::tenantId();
        if (empty($data)) return false;

        $fields = [];
        $params = [];
        foreach ($data as $key => $value) {
            $fields[] = "{$key} = :{$key}";
            $params[$key] = $value;
        }
        $params['id'] = $id;
        $params['tenant_id'] = $tenantId;

        $sql = "UPDATE fuelings SET " . implode(', ', $fields) . " WHERE id = :id AND tenant_id = :tenant_id";
        return DB::query($sql, $params)->rowCount() > 0;
    }

    public function getLatestForVehicle(int $vehicleId): ?array
    {
        $tenantId = Auth::tenantId();
        return DB::fetch("SELECT * FROM fuelings WHERE vehicle_id = ? AND tenant_id = ? ORDER BY created_at DESC, id DESC LIMIT 1", [$vehicleId, $tenantId]);
    }
}
