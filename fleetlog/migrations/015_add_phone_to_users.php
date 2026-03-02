<?php

use FleetLog\Core\DB;

DB::query("ALTER TABLE users ADD COLUMN phone VARCHAR(20) NULL AFTER email");
