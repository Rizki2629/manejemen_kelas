<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\KelasModel;
use App\Models\StudentModel;

class DebugStudentData extends BaseCommand
{
    protected $group        = 'Debug';
    protected $name         = 'debug:students';
    protected $description  = 'Debug student data and class assignments';

    public function run(array $params)
    {
        $kelasModel = new KelasModel();
        $studentModel = new StudentModel();
        $db = db_connect();

        CLI::write('=== DEBUG STUDENT DATA ===', 'yellow');
        
        // Check total students
        $totalSiswa = $studentModel->countAll();
        CLI::write("Total students in 'siswa' table: $totalSiswa", 'green');
        
        // Check students with kelas_id
        $withKelasId = $studentModel->where('kelas_id >', 0)->countAllResults(false);
        CLI::write("Students with kelas_id > 0: $withKelasId", 'green');
        
        // Check kelas_id = 1 specifically
        $kelas1Students = $studentModel->where('kelas_id', 1)->findAll();
        CLI::write("Students in kelas_id = 1: " . count($kelas1Students), 'green');
        
        foreach ($kelas1Students as $s) {
            CLI::write("  ID: {$s['id']}, Nama: {$s['nama']}", 'white');
        }
        
        // Check all students if none in kelas 1
        if (empty($kelas1Students)) {
            CLI::write("\nAll students (first 10):", 'yellow');
            $allStudents = $studentModel->limit(10)->findAll();
            foreach ($allStudents as $s) {
                CLI::write("  ID: {$s['id']}, Nama: {$s['nama']}, Kelas ID: {$s['kelas_id']}", 'white');
            }
        }
        
        // Check tb_siswa
        CLI::write("\n=== TB_SISWA DATA ===", 'yellow');
        try {
            $tbSiswa = $db->table('tb_siswa')->limit(5)->get()->getResultArray();
            foreach ($tbSiswa as $s) {
                $kelas = $s['kelas'] ?? 'NULL';
                CLI::write("  ID: {$s['id']}, Nama: {$s['nama']}, Kelas: $kelas", 'white');
            }
        } catch (\Exception $e) {
            CLI::write("Error reading tb_siswa: " . $e->getMessage(), 'red');
        }
        
        // Check classes
        CLI::write("\n=== KELAS DATA ===", 'yellow');
        $classes = $kelasModel->limit(5)->findAll();
        foreach ($classes as $k) {
            CLI::write("  ID: {$k['id']}, Nama: {$k['nama']}", 'white');
        }
        
        CLI::write("\n=== RECOMMENDATIONS ===", 'cyan');
        if ($withKelasId == 0) {
            CLI::write("• No students have kelas_id assigned", 'red');
            CLI::write("• Run: php spark sync:student-classes", 'yellow');
        } else {
            CLI::write("• Students data looks good", 'green');
        }
    }
}
