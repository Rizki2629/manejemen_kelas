<?php

/**
 * Script Alternatif: Import SQL Langsung ke PostgreSQL
 * Gunakan jika MySQL lokal tidak tersedia
 * 
 * Usage: php migrate_direct.php
 */

require 'vendor/autoload.php';

// Load environment
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "========================================\n";
echo "  Direct MySQL → PostgreSQL Migration\n";
echo "========================================\n\n";

// PostgreSQL connection
$pgHost = getenv('database.default.hostname');
$pgPort = getenv('database.default.port') ?: 5432;
$pgDb = getenv('database.default.database');
$pgUser = getenv('database.default.username');
$pgPass = getenv('database.default.password');

echo "📡 Connecting to Railway PostgreSQL...\n";

try {
    $pdo = new PDO(
        "pgsql:host={$pgHost};port={$pgPort};dbname={$pgDb}",
        $pgUser,
        $pgPass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "✅ Connected to PostgreSQL\n\n";
} catch (PDOException $e) {
    die("❌ Connection failed: " . $e->getMessage() . "\n");
}

// Read SQL file
$sqlFile = __DIR__ . '/sdngu09.sql';

if (!file_exists($sqlFile)) {
    die("❌ File not found: {$sqlFile}\n");
}

echo "📄 Reading SQL file...\n";
$sql = file_get_contents($sqlFile);

// Convert MySQL to PostgreSQL syntax
echo "🔧 Converting MySQL syntax to PostgreSQL...\n";

// Remove MySQL-specific commands
$sql = preg_replace('/SET SQL_MODE.*?;/i', '', $sql);
$sql = preg_replace('/START TRANSACTION;/i', '', $sql);
$sql = preg_replace('/SET time_zone.*?;/i', '', $sql);
$sql = preg_replace('/\/\*!40\d{3}.*?\*\//s', '', $sql);

// Convert AUTO_INCREMENT to SERIAL
$sql = preg_replace('/`(\w+)` int\(\d+\) UNSIGNED NOT NULL AUTO_INCREMENT/i', '"$1" SERIAL', $sql);
$sql = preg_replace('/`(\w+)` int\(\d+\) NOT NULL AUTO_INCREMENT/i', '"$1" SERIAL', $sql);

// Convert backticks to double quotes
$sql = str_replace('`', '"', $sql);

// Convert ENGINE and CHARSET
$sql = preg_replace('/\) ENGINE=\w+ DEFAULT CHARSET=\w+;/i', ');', $sql);
$sql = preg_replace('/\) ENGINE=\w+ CHARSET=\w+ COLLATE=\w+;/i', ');', $sql);

// Convert data types
$sql = preg_replace('/datetime\s+DEFAULT\s+NULL/i', 'TIMESTAMP DEFAULT NULL', $sql);
$sql = preg_replace('/datetime\s+NOT\s+NULL/i', 'TIMESTAMP NOT NULL', $sql);
$sql = preg_replace('/TEXT\s+COLLATE\s+\w+/i', 'TEXT', $sql);
$sql = preg_replace('/VARCHAR\((\d+)\)\s+COLLATE\s+\w+/i', 'VARCHAR($1)', $sql);

// Convert ENUM to VARCHAR
$sql = preg_replace('/enum\([^)]+\)/i', 'VARCHAR(50)', $sql);

// Split by statements
$statements = array_filter(
    array_map('trim', explode(';', $sql)),
    function ($stmt) {
        return !empty($stmt) &&
            !str_starts_with($stmt, '--') &&
            !str_starts_with($stmt, '/*');
    }
);

$total = count($statements);
$success = 0;
$errors = 0;

echo "📊 Total statements: {$total}\n";
echo "🚀 Executing statements...\n\n";

foreach ($statements as $i => $statement) {
    $statement = trim($statement);

    if (empty($statement)) {
        continue;
    }

    try {
        $pdo->exec($statement);
        $success++;

        if ($i % 100 == 0) {
            $percent = round(($i / $total) * 100, 1);
            echo "\rProgress: {$percent}% ({$i}/{$total})";
        }
    } catch (PDOException $e) {
        $errors++;

        // Log error tapi tetap lanjut
        if (stripos($statement, 'CREATE TABLE') !== false) {
            preg_match('/CREATE TABLE "?(\w+)"?/i', $statement, $matches);
            $table = $matches[1] ?? 'unknown';
            echo "\n⚠️  Error creating table {$table}: " . $e->getMessage() . "\n";
        }
    }
}

echo "\rProgress: 100% ({$total}/{$total})\n\n";

echo "========================================\n";
echo "✅ Migration Complete!\n";
echo "========================================\n";
echo "Success: {$success}\n";
echo "Errors: {$errors}\n";
echo "Total: {$total}\n";

// Validate tables
echo "\n📊 Validating tables...\n";

$result = $pdo->query("
    SELECT table_name 
    FROM information_schema.tables 
    WHERE table_schema='public' 
    ORDER BY table_name
");

$tables = $result->fetchAll(PDO::FETCH_COLUMN);

echo "✅ Tables created: " . count($tables) . "\n\n";

foreach ($tables as $table) {
    $count = $pdo->query("SELECT COUNT(*) FROM \"{$table}\"")->fetchColumn();
    echo "   {$table}: {$count} rows\n";
}

echo "\n✅ Done! Database ready to use.\n";
