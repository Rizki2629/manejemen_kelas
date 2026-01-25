<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestMonthlyAPI extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:monthly-api';
    protected $description = 'Test monthly API endpoint functionality';

    public function run(array $params)
    {
        $studentId = $params[0] ?? '5';
        $month = $params[1] ?? '2025-08';
        
        CLI::write("=== Testing Monthly API for student ID: {$studentId}, month: {$month} ===", 'green');
        
        $db = \Config\Database::connect();
        
        // Test the monthly data query
        $startDate = $month . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));
        
        CLI::write("\nDate range: {$startDate} to {$endDate}", 'cyan');
        
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
        
        CLI::write("Query results count: " . count($results), 'yellow');
        
        // Process results like in controller
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
        
        CLI::write("\nProcessed monthly data:", 'green');
        CLI::write("Available dates: " . implode(', ', array_keys($monthlyData)), 'cyan');
        
        // Check specific date
        $targetDate = '2025-08-21';
        if (isset($monthlyData[$targetDate])) {
            CLI::write("\nData for {$targetDate}:", 'green');
            foreach ($monthlyData[$targetDate] as $habitKey => $habitData) {
                CLI::write("  {$habitKey}: completed=" . ($habitData['completed'] ? 'YES' : 'NO') . 
                          ", time=" . ($habitData['time'] ?: 'null') . 
                          ", duration=" . ($habitData['duration'] ?: 'null') . 
                          ", notes=" . ($habitData['notes'] ?: 'null'), 'white');
            }
        } else {
            CLI::write("❌ No data found for {$targetDate}", 'red');
        }
        
        // Show final response structure
        CLI::write("\nFinal API response structure:", 'yellow');
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
        
        CLI::write("Response has " . count($response['data']) . " date entries", 'cyan');
        
        CLI::write("\n✅ Test completed!", 'green');
    }
}
