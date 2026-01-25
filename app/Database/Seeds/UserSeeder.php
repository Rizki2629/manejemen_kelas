<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Default password: 123456 (hashed)
        $defaultPassword = password_hash('123456', PASSWORD_DEFAULT);

        $data = [
            // Admin user
            [
                'username' => 'admin',
                'password' => $defaultPassword,
                'nama' => 'Administrator',
                'email' => 'admin@sdngu09.com',
                'role' => 'admin',
                'walikelas_id' => null,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Walikelas users (berdasarkan data di walikelas table)
            [
                'username' => '199303292019031011', // NIP Rizki
                'password' => $defaultPassword,
                'nama' => 'Rizki',
                'email' => 'rizki@sdngu09.com',
                'role' => 'walikelas',
                'walikelas_id' => 1, // ID dari walikelas table
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => '198505152010032003', // NIP Sari Indah
                'password' => $defaultPassword,
                'nama' => 'Sari Indah',
                'email' => 'sari@sdngu09.com',
                'role' => 'walikelas',
                'walikelas_id' => 2,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => '198803072012032001', // NIP Maya Sari
                'password' => $defaultPassword,
                'nama' => 'Maya Sari',
                'email' => 'maya@sdngu09.com',
                'role' => 'walikelas',
                'walikelas_id' => 3,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => '199201142016032002', // NIP Dewi Lestari
                'password' => $defaultPassword,
                'nama' => 'Dewi Lestari',
                'email' => 'dewi@sdngu09.com',
                'role' => 'walikelas',
                'walikelas_id' => 4,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => '198609182013031002', // NIP Hendra Wijaya
                'password' => $defaultPassword,
                'nama' => 'Hendra Wijaya',
                'email' => 'hendra@sdngu09.com',
                'role' => 'walikelas',
                'walikelas_id' => 5,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Guru users (bukan walikelas)
            [
                'username' => '199012102015031001', // NIP Ahmad Fauzi
                'password' => $defaultPassword,
                'nama' => 'Ahmad Fauzi',
                'email' => 'ahmad@sdngu09.com',
                'role' => 'guru',
                'walikelas_id' => null,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => '199507252018031002', // NIP Budi Santoso
                'password' => $defaultPassword,
                'nama' => 'Budi Santoso',
                'email' => 'budi@sdngu09.com',
                'role' => 'guru',
                'walikelas_id' => null,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => '199404032017032003', // NIP Rina Marlina
                'password' => $defaultPassword,
                'nama' => 'Rina Marlina',
                'email' => 'rina@sdngu09.com',
                'role' => 'guru',
                'walikelas_id' => null,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
