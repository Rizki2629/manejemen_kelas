<?php
/**
 * Database Import Script for Heroku JawsDB
 * Run this via: heroku run "php import_database.php" --app manajemen-kelas
 */

// Parse JawsDB URL
$url = getenv('JAWSDB_URL');
if (!$url) {
    // Fallback for local testing
    $url = 'mysql://lbm708ryjh6kmpo0:fehkiluv3nlvrqs7@d6vscs19jtah8iwb.cbetxkdyhwsb.us-east-1.rds.amazonaws.com:3306/itg75cht8gxu0lpx';
}

$dbparts = parse_url($url);

$hostname = $dbparts['host'];
$username = $dbparts['user'];
$password = $dbparts['pass'];
$database = ltrim($dbparts['path'], '/');

echo "Connecting to database...\n";
echo "Host: $hostname\n";
echo "Database: $database\n";

try {
    $pdo = new PDO(
        "mysql:host=$hostname;dbname=$database;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_LOCAL_INFILE => true,
        ]
    );
    echo "Connected successfully!\n\n";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage() . "\n");
}

// Read SQL file
$sqlFile = __DIR__ . '/sdngu09_.sql';
if (!file_exists($sqlFile)) {
    die("SQL file not found: $sqlFile\n");
}

echo "Reading SQL file...\n";
$sql = file_get_contents($sqlFile);
echo "File size: " . number_format(strlen($sql)) . " bytes\n\n";

// Disable foreign key checks
echo "Disabling foreign key checks...\n";
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
$pdo->exec("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO'");

// Remove database-specific commands
$sql = preg_replace('/^USE\s+`[^`]+`\s*;/mi', '', $sql);
$sql = preg_replace('/^CREATE DATABASE.*$/mi', '', $sql);

// Better SQL statement splitting that handles multi-line INSERTs
echo "Parsing SQL statements...\n";

$statements = [];
$currentStmt = '';
$inString = false;
$stringChar = '';
$escaped = false;

for ($i = 0; $i < strlen($sql); $i++) {
    $char = $sql[$i];
    $currentStmt .= $char;
    
    if ($escaped) {
        $escaped = false;
        continue;
    }
    
    if ($char === '\\') {
        $escaped = true;
        continue;
    }
    
    if (!$inString && ($char === "'" || $char === '"')) {
        $inString = true;
        $stringChar = $char;
        continue;
    }
    
    if ($inString && $char === $stringChar) {
        $inString = false;
        continue;
    }
    
    if (!$inString && $char === ';') {
        $stmt = trim($currentStmt);
        if (!empty($stmt) && $stmt !== ';') {
            // Skip comments-only statements
            $cleanStmt = preg_replace('/--.*$/m', '', $stmt);
            $cleanStmt = preg_replace('/\/\*.*?\*\//s', '', $cleanStmt);
            $cleanStmt = trim($cleanStmt);
            if (!empty($cleanStmt) && $cleanStmt !== ';') {
                $statements[] = $stmt;
            }
        }
        $currentStmt = '';
    }
}

$total = count($statements);
echo "Found $total statements to execute\n\n";

$success = 0;
$errors = 0;
$errorMessages = [];

foreach ($statements as $i => $stmt) {
    // Skip SET and comment lines
    $trimmed = trim($stmt);
    if (empty($trimmed) || 
        preg_match('/^\/\*!\d+/', $trimmed) ||
        preg_match('/^--/', $trimmed)) {
        continue;
    }
    
    try {
        $pdo->exec($stmt);
        $success++;
        
        // Progress every 50 statements
        if ($success % 50 === 0) {
            echo "Progress: $success statements executed\n";
        }
    } catch (PDOException $e) {
        $errors++;
        $shortStmt = substr(preg_replace('/\s+/', ' ', $stmt), 0, 80);
        $errorMessages[] = "[$shortStmt...] - " . $e->getMessage();
        
        if ($errors <= 10) {
            echo "Warning: " . $e->getMessage() . "\n";
        }
    }
}

echo "\n========== IMPORT COMPLETE ==========\n";
echo "Successful: $success\n";
echo "Errors: $errors\n";

// Re-enable foreign key checks  
$pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
echo "Foreign key checks re-enabled\n";

if ($errors > 0 && $errors <= 10) {
    echo "\nError details:\n";
    foreach ($errorMessages as $msg) {
        echo "- $msg\n";
    }
}

// Verify tables
echo "\n========== VERIFYING TABLES ==========\n";
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo "Total tables: " . count($tables) . "\n";

foreach ($tables as $table) {
    $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
    echo "- $table: $count rows\n";
}

echo "\nDone!\n";
