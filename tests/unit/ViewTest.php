<?php
namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;

class ViewTest extends CIUnitTestCase
{
    public function testViewFilesExist()
    {
        // Test bahwa file-file view penting ada
        $this->assertFileExists(APPPATH . 'Views/home.php');
        $this->assertFileExists(APPPATH . 'Views/login.php');
        $this->assertFileExists(APPPATH . 'Views/layout/admin.php');
        $this->assertFileExists(APPPATH . 'Views/admin/dashboard.php');
        $this->assertFileExists(APPPATH . 'Views/admin/absensi/input.php');
        $this->assertFileExists(APPPATH . 'Views/admin/absensi/rekap.php');
        $this->assertFileExists(APPPATH . 'Views/admin/nilai/index.php');
        $this->assertFileExists(APPPATH . 'Views/admin/guru/index.php');
        $this->assertFileExists(APPPATH . 'Views/admin/data-siswa/index.php');
        $this->assertFileExists(APPPATH . 'Views/admin/users/index.php');
        $this->assertFileExists(APPPATH . 'Views/admin/berita/index.php');
    }

    public function testPublicAssetsExist()
    {
        // Test bahwa asset-asset publik ada
        $this->assertFileExists(PUBLICPATH . 'index.php');
        $this->assertFileExists(PUBLICPATH . 'favicon.ico');
        $this->assertFileExists(PUBLICPATH . 'robots.txt');
        
        // Test CSS files
        $this->assertFileExists(PUBLICPATH . 'css/rekap-absensi-clean.css');
        $this->assertFileExists(PUBLICPATH . 'css/rekap-enhanced.css');
    }

    public function testWritableDirectoriesExist()
    {
        // Test bahwa direktori writable ada
        $this->assertDirectoryExists(WRITEPATH . 'cache');
        $this->assertDirectoryExists(WRITEPATH . 'logs');
        $this->assertDirectoryExists(WRITEPATH . 'session');
        $this->assertDirectoryExists(WRITEPATH . 'uploads');
    }
} 