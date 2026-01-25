<?php
namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

/**
 * Migrate habit_logs.student_id to use tb_siswa.id (canonical) instead of legacy siswa.id.
 * Strategy:
 * 1. Detect whether habit_logs currently mixes IDs (some matching siswa.id, some matching tb_siswa.id)
 * 2. For each legacy siswa row that has matching nisn in tb_siswa, map legacy habit_logs.student_id -> tb_siswa.id
 * 3. Backup rows to habit_logs_backup_<timestamp> before modifying (structure identical)
 * 4. Provide dry-run option.
 *
 * Usage:
 *  php spark habits:migrate-ids            (dry run only shows plan)
 *  php spark habits:migrate-ids --apply    (execute update)
 */
class MigrateHabitStudentIds extends BaseCommand
{
    protected $group = 'habits';
    protected $name = 'habits:migrate-ids';
    protected $description = 'Migrasi habit_logs.student_id agar memakai tb_siswa.id canonical.';
    protected $usage = 'habits:migrate-ids [--apply] [--limit n]';
    protected $options = [
        '--apply' => 'Jalankan eksekusi (tanpa opsi ini hanya dry-run)',
        '--limit' => 'Batasi jumlah update (untuk testing)' ,
    ];

    public function run(array $params)
    {
        // Gunakan mekanisme resmi CodeIgniter CLI untuk membaca opsi.
        // Dengan cara ini --apply atau --limit= / --limit <n> akan terdeteksi.
        $apply = CLI::getOption('apply') !== null; // boolean flag
        $limitOpt = CLI::getOption('limit');
        if ($limitOpt !== null) {
            $limitOpt = (int)$limitOpt;
        }

        $db = Database::connect();
        CLI::write('== MIGRASI habit_logs.student_id ke tb_siswa.id ==', 'yellow');
    CLI::write('Mode: '.($apply ? 'APPLY (akan mengubah data)' : 'DRY-RUN (tidak mengubah data)'), $apply ? 'red' : 'green');
        if ($limitOpt) {
            CLI::write('Limit update: '.$limitOpt, 'yellow');
        }

        // 1. Ambil mapping nisn -> tb_siswa.id
        $tbMap = [];
        $tbRows = $db->table('tb_siswa')->select('id, nisn')->where('deleted_at', null)->get()->getResultArray();
        foreach ($tbRows as $r) {
            if (!empty($r['nisn'])) $tbMap[$r['nisn']] = (int)$r['id'];
        }
        CLI::write('Total tb_siswa dengan nisn: '.count($tbMap));

        // 2. Join habit_logs -> legacy siswa untuk temukan baris yang perlu di-migrate
        $sql = "SELECT hl.id hl_id, hl.student_id legacy_id, s.nisn, hl.habit_id, hl.log_date
                FROM habit_logs hl
                JOIN siswa s ON s.id = hl.student_id
                LEFT JOIN tb_siswa t ON t.nisn = s.nisn
                WHERE t.id IS NOT NULL AND hl.student_id <> t.id";
        if ($limitOpt) {
            $sql .= ' LIMIT '.(int)$limitOpt;
        }
        $candidates = $db->query($sql)->getResultArray();
        CLI::write('Baris kandidat untuk migrasi: '.count($candidates), count($candidates) ? 'yellow' : 'green');

        if (empty($candidates)) {
            CLI::write('Tidak ada baris yang perlu di-migrasi. Selesai.', 'green');
            return;
        }

        // 3. Ringkas per legacy_id -> tb_id target
        $plan = [];
        foreach ($candidates as $row) {
            $nisn = $row['nisn'];
            $target = $tbMap[$nisn] ?? null;
            if (!$target) continue;
            $legacyId = (int)$row['legacy_id'];
            $plan[$legacyId]['target_tb_id'] = $target;
            $plan[$legacyId]['rows'][] = $row['hl_id'];
        }

        CLI::write('Rencana migrasi (legacy siswa.id -> tb_siswa.id): '.count($plan).' legacy id akan dipetakan');
        $show = 0;
        foreach ($plan as $legacy => $info) {
            CLI::write(" - $legacy => ".$info['target_tb_id'].' ('.count($info['rows']).' logs)');
            if (++$show >= 15) { CLI::write('   ... (terpotong)'); break; }
        }

        if (!$apply) {
            CLI::write('Dry-run selesai. Jalankan dengan --apply untuk eksekusi.', 'yellow');
            return;
        }

        // 4. Backup table
        $backupName = 'habit_logs_backup_'.date('Ymd_His');
        CLI::write('Membuat backup table: '.$backupName, 'yellow');
        $db->query('CREATE TABLE `'.$backupName.'` LIKE habit_logs');
        $db->query('INSERT INTO `'.$backupName.'` SELECT * FROM habit_logs');
        CLI::write('Backup selesai.', 'green');

        // 5. Eksekusi update per legacy id
        $updatedTotal = 0;
        foreach ($plan as $legacyId => $info) {
            $target = (int)$info['target_tb_id'];
            $idsChunk = array_chunk($info['rows'], 500);
            foreach ($idsChunk as $chunk) {
                $in = implode(',', array_map('intval', $chunk));
                $sqlUpdate = "UPDATE habit_logs SET student_id = $target WHERE id IN ($in)";
                $db->query($sqlUpdate);
                $updatedTotal += count($chunk);
            }
        }
        CLI::write('Total baris di-update: '.$updatedTotal, 'green');

        // 6. Validasi cepat
        $still = $db->query($sql)->getResultArray();
        CLI::write('Sisa baris mismatch (harus 0): '.count($still), count($still)?'red':'green');
        CLI::write('Selesai.', 'green');
    }
}
