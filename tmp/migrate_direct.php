<?php
$db = new SQLite3('fleetlog/database.sqlite');
@$db->exec("ALTER TABLE tenants ADD COLUMN daily_report_enabled INTEGER DEFAULT 0");
@$db->exec("ALTER TABLE tenants ADD COLUMN daily_report_last_sent TEXT");
echo "DONE";
