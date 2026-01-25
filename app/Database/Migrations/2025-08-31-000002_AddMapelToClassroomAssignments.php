<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMapelToClassroomAssignments extends Migration
{
    public function up()
    {
        // Add mapel column if not exists
        if (!$this->db->fieldExists('mapel','classroom_assignments')) {
            $this->forge->addColumn('classroom_assignments', [
                'mapel' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 120,
                    'null'       => true,
                    'after'      => 'kelas'
                ],
            ]);
            // Optional composite index for filtering by kelas+mapel
            $this->db->query("CREATE INDEX IF NOT EXISTS idx_assignments_kelas_mapel ON classroom_assignments (kelas, mapel)");
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('mapel','classroom_assignments')) {
            $this->forge->dropColumn('classroom_assignments','mapel');
        }
    }
}
