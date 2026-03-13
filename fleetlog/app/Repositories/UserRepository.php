<?php

namespace FleetLog\App\Repositories;

use FleetLog\Core\DB;
use FleetLog\Core\Auth;

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

    public function create(array $input): bool
    {
        $input = $this->prepareData($input);

        $password = null;
        if (isset($input['password']) && !empty($input['password'])) {
            $password = password_hash($input['password'], PASSWORD_DEFAULT);
        }
        
        $data = [
            'tenant_id'      => $input['tenant_id'],
            'name'           => $input['name'] ?? '',
            'email'          => $input['email'] ?? null,
            'phone'          => $input['phone'] ?? null,
            'password'       => $password,
            'role'           => $input['role'] ?? 'driver',
            'active'         => $input['active'] ?? 1,
            'cnp'            => $input['cnp'] ?? null,
            'id_expiry'      => $input['id_expiry'] ?? null,
            'license_series' => $input['license_series'] ?? null,
            'license_expiry' => $input['license_expiry'] ?? null,
            'pin'            => isset($input['pin']) ? password_hash($input['pin'], PASSWORD_DEFAULT) : null,
        ];
        
        $sql = "INSERT INTO users (tenant_id, name, email, phone, password, pin, role, active, cnp, id_expiry, license_series, license_expiry) 
                VALUES (:tenant_id, :name, :email, :phone, :password, :pin, :role, :active, :cnp, :id_expiry, :license_series, :license_expiry)";
        
        return DB::query($sql, $data)->rowCount() > 0;
    }

    public function update(int $id, array $input): bool
    {
        $input['id'] = $id;
        $input['tenant_id'] = Auth::tenantId();

        // Fetch current user to preserve existing data for missing keys
        $currentUser = $this->find($id);
        if (!$currentUser) return false;

        $passwordSql = "";
        if (!empty($input['password'])) {
            $input['password'] = password_hash($input['password'], PASSWORD_DEFAULT);
            $passwordSql = "password = :password,";
        } else {
            // If password is not provided, we keep the old one and DON'T include it in the SET clause
            $input['password'] = $currentUser['password'];
        }

        $data = [
            'id'             => $id,
            'tenant_id'      => $input['tenant_id'],
            'name'           => $input['name'] ?? $currentUser['name'],
            'email'          => $input['email'] ?? $currentUser['email'],
            'phone'          => $input['phone'] ?? $currentUser['phone'],
            'password'       => $input['password'],
            'active'         => $input['active'] ?? $currentUser['active'],
            'cnp'            => $input['cnp'] ?? $currentUser['cnp'],
            'id_expiry'      => $input['id_expiry'] ?? $currentUser['id_expiry'],
            'license_series' => $input['license_series'] ?? $currentUser['license_series'],
            'license_expiry' => $input['license_expiry'] ?? $currentUser['license_expiry'],
            'pin'            => $input['pin'] ?? $currentUser['pin'],
        ];

        // If password wasn't provided in $input, we still have it in $data but we don't need it in SQL
        // Actually, the current SQL uses $passwordSql variable for the SET list.
        // But if we use named parameters in execute(), ALL markers in the SQL must have a matching key in $data.
        // It's safer to just always include password in the update if we've handled the hash above.

        $sql = "UPDATE users SET 
                name = :name,
                email = :email,
                phone = :phone,
                password = :password,
                active = :active,
                cnp = :cnp,
                id_expiry = :id_expiry,
                license_series = :license_series,
                license_expiry = :license_expiry,
                pin = :pin
                WHERE id = :id AND tenant_id = :tenant_id";
        
        return DB::query($sql, $data)->rowCount() > 0;
    }

    public function updatePin(int $id, string $pin): bool
    {
        $hashedPin = password_hash($pin, PASSWORD_DEFAULT);
        return DB::query("UPDATE users SET pin = ? WHERE id = ?", [$hashedPin, $id])->rowCount() > 0;
    }
}
