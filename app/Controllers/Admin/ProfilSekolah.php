<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProfilSekolahModel;

class ProfilSekolah extends BaseController
{
    protected $profilSekolahModel;

    public function __construct()
    {
        $this->profilSekolahModel = new ProfilSekolahModel();
    }

    /**
     * Display profil sekolah form
     */
    public function index()
    {
        // Check if user is logged in and is admin
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userRole = session()->get('role');
        if ($userRole !== 'admin') {
            return redirect()->to('/admin')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        $data = [
            'title' => 'Profil Sekolah',
            'profilSekolah' => $this->profilSekolahModel->getProfilSekolah()
        ];

        return view('admin/profil_sekolah/index', $data);
    }

    /**
     * Save profil sekolah data
     */
    public function save()
    {
        // Check if user is logged in and is admin
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userRole = session()->get('role');
        if ($userRole !== 'admin') {
            return redirect()->to('/admin')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        // Get POST data
        $data = [
            'nama_sekolah' => $this->request->getPost('nama_sekolah'),
            'npsn' => $this->request->getPost('npsn'),
            'alamat_sekolah' => $this->request->getPost('alamat_sekolah'),
            'kurikulum' => $this->request->getPost('kurikulum'),
            'tahun_pelajaran' => $this->request->getPost('tahun_pelajaran'),
            'nama_kepala_sekolah' => $this->request->getPost('nama_kepala_sekolah'),
            'nip_kepala_sekolah' => $this->request->getPost('nip_kepala_sekolah')
        ];

        // Handle AJAX request
        if ($this->request->isAJAX()) {
            if ($this->profilSekolahModel->saveProfilSekolah($data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Profil sekolah berhasil disimpan'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyimpan profil sekolah',
                    'errors' => $this->profilSekolahModel->errors()
                ]);
            }
        }

        // Handle regular form submission
        if ($this->profilSekolahModel->saveProfilSekolah($data)) {
            return redirect()->to('/admin/profil-sekolah')->with('success', 'Profil sekolah berhasil disimpan');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->profilSekolahModel->errors());
        }
    }
}
