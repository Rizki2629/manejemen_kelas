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
            // Optional composite index for filtering by kelas+mapel (MySQL 5.7 compat)
            $idxExists = $this->db->query("SHOW INDEX FROM classroom_assignments WHERE Key_name = 'idx_assignments_kelas_mapel'");
            if ($idxExists->getResultArray() === []) {
                $this->db->query('ALTER TABLE classroom_assignments ADD INDEX idx_assignments_kelas_mapel (kelas, mapel)');
            }
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('mapel','classroom_assignments')) {
            $this->forge->dropColumn('classroom_assignments','mapel');
        }
    }
}
