<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddQuestionsJsonToClassroomAssignments extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('questions_json','classroom_assignments')) {
            $fields = [
                'questions_json' => [
                    'type' => 'LONGTEXT',
                    'null' => true,
                    'after' => 'deskripsi_html'
                ]
            ];
            $this->forge->addColumn('classroom_assignments', $fields);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('questions_json','classroom_assignments')) {
            $this->forge->dropColumn('classroom_assignments','questions_json');
        }
    }
}
?>