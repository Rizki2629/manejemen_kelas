<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKalenderAkademik extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'tanggal_mulai' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'tanggal_selesai' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['off', 'libur_nasional', 'libur_sekolah', 'ujian', 'kegiatan'],
                'default'    => 'off',
            ],
            'keterangan' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'is_manual' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'comment'    => '1 = manual setting, 0 = auto (weekend/holiday)',
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['tanggal_mulai', 'tanggal_selesai']);
        $this->forge->addKey('status');
        $this->forge->createTable('kalender_akademik');

        // Insert some national holidays for 2025
        $nationalHolidays = [
            ['2025-01-01', '2025-01-01', 'libur_nasional', 'Tahun Baru Masehi', 0],
            ['2025-03-29', '2025-03-29', 'libur_nasional', 'Hari Raya Nyepi', 0],
            ['2025-03-30', '2025-03-30', 'libur_nasional', 'Wafat Isa Almasih', 0],
            ['2025-04-09', '2025-04-09', 'libur_nasional', 'Isra Miraj', 0],
            ['2025-05-01', '2025-05-01', 'libur_nasional', 'Hari Buruh Internasional', 0],
            ['2025-05-12', '2025-05-12', 'libur_nasional', 'Hari Raya Waisak', 0],
            ['2025-05-29', '2025-05-29', 'libur_nasional', 'Kenaikan Isa Almasih', 0],
            ['2025-06-01', '2025-06-01', 'libur_nasional', 'Hari Lahir Pancasila', 0],
            ['2025-08-17', '2025-08-17', 'libur_nasional', 'Hari Kemerdekaan Indonesia', 0],
            ['2025-12-25', '2025-12-25', 'libur_nasional', 'Hari Raya Natal', 0],
        ];

        foreach ($nationalHolidays as $holiday) {
            $this->db->table('kalender_akademik')->insert([
                'tanggal_mulai' => $holiday[0],
                'tanggal_selesai' => $holiday[1],
                'status' => $holiday[2],
                'keterangan' => $holiday[3],
                'is_manual' => $holiday[4],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropTable('kalender_akademik');
    }
}
