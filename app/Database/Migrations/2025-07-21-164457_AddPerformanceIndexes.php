<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPerformanceIndexes extends Migration
{
    public function up()
    {
        // Helper closures
        $getIndexMap = function(string $table) {
            $map = [];
            try {
                $query = $this->db->query("SHOW INDEX FROM `{$table}`");
                $temp = [];
                foreach ($query->getResultArray() as $row) {
                    $key = $row['Key_name'];
                    $seq = (int)$row['Seq_in_index'];
                    $col = $row['Column_name'];
                    $temp[$key][$seq] = $col;
                }
                foreach ($temp as $name => $cols) {
                    ksort($cols);
                    $map[$name] = implode(',', $cols);
                }
            } catch (\Throwable $e) {}
            return $map;
        };
        $hasColumnsSignature = function(array $indexMap, array $columns) {
            $sig = implode(',', $columns);
            return in_array($sig, $indexMap, true);
        };
        $addIndexIfMissing = function(string $table, string $indexName, array $columns) use ($getIndexMap, $hasColumnsSignature) {
            if (!$this->db->tableExists($table)) return;
            $indexMap = $getIndexMap($table);
            if (isset($indexMap[$indexName])) return; // exact name exists
            if ($hasColumnsSignature($indexMap, $columns)) return; // same columns different name
            $colsSql = '`' . implode('`,`', $columns) . '`';
            try {
                $this->db->query("ALTER TABLE `{$table}` ADD INDEX `{$indexName}` ({$colsSql})");
            } catch (\Throwable $e) {
                // swallow to keep migration moving
            }
        };

        // Users
        $addIndexIfMissing('users', 'idx_users_role', ['role']);
        $addIndexIfMissing('users', 'idx_users_email', ['email']);

        // Walikelas
        $addIndexIfMissing('walikelas', 'idx_walikelas_user_id', ['user_id']);
        $addIndexIfMissing('walikelas', 'idx_walikelas_kelas', ['kelas']);

        // tb_siswa
        $addIndexIfMissing('tb_siswa', 'idx_tb_siswa_kelas', ['kelas']);
        $addIndexIfMissing('tb_siswa', 'idx_tb_siswa_siswa_id', ['siswa_id']);
        $addIndexIfMissing('tb_siswa', 'idx_tb_siswa_kelas_siswa_id', ['kelas','siswa_id']);

        // absensi
        $addIndexIfMissing('absensi', 'idx_absensi_tanggal', ['tanggal']);
        $addIndexIfMissing('absensi', 'idx_absensi_kelas', ['kelas']);
        $addIndexIfMissing('absensi', 'idx_absensi_siswa_id', ['siswa_id']);
        $addIndexIfMissing('absensi', 'idx_absensi_tanggal_kelas', ['tanggal','kelas']);
        $addIndexIfMissing('absensi', 'idx_absensi_siswa_id_tanggal', ['siswa_id','tanggal']);

        // nilai
        $addIndexIfMissing('nilai', 'idx_nilai_siswa_id', ['siswa_id']);
        $addIndexIfMissing('nilai', 'idx_nilai_mata_pelajaran', ['mata_pelajaran']);
        $addIndexIfMissing('nilai', 'idx_nilai_semester', ['semester']);
        $addIndexIfMissing('nilai', 'idx_nilai_siswa_id_semester', ['siswa_id','semester']);

        // kalender_akademik
        $addIndexIfMissing('kalender_akademik', 'idx_kalender_mulai', ['tanggal_mulai']);
        $addIndexIfMissing('kalender_akademik', 'idx_kalender_selesai', ['tanggal_selesai']);
        $addIndexIfMissing('kalender_akademik', 'idx_kalender_jenis', ['jenis_kegiatan']);

        // berita
        $addIndexIfMissing('berita', 'idx_berita_tanggal', ['tanggal']);
        $addIndexIfMissing('berita', 'idx_berita_created_at', ['created_at']);
    }

    public function down()
    {
        // Drop only indexes we added (use explicit index names created in up()).
        $dropIdx = function(string $table, array $indexNames){
            if(!$this->db->tableExists($table)) return; 
            foreach($indexNames as $ix){
                try { $this->db->query("ALTER TABLE `{$table}` DROP INDEX `{$ix}`"); } catch(\Throwable $e) {}
            }
        };

        $dropIdx('users', ['idx_users_role','idx_users_email']);
        $dropIdx('walikelas', ['idx_walikelas_user_id','idx_walikelas_kelas']);
        $dropIdx('tb_siswa', ['idx_tb_siswa_kelas','idx_tb_siswa_siswa_id','idx_tb_siswa_kelas_siswa_id']);
        $dropIdx('absensi', [
            'idx_absensi_tanggal','idx_absensi_kelas','idx_absensi_siswa_id',
            'idx_absensi_tanggal_kelas','idx_absensi_siswa_id_tanggal'
        ]);
        $dropIdx('nilai', ['idx_nilai_siswa_id','idx_nilai_mata_pelajaran','idx_nilai_semester','idx_nilai_siswa_id_semester']);
        $dropIdx('kalender_akademik', ['idx_kalender_mulai','idx_kalender_selesai','idx_kalender_jenis']);
        $dropIdx('berita', ['idx_berita_tanggal','idx_berita_created_at']);
    }
}
