<?php

namespace FleetLog\App\Repositories;

use FleetLog\Core\DB;
use FleetLog\Core\Auth;

class HandoverRepository extends BaseRepository
{
    protected string $table = 'vehicle_handovers';

    public function recordHandover(array $input): int|false
    {
        $input = $this->prepareData($input);
        
        $data = [
            'tenant_id'    => $input['tenant_id'],
            'vehicle_id'   => $input['vehicle_id'],
            'from_user_id' => $input['from_user_id'],
            'to_user_id'   => $input['to_user_id'],
            'datetime'     => $input['datetime'] ?? date('Y-m-d H:i:s'),
            'odometer_km'  => $input['odometer_km'] ?? 0,
            'has_damage'   => $input['has_damage'] ?? 0,
            'notes'        => $input['notes'] ?? null
        ];

        $sql = "INSERT INTO vehicle_handovers (tenant_id, vehicle_id, from_user_id, to_user_id, datetime, odometer_km, has_damage, notes) 
                VALUES (:tenant_id, :vehicle_id, :from_user_id, :to_user_id, :datetime, :odometer_km, :has_damage, :notes)";
        
        DB::query($sql, $data);
        $handoverId = (int) DB::lastInsertId();

        if ($handoverId) {
            $vehicleRepo = new VehicleRepository();
            $vehicleRepo->updateOdometer($data['vehicle_id'], $data['odometer_km']);
        }

        return $handoverId;
    }
}
