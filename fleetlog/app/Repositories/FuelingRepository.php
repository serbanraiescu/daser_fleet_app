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

    public function getByTenant(int $tenantId): array
    {
        return DB::fetchAll("SELECT f.*, v.license_plate, u.name as driver_name 
                            FROM fuelings f 
                            JOIN vehicles v ON f.vehicle_id = v.id 
                            JOIN users u ON f.user_id = u.id 
                            WHERE f.tenant_id = ? 
                            ORDER BY f.created_at DESC", [$tenantId]);
    }
}
