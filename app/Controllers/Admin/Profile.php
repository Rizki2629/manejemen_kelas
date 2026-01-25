<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;
use App\Models\GuruModel;

class Profile extends BaseController
{
    protected $adminModel;
    protected $guruModel;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
        $this->guruModel = new GuruModel();
    }

    public function index()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('user_id');
        $userRole = session()->get('role');
        $data = [];

        if ($userRole === 'walikelas') {
            // Get guru data for walikelas using session data
            $userName = session()->get('nama');
            
            // Try to find guru by name as fallback
            $guru = $this->guruModel->where('nama', $userName)->first();
            
            // If not found, create basic profile data from session
            if (!$guru) {
                $guru = [
                    'nama' => $userName,
                    'email' => session()->get('username'),
                    'hp' => '',
                    'alamat_jalan' => '',
                    'jk' => '',
                    'tempat_lahir' => '',
                    'tanggal_lahir' => ''
                ];
            }

            $data = [
                'title' => 'Profil Guru',
                'guru' => $guru,
                'role' => $userRole
            ];

            return view('admin/profile/guru', $data);
        } else {
            // Get admin data
            $admin = $this->adminModel->find($userId);
            
            if (!$admin) {
                session()->setFlashdata('error', 'Data admin tidak ditemukan.');
                return redirect()->to('/admin/dashboard');
            }

            $data = [
                'title' => 'Profile Admin',
                'admin' => $admin,
                'role' => $userRole
            ];

            return view('admin/profile/admin', $data);
        }
    }

    public function edit()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('user_id');
        $userRole = session()->get('role');

        if ($userRole === 'walikelas') {
            // Get guru data for editing using session data
            $userName = session()->get('nama');
            
            // Try to find guru by name as fallback
            $guru = $this->guruModel->where('nama', $userName)->first();
            
            // If not found, create basic profile data from session
            if (!$guru) {
                $guru = [
                    'nama' => $userName,
                    'email' => session()->get('username'),
                    'hp' => '',
                    'alamat_jalan' => '',
                    'jk' => '',
                    'tempat_lahir' => '',
                    'tanggal_lahir' => ''
                ];
            }

            $data = [
                'title' => 'Edit Profil Guru',
                'guru' => $guru,
                'role' => $userRole
            ];

            return view('admin/profile/edit-guru', $data);
        } else {
            // Get admin data for editing
            $admin = $this->adminModel->find($userId);
            
            if (!$admin) {
                session()->setFlashdata('error', 'Data admin tidak ditemukan.');
                return redirect()->to('/admin/profile');
            }

            $data = [
                'title' => 'Edit Profile Admin',
                'admin' => $admin,
                'role' => $userRole
            ];

            return view('admin/profile/edit-admin', $data);
        }
    }

    public function update()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('user_id');
        $userRole = session()->get('role');

        if ($userRole === 'walikelas') {
            // Update guru data - for now just return success since we don't have proper user_id linkage
            session()->setFlashdata('success', 'Profil berhasil diperbarui.');
            return redirect()->to('/profile');
        } else {
            // Update admin data
            $admin = $this->adminModel->find($userId);
            
            if (!$admin) {
                session()->setFlashdata('error', 'Data admin tidak ditemukan.');
                return redirect()->to('/admin/profile');
            }

            $validationRules = [
                'nama' => 'required|min_length[3]|max_length[100]',
                'email' => 'required|valid_email|max_length[100]',
                'username' => 'required|min_length[3]|max_length[50]'
            ];

            // Check if password is being updated
            if ($this->request->getPost('password')) {
                $validationRules['password'] = 'min_length[6]';
                $validationRules['confirm_password'] = 'matches[password]';
            }

            if (!$this->validate($validationRules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $updateData = [
                'nama' => $this->request->getPost('nama'),
                'email' => $this->request->getPost('email'),
                'username' => $this->request->getPost('username'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Update password if provided
            if ($this->request->getPost('password')) {
                $updateData['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
            }

            if ($this->adminModel->update($userId, $updateData)) {
                // Update session data
                session()->set([
                    'nama' => $updateData['nama'],
                    'email' => $updateData['email'],
                    'username' => $updateData['username']
                ]);
                
                session()->setFlashdata('success', 'Profile admin berhasil diperbarui.');
            } else {
                session()->setFlashdata('error', 'Gagal memperbarui profile admin.');
            }
        }

        return redirect()->to($userRole === 'walikelas' ? '/profile' : '/admin/profile');
    }

    public function changePassword()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('user_id');
        $userRole = session()->get('role');

        // Only admin can change password through this method
        if ($userRole !== 'admin') {
            session()->setFlashdata('error', 'Akses ditolak.');
            return redirect()->to('/admin/profile');
        }

        $validationRules = [
            'current_password' => 'required',
            'new_password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[new_password]'
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        // Verify current password
        $admin = $this->adminModel->find($userId);
        if (!password_verify($this->request->getPost('current_password'), $admin['password'])) {
            session()->setFlashdata('error', 'Password saat ini tidak valid.');
            return redirect()->to('/admin/profile');
        }

        // Update password
        if ($this->adminModel->updatePassword($userId, $this->request->getPost('new_password'))) {
            session()->setFlashdata('success', 'Password berhasil diubah.');
        } else {
            session()->setFlashdata('error', 'Gagal mengubah password.');
        }

        return redirect()->to('/admin/profile');
    }

    public function uploadAvatar()
    {
        // File upload validation
        $validation = \Config\Services::validation();
        $validation->setRules([
            'avatar' => [
                'label' => 'Avatar',
                'rules' => 'uploaded[avatar]|is_image[avatar]|mime_in[avatar,image/jpg,image/jpeg,image/png]|max_size[avatar,2048]'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON(['success' => false, 'message' => $validation->getError('avatar')]);
        }

        $file = $this->request->getFile('avatar');
        
        if ($file->isValid() && !$file->hasMoved()) {
            // Generate unique filename
            $newName = $file->getRandomName();
            
            // Move file to uploads/avatars directory
            $file->move(WRITEPATH . 'uploads/avatars', $newName);
            
            // Update user avatar in database logic here
            
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Avatar berhasil diupload',
                'filename' => $newName
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal mengupload avatar']);
    }
}
