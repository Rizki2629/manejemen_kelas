<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAbsensiMobileTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'siswa_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'nisn' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
                'null' => false,
            ],
            'nama' => [
                'type' => 'VARCHAR',
                'constraint' => 120,
                'null' => false,
            ],
            'kelas' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'tanggal' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'device_taken_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'latitude' => [
                'type' => 'DECIMAL',
                'constraint' => '10,7',
                'null' => true,
            ],
            'longitude' => [
                'type' => 'DECIMAL',
                'constraint' => '10,7',
                'null' => true,
            ],
            'accuracy_m' => [
                'type' => 'FLOAT',
                'null' => true,
            ],
            'address' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'photo_path' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
            ],
            'user_agent' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
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
        $this->forge->addKey(['siswa_id', 'tanggal']);
        $this->forge->addKey('nisn');

        $this->forge->createTable('absensi_mobile', true);
    }

    public function down()
    {
        $this->forge->dropTable('absensi_mobile', true);
    }
}
