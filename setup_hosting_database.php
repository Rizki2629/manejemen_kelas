<?php

/**
 * Database Setup Helper for Hosting sdngu09.my.id
 * 
 * This script helps you set up the database connection for your hosting environment.
 * Run this script AFTER you have:
 * 1. Created a database in your hosting cPanel/control panel
 * 2. Created a database user with proper permissions
 * 3. Updated the .env file with correct credentials
 */

// Test database connection
function testDatabaseConnection() {
    // Load environment variables
    $env = parse_ini_file('.env');
    
    if (!$env) {
        die("❌ Error: Cannot read .env file\n");
    }
    
    $hostname = $env['database.default.hostname'] ?? 'localhost';
    $database = $env['database.default.database'] ?? '';
    $username = $env['database.default.username'] ?? '';
    $password = $env['database.default.password'] ?? '';
    $port = $env['database.default.port'] ?? 3306;
    
    echo "🔄 Testing database connection...\n";
    echo "Host: $hostname\n";
    echo "Database: $database\n";
    echo "Username: $username\n";
    echo "Port: $port\n\n";
    
    try {
        $dsn = "mysql:host=$hostname;port=$port;charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        
        echo "✅ Connection to MySQL server successful!\n";
        
        // Test database access
        $dsn = "mysql:host=$hostname;port=$port;dbname=$database;charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        
        echo "✅ Database '$database' accessible!\n";
        
        // Check if tables exist
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (empty($tables)) {
            echo "⚠️  Database is empty. You need to run migrations.\n";
            echo "💡 Run: php spark migrate\n";
            echo "💡 Run: php spark db:seed DemoSeeder\n";
        } else {
            echo "✅ Found " . count($tables) . " tables in database:\n";
            foreach ($tables as $table) {
                echo "   - $table\n";
            }
        }
        
        return true;
        
    } catch (PDOException $e) {
        echo "❌ Database connection failed!\n";
        echo "Error: " . $e->getMessage() . "\n";
        
        if (strpos($e->getMessage(), 'Access denied') !== false) {
            echo "\n💡 Troubleshooting tips:\n";
            echo "1. Check username and password in .env file\n";
            echo "2. Make sure database user has proper permissions\n";
            echo "3. Check if remote connections are allowed\n";
        } elseif (strpos($e->getMessage(), 'Unknown database') !== false) {
            echo "\n💡 Troubleshooting tips:\n";
            echo "1. Create the database in your hosting control panel\n";
            echo "2. Check database name in .env file\n";
        } elseif (strpos($e->getMessage(), "Can't connect") !== false) {
            echo "\n💡 Troubleshooting tips:\n";
            echo "1. Check hostname in .env file\n";
            echo "2. Check if MySQL service is running\n";
            echo "3. Check firewall settings\n";
        }
        
        return false;
    }
}

// Display hosting setup instructions
function displaySetupInstructions() {
    echo "🚀 HOSTING DATABASE SETUP INSTRUCTIONS\n";
    echo "=====================================\n\n";
    
    echo "📋 Steps to set up database on sdngu09.my.id:\n\n";
    
    echo "1. Login to your hosting control panel (cPanel/DirectAdmin/etc.)\n";
    echo "2. Go to 'MySQL Databases' or 'Database' section\n";
    echo "3. Create a new database (e.g., 'manajemen_kelas')\n";
    echo "4. Create a database user with password\n";
    echo "5. Add the user to the database with ALL PRIVILEGES\n";
    echo "6. Note down the credentials:\n";
    echo "   - Database Name: [prefix_]manajemen_kelas\n";
    echo "   - Username: [prefix_]username\n";
    echo "   - Password: your_secure_password\n";
    echo "   - Host: usually 'localhost'\n\n";
    
    echo "7. Update your .env file with these credentials\n";
    echo "8. Upload your project files to hosting\n";
    echo "9. Run database migrations (if SSH access available):\n";
    echo "   php spark migrate\n";
    echo "   php spark db:seed DemoSeeder\n\n";
    
    echo "📝 Common hosting database formats:\n";
    echo "   - Database: username_dbname\n";
    echo "   - User: username_dbuser\n";
    echo "   - Host: localhost (or provided hostname)\n\n";
}

// Main execution
echo "🔧 MANAJEMEN KELAS - HOSTING DATABASE SETUP\n";
echo "==========================================\n\n";

if (!file_exists('.env')) {
    echo "❌ Error: .env file not found!\n";
    echo "💡 Copy .env.example to .env and configure database settings.\n";
    exit(1);
}

displaySetupInstructions();

if (testDatabaseConnection()) {
    echo "\n🎉 Database connection successful! Your application is ready to use the hosting database.\n";
} else {
    echo "\n⚠️  Please fix the database connection issues and run this script again.\n";
}

echo "\n📚 Additional Notes:\n";
echo "- Make sure your hosting supports PHP " . PHP_VERSION . "\n";
echo "- Ensure MySQL version is compatible (5.7+ recommended)\n";
echo "- Check if your hosting allows external database connections\n";
echo "- Some hosting providers require whitelisting IP addresses\n";

?>
