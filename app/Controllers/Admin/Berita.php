<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BeritaModel;

class Berita extends BaseController
{
    protected $beritaModel;

    public function __construct()
    {
        $this->beritaModel = new BeritaModel();
    }

    public function index()
    {
        $data['berita'] = $this->beritaModel->findAll();
        return view('admin/berita/index', $data);
    }

    public function create()
    {
        return view('admin/berita/create');
    }

    public function store()
    {
        $data = [
            'judul' => $this->request->getPost('judul'),
            'isi' => $this->request->getPost('isi'),
            'tanggal' => $this->request->getPost('tanggal'),
            'gambar' => $this->request->getPost('gambar'),
        ];

        $this->beritaModel->insert($data);
        return redirect()->to('/admin/berita')->with('success', 'Berita berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $data['berita'] = $this->beritaModel->find($id);
        if (!$data['berita']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Berita tidak ditemukan');
        }
        return view('admin/berita/edit', $data);
    }

    public function update($id)
    {
        $data = [
            'judul' => $this->request->getPost('judul'),
            'isi' => $this->request->getPost('isi'),
            'tanggal' => $this->request->getPost('tanggal'),
            'gambar' => $this->request->getPost('gambar'),
        ];

        $this->beritaModel->update($id, $data);
        return redirect()->to('/admin/berita')->with('success', 'Berita berhasil diperbarui.');
    }

    public function delete($id)
    {
        $this->beritaModel->delete($id);
        return redirect()->to('/admin/berita')->with('success', 'Berita berhasil dihapus.');
    }

    public function uploadImage()
    {
        $img = $this->request->getFile('upload');
        if ($img && $img->isValid() && !$img->hasMoved()) {
            $newName = $img->getRandomName();
            $img->move(WRITEPATH . 'uploads', $newName);
            $url = base_url('writable/uploads/' . $newName);

            $response = [
                'uploaded' => 1,
                'fileName' => $newName,
                'url' => $url,
            ];
        } else {
            $response = [
                'uploaded' => 0,
                'error' => ['message' => 'Upload gagal.'],
            ];
        }

        return $this->response->setJSON($response);
    }
}
