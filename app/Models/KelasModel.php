<?php

namespace App\Models;

use CodeIgniter\Model;

class KelasModel extends Model
{
    protected $table            = 'kelas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama',
        'tingkat',
        'walikelas_id',
        'tahun_ajaran',
        'status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Get all active classes
    public function getKelasAktif()
    {
        // Some deployments may not yet have a 'status' column on table 'kelas'.
        // We detect column existence first to avoid SQL errors (Unknown column 'status').
        $fields = [];
        try {
            $fields = $this->db->getFieldNames($this->table);
        } catch (\Throwable $e) {
            // If introspection fails just return all ordered.
            return $this->orderBy('tingkat','ASC')->orderBy('nama','ASC')->findAll();
        }
        if (in_array('status', $fields)) {
            return $this->where('status','aktif')
                ->orderBy('tingkat','ASC')
                ->orderBy('nama','ASC')
                ->findAll();
        }
        return $this->orderBy('tingkat','ASC')->orderBy('nama','ASC')->findAll();
    }

    /**
     * Ensure standard classes 1A..6D exist (24 entries). Creates any missing ones.
     * It only sets columns that actually exist in the current table schema to avoid errors.
     */
    public function ensureStandardClasses(): void
    {
        try {
            $fields = $this->db->getFieldNames($this->table);
        } catch (\Throwable $e) {
            return; // cannot introspect
        }
        $existing = [];
        foreach ($this->select('nama')->findAll() as $row) {
            $existing[strtolower(trim($row['nama']))] = true;
        }
        $year = date('Y');
        $letters = ['A','B','C','D'];
        $batch = [];
        for ($i=1; $i<=6; $i++) {
            foreach ($letters as $L) {
                $name = "Kelas {$i}{$L}"; // keep space for readability
                if (!isset($existing[strtolower($name)])) {
                    $data = [];
                    if (in_array('nama',$fields)) $data['nama'] = $name;
                    if (in_array('tingkat',$fields)) $data['tingkat'] = $i;
                    if (in_array('tahun_ajaran',$fields)) $data['tahun_ajaran'] = $year.'/'.($year+1);
                    if (in_array('status',$fields)) $data['status'] = 'aktif';
                    $batch[] = $data;
                }
            }
        }
        if (!empty($batch)) {
            try { $this->insertBatch($batch); } catch (\Throwable $e) { /* ignore */ }
        }
    }

    // Get class with walikelas information
    public function getKelasWithWalikelas($id = null)
    {
        $builder = $this->db->table('kelas k');
        $builder->select('k.*, u.nama as nama_walikelas');
        $builder->join('users u', 'u.id = k.walikelas_id', 'left');
        
        if ($id !== null) {
            $builder->where('k.id', $id);
            return $builder->get()->getRowArray();
        }
        
        return $builder->get()->getResultArray();
    }

    // Get students in a class
    public function getSiswaByKelas($kelasId)
    {
        $builder = $this->db->table('tb_siswa s');
        $builder->select('s.*');
        $builder->where('s.kelas_id', $kelasId);
        $builder->where('s.deleted_at', null);
        
        return $builder->get()->getResultArray();
    }
}
