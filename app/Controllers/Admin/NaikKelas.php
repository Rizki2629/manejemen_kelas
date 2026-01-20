<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SiswaModel;

class NaikKelas extends BaseController
{
    protected $siswaModel;

    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
    }

    public function index()
    {
        $session = session();
        
        // Check if user is logged in and is admin
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userRole = $session->get('role');
        if ($userRole !== 'admin') {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Akses ditolak: Hanya admin yang dapat mengakses fitur naik kelas');
        }

        // Get current class distribution
        $kelasData = $this->siswaModel->select('kelas, COUNT(*) as jumlah')
                                     ->where('kelas IS NOT NULL')
                                     ->groupBy('kelas')
                                     ->orderBy('kelas')
                                     ->findAll();

        // Get total students
        $totalSiswa = $this->siswaModel->countAllResults();

        $data = [
            'kelasData' => $kelasData,
            'totalSiswa' => $totalSiswa,
            'title' => 'Naik Kelas Per Rombel'
        ];

        return view('admin/naik-kelas/index', $data);
    }

    public function preview()
    {
        $session = session();
        
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setJSON(['error' => 'Unauthorized access']);
        }

        $fromKelas = $this->request->getPost('from_kelas');
        $toKelas = $this->request->getPost('to_kelas');

        if (empty($fromKelas) || empty($toKelas)) {
            return $this->response->setJSON(['error' => 'Kelas asal dan tujuan harus dipilih']);
        }

        // Check if target class already has students (not empty)
        $existingStudentsInTarget = $this->siswaModel->where('kelas', $toKelas)->countAllResults();
        
        if ($existingStudentsInTarget > 0) {
            return $this->response->setJSON([
                'error' => "Kelas tujuan '{$toKelas}' sudah berisi {$existingStudentsInTarget} siswa. Promosi hanya dapat dilakukan ke kelas yang kosong."
            ]);
        }

        // Get students in source class
        $siswa = $this->siswaModel->where('kelas', $fromKelas)
                                 ->orderBy('nama')
                                 ->findAll();

        if (count($siswa) === 0) {
            return $this->response->setJSON(['error' => "Kelas asal '{$fromKelas}' tidak memiliki siswa"]);
        }

        $preview = [
            'from_kelas' => $fromKelas,
            'to_kelas' => $toKelas,
            'jumlah_siswa' => count($siswa),
            'siswa' => $siswa
        ];

        return $this->response->setJSON(['success' => true, 'data' => $preview]);
    }

    public function execute()
    {
        $session = session();
        
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setJSON(['error' => 'Unauthorized access']);
        }

        $fromKelas = $this->request->getPost('from_kelas');
        $toKelas = $this->request->getPost('to_kelas');
        $confirm = $this->request->getPost('confirm');

        if (empty($fromKelas) || empty($toKelas)) {
            return $this->response->setJSON(['error' => 'Kelas asal dan tujuan harus dipilih']);
        }

        if ($confirm !== 'yes') {
            return $this->response->setJSON(['error' => 'Konfirmasi diperlukan untuk melaksanakan naik kelas']);
        }

        try {
            // Begin transaction
            $this->siswaModel->db->transStart();

            // Update all students from source class to target class
            $result = $this->siswaModel->where('kelas', $fromKelas)
                                      ->set(['kelas' => $toKelas])
                                      ->update();

            // Get affected rows count
            $affectedRows = $this->siswaModel->db->affectedRows();

            // Complete transaction
            $this->siswaModel->db->transComplete();

            if ($this->siswaModel->db->transStatus() === false) {
                return $this->response->setJSON(['error' => 'Gagal melaksanakan naik kelas']);
            }

            // Log the action
            log_message('info', "Naik kelas executed: {$fromKelas} -> {$toKelas}, {$affectedRows} students affected by user " . $session->get('username'));

            return $this->response->setJSON([
                'success' => true, 
                'message' => "Berhasil menaikkan {$affectedRows} siswa dari {$fromKelas} ke {$toKelas}",
                'affected_rows' => $affectedRows
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function batchNaikKelas()
    {
        $session = session();
        
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setJSON(['error' => 'Unauthorized access']);
        }

        // Handle JSON input
        $input = json_decode($this->request->getBody(), true);
        
        if ($input === null) {
            // Fallback to POST data
            $mappings = $this->request->getPost('mappings');
            $confirm = $this->request->getPost('confirm');
        } else {
            $mappings = $input['mappings'] ?? null;
            $confirm = $input['confirm'] ?? null;
        }

        if (empty($mappings) || !is_array($mappings)) {
            return $this->response->setJSON(['error' => 'Mapping kelas harus dipilih']);
        }

        if ($confirm !== 'yes') {
            return $this->response->setJSON(['error' => 'Konfirmasi diperlukan untuk melaksanakan naik kelas batch']);
        }

        try {
            // Begin transaction
            $this->siswaModel->db->transStart();

            $totalAffected = 0;
            $results = [];

            foreach ($mappings as $mapping) {
                $fromKelas = $mapping['from'] ?? '';
                $toKelas = $mapping['to'] ?? '';

                if (empty($fromKelas) || empty($toKelas)) {
                    continue;
                }

                // Update students
                $this->siswaModel->where('kelas', $fromKelas)
                                ->set(['kelas' => $toKelas])
                                ->update();

                $affectedRows = $this->siswaModel->db->affectedRows();
                $totalAffected += $affectedRows;

                $results[] = [
                    'from' => $fromKelas,
                    'to' => $toKelas,
                    'affected' => $affectedRows
                ];
            }

            // Complete transaction
            $this->siswaModel->db->transComplete();

            if ($this->siswaModel->db->transStatus() === false) {
                return $this->response->setJSON(['error' => 'Gagal melaksanakan naik kelas batch']);
            }

            // Log the action
            log_message('info', "Batch naik kelas executed: {$totalAffected} students affected by user " . $session->get('username'));

            return $this->response->setJSON([
                'success' => true, 
                'message' => "Berhasil menaikkan {$totalAffected} siswa dalam batch naik kelas",
                'total_affected' => $totalAffected,
                'results' => $results
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function graduateClass6()
    {
        $session = session();
        
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setJSON(['error' => 'Unauthorized access']);
        }

        // Handle JSON input
        $input = json_decode($this->request->getBody(), true);
        $confirm = $input['confirm'] ?? null;

        if ($confirm !== 'yes') {
            return $this->response->setJSON(['error' => 'Konfirmasi diperlukan untuk meluluskan semua kelas 6']);
        }

        try {
            // Find all students in grade 6 classes
            $class6Students = $this->siswaModel->like('kelas', '6', 'both')
                                              ->orLike('kelas', 'Kelas 6', 'both')
                                              ->findAll();

            if (empty($class6Students)) {
                return $this->response->setJSON(['error' => 'Tidak ada siswa kelas 6 yang ditemukan']);
            }

            // Begin transaction
            $this->siswaModel->db->transStart();

            // Update all grade 6 students to "Lulus" status
            $affectedRows = $this->siswaModel->like('kelas', '6', 'both')
                                            ->orLike('kelas', 'Kelas 6', 'both')
                                            ->set(['kelas' => 'Lulus'])
                                            ->update();

            // Complete transaction
            $this->siswaModel->db->transComplete();

            if ($this->siswaModel->db->transStatus() === false) {
                return $this->response->setJSON(['error' => 'Gagal meluluskan siswa kelas 6']);
            }

            // Log the action
            log_message('info', "Grade 6 graduation executed: {$affectedRows} students graduated by user " . $session->get('username'));

            return $this->response->setJSON([
                'success' => true, 
                'message' => "Berhasil meluluskan {$affectedRows} siswa kelas 6. Selamat kepada para lulusan! 🎓",
                'affected_rows' => $affectedRows
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function checkTargetClass()
    {
        $session = session();
        
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setJSON(['error' => 'Unauthorized access']);
        }

        $targetClass = $this->request->getPost('target_class');

        if (empty($targetClass)) {
            return $this->response->setJSON(['error' => 'Target class required']);
        }

        // Check if target class already has students
        $existingStudents = $this->siswaModel->where('kelas', $targetClass)->countAllResults();
        
        return $this->response->setJSON([
            'success' => true,
            'isEmpty' => $existingStudents === 0,
            'studentCount' => $existingStudents,
            'message' => $existingStudents === 0 ? 'Kelas tujuan kosong - siap untuk promosi' : "Kelas tujuan sudah berisi {$existingStudents} siswa"
        ]);
    }
}
