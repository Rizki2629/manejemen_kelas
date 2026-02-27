<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Migrasi users dari MySQL lokal ke PostgreSQL Railway
 * Jalankan di SERVER (bukan lokal), karena Railway hanya bisa diakses dari server.
 * 
 * Usage: php spark migrate:users-from-mysql [--mysql-host=127.0.0.1] [--mysql-port=3306] [--mysql-db=sdngu09] [--mysql-user=root] [--mysql-pass=]
 */
class MigrateUsersFromMySQL extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'migrate:users-from-mysql';
    protected $description = 'Migrasi users & walikelas dari MySQL lama ke PostgreSQL Railway (jalankan di server)';
    protected $usage       = 'migrate:users-from-mysql [options]';
    protected $options     = [
        '--mysql-host' => 'Host MySQL (default: 127.0.0.1)',
        '--mysql-port' => 'Port MySQL (default: 3306)',
        '--mysql-db'   => 'Nama database MySQL (default: sdngu09)',
        '--mysql-user' => 'Username MySQL (default: root)',
        '--mysql-pass' => 'Password MySQL (default: kosong)',
        '--dry-run'    => 'Hanya cek, tidak insert data',
    ];

    public function run(array $params)
    {
        CLI::write('============================================', 'green');
        CLI::write('   Migrasi Users MySQL → PostgreSQL Railway', 'green');
        CLI::write('============================================', 'green');
        CLI::newLine();

        $argv = $_SERVER['argv'] ?? [];
        $getArg = function ($key, $default) use ($argv, $params) {
            foreach ($argv as $a) {
                if (str_starts_with($a, "--$key=")) {
                    return substr($a, strlen("--$key=") + 1);
                }
            }
            return $default;
        };

        $mysqlHost = $getArg('mysql-host', '127.0.0.1');
        $mysqlPort = $getArg('mysql-port', '3306');
        $mysqlDb   = $getArg('mysql-db',   'sdngu09');
        $mysqlUser = $getArg('mysql-user', 'root');
        $mysqlPass = $getArg('mysql-pass', '');
        $dryRun    = in_array('--dry-run', $argv);

        if ($dryRun) {
            CLI::write('⚠️  DRY RUN mode - tidak ada data yang diinsert', 'yellow');
            CLI::newLine();
        }

        // --- Koneksi MySQL ---
        CLI::write("📡 Koneksi ke MySQL $mysqlHost:$mysqlPort/$mysqlDb...");
        try {
            $mysql = new \PDO(
                "mysql:host=$mysqlHost;port=$mysqlPort;dbname=$mysqlDb;charset=utf8mb4",
                $mysqlUser,
                $mysqlPass,
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );
            CLI::write('✅ MySQL terhubung', 'green');
        } catch (\Exception $e) {
            // Try to detect database automatically
            CLI::write("⚠️  Database '$mysqlDb' tidak ditemukan, coba deteksi otomatis...", 'yellow');
            try {
                $mysqlNoDb = new \PDO(
                    "mysql:host=$mysqlHost;port=$mysqlPort;charset=utf8mb4",
                    $mysqlUser,
                    $mysqlPass,
                    [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
                );
                $allDbs = $mysqlNoDb->query("SHOW DATABASES")->fetchAll(\PDO::FETCH_COLUMN);
                $candidates = array_filter($allDbs, fn($d) => !in_array($d, ['information_schema','mysql','performance_schema','sys']));
                CLI::write("Database tersedia: " . implode(', ', $candidates), 'cyan');

                // Try to find one with 'users' table
                foreach ($candidates as $candidate) {
                    try {
                        $mysqlNoDb->exec("USE `$candidate`");
                        $tables = $mysqlNoDb->query("SHOW TABLES LIKE 'users'")->fetchAll(\PDO::FETCH_COLUMN);
                        if (!empty($tables)) {
                            $mysqlDb = $candidate;
                            $mysql = $mysqlNoDb;
                            CLI::write("✅ Database ditemukan: $mysqlDb", 'green');
                            break;
                        }
                    } catch (\Exception $ie) {}
                }
                if (!isset($mysql)) {
                    CLI::error("Tidak bisa menemukan database dengan tabel 'users'");
                    return;
                }
            } catch (\Exception $e2) {
                CLI::error("Gagal konek MySQL: " . $e->getMessage());
                return;
            }
        }

        // --- Koneksi PostgreSQL ---
        CLI::write("📡 Koneksi ke PostgreSQL Railway...");
        try {
            $pg = \Config\Database::connect();
            $pg->query("SELECT 1"); // test connection
            CLI::write('✅ PostgreSQL terhubung', 'green');
        } catch (\Exception $e) {
            CLI::error("Gagal konek PostgreSQL: " . $e->getMessage());
            return;
        }

        CLI::newLine();

        // --- Migrasi tabel walikelas ---
        CLI::write('--- [1/2] Migrasi tabel walikelas ---', 'cyan');
        try {
            $wkRows = $mysql->query("SELECT * FROM walikelas ORDER BY id")->fetchAll(\PDO::FETCH_ASSOC);
            CLI::write("Total walikelas di MySQL: " . count($wkRows));
            $wkInserted = 0; $wkSkipped = 0;

            foreach ($wkRows as $wk) {
                // Cek sudah ada belum di PostgreSQL
                $exist = $pg->query("SELECT id FROM walikelas WHERE id = {$wk['id']}")->getRowArray();
                if ($exist) {
                    if (!$dryRun) {
                        $pg->query("UPDATE walikelas SET nama='" . $pg->escapeString($wk['nama']) . "', kelas='" . $pg->escapeString($wk['kelas']) . "', nip='" . $pg->escapeString($wk['nip'] ?? '') . "' WHERE id={$wk['id']}");
                    }
                    $wkSkipped++;
                } else {
                    if (!$dryRun) {
                        $pg->query("INSERT INTO walikelas (id, nama, kelas, nip, created_at, updated_at) VALUES ({$wk['id']}, '" . $pg->escapeString($wk['nama']) . "', '" . $pg->escapeString($wk['kelas']) . "', '" . $pg->escapeString($wk['nip'] ?? '') . "', NOW(), NOW())");
                    }
                    $wkInserted++;
                }
            }
            CLI::write("  Inserted: $wkInserted | Updated: $wkSkipped", 'green');
            if (!$dryRun && !empty($wkRows)) {
                $pg->query("SELECT setval('walikelas_id_seq', (SELECT COALESCE(MAX(id),1) FROM walikelas))");
            }
        } catch (\Exception $e) {
            CLI::write("⚠️  Migrasi walikelas error: " . $e->getMessage(), 'yellow');
        }

        CLI::newLine();

        // --- Migrasi tabel users ---
        CLI::write('--- [2/2] Migrasi tabel users ---', 'cyan');
        $usersRows = $mysql->query("SELECT * FROM users ORDER BY id")->fetchAll(\PDO::FETCH_ASSOC);
        CLI::write("Total users di MySQL: " . count($usersRows));

        $inserted = 0; $updated = 0; $errors = 0;

        foreach ($usersRows as $u) {
            try {
                $id           = (int)$u['id'];
                $username     = $pg->escapeString($u['username'] ?? '');
                $password     = $pg->escapeString($u['password'] ?? '');
                $nama         = $pg->escapeString($u['nama'] ?? '');
                $email        = $u['email'] ? "'" . $pg->escapeString($u['email']) . "'" : 'NULL';
                $role         = $pg->escapeString($u['role'] ?? 'walikelas');
                $walikelas_id = $u['walikelas_id'] ? (int)$u['walikelas_id'] : 'NULL';
                $is_active    = (int)($u['is_active'] ?? 1);
                $last_login   = $u['last_login'] ? "'" . $pg->escapeString($u['last_login']) . "'" : 'NULL';
                $created_at   = $u['created_at'] ?: date('Y-m-d H:i:s');
                $updated_at   = $u['updated_at'] ?: date('Y-m-d H:i:s');

                if (!$dryRun) {
                    $exist = $pg->query("SELECT id FROM users WHERE id = $id")->getRowArray();
                    if ($exist) {
                        $pg->query("UPDATE users SET username='$username', password='$password', nama='$nama', email=$email, role='$role', walikelas_id=$walikelas_id, is_active=$is_active WHERE id=$id");
                        $updated++;
                    } else {
                        $pg->query("INSERT INTO users (id, username, password, nama, email, role, walikelas_id, is_active, last_login, created_at, updated_at)
                            VALUES ($id, '$username', '$password', '$nama', $email, '$role', $walikelas_id, $is_active, $last_login, '$created_at', '$updated_at')");
                        $inserted++;
                    }
                } else {
                    $inserted++;
                }
            } catch (\Exception $e) {
                CLI::write("  ⚠️  User ID {$u['id']} ({$u['username']}): " . $e->getMessage(), 'red');
                $errors++;
            }
        }

        if (!$dryRun) {
            $pg->query("SELECT setval('users_id_seq', (SELECT COALESCE(MAX(id),1) FROM users))");
        }

        CLI::newLine();
        CLI::write("✅ SELESAI", 'green');
        CLI::write("   Inserted : $inserted", 'green');
        CLI::write("   Updated  : $updated", 'cyan');
        CLI::write("   Errors   : $errors", $errors > 0 ? 'red' : 'green');
        CLI::newLine();

        // Cek user muhammad.rizki.pratama
        if (!$dryRun) {
            $check = $pg->query("SELECT id, username, nama, role, is_active FROM users WHERE username = 'muhammad.rizki.pratama'")->getRowArray();
            if ($check) {
                CLI::write("✅ User 'muhammad.rizki.pratama' berhasil ada di Railway:", 'green');
                CLI::write("   ID: {$check['id']} | Role: {$check['role']} | Aktif: {$check['is_active']}");
            } else {
                CLI::write("⚠️  User 'muhammad.rizki.pratama' masih tidak ditemukan di Railway", 'yellow');
            }
        }
    }
}
