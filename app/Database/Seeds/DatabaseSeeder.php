<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Jalankan seeder untuk tb_guru terlebih dahulu
        $this->call('TbGuruSeeder');
        
        // Kemudian seeder untuk walikelas (yang mengacu ke guru)
        $this->call('WalikelasSeeder');
        
        // Kemudian seeder untuk users (yang mengacu ke walikelas)
        $this->call('UserSeeder');
        
        // Terakhir seeder untuk siswa
        $this->call('SiswaSeeder');
        
        echo "\nDatabase seeding completed successfully!\n";
        echo "Default login credentials:\n";
        echo "Admin: username='admin', password='123456'\n";
        echo "Walikelas: username='[NIP]', password='123456'\n";
        echo "Guru: username='[NIP]', password='123456'\n";
        echo "\nTotal Data:\n";
        echo "- Guru: 8 records\n";
        echo "- Walikelas: 5 records\n";
        echo "- Users: 9 records\n";
        echo "- Siswa: 834 records\n";
    }
}
