<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBukuKasusTable extends Migration
{
    public function up()
    {
        // Create table for storing case records
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'siswa_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'guru_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tanggal_kejadian' => [
                'type' => 'DATE',
            ],
            'jenis_kasus' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'deskripsi_kasus' => [
                'type' => 'TEXT',
            ],
            'tindakan_yang_diambil' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['belum_ditangani', 'dalam_proses', 'selesai'],
                'default'    => 'belum_ditangani',
            ],
            'tingkat_keparahan' => [
                'type'       => 'ENUM',
                'constraint' => ['Ringan', 'Sedang', 'Berat'],
            ],
            'catatan_guru' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addForeignKey('siswa_id', 'tb_siswa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('guru_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('buku_kasus');
    }

    public function down()
    {
        $this->forge->dropTable('buku_kasus');
    }
}
