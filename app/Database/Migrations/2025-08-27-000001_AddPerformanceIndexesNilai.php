<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPerformanceIndexesNilai extends Migration
{
    public function up()
    {
        $forge = \Config\Database::forge();
        // Add composite indexes if not exists (manual SQL for portability)
        $db = \Config\Database::connect();
        $indexes = $db->query("SHOW INDEX FROM nilai")->getResultArray();
        $existing = [];
        foreach($indexes as $ix){ $existing[$ix['Key_name']] = true; }
        $toCreate = [
            'idx_nilai_kelas_mapel_jenis_del' => 'CREATE INDEX idx_nilai_kelas_mapel_jenis_del ON nilai (kelas, mata_pelajaran, jenis_nilai, deleted_at)',
            'idx_nilai_kelas_mapel_jenis_kode_del' => 'CREATE INDEX idx_nilai_kelas_mapel_jenis_kode_del ON nilai (kelas, mata_pelajaran, jenis_nilai, kode_penilaian, deleted_at)',
            'idx_nilai_siswa_mapel_jenis_del' => 'CREATE INDEX idx_nilai_siswa_mapel_jenis_del ON nilai (siswa_id, mata_pelajaran, jenis_nilai, deleted_at)'
        ];
        foreach($toCreate as $name=>$sql){ if(!isset($existing[$name])){ try { $db->query($sql); } catch(\Throwable $e){} } }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        foreach(['idx_nilai_kelas_mapel_jenis_del','idx_nilai_kelas_mapel_jenis_kode_del','idx_nilai_siswa_mapel_jenis_del'] as $name){
            try { $db->query("DROP INDEX $name ON nilai"); } catch(\Throwable $e){}
        }
    }
}
