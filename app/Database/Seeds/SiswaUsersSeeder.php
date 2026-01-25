<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SiswaUsersSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;
        $siswaRows = $db->table('siswa')->select('id, nama, nisn')->get()->getResultArray();
        if (!$siswaRows) return;

        $users = [];
        $now = date('Y-m-d H:i:s');
        foreach ($siswaRows as $s) {
            if (empty($s['nisn'])) continue;
            $users[] = [
                'username' => $s['nisn'],
                'password' => password_hash('siswa123', PASSWORD_DEFAULT),
                'nama' => $s['nama'] ?? 'Siswa',
                'email' => null,
                'role' => 'siswa',
                'walikelas_id' => null,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Upsert by username
        foreach ($users as $u) {
            $exists = $db->table('users')->where('username', $u['username'])->get()->getFirstRow('array');
            if ($exists) {
                $db->table('users')->where('id', $exists['id'])->update($u);
            } else {
                $db->table('users')->insert($u);
            }
        }
    }
}
