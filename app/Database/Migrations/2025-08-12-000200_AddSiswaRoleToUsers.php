<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSiswaRoleToUsers extends Migration
{
    public function up()
    {
        // Modify ENUM to include 'siswa'
        $this->forge->modifyColumn('users', [
            'role' => [
                'type' => "ENUM('admin','walikelas','guru','siswa')",
                'default' => 'walikelas',
                'null' => false,
            ],
        ]);
    }

    public function down()
    {
        // Revert ENUM to original
        $this->forge->modifyColumn('users', [
            'role' => [
                'type' => "ENUM('admin','walikelas','guru')",
                'default' => 'walikelas',
                'null' => false,
            ],
        ]);
    }
}
