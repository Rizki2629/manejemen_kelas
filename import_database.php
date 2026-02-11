<?php
/**
 * Database Import Script for Heroku JawsDB
 * Run this via: heroku run "php import_database.php" --app manajemen-kelas
 */

// Parse JawsDB URL
$url = getenv('JAWSDB_URL');
if (!$url) {
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

$mysqli = new mysqli($hostname, $username, $password, $database);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error . "\n");
}
$mysqli->set_charset('utf8mb4');
echo "Connected successfully!\n\n";

// Read SQL file
$sqlFile = __DIR__ . '/sdngu09_.sql';
if (!file_exists($sqlFile)) {
    die("SQL file not found: $sqlFile\n");
}

echo "Reading SQL file...\n";
$sql = file_get_contents($sqlFile);
echo "File size: " . number_format(strlen($sql)) . " bytes\n\n";

// Disable FK checks and prepare SQL
echo "Preparing SQL...\n";
$sql = "SET FOREIGN_KEY_CHECKS = 0;\nSET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n" . $sql . "\nSET FOREIGN_KEY_CHECKS = 1;";

// Remove USE database commands (we're already connected)
$sql = preg_replace('/^USE\s+`[^`]+`\s*;/mi', '', $sql);
$sql = preg_replace('/^CREATE DATABASE.*$/mi', '', $sql);

echo "Executing SQL using mysqli_multi_query...\n";

$success = 0;
$errors = 0;
$errorMessages = [];

if ($mysqli->multi_query($sql)) {
    do {
        // Store result to free connection for next query
        if ($result = $mysqli->store_result()) {
            $result->free();
        }
        $success++;
        
        if ($success % 20 === 0) {
            echo "Progress: $success queries processed\n";
        }
    } while ($mysqli->more_results() && $mysqli->next_result());
    
    // Check for errors in the last query
    if ($mysqli->errno) {
        $errors++;
        $errorMessages[] = "MySQL Error: " . $mysqli->error;
    }
} else {
    $errors++;
    $errorMessages[] = "Initial query failed: " . $mysqli->error;
}

echo "\n========== IMPORT COMPLETE ==========\n";
echo "Queries processed: $success\n";
echo "Errors: $errors\n";

if ($errors > 0) {
    echo "\nError details:\n";
    foreach ($errorMessages as $msg) {
        echo "- $msg\n";
    }
}

// Verify tables
echo "\n========== VERIFYING TABLES ==========\n";
$result = $mysqli->query("SHOW TABLES");
$tables = [];
while ($row = $result->fetch_row()) {
    $tables[] = $row[0];
}
echo "Total tables: " . count($tables) . "\n\n";

foreach ($tables as $table) {
    $countResult = $mysqli->query("SELECT COUNT(*) FROM `$table`");
    $count = $countResult->fetch_row()[0];
    echo "- $table: $count rows\n";
}

$mysqli->close();
echo "\nDone!\n";
