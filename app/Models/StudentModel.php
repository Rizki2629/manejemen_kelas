<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentModel extends Model
{
    protected $table            = 'siswa';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['nama','nis','nisn','kelas_id','jenis_kelamin','tempat_lahir','tanggal_lahir','alamat','nama_ayah','nama_ibu','no_telepon_ortu','status'];
    protected $useTimestamps    = true;
}
