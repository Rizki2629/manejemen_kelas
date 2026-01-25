<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class DbRowCounts extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:rowcounts';
    protected $description = 'Show row counts for every table in the current database connection.';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $tables = $db->listTables();
        sort($tables);

        if (empty($tables)) {
            CLI::write('No tables found.', 'yellow');
            return;
        }

        CLI::write(str_pad('table', 35) . "rows", 'cyan');
        CLI::write(str_repeat('-', 50), 'cyan');

        foreach ($tables as $t) {
            try {
                // Quote identifier for PostgreSQL; safe for MySQL too.
                $row = $db->query('SELECT COUNT(*) AS c FROM "' . $t . '"')->getRowArray();
                $count = $row['c'] ?? 0;
            } catch (\Throwable $e) {
                // Fallback for drivers that don't like double-quotes
                try {
                    $row = $db->query('SELECT COUNT(*) AS c FROM ' . $t)->getRowArray();
                    $count = $row['c'] ?? 0;
                } catch (\Throwable $e2) {
                    CLI::write(str_pad($t, 35) . 'ERROR: ' . $e2->getMessage(), 'red');
                    continue;
                }
            }

            CLI::write(str_pad($t, 35) . $count);
        }
    }
}
