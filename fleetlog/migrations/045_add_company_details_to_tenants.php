<?php

return "ALTER TABLE tenants 
        ADD COLUMN reg_com VARCHAR(50) AFTER cui,
        ADD COLUMN county VARCHAR(100) AFTER address,
        ADD COLUMN city VARCHAR(100) AFTER county;";
