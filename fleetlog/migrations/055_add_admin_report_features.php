<?php

return "ALTER TABLE tenants ADD COLUMN send_to_admin_enabled TINYINT DEFAULT 0 AFTER daily_report_phone_type;";
