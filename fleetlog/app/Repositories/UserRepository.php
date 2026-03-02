<?php

namespace FleetLog\App\Repositories;

use FleetLog\Core\DB;

class UserRepository extends BaseRepository
{
    protected string $table = 'users';

    public function find(int $id): ?array
    {
        return DB::fetch("SELECT * FROM users WHERE id = ?", [$id]);
    }

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
        
        $sql = "INSERT INTO users (tenant_id, name, email, phone, password, role, active, cnp, id_expiry, license_series, license_expiry) 
                VALUES (:tenant_id, :name, :email, :phone, :password, :role, :active, :cnp, :id_expiry, :license_series, :license_expiry)";
        
        return DB::query($sql, $data)->rowCount() > 0;
    }

    public function update(int $id, array $data): bool
    {
        $data['id'] = $id;
        $data['tenant_id'] = Auth::tenantId();

        $passwordSql = "";
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            $passwordSql = "password = :password,";
        } else {
            unset($data['password']);
        }

        $sql = "UPDATE users SET 
                name = :name,
                email = :email,
                phone = :phone,
                $passwordSql
                active = :active,
                cnp = :cnp,
                id_expiry = :id_expiry,
                license_series = :license_series,
                license_expiry = :license_expiry
                WHERE id = :id AND tenant_id = :tenant_id";
        
        return DB::query($sql, $data)->rowCount() > 0;
    }
}
