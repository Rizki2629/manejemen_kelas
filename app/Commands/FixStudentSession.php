<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class FixStudentSession extends BaseCommand
{
    protected $group       = 'Fix';
    protected $name        = 'fix:student-session';
    protected $description = 'Fix student session for testing monthly report';

    public function run(array $params)
    {
        $nisn = $params[0] ?? '3157252958'; // Default AFIFAH FITIYA
        
        CLI::write("=== Fixing Student Session ===", 'green');
        CLI::write("Target NISN: {$nisn}", 'cyan');
        
        // Load models
        $studentModel = new \App\Models\StudentModel();
        $userModel = new \App\Models\UserModel();
        
        // Find student
        $student = $studentModel->where('nisn', $nisn)->first();
        if (!$student) {
            CLI::write("❌ Student not found with NISN: {$nisn}", 'red');
            return;
        }
        
        CLI::write("✅ Student found: {$student['nama']} (ID: {$student['id']})", 'green');
        
        // Check if user exists for this student
        $user = $userModel->where('username', $nisn)->first();
        if (!$user) {
            CLI::write("⚠️ User account not found for NISN: {$nisn}", 'yellow');
            CLI::write("Creating user account...", 'cyan');
            
            $userData = [
                'username' => $nisn,
                'password' => password_hash($nisn, PASSWORD_DEFAULT), // Use NISN as default password
                'role' => 'siswa',
                'status' => 'active',
                'name' => $student['nama'],
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $userId = $userModel->insert($userData);
            if ($userId) {
                CLI::write("✅ User account created with ID: {$userId}", 'green');
                $user = $userModel->find($userId);
            } else {
                CLI::write("❌ Failed to create user account", 'red');
                return;
            }
        } else {
            CLI::write("✅ User account found: {$user['username']} (ID: {$user['id']})", 'green');
        }
        
        CLI::write("\n📋 Session Information:", 'yellow');
        CLI::write("Username (NISN): {$nisn}", 'white');
        CLI::write("Student ID: {$student['id']}", 'white');
        CLI::write("User ID: {$user['id']}", 'white');
        CLI::write("Role: siswa", 'white');
        
        CLI::write("\n🔧 Session Setup Instructions:", 'yellow');
        CLI::write("1. Login to system with:", 'white');
        CLI::write("   Username: {$nisn}", 'cyan');
        CLI::write("   Password: {$nisn}", 'cyan');
        
        CLI::write("\n2. Or manually set session in browser console:", 'white');
        CLI::write("   document.cookie = 'ci_session=manual_session_for_testing'", 'cyan');
        
        CLI::write("\n3. Expected session values:", 'white');
        CLI::write("   isLoggedIn: true", 'cyan');
        CLI::write("   username: {$nisn}", 'cyan');
        CLI::write("   student_id: {$student['id']}", 'cyan');
        CLI::write("   user_id: {$user['id']}", 'cyan');
        CLI::write("   role: siswa", 'cyan');
        
        // Test the monthly data with this student
        CLI::write("\n🧪 Testing monthly data for this student:", 'yellow');
        
        $month = '2025-08';
        $startDate = $month . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));
        
        $db = \Config\Database::connect();
        $sql = "
            SELECT COUNT(*) as count, MIN(log_date) as first_date, MAX(log_date) as last_date
            FROM habit_logs 
            WHERE student_id = ? AND log_date BETWEEN ? AND ?
        ";
        
        $query = $db->query($sql, [$student['id'], $startDate, $endDate]);
        $result = $query->getRowArray();
        
        if ($result['count'] > 0) {
            CLI::write("✅ Found {$result['count']} habit records", 'green');
            CLI::write("   Date range: {$result['first_date']} to {$result['last_date']}", 'white');
            
            // Check specifically for 2025-08-21
            $targetCheck = $db->query("
                SELECT COUNT(*) as count 
                FROM habit_logs 
                WHERE student_id = ? AND log_date = '2025-08-21'
            ", [$student['id']])->getRowArray();
            
            if ($targetCheck['count'] > 0) {
                CLI::write("✅ Data exists for 2025-08-21: {$targetCheck['count']} records", 'green');
            } else {
                CLI::write("❌ No data for 2025-08-21", 'red');
            }
        } else {
            CLI::write("❌ No habit records found for this student in {$month}", 'red');
        }
        
        CLI::write("\n✅ Session fix completed!", 'green');
        CLI::write("\nNext steps:", 'yellow');
        CLI::write("1. Login with the credentials above", 'white');
        CLI::write("2. Navigate to monthly report page", 'white');
        CLI::write("3. Check browser console for debugging output", 'white');
        CLI::write("4. If still not working, check session/authentication", 'white');
    }
}
