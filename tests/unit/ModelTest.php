<?php
namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;

class ModelTest extends CIUnitTestCase
{
    public function testModelFilesExist()
    {
        // Test bahwa file-file model ada
        $this->assertFileExists(APPPATH . 'Models/UserModel.php');
        $this->assertFileExists(APPPATH . 'Models/SiswaModel.php');
        $this->assertFileExists(APPPATH . 'Models/GuruModel.php');
        $this->assertFileExists(APPPATH . 'Models/AbsensiModel.php');
        $this->assertFileExists(APPPATH . 'Models/NilaiModel.php');
        $this->assertFileExists(APPPATH . 'Models/BeritaModel.php');
        $this->assertFileExists(APPPATH . 'Models/ProfilSekolahModel.php');
        $this->assertFileExists(APPPATH . 'Models/KalenderAkademikModel.php');
        $this->assertFileExists(APPPATH . 'Models/WalikelasModel.php');
    }

    public function testModelClassesAreDefined()
    {
        // Test bahwa class-class model didefinisikan
        $this->assertTrue(class_exists('\App\Models\UserModel'));
        $this->assertTrue(class_exists('\App\Models\SiswaModel'));
        $this->assertTrue(class_exists('\App\Models\GuruModel'));
        $this->assertTrue(class_exists('\App\Models\AbsensiModel'));
        $this->assertTrue(class_exists('\App\Models\NilaiModel'));
        $this->assertTrue(class_exists('\App\Models\BeritaModel'));
        $this->assertTrue(class_exists('\App\Models\ProfilSekolahModel'));
        $this->assertTrue(class_exists('\App\Models\KalenderAkademikModel'));
        $this->assertTrue(class_exists('\App\Models\WalikelasModel'));
    }
} 