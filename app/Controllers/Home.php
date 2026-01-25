<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        // Use sample data instead of database for now
        $data['berita'] = [
            [
                'judul' => 'Selamat Datang di Website SDN Grogol Utara 09',
                'isi' => 'Website resmi SDN Grogol Utara 09 telah diluncurkan untuk memberikan informasi terkini kepada seluruh warga sekolah dan masyarakat.',
                'tanggal' => date('Y-m-d'),
                'gambar' => 'https://via.placeholder.com/600x400/0052cc/ffffff?text=Berita+Sekolah'
            ],
            [
                'judul' => 'Penerimaan Peserta Didik Baru Tahun Ajaran 2025/2026',
                'isi' => 'Pendaftaran PPDB untuk tahun ajaran 2025/2026 akan segera dibuka. Informasi lengkap akan diumumkan melalui website ini.',
                'tanggal' => date('Y-m-d', strtotime('-1 day')),
                'gambar' => 'https://via.placeholder.com/600x400/00b894/ffffff?text=PPDB+2025'
            ],
            [
                'judul' => 'Kegiatan Pembelajaran Tatap Muka',
                'isi' => 'Kegiatan pembelajaran tatap muka telah berjalan dengan menerapkan protokol kesehatan yang ketat untuk keamanan bersama.',
                'tanggal' => date('Y-m-d', strtotime('-2 days')),
                'gambar' => 'https://via.placeholder.com/600x400/6c5ce7/ffffff?text=Pembelajaran'
            ]
        ];
        
        return view('home', $data);
    }
}
