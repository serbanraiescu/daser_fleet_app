<?php

namespace FleetLog\App\Repositories;

use FleetLog\Core\DB;
use FleetLog\Core\Auth;

class TripRepository extends BaseRepository
{
    protected string $table = 'trips';

    public function hasOpenTrip(int $driverId): bool
    {
        $sql = "SELECT id FROM trips WHERE driver_id = ? AND status = 'open' LIMIT 1";
        return (bool) DB::fetch($sql, [$driverId]);
    }

    public function getOpenTrip(int $driverId): ?array
    {
        $sql = "SELECT t.*, v.license_plate 
                FROM trips t 
                JOIN vehicles v ON t.vehicle_id = v.id 
                WHERE t.driver_id = ? AND t.status = 'open' 
                LIMIT 1";
        return DB::fetch($sql, [$driverId]);
    }

    public function startTrip(array $input): int|false
    {
        if ($this->hasOpenTrip($input['driver_id'])) {
            return false;
        }

        $input = $this->prepareData($input);
        
        $data = [
            'tenant_id'    => $input['tenant_id'],
            'vehicle_id'   => $input['vehicle_id'],
            'driver_id'    => $input['driver_id'],
            'type'         => $input['type'] ?? 'business',
            'start_time'   => $input['start_time'] ?? date('Y-m-d H:i:s'),
            'start_km'     => $input['start_km'] ?? 0,
            'needs_review' => $input['needs_review'] ?? 0,
            'notes'        => $input['notes'] ?? null
        ];

        $sql = "INSERT INTO trips (tenant_id, vehicle_id, driver_id, type, start_time, start_km, status, needs_review, notes) 
                VALUES (:tenant_id, :vehicle_id, :driver_id, :type, :start_time, :start_km, 'open', :needs_review, :notes)";
        
        DB::query($sql, $data);
        return (int) DB::lastInsertId();
    }

    public function closeTrip(int $tripId, int $endKm, string $endTime, ?string $notes = null): bool
    {
        $trip = $this->find($tripId);
        if (!$trip || $trip['status'] !== 'open') {
            return false;
        }

        if ($endKm < $trip['start_km']) {
            return false;
        }

        $sql = "UPDATE trips SET end_km = :end_km, end_time = :end_time, status = 'closed', notes = :notes 
                WHERE id = :id AND tenant_id = :tenant_id";
        
        $result = DB::query($sql, [
            'end_km' => $endKm,
            'end_time' => $endTime,
            'notes' => $notes ?: $trip['notes'],
            'id' => $tripId,
            'tenant_id' => Auth::tenantId()
        ])->rowCount() > 0;

        if ($result) {
            $vehicleRepo = new VehicleRepository();
            $vehicleRepo->updateOdometer($trip['vehicle_id'], $endKm);
        }

        return $result;
    }
}
