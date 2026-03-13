<?php

namespace FleetLog\App\Repositories;

use FleetLog\Core\DB;
use FleetLog\Core\Auth;

class ExpenseRepository extends BaseRepository
{
    protected string $table = 'vehicle_expenses';

    public function create(array $data): bool
    {
        $data['tenant_id'] = Auth::tenantId();
        
        $sql = "INSERT INTO vehicle_expenses (tenant_id, vehicle_id, expense_type, name, cost, odometer_at_expense, expense_date, notes) 
                VALUES (:tenant_id, :vehicle_id, :expense_type, :name, :cost, :odometer_at_expense, :expense_date, :notes)";
        
        return DB::query($sql, [
            'tenant_id' => $data['tenant_id'],
            'vehicle_id' => $data['vehicle_id'],
            'expense_type' => $data['expense_type'],
            'name' => $data['name'],
            'cost' => $data['cost'],
            'odometer_at_expense' => $data['odometer_at_expense'] !== '' ? $data['odometer_at_expense'] : null,
            'expense_date' => $data['expense_date'],
            'notes' => $data['notes'] ?: null
        ])->rowCount() > 0;
    }

    public function update(int $id, array $data): bool
    {
        $data['id'] = $id;
        $data['tenant_id'] = Auth::tenantId();
        
        $sql = "UPDATE vehicle_expenses SET 
                vehicle_id = :vehicle_id,
                expense_type = :expense_type,
                name = :name,
                cost = :cost,
                odometer_at_expense = :odometer_at_expense,
                expense_date = :expense_date,
                notes = :notes
                WHERE id = :id AND tenant_id = :tenant_id";
        
        return DB::query($sql, [
            'id' => $data['id'],
            'tenant_id' => $data['tenant_id'],
            'vehicle_id' => $data['vehicle_id'],
            'expense_type' => $data['expense_type'],
            'name' => $data['name'],
            'cost' => $data['cost'],
            'odometer_at_expense' => $data['odometer_at_expense'] !== '' ? $data['odometer_at_expense'] : null,
            'expense_date' => $data['expense_date'],
            'notes' => $data['notes'] ?: null
        ])->rowCount() > 0;
    }

    public function getByTenant(int $tenantId): array
    {
        return DB::fetchAll("
            SELECT e.*, v.license_plate, v.make, v.model 
            FROM vehicle_expenses e
            JOIN vehicles v ON e.vehicle_id = v.id
            WHERE e.tenant_id = ?
            ORDER BY e.expense_date DESC, e.id DESC
        ", [$tenantId]);
    }

    public function getByVehicle(int $vehicleId): array
    {
        $tenantId = Auth::tenantId();
        return DB::fetchAll("
            SELECT * FROM vehicle_expenses 
            WHERE vehicle_id = ? AND tenant_id = ? 
            ORDER BY expense_date DESC
        ", [$vehicleId, $tenantId]);
    }

    public function updateNextServiceKm(int $vehicleId, int $nextServiceKm): bool
    {
        $tenantId = Auth::tenantId();
        $sql = "UPDATE vehicles SET next_service_km = :next_service_km WHERE id = :id AND tenant_id = :tenant_id";
        return DB::query($sql, [
            'next_service_km' => $nextServiceKm,
            'id' => $vehicleId,
            'tenant_id' => $tenantId
        ])->rowCount() > 0;
    }
    
    public function getServiceDueVehicles(?int $tenantId, int $warningThreshold = 1000): array
    {
        if ($tenantId === null) return [];
        // Returns vehicles that are within the warning threshold of their next service or past due.
        // Also includes vehicles where next_service_km is strictly > 0 to avoid triggering on unconfigured vehicles.
        return DB::fetchAll("
            SELECT *, (next_service_km - current_odometer) as km_until_service 
            FROM vehicles 
            WHERE tenant_id = ? 
            AND next_service_km > 0 
            AND (current_odometer >= (next_service_km - ?))
            AND status != 'archived'
            ORDER BY km_until_service ASC
        ", [$tenantId, $warningThreshold]);
    }

    public function getTotalExpensesByTenantAndDateRange(?int $tenantId, string $startDate, string $endDate): string
    {
        if ($tenantId === null) return '0.00';
        $result = DB::fetch("
            SELECT SUM(cost) as total 
            FROM vehicle_expenses 
            WHERE tenant_id = ? AND expense_date BETWEEN ? AND ?
        ", [$tenantId, $startDate, $endDate]);
        return $result['total'] ?? '0.00';
    }
    
    public function delete(int $id): bool
    {
        $tenantId = Auth::tenantId();
        return DB::query("DELETE FROM vehicle_expenses WHERE id = ? AND tenant_id = ?", [$id, $tenantId])->rowCount() > 0;
    }

    public function getVehicleHistory(int $vehicleId): array
    {
        $tenantId = Auth::tenantId();
        return DB::fetchAll("
            SELECT * FROM vehicle_expenses 
            WHERE vehicle_id = ? AND tenant_id = ? 
            ORDER BY expense_date DESC, id DESC
        ", [$vehicleId, $tenantId]);
    }

    public function getLastMaintenance(int $vehicleId): ?array
    {
        $tenantId = Auth::tenantId();
        return DB::fetch("
            SELECT * FROM vehicle_expenses 
            WHERE vehicle_id = ? AND tenant_id = ? 
            AND expense_type IN ('maintenance', 'consumable')
            ORDER BY expense_date DESC, id DESC 
            LIMIT 1
        ", [$vehicleId, $tenantId]);
    }
}
