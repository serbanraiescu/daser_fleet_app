<?php

return "INSERT INTO system_settings (`key`, `value`) VALUES ('admin_report_email', 'office@daserdesign.ro') ON DUPLICATE KEY UPDATE value = value;";
