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

// Remove database-specific commands that might conflict
$sql = preg_replace('/^USE\s+`[^`]+`\s*;/mi', '', $sql);
$sql = preg_replace('/^CREATE DATABASE.*$/mi', '', $sql);

// Split by delimiter (handle both ; and custom delimiters)
echo "Executing SQL statements...\n";

$statements = [];
$currentStatement = '';
$delimiter = ';';

$lines = explode("\n", $sql);
$totalLines = count($lines);

foreach ($lines as $i => $line) {
    // Check for delimiter change
    if (preg_match('/^DELIMITER\s+(\S+)/i', trim($line), $matches)) {
        $delimiter = $matches[1];
        continue;
    }
    
    $currentStatement .= $line . "\n";
    
    // Check if statement is complete
    if (str_ends_with(trim($line), $delimiter)) {
        $stmt = trim($currentStatement);
        $stmt = rtrim($stmt, $delimiter);
        
        if (!empty($stmt) && !preg_match('/^--/', $stmt) && !preg_match('/^\/\*/', $stmt)) {
            $statements[] = $stmt;
        }
        $currentStatement = '';
    }
}

$total = count($statements);
echo "Found $total statements to execute\n\n";

$success = 0;
$errors = 0;
$errorMessages = [];

foreach ($statements as $i => $stmt) {
    // Skip comments and empty statements
    $trimmed = trim($stmt);
    if (empty($trimmed) || strpos($trimmed, '--') === 0 || strpos($trimmed, '/*') === 0) {
        continue;
    }
    
    try {
        $pdo->exec($stmt);
        $success++;
        
        // Progress indicator every 100 statements
        if ($success % 100 === 0) {
            echo "Progress: $success / $total statements executed\n";
        }
    } catch (PDOException $e) {
        $errors++;
        $shortStmt = substr($stmt, 0, 100);
        $errorMessages[] = "Error in statement: $shortStmt... - " . $e->getMessage();
        
        // Continue on error (some errors like "table already exists" are OK)
        if ($errors <= 5) {
            echo "Warning: " . $e->getMessage() . "\n";
        }
    }
}

echo "\n========== IMPORT COMPLETE ==========\n";
echo "Successful: $success\n";
echo "Errors: $errors\n";

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
