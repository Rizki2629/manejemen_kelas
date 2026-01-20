<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateHabitLogsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'student_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'habit_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'log_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'value_bool' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => true,
            ],
            'value_time' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'value_number' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'notes' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

    $this->forge->addKey('id', true);
    // Unique key to prevent double submit per day
    $this->forge->addUniqueKey(['student_id', 'habit_id', 'log_date']);
        $this->forge->addForeignKey('student_id', 'siswa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('habit_id', 'habits', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('habit_logs');
    }

    public function down()
    {
        $this->forge->dropTable('habit_logs');
    }
}
