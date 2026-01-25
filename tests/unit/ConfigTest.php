<?php
namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;

class ConfigTest extends CIUnitTestCase
{
    public function testConfigFilesExist()
    {
        // Test bahwa file-file konfigurasi penting ada
        $this->assertFileExists(APPPATH . 'Config/App.php');
        $this->assertFileExists(APPPATH . 'Config/Database.php');
        $this->assertFileExists(APPPATH . 'Config/Routes.php');
        $this->assertFileExists(APPPATH . 'Config/Autoload.php');
        $this->assertFileExists(APPPATH . 'Config/Session.php');
        $this->assertFileExists(APPPATH . 'Config/Validation.php');
    }

    public function testAppConfigClassIsDefined()
    {
        // Test bahwa class App config didefinisikan
        $this->assertTrue(class_exists('\Config\App'));
    }

    public function testDatabaseConfigClassIsDefined()
    {
        // Test bahwa class Database config didefinisikan
        $this->assertTrue(class_exists('\Config\Database'));
    }

    public function testAutoloadConfigClassIsDefined()
    {
        // Test bahwa class Autoload config didefinisikan
        $this->assertTrue(class_exists('\Config\Autoload'));
    }

    public function testSessionConfigClassIsDefined()
    {
        // Test bahwa class Session config didefinisikan
        $this->assertTrue(class_exists('\Config\Session'));
    }

    public function testValidationConfigClassIsDefined()
    {
        // Test bahwa class Validation config didefinisikan
        $this->assertTrue(class_exists('\Config\Validation'));
    }
} 