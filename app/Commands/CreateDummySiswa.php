<?php

namespace App\Commands;

use App\Models\SiswaModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CreateDummySiswa extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'create:dummy-siswa';
    protected $description = 'Create/update a dummy student in tb_siswa for testing (default: NISN 123456, Desi, kelas 5A).';

    public function run(array $params)
    {
        $nisn  = trim((string)($params[0] ?? '123456'));
        $nama  = trim((string)($params[1] ?? 'Desi'));
        $kelas = trim((string)($params[2] ?? '5A'));
        $jk    = strtoupper(trim((string)($params[3] ?? 'P')));

        if ($nisn === '' || $nama === '' || $kelas === '') {
            CLI::write('Usage: php spark create:dummy-siswa [nisn] [nama] [kelas] [jk]', 'yellow');
            CLI::write('Example: php spark create:dummy-siswa 123456 Desi 5A P', 'yellow');
            return;
        }

        if (!in_array($jk, ['L', 'P'], true)) {
            CLI::write('JK harus L atau P.', 'red');
            return;
        }

        $model = new SiswaModel();
        $db = \Config\Database::connect();

        $existing = $model->withDeleted()->where('nisn', $nisn)->first();

        $data = [
            'nama'  => $nama,
            'nisn'  => $nisn,
            'kelas' => $kelas,
            'jk'    => $jk,
        ];

        if ($existing) {
            $ok = $model->update($existing['id'], $data);

            if (!$ok) {
                CLI::write('Gagal update dummy siswa.', 'red');
                foreach (($model->errors() ?? []) as $field => $msg) {
                    CLI::write("- {$field}: {$msg}", 'red');
                }
                return;
            }

            // If it was soft-deleted, restore it.
            if (!empty($existing['deleted_at'])) {
                $db->table('tb_siswa')->where('id', $existing['id'])->update(['deleted_at' => null]);
            }

            CLI::write('Dummy siswa sudah ada, data diperbarui:', 'green');
            CLI::write("- ID: {$existing['id']}");
            CLI::write("- NISN: {$nisn}");
            CLI::write("- Nama: {$nama}");
            CLI::write("- Kelas: {$kelas}");
            CLI::write("- JK: {$jk}");
            return;
        }

        try {
            $id = $model->insert($data, true);
        } catch (\Throwable $e) {
            // Common on some Postgres imports: sequence for tb_siswa.id is behind existing max(id)
            $msg = $e->getMessage() ?? '';
            if (stripos($msg, 'tb_siswa_pkey') !== false || stripos($msg, 'duplicate key value violates unique constraint') !== false) {
                try {
                    $db->query("SELECT setval(pg_get_serial_sequence('tb_siswa','id'), COALESCE((SELECT MAX(id) FROM tb_siswa), 0), true)");
                    $id = $model->insert($data, true);
                } catch (\Throwable $e2) {
                    CLI::write('Gagal membuat dummy siswa (setelah perbaikan sequence).', 'red');
                    CLI::write($e2->getMessage(), 'red');
                    return;
                }
            } else {
                CLI::write('Gagal membuat dummy siswa.', 'red');
                CLI::write($msg, 'red');
                return;
            }
        }

        if (!$id) {
            CLI::write('Gagal membuat dummy siswa.', 'red');
            foreach (($model->errors() ?? []) as $field => $msg) {
                CLI::write("- {$field}: {$msg}", 'red');
            }
            return;
        }

        CLI::write('Dummy siswa berhasil dibuat:', 'green');
        CLI::write("- ID: {$id}");
        CLI::write("- NISN: {$nisn}");
        CLI::write("- Nama: {$nama}");
        CLI::write("- Kelas: {$kelas}");
        CLI::write("- JK: {$jk}");
    }
}
