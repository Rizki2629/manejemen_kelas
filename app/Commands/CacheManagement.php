<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\CacheHelper;

class CacheManagement extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Optimization';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'cache:manage';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Manage application cache (clear, stats, warm-up)';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'cache:manage [action] [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [
        'action' => 'Action to perform: clear, stats, warmup'
    ];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [
        '--type' => 'Cache type to clear: profil, kalender, berita, guru, all'
    ];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        $action = $params[0] ?? CLI::prompt('Choose action', ['clear', 'stats', 'warmup']);
        $cacheHelper = new CacheHelper();
        
        switch ($action) {
            case 'clear':
                $this->clearCache($cacheHelper, $params);
                break;
                
            case 'stats':
                $this->showStats($cacheHelper);
                break;
                
            case 'warmup':
                $this->warmupCache($cacheHelper);
                break;
                
            default:
                CLI::error("Invalid action: {$action}");
                CLI::write("Available actions: clear, stats, warmup");
                break;
        }
    }
    
    /**
     * Clear cache based on type
     */
    private function clearCache(CacheHelper $cacheHelper, array $params)
    {
        $type = CLI::getOption('type') ?? $params[1] ?? 'all';
        
        CLI::write("Clearing cache type: {$type}", 'yellow');
        
        switch ($type) {
            case 'profil':
                $cacheHelper->invalidateProfilSekolah();
                CLI::write('School profile cache cleared', 'green');
                break;
                
            case 'kalender':
                $cacheHelper->invalidateKalenderAkademik();
                CLI::write('Academic calendar cache cleared', 'green');
                break;
                
            case 'berita':
                $cacheHelper->invalidateBerita();
                CLI::write('News cache cleared', 'green');
                break;
                
            case 'guru':
                $cacheHelper->invalidateGuruList();
                CLI::write('Teacher list cache cleared', 'green');
                break;
                
            case 'all':
                $cacheHelper->clearAll();
                CLI::write('All application cache cleared', 'green');
                break;
                
            default:
                CLI::error("Invalid cache type: {$type}");
                CLI::write("Available types: profil, kalender, berita, guru, all");
                break;
        }
    }
    
    /**
     * Show cache statistics
     */
    private function showStats(CacheHelper $cacheHelper)
    {
        CLI::write('Cache Statistics:', 'cyan');
        CLI::write('================', 'cyan');
        
        $stats = $cacheHelper->getStats();
        
        if (isset($stats['message'])) {
            CLI::write($stats['message'], 'yellow');
        } else {
            foreach ($stats as $key => $value) {
                if (is_array($value)) {
                    CLI::write("{$key}: " . json_encode($value));
                } else {
                    CLI::write("{$key}: {$value}");
                }
            }
        }
        
        // Show cache file information if using file cache
        $cachePath = WRITEPATH . 'cache/';
        if (is_dir($cachePath)) {
            $files = glob($cachePath . '*');
            $totalSize = 0;
            $fileCount = 0;
            
            foreach ($files as $file) {
                if (is_file($file)) {
                    $totalSize += filesize($file);
                    $fileCount++;
                }
            }
            
            CLI::newLine();
            CLI::write("Cache files: {$fileCount}");
            CLI::write("Total size: " . $this->formatBytes($totalSize));
        }
    }
    
    /**
     * Warm up cache with commonly used data
     */
    private function warmupCache(CacheHelper $cacheHelper)
    {
        CLI::write('Warming up cache...', 'yellow');
        
        try {
            // Warm up school profile
            $profilModel = new \App\Models\ProfilSekolahModel();
            $profilModel->getProfilSekolah();
            CLI::write('✓ School profile cache warmed up', 'green');
            
            // Warm up news
            $beritaModel = new \App\Models\BeritaModel();
            $beritaModel->getLatestBerita(5);
            $beritaModel->getLatestBerita(10);
            CLI::write('✓ News cache warmed up', 'green');
            
            // Warm up current month calendar
            $kalenderModel = new \App\Models\KalenderAkademikModel();
            $currentYear = date('Y');
            $currentMonth = date('m');
            $kalenderModel->getCalendarEvents($currentYear, $currentMonth);
            CLI::write('✓ Calendar cache warmed up', 'green');
            
            CLI::write('Cache warmup completed!', 'green');
            
        } catch (\Exception $e) {
            CLI::error('Error warming up cache: ' . $e->getMessage());
        }
    }
    
    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
