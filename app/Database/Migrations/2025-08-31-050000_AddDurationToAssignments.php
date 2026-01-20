<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDurationToAssignments extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('work_duration_minutes','classroom_assignments')) {
            $this->forge->addColumn('classroom_assignments', [
                'work_duration_minutes' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => true,
                    'after' => 'due_at'
                ]
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('work_duration_minutes','classroom_assignments')) {
            $this->forge->dropColumn('classroom_assignments','work_duration_minutes');
        }
    }
}
?>
