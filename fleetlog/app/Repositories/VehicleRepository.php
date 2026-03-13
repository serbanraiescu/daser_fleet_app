<?php

namespace FleetLog\App\Repositories;

use FleetLog\Core\DB;
use FleetLog\Core\Auth;

class VehicleRepository extends BaseRepository
{
    protected string $table = 'vehicles';

    public function create(array $input): bool
    {
        $input = $this->prepareData($input);
        
        $data = [
            'tenant_id'           => $input['tenant_id'],
            'license_plate'       => $input['license_plate'] ?? '',
            'make'                => $input['make'] ?? null,
            'model'               => $input['model'] ?? null,
            'expiry_rca'          => $input['expiry_rca'] ?? null,
            'expiry_itp'          => $input['expiry_itp'] ?? null,
            'expiry_rovigneta'    => $input['expiry_rovigneta'] ?? null,
            'status'              => $input['status'] ?? 'active',
            'current_odometer'    => $input['current_odometer'] ?? 0,
            'qr_code'             => $input['qr_code'] ?? null,
            'has_triangles'       => $input['has_triangles'] ?? 0,
            'has_vest'            => $input['has_vest'] ?? 0,
            'has_jack'            => $input['has_jack'] ?? 0,
            'medical_kit_expiry'  => $input['medical_kit_expiry'] ?? null,
            'has_tow_rope'        => $input['has_tow_rope'] ?? 0,
            'has_jumper_cables'   => $input['has_jumper_cables'] ?? 0,
            'extinguisher_expiry' => $input['extinguisher_expiry'] ?? null,
            'has_spare_wheel'     => $input['has_spare_wheel'] ?? 0
        ];

        $sql = "INSERT INTO vehicles (tenant_id, license_plate, make, model, expiry_rca, expiry_itp, expiry_rovigneta, status, current_odometer, qr_code, has_triangles, has_vest, has_jack, medical_kit_expiry, has_tow_rope, has_jumper_cables, extinguisher_expiry, has_spare_wheel) 
                VALUES (:tenant_id, :license_plate, :make, :model, :expiry_rca, :expiry_itp, :expiry_rovigneta, :status, :current_odometer, :qr_code, :has_triangles, :has_vest, :has_jack, :medical_kit_expiry, :has_tow_rope, :has_jumper_cables, :extinguisher_expiry, :has_spare_wheel)";
        
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

    public function update(int $id, array $input): bool
    {
        $input['id'] = $id;
        $input['tenant_id'] = Auth::tenantId();

        $current = $this->find($id);
        if (!$current) return false;
        
        $data = [
            'id'                  => $id,
            'tenant_id'           => $input['tenant_id'],
            'license_plate'       => $input['license_plate'] ?? $current['license_plate'],
            'make'                => $input['make'] ?? $current['make'],
            'model'               => $input['model'] ?? $current['model'],
            'expiry_rca'          => $input['expiry_rca'] ?? $current['expiry_rca'],
            'expiry_itp'          => $input['expiry_itp'] ?? $current['expiry_itp'],
            'expiry_rovigneta'    => $input['expiry_rovigneta'] ?? $current['expiry_rovigneta'],
            'status'              => $input['status'] ?? $current['status'],
            'current_odometer'    => $input['current_odometer'] ?? $current['current_odometer'],
            'qr_code'             => $input['qr_code'] ?? $current['qr_code'],
            'has_triangles'       => $input['has_triangles'] ?? $current['has_triangles'],
            'has_vest'            => $input['has_vest'] ?? $current['has_vest'],
            'has_jack'            => $input['has_jack'] ?? $current['has_jack'],
            'medical_kit_expiry'  => $input['medical_kit_expiry'] ?? $current['medical_kit_expiry'],
            'has_tow_rope'        => $input['has_tow_rope'] ?? $current['has_tow_rope'],
            'has_jumper_cables'   => $input['has_jumper_cables'] ?? $current['has_jumper_cables'],
            'extinguisher_expiry' => $input['extinguisher_expiry'] ?? $current['extinguisher_expiry'],
            'has_spare_wheel'     => $input['has_spare_wheel'] ?? $current['has_spare_wheel']
        ];

        $sql = "UPDATE vehicles SET 
                license_plate = :license_plate,
                make = :make,
                model = :model,
                expiry_rca = :expiry_rca,
                expiry_itp = :expiry_itp,
                expiry_rovigneta = :expiry_rovigneta,
                status = :status,
                current_odometer = :current_odometer,
                qr_code = :qr_code,
                has_triangles = :has_triangles,
                has_vest = :has_vest,
                has_jack = :has_jack,
                medical_kit_expiry = :medical_kit_expiry,
                has_tow_rope = :has_tow_rope,
                has_jumper_cables = :has_jumper_cables,
                extinguisher_expiry = :extinguisher_expiry,
                has_spare_wheel = :has_spare_wheel
                WHERE id = :id AND tenant_id = :tenant_id";
        
        return DB::query($sql, $data)->rowCount() > 0;
    }

    public function updateStatus(int $id, string $status): bool
    {
        $tenantId = Auth::tenantId();
        $sql = "UPDATE vehicles SET status = :status WHERE id = :id AND tenant_id = :tenant_id";
        return DB::query($sql, [
            'status' => $status,
            'id' => $id,
            'tenant_id' => $tenantId
        ])->rowCount() > 0;
    }

    public function getActiveByTenant(int $tenantId): array
    {
        return DB::fetchAll("SELECT * FROM vehicles WHERE tenant_id = ? AND status = 'active'", [$tenantId]);
    }

    public function getAllNonArchivedByTenant(int $tenantId): array
    {
        return DB::fetchAll("SELECT * FROM vehicles WHERE tenant_id = ? AND status != 'archived' ORDER BY id DESC", [$tenantId]);
    }

    public function getArchivedByTenant(int $tenantId): array
    {
        return DB::fetchAll("SELECT * FROM vehicles WHERE tenant_id = ? AND status = 'archived' ORDER BY id DESC", [$tenantId]);
    }

    public function findByQrCode(string $code): ?array
    {
        return DB::fetch("SELECT * FROM vehicles WHERE qr_code = ?", [$code]);
    }

    public function archiveVehicle(int $id, string $notes): bool
    {
        try {
            $tenantId = Auth::tenantId();
            $sql = "UPDATE vehicles SET status = 'archived', archive_notes = :notes WHERE id = :id AND tenant_id = :tenant_id";
            $res = DB::query($sql, [
                'notes' => $notes,
                'id' => $id,
                'tenant_id' => $tenantId
            ]);
            $count = $res->rowCount();
            error_log("VehicleRepository::archiveVehicle - Affected Rows: $count");
            return $count > 0;
        } catch (\Exception $e) {
            error_log("VehicleRepository::archiveVehicle - ERROR: " . $e->getMessage());
            return false;
        }
    }
}
