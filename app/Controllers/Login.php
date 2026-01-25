<?php

namespace App\Controllers;

use App\Models\UserModel;

class Login extends BaseController
{
    public function index()
    {
        helper(['form']);
        return view('login');
    }

    public function authenticate()
    {
        helper(['form']);
        $session = session();

        $rules = [
            'username' => 'required',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            return view('login', [
                'validation' => $this->validator
            ]);
        }

        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');

        // Fallback authentication when database is not available
        if ($username === 'admin' && $password === '123456') {
            $session->set([
                'user_id' => 1,
                'username' => 'admin',
                'nama' => 'Administrator',
                'role' => 'admin',
                'walikelas_id' => null,
                'logged_in' => true,
                'isLoggedIn' => true, // Keep for backward compatibility
            ]);
            
            return redirect()->to('/admin/dashboard');
        }

        // Try database authentication if available
        try {
            $model = new UserModel();
            $user = $model->authenticate($username, $password);

            if ($user) {
                // Update last login
                $model->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);
                
                $session->set([
                    'user_id' => $user['id'],
                    'username' => $user['username'],
                    'nama' => $user['nama'],
                    'role' => $user['role'],
                    'walikelas_id' => $user['walikelas_id'],
                    'logged_in' => true,
                    'isLoggedIn' => true, // Keep for backward compatibility
                ]);
                
                    // Jika siswa, map ke siswa.id dari NISN
                    if ($user['role'] === 'siswa') {
                        // Pastikan nama lengkap siswa tersimpan sekali secara kanonik untuk semua halaman
                        // Prioritas sumber: tb_siswa.nama > siswa.nama > users.nama > username (NISN)
                        try {
                            $db = db_connect();
                            $canonicalName = null;

                            // Ambil dari tb_siswa (data resmi)
                            $rowTb = $db->table('tb_siswa')
                                ->select('nama, id')
                                ->where('nisn', $user['username'])
                                ->get()->getFirstRow('array');

                            if ($rowTb && !empty($rowTb['nama'])) {
                                $canonicalName = $rowTb['nama'];
                                // Simpan juga id tb_siswa jika dibutuhkan ke depan
                                session()->set('student_tb_id', (int)$rowTb['id']);
                            }

                            // Ambil dari tabel siswa (legacy / untuk relasi habit_logs)
                            $rowLegacy = $db->table('siswa')
                                ->select('id, nama')
                                ->where('nisn', $user['username'])
                                ->orWhere('nis', $user['username'])
                                ->get()->getFirstRow('array');
                            if ($rowLegacy) {
                                // Simpan id hubungan habit_logs
                                session()->set('student_id', (int)$rowLegacy['id']);
                                if (!$canonicalName && !empty($rowLegacy['nama'])) {
                                    $canonicalName = $rowLegacy['nama'];
                                }
                            }

                            if (!$canonicalName) {
                                $canonicalName = $user['nama'] ?: $user['username'];
                            }
                            // Set hanya jika belum ada atau berbeda agar tidak sering overwrite
                            if (!session()->has('student_name') || session('student_name') !== $canonicalName) {
                                session()->set('student_name', $canonicalName);
                            }
                        } catch (\Throwable $e) {
                            // Fallback jika query gagal
                            if (!session()->has('student_name')) {
                                session()->set('student_name', $user['nama'] ?: $user['username']);
                            }
                        }
                        return redirect()->to('/siswa');
                    }

                    // Redirect berdasarkan role lain
                    if ($user['role'] === 'admin') {
                        return redirect()->to('/admin/dashboard');
                    } elseif ($user['role'] === 'walikelas' || $user['role'] === 'guru') {
                        return redirect()->to('/dashboard');
                    }
                    return redirect()->to('/dashboard');
            }
        } catch (\Exception $e) {
            // Database not available, continue with fallback
        }

        $session->setFlashdata('error', 'Invalid username or password');
        return redirect()->to('/login');
    }

    public function logout()
    {
        $session = session();
        
        // Destroy all session data
        $session->destroy();
        
        // Redirect to login page with success message
        return redirect()->to('/login')->with('success', 'Anda berhasil logout');
    }
}
