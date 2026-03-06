<?php

namespace FleetLog\Core;

/**
 * TemplateService - Handles placeholder replacement for SMS and Email
 */
class TemplateService
{
    /**
     * Replace placeholders in a string with data from an array
     * 
     * @param string $text The template string
     * @param array $data Associative array of placeholder => value
     * @return string
     */
    public static function replace(string $text, array $data): string
    {
        $placeholders = [];
        $replacements = [];

        foreach ($data as $key => $value) {
            $placeholders[] = '{' . $key . '}';
            $replacements[] = $value;
        }

        return str_replace($placeholders, $replacements, $text);
    }

    /**
     * Get a template by its key
     */
    public static function getTemplate(string $key): ?array
    {
        return DB::fetch("SELECT * FROM sms_queue_templates WHERE template_key = ? AND is_active = 1", [$key]);
    }
}
