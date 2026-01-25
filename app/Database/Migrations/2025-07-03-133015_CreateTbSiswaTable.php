<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTbSiswaTable extends Migration
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
            'nipd' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'jk' => [
                'type' => 'ENUM',
                'constraint' => ['L', 'P'],
                'null' => false,
            ],
            'nisn' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
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
            'nik' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'agama' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'alamat' => [
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
            'dusun' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'kelurahan' => [
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
            'jenis_tinggal' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'alat_transportasi' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
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
            'skhun' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'penerima_kps' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'no_kps' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'nama_ayah' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'tahun_lahir_ayah' => [
                'type' => 'YEAR',
                'null' => true,
            ],
            'pendidikan_ayah' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'pekerjaan_ayah' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'penghasilan_ayah' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'nik_ayah' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'nama_ibu' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'tahun_lahir_ibu' => [
                'type' => 'YEAR',
                'null' => true,
            ],
            'pendidikan_ibu' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'pekerjaan_ibu' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'penghasilan_ibu' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'nik_ibu' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'nama_wali' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'tahun_lahir_wali' => [
                'type' => 'YEAR',
                'null' => true,
            ],
            'pendidikan_wali' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'pekerjaan_wali' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'penghasilan_wali' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'nik_wali' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'kelas' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'no_peserta_ujian_nasional' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'no_seri_ijazah' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'penerima_kip' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'nomor_kip' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'nama_di_kip' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'nomor_kks' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'no_registrasi_akta_lahir' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'bank' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'nomor_rekening_bank' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'rekening_atas_nama' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'layak_pip' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'alasan_layak_pip' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'kebutuhan_khusus' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'sekolah_asal' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'anak_ke_berapa' => [
                'type' => 'INT',
                'constraint' => 3,
                'null' => true,
            ],
            'lintang' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'bujur' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'no_kk' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'berat_badan' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
            ],
            'tinggi_badan' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
            ],
            'lingkar_kepala' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
            ],
            'jml_saudara_kandung' => [
                'type' => 'INT',
                'constraint' => 3,
                'null' => true,
            ],
            'jarak_rumah_ke_sekolah' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
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
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('tb_siswa');
    }

    public function down()
    {
        $this->forge->dropTable('tb_siswa');
    }
}
