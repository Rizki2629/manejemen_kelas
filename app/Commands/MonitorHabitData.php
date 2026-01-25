<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class MonitorHabitData extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'monitor:habit-data';
    protected $description = 'Monitor habit data changes in database';

    public function run(array $params)
    {
        $studentId = $params[0] ?? '5';
        $date = $params[1] ?? date('Y-m-d');
        
        CLI::write("Monitoring habit data for student ID: {$studentId} on date: {$date}", 'green');
        CLI::write("URL to test: http://localhost:8080/siswa/habits", 'cyan');
        CLI::write("Press Ctrl+C to stop monitoring\n", 'yellow');
        
        $db = \Config\Database::connect();
        
        // Get initial count
        $initialQuery = $db->query("SELECT COUNT(*) as count FROM habit_logs WHERE student_id = ? AND log_date = ?", [$studentId, $date]);
        $initialCount = $initialQuery->getRow()->count;
        
        CLI::write("Initial records count: {$initialCount}", 'blue');
        
        // Show current data
        $this->showCurrentData($studentId, $date);
        
        $lastCount = $initialCount;
        $checkCount = 0;
        
        while (true) {
            sleep(2); // Check every 2 seconds
            $checkCount++;
            
            // Check current count
            $currentQuery = $db->query("SELECT COUNT(*) as count FROM habit_logs WHERE student_id = ? AND log_date = ?", [$studentId, $date]);
            $currentCount = $currentQuery->getRow()->count;
            
            if ($currentCount != $lastCount) {
                CLI::write("\n🔄 Data changed! New count: {$currentCount} (was {$lastCount})", 'green');
                $this->showCurrentData($studentId, $date);
                $lastCount = $currentCount;
            } else {
                // Show a dot every 10 checks to indicate monitoring is active
                if ($checkCount % 10 == 0) {
                    CLI::write(".", 'white', false);
                }
            }
        }
    }
    
    private function showCurrentData($studentId, $date)
    {
        $db = \Config\Database::connect();
        
        CLI::write("\n📊 Current habit data:", 'yellow');
        CLI::write("Student ID: {$studentId} | Date: {$date}", 'cyan');
        CLI::write(str_repeat("-", 80), 'white');
        
        $query = $db->query("
            SELECT 
                hl.habit_id,
                h.name as habit_name,
                hl.value_bool,
                hl.value_time,
                hl.value_number,
                hl.notes,
                hl.value_json,
                hl.created_at
            FROM habit_logs hl
            JOIN habits h ON h.id = hl.habit_id
            WHERE hl.student_id = ? AND hl.log_date = ?
            ORDER BY hl.habit_id
        ", [$studentId, $date]);
        
        $results = $query->getResultArray();
        
        if (empty($results)) {
            CLI::write("❌ No data found", 'red');
        } else {
            foreach ($results as $row) {
                CLI::write("Habit {$row['habit_id']}: {$row['habit_name']}", 'cyan');
                CLI::write("  ✓ Bool: " . ($row['value_bool'] ? 'Yes' : 'No'), 'white');
                CLI::write("  ⏰ Time: " . ($row['value_time'] ?: 'None'), 'white');
                CLI::write("  🔢 Number: " . ($row['value_number'] ?: 'None'), 'white');
                CLI::write("  📝 Notes: " . ($row['notes'] ?: 'None'), 'white');
                CLI::write("  📋 JSON: " . ($row['value_json'] ?: 'None'), 'white');
                CLI::write("  🕐 Created: " . $row['created_at'], 'white');
                CLI::write("", 'white');
            }
        }
        CLI::write(str_repeat("-", 80), 'white');
    }
}
