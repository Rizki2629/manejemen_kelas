<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterUsersAddSiswaRole extends Migration
{
    public function up()
    {
        // Modify role enum to include 'siswa'
        $fields = [
            'role' => [
                'name'       => 'role',
                'type'       => 'ENUM',
                'constraint' => ['admin','walikelas','guru','siswa'],
                'default'    => 'walikelas',
            ],
        ];
        $this->forge->modifyColumn('users', $fields);
    }

    public function down()
    {
        // Revert to original
        $fields = [
            'role' => [
                'name'       => 'role',
                'type'       => 'ENUM',
                'constraint' => ['admin','walikelas','guru'],
                'default'    => 'walikelas',
            ],
        ];
        $this->forge->modifyColumn('users', $fields);
    }
}
