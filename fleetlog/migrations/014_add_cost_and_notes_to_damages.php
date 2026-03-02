<?php

return "ALTER TABLE damage_reports 
        ADD COLUMN repair_cost DECIMAL(10, 2) DEFAULT 0.00 AFTER description,
        ADD COLUMN admin_notes TEXT AFTER repair_cost;";
