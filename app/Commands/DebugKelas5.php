<?php
namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class DebugKelas5 extends BaseCommand
{
    protected $group       = 'Debug';
    protected $name        = 'debug:kelas5';
    protected $description = 'Debug grade 5 class data';

    public function run(array $params)
    {
        $db = db_connect();
        
        CLI::write("=== DEBUG KELAS 5 DATA ===", 'yellow');

        // Check for students in grade 5 classes
        CLI::write("\n1. Searching for any grade 5 students in tb_siswa...", 'green');
        $grade5 = $db->query("SELECT id, nama, kelas FROM tb_siswa WHERE kelas LIKE '%5%' AND deleted_at IS NULL ORDER BY kelas, nama")->getResultArray();
        CLI::write("Found " . count($grade5) . " students in grade 5 classes:");
        foreach($grade5 as $s) {
            CLI::write("  - ID: {$s['id']}, Nama: {$s['nama']}, Kelas: {$s['kelas']}");
        }

        // Check kelas table for grade 5 classes
        CLI::write("\n2. Checking kelas table for grade 5 classes...", 'green');
        $kelas5 = $db->query("SELECT id, nama FROM kelas WHERE nama LIKE '%5%' ORDER BY nama")->getResultArray();
        CLI::write("Found " . count($kelas5) . " grade 5 classes:");
        foreach($kelas5 as $k) {
            CLI::write("  - ID: {$k['id']}, Nama: {$k['nama']}");
        }

        // Check what classes exist in tb_siswa
        CLI::write("\n3. All unique kelas values in tb_siswa...", 'green');
        $allKelas = $db->query("SELECT DISTINCT kelas, COUNT(*) as jumlah FROM tb_siswa WHERE deleted_at IS NULL GROUP BY kelas ORDER BY kelas")->getResultArray();
        CLI::write("Found " . count($allKelas) . " unique classes:");
        foreach($allKelas as $k) {
            CLI::write("  - Kelas: '{$k['kelas']}' ({$k['jumlah']} siswa)");
        }

        // Check if there's kelas_id field and its values
        CLI::write("\n4. Checking kelas_id field in tb_siswa...", 'green');
        $fields = $db->getFieldNames('tb_siswa');
        if(in_array('kelas_id', $fields)) {
            $kelasIds = $db->query("SELECT DISTINCT kelas_id, COUNT(*) as jumlah FROM tb_siswa WHERE deleted_at IS NULL GROUP BY kelas_id ORDER BY kelas_id")->getResultArray();
            CLI::write("Found kelas_id values:");
            foreach($kelasIds as $k) {
                CLI::write("  - kelas_id: {$k['kelas_id']} ({$k['jumlah']} siswa)");
            }
        } else {
            CLI::write("No kelas_id field found in tb_siswa");
        }

        // Test the controller logic for a specific kelas
        CLI::write("\n5. Testing controller logic for Kelas 5 A...", 'green');
        try {
            $canonicalName = "Kelas 5 A";
            $canonCollapsed = preg_replace('/\s+/',' ',trim(str_ireplace('KELAS','',$canonicalName)));
            $canonNoSpace = str_replace(' ','',$canonCollapsed);
            
            CLI::write("Target patterns:");
            CLI::write("  - canonCollapsed: '{$canonCollapsed}'");
            CLI::write("  - canonNoSpace: '{$canonNoSpace}'");
            
            $select = 'id,nama,nisn,kelas';
            $builder = $db->table('tb_siswa')->select($select)->where('deleted_at',null);
            
            // Add LIKE conditions
            $builder = $builder->groupStart()
                ->like('kelas',$canonCollapsed,'none')
                ->orLike('kelas',$canonNoSpace,'none')
            ->groupEnd();
            
            $builder->where('(kelas IS NULL OR kelas NOT IN ("Lulus","LULUS"))');
            $results = $builder->orderBy('nama','ASC')->get()->getResultArray();
            
            CLI::write("Raw query results: " . count($results) . " students");
            foreach($results as $r) {
                CLI::write("  - ID: {$r['id']}, Nama: {$r['nama']}, Kelas: '{$r['kelas']}'");
            }
            
            // Manual filtering
            $filtered = [];
            foreach($results as $r) {
                $val = preg_replace('/\s+/',' ',trim(str_ireplace('KELAS','',$r['kelas'] ?? '')));
                if(strcasecmp($val,$canonCollapsed)===0 || strcasecmp(str_replace(' ','',$val),$canonNoSpace)===0) {
                    $filtered[] = $r;
                }
            }
            
            CLI::write("After strict filtering: " . count($filtered) . " students");
            foreach($filtered as $r) {
                CLI::write("  - ID: {$r['id']}, Nama: {$r['nama']}, Kelas: '{$r['kelas']}'");
            }
            
        } catch(\Exception $e) {
            CLI::error("Error: " . $e->getMessage());
        }
    }
}
