<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNilaiTableV2 extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type'=>'INT','auto_increment'=>true],
            'siswa_id' => ['type'=>'INT','null'=>false],
            'subject_id' => ['type'=>'INT','null'=>true,'comment'=>'FK subjects.id (optional)'],
            'mata_pelajaran' => ['type'=>'VARCHAR','constraint'=>120,'null'=>true,'comment'=>'Legacy subject name'],
            'jenis_nilai' => ['type'=>'VARCHAR','constraint'=>20,'null'=>true,'comment'=>'harian|pts|pas'],
            'nilai' => ['type'=>'DECIMAL','constraint'=>'5,2','null'=>true],
            'tp_materi' => ['type'=>'VARCHAR','constraint'=>150,'null'=>true],
            'tanggal' => ['type'=>'DATE','null'=>true],
            'kelas' => ['type'=>'VARCHAR','constraint'=>20,'null'=>true],
            'semester' => ['type'=>'TINYINT','null'=>true],
            'tahun_ajar' => ['type'=>'VARCHAR','constraint'=>15,'null'=>true],
            'created_at' => ['type'=>'DATETIME','null'=>true],
            'updated_at' => ['type'=>'DATETIME','null'=>true],
            'deleted_at' => ['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['siswa_id','subject_id']);
        $this->forge->addForeignKey('subject_id','subjects','id','SET NULL','CASCADE');
        $this->forge->createTable('nilai', true);
    }

    public function down()
    {
        $this->forge->dropTable('nilai', true);
    }
}
