<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\HabitLogModel;
use App\Models\StudentModel;
use App\Models\UserModel;

class TestHabitLogin extends BaseCommand
{
    protected $group = 'testing';
    protected $name = 'test:habit-login';
    protected $description = 'Test habit system with simulated login session';

    public function run(array $params)
    {
        $habitLogModel = new HabitLogModel();
        $studentModel = new StudentModel();
        $userModel = new UserModel();

        CLI::write('=== Testing Habit System with Login ===', 'green');

        // Get parameters
        $username = $params[0] ?? '3157252958'; // NISN for AFIFAH FITIYA
        $date = $params[1] ?? date('Y-m-d');

        CLI::write("Testing login for: $username on Date: $date");

        // 1. Find user by username (NISN)
        $user = $userModel->where('username', $username)->first();
        if (!$user) {
            CLI::error("User not found with username: $username");
            return;
        }

        CLI::write("✅ User found: " . $user['username'] . " (Role: " . $user['role'] . ")", 'green');

        // 2. Find student by NISN
        $student = $studentModel->where('nisn', $username)->first();
        if (!$student) {
            CLI::error("Student not found with NISN: $username");
            return;
        }

        CLI::write("✅ Student found: " . $student['nama'] . " (ID: " . $student['id'] . ")", 'green');

        // 3. Check existing habit data for this date
        CLI::write("\nChecking existing habit data for $date...", 'yellow');
        $summary = $habitLogModel->getDailySummary($student['id'], $date);

        if (empty($summary)) {
            CLI::write("❌ No habit data found for $date", 'red');
            CLI::write("Let's create some test data...", 'yellow');
            
            // Create test data
            $testData = [
                [
                    'student_id' => $student['id'],
                    'habit_id' => 1,
                    'log_date' => $date,
                    'value_time' => '06:30:00',
                    'notes' => 'Bangun pagi via login test'
                ],
                [
                    'student_id' => $student['id'],
                    'habit_id' => 2,
                    'log_date' => $date,
                    'value_bool' => 1,
                    'notes' => 'Sholat 5 waktu',
                    'value_json' => json_encode(['prayers' => ['Subuh', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya']])
                ]
            ];

            foreach ($testData as $data) {
                $result = $habitLogModel->upsertLog($data);
                CLI::write("✅ Created habit log ID: $result");
            }

            // Re-check
            $summary = $habitLogModel->getDailySummary($student['id'], $date);
        }

        if (!empty($summary)) {
            CLI::write("✅ Found " . count($summary) . " habit records for $date:", 'green');
            foreach ($summary as $habit) {
                $output = "  - Habit ID {$habit['habit_id']}: ";
                if ($habit['value_bool']) $output .= "✓ ";
                if ($habit['value_time']) $output .= "time={$habit['value_time']} ";
                if ($habit['value_number']) $output .= "number={$habit['value_number']} ";
                if ($habit['notes']) $output .= "notes=" . substr($habit['notes'], 0, 30) . "... ";
                if ($habit['value_json']) {
                    $json = json_decode($habit['value_json'], true);
                    $output .= "json=" . count($json) . " items ";
                }
                CLI::write($output);
            }
        }

        // 4. Test the controller method directly
        CLI::write("\nTesting controller method directly...", 'yellow');
        
        try {
            // Simulate the HabitController::summary method
            $rows = $habitLogModel->getDailySummary($student['id'], $date);
            
            // Parse JSON data if exists
            foreach ($rows as &$row) {
                if (!empty($row['value_json'])) {
                    $row['complex_data'] = json_decode($row['value_json'], true);
                }
            }
            
            $response = ['data' => $rows];
            CLI::write("✅ Controller response would be:", 'green');
            CLI::write(json_encode($response, JSON_PRETTY_PRINT));
            
        } catch (\Exception $e) {
            CLI::error("❌ Error in controller simulation: " . $e->getMessage());
        }

        // 5. Provide login instructions
        CLI::write("\n=== Login Instructions ===", 'cyan');
        CLI::write("To access habits via web:");
    CLI::write("1. Go to: https://sdngu09.my.id/login");
        CLI::write("2. Username: $username");
        CLI::write("3. Password: 123456 (or check users table)");
    CLI::write("4. Then go to: https://sdngu09.my.id/siswa/habits?date=$date");

        CLI::write("\nTest completed!", 'green');
    }
}
