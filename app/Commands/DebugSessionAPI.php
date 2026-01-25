<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class DebugSessionAPI extends BaseCommand
{
    protected $group       = 'Debug';
    protected $name        = 'debug:session-api';
    protected $description = 'Debug session issues with monthly API';

    public function run(array $params)
    {
        CLI::write("=== Debugging Session API Issue ===", 'green');
        
        // Create a fake session for testing
        $studentId = 5; // AFIFAH FITIYA
        $username = '3157252958'; // NISN
        
        CLI::write("Setting up test session:", 'cyan');
        CLI::write("  student_id: {$studentId}", 'white');
        CLI::write("  username: {$username}", 'white');
        
        // Load models
        $studentModel = new \App\Models\StudentModel();
        
        // Test resolveStudentId logic
        CLI::write("\n1. Testing student resolution:", 'yellow');
        
        // Test by NISN
        $student = $studentModel->where('nisn', $username)->first();
        if ($student) {
            CLI::write("✅ Found student by NISN: {$student['nama']} (ID: {$student['id']})", 'green');
        } else {
            CLI::write("❌ Student not found by NISN", 'red');
        }
        
        // Test by NIS
        $student2 = $studentModel->where('nis', $username)->first();
        if ($student2) {
            CLI::write("✅ Found student by NIS: {$student2['nama']} (ID: {$student2['id']})", 'green');
        } else {
            CLI::write("❌ Student not found by NIS", 'red');
        }
        
        CLI::write("\n2. Testing monthlyData controller logic:", 'yellow');
        
        // Simulate the controller monthlyData method
        $month = '2025-08';
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            CLI::write("❌ Invalid month format", 'red');
            return;
        }
        
        $resolvedStudentId = $student['id'] ?? null;
        if (!$resolvedStudentId) {
            CLI::write("❌ Student ID not resolved", 'red');
            return;
        }
        
        CLI::write("✅ Student ID resolved: {$resolvedStudentId}", 'green');
        
        // Test the query
        $startDate = $month . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));
        
        $db = \Config\Database::connect();
        $sql = "
            SELECT 
                hl.log_date as log_date,
                hl.habit_id,
                hl.value_bool as completed,
                hl.value_time as time,
                hl.value_number as duration,
                hl.notes,
                h.name as habit_name
            FROM habit_logs hl
            JOIN habits h ON h.id = hl.habit_id
            WHERE hl.student_id = ? 
                AND hl.log_date BETWEEN ? AND ?
            ORDER BY hl.log_date ASC, hl.habit_id ASC
        ";

        $query = $db->query($sql, [$resolvedStudentId, $startDate, $endDate]);
        $results = $query->getResultArray();
        
        CLI::write("Query results: " . count($results) . " records", 'cyan');
        
        if (count($results) > 0) {
            CLI::write("✅ Data found for student", 'green');
            
            // Sample some results
            CLI::write("\nSample results:", 'yellow');
            foreach (array_slice($results, 0, 3) as $row) {
                CLI::write("  Date: {$row['log_date']}, Habit: {$row['habit_name']}, Completed: {$row['completed']}", 'white');
            }
            
            // Check target date
            $targetDate = '2025-08-21';
            $targetRecords = array_filter($results, function($row) use ($targetDate) {
                return $row['log_date'] === $targetDate;
            });
            
            CLI::write("\nRecords for {$targetDate}: " . count($targetRecords), 'cyan');
            
        } else {
            CLI::write("❌ No data found", 'red');
        }
        
        CLI::write("\n3. Potential Issues:", 'yellow');
        CLI::write("- Check if session is properly set when accessing monthly report page", 'white');
        CLI::write("- Check if student is properly logged in", 'white');
        CLI::write("- Check if resolveStudentId() returns null due to missing session", 'white');
        CLI::write("- Check authentication filter on routes", 'white');
        
        CLI::write("\n4. Recommended Solutions:", 'yellow');
        CLI::write("- Add more debugging to HabitController::monthlyData()", 'white');
        CLI::write("- Add logging to see what session data exists", 'white');
        CLI::write("- Add error handling for failed student resolution", 'white');
        CLI::write("- Test with proper session authentication", 'white');
        
        CLI::write("\n✅ Debug completed!", 'green');
    }
}
