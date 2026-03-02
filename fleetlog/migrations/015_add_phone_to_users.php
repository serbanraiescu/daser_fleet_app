<?php

namespace FleetLog\Migrations;

use FleetLog\Core\DB;

class M015_AddPhoneToUsers
{
    public function up(): void
    {
        DB::query("ALTER TABLE users ADD COLUMN phone VARCHAR(20) NULL AFTER email");
    }

    public function down(): void
    {
        DB::query("ALTER TABLE users DROP COLUMN phone");
    }
}
