<?php

// 1. Add column to tenants
$sql1 = "ALTER TABLE tenants ADD COLUMN send_to_admin_enabled TINYINT DEFAULT 0 AFTER daily_report_phone_type;";

// 2. Insert initial admin_report_email setting
$sql2 = "INSERT INTO system_settings (`key`, `value`) VALUES ('admin_report_email', 'office@daserdesign.ro') ON DUPLICATE KEY UPDATE value = value;";

return $sql1 . " " . $sql2;
