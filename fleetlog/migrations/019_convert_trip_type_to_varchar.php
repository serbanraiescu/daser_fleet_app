<?php

use FleetLog\Core\DB;

DB::query("ALTER TABLE trips MODIFY COLUMN type VARCHAR(50) NOT NULL");
