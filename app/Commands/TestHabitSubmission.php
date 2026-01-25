<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestHabitSubmission extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:habit-submission';
    protected $description = 'Test habit data submission to database';

    public function run(array $params)
    {
        $studentId = $params[0] ?? '5';
        $date = $params[1] ?? date('Y-m-d');
        
        CLI::write("Testing habit submission for student ID: {$studentId} on date: {$date}", 'green');
        
        // Test data structure that frontend should send
        $testData = [
            'date' => $date,
            'habits' => [
                1 => [
                    'bool' => true,
                    'time' => '06:00'
                ],
                2 => [
                    'bool' => true,
                    'prayers' => ['Subuh', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya'],
                    'activities' => ['Baca Kitab Suci', 'Sedekah', 'Berdoa'],
                    'notes' => 'Sholat: Subuh, Dzuhur, Ashar, Maghrib, Isya; Aktivitas: Baca Kitab Suci, Sedekah, Berdoa'
                ],
                3 => [
                    'bool' => true,
                    'number' => 30,
                    'activities' => ['Jogging', 'Push up', 'Sit up'],
                    'notes' => 'Jogging, Push up, Sit up'
                ],
                4 => [
                    'bool' => true,
                    'items' => ['Sayur', 'Buah', 'Ikan'],
                    'notes' => 'Sayur, Buah, Ikan'
                ],
                5 => [
                    'bool' => true,
                    'items' => ['Matematika', 'Bahasa Indonesia'],
                    'notes' => 'Matematika, Bahasa Indonesia'
                ],
                6 => [
                    'bool' => true,
                    'items' => ['Membantu orang tua', 'Bermain dengan teman'],
                    'notes' => 'Membantu orang tua, Bermain dengan teman'
                ],
                7 => [
                    'bool' => true,
                    'time' => '21:00'
                ]
            ]
        ];
        
        CLI::write("Test data structure:", 'yellow');
        CLI::write(json_encode($testData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), 'blue');
        
        // Simulate API call
        CLI::write("\nSimulating API call to /siswa/habits (POST)...", 'yellow');
        
        // Test using cURL
        $baseUrl = 'http://localhost:8080';
        $url = $baseUrl . '/siswa/habits';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            CLI::write("❌ cURL Error: " . $error, 'red');
        } else {
            CLI::write("✅ HTTP Response Code: " . $httpCode, $httpCode == 200 ? 'green' : 'red');
            CLI::write("Response Body: " . $response, 'blue');
        }
        
        // Check database after submission
        CLI::write("\nChecking database after submission...", 'yellow');
        
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT COUNT(*) as count
            FROM habit_logs 
            WHERE student_id = ? AND log_date = ?
        ", [$studentId, $date]);
        
        $count = $query->getRow()->count;
        CLI::write("Total records in database: " . $count, 'cyan');
        
        // Show latest records
        $latestQuery = $db->query("
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
            ORDER BY hl.created_at DESC
            LIMIT 5
        ", [$studentId, $date]);
        
        $latest = $latestQuery->getResultArray();
        
        CLI::write("\nLatest 5 records:", 'yellow');
        foreach ($latest as $record) {
            CLI::write("- {$record['habit_name']}: Bool={$record['value_bool']}, Time={$record['value_time']}, Number={$record['value_number']}", 'white');
            if ($record['value_json']) {
                CLI::write("  JSON: " . $record['value_json'], 'blue');
            }
        }
    }
}
