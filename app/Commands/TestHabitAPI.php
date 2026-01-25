<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestHabitAPI extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:habit-api';
    protected $description = 'Test habit API endpoints';

    public function run(array $params)
    {
        $studentId = $params[0] ?? '5';
        $date = $params[1] ?? '2025-08-18';
        
        CLI::write("Testing habit API for student ID: {$studentId} on date: {$date}", 'green');
        
        $db = \Config\Database::connect();
        
        // Test the same query that HabitController uses
        CLI::write("\n1. Testing HabitLogModel->getDailySummary() equivalent query:");
        
        $query = $db->query("
            SELECT 
                hl.*,
                h.name as habit_name,
                h.code as habit_code
            FROM habit_logs hl
            JOIN habits h ON h.id = hl.habit_id
            WHERE hl.student_id = ? AND hl.log_date = ?
            ORDER BY hl.habit_id
        ", [$studentId, $date]);
        
        $results = $query->getResultArray();
        
        if (!empty($results)) {
            CLI::write("Found " . count($results) . " records:", 'green');
            foreach ($results as $row) {
                CLI::write("  Habit ID: {$row['habit_id']} - {$row['habit_name']}");
                CLI::write("    value_bool: " . ($row['value_bool'] ?? 'NULL'));
                CLI::write("    value_time: " . ($row['value_time'] ?? 'NULL'));
                CLI::write("    value_number: " . ($row['value_number'] ?? 'NULL'));
                CLI::write("    notes: " . ($row['notes'] ?? 'NULL'));
                CLI::write("");
            }
        } else {
            CLI::write("No records found", 'red');
        }
        
        // Test the format that should be returned by API
        CLI::write("\n2. Testing API response format:");
        $apiResponse = ['data' => $results];
        CLI::write("API would return: " . json_encode($apiResponse, JSON_PRETTY_PRINT));
        
        CLI::write("\nDone!", 'green');
    }
}
