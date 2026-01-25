<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class DaftarHadir extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Daftar Hadir'
        ];
        
        return view('admin/daftar-hadir/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Buat Daftar Hadir Baru'
        ];
        
        return view('admin/daftar-hadir/create', $data);
    }

    public function store()
    {
        // Validation rules
        $validation = \Config\Services::validation();
        $validation->setRules([
            'tanggal' => 'required|valid_date',
            'kelas' => 'required',
            'mata_pelajaran' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('validation', $validation);
        }

        // Store attendance data logic here
        return redirect()->to('/admin/daftar-hadir')->with('success', 'Daftar hadir berhasil dibuat');
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Edit Daftar Hadir',
            'id' => $id
        ];
        
        return view('admin/daftar-hadir/edit', $data);
    }

    public function update($id)
    {
        // Update attendance data logic here
        return redirect()->to('/admin/daftar-hadir')->with('success', 'Daftar hadir berhasil diperbarui');
    }

    public function delete($id)
    {
        // Delete attendance data logic here
        return redirect()->to('/admin/daftar-hadir')->with('success', 'Daftar hadir berhasil dihapus');
    }

    public function view($param)
    {
        // $param could be date (YYYY-MM-DD) or class name
        $data = [
            'title' => 'Lihat Daftar Hadir',
            'param' => $param
        ];
        
        return view('admin/daftar-hadir/view', $data);
    }

    public function markAttendance()
    {
        // Mark student attendance logic here
        $validation = \Config\Services::validation();
        $validation->setRules([
            'attendance_id' => 'required|numeric',
            'siswa_id' => 'required|numeric',
            'status' => 'required|in_list[hadir,tidak_hadir,izin,sakit]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak valid']);
        }

        // Process attendance marking logic here
        return $this->response->setJSON(['success' => true, 'message' => 'Kehadiran berhasil dicatat']);
    }
}
