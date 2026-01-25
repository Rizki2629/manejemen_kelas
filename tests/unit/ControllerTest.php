<?php
namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;

class ControllerTest extends CIUnitTestCase
{
    public function testControllerFilesExist()
    {
        // Test bahwa file-file controller ada
        $this->assertFileExists(APPPATH . 'Controllers/Home.php');
        $this->assertFileExists(APPPATH . 'Controllers/Login.php');
        $this->assertFileExists(APPPATH . 'Controllers/BaseController.php');
        $this->assertFileExists(APPPATH . 'Controllers/Admin/Dashboard.php');
        $this->assertFileExists(APPPATH . 'Controllers/Admin/Absensi.php');
        $this->assertFileExists(APPPATH . 'Controllers/Admin/Nilai.php');
        $this->assertFileExists(APPPATH . 'Controllers/Admin/Guru.php');
        $this->assertFileExists(APPPATH . 'Controllers/Admin/DataSiswa.php');
        $this->assertFileExists(APPPATH . 'Controllers/Admin/Users.php');
        $this->assertFileExists(APPPATH . 'Controllers/Admin/Berita.php');
    }

    public function testControllerClassesAreDefined()
    {
        // Test bahwa class-class controller didefinisikan
        $this->assertTrue(class_exists('\App\Controllers\Home'));
        $this->assertTrue(class_exists('\App\Controllers\Login'));
        $this->assertTrue(class_exists('\App\Controllers\BaseController'));
        $this->assertTrue(class_exists('\App\Controllers\Admin\Dashboard'));
        $this->assertTrue(class_exists('\App\Controllers\Admin\Absensi'));
        $this->assertTrue(class_exists('\App\Controllers\Admin\Nilai'));
        $this->assertTrue(class_exists('\App\Controllers\Admin\Guru'));
        $this->assertTrue(class_exists('\App\Controllers\Admin\DataSiswa'));
        $this->assertTrue(class_exists('\App\Controllers\Admin\Users'));
        $this->assertTrue(class_exists('\App\Controllers\Admin\Berita'));
    }
} 