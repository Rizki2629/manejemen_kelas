<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLessonViewTracking extends Migration
{
    public function up()
    {
        // Add view_count column if not exists
        if (! $this->db->fieldExists('view_count', 'classroom_lessons')) {
            $forge = \Config\Database::forge();
            $forge->addColumn('classroom_lessons', [
                'view_count' => [ 'type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'default' => 0, 'after' => 'published_at']
            ]);
        }
        // Create classroom_lesson_views table if not exists
        if (! $this->db->tableExists('classroom_lesson_views')) {
            $forge = \Config\Database::forge();
            $forge->addField([
                'id' => [ 'type' => 'INT', 'unsigned' => true, 'auto_increment' => true ],
                'lesson_id' => [ 'type' => 'INT', 'unsigned' => true ],
                'user_id' => [ 'type' => 'INT', 'unsigned' => true ],
                'viewed_at' => [ 'type' => 'DATETIME', 'null' => false ],
                'created_at' => [ 'type' => 'DATETIME', 'null' => false ],
                'updated_at' => [ 'type' => 'DATETIME', 'null' => true ],
            ]);
            $forge->addKey('id', true);
            $forge->addKey(['lesson_id']);
            $forge->addKey(['user_id']);
            $forge->addUniqueKey(['lesson_id','user_id']);
            $forge->createTable('classroom_lesson_views');
        }
    }

    public function down()
    {
        if ($this->db->tableExists('classroom_lesson_views')) {
            $forge = \Config\Database::forge();
            $forge->dropTable('classroom_lesson_views');
        }
        if ($this->db->fieldExists('view_count', 'classroom_lessons')) {
            $forge = \Config\Database::forge();
            $forge->dropColumn('classroom_lessons', 'view_count');
        }
    }
}
