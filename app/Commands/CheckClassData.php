<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\TbSiswaModel;

class CheckClassData extends BaseCommand
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
    protected $name = 'test:classdata';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Check class data in tb_siswa table';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'test:classdata';

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
        CLI::write('Checking Class Data in tb_siswa table', 'green');
        CLI::newLine();

        try {
            $tbSiswaModel = new TbSiswaModel();
            
            // Get all active classes using the same method as the controller
            $activeClasses = $tbSiswaModel->getActiveClasses();
            
            CLI::write('Active classes from getActiveClasses():', 'yellow');
            foreach ($activeClasses as $index => $classData) {
                CLI::write("[$index] Data: " . json_encode($classData), 'white');
                if (isset($classData['kelas'])) {
                    CLI::write("     Raw kelas value: '{$classData['kelas']}'", 'cyan');
                    CLI::write("     Display as 'Kelas {$classData['kelas']}': 'Kelas {$classData['kelas']}'", 'cyan');
                }
            }
            
            CLI::newLine();
            CLI::write('Raw query to check all unique kelas values:', 'yellow');
            
            // Let's also check the raw data
            $db = \Config\Database::connect();
            $query = $db->query("SELECT DISTINCT kelas FROM tb_siswa WHERE kelas != 'Lulus' AND deleted_at IS NULL ORDER BY kelas");
            $rawClasses = $query->getResultArray();
            
            foreach ($rawClasses as $index => $classData) {
                CLI::write("[$index] Raw kelas: '{$classData['kelas']}'", 'white');
                
                // Check if the kelas value already contains "Kelas"
                if (strpos($classData['kelas'], 'Kelas') !== false) {
                    CLI::write("     ⚠️ ISSUE FOUND: This already contains 'Kelas'!", 'red');
                    CLI::write("     If displayed as 'Kelas {$classData['kelas']}' = 'Kelas {$classData['kelas']}'", 'red');
                    CLI::write("     SOLUTION: Display as just '{$classData['kelas']}'", 'green');
                } else {
                    CLI::write("     ✅ OK: Display as 'Kelas {$classData['kelas']}'", 'green');
                }
            }
            
        } catch (\Exception $e) {
            CLI::write('❌ Error: ' . $e->getMessage(), 'red');
        }
    }
}
