<?php

namespace FleetLog\Core;

use PDO;
use PDOException;

class DB
{
    private static ?PDO $instance = null;

    private static function getEnv(string $key, $default = null)
    {
        $val = getenv($key);
        if ($val === false || $val === "") {
            $val = $_ENV[$key] ?? $_SERVER[$key] ?? $default;
        }
        return $val;
    }

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $host = self::getEnv('DB_HOST', 'localhost');
            $db   = self::getEnv('DB_NAME');
            $user = self::getEnv('DB_USER');
            $pass = self::getEnv('DB_PASS');
            $charset = 'utf8mb4';

            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                throw new PDOException($e->getMessage(), (int)$e->getCode());
            }
        }

        return self::$instance;
    }

    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetch(string $sql, array $params = []): ?array
    {
        return self::query($sql, $params)->fetch() ?: null;
    }

    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    public static function getSetting(string $key, $default = null)
    {
        $res = self::fetch("SELECT value FROM system_settings WHERE `key` = ?", [$key]);
        return $res ? $res['value'] : $default;
    }

    public static function lastInsertId(): string
    {
        return self::getInstance()->lastInsertId();
    }
}
