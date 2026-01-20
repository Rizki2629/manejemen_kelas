<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\UserModel;

class TestUserUpdate extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Testing';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'test:userupdate';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Test user update functionality';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'test:userupdate';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        CLI::write('Testing User Update Functionality', 'green');
        CLI::newLine();

        try {
            // Test 1: Check database connection and table structure
            CLI::write('Test 1: Database Connection & Table Structure', 'yellow');
            $db = \Config\Database::connect();
            
            if ($db->tableExists('users')) {
                CLI::write('✅ Users table exists', 'green');
                
                // Get table structure
                $fields = $db->getFieldData('users');
                CLI::write('Table fields:', 'cyan');
                foreach ($fields as $field) {
                    CLI::write("  - {$field->name} ({$field->type})", 'white');
                }
                
            } else {
                CLI::write('❌ Users table not found', 'red');
                return;
            }

            // Test 2: Check UserModel and data
            CLI::write('Test 2: UserModel Test', 'yellow');
            $userModel = new UserModel();
            
            $testUser = $userModel->first();
            if (!$testUser) {
                CLI::write('❌ No users found in database', 'red');
                return;
            }
            
            CLI::write("✅ Found test user data:", 'green');
            foreach ($testUser as $key => $value) {
                CLI::write("  - {$key}: {$value}", 'white');
            }

            // Test 3: Safe update test
            CLI::write('Test 3: Update Test', 'yellow');
            
            // Determine what fields exist
            $availableFields = array_keys($testUser);
            
            // Create update data based on available fields
            $updateData = [];
            
            if (in_array('username', $availableFields)) {
                $updateData['username'] = $testUser['username'];
            }
            
            if (in_array('full_name', $availableFields)) {
                $updateData['full_name'] = $testUser['full_name'] . ' (tested)';
            } elseif (in_array('name', $availableFields)) {
                $updateData['name'] = $testUser['name'] . ' (tested)';
            } elseif (in_array('nama', $availableFields)) {
                $updateData['nama'] = $testUser['nama'] . ' (tested)';
            }
            
            if (in_array('role', $availableFields)) {
                $updateData['role'] = $testUser['role'];
            }

            CLI::write('Update data: ' . json_encode($updateData), 'cyan');

            // Skip validation to test direct model update (like controller does)
            $updateResult = $userModel->skipValidation()->update($testUser['id'], $updateData);
            
            if ($updateResult) {
                CLI::write('✅ User update successful', 'green');
                
                // Verify update
                $updatedUser = $userModel->find($testUser['id']);
                if ($updatedUser) {
                    CLI::write('✅ Update verified - data retrieved successfully', 'green');
                    
                    // Check if any field was actually updated
                    $updated = false;
                    foreach ($updateData as $field => $value) {
                        if (isset($updatedUser[$field]) && $updatedUser[$field] === $value) {
                            if (strpos($value, '(tested)') !== false) {
                                $updated = true;
                                break;
                            }
                        }
                    }
                    
                    if ($updated) {
                        CLI::write('✅ Update successful - field value changed', 'green');
                    } else {
                        CLI::write('⚠️ Update might not have taken effect', 'yellow');
                    }
                } else {
                    CLI::write('⚠️ Could not retrieve updated user', 'yellow');
                }
                
            } else {
                CLI::write('❌ User update failed', 'red');
                $errors = $userModel->errors();
                if (!empty($errors)) {
                    CLI::write('Errors: ' . json_encode($errors), 'red');
                }
            }

            // Test 4: Validation test
            CLI::write('Test 4: Validation Test', 'yellow');
            
            $invalidData = [];
            if (in_array('username', $availableFields)) {
                $invalidData['username'] = ''; // Should fail
            }
            
            $invalidResult = $userModel->update($testUser['id'], $invalidData);
            if (!$invalidResult) {
                CLI::write('✅ Validation working - invalid data rejected', 'green');
                $errors = $userModel->errors();
                if (!empty($errors)) {
                    CLI::write('Validation errors: ' . json_encode($errors), 'cyan');
                }
            } else {
                CLI::write('⚠️ Validation might not be working properly', 'yellow');
            }

            CLI::newLine();
            CLI::write('=== TEST SUMMARY ===', 'cyan');
            CLI::write('To test the web interface:', 'white');
            CLI::write('1. Go to: http://localhost:8080/admin/users', 'white');
            CLI::write('2. Click "Edit" on any user', 'white');
            CLI::write('3. Make changes and click "Update User"', 'white');
            CLI::write('4. Check if changes persist after page refresh', 'white');
            CLI::write('5. Check logs at: writable/logs/ for any errors', 'white');
            
        } catch (\Exception $e) {
            CLI::write('❌ Error during testing: ' . $e->getMessage(), 'red');
            CLI::write('Stack trace: ' . $e->getTraceAsString(), 'red');
        }
    }
}
