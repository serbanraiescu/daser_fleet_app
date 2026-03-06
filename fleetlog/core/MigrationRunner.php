<?php

namespace FleetLog\Core;

class MigrationRunner
{
    public function run(): void
    {
        $this->createMigrationsTable();
        $executed = $this->getExecutedMigrations();
        $files = scandir(dirname(__DIR__) . '/migrations');

        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || !str_ends_with($file, '.php')) {
                continue;
            }

            if (!in_array($file, $executed)) {
                $this->execute($file);
            }
        }
    }

    private function createMigrationsTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;";
        DB::query($sql);
    }

    private function getExecutedMigrations(): array
    {
        $rows = DB::fetchAll("SELECT migration FROM migrations");
        return array_column($rows, 'migration');
    }

    private function execute(string $file): void
    {
        $filePath = dirname(__DIR__) . '/migrations/' . $file;
        echo "Executing migration: $file ... ";
        
        try {
            $result = require $filePath;
            
            if (is_object($result) && method_exists($result, 'up')) {
                $result->up();
                echo "<span style='color:green'>Class UP method called.</span> ";
            } elseif (is_string($result)) {
                DB::query($result);
                echo "<span style='color:green'>String SQL executed.</span> ";
            }
            
            DB::query("INSERT INTO migrations (migration) VALUES (?)", [$file]);
            echo "<b style='color:green'>SUCCESS</b><br>";
        } catch (\Exception $e) {
            echo "<b style='color:red'>ERROR:</b> " . $e->getMessage() . "<br>";
            error_log("Migration Error ($file): " . $e->getMessage());
        }
    }
}
