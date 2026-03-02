<?php

namespace FleetLog\App\Repositories;

use FleetLog\Core\DB;
use FleetLog\Core\Auth;
use FleetLog\Core\RBAC;

abstract class BaseRepository
{
    protected string $table;

    public function find(int $id): ?array
    {
        $tenantId = Auth::tenantId();
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $params = [$id];

        if ($tenantId !== null) {
            $sql .= " AND tenant_id = ?";
            $params[] = $tenantId;
        }

        return DB::fetch($sql, $params);
    }

    public function all(): array
    {
        $tenantId = Auth::tenantId();
        $sql = "SELECT * FROM {$this->table}";
        $params = [];

        if ($tenantId !== null) {
            $sql .= " WHERE tenant_id = ?";
            $params[] = $tenantId;
        }

        return DB::fetchAll($sql, $params);
    }

    // Helper to enforce tenant_id on insert
    protected function prepareData(array $data): array
    {
        if (!RBAC::isSuperAdmin()) {
            $data['tenant_id'] = Auth::tenantId();
        }
        return $data;
    }
}
