<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateClassroomSubmissions extends Migration
{
    public function up()
    {
        // If table already exists, skip to prevent duplicate index errors during partial migration runs
        if ($this->db->tableExists('classroom_submissions')) {
            return; // safe no-op
        }
        $this->forge->addField([
            'id' => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
            'assignment_id' => ['type'=>'INT','constraint'=>11,'unsigned'=>true],
            'siswa_user_id' => ['type'=>'INT','constraint'=>11,'unsigned'=>true],
            'content_text' => ['type'=>'TEXT','null'=>true],
            'content_html' => ['type'=>'MEDIUMTEXT','null'=>true],
            'submitted_at' => ['type'=>'DATETIME','null'=>true],
            'late' => ['type'=>'TINYINT','constraint'=>1,'default'=>0],
            'graded_at' => ['type'=>'DATETIME','null'=>true],
            'grader_user_id' => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'null'=>true],
            'score' => ['type'=>'DECIMAL','constraint'=>'5,2','null'=>true],
            'feedback_text' => ['type'=>'TEXT','null'=>true],
            'created_at' => ['type'=>'DATETIME','null'=>true],
            'updated_at' => ['type'=>'DATETIME','null'=>true],
            'deleted_at' => ['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
    // Only need UNIQUE key; removing separate non-unique key to avoid duplicate name conflict
    $this->forge->addUniqueKey(['assignment_id','siswa_user_id']);
        $this->forge->addKey('grader_user_id');
        $this->forge->createTable('classroom_submissions', true);
    }

    public function down()
    {
        $this->forge->dropTable('classroom_submissions', true);
    }
}
