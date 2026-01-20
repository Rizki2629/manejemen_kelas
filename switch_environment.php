<?php

/**
 * Environment Switcher - XAMPP vs Hosting
 * 
 * Script untuk memudahkan perpindahan konfigurasi antara:
 * - XAMPP (development lokal)
 * - Hosting sdngu09.my.id (production)
 */

class EnvironmentSwitcher 
{
    private $envFile = '.env';
    private $xamppConfig = [
        'CI_ENVIRONMENT' => 'development',
        'app.baseURL' => 'http://localhost:8080/',
        'database.default.hostname' => 'localhost',
        'database.default.database' => 'sdngu09',
        'database.default.username' => 'root',
        'database.default.password' => '',
        'database.default.port' => '3306'
    ];
    
    private $hostingConfig = [
        'CI_ENVIRONMENT' => 'production',
    'app.baseURL' => 'https://sdngu09.my.id/',
        'database.default.hostname' => 'localhost',
        'database.default.database' => 'sdngu09_manajemen_kelas',
        'database.default.username' => 'sdngu09_user',
        'database.default.password' => 'your_hosting_password',
        'database.default.port' => '3306'
    ];
    
    public function getCurrentConfig()
    {
        if (!file_exists($this->envFile)) {
            return false;
        }
        
        $content = file_get_contents($this->envFile);
        
        if (strpos($content, 'localhost:8080') !== false && strpos($content, 'root') !== false) {
            return 'xampp';
        } elseif (strpos($content, 'sdngu09.my.id') !== false) {
            return 'hosting';
        }
        
        return 'unknown';
    }
    
    public function switchToXampp()
    {
        echo "🔄 Switching to XAMPP configuration...\n";
        $this->updateEnvFile($this->xamppConfig);
        echo "✅ Switched to XAMPP (local development)\n";
        echo "🌐 Base URL: http://localhost:8080/\n";
        echo "💾 Database: sdngu09 (XAMPP MySQL)\n";
    }
    
    public function switchToHosting()
    {
        echo "🔄 Switching to Hosting configuration...\n";
        echo "⚠️  Remember to update hosting database credentials!\n";
        $this->updateEnvFile($this->hostingConfig);
        echo "✅ Switched to Hosting (production)\n";
    echo "🌐 Base URL: https://sdngu09.my.id/\n";
        echo "💾 Database: sdngu09_manajemen_kelas (hosting)\n";
        echo "📝 Update password in .env file!\n";
    }
    
    private function updateEnvFile($config)
    {
        $envContent = file_get_contents($this->envFile);
        
        foreach ($config as $key => $value) {
            $pattern = "/^{$key}\s*=.*$/m";
            $replacement = "{$key} = {$value}";
            
            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                // If key doesn't exist, add it
                $envContent .= "\n{$replacement}\n";
            }
        }
        
        file_put_contents($this->envFile, $envContent);
    }
    
    public function testConnection()
    {
        $env = parse_ini_file($this->envFile);
        
        if (!$env) {
            echo "❌ Error: Cannot read .env file\n";
            return false;
        }
        
        $hostname = $env['database.default.hostname'] ?? 'localhost';
        $database = $env['database.default.database'] ?? '';
        $username = $env['database.default.username'] ?? '';
        $password = $env['database.default.password'] ?? '';
        $port = $env['database.default.port'] ?? 3306;
        
        try {
            $dsn = "mysql:host=$hostname;port=$port;dbname=$database;charset=utf8mb4";
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            
            echo "✅ Database connection successful!\n";
            return true;
            
        } catch (PDOException $e) {
            echo "❌ Database connection failed: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    public function showStatus()
    {
        $current = $this->getCurrentConfig();
        
        echo "📊 CURRENT CONFIGURATION STATUS\n";
        echo "==============================\n\n";
        
        switch ($current) {
            case 'xampp':
                echo "🔧 Mode: XAMPP Development\n";
                echo "🌐 URL: http://localhost:8080/\n";
                echo "💾 Database: sdngu09 (local)\n";
                echo "👤 User: root (no password)\n";
                break;
                
            case 'hosting':
                echo "🚀 Mode: Hosting Production\n";
                echo "🌐 URL: https://sdngu09.my.id/\n";
                echo "💾 Database: sdngu09_manajemen_kelas\n";
                echo "👤 User: sdngu09_user\n";
                break;
                
            default:
                echo "❓ Mode: Unknown/Custom\n";
                break;
        }
        
        echo "\n";
        $this->testConnection();
    }
}

// Main execution
echo "🔧 ENVIRONMENT SWITCHER - XAMPP vs HOSTING\n";
echo "==========================================\n\n";

$switcher = new EnvironmentSwitcher();

if ($argc < 2) {
    echo "Usage: php switch_environment.php [xampp|hosting|status]\n\n";
    echo "Commands:\n";
    echo "  xampp    - Switch to XAMPP local development\n";
    echo "  hosting  - Switch to hosting production\n";
    echo "  status   - Show current configuration\n\n";
    
    $switcher->showStatus();
    exit;
}

$command = $argv[1];

switch ($command) {
    case 'xampp':
        $switcher->switchToXampp();
        echo "\n";
        $switcher->testConnection();
        break;
        
    case 'hosting':
        $switcher->switchToHosting();
        echo "\n💡 Don't forget to update the database password in .env!\n";
        break;
        
    case 'status':
        $switcher->showStatus();
        break;
        
    default:
        echo "❌ Unknown command: $command\n";
        echo "Use: xampp, hosting, or status\n";
        break;
}

?>
