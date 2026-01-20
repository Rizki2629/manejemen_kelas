<?php

namespace App\Models;

use CodeIgniter\Model;

class BukuKasusModel extends Model
{
    protected $table            = 'buku_kasus';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'siswa_id', 
        'guru_id', 
        'tanggal_kejadian', 
        'jenis_kasus', 
        'deskripsi_kasus', 
        'tindakan_yang_diambil', 
        'status',
        'tingkat_keparahan',
        'catatan_guru'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Get cases with related information (student name, class name, teacher name)
    public function getKasusWithDetails($kelasFilter = '', $statusFilter = '', $id = null)
    {
        $builder = $this->db->table('buku_kasus bk');
        $builder->select('bk.*, s.nama as nama_siswa, s.nipd as nis, s.kelas, s.jk as jenis_kelamin, s.tempat_lahir, s.tanggal_lahir, u.nama as nama_guru, u.email as email_guru, g.nip as nip_guru');
        $builder->join('tb_siswa s', 's.id = bk.siswa_id');
        $builder->join('users u', 'u.id = bk.guru_id');
        $builder->join('guru g', 'g.nip = u.username OR g.email = u.email', 'left');
        $builder->orderBy('bk.created_at', 'DESC');
        
        // Apply filters
        if (!empty($kelasFilter)) {
            $builder->where('s.kelas', $kelasFilter);
        }
        
        if (!empty($statusFilter)) {
            $builder->where('bk.status', $statusFilter);
        }
        
        if ($id !== null) {
            $builder->where('bk.id', $id);
            return $builder->get()->getRowArray();
        }
        
        return $builder->get()->getResultArray();
    }

    // Get cases for a specific class
    public function getKasusByKelas($kelas, $statusFilter = '')
    {
        $builder = $this->db->table('buku_kasus bk');
        $builder->select('bk.*, s.nama as nama_siswa, s.nipd as nis, s.kelas, s.jk as jenis_kelamin, s.tempat_lahir, s.tanggal_lahir, u.nama as nama_guru, u.email as email_guru, g.nip as nip_guru');
        $builder->join('tb_siswa s', 's.id = bk.siswa_id');
        $builder->join('users u', 'u.id = bk.guru_id');
        $builder->join('guru g', 'g.nip = u.username OR g.email = u.email', 'left');
        $builder->where('s.kelas', $kelas);
        $builder->orderBy('bk.created_at', 'DESC');
        
        if (!empty($statusFilter)) {
            $builder->where('bk.status', $statusFilter);
        }
        
        return $builder->get()->getResultArray();
    }

    // Get cases for a specific student
    public function getKasusBySiswa($siswaId)
    {
        $builder = $this->db->table('buku_kasus bk');
        $builder->select('bk.*, s.nama as nama_siswa, s.nipd as nis, s.kelas, s.jk as jenis_kelamin, s.tempat_lahir, s.tanggal_lahir, u.nama as nama_guru, u.email as email_guru, g.nip as nip_guru');
        $builder->join('tb_siswa s', 's.id = bk.siswa_id');
        $builder->join('users u', 'u.id = bk.guru_id');
        $builder->join('guru g', 'g.nip = u.username OR g.email = u.email', 'left');
        $builder->where('bk.siswa_id', $siswaId);
        $builder->orderBy('bk.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }
}
