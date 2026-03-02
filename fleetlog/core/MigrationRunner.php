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
        $sql = require $filePath;
        
        try {
            DB::query($sql);
            DB::query("INSERT INTO migrations (migration) VALUES (?)", [$file]);
            echo "Executed: $file\n";
        } catch (\Exception $e) {
            echo "Error executing $file: " . $e->getMessage() . "\n";
        }
    }
}
