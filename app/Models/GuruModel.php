<?php

namespace App\Models;

use CodeIgniter\Model;

class GuruModel extends Model
{
    protected $table = 'guru';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'nama',
        'nuptk',
        'jk',
        'tempat_lahir',
        'tanggal_lahir',
        'nip',
        'status_kepegawaian',
        'jenis_ptk',
        'agama',
        'alamat_jalan',
        'rt',
        'rw',
        'nama_dusun',
        'desa_kelurahan',
        'kecamatan',
        'kode_pos',
        'telepon',
        'hp',
        'email',
        'tugas_tambahan',
        'sk_cpns',
        'tanggal_cpns',
        'sk_pengangkatan',
        'tmt_pengangkatan',
        'lembaga_pengangkatan',
        'pangkat_golongan',
        'sumber_gaji',
        'nama_ibu_kandung',
        'status_perkawinan',
        'nama_suami_istri',
        'nip_suami_istri',
        'pekerjaan_suami_istri',
        'tmt_pns',
        'sudah_lisensi_kepala_sekolah',
        'pernah_diklat_kepengawasan',
        'keahlian_braille',
        'keahlian_bahasa_isyarat',
        'npwp',
        'nama_wajib_pajak',
        'kewarganegaraan',
        'bank',
        'nomor_rekening_bank',
        'rekening_atas_nama',
        'nik',
        'no_kk',
        'karpeg',
        'karis_karsu',
        'tugas_mengajar'
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
        'nuptk' => 'permit_empty|numeric|max_length[20]',
        'jk' => 'required|in_list[L,P]',
        'tempat_lahir' => 'required|max_length[50]',
        'tanggal_lahir' => 'required|valid_date',
        'nip' => 'permit_empty|numeric|max_length[20]',
        'email' => 'permit_empty|valid_email|max_length[100]',
        'hp' => 'permit_empty|max_length[15]',
        'nik' => 'permit_empty|numeric|max_length[20]',
    ];

    protected $validationMessages = [
        'nama' => [
            'required' => 'Nama guru harus diisi',
            'min_length' => 'Nama guru minimal 3 karakter',
            'max_length' => 'Nama guru maksimal 100 karakter'
        ],
        'jk' => [
            'required' => 'Jenis kelamin harus dipilih',
            'in_list' => 'Jenis kelamin harus L atau P'
        ],
        'tempat_lahir' => [
            'required' => 'Tempat lahir harus diisi',
            'max_length' => 'Tempat lahir maksimal 50 karakter'
        ],
        'tanggal_lahir' => [
            'required' => 'Tanggal lahir harus diisi',
            'valid_date' => 'Format tanggal lahir tidak valid'
        ],
        'email' => [
            'valid_email' => 'Format email tidak valid',
            'max_length' => 'Email maksimal 100 karakter'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Get all teachers with optional search
     */
    public function getGuru($search = null, $limit = null, $offset = null)
    {
        $builder = $this->builder();
        
        if ($search) {
            $builder->groupStart()
                    ->like('nama_lengkap', $search)
                    ->orLike('nip', $search)
                    ->orLike('jabatan', $search)
                    ->groupEnd();
        }
        
        if ($limit) {
            $builder->limit($limit, $offset);
        }
        
        return $builder->get()->getResultArray();
    }

    /**
     * Get teacher by NIP
     */
    public function getByNip($nip)
    {
        return $this->where('nip', $nip)->first();
    }

    /**
     * Get teachers by jabatan (position)
     */
    public function getByJabatan($jabatan)
    {
        return $this->like('jabatan', $jabatan)->findAll();
    }

    /**
     * Get homeroom teachers (wali kelas)
     */
    public function getWaliKelas()
    {
        return $this->like('jabatan', 'Wali Kelas')->findAll();
    }

    /**
     * Get teacher full name with title
     */
    public function getFullName($id)
    {
        $guru = $this->find($id);
        if ($guru) {
            return trim($guru['nama_lengkap'] . ' ' . $guru['gelar']);
        }
        return null;
    }

    /**
     * Count teachers by gender
     */
    public function countByGender()
    {
        return [
            'laki_laki' => $this->where('jenis_kelamin', 'Laki-laki')->countAllResults(),
            'perempuan' => $this->where('jenis_kelamin', 'Perempuan')->countAllResults()
        ];
    }
}
