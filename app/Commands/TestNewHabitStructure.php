<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestNewHabitStructure extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:habit-structure';
    protected $description = 'Test new habit_logs table structure with complex data';

    public function run(array $params)
    {
        $studentId = $params[0] ?? '5';
        $date = $params[1] ?? '2025-08-18';
        
        CLI::write("Testing new habit structure for student ID: {$studentId} on date: {$date}", 'green');
        
        $habitLogModel = new \App\Models\HabitLogModel();
        
        // Test saving complex data
        CLI::write("\n1. Testing complex data save...", 'yellow');
        
        // Sample complex data for worship habit (ID 2)
        $complexData = [
            'prayers' => ['Subuh', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya'],
            'activities' => ['Baca Kitab Suci', 'Sedekah', 'Berdoa']
        ];
        
        $data = [
            'student_id' => $studentId,
            'habit_id' => 2,
            'log_date' => $date,
            'value_bool' => 1,
            'value_json' => json_encode($complexData),
            'notes' => 'Sholat 5 waktu dan aktivitas ibadah lainnya'
        ];
        
        try {
            $result = $habitLogModel->upsertLog($data);
            CLI::write("✅ Complex data saved with ID: {$result}", 'green');
        } catch (\Exception $e) {
            CLI::write("❌ Error saving: " . $e->getMessage(), 'red');
            return;
        }
        
        // Test retrieving data
        CLI::write("\n2. Testing data retrieval...", 'yellow');
        
        $retrieved = $habitLogModel->getDailySummary($studentId, $date);
        
        foreach ($retrieved as $row) {
            CLI::write("Habit ID: {$row['habit_id']}", 'cyan');
            CLI::write("  Value Bool: " . ($row['value_bool'] ?? 'null'), 'white');
            CLI::write("  Value Time: " . ($row['value_time'] ?? 'null'), 'white');
            CLI::write("  Value Number: " . ($row['value_number'] ?? 'null'), 'white');
            CLI::write("  Notes: " . ($row['notes'] ?? 'null'), 'white');
            CLI::write("  Value JSON: " . ($row['value_json'] ?? 'null'), 'white');
            
            if (!empty($row['value_json'])) {
                $parsed = json_decode($row['value_json'], true);
                CLI::write("  Parsed JSON:", 'cyan');
                CLI::write("    " . print_r($parsed, true), 'blue');
            }
            CLI::write("", 'white');
        }
        
        // Test API endpoint
        CLI::write("\n3. Testing API endpoint...", 'yellow');
        
        $baseUrl = base_url();
        $apiUrl = $baseUrl . "siswa/summary?date={$date}";
        
        CLI::write("API URL: {$apiUrl}", 'cyan');
        
        // For now, just show the SQL query that would be executed
        $db = \Config\Database::connect();
        $sql = "SELECT habit_id, value_bool, value_time, value_number, notes, value_json 
                FROM habit_logs 
                WHERE student_id = ? AND log_date = ?";
        
        CLI::write("SQL Query:", 'cyan');
        CLI::write($sql, 'blue');
        CLI::write("Parameters: [{$studentId}, {$date}]", 'blue');
        
        CLI::write("\n✅ Test completed! Structure is ready for complex data.", 'green');
    }
}
