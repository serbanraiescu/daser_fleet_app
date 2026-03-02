<?php
echo "GD Extension: " . (extension_loaded('gd') ? "LOADED" : "NOT LOADED") . "\n";
echo "imagecreatetruecolor exists: " . (function_exists('imagecreatetruecolor') ? "YES" : "NO") . "\n";
