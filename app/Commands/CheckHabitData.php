<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CheckHabitData extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'check:habit-data';
    protected $description = 'Check habit data for specific user on specific date';

    public function run(array $params)
    {
        $identifier = $params[0] ?? '3157252958';
        $date = $params[1] ?? '2025-08-18';
        
        CLI::write("Checking habit data for user: {$identifier} on date: {$date}", 'green');
        
        $db = \Config\Database::connect();
        
        // First, let's find the student
        CLI::write("\n1. Finding student with identifier: {$identifier}");
        
        // Check in siswa table
        $siswaQuery = $db->query("SELECT * FROM siswa WHERE nisn = ? OR nis = ? OR id = ?", 
            [$identifier, $identifier, $identifier]);
        $siswa = $siswaQuery->getResultArray();
        
        if (!empty($siswa)) {
            CLI::write("Found in siswa table:", 'yellow');
            foreach ($siswa as $s) {
                CLI::write("  ID: {$s['id']}, Name: {$s['nama']}, NISN: {$s['nisn']}, NIS: {$s['nis']}");
            }
        } else {
            CLI::write("Not found in siswa table", 'red');
        }
        
        // Check in tb_siswa table
        $tbSiswaQuery = $db->query("SELECT * FROM tb_siswa WHERE nisn = ? OR nipd = ? OR id = ?", 
            [$identifier, $identifier, $identifier]);
        $tbSiswa = $tbSiswaQuery->getResultArray();
        
        if (!empty($tbSiswa)) {
            CLI::write("Found in tb_siswa table:", 'yellow');
            foreach ($tbSiswa as $s) {
                CLI::write("  ID: {$s['id']}, Name: {$s['nama']}, NISN: {$s['nisn']}, NIPD: {$s['nipd']}");
            }
        } else {
            CLI::write("Not found in tb_siswa table", 'red');
        }
        
        // Get all student IDs to check habit logs
        $studentIds = [];
        foreach ($siswa as $s) {
            $studentIds[] = $s['id'];
        }
        
        if (empty($studentIds)) {
            CLI::write("\nNo student found with identifier: {$identifier}", 'red');
            return;
        }
        
        // Check habit logs
        CLI::write("\n2. Checking habit logs for date: {$date}");
        $studentIdList = implode(',', $studentIds);
        
        $habitLogsQuery = $db->query("
            SELECT hl.*, h.name as habit_name, h.code as habit_code 
            FROM habit_logs hl 
            JOIN habits h ON h.id = hl.habit_id 
            WHERE hl.student_id IN ({$studentIdList}) AND hl.log_date = ?
            ORDER BY hl.habit_id", [$date]);
        
        $habitLogs = $habitLogsQuery->getResultArray();
        
        if (!empty($habitLogs)) {
            CLI::write("Found " . count($habitLogs) . " habit log entries:", 'green');
            foreach ($habitLogs as $log) {
                CLI::write("  Habit: {$log['habit_name']} ({$log['habit_code']})");
                CLI::write("    Value Bool: " . ($log['value_bool'] ? 'Yes' : 'No'));
                CLI::write("    Value Time: " . ($log['value_time'] ?? 'None'));
                CLI::write("    Value Number: " . ($log['value_number'] ?? 'None'));
                CLI::write("    Notes: " . ($log['notes'] ?? 'None'));
                CLI::write("    Created: " . $log['created_at']);
                CLI::write("");
            }
        } else {
            CLI::write("No habit logs found for date: {$date}", 'red');
        }
        
        // Check all habit logs for this student
        CLI::write("\n3. Checking all habit logs for this student:");
        $allLogsQuery = $db->query("
            SELECT hl.log_date, COUNT(*) as habit_count 
            FROM habit_logs hl 
            WHERE hl.student_id IN ({$studentIdList})
            GROUP BY hl.log_date 
            ORDER BY hl.log_date DESC
            LIMIT 10");
        
        $allLogs = $allLogsQuery->getResultArray();
        
        if (!empty($allLogs)) {
            CLI::write("Recent habit log dates:", 'green');
            foreach ($allLogs as $log) {
                CLI::write("  Date: {$log['log_date']} - {$log['habit_count']} habits recorded");
            }
        } else {
            CLI::write("No habit logs found for this student", 'red');
        }
        
        CLI::write("\nDone!", 'green');
    }
}
