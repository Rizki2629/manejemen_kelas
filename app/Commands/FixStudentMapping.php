<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\StudentModel;

class FixStudentMapping extends BaseCommand
{
    protected $group       = 'debugging';
    protected $name        = 'fix:student-mapping';
    protected $description = 'Fix student ID mapping for NISN 3157252958';

    public function run(array $params)
    {
        $studentModel = new StudentModel();
        
        CLI::write('=== Fixing Student Mapping for NISN: 3157252958 ===', 'yellow');
        
        // Find student by NISN
        $student = $studentModel->where('nisn', '3157252958')->first();
        
        if (!$student) {
            CLI::write('❌ Student with NISN 3157252958 not found in students table', 'red');
            
            // Check in tb_siswa table
            $db = db_connect();
            $tbSiswa = $db->table('tb_siswa')->where('nisn', '3157252958')->get()->getRowArray();
            
            if ($tbSiswa) {
                CLI::write('✅ Found in tb_siswa: ' . $tbSiswa['nama'], 'green');
                
                // Create student record
                $studentData = [
                    'nisn' => $tbSiswa['nisn'],
                    'nis' => $tbSiswa['nis'] ?? null,
                    'nama' => $tbSiswa['nama'],
                    'kelas_id' => $tbSiswa['kelas_id'] ?? null,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                $newId = $studentModel->insert($studentData);
                if ($newId) {
                    CLI::write("✅ Created student record with ID: $newId", 'green');
                    
                    // Update habit_logs to use correct student_id
                    $habitLogsUpdated = $db->table('habit_logs')
                        ->where('student_id', 3157252958) // Old incorrect ID
                        ->update(['student_id' => $newId]);
                    
                    CLI::write("✅ Updated $habitLogsUpdated habit_logs records", 'green');
                } else {
                    CLI::write('❌ Failed to create student record', 'red');
                }
            } else {
                CLI::write('❌ Student not found in tb_siswa either', 'red');
            }
        } else {
            CLI::write('✅ Student found: ' . $student['nama'] . ' (ID: ' . $student['id'] . ')', 'green');
            
            // Check if there are any habit_logs with wrong student_id
            $db = db_connect();
            $wrongLogs = $db->table('habit_logs')
                ->where('student_id', 3157252958) // NISN used as student_id
                ->countAllResults();
            
            if ($wrongLogs > 0) {
                CLI::write("Found $wrongLogs habit_logs with wrong student_id (NISN instead of ID)", 'yellow');
                
                // Fix the mapping
                $updated = $db->table('habit_logs')
                    ->where('student_id', 3157252958)
                    ->update(['student_id' => $student['id']]);
                
                CLI::write("✅ Fixed $updated habit_logs records", 'green');
            } else {
                CLI::write('✅ All habit_logs already have correct student_id', 'green');
            }
        }
        
        // Verify the fix
        CLI::write("\n=== Verification ===", 'yellow');
        
        $student = $studentModel->where('nisn', '3157252958')->first();
        if ($student) {
            CLI::write("Student ID for NISN 3157252958: " . $student['id'], 'green');
            
            $db = db_connect();
            $logCount = $db->table('habit_logs')
                ->where('student_id', $student['id'])
                ->countAllResults();
            
            CLI::write("Habit logs count for this student: $logCount", 'green');
            
            // Show August 2025 logs
            $augustLogs = $db->table('habit_logs')
                ->where('student_id', $student['id'])
                ->where('log_date >=', '2025-08-01')
                ->where('log_date <=', '2025-08-31')
                ->countAllResults();
            
            CLI::write("August 2025 logs: $augustLogs", 'green');
            
            // Check specifically for 2025-08-21
            $date21Logs = $db->table('habit_logs')
                ->where('student_id', $student['id'])
                ->where('log_date', '2025-08-21')
                ->countAllResults();
            
            CLI::write("Logs for 2025-08-21: $date21Logs", 'green');
        }
        
        CLI::write("\n✅ Student mapping fix completed!", 'green');
    }
}
