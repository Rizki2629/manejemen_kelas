<?php
namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

/**
 * Audit duplicate tb_siswa nisn and habit_logs distribution.
 * Usage:
 *  php spark habits:audit-students                -> list duplicates & counts
 *  php spark habits:audit-students --fix-nisn=XXXX -> consolidate all habit_logs of duplicate nisn to smallest tb_siswa.id
 */
class AuditHabitStudentIds extends BaseCommand
{
    protected $group = 'habits';
    protected $name = 'habits:audit-students';
    protected $description = 'Audit duplikasi nisn di tb_siswa dan distribusi habit_logs; opsi konsolidasi.';
    protected $usage = 'habits:audit-students [--fix-nisn=1234567890]';
    protected $options = [
        '--fix-nisn' => 'Konsolidasikan nisn duplikat: pindahkan habit_logs ke id terkecil & tandai baris lain deleted_at'
    ];

    public function run(array $params)
    {
        $fixNisn = CLI::getOption('fix-nisn');
        $db = Database::connect();
        CLI::write('== AUDIT DUPLIKASI NISN tb_siswa ==', 'yellow');

        $dupSql = "SELECT nisn, GROUP_CONCAT(id ORDER BY id ASC) ids, COUNT(*) cnt
                   FROM tb_siswa
                   WHERE nisn IS NOT NULL AND nisn <> '' AND deleted_at IS NULL
                   GROUP BY nisn
                   HAVING cnt > 1";
        $dups = $db->query($dupSql)->getResultArray();
        if (!$dups) {
            CLI::write('Tidak ada duplikasi nisn aktif.', 'green');
        } else {
            CLI::write('Ditemukan '.count($dups).' nisn duplikat:', 'red');
            foreach ($dups as $d) {
                $ids = explode(',', $d['ids']);
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $counts = $db->query('SELECT student_id, COUNT(*) c FROM habit_logs WHERE student_id IN ('.$placeholders.') GROUP BY student_id', $ids)->getResultArray();
                $countMap = [];
                foreach ($counts as $c) { $countMap[$c['student_id']] = $c['c']; }
                CLI::write(' - nisn '.$d['nisn'].' -> ids: '.$d['ids'].' | logs: '.json_encode($countMap));
            }
        }

        if ($fixNisn) {
            $target = null;
            $row = null;
            foreach ($dups as $d) {
                if ($d['nisn'] === $fixNisn) { $row = $d; break; }
            }
            if (!$row) {
                CLI::write('NISN '.$fixNisn.' tidak ditemukan sebagai duplikat atau sudah bersih.', 'red');
                return;
            }
            $ids = explode(',', $row['ids']);
            $target = (int)$ids[0]; // pilih id terkecil sebagai canonical
            CLI::write('Konsolidasi nisn '.$fixNisn.' ke id canonical '.$target, 'yellow');
            // Pindahkan habit_logs dari id lain
            $moved = 0;
            foreach ($ids as $id) {
                $idInt = (int)$id;
                if ($idInt === $target) continue;
                $affected = $db->query('UPDATE habit_logs SET student_id = ? WHERE student_id = ?', [$target, $idInt]);
                $moved += $db->affectedRows();
                // Tandai baris lain sebagai deleted_at jika belum
                $db->query('UPDATE tb_siswa SET deleted_at = NOW() WHERE id = ? AND deleted_at IS NULL', [$idInt]);
            }
            CLI::write('Total habit_logs dipindah: '.$moved, 'green');
            CLI::write('Selesai konsolidasi. Jalankan lagi audit untuk verifikasi.', 'green');
        }
    }
}
