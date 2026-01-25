<?php

namespace App\Models;

use CodeIgniter\Model;

class SiswaModel extends Model
{
    protected $table = 'tb_siswa';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
        'nama',
        'nipd',
        'jk',
        'nisn',
        'tempat_lahir',
        'tanggal_lahir',
        'nik',
        'agama',
        'alamat',
        'rt',
        'rw',
        'dusun',
        'kelurahan',
        'kecamatan',
        'kode_pos',
        'jenis_tinggal',
        'alat_transportasi',
        'telepon',
        'hp',
        'email',
        'skhun',
        'penerima_kps',
        'no_kps',
        'nama_ayah',
        'tahun_lahir_ayah',
        'pendidikan_ayah',
        'pekerjaan_ayah',
        'penghasilan_ayah',
        'nik_ayah',
        'nama_ibu',
        'tahun_lahir_ibu',
        'pendidikan_ibu',
        'pekerjaan_ibu',
        'penghasilan_ibu',
        'nik_ibu',
        'nama_wali',
        'tahun_lahir_wali',
        'pendidikan_wali',
        'pekerjaan_wali',
        'penghasilan_wali',
        'nik_wali',
        'kelas',
        'no_peserta_ujian_nasional',
        'no_seri_ijazah',
        'penerima_kip',
        'nomor_kip',
        'nama_di_kip',
        'nomor_kks',
        'no_registrasi_akta_lahir',
        'bank',
        'nomor_rekening_bank',
        'rekening_atas_nama',
        'layak_pip',
        'alasan_layak_pip',
        'kebutuhan_khusus',
        'sekolah_asal',
        'anak_ke_berapa',
        'lintang',
        'bujur',
        'no_kk',
        'berat_badan',
        'tinggi_badan',
        'lingkar_kepala',
        'jml_saudara_kandung',
        'jarak_rumah_ke_sekolah'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'nama' => 'required|min_length[3]|max_length[100]',
        'jk' => 'required|in_list[L,P]',
        'tanggal_lahir' => 'permit_empty|valid_date',
        'email' => 'permit_empty|valid_email'
    ];

    protected $validationMessages = [
        'nama' => [
            'required' => 'Nama siswa harus diisi',
            'min_length' => 'Nama siswa minimal 3 karakter',
            'max_length' => 'Nama siswa maksimal 100 karakter'
        ],
        'jk' => [
            'required' => 'Jenis kelamin harus dipilih',
            'in_list' => 'Jenis kelamin harus L atau P'
        ],
        'tanggal_lahir' => [
            'valid_date' => 'Format tanggal lahir tidak valid'
        ],
        'email' => [
            'valid_email' => 'Format email tidak valid'
        ]
    ];

    /**
     * Search siswa by name, NISN, or class
     *
     * @param string $search
     * @return array
     */
    public function searchSiswa(string $search): array
    {
        return $this->like('nama', $search)
                    ->orLike('nisn', $search)
                    ->orLike('kelas', $search)
                    ->findAll();
    }

    /**
     * Get siswa by class
     *
     * @param string $kelas
     * @return array
     */
    public function getSiswaByClass(string $kelas): array
    {
        return $this->where('kelas', $kelas)->findAll();
    }

    /**
     * Get siswa statistics
     *
     * @return array
     */
    public function getStatistics(): array
    {
        $totalSiswa = $this->countAllResults();
        $siswaLaki = $this->where('jk', 'L')->countAllResults();
        $siswaPerempuan = $this->where('jk', 'P')->countAllResults();
        
        return [
            'total' => $totalSiswa,
            'laki_laki' => $siswaLaki,
            'perempuan' => $siswaPerempuan
        ];
    }

    /**
     * Get siswa by gender
     *
     * @param string $gender
     * @return array
     */
    public function getSiswaByGender(string $gender): array
    {
        return $this->where('jk', $gender)->findAll();
    }
}
