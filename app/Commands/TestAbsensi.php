<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\AbsensiModel;

class TestAbsensi extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:absensi';
    protected $description = 'Test absensi save functionality';

    public function run(array $params)
    {
        CLI::write('Testing Absensi Save Functionality', 'yellow');
        
        try {
            // Test database connection
            $db = \Config\Database::connect();
            
            CLI::write('1. Testing database connection...', 'blue');
            if ($db->connect()) {
                CLI::write('✓ Database connected successfully', 'green');
                CLI::write('   Database: ' . $db->getDatabase(), 'white');
            } else {
                CLI::write('✗ Database connection failed', 'red');
                return;
            }
            
            // Test table exists
            CLI::write('', 'white');
            CLI::write('2. Checking absensi table...', 'blue');
            $fields = $db->getFieldNames('absensi');
            if (!empty($fields)) {
                CLI::write('✓ Absensi table exists', 'green');
                CLI::write('   Fields: ' . implode(', ', $fields), 'white');
            } else {
                CLI::write('✗ Absensi table does not exist', 'red');
                return;
            }
            
            // Test finding a student
            CLI::write('', 'white');
            CLI::write('3. Finding test student...', 'blue');
            $student = $db->table('tb_siswa')->limit(1)->get()->getRow();
            if ($student) {
                CLI::write('✓ Found test student', 'green');
                CLI::write("   ID: {$student->id}, Name: {$student->nama}", 'white');
            } else {
                CLI::write('✗ No students found', 'red');
                return;
            }
            
            // Test direct database insert
            CLI::write('', 'white');
            CLI::write('4. Testing direct database insert...', 'blue');
            
            $testData = [
                'siswa_id' => $student->id,
                'tanggal' => '2025-07-21',
                'status' => 'hadir',
                'keterangan' => 'CLI test',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            // Delete existing record first
            $db->table('absensi')
               ->where('siswa_id', $testData['siswa_id'])
               ->where('tanggal', $testData['tanggal'])
               ->delete();
            
            $result = $db->table('absensi')->insert($testData);
            
            if ($result) {
                CLI::write('✓ Direct database insert successful', 'green');
                $insertId = $db->insertID();
                CLI::write("   Insert ID: {$insertId}", 'white');
            } else {
                CLI::write('✗ Direct database insert failed', 'red');
                $error = $db->error();
                CLI::write('   Error: ' . $error['message'], 'red');
                return;
            }
            
            // Test using AbsensiModel
            CLI::write('', 'white');
            CLI::write('5. Testing AbsensiModel...', 'blue');
            
            $absensiModel = new AbsensiModel();
            
            $modelData = [
                'siswa_id' => $student->id,
                'tanggal' => '2025-07-21',
                'status' => 'alpha',
                'keterangan' => 'Model CLI test',
                'created_by' => 1
            ];
            
            $modelResult = $absensiModel->saveAttendance($modelData);
            
            if ($modelResult) {
                CLI::write('✓ AbsensiModel test successful', 'green');
            } else {
                CLI::write('✗ AbsensiModel test failed', 'red');
                $errors = $absensiModel->errors();
                if (!empty($errors)) {
                    CLI::write('   Errors: ' . print_r($errors, true), 'red');
                }
            }
            
            // Show current records
            CLI::write('', 'white');
            CLI::write('6. Current absensi records:', 'blue');
            $count = $db->table('absensi')->countAllResults();
            CLI::write("   Total records: {$count}", 'white');
            
            if ($count > 0) {
                $recent = $db->table('absensi')
                            ->orderBy('created_at', 'DESC')
                            ->limit(3)
                            ->get()
                            ->getResult();
                
                CLI::write('   Recent records:', 'white');
                foreach ($recent as $record) {
                    CLI::write("   - ID: {$record->id}, Student: {$record->siswa_id}, Date: {$record->tanggal}, Status: {$record->status}", 'white');
                }
            }
            
            CLI::write('', 'white');
            CLI::write('Test completed successfully!', 'yellow');
            
        } catch (\Exception $e) {
            CLI::write('Error: ' . $e->getMessage(), 'red');
            CLI::write('File: ' . $e->getFile(), 'red');
            CLI::write('Line: ' . $e->getLine(), 'red');
        }
    }
}
