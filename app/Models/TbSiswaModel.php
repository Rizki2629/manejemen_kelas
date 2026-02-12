<?php

namespace App\Models;

use CodeIgniter\Model;

class TbSiswaModel extends Model
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

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    /**
     * Get all active classes (cached for 5 minutes)
     */
    public function getActiveClasses()
    {
        $cache = \Config\Services::cache();
        $cacheKey = 'active_classes_list';
        
        $classes = $cache->get($cacheKey);
        if ($classes === null) {
            $classes = $this->select('kelas')
                        ->where('kelas !=', 'Lulus')
                        ->where('deleted_at IS NULL')
                        ->groupBy('kelas')
                        ->orderBy('kelas', 'ASC')
                        ->findAll();
            
            $cache->save($cacheKey, $classes, 300); // Cache for 5 minutes
        }
        
        return $classes;
    }

    /**
     * Get students by class
     */
    public function getStudentsByClass($kelas)
    {
        return $this->where('kelas', $kelas)
                    ->where('deleted_at IS NULL')
                    ->orderBy('nama', 'ASC')
                    ->findAll();
    }
}
