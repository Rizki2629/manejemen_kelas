<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\CacheHelper;

class ProfilSekolahModel extends Model
{
    protected $table = 'profil_sekolah';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'nama_sekolah',
        'npsn', 
        'alamat_sekolah',
        'kurikulum',
        'tahun_pelajaran',
        'nama_kepala_sekolah',
        'nip_kepala_sekolah'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'nama_sekolah' => 'required|max_length[255]',
        'npsn' => 'permit_empty|max_length[20]',
        'alamat_sekolah' => 'permit_empty',
        'kurikulum' => 'permit_empty|max_length[100]',
        'tahun_pelajaran' => 'permit_empty|max_length[20]',
        'nama_kepala_sekolah' => 'permit_empty|max_length[255]',
        'nip_kepala_sekolah' => 'permit_empty|max_length[30]'
    ];

    protected $validationMessages = [
        'nama_sekolah' => [
            'required' => 'Nama sekolah harus diisi',
            'max_length' => 'Nama sekolah maksimal 255 karakter'
        ],
        'npsn' => [
            'max_length' => 'NPSN maksimal 20 karakter'
        ],
        'kurikulum' => [
            'max_length' => 'Kurikulum maksimal 100 karakter'
        ],
        'tahun_pelajaran' => [
            'max_length' => 'Tahun pelajaran maksimal 20 karakter'
        ],
        'nama_kepala_sekolah' => [
            'max_length' => 'Nama kepala sekolah maksimal 255 karakter'
        ],
        'nip_kepala_sekolah' => [
            'max_length' => 'NIP kepala sekolah maksimal 30 karakter'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = ['clearCache'];
    protected $beforeUpdate = [];
    protected $afterUpdate = ['clearCache'];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = ['clearCache'];

    protected $cacheHelper;

    public function __construct()
    {
        parent::__construct();
        $this->cacheHelper = new CacheHelper();
    }

    /**
     * Get profil sekolah with caching (should only be one record)
     * 
     * @return array|null
     */
    public function getProfilSekolah()
    {
        return $this->cacheHelper->getProfilSekolah(function() {
            return $this->first();
        });
    }

    /**
     * Get profil sekolah without cache (for admin operations)
     * 
     * @return array|null
     */
    public function getProfilSekolahDirect()
    {
        return $this->first();
    }

    /**
     * Update or insert profil sekolah
     * 
     * @param array $data
     * @return bool
     */
    public function saveProfilSekolah($data)
    {
        $existing = $this->first();
        
        if ($existing) {
            // Update existing record
            $result = $this->update($existing['id'], $data);
        } else {
            // Insert new record
            $result = $this->insert($data);
        }
        
        // Clear cache after save
        if ($result) {
            $this->cacheHelper->invalidateProfilSekolah();
        }
        
        return $result;
    }

    /**
     * Callback to clear cache after database operations
     * 
     * @param array $data
     * @return array
     */
    protected function clearCache(array $data)
    {
        $this->cacheHelper->invalidateProfilSekolah();
        return $data;
    }
}
