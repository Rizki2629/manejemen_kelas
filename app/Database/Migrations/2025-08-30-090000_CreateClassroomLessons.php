<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateClassroomLessons extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true ],
            'kelas' => [ 'type' => 'VARCHAR', 'constraint' => 50, 'null' => false ],
            'mapel' => [ 'type' => 'VARCHAR', 'constraint' => 100, 'null' => true ],
            'judul' => [ 'type' => 'VARCHAR', 'constraint' => 200 ],
            'slug' => [ 'type' => 'VARCHAR', 'constraint' => 220, 'unique' => true ],
            'ringkas_text' => [ 'type' => 'VARCHAR', 'constraint' => 255, 'null' => true ],
            'konten_html' => [ 'type' => 'MEDIUMTEXT', 'null' => true ],
            'author_user_id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true ],
            'visibility' => [ 'type' => 'ENUM', 'constraint' => ['draft','published'], 'default' => 'draft' ],
            'published_at' => [ 'type' => 'DATETIME', 'null' => true ],
            'created_at' => [ 'type' => 'DATETIME', 'null' => true ],
            'updated_at' => [ 'type' => 'DATETIME', 'null' => true ],
            'deleted_at' => [ 'type' => 'DATETIME', 'null' => true ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['kelas','visibility']);
        $this->forge->addKey('author_user_id');
        $this->forge->createTable('classroom_lessons', true);
    }

    public function down()
    {
        $this->forge->dropTable('classroom_lessons', true);
    }
}
