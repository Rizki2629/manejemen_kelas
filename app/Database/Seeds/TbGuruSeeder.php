<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TbGuruSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nama_lengkap' => 'Rizki',
                'gelar' => 'S.Pd',
                'nip' => '199303292019031011',
                'nuptk' => '011313131313',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Bima',
                'tanggal_lahir' => '1993-03-29',
                'jabatan' => 'Wali Kelas 5 A',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_lengkap' => 'Sari Indah',
                'gelar' => 'S.Pd',
                'nip' => '198505152010032003',
                'nuptk' => '021414141414',
                'jenis_kelamin' => 'Perempuan',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1985-05-15',
                'jabatan' => 'Wali Kelas 4 B',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_lengkap' => 'Ahmad Fauzi',
                'gelar' => 'S.Pd.I',
                'nip' => '199012102015031001',
                'nuptk' => '031515151515',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1990-12-10',
                'jabatan' => 'Guru Agama',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_lengkap' => 'Maya Sari',
                'gelar' => 'S.Pd',
                'nip' => '198803072012032002',
                'nuptk' => '041616161616',
                'jenis_kelamin' => 'Perempuan',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1988-03-07',
                'jabatan' => 'Wali Kelas 3 A',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_lengkap' => 'Budi Santoso',
                'gelar' => 'S.Pd',
                'nip' => '199507252018031003',
                'nuptk' => '051717171717',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Yogyakarta',
                'tanggal_lahir' => '1995-07-25',
                'jabatan' => 'Guru Olahraga',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_lengkap' => 'Dewi Lestari',
                'gelar' => 'S.Pd',
                'nip' => '199201142016032001',
                'nuptk' => '061818181818',
                'jenis_kelamin' => 'Perempuan',
                'tempat_lahir' => 'Medan',
                'tanggal_lahir' => '1992-01-14',
                'jabatan' => 'Wali Kelas 2 B',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_lengkap' => 'Hendra Wijaya',
                'gelar' => 'S.Pd',
                'nip' => '198609182013031002',
                'nuptk' => '071919191919',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Palembang',
                'tanggal_lahir' => '1986-09-18',
                'jabatan' => 'Wali Kelas 1 A',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_lengkap' => 'Rina Marlina',
                'gelar' => 'S.Pd',
                'nip' => '199404032017032004',
                'nuptk' => '082020202020',
                'jenis_kelamin' => 'Perempuan',
                'tempat_lahir' => 'Makassar',
                'tanggal_lahir' => '1994-04-03',
                'jabatan' => 'Guru Bahasa Inggris',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Insert data using query builder
        $this->db->table('tb_guru')->insertBatch($data);
    }
}
