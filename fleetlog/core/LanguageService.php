<?php

namespace FleetLog\Core;

class LanguageService
{
    private static array $translations = [];
    private static string $language = 'ro';

    public static function load(string $lang): void
    {
        self::$language = $lang;
        $file = dirname(__DIR__) . "/lang/{$lang}.php";
        
        if (file_exists($file)) {
            self::$translations = require $file;
        } else {
            // Fallback to Romanian if file is missing
            $fallback = dirname(__DIR__) . "/lang/ro.php";
            if (file_exists($fallback)) {
                self::$translations = require $fallback;
            }
        }
    }

    public static function get(string $key, array $placeholders = []): string
    {
        $text = self::$translations[$key] ?? $key;

        foreach ($placeholders as $placeholder => $value) {
            $text = str_replace("{{{$placeholder}}}", $value, $text);
        }

        return $text;
    }
}

// Global helper function for easier access in views
if (!function_exists('__')) {
    function __(string $key, array $placeholders = []): string
    {
        return \FleetLog\Core\LanguageService::get($key, $placeholders);
    }
}
