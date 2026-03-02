<?php

namespace FleetLog\App\Repositories;

use FleetLog\Core\DB;

class FuelingRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct('fuelings');
    }

    public function create(array $data): bool
    {
        $sql = "INSERT INTO fuelings (tenant_id, vehicle_id, user_id, odometer, liters, total_price, receipt_photo) 
                VALUES (:tenant_id, :vehicle_id, :user_id, :odometer, :liters, :total_price, :receipt_photo)";
        
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
