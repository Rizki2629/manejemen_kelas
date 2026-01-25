<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CreateStudentUser extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'create:student-user';
    protected $description = 'Create user account for student';

    public function run(array $params)
    {
        $nisn = $params[0] ?? '3157252958';
        
        CLI::write("Creating user account for student with NISN: {$nisn}", 'green');
        
        $db = \Config\Database::connect();
        
        // Check if user already exists
        $existingUser = $db->query("SELECT * FROM users WHERE username = ?", [$nisn])->getRowArray();
        
        if ($existingUser) {
            CLI::write("User already exists:", 'yellow');
            CLI::write("  ID: {$existingUser['id']}");
            CLI::write("  Username: {$existingUser['username']}");
            CLI::write("  Name: {$existingUser['nama']}");
            CLI::write("  Role: {$existingUser['role']}");
            return;
        }
        
        // Get student data
        $student = $db->query("SELECT * FROM siswa WHERE nisn = ?", [$nisn])->getRowArray();
        
        if (!$student) {
            CLI::write("Student not found with NISN: {$nisn}", 'red');
            return;
        }
        
        // Create user account
        $userData = [
            'username' => $nisn,
            'password' => password_hash('password', PASSWORD_DEFAULT), // Default password
            'nama' => $student['nama'],
            'role' => 'siswa',
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $result = $db->table('users')->insert($userData);
        
        if ($result) {
            $userId = $db->insertID();
            CLI::write("User created successfully:", 'green');
            CLI::write("  User ID: {$userId}");
            CLI::write("  Username: {$nisn}");
            CLI::write("  Name: {$student['nama']}");
            CLI::write("  Password: password (default)");
            CLI::write("  Role: siswa");
        } else {
            CLI::write("Failed to create user", 'red');
        }
    }
}
