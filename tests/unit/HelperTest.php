<?php
namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;

class HelperTest extends CIUnitTestCase
{
    public function testAttendanceHelperExists()
    {
        // Test bahwa file helper absensi ada
        $this->assertFileExists(APPPATH . 'Helpers/AttendanceHelper.php');
    }

    public function testCommonFileExists()
    {
        // Test bahwa file Common.php ada
        $this->assertFileExists(APPPATH . 'Common.php');
    }

    public function testConfigFilesExist()
    {
        // Test bahwa file-file konfigurasi penting ada
        $this->assertFileExists(APPPATH . 'Config/App.php');
        $this->assertFileExists(APPPATH . 'Config/Database.php');
        $this->assertFileExists(APPPATH . 'Config/Routes.php');
    }

    public function testControllerFilesExist()
    {
        // Test bahwa controller-controller utama ada
        $this->assertFileExists(APPPATH . 'Controllers/Home.php');
        $this->assertFileExists(APPPATH . 'Controllers/Login.php');
        $this->assertFileExists(APPPATH . 'Controllers/Admin/Dashboard.php');
    }

    public function testModelFilesExist()
    {
        // Test bahwa model-model utama ada
        $this->assertFileExists(APPPATH . 'Models/UserModel.php');
        $this->assertFileExists(APPPATH . 'Models/SiswaModel.php');
        $this->assertFileExists(APPPATH . 'Models/GuruModel.php');
        $this->assertFileExists(APPPATH . 'Models/AbsensiModel.php');
        $this->assertFileExists(APPPATH . 'Models/NilaiModel.php');
    }
} 