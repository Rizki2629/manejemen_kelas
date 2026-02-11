<?php

namespace Config;

use CodeIgniter\Database\Config;

/**
 * Database Configuration
 */
class Database extends Config
{
    /**
     * The directory that holds the Migrations and Seeds directories.
     */
    public string $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;

    /**
     * Lets you choose which connection group to use if no other is specified.
     */
    public string $defaultGroup = 'default';

    /**
     * The default database connection.
     *
     * @var array<string, mixed>
     */
    public array $default = [
        'DSN'          => '',
        'hostname'     => 'localhost',
        'username'     => '',
        'password'     => '',
        'database'     => '',
        'DBDriver'     => 'MySQLi',
        'DBPrefix'     => '',
        'pConnect'     => true, // Enable persistent connections for better performance
        'DBDebug'      => true,
        // IMPORTANT:
        // - MySQL uses utf8mb4
        // - PostgreSQL expects UTF8 (no utf8mb4)
        // We'll normalize this at runtime in the constructor.
        'charset'      => 'utf8mb4',
        'DBCollat'     => 'utf8mb4_general_ci',
        'swapPre'      => '',
        'encrypt'      => false,
        'compress'     => false,
        'strictOn'     => false,
        'failover'     => [],
        'port'         => 3306,
        'numberNative' => false,
        'foundRows'    => false,
        'dateFormat'   => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
        // Additional optimization settings
        'save_queries' => ENVIRONMENT !== 'production', // Only save queries in non-production
        'maxLifetime'  => 0, // Connection max lifetime (0 = no limit)
        'maxIdleTime'  => 600, // Max idle time before closing (10 minutes)
    ];

    /**
     * This database connection is used when running PHPUnit database tests.
     *
     * @var array<string, mixed>
     */
    public array $tests = [
        'DSN'         => '',
        'hostname'    => '127.0.0.1',
        'username'    => '',
        'password'    => '',
        'database'    => ':memory:',
        'DBDriver'    => 'SQLite3',
        'DBPrefix'    => 'db_',  // Needed to ensure we're working correctly with prefixes live. DO NOT REMOVE FOR CI DEVS
        'pConnect'    => false,
        'DBDebug'     => true,
        'charset'     => 'utf8',
        'DBCollat'    => '',
        'swapPre'     => '',
        'encrypt'     => false,
        'compress'    => false,
        'strictOn'    => false,
        'failover'    => [],
        'port'        => 3306,
        'foreignKeys' => true,
        'busyTimeout' => 1000,
        'dateFormat'  => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];

    public function __construct()
    {
        parent::__construct();

        // Parse JAWSDB_URL for Heroku deployment
        $jawsdbUrl = getenv('JAWSDB_URL');
        if ($jawsdbUrl) {
            $dbparts = parse_url($jawsdbUrl);
            $this->default['hostname'] = $dbparts['host'] ?? 'localhost';
            $this->default['username'] = $dbparts['user'] ?? '';
            $this->default['password'] = $dbparts['pass'] ?? '';
            $this->default['database'] = ltrim($dbparts['path'] ?? '', '/');
            $this->default['port']     = $dbparts['port'] ?? 3306;
            $this->default['DBDriver'] = 'MySQLi';
            $this->default['charset']  = 'utf8mb4';
            $this->default['DBCollat'] = 'utf8mb4_general_ci';
        }

        // If we are using PostgreSQL, ensure the charset is compatible.
        // CI4 will run SET client_encoding based on this value.
        if (($this->default['DBDriver'] ?? null) === 'Postgre' || ($this->default['DBDriver'] ?? null) === 'PostgreSQL') {
            $this->default['charset']  = 'utf8';
            $this->default['DBCollat'] = '';
        }
    }
}
