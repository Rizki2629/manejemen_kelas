<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVideoUrlToClassroomLessons extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('video_url', 'classroom_lessons')) {
            $this->forge->addColumn('classroom_lessons', [
                'video_url' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => true,
                    'after' => 'konten_html'
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('video_url', 'classroom_lessons')) {
            $this->forge->dropColumn('classroom_lessons', 'video_url');
        }
    }
}
