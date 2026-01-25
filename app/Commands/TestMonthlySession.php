<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestMonthlySession extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:monthly-session';
    protected $description = 'Test monthly API with simulated session';

    public function run(array $params)
    {
        CLI::write("=== Testing Monthly API Session Issue ===", 'green');
        
        // Simulate session
        $studentId = 5; // AFIFAH FITIYA
        $month = '2025-08';
        
        CLI::write("Simulating API call for student ID: {$studentId}, month: {$month}", 'cyan');
        
        // Load models
        $habitModel = new \App\Models\HabitModel();
        $habitLogModel = new \App\Models\HabitLogModel();
        $studentModel = new \App\Models\StudentModel();
        
        // Verify student exists
        $student = $studentModel->find($studentId);
        if (!$student) {
            CLI::write("❌ Student not found", 'red');
            return;
        }
        
        CLI::write("✅ Student found: {$student['nama']} (NISN: {$student['nisn']})", 'green');
        
        // Test monthly data logic directly
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

        $query = $db->query($sql, [$studentId, $startDate, $endDate]);
        $results = $query->getResultArray();
        
        CLI::write("Query found " . count($results) . " records", 'yellow');
        
        // Process data exactly like in controller
        $monthlyData = [];
        
        foreach ($results as $row) {
            $date = $row['log_date'];
            $habitKey = 'habit_' . $row['habit_id'];
            
            if (!isset($monthlyData[$date])) {
                $monthlyData[$date] = [];
            }
            
            $notes = '';
            if ($row['notes']) {
                $notesData = json_decode($row['notes'], true);
                if (is_array($notesData)) {
                    if (isset($notesData['prayers']) && is_array($notesData['prayers'])) {
                        $prayers = array_keys(array_filter($notesData['prayers']));
                        if (!empty($prayers)) {
                            $notes .= 'Sholat: ' . implode(', ', $prayers) . '. ';
                        }
                    }
                    if (isset($notesData['note'])) {
                        $notes .= $notesData['note'];
                    }
                } else {
                    $notes = $row['notes'];
                }
            }
            
            // Determine if habit is completed
            $completed = false;
            if ($row['completed'] == 1) {
                $completed = true;
            } elseif ($row['time']) {
                $completed = true;
            } elseif ($row['duration'] && $row['duration'] > 0) {
                $completed = true;
            }
            
            $monthlyData[$date][$habitKey] = [
                'completed' => $completed,
                'time' => $row['time'],
                'duration' => $row['duration'],
                'notes' => trim($notes),
                'habit_name' => $row['habit_name']
            ];
        }
        
        // Show formatted API response
        $response = [
            'status' => 'success',
            'data' => $monthlyData,
            'month' => $month,
            'debug' => [
                'query_results_count' => count($results),
                'student_id' => $studentId,
                'date_range' => [$startDate, $endDate]
            ]
        ];
        
        CLI::write("\n=== SIMULATED API RESPONSE ===", 'yellow');
        CLI::write(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), 'white');
        
        // Test JavaScript date key format
        CLI::write("\n=== DATE KEY TESTING ===", 'yellow');
        CLI::write("JavaScript getDateKey for day 21:", 'cyan');
        CLI::write("Format: YYYY-MM-DD with zero padding", 'white');
        CLI::write("Expected key: 2025-08-21", 'white');
        CLI::write("Available in data: " . (isset($monthlyData['2025-08-21']) ? 'YES ✅' : 'NO ❌'), 'cyan');
        
        if (isset($monthlyData['2025-08-21'])) {
            CLI::write("\nData for 2025-08-21:", 'green');
            foreach ($monthlyData['2025-08-21'] as $key => $data) {
                CLI::write("  {$key}: " . json_encode($data), 'white');
            }
        }
        
        CLI::write("\n✅ Test completed!", 'green');
    }
}
