<?php

return "ALTER TABLE tenants 
    ADD COLUMN open_trip_reminder_enabled TINYINT(1) DEFAULT 0, 
    ADD COLUMN open_trip_reminder_last_sent DATE DEFAULT NULL;";
