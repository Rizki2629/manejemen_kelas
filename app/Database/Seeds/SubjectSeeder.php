<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run()
    {
        if (!$this->db->tableExists('subject')) return;
        $subjects = [
            ['code'=>'MTK','name'=>'Matematika'],
            ['code'=>'BIN','name'=>'Bahasa Indonesia'],
            ['code'=>'BIG','name'=>'Bahasa Inggris'],
            ['code'=>'IPA','name'=>'Ilmu Pengetahuan Alam'],
            ['code'=>'IPS','name'=>'Ilmu Pengetahuan Sosial'],
            ['code'=>'PKN','name'=>'Pendidikan Pancasila'],
            ['code'=>'SBD','name'=>'Seni Budaya'],
        ];
        $builder = $this->db->table('subject');
        foreach ($subjects as $s) {
            // skip if already exists
            $exists = $builder->where('name', $s['name'])->get()->getFirstRow();
            if ($exists) { $builder->resetQuery(); continue; }
            $builder->insert($s);
            $builder->resetQuery();
        }
    }
}
