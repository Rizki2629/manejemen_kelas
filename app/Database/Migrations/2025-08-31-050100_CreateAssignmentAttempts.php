<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAssignmentAttempts extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('classroom_assignment_attempts')) {
            $this->forge->addField([
                'id' => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
                'assignment_id' => ['type'=>'INT','constraint'=>11,'unsigned'=>true],
                'user_id' => ['type'=>'INT','constraint'=>11,'unsigned'=>true],
                'started_at' => ['type'=>'DATETIME','null'=>true],
                'ended_at' => ['type'=>'DATETIME','null'=>true],
                'remaining_seconds' => ['type'=>'INT','constraint'=>11,'null'=>true],
                'answers_json' => ['type'=>'LONGTEXT','null'=>true],
                'status' => ['type'=>'ENUM','constraint'=>['in_progress','finished','expired'],'default'=>'in_progress'],
                'created_at' => ['type'=>'DATETIME','null'=>true],
                'updated_at' => ['type'=>'DATETIME','null'=>true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey(['assignment_id','user_id']);
            $this->forge->createTable('classroom_assignment_attempts', true);
            $this->db->query('CREATE INDEX IF NOT EXISTS idx_attempts_status ON classroom_assignment_attempts (status)');
        }
    }

    public function down()
    {
        if ($this->db->tableExists('classroom_assignment_attempts')) {
            $this->forge->dropTable('classroom_assignment_attempts', true);
        }
    }
}
?>
