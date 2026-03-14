<?php

namespace FleetLog\App\Repositories;

use FleetLog\Core\DB;
use FleetLog\Core\Auth;

class VehicleEventRepository extends BaseRepository
{
    protected string $table = 'vehicle_events';

    public function create(array $data): int|false
    {
        $tenantId = Auth::tenantId();
        $userId = Auth::user()['id'];

        $sql = "INSERT INTO vehicle_events (
                    tenant_id, vehicle_id, event_type, event_subtype, 
                    event_date, odometer, cost, description, status, created_by
                ) VALUES (
                    :tenant_id, :vehicle_id, :event_type, :event_subtype, 
                    :event_date, :odometer, :cost, :description, :status, :created_by
                )";

        DB::query($sql, [
            'tenant_id' => $tenantId,
            'vehicle_id' => $data['vehicle_id'],
            'event_type' => $data['event_type'],
            'event_subtype' => $data['event_subtype'] ?: null,
            'event_date' => $data['event_date'],
            'odometer' => $data['odometer'] !== '' ? $data['odometer'] : null,
            'cost' => $data['cost'] !== '' ? $data['cost'] : null,
            'description' => $data['description'] ?: null,
            'status' => $data['status'] ?? 'open',
            'created_by' => $userId
        ]);

        return (int) DB::lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $tenantId = Auth::tenantId();

        $sql = "UPDATE vehicle_events SET 
                vehicle_id = :vehicle_id,
                event_type = :event_type,
                event_subtype = :event_subtype,
                event_date = :event_date,
                odometer = :odometer,
                cost = :cost,
                description = :description,
                status = :status
                WHERE id = :id AND tenant_id = :tenant_id";

        return DB::query($sql, [
            'id' => $id,
            'tenant_id' => $tenantId,
            'vehicle_id' => $data['vehicle_id'],
            'event_type' => $data['event_type'],
            'event_subtype' => $data['event_subtype'] ?: null,
            'event_date' => $data['event_date'],
            'odometer' => $data['odometer'] !== '' ? $data['odometer'] : null,
            'cost' => $data['cost'] !== '' ? $data['cost'] : null,
            'description' => $data['description'] ?: null,
            'status' => $data['status'] ?? 'open'
        ])->rowCount() > 0;
    }

    public function getByVehicle(int $vehicleId): array
    {
        $tenantId = Auth::tenantId();
        return DB::fetchAll("
            SELECT e.*, u.name as created_by_name 
            FROM vehicle_events e
            LEFT JOIN users u ON e.created_by = u.id
            WHERE e.vehicle_id = ? AND e.tenant_id = ?
            ORDER BY e.event_date DESC, e.id DESC
        ", [$vehicleId, $tenantId]);
    }

    public function delete(int $id): bool
    {
        $tenantId = Auth::tenantId();
        return DB::query("DELETE FROM vehicle_events WHERE id = ? AND tenant_id = ?", [$id, $tenantId])->rowCount() > 0;
    }

    public function addPhoto(int $eventId, string $path): bool
    {
        $sql = "INSERT INTO vehicle_event_photos (event_id, path) VALUES (?, ?)";
        return DB::query($sql, [$eventId, $path])->rowCount() > 0;
    }

    public function getPhotos(int $eventId): array
    {
        return DB::fetchAll("SELECT * FROM vehicle_event_photos WHERE event_id = ?", [$eventId]);
    }
}
