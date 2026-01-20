<?php

if (!function_exists('optimized_css')) {
    /**
     * Load optimized CSS bundle
     *
     * @param array $files Array of CSS files to bundle
     * @param string $name Bundle name (optional)
     * @return string HTML link tag for optimized CSS
     */
    function optimized_css($files, $name = 'bundle')
    {
        static $optimizer = null;
        
        if ($optimizer === null) {
            $optimizer = new \App\Libraries\AssetOptimizer();
        }
        
        $bundleUrl = $optimizer->bundleCSS($files, $name . '.css');
        return '<link rel="stylesheet" href="' . base_url($bundleUrl) . '">';
    }
}

if (!function_exists('optimized_js')) {
    /**
     * Load optimized JavaScript bundle
     *
     * @param array $files Array of JS files to bundle
     * @param string $name Bundle name (optional)
     * @return string HTML script tag for optimized JS
     */
    function optimized_js($files, $name = 'bundle')
    {
        static $optimizer = null;
        
        if ($optimizer === null) {
            $optimizer = new \App\Libraries\AssetOptimizer();
        }
        
        $bundleUrl = $optimizer->bundleJS($files, $name . '.js');
        return '<script src="' . base_url($bundleUrl) . '"></script>';
    }
}

if (!function_exists('cached_asset')) {
    /**
     * Get asset URL with cache busting parameter
     *
     * @param string $asset Asset path
     * @return string Asset URL with cache busting
     */
    function cached_asset($asset)
    {
        static $optimizer = null;
        
        if ($optimizer === null) {
            $optimizer = new \App\Libraries\AssetOptimizer();
        }
        
        return $optimizer->asset($asset);
    }
}

if (!function_exists('cache_remember')) {
    /**
     * Cache helper for views and controllers
     *
     * @param string $key Cache key
     * @param callable $callback Function to execute if cache miss
     * @param int $ttl Time to live in seconds
     * @return mixed Cached or generated data
     */
    function cache_remember($key, $callback, $ttl = 3600)
    {
        static $cacheHelper = null;
        
        if ($cacheHelper === null) {
            $cacheHelper = new \App\Libraries\CacheHelper();
        }
        
        return $cacheHelper->remember($key, $callback, $ttl);
    }
}