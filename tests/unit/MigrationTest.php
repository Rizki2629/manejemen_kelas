<?php
namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;

class MigrationTest extends CIUnitTestCase
{
    public function testMigrationFilesExist()
    {
        // Test bahwa file-file migration ada
        $this->assertFileExists(APPPATH . 'Database/Migrations/2025-07-01-195920_CreateUsersTable.php');
        $this->assertFileExists(APPPATH . 'Database/Migrations/2025-07-03-133015_CreateTbSiswaTable.php');
        $this->assertFileExists(APPPATH . 'Database/Migrations/2025-07-03-130804_CreateTbGuruTable.php');
        $this->assertFileExists(APPPATH . 'Database/Migrations/2025-07-01-195911_CreateWalikelasTable.php');
        $this->assertFileExists(APPPATH . 'Database/Migrations/2025-07-03-160000_CreateAbsensi.php');
        $this->assertFileExists(APPPATH . 'Database/Migrations/2024-12-19-000001_CreateNilaiTable.php');
        $this->assertFileExists(APPPATH . 'Database/Migrations/2024-06-01-000000_CreateBeritaTable.php');
        $this->assertFileExists(APPPATH . 'Database/Migrations/2025-07-03-154000_CreateKalenderAkademik.php');
    }

    public function testSeederFilesExist()
    {
        // Test bahwa file-file seeder ada
        $this->assertFileExists(APPPATH . 'Database/Seeds/DatabaseSeeder.php');
        $this->assertFileExists(APPPATH . 'Database/Seeds/UserSeeder.php');
        $this->assertFileExists(APPPATH . 'Database/Seeds/SiswaSeeder.php');
        $this->assertFileExists(APPPATH . 'Database/Seeds/TbGuruSeeder.php');
        $this->assertFileExists(APPPATH . 'Database/Seeds/NilaiSeeder.php');
        $this->assertFileExists(APPPATH . 'Database/Seeds/WalikelasSeeder.php');
    }

    public function testMigrationDirectoryExists()
    {
        // Test bahwa direktori migration ada
        $this->assertDirectoryExists(APPPATH . 'Database/Migrations');
    }

    public function testSeederDirectoryExists()
    {
        // Test bahwa direktori seeder ada
        $this->assertDirectoryExists(APPPATH . 'Database/Seeds');
    }
} 