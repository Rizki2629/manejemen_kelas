<?php

namespace App\Models;

use CodeIgniter\Model;

class ClassModel extends Model
{
    protected $table            = 'kelas';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['nama','tingkat','walikelas_id','tahun_ajaran','jumlah_siswa'];
    protected $useTimestamps    = true;
}
