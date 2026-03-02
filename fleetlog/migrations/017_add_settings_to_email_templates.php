<?php

use FleetLog\Core\DB;

DB::query("ALTER TABLE email_templates 
    ADD COLUMN alert_days INT DEFAULT 7 AFTER slug,
    ADD COLUMN recipient_type ENUM('tenant', 'admin') DEFAULT 'tenant' AFTER alert_days");

// Update existing notification templates to reasonable defaults
DB::query("UPDATE email_templates SET alert_days = 30 WHERE slug = 'expiry_alert_itp'");
DB::query("UPDATE email_templates SET alert_days = 15 WHERE slug = 'expiry_alert_rca'");
DB::query("UPDATE email_templates SET alert_days = 7 WHERE slug = 'expiry_alert_rovigneta'");
