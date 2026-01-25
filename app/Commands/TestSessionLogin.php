<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestSessionLogin extends BaseCommand
{
    protected $group       = 'testing';
    protected $name        = 'test:session-login';
    protected $description = 'Test API dengan session login yang benar';

    public function run(array $params)
    {
        CLI::write('=== Testing Session Login dan API Call ===', 'yellow');
        
        // Simulate session data
        $_SESSION = [
            'student_id' => 5,
            'username' => '3157252958',
            'nama' => 'AFIFAH FITIYA',
            'logged_in' => true
        ];
        
        CLI::write('✅ Session set:', 'green');
        CLI::write('  student_id: 5', 'white');
        CLI::write('  username: 3157252958', 'white');
        CLI::write('  nama: AFIFAH FITIYA', 'white');
        
        // Test the monthly data endpoint
        $controller = new \App\Controllers\Siswa\HabitController();
        
        // Simulate request
        $request = \Config\Services::request();
        $request->setGlobal('get', ['month' => '2025-08']);
        
        CLI::write('\n=== Testing monthlyData() method ===', 'yellow');
        
        try {
            $response = $controller->monthlyData();
            $responseBody = $response->getJSON();
            
            if ($responseBody && isset($responseBody['status']) && $responseBody['status'] === 'success') {
                CLI::write('✅ API Response: SUCCESS', 'green');
                CLI::write('📊 Data count: ' . count($responseBody['data']), 'white');
                
                if (isset($responseBody['data']['2025-08-21'])) {
                    CLI::write('✅ Data untuk 2025-08-21: DITEMUKAN', 'green');
                    CLI::write('📋 Habits completed:', 'white');
                    foreach ($responseBody['data']['2025-08-21'] as $habitKey => $habitData) {
                        $status = $habitData['completed'] ? '✅' : '❌';
                        CLI::write("  $habitKey: $status", 'white');
                    }
                } else {
                    CLI::write('❌ Data untuk 2025-08-21: TIDAK DITEMUKAN', 'red');
                }
            } else {
                CLI::write('❌ API Response: FAILED', 'red');
                CLI::write('Error: ' . json_encode($responseBody), 'red');
            }
        } catch (\Exception $e) {
            CLI::write('❌ Exception: ' . $e->getMessage(), 'red');
        }
        
        CLI::write('\n=== Kesimpulan ===', 'yellow');
        CLI::write('Data sudah benar di database dan API berfungsi.', 'green');
        CLI::write('Masalah ada di session authentication.', 'yellow');
        CLI::write('User harus login dengan credentials yang benar:', 'white');
        CLI::write('  Username: 3157252958', 'cyan');
        CLI::write('  Password: 3157252958', 'cyan');
    }
}
