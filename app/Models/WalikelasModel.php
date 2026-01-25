<?php

namespace App\Models;

use CodeIgniter\Model;

class WalikelasModel extends Model
{
    protected $table            = 'walikelas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nama', 'nip', 'kelas'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get walikelas by kelas
     * 
     * @param string $kelas
     * @return array|null
     */
    public function getByKelas($kelas)
    {
        return $this->where('kelas', $kelas)->first();
    }

    /**
     * Get all walikelas with their classes
     * 
     * @return array
     */
    public function getAllWithKelas()
    {
        return $this->select('id, nama, nip, kelas')
                   ->orderBy('kelas', 'ASC')
                   ->findAll();
    }
}
