<?php

return "ALTER TABLE tenants 
    ADD COLUMN daily_report_enabled TINYINT(1) DEFAULT 0, 
    ADD COLUMN daily_report_last_sent DATE DEFAULT NULL;";
