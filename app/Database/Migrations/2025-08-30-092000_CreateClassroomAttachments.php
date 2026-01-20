<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateClassroomAttachments extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [ 'type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true ],
            'context_type' => [ 'type'=>'VARCHAR','constraint'=>30,'null'=>false ], // lesson|assignment|submission
            'context_id' => [ 'type'=>'INT','constraint'=>11,'unsigned'=>true,'null'=>false ],
            'original_name' => [ 'type'=>'VARCHAR','constraint'=>255,'null'=>false ],
            'stored_name' => [ 'type'=>'VARCHAR','constraint'=>255,'null'=>false ],
            'mime_type' => [ 'type'=>'VARCHAR','constraint'=>120,'null'=>true ],
            'size_bytes' => [ 'type'=>'INT','constraint'=>11,'unsigned'=>true,'null'=>true ],
            'uploaded_by' => [ 'type'=>'INT','constraint'=>11,'unsigned'=>true,'null'=>false ],
            'created_at' => [ 'type'=>'DATETIME','null'=>true ],
            'updated_at' => [ 'type'=>'DATETIME','null'=>true ],
            'deleted_at' => [ 'type'=>'DATETIME','null'=>true ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['context_type','context_id']);
        $this->forge->createTable('classroom_attachments');
    }

    public function down()
    {
        $this->forge->dropTable('classroom_attachments');
    }
}
?>