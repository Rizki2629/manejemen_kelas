<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class HabitsSeeder extends Seeder
{
    public function run()
    {
        $habits = [
            ['code' => 'bangun_pagi', 'name' => 'Bangun Pagi', 'description' => 'Bangun sebelum jam 06:00', 'input_type' => 'boolean'],
            ['code' => 'beribadah', 'name' => 'Beribadah', 'description' => 'Ibadah harian sesuai agama', 'input_type' => 'boolean'],
            ['code' => 'berolahraga', 'name' => 'Berolahraga', 'description' => 'Aktivitas fisik harian', 'input_type' => 'boolean'],
            ['code' => 'makan_sehat', 'name' => 'Makan Sehat', 'description' => 'Pola makan bergizi', 'input_type' => 'boolean'],
            ['code' => 'gemar_belajar', 'name' => 'Gemar Belajar', 'description' => 'Belajar mandiri minimal 30 menit', 'input_type' => 'boolean'],
            ['code' => 'bermasyarakat', 'name' => 'Bermasyarakat', 'description' => 'Sopan dan membantu di rumah/lingkungan', 'input_type' => 'boolean'],
            ['code' => 'tidur_cepat', 'name' => 'Tidur Cepat', 'description' => 'Tidur sebelum jam 21:00', 'input_type' => 'boolean'],
        ];

        $this->db->table('habits')->insertBatch($habits);
    }
}
