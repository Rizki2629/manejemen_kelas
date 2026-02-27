<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class GuruSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nama'                   => 'Rizki',
                'nuptk'                  => '011313131313',
                'jk'                     => 'L',
                'tempat_lahir'           => 'Bima',
                'tanggal_lahir'          => '1993-03-29',
                'nip'                    => '199303292019031011',
                'status_kepegawaian'     => 'PNS',
                'tugas_mengajar'         => 'Wali Kelas 5 A',
                'created_at'             => date('Y-m-d H:i:s'),
                'updated_at'             => date('Y-m-d H:i:s'),
            ],
            [
                'nama'                   => 'Sari Indah',
                'nuptk'                  => '021414141414',
                'jk'                     => 'P',
                'tempat_lahir'           => 'Jakarta',
                'tanggal_lahir'          => '1985-05-15',
                'nip'                    => '198505152010032003',
                'status_kepegawaian'     => 'PNS',
                'tugas_mengajar'         => 'Wali Kelas 4 B',
                'created_at'             => date('Y-m-d H:i:s'),
                'updated_at'             => date('Y-m-d H:i:s'),
            ],
            [
                'nama'                   => 'Ahmad Fauzi',
                'nuptk'                  => '031515151515',
                'jk'                     => 'L',
                'tempat_lahir'           => 'Bandung',
                'tanggal_lahir'          => '1990-12-10',
                'nip'                    => '199012102015031001',
                'status_kepegawaian'     => 'PNS',
                'tugas_mengajar'         => 'Guru Agama',
                'created_at'             => date('Y-m-d H:i:s'),
                'updated_at'             => date('Y-m-d H:i:s'),
            ],
            [
                'nama'                   => 'Maya Sari',
                'nuptk'                  => '041616161616',
                'jk'                     => 'P',
                'tempat_lahir'           => 'Surabaya',
                'tanggal_lahir'          => '1988-03-07',
                'nip'                    => '198803072012032002',
                'status_kepegawaian'     => 'PNS',
                'tugas_mengajar'         => 'Wali Kelas 3 A',
                'created_at'             => date('Y-m-d H:i:s'),
                'updated_at'             => date('Y-m-d H:i:s'),
            ],
            [
                'nama'                   => 'Budi Santoso',
                'nuptk'                  => '051717171717',
                'jk'                     => 'L',
                'tempat_lahir'           => 'Yogyakarta',
                'tanggal_lahir'          => '1995-07-25',
                'nip'                    => '199507252018031003',
                'status_kepegawaian'     => 'PNS',
                'tugas_mengajar'         => 'Guru Olahraga',
                'created_at'             => date('Y-m-d H:i:s'),
                'updated_at'             => date('Y-m-d H:i:s'),
            ],
            [
                'nama'                   => 'Dewi Lestari',
                'nuptk'                  => '061818181818',
                'jk'                     => 'P',
                'tempat_lahir'           => 'Medan',
                'tanggal_lahir'          => '1992-01-14',
                'nip'                    => '199201142016032001',
                'status_kepegawaian'     => 'PNS',
                'tugas_mengajar'         => 'Wali Kelas 2 B',
                'created_at'             => date('Y-m-d H:i:s'),
                'updated_at'             => date('Y-m-d H:i:s'),
            ],
            [
                'nama'                   => 'Hendra Wijaya',
                'nuptk'                  => '071919191919',
                'jk'                     => 'L',
                'tempat_lahir'           => 'Palembang',
                'tanggal_lahir'          => '1986-09-18',
                'nip'                    => '198609182013031002',
                'status_kepegawaian'     => 'PNS',
                'tugas_mengajar'         => 'Wali Kelas 1 A',
                'created_at'             => date('Y-m-d H:i:s'),
                'updated_at'             => date('Y-m-d H:i:s'),
            ],
            [
                'nama'                   => 'Rina Marlina',
                'nuptk'                  => '082020202020',
                'jk'                     => 'P',
                'tempat_lahir'           => 'Makassar',
                'tanggal_lahir'          => '1994-04-03',
                'nip'                    => '199404032017032004',
                'status_kepegawaian'     => 'PNS',
                'tugas_mengajar'         => 'Guru Bahasa Inggris',
                'created_at'             => date('Y-m-d H:i:s'),
                'updated_at'             => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('guru')->insertBatch($data);
        echo "GuruSeeder: 8 guru records inserted.\n";
    }
}
