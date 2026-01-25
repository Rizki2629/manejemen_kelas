<?php

namespace App\Models;

use CodeIgniter\Model;

class AbsensiMobileModel extends Model
{
    protected $table            = 'absensi_mobile';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'siswa_id',
        'nisn',
        'nama',
        'kelas',
        'tanggal',
        'device_taken_at',
        'latitude',
        'longitude',
        'accuracy_m',
        'address',
        'photo_path',
        'ip_address',
        'user_agent',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
