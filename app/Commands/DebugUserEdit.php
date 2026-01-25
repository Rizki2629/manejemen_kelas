<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\UserModel;
use App\Models\WalikelasModel;
use App\Models\TbSiswaModel;

class DebugUserEdit extends BaseCommand
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
    protected $name = 'debug:useredit';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Debug user edit data';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'debug:useredit [user_id]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [
        'user_id' => 'The user ID to debug (default: 2)'
    ];

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
        $userId = $params[0] ?? 2; // Default to user ID 2
        
        CLI::write('Debugging User Edit Data for User ID: ' . $userId, 'green');
        CLI::newLine();

        try {
            $userModel = new UserModel();
            $walikelasModel = new WalikelasModel();
            $tbSiswaModel = new TbSiswaModel();

            // Get user data
            $user = $userModel->find($userId);
            if (!$user) {
                CLI::write('❌ User not found with ID: ' . $userId, 'red');
                return;
            }

            CLI::write('✅ User found:', 'green');
            CLI::write('  - ID: ' . $user['id'], 'white');
            CLI::write('  - Username: ' . $user['username'], 'white');
            CLI::write('  - Name: ' . $user['nama'], 'white');
            CLI::write('  - Role: ' . $user['role'], 'white');
            CLI::write('  - Walikelas ID: ' . ($user['walikelas_id'] ?: 'null'), 'white');
            CLI::newLine();

            // Get walikelas data
            $walikelas = $walikelasModel->findAll();
            CLI::write('Walikelas data (' . count($walikelas) . ' records):', 'yellow');
            if (empty($walikelas)) {
                CLI::write('  ❌ No walikelas records found', 'red');
            } else {
                foreach ($walikelas as $index => $kelas) {
                    CLI::write("  [$index] ID: {$kelas['id']}, Kelas: {$kelas['kelas']}, Nama: {$kelas['nama']}", 'white');
                }
            }
            CLI::newLine();

            // Get available classes
            $availableKelas = $tbSiswaModel->getActiveClasses();
            CLI::write('Available Classes (' . count($availableKelas) . ' records):', 'yellow');
            if (empty($availableKelas)) {
                CLI::write('  ❌ No available classes found', 'red');
            } else {
                foreach (array_slice($availableKelas, 0, 5) as $index => $kelas) { // Show first 5
                    CLI::write("  [$index] Kelas: {$kelas['kelas']}", 'white');
                }
                if (count($availableKelas) > 5) {
                    CLI::write('  ... and ' . (count($availableKelas) - 5) . ' more classes', 'cyan');
                }
            }
            CLI::newLine();

            // Check if data would show in dropdown
            CLI::write('Dropdown Analysis:', 'yellow');
            
            // Existing walikelas
            if (!empty($walikelas)) {
                CLI::write('✅ Existing walikelas group would show ' . count($walikelas) . ' options', 'green');
            } else {
                CLI::write('⚠️  No existing walikelas to show', 'yellow');
            }
            
            // Available classes
            if (!empty($availableKelas)) {
                $assignedKelas = array_column($walikelas, 'kelas');
                $unassignedCount = 0;
                foreach ($availableKelas as $kelas) {
                    if (!in_array($kelas['kelas'], $assignedKelas)) {
                        $unassignedCount++;
                    }
                }
                CLI::write('✅ Available classes group would show ' . count($availableKelas) . ' total options', 'green');
                CLI::write('   - ' . $unassignedCount . ' unassigned classes (selectable)', 'cyan');
                CLI::write('   - ' . (count($availableKelas) - $unassignedCount) . ' assigned classes (disabled)', 'cyan');
            } else {
                CLI::write('❌ No available classes to show', 'red');
            }
            
            CLI::newLine();
            CLI::write('🔧 If dropdown is empty, check:', 'yellow');
            CLI::write('1. Browser console for JavaScript errors', 'white');
            CLI::write('2. Network tab for any failed requests', 'white');
            CLI::write('3. Ensure the walikelasField div is properly toggled', 'white');

        } catch (\Exception $e) {
            CLI::write('❌ Error: ' . $e->getMessage(), 'red');
        }
    }
}
