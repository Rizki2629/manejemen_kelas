<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class UpdateHabitLogsTable extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:update-habit-logs';
    protected $description = 'Update habit_logs table structure for complex data';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        CLI::write('Updating habit_logs table structure...', 'yellow');
        
        try {
            // Check if value_json column exists
            $fields = $db->getFieldNames('habit_logs');
            
            if (!in_array('value_json', $fields)) {
                CLI::write('Adding value_json column...', 'cyan');
                $db->query("ALTER TABLE habit_logs ADD COLUMN value_json TEXT NULL COMMENT 'For storing complex data as JSON string'");
                CLI::write('✅ Added value_json column', 'green');
            } else {
                CLI::write('value_json column already exists', 'blue');
            }
            
            // Check current type of notes column
            $fieldData = $db->getFieldData('habit_logs');
            $notesField = null;
            foreach ($fieldData as $field) {
                if ($field->name === 'notes') {
                    $notesField = $field;
                    break;
                }
            }
            
            if ($notesField && stripos($notesField->type, 'varchar') !== false) {
                CLI::write('Expanding notes column to TEXT...', 'cyan');
                $db->query("ALTER TABLE habit_logs MODIFY COLUMN notes TEXT NULL COMMENT 'General notes or simple text data'");
                CLI::write('✅ Expanded notes column to TEXT', 'green');
            } else {
                CLI::write('notes column is already TEXT or larger', 'blue');
            }
            
            CLI::write('✅ Table structure update completed!', 'green');
            
            // Show current structure
            CLI::write("\nCurrent habit_logs table structure:", 'yellow');
            $fields = $db->query("DESCRIBE habit_logs")->getResultArray();
            
            foreach ($fields as $field) {
                CLI::write("  {$field['Field']}: {$field['Type']} " . 
                          ($field['Null'] === 'YES' ? 'NULL' : 'NOT NULL') . 
                          ($field['Default'] ? " DEFAULT {$field['Default']}" : ''), 'white');
            }
            
        } catch (\Exception $e) {
            CLI::write('❌ Error: ' . $e->getMessage(), 'red');
        }
    }
}
