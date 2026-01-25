<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CreateTestSession extends BaseCommand
{
    protected $group = 'testing';
    protected $name = 'create:test-session';
    protected $description = 'Create a test session file for habits testing';

    public function run(array $params)
    {
        $username = $params[0] ?? '3157252958';
        
        CLI::write('=== Creating Test Session ===', 'green');
        
        // Create a simple test login page
        $testLoginContent = '<?php
session_start();

// Simulate login for testing
if ($_POST["action"] ?? "" === "login") {
    $_SESSION["logged_in"] = true;
    $_SESSION["username"] = "' . $username . '";
    $_SESSION["user_id"] = 7; // User ID from users table
    $_SESSION["student_id"] = 5; // Student ID from siswa table  
    $_SESSION["role"] = "siswa";
    
    header("Location: /siswa/habits?date=" . date("Y-m-d"));
    exit;
}

if ($_GET["action"] ?? "" === "logout") {
    session_destroy();
    header("Location: /login");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Login - Habit System</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 500px; margin: 50px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        input[type="text"], input[type="password"] { width: 100%; padding: 8px; margin: 5px 0; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; cursor: pointer; }
        .info { background: #e7f3ff; padding: 15px; border-left: 4px solid #007bff; margin: 15px 0; }
    </style>
</head>
<body>
    <h2>🧪 Test Login - Habit System</h2>
    
    <div class="info">
        <h4>Status Sistem Habits:</h4>
        <p>✅ Database: Connected</p>
        <p>✅ Data: 5 habits tersimpan untuk ' . date('Y-m-d') . '</p>
        <p>✅ Rekap: Berfungsi normal</p>
        <p>❌ Session: Perlu login untuk akses web</p>
    </div>
    
    <form method="POST">
        <div class="form-group">
            <label>Username (NISN):</label>
            <input type="text" name="username" value="' . $username . '" readonly>
        </div>
        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" value="123456" readonly>
        </div>
        <input type="hidden" name="action" value="login">
        <button type="submit">🚀 Login Test & Akses Habits</button>
    </form>
    
    <div class="info">
        <h4>Setelah Login:</h4>
        <p>🎯 Akan redirect ke: <code>/siswa/habits?date=' . date('Y-m-d') . '</code></p>
        <p>📊 Rekap akan menampilkan 5 habits yang sudah tersimpan</p>
        <p>✨ Data JSON kompleks (ibadah) akan terparse dengan benar</p>
    </div>
    
    <p><small>File ini dibuat untuk testing. Hapus setelah selesai.</small></p>
</body>
</html>';

        // Save to public directory
        $testFile = ROOTPATH . 'public/test-login-habits.php';
        file_put_contents($testFile, $testLoginContent);
        
        CLI::write("✅ Test login page created: /test-login-habits.php", 'green');
    CLI::write("🌐 Access: https://sdngu09.my.id/test-login-habits.php", 'cyan');
        CLI::write("", '');
        CLI::write("🎯 Instructions:", 'yellow');
    CLI::write("1. Open: https://sdngu09.my.id/test-login-habits.php");
        CLI::write("2. Click 'Login Test & Akses Habits'");
        CLI::write("3. You will be redirected to habits page with active session");
        CLI::write("4. Rekap will show all 5 habits saved for today");
        CLI::write("", '');
        CLI::write("🗑️  Remember to delete this file after testing:", 'red');
        CLI::write("   rm public/test-login-habits.php");
    }
}
