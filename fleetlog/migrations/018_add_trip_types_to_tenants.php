<?php

use FleetLog\Core\DB;

DB::query("ALTER TABLE tenants ADD COLUMN trip_types TEXT NULL AFTER timezone");
