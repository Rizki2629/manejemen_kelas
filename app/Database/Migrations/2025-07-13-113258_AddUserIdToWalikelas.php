<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUserIdToWalikelas extends Migration
{
    public function up()
    {
        // Add user_id field to walikelas table
        $this->forge->addColumn('walikelas', [
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id'
            ]
        ]);
        
        // Add foreign key constraint to users table
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'SET NULL');
    }

    public function down()
    {
        // Remove foreign key and column
        $this->forge->dropForeignKey('walikelas', 'walikelas_user_id_foreign');
        $this->forge->dropColumn('walikelas', 'user_id');
    }
}
