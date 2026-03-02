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

    public function startTrip(array $data): int|false
    {
        if ($this->hasOpenTrip($data['driver_id'])) {
            return false;
        }

        $data = $this->prepareData($data);
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
