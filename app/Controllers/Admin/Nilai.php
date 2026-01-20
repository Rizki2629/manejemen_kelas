<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\NilaiModel;
use App\Models\TbSiswaModel;
use App\Models\WalikelasModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class Nilai extends BaseController
{
    protected $nilaiModel;
    protected $tbSiswaModel;
    protected $walikelasModel;

    public function __construct()
    {
        $this->nilaiModel = new NilaiModel();
        $this->tbSiswaModel = new TbSiswaModel();
        $this->walikelasModel = new WalikelasModel();
    }

    private function syncNilaiIdSequenceIfPostgres($db): void
    {
        try {
            $driver = '';
            try { $driver = (string) ($db->DBDriver ?? ''); } catch (\Throwable $e) { $driver = ''; }
            try { $driver = $driver ?: (string) ($db->getPlatform() ?? ''); } catch (\Throwable $e) { /* ignore */ }
            if (stripos($driver, 'Postgre') === false && stripos($driver, 'pgsql') === false) {
                return;
            }

            // If the sequence is behind (common after migrations), inserts can hit duplicate PK.
            // Set sequence to MAX(id) so nextval returns MAX(id)+1.
            $db->query("SELECT setval(pg_get_serial_sequence('nilai','id'), COALESCE((SELECT MAX(id) FROM nilai), 0))");
        } catch (\Throwable $e) {
            // Best-effort only.
        }
    }

    /**
     * Index - List nilai by mata pelajaran
     */
    public function index()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userRole = session()->get('role');
        $userId = session()->get('user_id');

        if (!in_array($userRole, ['admin', 'wali_kelas', 'walikelas'])) {
            return redirect()->to('/admin')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        // Get user's class if wali kelas
        $userKelas = null;
        if ($userRole === 'wali_kelas' || $userRole === 'walikelas') {
            $db = \Config\Database::connect();
            $query = $db->query("
                SELECT w.kelas 
                FROM users u 
                JOIN walikelas w ON u.walikelas_id = w.id 
                WHERE u.id = ?
            ", [$userId]);
            
            $result = $query->getRowArray();
            if (!$result) {
                return redirect()->to('/admin')->with('error', 'Data wali kelas tidak ditemukan');
            }
            $userKelas = $result['kelas'];
        }

        // Get selected parameters
        $selectedKelas = $this->request->getVar('kelas') ?? $userKelas;
        $selectedMapel = $this->request->getVar('mapel') ?? 'IPAS';

        // Get all classes for admin
        $allKelas = [];
        if ($userRole === 'admin') {
            $kelasData = $this->tbSiswaModel->getActiveClasses();
            $allKelas = array_column($kelasData, 'kelas');
        }

        // Get nilai rekap
        $nilaiRekap = [];
        if ($selectedKelas) {
            $nilaiRekap = $this->nilaiModel->getNilaiRekap($selectedKelas, $selectedMapel);
        }

        // Get mata pelajaran list
        $mataPelajaranList = $this->nilaiModel->getMataPelajaranList();

        $data = [
            'title' => 'Daftar Nilai - ' . ($mataPelajaranList[$selectedMapel] ?? $selectedMapel),
            'userRole' => $userRole,
            'userKelas' => $userKelas,
            'selectedKelas' => $selectedKelas,
            'selectedMapel' => $selectedMapel,
            'allKelas' => $allKelas,
            'nilaiRekap' => $nilaiRekap,
            'mataPelajaranList' => $mataPelajaranList
        ];

        return view('admin/nilai/index', $data);
    }

    /**
     * Form tambah nilai
     */
    public function create()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userRole = session()->get('role');
        $userId = session()->get('user_id');

        if (!in_array($userRole, ['admin', 'wali_kelas', 'walikelas'])) {
            return redirect()->to('/admin')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        // Get user's class if wali kelas
        $userKelas = null;
        if ($userRole === 'wali_kelas' || $userRole === 'walikelas') {
            $db = \Config\Database::connect();
            $query = $db->query("
                SELECT w.kelas 
                FROM users u 
                JOIN walikelas w ON u.walikelas_id = w.id 
                WHERE u.id = ?
            ", [$userId]);
            
            $result = $query->getRowArray();
            if (!$result) {
                return redirect()->to('/admin')->with('error', 'Data wali kelas tidak ditemukan');
            }
            $userKelas = $result['kelas'];
        }

        // Get parameters
        $selectedKelas = $this->request->getVar('kelas') ?? $userKelas;
        $selectedMapel = $this->request->getVar('mapel') ?? 'IPAS';
        $selectedSiswaId = $this->request->getVar('siswa_id') ?? '';

        // Get all classes for admin
        $allKelas = [];
        if ($userRole === 'admin') {
            $kelasData = $this->tbSiswaModel->getActiveClasses();
            $allKelas = array_column($kelasData, 'kelas');
        }

        // Get students in selected class
        $students = [];
        if ($selectedKelas) {
            $students = $this->tbSiswaModel->where('kelas', $selectedKelas)
                                          ->where('deleted_at IS NULL')
                                          ->orderBy('nama', 'ASC')
                                          ->findAll();
        }

        // Get mata pelajaran and jenis nilai list
        $mataPelajaranList = $this->nilaiModel->getMataPelajaranList();
        $jenisNilaiList = $this->nilaiModel->getJenisNilaiList();
        
        // Get selected siswa ID from request
        $selectedSiswaId = $this->request->getVar('siswa_id');

        $data = [
            'title' => 'Tambah Nilai - ' . ($mataPelajaranList[$selectedMapel] ?? $selectedMapel),
            'userRole' => $userRole,
            'userKelas' => $userKelas,
            'selectedKelas' => $selectedKelas,
            'selectedMapel' => $selectedMapel,
            'selectedSiswaId' => $selectedSiswaId,
            'allKelas' => $allKelas,
            'students' => $students,
            'mataPelajaranList' => $mataPelajaranList,
            'jenisNilaiList' => $jenisNilaiList
        ];

        return view('admin/nilai/create', $data);
    }

    /**
     * Store nilai
     */
    public function store()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userRole = session()->get('role');
        $userId = session()->get('user_id');

        if (!in_array($userRole, ['admin', 'wali_kelas', 'walikelas'])) {
            return redirect()->to('/admin')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        // Get form data
        $siswaId = $this->request->getPost('siswa_id');
        $mataPelajaran = $this->request->getPost('mata_pelajaran');
        $jenisNilai = $this->request->getPost('jenis_nilai');
        $nilai = $this->request->getPost('nilai');
        $tpMateri = $this->request->getPost('tp_materi');
        $tanggal = $this->request->getPost('tanggal');
        $kelas = $this->request->getPost('kelas');

        // Validate access to class
        if (!$this->nilaiModel->canAccessClass($userId, $kelas, $userRole)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke kelas tersebut');
        }

        // Prepare data
        $data = [
            'siswa_id' => $siswaId,
            'mata_pelajaran' => $mataPelajaran,
            'jenis_nilai' => $jenisNilai,
            'nilai' => $nilai,
            'tp_materi' => $tpMateri,
            'tanggal' => $tanggal,
            'kelas' => $kelas,
            'created_by' => $userId,
            'updated_by' => $userId
        ];

        // Save data
        if ($this->nilaiModel->save($data)) {
            return redirect()->to('/admin/nilai?kelas=' . $kelas . '&mapel=' . $mataPelajaran)
                           ->with('success', 'Nilai berhasil ditambahkan');
        } else {
            return redirect()->back()->withInput()
                           ->with('error', 'Gagal menambahkan nilai')
                           ->with('validation', $this->nilaiModel->errors());
        }
    }

    /**
     * Detail nilai siswa
     */
    public function detail($siswaId)
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userRole = session()->get('role');
        $userId = session()->get('user_id');

        if (!in_array($userRole, ['admin', 'wali_kelas', 'walikelas'])) {
            return redirect()->to('/admin')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        // Get parameters
        $selectedMapel = $this->request->getVar('mapel') ?? 'IPAS';

        // Get student data
        $student = $this->tbSiswaModel->find($siswaId);
        if (!$student) {
            return redirect()->to('/admin/nilai')->with('error', 'Siswa tidak ditemukan');
        }

        // Check access to student's class
        if (!$this->nilaiModel->canAccessClass($userId, $student['kelas'], $userRole)) {
            return redirect()->to('/admin/nilai')->with('error', 'Anda tidak memiliki akses ke kelas tersebut');
        }

        // Get nilai detail
        $nilaiDetail = $this->nilaiModel->getNilaiDetailSiswa($siswaId, $selectedMapel);

        // Get mata pelajaran list
        $mataPelajaranList = $this->nilaiModel->getMataPelajaranList();

        $data = [
            'title' => 'Detail Nilai - ' . $student['nama'],
            'student' => $student,
            'selectedMapel' => $selectedMapel,
            'nilaiDetail' => $nilaiDetail,
            'mataPelajaranList' => $mataPelajaranList
        ];

        return view('admin/nilai/detail', $data);
    }

    /**
     * Edit nilai
     */
    public function edit($id)
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userRole = session()->get('role');
        $userId = session()->get('user_id');

        if (!in_array($userRole, ['admin', 'wali_kelas', 'walikelas'])) {
            return redirect()->to('/admin')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        // Get nilai data
        $nilai = $this->nilaiModel->find($id);
        if (!$nilai) {
            return redirect()->to('/admin/nilai')->with('error', 'Nilai tidak ditemukan');
        }

        // Check access to class
        if (!$this->nilaiModel->canAccessClass($userId, $nilai['kelas'], $userRole)) {
            return redirect()->to('/admin/nilai')->with('error', 'Anda tidak memiliki akses ke nilai tersebut');
        }

        // Get student data
        $student = $this->tbSiswaModel->find($nilai['siswa_id']);

        // Get mata pelajaran and jenis nilai list
        $mataPelajaranList = $this->nilaiModel->getMataPelajaranList();
        $jenisNilaiList = $this->nilaiModel->getJenisNilaiList();

        $data = [
            'title' => 'Edit Nilai - ' . $student['nama'],
            'nilai' => $nilai,
            'student' => $student,
            'mataPelajaranList' => $mataPelajaranList,
            'jenisNilaiList' => $jenisNilaiList
        ];

        return view('admin/nilai/edit', $data);
    }

    /**
     * Update nilai
     */
    public function update($id)
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userRole = session()->get('role');
        $userId = session()->get('user_id');

        if (!in_array($userRole, ['admin', 'wali_kelas', 'walikelas'])) {
            return redirect()->to('/admin')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        // Get existing nilai
        $existingNilai = $this->nilaiModel->find($id);
        if (!$existingNilai) {
            return redirect()->to('/admin/nilai')->with('error', 'Nilai tidak ditemukan');
        }

        // Check access to class
        if (!$this->nilaiModel->canAccessClass($userId, $existingNilai['kelas'], $userRole)) {
            return redirect()->to('/admin/nilai')->with('error', 'Anda tidak memiliki akses ke nilai tersebut');
        }

        // Get form data
        $data = [
            'mata_pelajaran' => $this->request->getPost('mata_pelajaran'),
            'jenis_nilai' => $this->request->getPost('jenis_nilai'),
            'nilai' => $this->request->getPost('nilai'),
            'tp_materi' => $this->request->getPost('tp_materi'),
            'tanggal' => $this->request->getPost('tanggal'),
            'updated_by' => $userId
        ];

        // Update data
        if ($this->nilaiModel->update($id, $data)) {
            return redirect()->to('/admin/nilai?kelas=' . $existingNilai['kelas'] . '&mapel=' . $existingNilai['mata_pelajaran'])
                           ->with('success', 'Nilai berhasil diperbarui');
        } else {
            return redirect()->back()->withInput()
                           ->with('error', 'Gagal memperbarui nilai')
                           ->with('validation', $this->nilaiModel->errors());
        }
    }

    /**
     * Delete nilai
     */
    public function delete($id)
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userRole = session()->get('role');
        $userId = session()->get('user_id');

        if (!in_array($userRole, ['admin', 'wali_kelas', 'walikelas'])) {
            return redirect()->to('/admin')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        // Get nilai data
        $nilai = $this->nilaiModel->find($id);
        if (!$nilai) {
            return redirect()->to('/admin/nilai')->with('error', 'Nilai tidak ditemukan');
        }

        // Check access to class
        if (!$this->nilaiModel->canAccessClass($userId, $nilai['kelas'], $userRole)) {
            return redirect()->to('/admin/nilai')->with('error', 'Anda tidak memiliki akses ke nilai tersebut');
        }

        // Delete data
        if ($this->nilaiModel->delete($id)) {
            return redirect()->to('/admin/nilai?kelas=' . $nilai['kelas'] . '&mapel=' . $nilai['mata_pelajaran'])
                           ->with('success', 'Nilai berhasil dihapus');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus nilai');
        }
    }

    /**
     * Data TP (Tujuan Pembelajaran)
     */
    public function dataTP()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userRole = session()->get('role');
        $userId = session()->get('user_id');

        if (!in_array($userRole, ['admin', 'wali_kelas', 'walikelas'])) {
            return redirect()->to('/admin')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        // Get user's class if wali kelas
        $userKelas = null;
        if ($userRole === 'wali_kelas' || $userRole === 'walikelas') {
            $db = \Config\Database::connect();
            $query = $db->query("
                SELECT w.kelas 
                FROM users u 
                JOIN walikelas w ON u.walikelas_id = w.id 
                WHERE u.id = ?
            ", [$userId]);
            
            $result = $query->getRow();
            if ($result) {
                $userKelas = $result->kelas;
            }
        }

        $data = [
            'title' => 'Data Tujuan Pembelajaran',
            'userRole' => $userRole,
            'userKelas' => $userKelas,
            'currentUser' => [
                'nama' => session()->get('nama'),
                'role' => session()->get('role')
            ]
        ];

        return view('admin/nilai/data_tp', $data);
    }

    /**
     * Input Nilai
     */
    public function inputNilai($kelasSlug = null, $mapelSlug = null)
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userRole = session()->get('role');
        $userId = session()->get('user_id');

        if (!in_array($userRole, ['admin', 'wali_kelas', 'walikelas'])) {
            return redirect()->to('/admin')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        // Get user's class if wali kelas
        $userKelas = null;
        if ($userRole === 'wali_kelas' || $userRole === 'walikelas') {
            $db = \Config\Database::connect();
            $query = $db->query("
                SELECT w.kelas 
                FROM users u 
                JOIN walikelas w ON u.walikelas_id = w.id 
                WHERE u.id = ?
            ", [$userId]);
            
            $result = $query->getRow();
            if ($result) {
                $userKelas = $result->kelas;
            }
        }

        // Get available classes (exclude 'Lulus')
        $availableClasses = [];
        if ($userRole === 'admin') {
            $availableClasses = $this->tbSiswaModel->select('kelas')
                ->distinct()
                ->where('kelas !=', 'Lulus')
                ->orderBy('kelas', 'ASC')
                ->findAll();
        } else if ($userKelas && $userKelas !== 'Lulus') {
            $availableClasses = [['kelas' => $userKelas]];
        }

        // Legacy query param support -> if query params present but route segments missing, redirect (canonicalize)
        $legacyKelas = $this->request->getVar('kelas');
        $legacyMapel = $this->request->getVar('mapel');
        if((!$kelasSlug || !$mapelSlug) && $legacyKelas && $legacyMapel){
            $target = '/admin/nilai/input/' . $this->slugifyKelas($legacyKelas) . '/' . $this->slugifyMapel($legacyMapel);
            return redirect()->to($target, 301);
        }

        // Get students for selected class
    $students = [];
    // Support pretty URL slugs or fallback to query params
    $selectedKelas = $kelasSlug ? $this->unslugKelas($kelasSlug) : $this->request->getVar('kelas');
    $selectedMapel = $mapelSlug ? $this->unslugMapel($mapelSlug) : $this->request->getVar('mapel');
        $harianMatrix = null;
        
        if ($selectedKelas) {
            $students = $this->tbSiswaModel->where('kelas', $selectedKelas)
                                          ->where('deleted_at IS NULL')
                                          ->orderBy('nama', 'ASC')
                                          ->findAll();
            if ($selectedMapel) {
                // Cached fetch (60s) to reduce repeated heavy grouping
                $harianMatrix = $this->nilaiModel->getCachedNilaiHarianMatrix($selectedKelas, $selectedMapel, 60);
            }
        }

        // Dynamic subjects (mapel) from subjects table filtered by grade level if possible
        $subjectsDynamic = [];
        try {
            $subjectModel = new \App\Models\SubjectModel();
            $allSubjects = $subjectModel->listAll();
            // If selected class like "5 A" ambil angka awal sebagai grade
            $gradeNumber = null;
            if ($selectedKelas) {
                if (preg_match('/^(\d+)/', $selectedKelas, $m)) {
                    $gradeNumber = (int)$m[1];
                }
            }
            foreach ($allSubjects as $s) {
                if (!empty($s['grades']) && $gradeNumber) {
                    $grades = array_filter(array_map('intval', explode(',', $s['grades'])));
                    if ($grades && !in_array($gradeNumber, $grades)) {
                        continue; // skip not for this grade
                    }
                }
                $subjectsDynamic[] = $s['name'];
            }
        } catch(\Throwable $e) {}

        $data = [
            'title' => 'Input Nilai Siswa',
            'userRole' => $userRole,
            'userKelas' => $userKelas,
            'availableClasses' => $availableClasses,
            'students' => $students,
            'selectedKelas' => $selectedKelas,
            'selectedMapel' => $selectedMapel,
            'harianMatrix' => $harianMatrix,
            'subjectsDynamic' => $subjectsDynamic,
            'kelasSlug' => $selectedKelas ? $this->slugifyKelas($selectedKelas) : null,
            'mapelSlug' => $selectedMapel ? $this->slugifyMapel($selectedMapel) : null,
            'currentUser' => [
                'nama' => session()->get('nama'),
                'role' => session()->get('role')
            ]
        ];

        return view('admin/nilai/input', $data);
    }

    private function slugifyMapel(string $mapel): string
    {
        $mapel = strtolower(trim($mapel));
        $mapel = preg_replace('/[^a-z0-9]+/','-', $mapel);
        return trim($mapel,'-');
    }

    private function slugifyKelas(string $kelas): string
    {
        $kelas = strtolower(trim($kelas));
        $kelas = str_replace([' ','/'], '-', $kelas);
        return preg_replace('/[^a-z0-9\-]+/','', $kelas);
    }

    private function unslugKelas(?string $slug): ?string
    {
        if(!$slug) return null;
        // revert dashes to spaces & capitalize words with 'kelas'
        $slug = str_replace('-', ' ', $slug);
        // ensure leading 'kelas' capitalized
        return ucwords($slug);
    }

    private function unslugMapel(?string $slug): ?string
    {
        if(!$slug) return null;
        $slug = str_replace('-', ' ', $slug);
        return ucwords($slug);
    }

    /**
     * Cetak Nilai
     */
    public function cetakNilai()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userRole = session()->get('role');
        $userId = session()->get('user_id');

        if (!in_array($userRole, ['admin', 'wali_kelas', 'walikelas'])) {
            return redirect()->to('/admin')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        // Get user's class if wali kelas
        $userKelas = null;
        if ($userRole === 'wali_kelas' || $userRole === 'walikelas') {
            $db = \Config\Database::connect();
            $query = $db->query("
                SELECT w.kelas 
                FROM users u 
                JOIN walikelas w ON u.walikelas_id = w.id 
                WHERE u.id = ?
            ", [$userId]);
            
            $result = $query->getRow();
            if ($result) {
                $userKelas = $result->kelas;
            }
        }

        // Get available classes (exclude 'Lulus' for cleanliness)
        $availableClasses = [];
        if ($userRole === 'admin') {
            $availableClasses = $this->tbSiswaModel->select('kelas')
                ->distinct()
                ->where('kelas !=', 'Lulus')
                ->orderBy('kelas', 'ASC')
                ->findAll();
        } else if ($userKelas) {
            $availableClasses = [['kelas' => $userKelas]];
        }

        // Selected params (GET)
        $selectedKelas = $this->request->getGet('kelas');
        $selectedMapel = $this->request->getGet('mapel');

        // Guard walikelas: force kelas to their class if mismatch
        if (($userRole === 'wali_kelas' || $userRole === 'walikelas') && $userKelas) {
            if (!$selectedKelas || $selectedKelas !== $userKelas) {
                $selectedKelas = $userKelas; // override
            }
        }

        // Mata pelajaran list (canonical ordering)
        $mataPelajaranList = $this->nilaiModel->getMataPelajaranList();

    $reportRows = [];
    // Dynamic PH columns: show minimum 4 columns; if more assessments exist show up to their count (capped at 10)
    $phHeaderCount = 4; // will be recalculated after we know actual
    $phActualCount = 0;  // number of actual PH assessments (capped 10)
        $students = [];

        if ($selectedKelas && $selectedMapel) {
            // Access check for kelas
            if (!$this->nilaiModel->canAccessClass($userId, $selectedKelas, $userRole)) {
                return redirect()->to('/admin')->with('error', 'Anda tidak memiliki akses ke kelas tersebut');
            }

            // Students list
            $students = $this->tbSiswaModel->where('kelas', $selectedKelas)
                ->where('deleted_at IS NULL', null, false)
                ->orderBy('nama', 'ASC')
                ->findAll();

            // Harian matrix to derive PH values (chronological) then limit to 10
            $matrix = $this->nilaiModel->getNilaiHarianMatrix($selectedKelas, $selectedMapel);
            $headers = $matrix['headers'] ?? [];
            if ($headers) {
                $phActualCountTotal = min(count($headers), 10); // actual available up to 10
                $phActualCount = $phActualCountTotal; // store actual count used for averaging
                $phHeaderCount = max($phActualCountTotal, 4); // ensure at least 4 columns shown even if fewer assessments
            } else {
                // no headers -> still show 4 empty PH columns
                $phHeaderCount = 4; $phActualCount = 0;
            }

            // Build fast lookup for harian values per siswa
            $values = $matrix['values'] ?? [];

            // Fetch PTS & PAS for entire kelas+mapel (single query)
            $examRows = $this->nilaiModel->where('deleted_at IS NULL', null, false)
                ->where('kelas', $selectedKelas)
                ->where('mata_pelajaran', $selectedMapel)
                ->whereIn('jenis_nilai', ['pts', 'pas'])
                ->findAll();
            $ptsMap = [];
            $pasMap = [];
            foreach ($examRows as $er) {
                if ($er['jenis_nilai'] === 'pts') { $ptsMap[$er['siswa_id']] = (float)$er['nilai']; }
                if ($er['jenis_nilai'] === 'pas') { $pasMap[$er['siswa_id']] = (float)$er['nilai']; }
            }

            // Assemble rows
            $noUrut = 1;
            foreach ($students as $stu) {
                $sid = $stu['id'];
                $phScores = [];
                $sum = 0; $count = 0;
                for ($i = 1; $i <= $phHeaderCount; $i++) {
                    if ($i <= $phActualCount) {
                        $val = $values[$sid][$i] ?? null; // matrix uses 1-based column index
                        if ($val !== null && $val !== '') {
                            $phScores[$i] = $val;
                            $sum += (float)$val; $count++;
                        } else {
                            $phScores[$i] = '';
                        }
                    } else {
                        $phScores[$i] = '';
                    }
                }
                $avgPH = $count > 0 ? round($sum / $count, 2) : '';
                $pts = isset($ptsMap[$sid]) ? $ptsMap[$sid] : '';
                $pas = isset($pasMap[$sid]) ? $pasMap[$sid] : '';
                $nilaiRapot = ($avgPH !== '' && $pts !== '' && $pas !== '') ? round(($avgPH + $pts + $pas) / 3, 2) : '';
                $reportRows[] = [
                    'no' => $noUrut++,
                    'nisn' => $stu['nisn'] ?? '',
                    'nama' => $stu['nama'] ?? '',
                    'ph' => $phScores,
                    'avg_ph' => $avgPH,
                    'pts' => $pts,
                    'pas' => $pas,
                    'rapot' => $nilaiRapot,
                ];
            }
        }

        $data = [
            'title' => 'Cetak Nilai Siswa',
            'userRole' => $userRole,
            'userKelas' => $userKelas,
            'availableClasses' => $availableClasses,
            'mataPelajaranList' => $mataPelajaranList,
            'selectedKelas' => $selectedKelas,
            'selectedMapel' => $selectedMapel,
            'reportRows' => $reportRows,
            'phHeaderCount' => $phHeaderCount,
            'phActualCount' => $phActualCount,
            'currentUser' => [
                'nama' => session()->get('nama'),
                'role' => session()->get('role')
            ],
            // inject profil sekolah & walikelas data for print signature
            'profilSekolah' => (function(){
                try { $m = new \App\Models\ProfilSekolahModel(); return $m->getProfilSekolah(); } catch(\Throwable $e){ return []; }
            })(),
            'walikelasData' => (function() use ($selectedKelas){
                if(!$selectedKelas) return [];
                try {
                    $db = \Config\Database::connect();
                    // 1) Coba tabel 'guru' (skema import JSON) berdasarkan tugas_mengajar
                    $hasGuru = $db->tableExists('guru');
                    if($hasGuru){
                        $hasGelarGuru = false; try { $hasGelarGuru = $db->fieldExists('gelar','guru'); } catch(\Throwable $e) {}
                        $selectCols = 'nama, nip, tugas_mengajar' . ($hasGelarGuru ? ', gelar' : '');
                        $row = $db->table('guru')
                            ->select($selectCols)
                            ->where('tugas_mengajar', $selectedKelas)
                            ->orderBy('id','ASC')
                            ->get()->getRowArray();
                        if($row){
                            return [
                                'nama' => $row['nama'],
                                'nip'  => $row['nip'],
                                'kelas'=> $row['tugas_mengajar'],
                                'gelar'=> $row['gelar'] ?? null,
                            ];
                        }
                    }
                    // 2) Coba tabel 'tb_guru' (skema migrasi tb_guru) berdasarkan jabatan = 'Wali Kelas {kelas}'
                    $hasTbGuru = $db->tableExists('tb_guru');
                    if($hasTbGuru){
                        $jabatanTarget = 'Wali Kelas ' . $selectedKelas; // contoh: Wali Kelas 5 A
                        $row2 = $db->table('tb_guru')
                            ->select('nama_lengkap, gelar, nip, jabatan')
                            ->where('jabatan', $jabatanTarget)
                            ->orderBy('id','ASC')
                            ->get()->getRowArray();
                        if(!$row2){
                            // fallback like search (kadang format jabatan berbeda)
                            $row2 = $db->table('tb_guru')
                                ->select('nama_lengkap, gelar, nip, jabatan')
                                ->like('jabatan','Wali Kelas')
                                ->like('jabatan', $selectedKelas)
                                ->orderBy('id','ASC')
                                ->get()->getRowArray();
                        }
                        if($row2){
                            return [
                                'nama' => $row2['nama_lengkap'],
                                'nip'  => $row2['nip'],
                                'kelas'=> $selectedKelas,
                                'gelar'=> $row2['gelar'] ?? null,
                            ];
                        }
                    }
                    // 3) Fallback lama: tabel walikelas
                    if($db->tableExists('walikelas')){
                        $legacy = $db->table('walikelas')->where('kelas',$selectedKelas)->get()->getRowArray();
                        if($legacy){
                            return [
                                'nama' => $legacy['nama'] ?? ($legacy['walikelas_nama'] ?? null),
                                'nip'  => $legacy['nip'] ?? null,
                                'kelas'=> $selectedKelas,
                                'gelar'=> null,
                            ];
                        }
                    }
                } catch(\Throwable $e){ return []; }
                return [];
            })()
        ];

        return view('admin/nilai/cetak', $data);
    }

    /**
     * Store bulk nilai harian (AJAX)
     */
    public function storeBulkHarian()
    {
        if (strtoupper($this->request->getMethod()) !== 'POST') {
            return $this->response->setStatusCode(405)->setJSON(['status' => 'error', 'message' => 'Method not allowed']);
        }

        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $userRole = session()->get('role');
        $userId = session()->get('user_id');
        if (!in_array($userRole, ['admin', 'wali_kelas', 'walikelas'])) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Forbidden']);
        }

        // Support JSON and form-encoded
        $payload = $this->request->getJSON(true);
        if (!is_array($payload)) {
            $payload = $this->request->getPost();
        }

        $kelas  = $payload['kelas'] ?? null;
        $mapel  = $payload['mapel'] ?? null;
        $tanggal = $payload['tanggal'] ?? null;
    $deskripsi = $payload['deskripsi'] ?? null; // topik / TP
    $kode = $payload['kode_penilaian'] ?? null;
        $grades = $payload['grades'] ?? null; // array of ['siswa_id' => int, 'nilai' => number]

        if (!$kelas || !$mapel || !$tanggal || !is_array($grades)) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Data tidak lengkap']);
        }

        if (!$this->nilaiModel->canAccessClass($userId, $kelas, $userRole)) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Tidak memiliki akses ke kelas ini']);
        }

        // Pre-validate grades: reject if any >100 or <0
        foreach($grades as $g){
            if(isset($g['nilai']) && $g['nilai'] !== '' && $g['nilai'] !== null){
                $v = (float)$g['nilai'];
                if($v > 100){
                    return $this->response->setStatusCode(400)->setJSON(['status'=>'error','message'=>'Nilai tidak boleh lebih dari 100']);
                }
                if($v < 0){
                    return $this->response->setStatusCode(400)->setJSON(['status'=>'error','message'=>'Nilai tidak boleh kurang dari 0']);
                }
            }
        }

        $db = \Config\Database::connect();
        $this->syncNilaiIdSequenceIfPostgres($db);
        $hasKodeCol = false; try { $hasKodeCol = $db->fieldExists('kode_penilaian','nilai'); } catch(\Throwable $e){}
        // Duplicate kode_penilaian guard: if client sends a kode already existing for this kelas+mapel, reject to prevent accidental overwrite
        if($hasKodeCol && $kode){
            $exists = $db->table('nilai')
                ->where('kelas',$kelas)
                ->where('mata_pelajaran',$mapel)
                ->where('jenis_nilai','harian')
                ->where('kode_penilaian',$kode)
                ->where('deleted_at IS NULL')
                ->countAllResults();
            if($exists>0){
                return $this->response->setStatusCode(409)->setJSON(['status'=>'error','message'=>'Kode penilaian sudah dipakai. Silakan pilih nomor lain.']);
            }
        }

        try {
            $db->transStart();
        $inserted = 0;
        if (!$kode && $hasKodeCol) {
            // generate once if column exists
            $kode = $this->nilaiModel->getNextKodeHarian($kelas, $mapel);
        }
        foreach ($grades as $g) {
            if (!isset($g['siswa_id']) || $g['siswa_id'] === '' || $g['nilai'] === '' || $g['nilai'] === null) {
                continue;
            }
            $val = (float)$g['nilai'];
            if ($val < 0 || $val > 100) { // extra guard (should already have errored)
                continue;
            }
            $data = [
                'siswa_id' => (int)$g['siswa_id'],
                'mata_pelajaran' => $mapel,
                'jenis_nilai' => 'harian',
                'nilai' => $val,
                'tp_materi' => $deskripsi,
                'tanggal' => $tanggal,
                'kelas' => $kelas,
                'created_by' => $userId,
                'updated_by' => $userId,
            ];
            if($hasKodeCol && $kode){ $data['kode_penilaian'] = $kode; }
            $this->nilaiModel->insert($data);
            $inserted++;
        }
            $db->transComplete();
        } catch (\Throwable $e) {
            try { $db->transRollback(); } catch (\Throwable $e2) {}
            log_message('error', 'storeBulkHarian error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan nilai harian']);
        }

        if ($db->transStatus() === false) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan nilai']);
        }

        // Invalidate caches for this kelas+mapel
        $this->nilaiModel->invalidateHarianCaches($kelas,$mapel);
        return $this->response->setJSON([
            'status' => 'ok',
            'message' => 'Nilai berhasil disimpan',
            'inserted' => $inserted,
            'kode_penilaian' => $kode
        ]);
    }

    /**
     * Update bulk nilai harian for selected PH (by date+tp). If record doesn't exist for a student, create it.
     */
    public function updateBulkHarian()
    {
        if (strtoupper($this->request->getMethod()) !== 'POST') {
            return $this->response->setStatusCode(405)->setJSON(['status' => 'error', 'message' => 'Method not allowed']);
        }

        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $userRole = session()->get('role');
        $userId = session()->get('user_id');
        if (!in_array($userRole, ['admin', 'wali_kelas', 'walikelas'])) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Forbidden']);
        }

        $payload = $this->request->getJSON(true);
        if (!is_array($payload)) {
            $payload = $this->request->getPost();
        }

    $kelas  = $payload['kelas'] ?? null;
    $mapel  = $payload['mapel'] ?? null;
    $tanggal = $payload['tanggal'] ?? null; // updating target date
    $deskripsi = $payload['deskripsi'] ?? '';
    $kode = $payload['kode_penilaian'] ?? null; // explicit kode_penilaian from client (e.g. PH-3)
        $grades = $payload['grades'] ?? null; // [{siswa_id, nilai}]

        if (!$kelas || !$mapel || !$tanggal || !is_array($grades)) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Data tidak lengkap']);
        }

        if (!$this->nilaiModel->canAccessClass($userId, $kelas, $userRole)) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Tidak memiliki akses ke kelas ini']);
        }

        // Validate values upfront
        foreach($grades as $g){
            if(isset($g['nilai']) && $g['nilai'] !== '' && $g['nilai'] !== null){
                $v=(float)$g['nilai'];
                if($v>100){
                    return $this->response->setStatusCode(400)->setJSON(['status'=>'error','message'=>'Nilai tidak boleh lebih dari 100']);
                }
                if($v<0){
                    return $this->response->setStatusCode(400)->setJSON(['status'=>'error','message'=>'Nilai tidak boleh kurang dari 0']);
                }
            }
        }

        $db = \Config\Database::connect();
        $this->syncNilaiIdSequenceIfPostgres($db);
        $hasKodeCol = false; try { $hasKodeCol = $db->fieldExists('kode_penilaian','nilai'); } catch(\Throwable $e){}
        $updated = 0;
        try {
            $db->transStart();
            foreach ($grades as $g) {
            if (!isset($g['siswa_id'])) continue;
            $sid = (int)$g['siswa_id'];
            $val = $g['nilai'];
            if ($val === '' || $val === null) continue;
            $val = (float)$val;
            if ($val < 0 || $val > 100) continue;

            // Find existing row matching this PH. Priority: kode_penilaian if provided, else date.
            $builder = $this->nilaiModel->where('deleted_at IS NULL', null, false)
                ->where('kelas', $kelas)
                ->where('siswa_id', $sid)
                ->where('mata_pelajaran', $mapel)
                ->where('jenis_nilai', 'harian');
            if($hasKodeCol && $kode){
                $builder->where('kode_penilaian', $kode);
            } else {
                $builder->where('DATE(tanggal)', $tanggal);
            }
            $row = $builder->first();

        if ($row) {
                $updateData = [
                    'nilai' => $val,
                    'tanggal' => $tanggal,
                    'tp_materi' => $deskripsi,
                    'updated_by' => $userId,
                ];
                if($hasKodeCol && $kode){ $updateData['kode_penilaian'] = $kode ?: ($row['kode_penilaian'] ?? null); }
                $this->nilaiModel->update($row['id'], $updateData);
                $updated++;
            } else {
                $insertData = [
                    'siswa_id' => $sid,
                    'mata_pelajaran' => $mapel,
                    'jenis_nilai' => 'harian',
                    'nilai' => $val,
                    'tp_materi' => $deskripsi,
                    'tanggal' => $tanggal,
                    'kelas' => $kelas,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ];
                if($hasKodeCol){ $insertData['kode_penilaian'] = $kode ?: $this->nilaiModel->getNextKodeHarian($kelas, $mapel); }
                $this->nilaiModel->insert($insertData);
                $updated++;
            }
            }
            $db->transComplete();
        } catch (\Throwable $e) {
            try { $db->transRollback(); } catch (\Throwable $e2) {}
            log_message('error', 'updateBulkHarian error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan perubahan']);
        }

        if ($db->transStatus() === false) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan perubahan']);
        }

    $this->nilaiModel->invalidateHarianCaches($kelas,$mapel);
    return $this->response->setJSON(['status' => 'ok', 'updated' => $updated]);
    }

    /**
     * Dapatkan kode penilaian harian berikutnya (AJAX)
     */
    public function nextKodeHarian()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }
        $kelas = $this->request->getGet('kelas');
        $mapel = $this->request->getGet('mapel');
        if (!$kelas || !$mapel) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Parameter kelas & mapel wajib']);
        }
        $kode = $this->nilaiModel->getNextKodeHarian($kelas, $mapel);
        return $this->response->setJSON(['status' => 'ok', 'kode' => $kode]);
    }

    /**
     * Daftar nomor kode penilaian yang sudah terpakai (untuk disable dropdown).
     * GET params: kelas, mapel, jenis (harian|pts|pas), prefix (PH|PTS|PAS)
     */
    public function usedKodeHarian()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }
        $kelas = $this->request->getGet('kelas');
        $mapel = $this->request->getGet('mapel');
        $jenis = $this->request->getGet('jenis') ?: 'harian';
        $prefix = $this->request->getGet('prefix') ?: ($jenis === 'harian' ? 'PH' : strtoupper($jenis));
        if (!$kelas || !$mapel) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Parameter kelas & mapel wajib']);
        }
    $used = $this->nilaiModel->getCachedUsedKodeNumbers($kelas, $mapel, $jenis, $prefix, 60);
        return $this->response->setJSON(['status' => 'ok', 'used' => $used]);
    }

    /**
     * Hapus satu penilaian harian (seluruh nilai siswa untuk satu kode / tanggal).
     * Expect POST JSON: kelas,mapel,kode(optional),tanggal(optional)
     */
    public function deleteHarianAssessment()
    {
        // Endpoint disederhanakan: wajib kirim kelas, mapel, dan kode_penilaian (atau kode) -> hapus seluruh baris sesuai.
        if (strtoupper($this->request->getMethod()) !== 'POST') {
            return $this->response->setStatusCode(405)->setJSON(['status' => 'error', 'message' => 'Method not allowed']);
        }
        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }
        $userRole = session()->get('role');
        $userId = session()->get('user_id');
        if (!in_array($userRole, ['admin','wali_kelas','walikelas'])) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Forbidden']);
        }
        $payload = $this->request->getJSON(true); if(!is_array($payload)) $payload = $this->request->getPost();
        $kelas = $payload['kelas'] ?? null;
        $mapel = $payload['mapel'] ?? null;
        $kode  = $payload['kode_penilaian'] ?? ($payload['kode'] ?? null); // accept both keys
        if(!$kelas || !$mapel || !$kode){
            return $this->response->setStatusCode(400)->setJSON(['status'=>'error','message'=>'Parameter kurang (kelas,mapel,kode_penilaian)']);
        }
        if(!$this->nilaiModel->canAccessClass($userId, $kelas, $userRole)){
            return $this->response->setStatusCode(403)->setJSON(['status'=>'error','message'=>'Tidak punya akses']);
        }
        $db = \Config\Database::connect();
        if(!$db->fieldExists('kode_penilaian','nilai')){
            return $this->response->setStatusCode(400)->setJSON(['status'=>'error','message'=>'Kolom kode_penilaian belum tersedia']);
        }
        // Hitung dulu
        $count = $db->table('nilai')
            ->where('kelas',$kelas)
            ->where('mata_pelajaran',$mapel)
            ->where('jenis_nilai','harian')
            ->where('kode_penilaian',$kode)
            ->where('deleted_at IS NULL')
            ->countAllResults();
        if($count === 0){
            return $this->response->setJSON(['status'=>'ok','deleted'=>0,'message'=>'Tidak ada data']);
        }
        // Bulk soft delete (lebih cepat dari loop)
    $db->table('nilai')
            ->where('kelas',$kelas)
            ->where('mata_pelajaran',$mapel)
            ->where('jenis_nilai','harian')
            ->where('kode_penilaian',$kode)
            ->where('deleted_at IS NULL')
            ->update(['deleted_at'=>date('Y-m-d H:i:s')]);
    $this->nilaiModel->invalidateHarianCaches($kelas,$mapel);
    return $this->response->setJSON(['status'=>'ok','deleted'=>$count]);
    }

    /**
     * Show PTS input page
     */
    public function pts()
    {
        return $this->renderExamPage('pts');
    }

    /**
     * Show PAS input page
     */
    public function pas()
    {
        return $this->renderExamPage('pas');
    }

    /**
     * Shared renderer for exam pages (PTS/PAS)
     */
    private function renderExamPage(string $examType)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userRole = session()->get('role');
        $userId = session()->get('user_id');
        if (!in_array($userRole, ['admin', 'wali_kelas', 'walikelas'])) {
            return redirect()->to('/admin')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        // Resolve walikelas' class
        $userKelas = null;
        if ($userRole === 'wali_kelas' || $userRole === 'walikelas') {
            $db = \Config\Database::connect();
            $res = $db->query("SELECT w.kelas FROM users u JOIN walikelas w ON u.walikelas_id = w.id WHERE u.id = ?", [$userId])->getRow();
            if ($res) { $userKelas = $res->kelas; }
        }

        // Admin can choose class; walikelas fixed
        $availableClasses = [];
        if ($userRole === 'admin') {
            $availableClasses = $this->tbSiswaModel->select('kelas')->distinct()->orderBy('kelas','ASC')->findAll();
        } elseif ($userKelas) {
            $availableClasses = [['kelas' => $userKelas]];
        }

        $selectedKelas = $this->request->getVar('kelas') ?? $userKelas;
        $selectedMapel = $this->request->getVar('mapel') ?? '';

        // Subject list in required order
        $orderedMapel = $this->nilaiModel->getOrderedMapelList();

        // Students in selected class
        $students = [];
        if ($selectedKelas) {
            $students = $this->tbSiswaModel->where('kelas', $selectedKelas)
                ->where('deleted_at IS NULL', null, false)
                ->orderBy('nama', 'ASC')->findAll();
        }

        $data = [
            'title' => strtoupper($examType) . ' - Input Nilai',
            'userRole' => $userRole,
            'userKelas' => $userKelas,
            'availableClasses' => $availableClasses,
            'students' => $students,
            'selectedKelas' => $selectedKelas,
            'selectedMapel' => $selectedMapel,
            'orderedMapel' => $orderedMapel,
            'examType' => $examType,
        ];

        return view('admin/nilai/pts_pas', $data);
    }

    /**
     * Store bulk PTS/PAS
     */
    public function storeBulkExam()
    {
        if (strtoupper($this->request->getMethod()) !== 'POST') {
            return $this->response->setStatusCode(405)->setJSON(['status' => 'error', 'message' => 'Method not allowed']);
        }
        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }
        $userRole = session()->get('role');
        $userId = session()->get('user_id');
        if (!in_array($userRole, ['admin', 'wali_kelas', 'walikelas'])) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Forbidden']);
        }

        $payload = $this->request->getJSON(true);
        if (!is_array($payload)) { $payload = $this->request->getPost(); }

        $kelas  = $payload['kelas'] ?? null;
        $mapel  = $payload['mapel'] ?? null;
        $tanggal = $payload['tanggal'] ?? date('Y-m-d');
        $jenis = strtolower($payload['jenis'] ?? ''); // 'pts' or 'pas'
        $grades = $payload['grades'] ?? null; // [{siswa_id, nilai}]

        if (!$kelas || !$mapel || !in_array($jenis, ['pts','pas']) || !is_array($grades)) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Data tidak lengkap']);
        }
        if (!$this->nilaiModel->canAccessClass($userId, $kelas, $userRole)) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Tidak memiliki akses ke kelas ini']);
        }

        $db = \Config\Database::connect();
        $db->transStart();
        $upserted = 0;
        foreach ($grades as $g) {
            if (!isset($g['siswa_id']) || $g['nilai'] === '' || $g['nilai'] === null) { continue; }
            $sid = (int)$g['siswa_id'];
            $val = (float)$g['nilai'];
            if ($val < 0 || $val > 100) { continue; }

            // Upsert by unique key (siswa_id, kelas, mapel, jenis) on the same semester
            $row = $this->nilaiModel->where('deleted_at IS NULL', null, false)
                ->where('kelas', $kelas)
                ->where('siswa_id', $sid)
                ->where('mata_pelajaran', $mapel)
                ->where('jenis_nilai', $jenis)
                ->orderBy('id','DESC')
                ->first();

            if ($row) {
                $this->nilaiModel->update($row['id'], [
                    'nilai' => $val,
                    'tanggal' => $tanggal,
                    'updated_by' => $userId,
                ]);
            } else {
                $this->nilaiModel->insert([
                    'siswa_id' => $sid,
                    'mata_pelajaran' => $mapel,
                    'jenis_nilai' => $jenis,
                    'nilai' => $val,
                    'tp_materi' => null,
                    'tanggal' => $tanggal,
                    'kelas' => $kelas,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
            }
            $upserted++;
        }
        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan nilai']);
        }
        return $this->response->setJSON(['status' => 'ok', 'saved' => $upserted]);
    }

    /**
     * Test export method tanpa auth untuk debugging
     */
    /**
     * Export nilai ke Excel (XLSX format)
     */
    public function exportExcel()
    {
        // Debug mode - check what's happening
        log_message('info', 'Export Excel method called');
        
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            log_message('info', 'User not logged in, redirecting to login');
            return redirect()->to('/login');
        }

        $userRole = session()->get('role');
        $userId = session()->get('user_id');

        if (!in_array($userRole, ['admin', 'wali_kelas', 'walikelas'])) {
            log_message('info', 'User role not authorized: ' . $userRole);
            return redirect()->to('/admin')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        // Get parameters
        $selectedKelas = $this->request->getGet('kelas');
        $selectedMapel = $this->request->getGet('mapel');

        log_message('info', 'Export parameters - Kelas: ' . $selectedKelas . ', Mapel: ' . $selectedMapel);

        if (!$selectedKelas || !$selectedMapel) {
            log_message('info', 'Missing parameters');
            return redirect()->back()->with('error', 'Parameter kelas dan mata pelajaran wajib diisi');
        }

        // Check access to class
        if (!$this->nilaiModel->canAccessClass($userId, $selectedKelas, $userRole)) {
            log_message('info', 'User cannot access class: ' . $selectedKelas);
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke kelas tersebut');
        }

        try {
            // Get data
            $students = $this->tbSiswaModel->where('kelas', $selectedKelas)
                ->where('deleted_at IS NULL', null, false)
                ->orderBy('nama', 'ASC')
                ->findAll();

            if (empty($students)) {
                log_message('info', 'No students found for class: ' . $selectedKelas);
                return redirect()->back()->with('error', 'Tidak ada siswa di kelas tersebut');
            }

            log_message('info', 'Found ' . count($students) . ' students');

            // Get harian matrix
            $matrix = $this->nilaiModel->getNilaiHarianMatrix($selectedKelas, $selectedMapel);
            $headers = $matrix['headers'] ?? [];
            $values = $matrix['values'] ?? [];

            log_message('info', 'Harian matrix has ' . count($headers) . ' headers');

            // Limit PH columns to max 10
            $phActualCount = min(count($headers), 10);
            $phHeaderCount = max($phActualCount, 4); // minimum 4 columns

            // Get PTS & PAS values
            $examRows = $this->nilaiModel->where('deleted_at IS NULL', null, false)
                ->where('kelas', $selectedKelas)
                ->where('mata_pelajaran', $selectedMapel)
                ->whereIn('jenis_nilai', ['pts', 'pas'])
                ->findAll();

            $ptsMap = [];
            $pasMap = [];
            foreach ($examRows as $er) {
                if ($er['jenis_nilai'] === 'pts') {
                    $ptsMap[$er['siswa_id']] = (float)$er['nilai'];
                }
                if ($er['jenis_nilai'] === 'pas') {
                    $pasMap[$er['siswa_id']] = (float)$er['nilai'];
                }
            }

            log_message('info', 'Found ' . count($ptsMap) . ' PTS and ' . count($pasMap) . ' PAS values');

            // Prepare report data
            $reportRows = [];
            $noUrut = 1;
            foreach ($students as $stu) {
                $sid = $stu['id'];
                $phScores = [];
                $sum = 0;
                $count = 0;

                for ($i = 1; $i <= $phHeaderCount; $i++) {
                    if ($i <= $phActualCount) {
                        $val = $values[$sid][$i] ?? null;
                        if ($val !== null && $val !== '') {
                            $phScores[$i] = $val;
                            $sum += (float)$val;
                            $count++;
                        } else {
                            $phScores[$i] = '';
                        }
                    } else {
                        $phScores[$i] = '';
                    }
                }

                $avgPH = $count > 0 ? round($sum / $count, 2) : '';
                $pts = $ptsMap[$sid] ?? '';
                $pas = $pasMap[$sid] ?? '';
                $nilaiRapot = ($avgPH !== '' && $pts !== '' && $pas !== '') ? 
                    round(($avgPH + $pts + $pas) / 3, 2) : '';

                $reportRows[] = [
                    'no' => $noUrut++,
                    'nisn' => $stu['nisn'] ?? '',
                    'nama' => $stu['nama'] ?? '',
                    'ph' => $phScores,
                    'avg_ph' => $avgPH,
                    'pts' => $pts,
                    'pas' => $pas,
                    'rapot' => $nilaiRapot,
                ];
            }

            log_message('info', 'Prepared ' . count($reportRows) . ' report rows');

            // Create Excel file
            $spreadsheet = new Spreadsheet();
            // Set default font to Roboto
            try { $spreadsheet->getDefaultStyle()->getFont()->setName('Roboto')->setSize(11); } catch(\Throwable $e) {}
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Rekap Nilai');

            // Set title header
            $sheet->mergeCells('A1:K1');
            $sheet->setCellValue('A1', 'REKAP NILAI SISWA');
            $sheet->mergeCells('A2:K2');
            $sheet->setCellValue('A2', 'KELAS: ' . $selectedKelas . ' | MATA PELAJARAN: ' . $selectedMapel);
            $sheet->mergeCells('A3:K3');
            $sheet->setCellValue('A3', 'TAHUN AJARAN ' . date('Y') . '/' . (date('Y') + 1));

            // Style for title
            $titleStyle = [
                'font' => [
                    'bold' => true,
                    'size' => 14,
                    'color' => ['rgb' => '000000']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ];
            $sheet->getStyle('A1:K3')->applyFromArray($titleStyle);

            // Header row (starting from row 5)
            $headerRow = 5;
            $sheet->setCellValue('A' . $headerRow, 'No');
            $sheet->setCellValue('B' . $headerRow, 'NISN');
            $sheet->setCellValue('C' . $headerRow, 'Nama Siswa');

            // PH columns
            $col = 4;
            for ($i = 1; $i <= $phHeaderCount; $i++) {
                $colLetter = chr(ord('A') + $col - 1);
                $sheet->setCellValue($colLetter . $headerRow, 'PH ' . $i);
                $col++;
            }

            // Additional columns
            $avgCol = chr(ord('A') + $col - 1);
            $sheet->setCellValue($avgCol . $headerRow, 'Rata-rata PH');
            $col++;

            $ptsCol = chr(ord('A') + $col - 1);
            $sheet->setCellValue($ptsCol . $headerRow, 'PTS');
            $col++;

            $pasCol = chr(ord('A') + $col - 1);
            $sheet->setCellValue($pasCol . $headerRow, 'PAS');
            $col++;

            $rapotCol = chr(ord('A') + $col - 1);
            $sheet->setCellValue($rapotCol . $headerRow, 'Nilai Rapot');

            // Header style
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ];

            $lastCol = chr(ord('A') + $col - 1);
            $sheet->getStyle('A' . $headerRow . ':' . $lastCol . $headerRow)->applyFromArray($headerStyle);

            // Data rows
            $dataStartRow = $headerRow + 1;
            $row = $dataStartRow;

            foreach ($reportRows as $data) {
                $sheet->setCellValue('A' . $row, $data['no']);
                $sheet->setCellValue('B' . $row, $data['nisn']);
                $sheet->setCellValue('C' . $row, $data['nama']);

                // PH values
                $col = 4;
                for ($i = 1; $i <= $phHeaderCount; $i++) {
                    $colLetter = chr(ord('A') + $col - 1);
                    $value = $data['ph'][$i] ?? '';
                    if (is_numeric($value)) {
                        $sheet->setCellValue($colLetter . $row, (float)$value);
                    } else {
                        $sheet->setCellValue($colLetter . $row, $value);
                    }
                    $col++;
                }

                // Additional values
                $avgCol = chr(ord('A') + $col - 1);
                if (is_numeric($data['avg_ph'])) {
                    $sheet->setCellValue($avgCol . $row, (float)$data['avg_ph']);
                } else {
                    $sheet->setCellValue($avgCol . $row, $data['avg_ph']);
                }
                $col++;

                $ptsCol = chr(ord('A') + $col - 1);
                if (is_numeric($data['pts'])) {
                    $sheet->setCellValue($ptsCol . $row, (float)$data['pts']);
                } else {
                    $sheet->setCellValue($ptsCol . $row, $data['pts']);
                }
                $col++;

                $pasCol = chr(ord('A') + $col - 1);
                if (is_numeric($data['pas'])) {
                    $sheet->setCellValue($pasCol . $row, (float)$data['pas']);
                } else {
                    $sheet->setCellValue($pasCol . $row, $data['pas']);
                }
                $col++;

                $rapotCol = chr(ord('A') + $col - 1);
                if (is_numeric($data['rapot'])) {
                    $sheet->setCellValue($rapotCol . $row, (float)$data['rapot']);
                } else {
                    $sheet->setCellValue($rapotCol . $row, $data['rapot']);
                }

                $row++;
            }

            $lastDataRow = $row - 1;

            // Data style
            $dataStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ];

            $sheet->getStyle('A' . $dataStartRow . ':' . $lastCol . $lastDataRow)->applyFromArray($dataStyle);

            // Override alignment for Nama Siswa column (C) -> left align values
            if ($lastDataRow >= $dataStartRow) {
                $sheet->getStyle('C' . $dataStartRow . ':C' . $lastDataRow)
                      ->getAlignment()
                      ->setHorizontal(Alignment::HORIZONTAL_LEFT);
            }

            // Color coding for grades
            for ($r = $dataStartRow; $r <= $lastDataRow; $r++) {
                // Color based on final grade (rapot)
                $rapotValue = $sheet->getCell($rapotCol . $r)->getValue();
                if (is_numeric($rapotValue)) {
                    $grade = (float)$rapotValue;
                    if ($grade >= 90) {
                        $color = 'C6EFCE'; // Green for excellent
                    } elseif ($grade >= 80) {
                        $color = 'FFEB9C'; // Yellow for good
                    } elseif ($grade >= 70) {
                        $color = 'FFC7CE'; // Light red for needs improvement
                    } else {
                        $color = 'FF9999'; // Red for poor
                    }

                    $sheet->getStyle('A' . $r . ':' . $lastCol . $r)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB($color);
                }
            }

            // Auto-size columns
            foreach (range('A', $lastCol) as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            // Set row heights: header taller (21.5pt), data rows 15pt
            $sheet->getRowDimension($headerRow)->setRowHeight(21.5); // header
            $dataRowHeightPt = 15;
            for ($r = $dataStartRow; $r <= $lastDataRow; $r++) {
                $sheet->getRowDimension($r)->setRowHeight($dataRowHeightPt);
            }

            // Repeat header row on each printed page
            $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($headerRow, $headerRow);

            // Signature section (Kepala Sekolah & Wali Kelas) similar to web print
            $sigStart = $lastDataRow + 3; // spacing after table
            // Fetch profil sekolah
            $profilSekolah = [];
            try { $pm = new \App\Models\ProfilSekolahModel(); $profilSekolah = $pm->getProfilSekolah(); } catch(\Throwable $e) {}
            $kepalaNama = $profilSekolah['nama_kepala_sekolah'] ?? '';
            $kepalaNip  = $profilSekolah['nip_kepala_sekolah'] ?? '';

            // Fetch wali kelas data for selected class
            $waliNama = '';$waliNip='';
            try {
                $db = \Config\Database::connect();
                // Try guru table
                if($db->tableExists('guru')){
                    $row = $db->table('guru')->select('nama,nip,tugas_mengajar')->where('tugas_mengajar',$selectedKelas)->orderBy('id','ASC')->get()->getRowArray();
                    if($row){ $waliNama = $row['nama'] ?? ''; $waliNip = $row['nip'] ?? ''; }
                }
                if(!$waliNama && $db->tableExists('tb_guru')){
                    $jabatanTarget = 'Wali Kelas ' . $selectedKelas;
                    $row2 = $db->table('tb_guru')->select('nama_lengkap,nip,jabatan')->where('jabatan',$jabatanTarget)->orderBy('id','ASC')->get()->getRowArray();
                    if(!$row2){
                        $row2 = $db->table('tb_guru')->select('nama_lengkap,nip,jabatan')->like('jabatan','Wali Kelas')->like('jabatan',$selectedKelas)->orderBy('id','ASC')->get()->getRowArray();
                    }
                    if($row2){ $waliNama = $row2['nama_lengkap'] ?? ''; $waliNip = $row2['nip'] ?? ''; }
                }
                if(!$waliNama && $db->tableExists('walikelas')){
                    $legacy = $db->table('walikelas')->where('kelas',$selectedKelas)->get()->getRowArray();
                    if($legacy){ $waliNama = $legacy['nama'] ?? ($legacy['walikelas_nama'] ?? ''); $waliNip = $legacy['nip'] ?? ''; }
                }
            } catch(\Throwable $e) {}

            // Determine merge areas (split sheet width roughly in half)
            $totalCols = ord($lastCol) - ord('A') + 1;
            $mid = (int) floor($totalCols / 2);
            if($mid < 3) { $mid = 3; } // minimum width safeguard
            $leftEnd = chr(ord('A') + $mid - 1);
            $rightStart = chr(ord('A') + $mid);
            if(ord($rightStart) > ord($lastCol)) { $rightStart = $leftEnd; }

            // Date row above wali kelas column (right side only)
            $dateRow = $sigStart; // first row used for date on right side
            // Localized Indonesian date with location prefix
            $bulanIndo = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            $d = (int)date('j'); $m=(int)date('n'); $y=date('Y');
            $today = 'Jakarta, ' . $d . ' ' . ($bulanIndo[$m] ?? date('F')) . ' ' . $y;
            $sheet->mergeCells($rightStart.$dateRow.':'.$lastCol.$dateRow);
            $sheet->setCellValue($rightStart.$dateRow, $today);
            $sheet->getStyle($rightStart.$dateRow.':'.$lastCol.$dateRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getRowDimension($dateRow)->setRowHeight(18);

            // Labels row (one row below date)
            $labelRow = $dateRow + 1;
            $sheet->mergeCells('A'.$labelRow.':'.$leftEnd.$labelRow);
            $sheet->mergeCells($rightStart.$labelRow.':'.$lastCol.$labelRow);
            $sheet->setCellValue('A'.$labelRow, 'Kepala Sekolah');
            $sheet->setCellValue($rightStart.$labelRow, 'Walikelas ' . $selectedKelas);
            $sheet->getStyle('A'.$labelRow.':'.$lastCol.$labelRow)->getFont()->setBold(true);
            $sheet->getStyle('A'.$labelRow.':'.$lastCol.$labelRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Name row after space for signature (5 rows space from labelRow)
            $nameRow = $labelRow + 5;
            $nipRow  = $nameRow + 1;
            $sheet->mergeCells('A'.$nameRow.':'.$leftEnd.$nameRow);
            $sheet->mergeCells($rightStart.$nameRow.':'.$lastCol.$nameRow);
            $sheet->mergeCells('A'.$nipRow.':'.$leftEnd.$nipRow);
            $sheet->mergeCells($rightStart.$nipRow.':'.$lastCol.$nipRow);
            $sheet->setCellValue('A'.$nameRow, $kepalaNama ? $kepalaNama : '( _________________________ )');
            $sheet->setCellValue($rightStart.$nameRow, $waliNama ? $waliNama : '( _________________________ )');
            $sheet->setCellValue('A'.$nipRow, 'NIP: '. ($kepalaNip ?: '_____________'));
            $sheet->setCellValue($rightStart.$nipRow, 'NIP: '. ($waliNip ?: '_____________'));
            $sheet->getStyle('A'.$nameRow.':'.$lastCol.$nipRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A'.$nameRow.':'.$lastCol.$nameRow)->getFont()->setBold(true);

            // Adjust row heights for signature block (optional slightly larger name rows)
            $sheet->getRowDimension($nameRow)->setRowHeight(18);
            $sheet->getRowDimension($nipRow)->setRowHeight(15);

            // Page setup
            $sheet->getPageSetup()
                ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
                ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
                ->setFitToWidth(1)
                ->setFitToHeight(0);

            // Generate filename
            $filename = 'Rekap_Nilai_' . str_replace(' ', '_', $selectedKelas) . '_' . str_replace(' ', '_', $selectedMapel) . '_' . date('Y-m-d') . '.xlsx';

            log_message('info', 'Excel file created, preparing download: ' . $filename);

            // Output file
            $writer = new Xlsx($spreadsheet);
            
            ob_start();
            $writer->save('php://output');
            $data = ob_get_clean();

            log_message('info', 'Excel data generated, size: ' . strlen($data) . ' bytes');

            return $this->response
                ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
                ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->setBody($data);

        } catch (\Exception $e) {
            log_message('error', 'Export Excel error: ' . $e->getMessage());
            log_message('error', 'Trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat export: ' . $e->getMessage());
        }
    }
}
