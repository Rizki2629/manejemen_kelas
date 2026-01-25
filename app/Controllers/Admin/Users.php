<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\WalikelasModel;
use App\Models\TbSiswaModel;

class Users extends BaseController
{
    protected $userModel;
    protected $walikelasModel;
    protected $tbSiswaModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->walikelasModel = new WalikelasModel();
        $this->tbSiswaModel = new TbSiswaModel();
    }

    /**
     * Display users list
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

        // Get all users with walikelas info
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT u.*, w.kelas 
            FROM users u 
            LEFT JOIN walikelas w ON u.walikelas_id = w.id 
            ORDER BY u.created_at DESC
        ");
        $users = $query->getResultArray();

        // Get all walikelas for dropdown
        $walikelas = $this->walikelasModel->findAll();

        $data = [
            'title' => 'Kelola User',
            'users' => $users,
            'walikelas' => $walikelas
        ];

        return view('admin/users/index', $data);
    }

    /**
     * Create new user
     */
    public function create()
    {
        // Check if user is logged in and is admin
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userRole = session()->get('role');
        if ($userRole !== 'admin') {
            return redirect()->to('/admin')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        // Get all walikelas for existing assignments
        $walikelas = $this->walikelasModel->findAll();
        
        // Get all available kelas from tb_siswa (actual classes with students)
        $tbSiswaModel = new \App\Models\TbSiswaModel();
        $availableKelas = $tbSiswaModel->getActiveClasses();

        $data = [
            'title' => 'Tambah User',
            'walikelas' => $walikelas,
            'availableKelas' => $availableKelas
        ];

        return view('admin/users/create', $data);
    }

    /**
     * Store new user
     */
    public function store()
    {
        // Check if user is logged in and is admin
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userRole = session()->get('role');
        if ($userRole !== 'admin') {
            return redirect()->to('/admin')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        // Get walikelas_id and handle it before validation
        $walikelasId = $this->request->getPost('walikelas_id');
        $finalWalikelasId = null;
        
        // Handle new class assignment (with "new_" prefix)
        if ($walikelasId && strpos($walikelasId, 'new_') === 0) {
            // Extract class name from "new_ClassName" format
            $kelasName = urldecode(substr($walikelasId, 4));
            
            // Check if this class already has a wali kelas
            $existingWali = $this->walikelasModel->where('kelas', $kelasName)->first();
            if ($existingWali) {
                return redirect()->back()->withInput()->with('error', 'Kelas ' . $kelasName . ' sudah memiliki wali kelas');
            }
            
            // Get NIP from request and validate
            $nipInput = $this->request->getPost('nip');
            if ($nipInput) {
                // Validate NIP format (must be numeric)
                if (!is_numeric($nipInput)) {
                    return redirect()->back()->withInput()->with('error', 'NIP harus berupa angka');
                }
                
                // Check uniqueness
                $existingNip = $this->walikelasModel->where('nip', $nipInput)->first();
                if ($existingNip) {
                    return redirect()->back()->withInput()->with('error', 'NIP ' . $nipInput . ' sudah digunakan');
                }
            }
            
            // Create new walikelas entry
            $walikelasData = [
                'nama' => $this->request->getPost('nama'),
                'kelas' => $kelasName,
                'nip' => $nipInput ?: $this->generateUniqueNIP(), // Use input NIP or generate unique numeric NIP
            ];
            
            $newWalikelasId = $this->walikelasModel->insert($walikelasData);
            if ($newWalikelasId) {
                $finalWalikelasId = $newWalikelasId;
            } else {
                return redirect()->back()->withInput()->with('error', 'Gagal membuat assignment kelas baru');
            }
        } else {
            // Use existing walikelas_id
            $finalWalikelasId = $walikelasId ? (int)$walikelasId : null;
        }

        // Validation rules
        $rules = [
            'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'password' => 'required|min_length[6]',
            'nama' => 'required|max_length[255]',
            'email' => 'permit_empty|valid_email|is_unique[users.email]',
            'role' => 'required|in_list[admin,walikelas,wali_kelas]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'username' => $this->request->getPost('username'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'nama' => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
            'role' => $this->request->getPost('role'),
            'walikelas_id' => $finalWalikelasId,
            'is_active' => 1
        ];

        if ($this->userModel->insert($data)) {
            return redirect()->to('/admin/users')->with('success', 'User berhasil ditambahkan');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan user');
        }
    }

    /**
     * Edit user
     */
    public function edit($id)
    {
        // Check if user is logged in and is admin
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userRole = session()->get('role');
        if ($userRole !== 'admin') {
            return redirect()->to('/admin')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        // Get user with walikelas information
        $user = $this->userModel->getUserWithWalikelas($id);
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User tidak ditemukan');
        }

        // Get all walikelas for existing assignments
        $walikelas = $this->walikelasModel->findAll();
        
        // Get all available kelas from tb_siswa (actual classes with students)
        $tbSiswaModel = new \App\Models\TbSiswaModel();
        $availableKelas = $tbSiswaModel->getActiveClasses();

        $data = [
            'title' => 'Edit User',
            'user' => $user,
            'walikelas' => $walikelas,
            'availableKelas' => $availableKelas
        ];

        return view('admin/users/edit', $data);
    }

    /**
     * Update user
     */
    public function update($id)
    {
        // Check if user is logged in and is admin
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userRole = session()->get('role');
        if ($userRole !== 'admin') {
            return redirect()->to('/admin')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User tidak ditemukan');
        }

        // Debug: Log incoming request data
        log_message('debug', 'Update User Request Data: ' . json_encode($this->request->getPost()));

        // Get walikelas_id and handle it before validation
        $walikelasId = $this->request->getPost('walikelas_id');
        $finalWalikelasId = null;
        
        // Handle new class assignment (with "new_" prefix)
        if ($walikelasId && strpos($walikelasId, 'new_') === 0) {
            // Extract class name from "new_ClassName" format
            $kelasName = urldecode(substr($walikelasId, 4));
            
            // Check if this class already has a wali kelas (except current user)
            $existingWali = $this->walikelasModel->where('kelas', $kelasName)->first();
            if ($existingWali && $user['walikelas_id'] != $existingWali['id']) {
                return redirect()->back()->withInput()->with('error', 'Kelas ' . $kelasName . ' sudah memiliki wali kelas');
            }
            
            // If user already has this class, use existing ID
            if ($existingWali && $user['walikelas_id'] == $existingWali['id']) {
                $finalWalikelasId = $existingWali['id'];
                
                // Update NIP if provided
                $nipInput = $this->request->getPost('nip');
                if ($nipInput) {
                    // Validate NIP format (must be numeric)
                    if (!is_numeric($nipInput)) {
                        return redirect()->back()->withInput()->with('error', 'NIP harus berupa angka');
                    }
                    
                    // Check if NIP is unique (excluding current walikelas)
                    $nipCheck = $this->walikelasModel->where('nip', $nipInput)->where('id !=', $existingWali['id'])->first();
                    if ($nipCheck) {
                        return redirect()->back()->withInput()->with('error', 'NIP ' . $nipInput . ' sudah digunakan');
                    }
                    // Update existing walikelas with new NIP
                    $this->walikelasModel->update($existingWali['id'], ['nip' => $nipInput]);
                }
            } else {
                // Get NIP from request and validate
                $nipInput = $this->request->getPost('nip');
                if ($nipInput) {
                    // Validate NIP format (must be numeric)
                    if (!is_numeric($nipInput)) {
                        return redirect()->back()->withInput()->with('error', 'NIP harus berupa angka');
                    }
                    
                    // Check uniqueness
                    $existingNip = $this->walikelasModel->where('nip', $nipInput)->first();
                    if ($existingNip) {
                        return redirect()->back()->withInput()->with('error', 'NIP ' . $nipInput . ' sudah digunakan');
                    }
                }
                
                // Create new walikelas entry
                $walikelasData = [
                    'nama' => $this->request->getPost('nama'),
                    'kelas' => $kelasName,
                    'nip' => $nipInput ?: $this->generateUniqueNIP(), // Use input NIP or generate unique numeric NIP
                ];
                
                $newWalikelasId = $this->walikelasModel->insert($walikelasData);
                if ($newWalikelasId) {
                    $finalWalikelasId = $newWalikelasId;
                } else {
                    return redirect()->back()->withInput()->with('error', 'Gagal membuat assignment kelas baru');
                }
            }
        } else {
            // Use existing walikelas_id
            $finalWalikelasId = $walikelasId ? (int)$walikelasId : null;
            
            // If user selects existing walikelas and provides NIP, update the walikelas record
            $nipInput = $this->request->getPost('nip');
            if ($finalWalikelasId && $nipInput) {
                // Validate NIP format (must be numeric)
                if (!is_numeric($nipInput)) {
                    return redirect()->back()->withInput()->with('error', 'NIP harus berupa angka');
                }
                
                // Get current walikelas data
                $currentWalikelas = $this->walikelasModel->find($finalWalikelasId);
                if ($currentWalikelas) {
                    // Check if NIP is unique (excluding current walikelas)
                    $nipCheck = $this->walikelasModel->where('nip', $nipInput)->where('id !=', $finalWalikelasId)->first();
                    if ($nipCheck) {
                        return redirect()->back()->withInput()->with('error', 'NIP ' . $nipInput . ' sudah digunakan');
                    }
                    // Update existing walikelas with new NIP
                    $this->walikelasModel->update($finalWalikelasId, ['nip' => $nipInput]);
                }
            }
        }

        // Validation rules
        $rules = [
            'username' => "required|min_length[3]|max_length[50]|is_unique[users.username,id,$id]",
            'nama' => 'required|max_length[255]',
            'email' => "permit_empty|valid_email|is_unique[users.email,id,$id]",
            'role' => 'required|in_list[admin,walikelas,wali_kelas]'
        ];

        // Add password validation only if password is provided
        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[6]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Prepare data
        $data = [
            'username' => $this->request->getPost('username'),
            'nama' => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email') ?: null, // Handle empty email
            'role' => $this->request->getPost('role'),
            'walikelas_id' => $finalWalikelasId,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];

        // Update password if provided
        if ($this->request->getPost('password')) {
            $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        // Debug: Log update data
        log_message('debug', 'Update User Data: ' . json_encode($data));

        try {
            // Use skipValidation to bypass model validation since we already validated in controller
            $updateResult = $this->userModel->skipValidation()->update($id, $data);
            
            // Debug: Log update result
            log_message('debug', 'Update User Result: ' . ($updateResult ? 'success' : 'failed'));
            
            if ($updateResult) {
                return redirect()->to('/admin/users')->with('success', 'User berhasil diupdate');
            } else {
                // Get database errors if any
                $errors = $this->userModel->errors();
                log_message('error', 'Update User Errors: ' . json_encode($errors));
                return redirect()->back()->withInput()->with('error', 'Gagal mengupdate user. ' . (!empty($errors) ? implode(', ', $errors) : 'Database error'));
            }
        } catch (\Exception $e) {
            log_message('error', 'Update User Exception: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    /**
     * Delete user
     */
    public function delete($id)
    {
        // Check if user is logged in and is admin
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userRole = session()->get('role');
        if ($userRole !== 'admin') {
            return redirect()->to('/admin')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User tidak ditemukan');
        }

        // Prevent deleting current logged in user
        if ($id == session()->get('user_id')) {
            return redirect()->to('/admin/users')->with('error', 'Tidak dapat menghapus user yang sedang login');
        }

        if ($this->userModel->delete($id)) {
            return redirect()->to('/admin/users')->with('success', 'User berhasil dihapus');
        } else {
            return redirect()->to('/admin/users')->with('error', 'Gagal menghapus user');
        }
    }

    /**
     * Toggle user active status via AJAX
     */
    public function toggleStatus($id)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/admin/users');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User tidak ditemukan']);
        }

        // Prevent deactivating current logged in user
        if ($id == session()->get('user_id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tidak dapat menonaktifkan user yang sedang login']);
        }

        $newStatus = $user['is_active'] ? 0 : 1;
        
        if ($this->userModel->update($id, ['is_active' => $newStatus])) {
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Status user berhasil diubah',
                'newStatus' => $newStatus
            ]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal mengubah status user']);
        }
    }

    /**
     * Generate accounts for teachers with teaching assignments
     */
    public function generateAccounts()
    {
        // Check if user is logged in and is admin
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userRole = session()->get('role');
        if ($userRole !== 'admin') {
            return redirect()->to('/admin')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        // Get teachers who have teaching assignments but no user account
        $guruModel = new \App\Models\GuruModel();
        $teachersWithAssignments = $guruModel->select('guru.id, guru.nama, guru.tugas_mengajar, guru.nip')
                                           ->where('tugas_mengajar IS NOT NULL')
                                           ->where('tugas_mengajar !=', '')
                                           ->findAll();

        if (empty($teachersWithAssignments)) {
            return redirect()->to('/admin/users')->with('error', 'Tidak ada guru dengan tugas mengajar yang ditemukan');
        }

        // Get existing users to avoid duplicates
        $existingUsers = $this->userModel->select('nama, username')->findAll();
        $existingUsernames = array_column($existingUsers, 'username');
        $existingNames = array_column($existingUsers, 'nama');

        $generated = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($teachersWithAssignments as $teacher) {
            // Skip if user already exists with same name
            if (in_array($teacher['nama'], $existingNames)) {
                $skipped++;
                continue;
            }

            // Generate username from name (lowercase, replace spaces with dots)
            $baseUsername = strtolower(str_replace(' ', '.', $teacher['nama']));
            $username = $baseUsername;
            
            // Make username unique if needed
            $counter = 1;
            while (in_array($username, $existingUsernames)) {
                $username = $baseUsername . $counter;
                $counter++;
            }

            // Generate password (use NIP if available, otherwise generate random)
            $password = $teacher['nip'] ?: $this->generateRandomPassword();

            // Create walikelas entry for the teacher's class
            $walikelasData = [
                'nama' => $teacher['nama'],
                'kelas' => $teacher['tugas_mengajar'],
                'nip' => $teacher['nip'] ?: $this->generateUniqueNIP()
            ];

            try {
                // Check if walikelas entry already exists for this class
                $existingWalikelas = $this->walikelasModel->where('kelas', $teacher['tugas_mengajar'])->first();
                
                if (!$existingWalikelas) {
                    $walikelasId = $this->walikelasModel->insert($walikelasData);
                } else {
                    $walikelasId = $existingWalikelas['id'];
                }

                // Create user account
                $userData = [
                    'username' => $username,
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'nama' => $teacher['nama'],
                    'email' => null,
                    'role' => 'walikelas',
                    'walikelas_id' => $walikelasId,
                    'is_active' => 1
                ];

                if ($this->userModel->insert($userData)) {
                    $generated++;
                    $existingUsernames[] = $username; // Add to existing list to avoid duplicates
                    $existingNames[] = $teacher['nama'];
                } else {
                    $errors++;
                }
            } catch (\Exception $e) {
                $errors++;
                log_message('error', 'Error generating account for teacher: ' . $teacher['nama'] . ' - ' . $e->getMessage());
            }
        }

        // Prepare success message
        $message = "Generate akun selesai. ";
        if ($generated > 0) {
            $message .= "{$generated} akun berhasil dibuat. ";
        }
        if ($skipped > 0) {
            $message .= "{$skipped} guru dilewati (sudah memiliki akun). ";
        }
        if ($errors > 0) {
            $message .= "{$errors} akun gagal dibuat.";
        }

        return redirect()->to('/admin/users')->with('success', $message);
    }

    /**
     * Generate only Walikelas accounts from guru with teaching assignments
     */
    public function generateWalikelasAccounts()
    {
        if (!session()->get('isLoggedIn')) return redirect()->to('/login');
        if (session()->get('role') !== 'admin') return redirect()->to('/admin')->with('error','Anda tidak memiliki akses ke halaman ini');

        $guruModel = new \App\Models\GuruModel();
        $teachers = $guruModel->select('guru.id, guru.nama, guru.tugas_mengajar, guru.nip')
                              ->where('tugas_mengajar IS NOT NULL')
                              ->where('tugas_mengajar !=', '')
                              ->findAll();

        if (empty($teachers)) return redirect()->to('/admin/users')->with('error','Tidak ada guru dengan tugas mengajar yang ditemukan');

        $existingUsers = $this->userModel->select('nama, username')->findAll();
        $existingUsernames = array_column($existingUsers, 'username');
        $existingNames = array_column($existingUsers, 'nama');

        $generated = 0; $skipped = 0; $errors = 0;
        foreach ($teachers as $t) {
            if (in_array($t['nama'], $existingNames)) { $skipped++; continue; }
            $baseUsername = strtolower(str_replace(' ', '.', $t['nama']));
            $username = $baseUsername; $i=1; while(in_array($username,$existingUsernames)){ $username=$baseUsername.$i++; }
            $password = $t['nip'] ?: $this->generateRandomPassword();

            // ensure walikelas record exists for the class
            $existingWalikelas = $this->walikelasModel->where('kelas', $t['tugas_mengajar'])->first();
            $walikelasId = $existingWalikelas ? $existingWalikelas['id'] : $this->walikelasModel->insert([
                'nama' => $t['nama'],
                'kelas' => $t['tugas_mengajar'],
                'nip' => $t['nip'] ?: $this->generateUniqueNIP(),
            ]);

            try {
                $ok = $this->userModel->insert([
                    'username' => $username,
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'nama' => $t['nama'],
                    'email' => null,
                    'role' => 'walikelas',
                    'walikelas_id' => $walikelasId,
                    'is_active' => 1,
                ]);
                if ($ok) { $generated++; $existingUsernames[]=$username; $existingNames[]=$t['nama']; }
                else { $errors++; }
            } catch (\Throwable $e) { $errors++; log_message('error','generateWalikelas error: '.$e->getMessage()); }
        }

        $msg = "Generate Wali Kelas selesai. ".$generated." akun dibuat. ";
        if ($skipped) $msg .= $skipped." dilewati. ";
        if ($errors) $msg .= $errors." gagal.";
        return redirect()->to('/admin/users')->with('success', $msg);
    }

    /**
     * Generate Siswa accounts using table tb_siswa (username: NISN, password: siswa123)
     */
    public function generateSiswaAccounts()
    {
        if (!session()->get('isLoggedIn')) return redirect()->to('/login');
        if (session()->get('role') !== 'admin') return redirect()->to('/admin')->with('error','Anda tidak memiliki akses ke halaman ini');

        // Fetch students with non-empty NISN
        $tb = $this->tbSiswaModel->where('deleted_at', null)->where('nisn !=', '')->findAll();
        if (empty($tb)) return redirect()->to('/admin/users')->with('error','Tidak ada data siswa dengan NISN');

        // Existing users by username
        $existingUsers = $this->userModel->select('username')->where('role','siswa')->findAll();
        $existingUsernames = array_column($existingUsers, 'username');

        $generated=0; $skipped=0; $errors=0;
        foreach ($tb as $s) {
            $nisn = trim((string)($s['nisn'] ?? ''));
            if ($nisn === '') { $skipped++; continue; }
            if (in_array($nisn, $existingUsernames)) { $skipped++; continue; }
            try {
                $ok = $this->userModel->insert([
                    'username' => $nisn,
                    'password' => password_hash('siswa123', PASSWORD_DEFAULT),
                    'nama' => $s['nama'] ?? 'Siswa',
                    'email' => null,
                    'role' => 'siswa',
                    'walikelas_id' => null,
                    'is_active' => 1,
                ]);
                if ($ok) { $generated++; $existingUsernames[] = $nisn; }
                else { $errors++; }
            } catch (\Throwable $e) { $errors++; log_message('error','generateSiswa error: '.$e->getMessage()); }
        }

        $msg = "Generate Siswa selesai. ".$generated." akun dibuat. ";
        if ($skipped) $msg .= $skipped." dilewati. ";
        if ($errors) $msg .= $errors." gagal.";
        return redirect()->to('/admin/users')->with('success', $msg);
    }

    /**
     * Generate random password
     */
    private function generateRandomPassword($length = 8)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $password;
    }

    /**
     * Reset user password to default
     */
    public function resetPassword($id)
    {
        // Check if user is logged in and is admin
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userRole = session()->get('role');
        if ($userRole !== 'admin') {
            return redirect()->to('/admin')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User tidak ditemukan');
        }

        // Default password for teachers
        $defaultPassword = 'guru123456';
        $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);

        // Update user password
        $updateData = [
            'password' => $hashedPassword
        ];

        try {
            if ($this->userModel->update($id, $updateData)) {
                return redirect()->to('/admin/users')->with('success', 'Password user "' . $user['nama'] . '" berhasil direset ke password default');
            } else {
                return redirect()->to('/admin/users')->with('error', 'Gagal mereset password user');
            }
        } catch (\Exception $e) {
            log_message('error', 'Reset Password Error: ' . $e->getMessage());
            return redirect()->to('/admin/users')->with('error', 'Terjadi kesalahan sistem saat mereset password');
        }
    }

    /**
     * Generate unique numeric NIP
     */
    private function generateUniqueNIP()
    {
        do {
            // Generate NIP format: 19 + YYYYMMDD + 4 random digits
            $nip = '19' . date('Ymd') . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
            
            // Check if this NIP already exists
            $existing = $this->walikelasModel->where('nip', $nip)->first();
        } while ($existing);
        
        return $nip;
    }
}
