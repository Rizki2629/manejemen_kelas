<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SiswaSeeder extends Seeder
{
    public function run()
    {
        // Read JSON file
        $jsonPath = ROOTPATH . 'data.json';
        
        if (!file_exists($jsonPath)) {
            echo "File data.json not found!\n";
            return;
        }

        $jsonData = file_get_contents($jsonPath);
        $siswaData = json_decode($jsonData, true);

        if (!$siswaData) {
            echo "Failed to parse JSON data!\n";
            return;
        }

        echo "Loading " . count($siswaData) . " siswa records...\n";

        // Clear existing data first
        $this->db->table('tb_siswa')->truncate();
        echo "Cleared existing data.\n";

        // Process data in batches to avoid memory issues
        $batchSize = 50;
        $batches = array_chunk($siswaData, $batchSize);
        
        foreach ($batches as $batchIndex => $batch) {
            $processedData = [];
            
            foreach ($batch as $siswa) {
                // Map JSON fields to database fields (remove "No" field, add auto ID)
                $processedData[] = [
                    // Skip 'No' field - will use auto-increment ID
                    'nama' => $siswa['Nama'] ?? null,
                    'nipd' => isset($siswa['NIPD']) ? (string)$siswa['NIPD'] : null,
                    'jk' => $siswa['JK'] ?? null,
                    'nisn' => isset($siswa['NISN']) ? (string)$siswa['NISN'] : null,
                    'tempat_lahir' => $siswa['Tempat Lahir'] ?? null,
                    'tanggal_lahir' => $siswa['Tanggal Lahir'] ?? null,
                    'nik' => isset($siswa['NIK']) ? (string)$siswa['NIK'] : null,
                    'agama' => $siswa['Agama'] ?? null,
                    'alamat' => $siswa['Alamat'] ?? null,
                    'rt' => isset($siswa['RT']) ? (string)$siswa['RT'] : null,
                    'rw' => isset($siswa['RW']) ? (string)$siswa['RW'] : null,
                    'dusun' => $siswa['Dusun'] ?? null,
                    'kelurahan' => $siswa['Kelurahan'] ?? null,
                    'kecamatan' => $siswa['Kecamatan'] ?? null,
                    'kode_pos' => isset($siswa['Kode Pos']) ? (string)$siswa['Kode Pos'] : null,
                    'jenis_tinggal' => $siswa['Jenis Tinggal'] ?? null,
                    'alat_transportasi' => $siswa['Alat Transportasi'] ?? null,
                    'telepon' => $siswa['Telepon'] ?? null,
                    'hp' => $siswa['HP'] ?? null,
                    'email' => $siswa['E-Mail'] ?? null,
                    'skhun' => $siswa['SKHUN'] ?? null,
                    'penerima_kps' => $siswa['Penerima KPS'] ?? null,
                    'no_kps' => $siswa['No. KPS'] ?? null,
                    'nama_ayah' => $siswa['Nama Ayah'] ?? null,
                    'tahun_lahir_ayah' => isset($siswa['Tahun Lahir Ayah']) && is_numeric($siswa['Tahun Lahir Ayah']) ? $siswa['Tahun Lahir Ayah'] : null,
                    'pendidikan_ayah' => $siswa['Pendidikan Ayah'] ?? null,
                    'pekerjaan_ayah' => $siswa['Pekerjaan Ayah'] ?? null,
                    'penghasilan_ayah' => $siswa['Penghasilan Ayah'] ?? null,
                    'nik_ayah' => isset($siswa['NIK Ayah']) ? (string)$siswa['NIK Ayah'] : null,
                    'nama_ibu' => $siswa['Nama Ibu'] ?? null,
                    'tahun_lahir_ibu' => isset($siswa['Tahun Lahir Ibu']) && is_numeric($siswa['Tahun Lahir Ibu']) ? $siswa['Tahun Lahir Ibu'] : null,
                    'pendidikan_ibu' => $siswa['Pendidikan Ibu'] ?? null,
                    'pekerjaan_ibu' => $siswa['Pekerjaan Ibu'] ?? null,
                    'penghasilan_ibu' => $siswa['Penghasilan Ibu'] ?? null,
                    'nik_ibu' => isset($siswa['NIK Ibu']) ? (string)$siswa['NIK Ibu'] : null,
                    'nama_wali' => $siswa['Nama Wali'] ?? null,
                    'tahun_lahir_wali' => (isset($siswa['Tahun Lahir Wali']) && !empty($siswa['Tahun Lahir Wali']) && is_numeric($siswa['Tahun Lahir Wali'])) ? $siswa['Tahun Lahir Wali'] : null,
                    'pendidikan_wali' => $siswa['Pendidikan Wali'] ?? null,
                    'pekerjaan_wali' => $siswa['Pekerjaan Wali'] ?? null,
                    'penghasilan_wali' => $siswa['Penghasilan Wali'] ?? null,
                    'nik_wali' => isset($siswa['NIK Wali']) && !empty($siswa['NIK Wali']) ? (string)$siswa['NIK Wali'] : null,
                    'kelas' => $siswa['Class'] ?? null,
                    'no_peserta_ujian_nasional' => $siswa['No Peserta Ujian Nasional'] ?? null,
                    'no_seri_ijazah' => $siswa['No Seri Ijazah'] ?? null,
                    'penerima_kip' => $siswa['Penerima KIP'] ?? null,
                    'nomor_kip' => $siswa['Nomor KIP'] ?? null,
                    'nama_di_kip' => (isset($siswa['Nama di KIP']) && $siswa['Nama di KIP'] !== 0 && !empty($siswa['Nama di KIP'])) ? (string)$siswa['Nama di KIP'] : null,
                    'nomor_kks' => $siswa['Nomor KKS'] ?? null,
                    'no_registrasi_akta_lahir' => $siswa['No Registrasi Akta Lahir'] ?? null,
                    'bank' => $siswa['Bank'] ?? null,
                    'nomor_rekening_bank' => $siswa['Nomor Rekening Bank'] ?? null,
                    'rekening_atas_nama' => $siswa['Rekening Atas Nama'] ?? null,
                    'layak_pip' => $siswa['Layak PIP (usulan dari sekolah)'] ?? null,
                    'alasan_layak_pip' => $siswa['Alasan Layak PIP'] ?? null,
                    'kebutuhan_khusus' => $siswa['Kebutuhan Khusus'] ?? null,
                    'sekolah_asal' => $siswa['Sekolah Asal'] ?? null,
                    'anak_ke_berapa' => isset($siswa['Anak ke-berapa']) && is_numeric($siswa['Anak ke-berapa']) ? (int)$siswa['Anak ke-berapa'] : null,
                    'lintang' => $siswa['Lintang'] ?? null,
                    'bujur' => $siswa['Bujur'] ?? null,
                    'no_kk' => isset($siswa['No KK']) ? (string)$siswa['No KK'] : null,
                    'berat_badan' => isset($siswa['Berat Badan']) && is_numeric($siswa['Berat Badan']) ? (float)$siswa['Berat Badan'] : null,
                    'tinggi_badan' => isset($siswa['Tinggi Badan']) && is_numeric($siswa['Tinggi Badan']) ? (float)$siswa['Tinggi Badan'] : null,
                    'lingkar_kepala' => isset($siswa['Lingkar Kepala']) && is_numeric($siswa['Lingkar Kepala']) ? (float)$siswa['Lingkar Kepala'] : null,
                    'jml_saudara_kandung' => isset($siswa['Jml. Saudara\nKandung']) && is_numeric($siswa['Jml. Saudara\nKandung']) ? (int)$siswa['Jml. Saudara\nKandung'] : null,
                    'jarak_rumah_ke_sekolah' => isset($siswa['Jarak Rumah\nke Sekolah (KM)']) && is_numeric($siswa['Jarak Rumah\nke Sekolah (KM)']) ? (float)$siswa['Jarak Rumah\nke Sekolah (KM)'] : null,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
            
            // Insert batch
            $this->db->table('tb_siswa')->insertBatch($processedData);
            echo "Batch " . ($batchIndex + 1) . " of " . count($batches) . " inserted (" . count($processedData) . " records).\n";
        }
        
        echo "Successfully imported " . count($siswaData) . " siswa records!\n";
        
        // Show statistics
        $totalSiswa = $this->db->table('tb_siswa')->countAll();
        $siswaLaki = $this->db->table('tb_siswa')->where('jk', 'L')->countAllResults();
        $siswaPerempuan = $this->db->table('tb_siswa')->where('jk', 'P')->countAllResults();
        
        echo "\nImport Statistics:\n";
        echo "Total Siswa: $totalSiswa\n";
        echo "Laki-laki: $siswaLaki\n";
        echo "Perempuan: $siswaPerempuan\n";
    }
}
