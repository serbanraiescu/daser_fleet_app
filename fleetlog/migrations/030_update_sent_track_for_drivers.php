<?php

use FleetLog\Core\DB;

return [
    'up' => "
        ALTER TABLE email_sent_track 
            MODIFY COLUMN vehicle_id INT NULL,
            ADD COLUMN user_id INT NULL AFTER vehicle_id,
            DROP INDEX vehicle_template_day,
            ADD UNIQUE KEY sent_tracking_unique (vehicle_id, user_id, template_slug, alert_day);
    ",
    'down' => "
        ALTER TABLE email_sent_track 
            DROP INDEX sent_tracking_unique,
            DROP COLUMN user_id,
            MODIFY COLUMN vehicle_id INT NOT NULL,
            ADD UNIQUE KEY vehicle_template_day (vehicle_id, template_slug, alert_day);
    "
];
