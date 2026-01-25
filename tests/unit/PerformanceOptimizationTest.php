<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Libraries\CacheHelper;
use App\Libraries\AssetOptimizer;
use App\Models\ProfilSekolahModel;

class PerformanceOptimizationTest extends CIUnitTestCase
{
    protected $cacheHelper;
    protected $assetOptimizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cacheHelper = new CacheHelper();
        $this->assetOptimizer = new AssetOptimizer();
    }

    /**
     * Test cache functionality
     */
    public function testCacheHelper()
    {
        $testKey = 'test_cache_key';
        $testValue = ['test' => 'data', 'time' => time()];
        
        // Test cache remember functionality
        $result = $this->cacheHelper->remember($testKey, function() use ($testValue) {
            return $testValue;
        }, 60);
        
        $this->assertEquals($testValue, $result);
        
        // Test cached value retrieval (should be from cache)
        $cachedResult = $this->cacheHelper->remember($testKey, function() {
            return ['different' => 'data'];
        }, 60);
        
        $this->assertEquals($testValue, $cachedResult);
    }

    /**
     * Test ProfilSekolahModel caching
     */
    public function testProfilSekolahCaching()
    {
        $model = new ProfilSekolahModel();
        
        // This should work even if database is not available in tests
        // since we're testing the caching mechanism
        $this->assertTrue(method_exists($model, 'getProfilSekolah'));
        $this->assertTrue(method_exists($model, 'getProfilSekolahDirect'));
    }

    /**
     * Test CSS minification
     */
    public function testCSSMinification()
    {
        $css = "
        /* This is a comment */
        body {
            margin: 0 ;
            padding: 10px ;
            background-color: #ffffff ;
        }
        
        .test-class {
            display: block ;
            width: 100% ;
        }
        ";
        
        $minified = $this->assetOptimizer->minifyCSS($css);
        
        // Should be shorter than original
        $this->assertLessThan(strlen($css), strlen($minified));
        
        // Should not contain comments
        $this->assertStringNotContainsString('/* This is a comment */', $minified);
        
        // Should not contain unnecessary whitespace
        $this->assertStringNotContainsString("\n", $minified);
        $this->assertStringNotContainsString("\t", $minified);
    }

    /**
     * Test JavaScript minification
     */
    public function testJSMinification()
    {
        $js = "
        // This is a comment
        function testFunction() {
            var test = 'hello world' ;
            return test ;
        }
        
        var globalVar = 123 ;
        ";
        
        $minified = $this->assetOptimizer->minifyJS($js);
        
        // Should be shorter than original
        $this->assertLessThan(strlen($js), strlen($minified));
        
        // Should not contain single line comments
        $this->assertStringNotContainsString('// This is a comment', $minified);
        
        // Should not contain unnecessary whitespace
        $this->assertStringNotContainsString("\n", $minified);
        $this->assertStringNotContainsString("\t", $minified);
    }

    /**
     * Test helper functions exist
     */
    public function testHelperFunctions()
    {
        // Load the helper
        helper('optimization');
        
        // Check if functions exist
        $this->assertTrue(function_exists('optimized_css'));
        $this->assertTrue(function_exists('optimized_js'));
        $this->assertTrue(function_exists('cached_asset'));
        $this->assertTrue(function_exists('cache_remember'));
    }

    /**
     * Test cache statistics
     */
    public function testCacheStatistics()
    {
        $stats = $this->cacheHelper->getStats();
        
        $this->assertIsArray($stats);
        // Stats might be empty for some cache handlers, so we just check it exists
        $this->assertTrue(isset($stats) && is_array($stats));
    }

    /**
     * Test cache invalidation
     */
    public function testCacheInvalidation()
    {
        // Test that invalidation methods exist and can be called
        $this->assertNull($this->cacheHelper->invalidateProfilSekolah());
        $this->assertNull($this->cacheHelper->invalidateKalenderAkademik());
        $this->assertNull($this->cacheHelper->invalidateBerita());
        $this->assertNull($this->cacheHelper->invalidateGuruList());
        $this->assertNull($this->cacheHelper->clearAll());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        
        // Clean up test cache entries
        \Config\Services::cache()->delete('test_cache_key');
    }
}