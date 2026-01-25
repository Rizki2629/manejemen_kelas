<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\AssetOptimizer;

class OptimizeAssets extends BaseCommand
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
    protected $name = 'assets:optimize';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Optimize CSS and JS assets for better performance';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'assets:optimize [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [
        '--clean' => 'Clean old optimized files',
        '--force' => 'Force regeneration of all assets'
    ];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        CLI::write('Starting asset optimization...', 'green');
        
        $optimizer = new AssetOptimizer();
        
        // Clean old files if requested
        if (array_key_exists('clean', $params) || CLI::getOption('clean')) {
            CLI::write('Cleaning old optimized files...');
            $optimizer->cleanOldFiles();
        }
        
        // Define common CSS files to bundle
        $cssFiles = [];
        $cssPath = FCPATH . 'css/';
        
        if (is_dir($cssPath)) {
            $cssFiles = array_filter(scandir($cssPath), function($file) {
                return pathinfo($file, PATHINFO_EXTENSION) === 'css';
            });
        }
        
        // Define common JS files to bundle
        $jsFiles = [];
        $jsPath = FCPATH . 'js/';
        
        if (is_dir($jsPath)) {
            $jsFiles = array_filter(scandir($jsPath), function($file) {
                return pathinfo($file, PATHINFO_EXTENSION) === 'js';
            });
        }
        
        // Optimize CSS files if they exist
        if (!empty($cssFiles)) {
            CLI::write('Optimizing CSS files...');
            $cssBundle = $optimizer->bundleCSS($cssFiles, 'app-styles');
            CLI::write("CSS bundle created: {$cssBundle}", 'yellow');
            
            // Show size savings
            $originalFiles = array_map(function($file) use ($cssPath) {
                return $cssPath . $file;
            }, $cssFiles);
            $savings = $optimizer->getSizeSavings($originalFiles, FCPATH . $cssBundle);
            CLI::write("Size reduction: {$savings['percentage']}% ({$savings['savings']} bytes saved)", 'cyan');
        } else {
            CLI::write('No CSS files found to optimize', 'yellow');
        }
        
        // Optimize JS files if they exist  
        if (!empty($jsFiles)) {
            CLI::write('Optimizing JavaScript files...');
            $jsBundle = $optimizer->bundleJS($jsFiles, 'app-scripts');
            CLI::write("JS bundle created: {$jsBundle}", 'yellow');
            
            // Show size savings
            $originalFiles = array_map(function($file) use ($jsPath) {
                return $jsPath . $file;
            }, $jsFiles);
            $savings = $optimizer->getSizeSavings($originalFiles, FCPATH . $jsBundle);
            CLI::write("Size reduction: {$savings['percentage']}% ({$savings['savings']} bytes saved)", 'cyan');
        } else {
            CLI::write('No JavaScript files found to optimize', 'yellow');
        }
        
        CLI::write('Asset optimization completed!', 'green');
        CLI::newLine();
        CLI::write('To use optimized assets in your views:', 'white');
        CLI::write('<?= optimized_css([\'file1.css\', \'file2.css\']) ?>', 'light_gray');
        CLI::write('<?= optimized_js([\'file1.js\', \'file2.js\']) ?>', 'light_gray');
    }
}
