<?php
namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\GuruModel;

class ImportGuru extends BaseCommand
{
    protected $group       = 'Guru';
    protected $name        = 'guru:import';
    protected $description = 'Import data guru dari file data-guru.json ke tabel guru';
    protected $usage       = 'guru:import [--force]';

    public function run(array $params)
    {
        $force = in_array('--force', $params, true);
        $model = new GuruModel();
        $existing = $model->countAll();
        if ($existing > 0 && !$force) {
            CLI::write("Tabel guru sudah berisi {$existing} baris. Gunakan --force untuk menambah lagi.", 'yellow');
            return; 
        }

        $file = ROOTPATH . 'data-guru.json';
        if (!is_file($file)) {
            CLI::error('File data-guru.json tidak ditemukan di root project.');
            return; 
        }

        $json = file_get_contents($file);
        $dataGuru = json_decode($json, true, 512, JSON_THROW_ON_ERROR | JSON_BIGINT_AS_STRING);
        if (!is_array($dataGuru)) {
            CLI::error('Format JSON tidak valid.');
            return; 
        }

        $inserted = 0; $failed = 0; $batch = [];
        $map = function(array $t){
            // Ambil hanya kolom utama yang dipakai sistem saat ini
            return [
                'nama' => $t['Nama'] ?? null,
                'nuptk' => (string)($t['NUPTK'] ?? null) ?: null,
                'jk' => $t['JK'] ?? null,
                'tempat_lahir' => $t['Tempat Lahir'] ?? null,
                'tanggal_lahir' => $t['Tanggal Lahir'] ?? null,
                'nip' => (string)($t['NIP'] ?? null) ?: null,
                'status_kepegawaian' => $t['Status Kepegawaian'] ?? null,
                'jenis_ptk' => $t['Jenis PTK'] ?? null,
                'agama' => $t['Agama'] ?? null,
                'alamat_jalan' => $t['Alamat Jalan'] ?? null,
                'rt' => $t['RT'] ?? null,
                'rw' => $t['RW'] ?? null,
                'desa_kelurahan' => $t['Desa/Kelurahan'] ?? null,
                'kecamatan' => $t['Kecamatan'] ?? null,
                'hp' => $t['HP'] ?? null,
                'email' => $t['Email'] ?? null,
                'tugas_mengajar' => $t['Tugas Tambahan'] ?? null,
                'status_keaktifan' => 'Aktif',
                'npwp' => (string)($t['NPWP'] ?? null) ?: null,
                'nik' => (string)($t['NIK'] ?? null) ?: null,
            ];
        };

        foreach ($dataGuru as $row) {
            try {
                $batch[] = $map($row);
                if (count($batch) === 100) {
                    $model->ignore(true)->insertBatch($batch); // abaikan duplikat unique jika ada
                    $inserted += count($batch);
                    $batch = [];
                    CLI::write("Imported {$inserted} ...", 'green');
                }
            } catch (\Throwable $e) {
                $failed++;
            }
        }
        if ($batch) {
            try {
                $model->ignore(true)->insertBatch($batch);
                $inserted += count($batch);
            } catch (\Throwable $e) {
                $failed += count($batch);
            }
        }

        CLI::write("Selesai. Berhasil: {$inserted}, Gagal: {$failed}", 'cyan');
        if ($failed) {
            CLI::write('Lihat log untuk detail error.', 'yellow');
        }
    }
}
