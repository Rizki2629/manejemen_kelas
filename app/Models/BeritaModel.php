<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\CacheHelper;

class BeritaModel extends Model
{
    protected $table = 'berita';
    protected $primaryKey = 'id';
    protected $allowedFields = ['judul', 'isi', 'tanggal', 'gambar'];
    
    // Use timestamps
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Callbacks
    protected $allowCallbacks = true;
    protected $afterInsert = ['clearCache'];
    protected $afterUpdate = ['clearCache'];
    protected $afterDelete = ['clearCache'];

    protected $cacheHelper;

    public function __construct()
    {
        parent::__construct();
        $this->cacheHelper = new CacheHelper();
    }

    /**
     * Get latest berita with caching
     * 
     * @param int $limit
     * @return array
     */
    public function getLatestBerita($limit = 5)
    {
        return $this->cacheHelper->getBerita(function() use ($limit) {
            return $this->orderBy('tanggal', 'desc')
                       ->limit($limit)
                       ->findAll();
        }, $limit);
    }

    /**
     * Get latest berita without cache (for admin)
     * 
     * @param int $limit
     * @return array
     */
    public function getLatestBeritaDirect($limit = 5)
    {
        return $this->orderBy('tanggal', 'desc')
                   ->limit($limit)
                   ->findAll();
    }

    /**
     * Get berita by month with caching
     * 
     * @param int $year
     * @param int $month
     * @return array
     */
    public function getBeritaByMonth($year, $month)
    {
        $cacheKey = "berita_month_{$year}_{$month}";
        
        return $this->cacheHelper->remember($cacheKey, function() use ($year, $month) {
            return $this->where('YEAR(tanggal)', $year)
                       ->where('MONTH(tanggal)', $month)
                       ->orderBy('tanggal', 'desc')
                       ->findAll();
        }, CacheHelper::BERITA_TTL);
    }

    /**
     * Callback to clear cache after database operations
     * 
     * @param array $data
     * @return array
     */
    protected function clearCache(array $data)
    {
        $this->cacheHelper->invalidateBerita();
        return $data;
    }
}
