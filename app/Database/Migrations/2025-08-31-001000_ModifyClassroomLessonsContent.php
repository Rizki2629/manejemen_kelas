<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyClassroomLessonsContent extends Migration
{
    public function up()
    {
        // Drop ringkas_text if exists
        if ($this->db->fieldExists('ringkas_text', 'classroom_lessons')) {
            $this->forge->dropColumn('classroom_lessons', 'ringkas_text');
        }
        // Rename konten_html to konten if konten_html exists and konten not yet
        $hasKontenHtml = $this->db->fieldExists('konten_html', 'classroom_lessons');
        $hasKonten = $this->db->fieldExists('konten', 'classroom_lessons');
        if ($hasKontenHtml && !$hasKonten) {
            $this->db->query('ALTER TABLE `classroom_lessons` CHANGE `konten_html` `konten` MEDIUMTEXT NULL');
        }
    }

    public function down()
    {
        // Recreate ringkas_text (nullable) if missing
        if (!$this->db->fieldExists('ringkas_text', 'classroom_lessons')) {
            $this->forge->addColumn('classroom_lessons', [
                'ringkas_text' => [ 'type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'slug' ]
            ]);
        }
        // Rename konten back to konten_html if needed
        $hasKonten = $this->db->fieldExists('konten', 'classroom_lessons');
        $hasKontenHtml = $this->db->fieldExists('konten_html', 'classroom_lessons');
        if ($hasKonten && !$hasKontenHtml) {
            $this->db->query('ALTER TABLE `classroom_lessons` CHANGE `konten` `konten_html` MEDIUMTEXT NULL');
        }
    }
}
