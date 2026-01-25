<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKodePenilaianToNilai extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('kode_penilaian', 'nilai')) {
            $this->forge->addColumn('nilai', [
                'kode_penilaian' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'null'       => true,
                    'after'      => 'jenis_nilai'
                ],
            ]);
            // index untuk pencarian
            $this->db->query('CREATE INDEX idx_nilai_kode_penilaian ON nilai(kode_penilaian)');
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('kode_penilaian', 'nilai')) {
            // Drop the column; index will be dropped automatically by MySQL, but attempt explicit drop for portability
            try { $this->db->query('ALTER TABLE `nilai` DROP INDEX `idx_nilai_kode_penilaian`'); } catch(\Throwable $e) {}
            $this->forge->dropColumn('nilai', 'kode_penilaian');
        }
    }
}
