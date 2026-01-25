<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestExport extends BaseCommand
{
    protected $group = 'test';
    protected $name = 'test:export';
    protected $description = 'Test export Excel function';

    public function run(array $params)
    {
        try {
            CLI::write('Testing export Excel function...', 'green');
            
            if (isset($params[0]) && $params[0] === 'list-kelas') {
                // Check table structures
                $db = \Config\Database::connect();
                
                CLI::write('Students with their kelas_id:', 'yellow');
                $siswa = $db->query("SELECT nama, nisn, kelas_id FROM siswa ORDER BY kelas_id")->getResultArray();
                foreach ($siswa as $row) {
                    CLI::write('- ' . $row['nama'] . ' (' . $row['nisn'] . ') - kelas_id: ' . $row['kelas_id'], 'white');
                }
                
                return;
            }
            
            // Test PhpSpreadsheet
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'Test');
            CLI::write('PhpSpreadsheet working', 'green');
            
            // Test models
            $nilaiModel = new \App\Models\NilaiModel();
            $siswaModel = new \App\Models\TbSiswaModel();
            CLI::write('Models loaded successfully', 'green');
            
            // Test database connection
            $db = \Config\Database::connect();
            $result = $db->query("SELECT COUNT(*) as count FROM siswa")->getRowArray();
            CLI::write('Database connection OK. Students count: ' . $result['count'], 'green');
            
            CLI::write('All tests passed!', 'green');
            
        } catch (\Exception $e) {
            CLI::write('Error: ' . $e->getMessage(), 'red');
            CLI::write('File: ' . $e->getFile() . ' Line: ' . $e->getLine(), 'red');
        }
    }
}
