<?php

return "ALTER TABLE users 
        ADD COLUMN cnp VARCHAR(13) AFTER active,
        ADD COLUMN id_expiry DATE AFTER cnp,
        ADD COLUMN license_series VARCHAR(50) AFTER id_expiry,
        ADD COLUMN license_expiry DATE AFTER license_series;";
