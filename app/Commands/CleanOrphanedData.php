<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\UserModel;
use App\Models\WalikelasModel;

class CleanOrphanedData extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Database';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'clean:orphaned';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Clean orphaned walikelas data that refers to deleted users';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'clean:orphaned [--dry-run]';

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
    protected $options = [
        '--dry-run' => 'Show what would be cleaned without actually deleting'
    ];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        $dryRun = CLI::getOption('dry-run');
        
        CLI::write('🧹 Cleaning Orphaned Walikelas Data', 'green');
        if ($dryRun) {
            CLI::write('📋 DRY RUN MODE - No data will be deleted', 'yellow');
        }
        CLI::newLine();

        try {
            $db = \Config\Database::connect();
            
            // Check for orphaned walikelas records
            CLI::write('🔍 Checking for orphaned walikelas data...', 'yellow');
            
            // Find walikelas entries that don't have corresponding users
            $orphanedQuery = $db->query("
                SELECT w.* FROM walikelas w 
                LEFT JOIN users u ON u.walikelas_id = w.id 
                WHERE u.id IS NULL
            ");
            $orphanedWalikelas = $orphanedQuery->getResultArray();
            
            if (empty($orphanedWalikelas)) {
                CLI::write('✅ No orphaned walikelas data found!', 'green');
                return;
            }
            
            CLI::write('Found ' . count($orphanedWalikelas) . ' orphaned walikelas records:', 'red');
            foreach ($orphanedWalikelas as $orphan) {
                CLI::write("  - ID: {$orphan['id']}, Kelas: {$orphan['kelas']}, Nama: {$orphan['nama']}", 'white');
            }
            CLI::newLine();
            
            if ($dryRun) {
                CLI::write('🔍 These records would be deleted (use without --dry-run to actually delete)', 'yellow');
                return;
            }
            
            // Ask for confirmation
            $confirm = CLI::prompt('Are you sure you want to delete these orphaned records? (yes/no)', ['yes', 'no']);
            if ($confirm !== 'yes') {
                CLI::write('❌ Operation cancelled', 'yellow');
                return;
            }
            
            // Delete orphaned walikelas records
            $deletedCount = 0;
            foreach ($orphanedWalikelas as $orphan) {
                $result = $db->query("DELETE FROM walikelas WHERE id = ?", [$orphan['id']]);
                if ($result) {
                    $deletedCount++;
                    CLI::write("✅ Deleted: {$orphan['kelas']} - {$orphan['nama']}", 'green');
                } else {
                    CLI::write("❌ Failed to delete: {$orphan['kelas']} - {$orphan['nama']}", 'red');
                }
            }
            
            CLI::newLine();
            CLI::write("🎉 Cleanup completed! Deleted {$deletedCount} orphaned records.", 'green');
            
            // Also check for users with invalid walikelas_id
            CLI::write('🔍 Checking for users with invalid walikelas_id...', 'yellow');
            
            $invalidUsersQuery = $db->query("
                SELECT u.id, u.username, u.nama, u.walikelas_id 
                FROM users u 
                LEFT JOIN walikelas w ON u.walikelas_id = w.id 
                WHERE u.walikelas_id IS NOT NULL AND w.id IS NULL
            ");
            $invalidUsers = $invalidUsersQuery->getResultArray();
            
            if (!empty($invalidUsers)) {
                CLI::write('Found ' . count($invalidUsers) . ' users with invalid walikelas_id:', 'red');
                foreach ($invalidUsers as $user) {
                    CLI::write("  - User: {$user['username']} ({$user['nama']}) has walikelas_id: {$user['walikelas_id']} (doesn't exist)", 'white');
                    
                    // Fix by setting walikelas_id to NULL
                    $db->query("UPDATE users SET walikelas_id = NULL WHERE id = ?", [$user['id']]);
                    CLI::write("    ✅ Fixed: Set walikelas_id to NULL", 'green');
                }
            } else {
                CLI::write('✅ No users with invalid walikelas_id found!', 'green');
            }
            
            CLI::newLine();
            CLI::write('🎯 All orphaned data has been cleaned up!', 'green');
            CLI::write('💡 You can now use the user edit form without seeing deleted users in the dropdown.', 'cyan');
            
        } catch (\Exception $e) {
            CLI::write('❌ Error during cleanup: ' . $e->getMessage(), 'red');
        }
    }
}
