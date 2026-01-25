<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyHabitLogsTable extends Migration
{
    public function up()
    {
        // Check if habit_logs table exists
        if (!$this->db->tableExists('habit_logs')) {
            return;
        }

        // Add new field for complex data (JSON) - only if it doesn't exist
        $fields = $this->db->getFieldNames('habit_logs');
        if (!in_array('value_json', $fields)) {
            $this->forge->addColumn('habit_logs', [
                'value_json' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'comment' => 'For storing complex data as JSON string'
                ]
            ]);
        }

        // Modify notes field to be larger TEXT - only if it's currently VARCHAR
        $fieldData = $this->db->getFieldData('habit_logs');
        foreach ($fieldData as $field) {
            if ($field->name === 'notes' && stripos($field->type, 'varchar') !== false) {
                $this->forge->modifyColumn('habit_logs', [
                    'notes' => [
                        'type' => 'TEXT',
                        'null' => true,
                        'comment' => 'General notes or simple text data'
                    ]
                ]);
                break;
            }
        }
    }

    public function down()
    {
        // Check if habit_logs table exists
        if (!$this->db->tableExists('habit_logs')) {
            return;
        }

        // Remove the added column if it exists
        $fields = $this->db->getFieldNames('habit_logs');
        if (in_array('value_json', $fields)) {
            $this->forge->dropColumn('habit_logs', 'value_json');
        }
        
        // Revert notes field back to VARCHAR if it's currently TEXT
        $fieldData = $this->db->getFieldData('habit_logs');
        foreach ($fieldData as $field) {
            if ($field->name === 'notes' && stripos($field->type, 'text') !== false) {
                $this->forge->modifyColumn('habit_logs', [
                    'notes' => [
                        'type' => 'VARCHAR',
                        'constraint' => 255,
                        'null' => true
                    ]
                ]);
                break;
            }
        }
    }
}
