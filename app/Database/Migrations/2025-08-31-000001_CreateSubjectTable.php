<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSubjectTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('subject')) return; // idempotent
        $this->forge->addField([
            'id' => [ 'type'=>'INT', 'unsigned'=>true, 'auto_increment'=>true ],
            'code' => [ 'type'=>'VARCHAR','constraint'=>30,'null'=>true ],
            'name' => [ 'type'=>'VARCHAR','constraint'=>100 ],
            'created_at' => [ 'type'=>'DATETIME','null'=>true ],
            'updated_at' => [ 'type'=>'DATETIME','null'=>true ],
            'deleted_at' => [ 'type'=>'DATETIME','null'=>true ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('code');
        $this->forge->addUniqueKey('name');
        $this->forge->createTable('subject');
    }

    public function down()
    {
        if ($this->db->tableExists('subject')) {
            $this->forge->dropTable('subject');
        }
    }
}
