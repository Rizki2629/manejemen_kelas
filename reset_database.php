<?php
/**
 * Database Reset Script for Heroku JawsDB
 * This drops all tables to allow fresh import
 * Run: heroku run "php reset_database.php" --app manajemen-kelas
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

try {
    $pdo = new PDO(
        "mysql:host=$hostname;dbname=$database;charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "Connected!\n\n";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage() . "\n");
}

// Disable FK checks
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
echo "Foreign key checks disabled\n";

// Get all tables
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo "Found " . count($tables) . " tables\n\n";

echo "Dropping all tables...\n";
foreach ($tables as $table) {
    try {
        $pdo->exec("DROP TABLE IF EXISTS `$table`");
        echo "  Dropped: $table\n";
    } catch (PDOException $e) {
        echo "  Failed to drop $table: " . $e->getMessage() . "\n";
    }
}

// Re-enable FK checks
$pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

echo "\n========== RESET COMPLETE ==========\n";

// Verify
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo "Remaining tables: " . count($tables) . "\n";
