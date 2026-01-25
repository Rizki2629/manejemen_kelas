<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run()
    {
        // Create a class if none
        $kelas = $this->db->table('kelas')->get()->getFirstRow('array');
        if (!$kelas) {
            $this->db->table('kelas')->insert([
                'nama' => 'Kelas 1A',
                'tingkat' => 1,
                'walikelas_id' => null,
                'tahun_ajaran' => '2025/2026',
                'jumlah_siswa' => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            $kelasId = $this->db->insertID();
        } else {
            $kelasId = $kelas['id'];
        }

        // Create a student if none
        $siswa = $this->db->table('siswa')->get()->getFirstRow('array');
        if (!$siswa) {
            $this->db->table('siswa')->insert([
                'nama' => 'Budi Santoso',
                'nis' => 'S001',
                'nisn' => '1234567890',
                'kelas_id' => $kelasId,
                'jenis_kelamin' => 'L',
                'status' => 'aktif',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
