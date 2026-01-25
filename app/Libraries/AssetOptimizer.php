<?php

namespace App\Libraries;

/**
 * Asset Optimization Library
 * 
 * Handles CSS and JS minification, bundling and optimization
 * for better performance and faster loading times.
 */
class AssetOptimizer
{
    protected $cssPath;
    protected $jsPath;
    protected $optimizedPath;
    
    public function __construct()
    {
        $this->cssPath = FCPATH . 'css/';
        $this->jsPath = FCPATH . 'js/';
        $this->optimizedPath = FCPATH . 'assets/optimized/';
        
        // Create optimized directory if it doesn't exist
        if (!is_dir($this->optimizedPath)) {
            mkdir($this->optimizedPath, 0755, true);
        }
    }

    /**
     * Minify CSS content by removing whitespace and comments
     *
     * @param string $css CSS content
     * @return string Minified CSS
     */
    public function minifyCSS($css)
    {
        // Remove comments
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        
        // Remove whitespace
        $css = str_replace(["\r\n", "\r", "\n", "\t"], '', $css);
        $css = preg_replace('/\s+/', ' ', $css);
        $css = str_replace(['; ', ' {', '{ ', ' }', '} ', ': ', ', '], [';', '{', '{', '}', '}', ':', ','], $css);
        
        // Remove trailing semicolon before }
        $css = str_replace(';}', '}', $css);
        
        return trim($css);
    }

    /**
     * Minify JavaScript content by removing whitespace and comments
     *
     * @param string $js JavaScript content
     * @return string Minified JavaScript
     */
    public function minifyJS($js)
    {
        // Remove single line comments (but preserve URLs)
        $js = preg_replace('/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\'|\")\/\/.*))/', '', $js);
        
        // Remove whitespace
        $js = str_replace(["\r\n", "\r", "\n", "\t"], '', $js);
        $js = preg_replace('/\s+/', ' ', $js);
        
        // Remove spaces around operators (be careful with strings)
        $js = preg_replace('/\s*([{}();,=+\-*\/])\s*/', '$1', $js);
        
        return trim($js);
    }

    /**
     * Bundle multiple CSS files into one optimized file
     *
     * @param array $files Array of CSS file names
     * @param string $outputFile Output file name
     * @return string Path to bundled file
     */
    public function bundleCSS($files, $outputFile = 'bundle.css')
    {
        $combinedCSS = '';
        $lastModified = 0;
        
        foreach ($files as $file) {
            $filePath = $this->cssPath . $file;
            if (file_exists($filePath)) {
                $content = file_get_contents($filePath);
                $combinedCSS .= $content . "\n";
                $lastModified = max($lastModified, filemtime($filePath));
            }
        }
        
        // Generate unique filename based on content hash
        $hash = substr(md5($combinedCSS), 0, 8);
        $outputFile = pathinfo($outputFile, PATHINFO_FILENAME) . '_' . $hash . '.css';
        $outputPath = $this->optimizedPath . $outputFile;
        
        // Only regenerate if files have been modified
        if (!file_exists($outputPath) || filemtime($outputPath) < $lastModified) {
            $minifiedCSS = $this->minifyCSS($combinedCSS);
            file_put_contents($outputPath, $minifiedCSS);
        }
        
        return 'assets/optimized/' . $outputFile;
    }

    /**
     * Bundle multiple JavaScript files into one optimized file
     *
     * @param array $files Array of JS file names
     * @param string $outputFile Output file name
     * @return string Path to bundled file
     */
    public function bundleJS($files, $outputFile = 'bundle.js')
    {
        $combinedJS = '';
        $lastModified = 0;
        
        foreach ($files as $file) {
            $filePath = $this->jsPath . $file;
            if (file_exists($filePath)) {
                $content = file_get_contents($filePath);
                $combinedJS .= $content . ";\n";
                $lastModified = max($lastModified, filemtime($filePath));
            }
        }
        
        // Generate unique filename based on content hash
        $hash = substr(md5($combinedJS), 0, 8);
        $outputFile = pathinfo($outputFile, PATHINFO_FILENAME) . '_' . $hash . '.js';
        $outputPath = $this->optimizedPath . $outputFile;
        
        // Only regenerate if files have been modified
        if (!file_exists($outputPath) || filemtime($outputPath) < $lastModified) {
            $minifiedJS = $this->minifyJS($combinedJS);
            file_put_contents($outputPath, $minifiedJS);
        }
        
        return 'assets/optimized/' . $outputFile;
    }

    /**
     * Get optimized asset URL with cache busting
     *
     * @param string $asset Asset path
     * @return string Optimized asset URL
     */
    public function asset($asset)
    {
        $fullPath = FCPATH . $asset;
        
        if (file_exists($fullPath)) {
            $timestamp = filemtime($fullPath);
            return base_url($asset . '?v=' . $timestamp);
        }
        
        return base_url($asset);
    }

    /**
     * Clean old optimized files (keep only latest versions)
     */
    public function cleanOldFiles()
    {
        $files = glob($this->optimizedPath . '*');
        $patterns = [];
        
        // Group files by base name
        foreach ($files as $file) {
            $basename = preg_replace('/_[a-f0-9]{8}\.(css|js)$/', '.$1', basename($file));
            $patterns[$basename][] = $file;
        }
        
        // Keep only the latest version of each file
        foreach ($patterns as $group) {
            if (count($group) > 1) {
                // Sort by modification time, keep the newest
                usort($group, function($a, $b) {
                    return filemtime($b) - filemtime($a);
                });
                
                // Delete old versions
                for ($i = 1; $i < count($group); $i++) {
                    unlink($group[$i]);
                }
            }
        }
    }

    /**
     * Get file size savings from optimization
     *
     * @param array $originalFiles Array of original file paths
     * @param string $optimizedFile Path to optimized file
     * @return array Size information
     */
    public function getSizeSavings($originalFiles, $optimizedFile)
    {
        $originalSize = 0;
        foreach ($originalFiles as $file) {
            if (file_exists($file)) {
                $originalSize += filesize($file);
            }
        }
        
        $optimizedSize = file_exists($optimizedFile) ? filesize($optimizedFile) : 0;
        $savings = $originalSize - $optimizedSize;
        $percentage = $originalSize > 0 ? round(($savings / $originalSize) * 100, 2) : 0;
        
        return [
            'original' => $originalSize,
            'optimized' => $optimizedSize,
            'savings' => $savings,
            'percentage' => $percentage
        ];
    }
}