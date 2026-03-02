<?php

return "ALTER TABLE vehicles 
        ADD COLUMN status ENUM('active', 'inactive', 'service') DEFAULT 'active' AFTER is_active,
        DROP COLUMN is_active;";
