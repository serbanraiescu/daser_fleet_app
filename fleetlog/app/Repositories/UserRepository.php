<?php

namespace FleetLog\App\Repositories;

use FleetLog\Core\DB;

class UserRepository extends BaseRepository
{
    protected string $table = 'users';

    public function findByEmail(string $email): ?array
    {
        return DB::fetch("SELECT * FROM users WHERE email = ?", [$email]);
    }

    public function getDrivers(): array
    {
        $tenantId = Auth::tenantId();
        return DB::fetchAll("SELECT * FROM users WHERE tenant_id = ? AND role = 'driver' AND active = 1", [$tenantId]);
    }

    public function create(array $data): bool
    {
        $data = $this->prepareData($data);
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        $sql = "INSERT INTO users (tenant_id, name, email, password, role, active) 
                VALUES (:tenant_id, :name, :email, :password, :role, :active)";
        
        return DB::query($sql, $data)->rowCount() > 0;
    }
}
