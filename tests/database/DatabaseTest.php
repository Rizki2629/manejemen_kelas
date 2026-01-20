<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

class DatabaseTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = false;
    protected $migrateOnce = false;

    public function testDatabaseConnection()
    {
        $db = \Config\Database::connect();
        
        $this->assertInstanceOf('CodeIgniter\Database\BaseConnection', $db);
        $this->assertTrue($db->connID !== false);
    }

    public function testUsersTableExists()
    {
        $db = \Config\Database::connect();
        
        $tableExists = $db->tableExists('users');
        $this->assertTrue($tableExists, 'Users table should exist');
    }

    public function testSiswaTableExists()
    {
        $db = \Config\Database::connect();
        
        $tableExists = $db->tableExists('siswa');
        $this->assertTrue($tableExists, 'Siswa table should exist');
    }

    public function testHabitsTableExists()
    {
        $db = \Config\Database::connect();
        
        $tableExists = $db->tableExists('habits');
        $this->assertTrue($tableExists, 'Habits table should exist');
    }

    public function testUserSiswaRelationship()
    {
        $db = \Config\Database::connect();
        
        // Test that we have users with corresponding siswa records
        $query = $db->query("
            SELECT COUNT(*) as count 
            FROM users u 
            INNER JOIN siswa s ON u.nisn = s.nisn 
            LIMIT 1
        ");
        
        $result = $query->getRow();
        $this->assertGreaterThan(0, $result->count, 'Should have users with siswa records');
    }

    public function testSpecificUserExists()
    {
        $db = \Config\Database::connect();
        
        // Test for the specific user we fixed
        $query = $db->query("SELECT COUNT(*) as count FROM users WHERE nisn = '3157252958'");
        $result = $query->getRow();
        
        $this->assertGreaterThan(0, $result->count, 'User with NISN 3157252958 should exist');
    }
}
