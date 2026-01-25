<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\WalikelasModel;

class FixNIPData extends BaseCommand
{
    protected $group = 'Database';
    protected $name = 'fix:nip-data';
    protected $description = 'Fix non-numeric NIP data in walikelas table';

    public function run(array $params)
    {
        $walikelasModel = new WalikelasModel();
        
        CLI::write('Checking walikelas NIP data...', 'yellow');
        
        // Get all walikelas
        $allWalikelas = $walikelasModel->findAll();
        
        $needUpdate = [];
        
        foreach ($allWalikelas as $wali) {
            CLI::write("ID {$wali['id']}: {$wali['nama']} - {$wali['kelas']} - NIP: '{$wali['nip']}'");
            
            // Check if NIP is non-numeric or empty
            if (!is_numeric($wali['nip']) || empty($wali['nip'])) {
                $needUpdate[] = $wali;
                CLI::write("  → Needs update (non-numeric NIP)", 'red');
            } else {
                CLI::write("  → OK (numeric NIP)", 'green');
            }
        }
        
        if (empty($needUpdate)) {
            CLI::write('All NIP values are already numeric!', 'green');
            return;
        }
        
        CLI::write("\nFound " . count($needUpdate) . " records that need NIP update.", 'yellow');
        
        foreach ($needUpdate as $wali) {
            // Generate new unique NIP
            do {
                $newNIP = $this->generateUniqueNIP();
                $existing = $walikelasModel->where('nip', $newNIP)->first();
            } while ($existing);
            
            // Update the record
            $success = $walikelasModel->update($wali['id'], ['nip' => $newNIP]);
            
            if ($success) {
                CLI::write("✅ Updated ID {$wali['id']} ({$wali['nama']}) with NIP: $newNIP", 'green');
            } else {
                CLI::write("❌ Failed to update ID {$wali['id']} ({$wali['nama']})", 'red');
            }
        }
        
        CLI::write("\n✅ NIP fix process completed!", 'green');
    }
    
    private function generateUniqueNIP()
    {
        // Generate NIP format: 19 + YYYYMMDD + 4 random digits
        return '19' . date('Ymd') . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
    }
}
