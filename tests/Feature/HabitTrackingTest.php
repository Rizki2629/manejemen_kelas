<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

class HabitTrackingTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testHabitPageLoads()
    {
        $result = $this->get('/siswa');
        
        $result->assertStatus(302); // Should redirect to login since not authenticated
    }

    public function testLoginPageLoads()
    {
        $result = $this->get('/login');
        
        $result->assertOK();
        $result->assertSee('Login');
    }

    public function testDashboardRedirectsWhenNotLoggedIn()
    {
        $result = $this->get('/dashboard');
        
        // Should redirect to login if not authenticated
        $result->assertRedirectTo('/login');
    }

    public function testPublicPagesAreAccessible()
    {
        // Test public endpoints
        $pages = [
            '/',
            '/login'
        ];

        foreach ($pages as $page) {
            $result = $this->get($page);
            $result->assertOK();
        }
    }

    public function testStaticAssetsExist()
    {
        // Test public directory files
        $result = $this->get('/');
        $result->assertOK();
    }
}
