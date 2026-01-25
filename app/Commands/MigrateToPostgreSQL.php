<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Migrasi data dari MySQL (sdngu09.sql) ke PostgreSQL (Railway)
 * 
 * Usage: php spark migrate:postgresql
 */
class MigrateToPostgreSQL extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'migrate:postgresql';
    protected $description = 'Migrasi data MySQL ke PostgreSQL Railway';

    protected $sqlFile = ROOTPATH . 'sdngu09.sql';
    protected $mysqlDb;
    protected $postgresDb;

    protected string $failedLogFile;

    public function run(array $params)
    {
        CLI::write('==============================================', 'green');
        CLI::write('   Migrasi MySQL → PostgreSQL (Railway)', 'green');
        CLI::write('==============================================', 'green');
        CLI::newLine();

        $this->failedLogFile = WRITEPATH . 'logs' . DIRECTORY_SEPARATOR . 'migrate_pg_failed.sql';

        $argv = $_SERVER['argv'] ?? [];
        $isRetry = in_array('--retry', $argv, true) || in_array('retry', $params, true) || in_array('--retry', $params, true);

        // Mode selection: mysql (option 1) or direct (option 2)
        $mode = 'direct';
        foreach ($argv as $a) {
            if (str_starts_with($a, '--mode=')) {
                $mode = strtolower(substr($a, 7));
            }
        }

        if ($isRetry) {
            CLI::write('Mode: RETRY', 'cyan');
            if (!$this->testPostgreSQLConnection()) {
                return;
            }
            $this->retryFailedStatements();
            return;
        }

        // Step 1: Validasi file SQL
        if (!$this->validateSQLFile()) {
            return;
        }

        // Step 2: Test koneksi PostgreSQL
        if (!$this->testPostgreSQLConnection()) {
            return;
        }

        if ($mode === 'mysql') {
            CLI::write('Mode: MYSQL (opsi 1 - pakai MySQL lokal temp)', 'cyan');

            // Step 3: Setup temporary MySQL database
            if (!$this->setupTempMySQL()) {
                return;
            }

            // Step 4: Import SQL ke temp MySQL
            if (!$this->importSQLToMySQL()) {
                return;
            }

            // Step 5: Migrasi tabel ke PostgreSQL
            $this->migrateTables();

            // Step 6: Validasi data
            $this->validateMigration();

            // Step 7: Cleanup
            $this->cleanup();

            CLI::newLine();
            CLI::write('==============================================', 'green');
            CLI::write('   Migrasi Selesai! ✅', 'green');
            CLI::write('==============================================', 'green');
            return;
        }

        // Default: direct import
        CLI::write('Mode: DIRECT (opsi 2 - parse dump langsung)', 'cyan');

        // Reset failed log file
        @file_put_contents($this->failedLogFile, "-- Failed statements log\n-- Generated: " . date('c') . "\n\n");

        // Step 3: Import langsung dari dump MySQL ke PostgreSQL (tanpa MySQL lokal)
        if (!$this->importDumpDirectlyToPostgres()) {
            return;
        }

        CLI::newLine();
        CLI::write('Failed statement log: ' . $this->failedLogFile, 'cyan');

        CLI::newLine();
        CLI::write('==============================================', 'green');
        CLI::write('   Migrasi Selesai! ✅', 'green');
        CLI::write('==============================================', 'green');
    }

    private function retryFailedStatements(): void
    {
        CLI::write('🔁 Retry failed statements...', 'yellow');
        CLI::write('Log file: ' . $this->failedLogFile, 'cyan');

        if (!is_file($this->failedLogFile)) {
            CLI::error('File log gagal tidak ditemukan. Jalankan migrasi normal dulu.');
            return;
        }

        $lines = file($this->failedLogFile, FILE_IGNORE_NEW_LINES);
        if ($lines === false) {
            CLI::error('Gagal membaca file log.');
            return;
        }

        $stmts = [];
        $buf = '';
        $inInsert = false;

        foreach ($lines as $line) {
            $trim = ltrim($line);

            if ($trim === '' || str_starts_with($trim, '--')) {
                continue;
            }

            if (!$inInsert) {
                if (stripos($trim, 'INSERT INTO') === 0) {
                    $inInsert = true;
                    $buf = $trim;

                    if (str_contains($trim, ';')) {
                        $stmts[] = $buf;
                        $buf = '';
                        $inInsert = false;
                    }
                }
                continue;
            }

            // continue current INSERT; preserve newlines as spaces
            $buf .= "\n" . $line;
            if (str_contains($line, ';')) {
                $stmts[] = $buf;
                $buf = '';
                $inInsert = false;
            }
        }

        $stmts = array_values(array_filter(array_map('trim', $stmts), static fn($s) => $s !== ''));

        $total = count($stmts);
        CLI::write("Total failed statements to retry: {$total}", 'cyan');

        $ok = 0;
        $fail = 0;

        foreach ($stmts as $idx => $stmt) {
            // ensure single trailing semicolon
            $stmt = rtrim($stmt);
            $stmt = rtrim($stmt, ';') . ';';
            $stmt = $this->normalizeInsertForPostgres($stmt);

            $this->postgresDb->transBegin();
            try {
                $this->postgresDb->query($stmt);

                if ($this->postgresDb->transStatus() === false) {
                    // Try to pull last error if available
                    $err = method_exists($this->postgresDb, 'error') ? $this->postgresDb->error() : null;
                    $msg = is_array($err) ? ($err['message'] ?? 'Unknown DB error') : 'Unknown DB error';
                    throw new \RuntimeException('Query failed (transStatus=false): ' . $msg);
                }

                $this->postgresDb->transCommit();
                $ok++;
            } catch (\Throwable $e) {
                $this->postgresDb->transRollback();
                $fail++;

                $preview = substr(preg_replace('/\s+/', ' ', $stmt), 0, 260);
                CLI::write('⚠️  Retry failed #' . ($idx + 1) . ': ' . $e->getMessage(), 'yellow');
                CLI::write('   SQL preview: ' . $preview . (strlen($stmt) > 260 ? ' ...' : ''), 'yellow');
            }

            if (($idx + 1) % 10 === 0) {
                CLI::write("... progress retry " . ($idx + 1) . "/{$total}", 'cyan');
            }
        }

        CLI::newLine();
        CLI::write("✅ Retry done. OK={$ok}, FAIL={$fail}", 'green');
    }

    private function normalizeInsertForPostgres(string $stmt): string
    {
        // Turn MySQL-style backslash escaping into Postgres E'' strings.
        // This is a best-effort normalizer for JSON/text columns.
        // We only change if a backslash escape is present.
        if (!str_contains($stmt, '\\')) {
            return $stmt;
        }

        // Replace single-quoted literals that contain backslashes with E'...'
        $stmt = preg_replace_callback(
            "/'([^']*\\\\[^']*)'/s",
            static function ($m) {
                $s = $m[1];
                // Ensure backslashes are preserved; Postgres E'' will interpret them.
                return "E'" . str_replace("'", "''", $s) . "'";
            },
            $stmt
        );

        return $stmt;
    }

    /**
     * Direct importer:
     * - parses CREATE TABLE ... and INSERT INTO ... from a phpMyAdmin MySQL dump
     * - converts common MySQL types/syntax to PostgreSQL
     * - loads schema then data in PostgreSQL
     */
    private function importDumpDirectlyToPostgres(): bool
    {
        CLI::write('📥 Import dump MySQL langsung ke PostgreSQL...', 'yellow');
        CLI::write('   (Tanpa MySQL lokal, ini bisa agak lama)', 'cyan');

        try {
            $sql = file_get_contents($this->sqlFile);
            if ($sql === false) {
                throw new \RuntimeException('Gagal membaca file SQL.');
            }

            // Remove common dump noise we don't need
            $sql = preg_replace('/^\\s*--.*$/m', '', $sql);
            $sql = preg_replace('/^\\s*\\/\\*![0-9]+.*?\\*\\/;?\\s*$/m', '', $sql);
            $sql = str_replace("`", '"', $sql);

            // 1) Schema (no explicit transaction; DDL errors will throw with details)
            $createdTables = $this->importCreateTableStatements($sql);

            // 2) Data loads
            $insertedRows = $this->importInsertStatements($sql);

            CLI::newLine();
            CLI::write("✅ Schema dibuat: {$createdTables} tabel", 'green');
            CLI::write("✅ Data berhasil di-insert (perkiraan): ~{$insertedRows} rows", 'green');
            return true;
        } catch (\Throwable $e) {
            CLI::error('❌ Import gagal: ' . $e->getMessage());
            return false;
        }
    }

    private function importCreateTableStatements(string $sql): int
    {
        // Match CREATE TABLE "table" ( ... ) ENGINE=...;
        preg_match_all('/CREATE\\s+TABLE\\s+"(?<name>[^"]+)"\\s*\\((?<body>.*?)\\)\\s*ENGINE=.*?;/is', $sql, $matches, PREG_SET_ORDER);

        $count = 0;
        foreach ($matches as $m) {
            $table = $m['name'];
            $body  = $m['body'];

            CLI::write("🧱 Buat tabel: {$table}", 'yellow');

            try {
                $this->postgresDb->query('DROP TABLE IF EXISTS "' . $table . '" CASCADE');

                $pgCreate = $this->convertMySqlCreateTableToPostgres($table, $body);
                $this->postgresDb->query($pgCreate);

                $count++;
            } catch (\Throwable $e) {
                CLI::error("❌ Gagal buat tabel {$table}: " . $e->getMessage());
                if (isset($pgCreate)) {
                    CLI::write('--- CREATE SQL (Postgre) ---', 'yellow');
                    CLI::write($pgCreate, 'yellow');
                    CLI::write('---------------------------', 'yellow');
                }
                throw $e;
            }
        }

        return $count;
    }

    private function convertMySqlCreateTableToPostgres(string $table, string $body): string
    {
        $lines = preg_split('/\\r?\\n/', trim($body));
        $cols  = [];
        $constraints = [];

        foreach ($lines as $line) {
            $line = trim($line);
            $line = rtrim($line, ',');
            if ($line === '') {
                continue;
            }

            // PRIMARY KEY (`id`)
            if (preg_match('/^PRIMARY\\s+KEY\\s*\\((?<cols>.+)\\)$/i', $line, $mm)) {
                $constraints[] = 'PRIMARY KEY (' . $mm['cols'] . ')';
                continue;
            }

            // KEY / INDEX / CONSTRAINT - optional (skip for now)
            if (preg_match('/^(KEY|INDEX|UNIQUE\\s+KEY|CONSTRAINT)\\s+/i', $line)) {
                // For a first pass, skip indexes/foreign keys (can be added later)
                continue;
            }

            // column line: "col" int(10) unsigned NOT NULL AUTO_INCREMENT
            if (!preg_match('/^"(?<name>[^"]+)"\\s+(?<type>[a-zA-Z]+)(?<len>\\([^\\)]*\\))?(?<rest>.*)$/', $line, $mm)) {
                continue;
            }

            $name = $mm['name'];
            $type = strtolower($mm['type']);
            $len  = $mm['len'] ?? '';
            $rest = strtolower($mm['rest'] ?? '');

            $pgType = $this->mapMySqlTypeToPostgres($type, $len, $rest);

            $col = '"' . $name . '" ' . $pgType;

            if (str_contains($rest, 'not null')) {
                $col .= ' NOT NULL';
            }

            // DEFAULT handling (basic)
            if (preg_match('/default\\s+(?<def>[^\\s,]+)/i', $mm['rest'] ?? '', $dm)) {
                $def = trim($dm['def']);

                // Normalize MySQL function defaults to PostgreSQL
                if (preg_match('/^current_timestamp\\(\\)$/i', $def)) {
                    $def = 'CURRENT_TIMESTAMP';
                }

                if (strcasecmp($def, 'NULL') !== 0) {
                    $col .= ' DEFAULT ' . $def;
                }
            }

            // AUTO_INCREMENT => identity
            if (str_contains($rest, 'auto_increment')) {
                // Use identity for integer-like
                $col = '"' . $name . '" ' . $this->autoIncrementType($type, $len, $rest);
                $col .= ' GENERATED BY DEFAULT AS IDENTITY';
                if (str_contains($rest, 'not null')) {
                    $col .= ' NOT NULL';
                }
            }

            $cols[] = $col;
        }

        $all = array_merge($cols, $constraints);

        // IMPORTANT: proper quoting of table name
        return 'CREATE TABLE "' . $table . '"' . " (\n  " . implode(",\n  ", $all) . "\n);";
    }

    private function mapMySqlTypeToPostgres(string $type, string $len, string $rest): string
    {
        $isUnsigned = str_contains($rest, 'unsigned');

        // Strip parentheses for numeric length, e.g. (10) or (10,2)
        $lenClean = trim($len);

        return match ($type) {
            'tinyint'   => 'SMALLINT',
            'smallint'  => 'SMALLINT',
            'mediumint' => 'INTEGER',
            'int', 'integer' => 'INTEGER',
            'bigint'    => 'BIGINT',
            'float'     => 'REAL',
            'double'    => 'DOUBLE PRECISION',
            'decimal', 'numeric' => 'NUMERIC' . $lenClean,
            'varchar'   => 'VARCHAR' . ($lenClean !== '' ? $lenClean : '(255)'),
            'char'      => 'CHAR' . ($lenClean !== '' ? $lenClean : '(1)'),
            'text', 'mediumtext', 'longtext' => 'TEXT',
            'date'      => 'DATE',
            'datetime'  => 'TIMESTAMP',
            'timestamp' => 'TIMESTAMP',
            'time'      => 'TIME',
            'blob', 'longblob', 'mediumblob' => 'BYTEA',
            'enum'      => 'VARCHAR(50)',
            default     => 'TEXT',
        };
    }

    private function autoIncrementType(string $type, string $len, string $rest): string
    {
        $t = strtolower($type);
        return match ($t) {
            'bigint' => 'BIGINT',
            default  => 'INTEGER',
        };
    }

    private function importInsertStatements(string $sql): int
    {
        preg_match_all('/INSERT\\s+INTO\\s+"(?<table>[^"]+)"\\s*\\((?<cols>[^\\)]*)\\)\\s*VALUES\\s*(?<values>.*?);/is', $sql, $matches, PREG_SET_ORDER);

        $insertStatements = count($matches);
        $approxRows = 0;

        CLI::write("📦 Total INSERT statements: {$insertStatements}", 'cyan');

        $i = 0;
        foreach ($matches as $m) {
            $i++;
            $table  = $m['table'];
            $cols   = trim($m['cols']);
            $values = trim($m['values']);

            // Approximate row count
            $approxRows += max(1, preg_match_all('/\\(/', $values));

            $stmt = 'INSERT INTO "' . $table . '" (' . $cols . ') VALUES ' . $values . ';';
            $stmtForExec = $this->normalizeInsertForPostgres($stmt);

            // Per-statement transaction for resilience
            $this->postgresDb->transBegin();
            try {
                $this->postgresDb->query($stmtForExec);
                if ($this->postgresDb->transStatus() === false) {
                    throw new \RuntimeException('Query failed (transStatus=false).');
                }
                $this->postgresDb->transCommit();
            } catch (\Throwable $e) {
                $this->postgresDb->transRollback();

                CLI::write("⚠️  Gagal INSERT ke {$table} (#{$i}/{$insertStatements}): " . $e->getMessage(), 'yellow');

                // Log failed statement for retry
                try {
                    @file_put_contents(
                        $this->failedLogFile,
                        "-- Failed: {$table} (#{$i}/{$insertStatements})\n" . $stmt . "\n\n",
                        FILE_APPEND
                    );
                } catch (\Throwable $ignored) {
                    // ignore logging failure
                }

                // Continue
            }

            if ($i % 25 === 0) {
                CLI::write("... progress INSERT {$i}/{$insertStatements}", 'cyan');
            }
        }

        return $approxRows;
    }

    private function validateSQLFile(): bool
    {
        CLI::write('📄 Validasi file SQL...', 'yellow');

        if (!file_exists($this->sqlFile)) {
            CLI::error("File tidak ditemukan: {$this->sqlFile}");
            CLI::write("Pastikan file sdngu09.sql ada di root folder project.", 'red');
            return false;
        }

        $fileSize = filesize($this->sqlFile);
        $fileSizeMB = round($fileSize / 1024 / 1024, 2);

        CLI::write("✅ File ditemukan: sdngu09.sql ({$fileSizeMB} MB)", 'green');
        return true;
    }

    private function testPostgreSQLConnection(): bool
    {
        CLI::write('🔌 Test koneksi PostgreSQL...', 'yellow');

        try {
            $this->postgresDb = \Config\Database::connect();

            // Test simple query
            $result = $this->postgresDb->query("SELECT version()")->getRow();

            CLI::write("✅ Koneksi PostgreSQL berhasil!", 'green');
            CLI::write("   Version: " . substr($result->version, 0, 50) . "...", 'cyan');

            return true;
        } catch (\Exception $e) {
            CLI::error("❌ Gagal koneksi PostgreSQL: " . $e->getMessage());
            CLI::write("Pastikan credentials Railway sudah benar di .env", 'red');
            return false;
        }
    }

    private function setupTempMySQL(): bool
    {
        CLI::write('🗄️  Setup temporary MySQL...', 'yellow');

        try {
            $config = [
                'DSN'      => '',
                'hostname' => '127.0.0.1',
                'username' => 'root',
                'password' => '',
                'database' => '',
                'DBDriver' => 'MySQLi',
                'DBPrefix' => '',
                'pConnect' => false,
                'DBDebug'  => false,
                'charset'  => 'utf8mb4',
                'DBCollat' => 'utf8mb4_general_ci',
                'port'     => 3306,
            ];

            $this->mysqlDb = \Config\Database::connect($config);

            $this->mysqlDb->query("CREATE DATABASE IF NOT EXISTS sdngu09_temp");
            $this->mysqlDb->query("USE sdngu09_temp");

            CLI::write("✅ Temporary MySQL database created: sdngu09_temp", 'green');
            return true;
        } catch (\Exception $e) {
            CLI::error("❌ MySQL lokal tidak tersedia: " . $e->getMessage());
            CLI::write("Pastikan MySQL XAMPP nyala di 127.0.0.1:3306 (user root, password kosong)", 'yellow');
            return false;
        }
    }

    private function importSQLToMySQL(): bool
    {
        CLI::write('📤 Import SQL ke MySQL temporary...', 'yellow');

        try {
            // Use mysql CLI for robust import (prevents "MySQL server has gone away" on big multi-statement query)
            $mysqlBin = 'mysql';
            $xamppMysql = 'C:\\xampp\\mysql\\bin\\mysql.exe';
            if (is_file($xamppMysql)) {
                $mysqlBin = $xamppMysql;
            }

            $host = '127.0.0.1';
            $port = '3306';
            $user = 'root';
            $db   = 'sdngu09_temp';

            $sqlFile = $this->sqlFile;
            if (!is_file($sqlFile)) {
                throw new \RuntimeException('File SQL tidak ditemukan: ' . $sqlFile);
            }

            // PowerShell-safe command: use cmd.exe redirection
            $cmd = 'cmd /c ' . escapeshellarg("\"{$mysqlBin}\" -h {$host} -P {$port} -u {$user} {$db} < \"{$sqlFile}\"");
            $output = [];
            $code = 0;
            @exec($cmd, $output, $code);

            if ($code !== 0) {
                throw new \RuntimeException(
                    'mysql CLI import gagal (exit code ' . $code . '). ' .
                    'Pastikan mysql.exe tersedia (XAMPP: C:\\xampp\\mysql\\bin\\mysql.exe) dan service MySQL berjalan.'
                );
            }

            CLI::write("✅ SQL berhasil di-import ke MySQL temporary (via mysql CLI)", 'green');
            return true;
        } catch (\Throwable $e) {
            CLI::error('❌ Gagal import SQL ke MySQL: ' . $e->getMessage());
            return false;
        }
    }

    private function migrateTables(): void
    {
        CLI::write('🔄 Migrasi tabel ke PostgreSQL...', 'yellow');

        // Reset failed log file (for MySQL mode too)
        @file_put_contents($this->failedLogFile, "-- Failed statements log\n-- Generated: " . date('c') . "\n\n");

        // Get list of tables from temporary MySQL
        $q = $this->mysqlDb->query("SHOW TABLES");
        if ($q === false) {
            $msg = $this->dbErrorMessage($this->mysqlDb);
            CLI::error('❌ Gagal menjalankan SHOW TABLES di MySQL temp: ' . $msg);
            return;
        }

        $tables = $q->getResultArray();

        foreach ($tables as $t) {
            $table = reset($t);

            CLI::write("📤 Migrasi tabel: {$table}", 'cyan');

            // 1) Export table structure
            $row = $this->mysqlDb->query("SHOW CREATE TABLE `{$table}`")->getRowArray();
            $createSql = $row['Create Table'] ?? $row['Create Table '] ?? '';

            if ($createSql === '') {
                CLI::write("⚠️  Lewati {$table}: gagal ambil SHOW CREATE TABLE", 'yellow');
                continue;
            }

            // 2) Convert MySQL DDL -> PostgreSQL DDL and (re)create table
            try {
                $this->postgresDb->query('DROP TABLE IF EXISTS "' . $table . '" CASCADE');
                $pgCreate = $this->convertMySqlShowCreateTableToPostgres($table, $createSql);
                $this->postgresDb->query($pgCreate);
            } catch (\Throwable $e) {
                CLI::error("❌ Gagal buat tabel {$table} di PostgreSQL: " . $e->getMessage());
                @file_put_contents(
                    $this->failedLogFile,
                    "-- Failed: {$table} (DDL)\n-- " . $e->getMessage() . "\n" . ($pgCreate ?? '') . "\n\n",
                    FILE_APPEND
                );
                continue;
            }

            // 3) Export data in chunks
            $total = (int) ($this->mysqlDb->query("SELECT COUNT(*) AS c FROM `{$table}`")->getRowArray()['c'] ?? 0);
            if ($total === 0) {
                CLI::write("✅ {$table}: kosong", 'green');
                continue;
            }

            $batchSize = 250; // smaller batch for safer insertion
            $offset = 0;
            $inserted = 0;

            while ($offset < $total) {
                $data = $this->mysqlDb
                    ->query("SELECT * FROM `{$table}` LIMIT {$batchSize} OFFSET {$offset}")
                    ->getResultArray();

                if (empty($data)) {
                    break;
                }

                $insertSql = $this->prepareInsertStatement($table, $data);
                if ($insertSql === '') {
                    break;
                }

                $this->postgresDb->transBegin();
                try {
                    $this->postgresDb->query($insertSql);
                    if ($this->postgresDb->transStatus() === false) {
                        throw new \RuntimeException('Query failed (transStatus=false).');
                    }
                    $this->postgresDb->transCommit();
                    $inserted += count($data);
                } catch (\Throwable $e) {
                    $this->postgresDb->transRollback();

                    $pgMsg = $this->dbErrorMessage($this->postgresDb);
                    CLI::error("❌ Gagal insert batch {$table} offset {$offset}: " . $e->getMessage() . " | DB: " . $pgMsg);

                    @file_put_contents(
                        $this->failedLogFile,
                        "-- Failed: {$table} (offset={$offset}, batch=" . count($data) . ")\n-- " . $e->getMessage() . "\n-- DB: " . $pgMsg . "\n" . $insertSql . (strlen($insertSql) > 4000 ? " ...\n" : "\n") . "\n\n",
                        FILE_APPEND
                    );

                    // Stop further batches for this table (we need to fix schema/data issues first)
                    break;
                }

                $offset += count($data);

                if ($offset % 1000 === 0) {
                    CLI::write("... progress {$table}: {$offset}/{$total}", 'cyan');
                }
            }

            CLI::write("✅ Tabel migrasi selesai: {$table} ({$inserted}/{$total} rows)", 'green');
        }
    }

    private function dbErrorMessage($db): string
    {
        try {
            if (is_object($db) && method_exists($db, 'error')) {
                $err = $db->error();
                if (is_array($err)) {
                    $code = $err['code'] ?? '';
                    $msg  = $err['message'] ?? '';
                    $codeStr = $code !== '' ? "{$code} " : '';
                    return trim($codeStr . $msg) !== '' ? trim($codeStr . $msg) : 'Unknown DB error';
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return 'Unknown DB error';
    }

    /**
     * Convert output of MySQL "SHOW CREATE TABLE" into PostgreSQL CREATE TABLE.
     */
    private function convertMySqlShowCreateTableToPostgres(string $table, string $createSql): string
    {
        // Extract the body inside parentheses
        $createSql = str_replace("`", '"', $createSql);
        if (!preg_match('/\((.*)\)\s*(ENGINE|DEFAULT|COMMENT|COLLATE|CHARSET|ROW_FORMAT|;)/is', $createSql, $m)) {
            // fallback: take between first '(' and last ')'
            $start = strpos($createSql, '(');
            $end = strrpos($createSql, ')');
            $body = ($start !== false && $end !== false && $end > $start) ? substr($createSql, $start + 1, $end - $start - 1) : '';
        } else {
            $body = $m[1] ?? '';
        }

        $body = trim($body);
        $lines = preg_split('/\r?\n/', $body);

        $cols = [];
        $constraints = [];

        foreach ($lines as $line) {
            $line = trim($line);
            $line = rtrim($line, ',');
            if ($line === '') {
                continue;
            }

            if (preg_match('/^PRIMARY\s+KEY\s*\((?<cols>.+)\)$/i', $line, $mm)) {
                $constraints[] = 'PRIMARY KEY (' . str_replace('"', '"', $mm['cols']) . ')';
                continue;
            }

            if (preg_match('/^(KEY|INDEX|UNIQUE\s+KEY|CONSTRAINT)\s+/i', $line)) {
                continue;
            }

            if (!preg_match('/^"(?<name>[^"]+)"\s+(?<type>[a-zA-Z]+)(?<len>\([^\)]*\))?(?<rest>.*)$/', $line, $mm)) {
                continue;
            }

            $name = $mm['name'];
            $type = strtolower($mm['type']);
            $len  = $mm['len'] ?? '';
            $rest = strtolower($mm['rest'] ?? '');

            $pgType = $this->mapMySqlTypeToPostgres($type, $len, $rest);
            $col = '"' . $name . '" ' . $pgType;

            if (str_contains($rest, 'not null')) {
                $col .= ' NOT NULL';
            }

            if (preg_match('/default\s+(?<def>[^\s,]+)/i', $mm['rest'] ?? '', $dm)) {
                $def = trim($dm['def']);
                if (preg_match('/^current_timestamp\(\)$/i', $def)) {
                    $def = 'CURRENT_TIMESTAMP';
                }
                if (strcasecmp($def, 'NULL') !== 0) {
                    $col .= ' DEFAULT ' . $def;
                }
            }

            if (str_contains($rest, 'auto_increment')) {
                $col = '"' . $name . '" ' . $this->autoIncrementType($type, $len, $rest);
                $col .= ' GENERATED BY DEFAULT AS IDENTITY';
                if (str_contains($rest, 'not null')) {
                    $col .= ' NOT NULL';
                }
            }

            $cols[] = $col;
        }

        $all = array_merge($cols, $constraints);
        return 'CREATE TABLE "' . $table . '"' . " (\n  " . implode(",\n  ", $all) . "\n);";
    }

    private function prepareInsertStatement(string $table, array $data): string
    {
        if (empty($data)) {
            return '';
        }

        $columns = array_keys($data[0]);
        $columnList = '"' . implode('", "', $columns) . '"';

        $valuesList = [];
        foreach ($data as $row) {
            $values = [];
            foreach ($columns as $col) {
                $v = $row[$col] ?? null;

                // Common MySQL dump problems for PostgreSQL
                // - invalid date 0000-00-00
                // - empty string in date/timestamp columns
                if (is_string($v)) {
                    $vv = trim($v);
                    if ($vv === '0000-00-00' || $vv === '0000-00-00 00:00:00') {
                        $v = null;
                    }
                }

                $values[] = $this->postgresDb->escape($v);
            }
            $valuesList[] = '(' . implode(', ', $values) . ')';
        }

        $valuesSql = implode(", ", $valuesList);
        return "INSERT INTO \"$table\" ($columnList) VALUES $valuesSql;";
    }

    private function validateMigration(): void
    {
        CLI::write('✅ Validasi data migrasi...', 'green');

        $q = $this->mysqlDb->query("SHOW TABLES");
        if ($q === false) {
            $err = method_exists($this->mysqlDb, 'error') ? $this->mysqlDb->error() : null;
            $msg = is_array($err) ? ($err['message'] ?? 'Unknown MySQL error') : 'Unknown MySQL error';
            CLI::error('❌ Validasi dibatalkan: gagal SHOW TABLES di MySQL temp: ' . $msg);
            return;
        }

        $tables = $q->getResultArray();

        foreach ($tables as $t) {
            $table = reset($t);

            $mysqlCount = $this->mysqlDb->query("SELECT COUNT(*) as count FROM `{$table}`")->getRow()->count ?? 0;
            $pgCount = $this->postgresDb->query("SELECT COUNT(*) as count FROM \"$table\"")->getRow()->count ?? 0;

            if ($mysqlCount != $pgCount) {
                CLI::write("⚠️  Tabel $table: Jumlah baris berbeda (MySQL: $mysqlCount, PostgreSQL: $pgCount)", 'yellow');
            } else {
                CLI::write("✅ Tabel $table: Jumlah baris cocok ($mysqlCount)", 'green');
            }
        }
    }

    private function cleanup()
    {
        CLI::write('🧹 Cleanup temporary database...', 'yellow');

        try {
            $this->mysqlDb->query("DROP DATABASE IF EXISTS sdngu09_temp");
            CLI::write("✅ Cleanup selesai", 'green');
        } catch (\Exception $e) {
            CLI::write("⚠️  Cleanup manual required: DROP DATABASE sdngu09_temp;", 'yellow');
        }
    }
}
