<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateClassroomAssignments extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true ],
            'kelas' => [ 'type' => 'VARCHAR', 'constraint' => 50, 'null' => false ],
            'judul' => [ 'type' => 'VARCHAR', 'constraint' => 200 ],
            'slug' => [ 'type' => 'VARCHAR', 'constraint' => 220, 'unique' => true ],
            'deskripsi_html' => [ 'type' => 'MEDIUMTEXT', 'null' => true ],
            'due_at' => [ 'type' => 'DATETIME', 'null' => true ],
            'allow_late' => [ 'type' => 'TINYINT', 'constraint' => 1, 'default' => 0 ],
            'author_user_id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true ],
            'visibility' => [ 'type' => 'ENUM', 'constraint' => ['draft','published'], 'default' => 'draft' ],
            'published_at' => [ 'type' => 'DATETIME', 'null' => true ],
            'created_at' => [ 'type' => 'DATETIME', 'null' => true ],
            'updated_at' => [ 'type' => 'DATETIME', 'null' => true ],
            'deleted_at' => [ 'type' => 'DATETIME', 'null' => true ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['kelas','visibility']);
        $this->forge->addKey('due_at');
        $this->forge->addKey('author_user_id');
        $this->forge->createTable('classroom_assignments', true);
    }

    public function down()
    {
        $this->forge->dropTable('classroom_assignments', true);
    }
}
