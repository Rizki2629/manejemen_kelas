<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class NilaiSeeder extends Seeder
{
    public function run()
    {
        // Data nilai dummy untuk testing
        $data = [
            // Nilai untuk Ahmad Fauzan (siswa_id = 1)
            [
                'siswa_id' => 1,
                'mata_pelajaran' => 'IPAS',
                'jenis_nilai' => 'harian',
                'nilai' => 85.5,
                'tp_materi' => 'TP 1.1 - Ciri-ciri Makhluk Hidup
Siswa mampu mengidentifikasi ciri-ciri makhluk hidup seperti bernapas, bergerak, berkembang biak, memerlukan makanan, dan tumbuh.',
                'tanggal' => '2025-07-10',
                'kelas' => '1',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'siswa_id' => 1,
                'mata_pelajaran' => 'IPAS',
                'jenis_nilai' => 'harian',
                'nilai' => 90.0,
                'tp_materi' => 'TP 1.2 - Kebutuhan Makhluk Hidup
Siswa mampu menjelaskan kebutuhan dasar makhluk hidup seperti air, udara, makanan, dan tempat tinggal.',
                'tanggal' => '2025-07-12',
                'kelas' => '1',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'siswa_id' => 1,
                'mata_pelajaran' => 'IPAS',
                'jenis_nilai' => 'pts',
                'nilai' => 88.0,
                'tp_materi' => 'Penilaian Tengah Semester - Bab 1 Makhluk Hidup
Tes tertulis tentang ciri-ciri makhluk hidup dan kebutuhan dasar makhluk hidup.',
                'tanggal' => '2025-07-15',
                'kelas' => '1',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            
            // Nilai untuk Siti Aisyah (siswa_id = 2)
            [
                'siswa_id' => 2,
                'mata_pelajaran' => 'IPAS',
                'jenis_nilai' => 'harian',
                'nilai' => 92.5,
                'tp_materi' => 'TP 1.1 - Ciri-ciri Makhluk Hidup
Siswa mampu mengidentifikasi ciri-ciri makhluk hidup seperti bernapas, bergerak, berkembang biak, memerlukan makanan, dan tumbuh.',
                'tanggal' => '2025-07-10',
                'kelas' => '1',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'siswa_id' => 2,
                'mata_pelajaran' => 'IPAS',
                'jenis_nilai' => 'harian',
                'nilai' => 87.0,
                'tp_materi' => 'TP 1.2 - Kebutuhan Makhluk Hidup
Siswa mampu menjelaskan kebutuhan dasar makhluk hidup seperti air, udara, makanan, dan tempat tinggal.',
                'tanggal' => '2025-07-12',
                'kelas' => '1',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'siswa_id' => 2,
                'mata_pelajaran' => 'IPAS',
                'jenis_nilai' => 'pts',
                'nilai' => 91.0,
                'tp_materi' => 'Penilaian Tengah Semester - Bab 1 Makhluk Hidup
Tes tertulis tentang ciri-ciri makhluk hidup dan kebutuhan dasar makhluk hidup.',
                'tanggal' => '2025-07-15',
                'kelas' => '1',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            
            // Nilai untuk Rizki Pratama (siswa_id = 3)
            [
                'siswa_id' => 3,
                'mata_pelajaran' => 'IPAS',
                'jenis_nilai' => 'harian',
                'nilai' => 80.0,
                'tp_materi' => 'TP 1.1 - Ciri-ciri Makhluk Hidup
Siswa mampu mengidentifikasi ciri-ciri makhluk hidup seperti bernapas, bergerak, berkembang biak, memerlukan makanan, dan tumbuh.',
                'tanggal' => '2025-07-10',
                'kelas' => '1',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'siswa_id' => 3,
                'mata_pelajaran' => 'IPAS',
                'jenis_nilai' => 'harian',
                'nilai' => 85.5,
                'tp_materi' => 'TP 1.2 - Kebutuhan Makhluk Hidup
Siswa mampu menjelaskan kebutuhan dasar makhluk hidup seperti air, udara, makanan, dan tempat tinggal.',
                'tanggal' => '2025-07-12',
                'kelas' => '1',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Insert data ke tabel nilai
        $this->db->table('nilai')->insertBatch($data);
    }
}
