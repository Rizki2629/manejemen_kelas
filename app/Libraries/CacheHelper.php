<?php

namespace App\Libraries;

use CodeIgniter\Cache\CacheInterface;

/**
 * Cache Helper Library
 * 
 * Provides centralized caching functionality for the application
 * with predefined TTL values for different data types.
 */
class CacheHelper
{
    protected $cache;
    
    // Cache TTL constants (in seconds)
    public const PROFIL_SEKOLAH_TTL = 86400;    // 24 hours - school profile rarely changes
    public const KALENDER_AKADEMIK_TTL = 3600;  // 1 hour - academic calendar
    public const GURU_LIST_TTL = 3600;          // 1 hour - teacher list
    public const BERITA_TTL = 1800;             // 30 minutes - news
    public const CONFIG_TTL = 86400;            // 24 hours - configuration data

    public function __construct()
    {
        $this->cache = \Config\Services::cache();
    }

    /**
     * Get cached data or execute callback to generate and cache it
     *
     * @param string $key Cache key
     * @param callable $callback Function to execute if cache miss
     * @param int $ttl Time to live in seconds
     * @return mixed Cached or generated data
     */
    public function remember(string $key, callable $callback, int $ttl = 3600)
    {
        $data = $this->cache->get($key);
        
        if ($data === null) {
            $data = $callback();
            if ($data !== null) {
                $this->cache->save($key, $data, $ttl);
            }
        }
        
        return $data;
    }

    /**
     * Cache school profile data
     *
     * @param callable $callback Function to get school profile data
     * @return mixed School profile data
     */
    public function getProfilSekolah(callable $callback)
    {
        return $this->remember('profil_sekolah', $callback, self::PROFIL_SEKOLAH_TTL);
    }

    /**
     * Cache academic calendar data
     *
     * @param callable $callback Function to get calendar data
     * @return mixed Calendar data
     */
    public function getKalenderAkademik(callable $callback)
    {
        return $this->remember('kalender_akademik', $callback, self::KALENDER_AKADEMIK_TTL);
    }

    /**
     * Cache teacher list data
     *
     * @param callable $callback Function to get teacher data
     * @return mixed Teacher data
     */
    public function getGuruList(callable $callback)
    {
        return $this->remember('guru_list', $callback, self::GURU_LIST_TTL);
    }

    /**
     * Cache news data
     *
     * @param callable $callback Function to get news data  
     * @param int $limit Number of news items
     * @return mixed News data
     */
    public function getBerita(callable $callback, int $limit = 10)
    {
        $key = "berita_list_{$limit}";
        return $this->remember($key, $callback, self::BERITA_TTL);
    }

    /**
     * Invalidate school profile cache (when updated)
     */
    public function invalidateProfilSekolah()
    {
        $this->cache->delete('profil_sekolah');
    }

    /**
     * Invalidate academic calendar cache
     */
    public function invalidateKalenderAkademik()
    {
        $this->cache->delete('kalender_akademik');
    }

    /**
     * Invalidate teacher list cache
     */
    public function invalidateGuruList()
    {
        $this->cache->delete('guru_list');
    }

    /**
     * Invalidate news cache
     */
    public function invalidateBerita()
    {
        $keys = ['berita_list_10', 'berita_list_5', 'berita_list_20']; // Common limits
        foreach ($keys as $key) {
            $this->cache->delete($key);
        }
    }

    /**
     * Clear all application caches
     */
    public function clearAll()
    {
        $this->invalidateProfilSekolah();
        $this->invalidateKalenderAkademik(); 
        $this->invalidateGuruList();
        $this->invalidateBerita();
    }

    /**
     * Get cache statistics (if supported by handler)
     *
     * @return array Cache statistics
     */
    public function getStats()
    {
        if (method_exists($this->cache, 'getCacheInfo')) {
            return $this->cache->getCacheInfo();
        }
        
        return ['message' => 'Cache statistics not available for current handler'];
    }
}