<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGuruTable extends Migration
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
            'nama' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'nuptk' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'jk' => [
                'type' => 'CHAR',
                'constraint' => 1,
                'null' => true,
                'comment' => 'L=Laki-laki, P=Perempuan',
            ],
            'tempat_lahir' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'tanggal_lahir' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'nip' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'status_kepegawaian' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'jenis_ptk' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'agama' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'alamat_jalan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'rt' => [
                'type' => 'VARCHAR',
                'constraint' => 5,
                'null' => true,
            ],
            'rw' => [
                'type' => 'VARCHAR',
                'constraint' => 5,
                'null' => true,
            ],
            'nama_dusun' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'desa_kelurahan' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'kecamatan' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'kode_pos' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'telepon' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'hp' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'tugas_mengajar' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'status_keaktifan' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'sk_cpns' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'tanggal_cpns' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'sk_pengangkatan' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'tmt_pengangkatan' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'lembaga_pengangkatan' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'pangkat_golongan' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'sumber_gaji' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'nama_suami_istri' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'nip_suami_istri' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'pekerjaan_suami_istri' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'tmt_pns' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'lisensi_kepsek' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'jumlah_sekolah_binaan' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'diklat_kepengawasan' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'mampu_handle_kk' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'keahlian_braille' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'keahlian_bahasa_isyarat' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'npwp' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'nama_wajib_pajak' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'kewarganegaraan' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'bank' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'nomor_rekening' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'rekening_atas_nama' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'nik' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'no_kk' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'karpeg' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'karis_karsu' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'lintang' => [
                'type' => 'DECIMAL',
                'constraint' => '10,8',
                'null' => true,
            ],
            'bujur' => [
                'type' => 'DECIMAL',
                'constraint' => '11,8',
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

        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['nama']);
        $this->forge->addKey(['nip']);
        $this->forge->addKey(['nuptk']);
        $this->forge->createTable('guru');
    }

    public function down()
    {
        $this->forge->dropTable('guru');
    }
}
