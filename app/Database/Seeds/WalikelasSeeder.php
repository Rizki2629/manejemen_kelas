<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class WalikelasSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nama' => 'Rizki',
                'nip' => '199303292019031011',
                'kelas' => '5A',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama' => 'Sari Indah',
                'nip' => '198505152010032003',
                'kelas' => '4B',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama' => 'Maya Sari',
                'nip' => '198803072012032001',
                'kelas' => '3A',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama' => 'Dewi Lestari',
                'nip' => '199201142016032002',
                'kelas' => '2B',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama' => 'Hendra Wijaya',
                'nip' => '198609182013031002',
                'kelas' => '1A',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('walikelas')->insertBatch($data);
    }
}
