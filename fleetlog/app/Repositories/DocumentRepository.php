<?php

namespace FleetLog\App\Repositories;

use FleetLog\Core\DB;

class DocumentRepository
{
    public function getAllHandoverByTenant(int $tenantId): array
    {
        return DB::fetchAll("
            SELECT d.*, v.license_plate, v.make, v.model, u.name as driver_name
            FROM vehicle_handover_reports d
            LEFT JOIN vehicles v ON d.vehicle_id = v.id
            JOIN users u ON d.driver_id = u.id
            WHERE d.tenant_id = ?
            ORDER BY d.created_at DESC
        ", [$tenantId]);
    }

    public function findHandover(int $id, int $tenantId): ?array
    {
        return DB::fetch("
            SELECT d.*, v.license_plate, v.make, v.model, u.name as driver_name, u.cnp, u.license_series, u.license_expiry,
                   t.name as tenant_name, t.cui as tenant_cui, t.address as tenant_address, t.reg_com as tenant_reg_com, 
                   t.county as tenant_county, t.city as tenant_city, t.equipment_config
            FROM vehicle_handover_reports d
            LEFT JOIN vehicles v ON d.vehicle_id = v.id
            JOIN users u ON d.driver_id = u.id
            JOIN tenants t ON d.tenant_id = t.id
            WHERE d.id = ? AND d.tenant_id = ?
        ", [$id, $tenantId]);
    }

    public function generateDocumentNumber(string $typePrefix = 'PV'): string
    {
        $year = date('Y');
        $prefix = "$typePrefix-$year-";
        
        $last = DB::fetch("
            SELECT document_number 
            FROM vehicle_handover_reports 
            WHERE document_number LIKE ? 
            ORDER BY document_number DESC 
            LIMIT 1
        ", [$prefix . '%']);

        if (!$last) {
            return $prefix . "00001";
        }

        $parts = explode('-', $last['document_number']);
        $inc = (int)end($parts);
        $next = str_pad($inc + 1, 5, '0', STR_PAD_LEFT);

        return $prefix . $next;
    }

    public function getByVehicle(int $vehicleId): array
    {
        return DB::fetchAll("
            SELECT d.*, v.license_plate, v.make, v.model, u.name as driver_name
            FROM vehicle_handover_reports d
            JOIN vehicles v ON d.vehicle_id = v.id
            JOIN users u ON d.driver_id = u.id
            WHERE d.vehicle_id = ?
            ORDER BY d.created_at DESC
        ", [$vehicleId]);
    }

    public function getByTenant(int $tenantId): array
    {
        return DB::fetchAll("
            SELECT d.*, v.license_plate, v.make, v.model, u.name as driver_name
            FROM vehicle_handover_reports d
            JOIN vehicles v ON d.vehicle_id = v.id
            JOIN users u ON d.driver_id = u.id
            WHERE d.tenant_id = ?
            ORDER BY d.created_at DESC
        ", [$tenantId]);
    }

    public function createHandover(array $data): int
    {
        $keys = array_keys($data);
        $fields = implode(', ', $keys);
        $placeholders = implode(', ', array_fill(0, count($keys), '?'));
        
        $sql = "INSERT INTO vehicle_handover_reports ($fields) VALUES ($placeholders)";
        DB::query($sql, array_values($data));
        
        return (int)DB::lastInsertId();
    }
}
