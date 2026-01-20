<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;
use App\Models\HabitModel;
use App\Models\HabitLogModel;
use App\Models\SiswaModel;
use App\Models\TbSiswaModel;

class HabitController extends BaseController
{
    protected $habitModel;
    protected $habitLogModel;
    protected $siswaModel;
    protected $tbSiswaModel;

    public function __construct()
    {
        helper(['form','date']);
        $this->habitModel = new HabitModel();
        $this->habitLogModel = new HabitLogModel();
    $this->siswaModel = new SiswaModel();
    $this->tbSiswaModel = new TbSiswaModel();
    }

    /**
     * Resolve current logged-in student's ID from session.
     * Priority: session('student_id') -> find by session('username') as NISN -> fallback NIS.
     * When found by username, sync session('student_id').
     */
    protected function resolveStudentId(): ?int
    {
        $sid = session('student_id');
        if ($sid) return (int)$sid;
        $username = session('username');
        if (!$username) return null;
    // Ambil dari tb_siswa (kanonik) terlebih dahulu untuk nama
    $tb = $this->siswaModel->where('nisn', $username)->first();
    $stu = $tb; // alias agar logika lama tetap bekerja dengan field nama
    // Jika belum ada di tb_siswa, coba fallback ke tabel siswa (legacy)
    if (!$stu) {
        $stu = db_connect()->table('siswa')->where('nisn', $username)->orWhere('nis', $username)->get()->getFirstRow('array');
    }
        if (!$stu) {
            // legacy fallback nis pada tb_siswa (jika ada kolom nipd dsb)
            $stu = $this->siswaModel->where('nipd', $username)->first();
        }
        if ($stu) {
            // student_id yang dibutuhkan habit_logs berasal dari tabel siswa (legacy). Cari jika belum.
            if (!session()->has('student_id')) {
                try {
                    $legacy = db_connect()->table('siswa')->select('id,nama')
                        ->where('nisn', $username)->orWhere('nis', $username)
                        ->get()->getFirstRow('array');
                    if ($legacy) {
                        session()->set('student_id', (int)$legacy['id']);
                        if (empty($stu['nama']) && !empty($legacy['nama'])) {
                            $stu['nama'] = $legacy['nama'];
                        }
                    }
                } catch (\Throwable $e) {}
            }
            if (!session()->has('student_id') && isset($stu['id'])) {
                session()->set('student_id', (int)$stu['id']);
            }
            if (!empty($stu['nama']) && session('student_name') !== $stu['nama']) {
                session()->set('student_name', $stu['nama']);
            }
            return (int)(session('student_id'));
        }
        return null;
    }

    public function index()
    {
        // Landing dashboard
        $studentId = $this->resolveStudentId();
        if (!$studentId) {
            return redirect()->to('/login')->with('error', 'Sesi siswa tidak ditemukan');
        }

        // Preload habits (names) for front-end dashboard status grid
        $habits = $this->habitModel->orderBy('id')->findAll();
        return view('siswa/dashboard', [
            'habits' => $habits,
        ]);
    }

    /**
     * Original habit input page moved from index() so /siswa/habits still works
     */
    public function habits()
    {
        $date = $this->request->getGet('date') ?: date('Y-m-d');
        $habits = $this->habitModel->orderBy('id')->findAll();
        $studentId = $this->resolveStudentId();
        if (!$studentId) {
            return redirect()->to('/login')->with('error', 'Sesi siswa tidak ditemukan');
        }

        $summary = $this->habitLogModel->getDailySummary($studentId, $date);
        $summaryByHabit = [];
        foreach ($summary as $row) {
            $summaryByHabit[$row['habit_id']] = $row;
        }

        // Detect religion (Islam) for special UI in "Beribadah"
        $isIslam = false;
        // $studentId adalah id di tabel siswa (legacy). Untuk data kanonik (tb_siswa) gunakan nisn dari session username.
        $student = null;
        $username = session('username');
        if ($username) {
            $student = $this->siswaModel->where('nisn', $username)->first();
        }
        if (!$student) {
            // fallback: coba mapping tb_siswa.id tersimpan di session
            $tbId = session('student_tb_id');
            if ($tbId) {
                $student = $this->siswaModel->find($tbId);
            }
        }
        if (!$student) {
            // fallback terakhir: cari melalui legacy siswa table untuk set nama (tidak dipakai untuk data lain)
            try {
                $legacy = db_connect()->table('siswa')->select('nama, nisn')
                    ->where('id', $studentId)->get()->getFirstRow('array');
                if ($legacy) {
                    $student = ['nama' => $legacy['nama'], 'nisn' => $legacy['nisn']];
                }
            } catch (\Throwable $e) {}
        }
        if ($student && !empty($student['nama']) && session('student_name') !== $student['nama']) {
            session()->set('student_name', $student['nama']);
        }
        $agama = $student['agama'] ?? null;
        if (!$agama && !empty($student['nisn'] ?? null)) {
            $fromTb = $this->tbSiswaModel->where('nisn', $student['nisn'])->first();
            $agama = $fromTb['agama'] ?? null;
        }
        if ($agama && stripos($agama, 'islam') !== false) {
            $isIslam = true;
        }

        // TEMP: force Islam UI during development (remove later)
        $isIslam = true;

        return view('siswa/habits/index', [
            'habits' => $habits,
            'date' => $date,
            'summary' => $summaryByHabit,
            'isIslam' => $isIslam,
        ]);
    }

    public function store()
    {
        $input = $this->request->getJSON(true) ?? $this->request->getPost();
        $date = $input['date'] ?? null;
        if (! $date || ! \CodeIgniter\I18n\Time::createFromFormat('Y-m-d', $date)) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Tanggal tidak valid']);
        }
        $studentId = $this->resolveStudentId();
        if (!$studentId) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Sesi siswa tidak ditemukan']);
        }

        $payload = $input['habits'] ?? null; // array: habit_id => [bool, number, time]
        if (!is_array($payload)) $payload = [];

        $createdBy = session('user_id');
        $createdBy = $createdBy ?: null;

        foreach ($payload as $habitId => $vals) {
            $data = [
                'student_id' => (int)$studentId,
                'habit_id'   => (int)$habitId,
                'log_date'   => $date,
                'value_bool' => isset($vals['bool']) ? (int)!!$vals['bool'] : null,
                'value_time' => $vals['time'] ?? null,
                'value_number' => isset($vals['number']) && $vals['number'] !== '' ? (float)$vals['number'] : null,
                'notes'      => null,
                'value_json' => null,
                'created_by' => $createdBy,
            ];

            // Handle complex data (prayers, activities, etc.)
            $complexData = [];
            
            // Handle prayer times for worship habit
            if (isset($vals['prayers']) && is_array($vals['prayers'])) {
                $complexData['prayers'] = $vals['prayers'];
            }
            
            // Handle activities list (exercise, learning, social, etc.)
            if (isset($vals['activities']) && is_array($vals['activities'])) {
                $complexData['activities'] = $vals['activities'];
            }
            
            // Handle food items for healthy food habit
            if (isset($vals['items']) && is_array($vals['items'])) {
                $complexData['items'] = $vals['items'];
            }
            
            // Store complex data as JSON
            if (!empty($complexData)) {
                $data['value_json'] = json_encode($complexData, JSON_UNESCAPED_UNICODE);
            }

            // Handle simple notes
            if (isset($vals['notes']) && is_string($vals['notes']) && $vals['notes'] !== '') {
                $data['notes'] = $vals['notes'];
            }

            $this->habitLogModel->upsertLog($data);
        }

    return $this->response->setJSON(['message' => 'Tersimpan']);
    }

    public function today()
    {
        $date = date('Y-m-d');
    $studentId = (int) ($this->resolveStudentId() ?? 0);
        $summary = $this->habitLogModel->getDailySummary($studentId, $date);
        return $this->response->setJSON($summary);
    }

    public function summary()
    {
        $date = $this->request->getGet('date');
        if (! $date || ! \CodeIgniter\I18n\Time::createFromFormat('Y-m-d', $date)) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Tanggal tidak valid']);
        }
        $studentId = (int) ($this->resolveStudentId() ?? 0);
        if (!$studentId) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Sesi siswa tidak ditemukan']);
        }
        $rows = $this->habitLogModel->getDailySummary($studentId, $date);
        
        // Parse JSON data if exists
        foreach ($rows as &$row) {
            if (!empty($row['value_json'])) {
                $row['complex_data'] = json_decode($row['value_json'], true);
            }
        }
        
        return $this->response->setJSON(['data' => $rows]);
    }

    /**
     * Calculate current streak for student
     */
    private function calculateCurrentStreak($studentId)
    {
        $db = db_connect();
        $sql = "
            WITH RECURSIVE date_series AS (
                SELECT DATE(NOW()) as log_date
                UNION ALL
                SELECT DATE_SUB(log_date, INTERVAL 1 DAY)
                FROM date_series
                WHERE log_date > DATE_SUB(NOW(), INTERVAL 30 DAY)
            ),
            daily_completion AS (
                SELECT 
                    ds.log_date,
                    COUNT(DISTINCT hl.habit_id) as completed_habits,
                    (SELECT COUNT(*) FROM habits WHERE status = 'active') as total_habits
                FROM date_series ds
                LEFT JOIN habit_logs hl ON hl.log_date = ds.log_date 
                    AND hl.student_id = ? 
                    AND hl.value_bool = 1
                GROUP BY ds.log_date
                ORDER BY ds.log_date DESC
            )
            SELECT 
                log_date,
                completed_habits,
                total_habits,
                CASE WHEN completed_habits >= total_habits * 0.8 THEN 1 ELSE 0 END as is_good_day
            FROM daily_completion
        ";
        
        $query = $db->query($sql, [$studentId]);
        $results = $query->getResultArray();
        
        $streak = 0;
        foreach ($results as $row) {
            if ($row['is_good_day'] == 1) {
                $streak++;
            } else {
                break;
            }
        }
        
        return $streak;
    }

    /**
     * Calculate weekly progress for student
     */
    private function calculateWeeklyProgress($studentId)
    {
        $db = db_connect();
        $sql = "
            SELECT 
                COUNT(DISTINCT hl.log_date) as days_with_activity,
                COUNT(DISTINCT CASE WHEN hl.value_bool = 1 THEN CONCAT(hl.log_date, '_', hl.habit_id) END) as completed_habits,
                (SELECT COUNT(*) FROM habits WHERE status = 'active') * 7 as total_possible_habits
            FROM habit_logs hl
            WHERE hl.student_id = ? 
                AND hl.log_date >= DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)
                AND hl.log_date <= CURDATE()
        ";
        
        $query = $db->query($sql, [$studentId]);
        $result = $query->getRowArray();
        
        return [
            'completed' => $result['completed_habits'] ?? 0,
            'total' => $result['total_possible_habits'] ?? 35
        ];
    }

    /**
     * Get student statistics
     */
    public function getStats()
    {
        $studentId = (int) ($this->resolveStudentId() ?? 0);
        if (!$studentId) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Sesi siswa tidak ditemukan']);
        }

        $currentStreak = $this->calculateCurrentStreak($studentId);
        $weeklyProgress = $this->calculateWeeklyProgress($studentId);
        
        // Best streak (simplified - could be enhanced with proper tracking)
        $bestStreak = max($currentStreak, 21); // Assuming 21 as historical best
        
        return $this->response->setJSON([
            'current_streak' => $currentStreak,
            'best_streak' => $bestStreak,
            'weekly_progress' => $weeklyProgress
        ]);
    }

    /**
     * Show monthly report page
     */
    public function monthlyReport()
    {
        $role = session('role');
        $studentId = $this->resolveStudentId();
        if (!$studentId && !in_array($role, ['admin','walikelas'])) {
            return redirect()->to('/login')->with('error', 'Sesi siswa tidak ditemukan');
        }
        $students = null;
        if (in_array($role, ['admin','walikelas'])) {
            // Sekarang daftar siswa bersumber dari tb_siswa sebagai sumber utama
            try {
                $students = $this->tbSiswaModel
                    ->select('id, nama, nisn, kelas')
                    ->where('deleted_at', null)
                    ->orderBy('nama','ASC')
                    ->findAll();
            } catch (\Throwable $e) {
                log_message('error', 'Gagal mengambil daftar tb_siswa: '.$e->getMessage());
            }
        } else {
            // Siswa view: pastikan session student_name di-set dari tb_siswa menggunakan nisn
            $username = session('username');
            if ($username) {
                $tb = $this->siswaModel->where('nisn', $username)->first();
                if ($tb && !empty($tb['nama']) && session('student_name') !== $tb['nama']) {
                    session()->set('student_name', $tb['nama']);
                }
            }
        }
        return view('siswa/habits/monthly_report', [ 'students' => $students ]);
    }

    /**
     * Get monthly data for student
     */
    public function monthlyData()
    {
        $month = $this->request->getGet('month');
        if (!$month || !preg_match('/^\d{4}-\d{2}$/', $month)) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Format bulan tidak valid (YYYY-MM)']);
        }
        // Baru: gunakan tb_siswa sebagai sumber utama id; fallback ke legacy bila perlu
        $role = session('role');
    $rawStudentParam = $this->request->getGet('student_id');
    $requestedTbId = abs((int)($rawStudentParam ?? 0)); // tb_siswa id dari dropdown (normalisasi: buang tanda negatif jika ada)
        if ($rawStudentParam !== null) {
            log_message('debug', 'monthlyData request student_id param: '.$rawStudentParam);
        }
        $tbRow = null;
        if (in_array($role, ['admin','walikelas'])) {
            if ($requestedTbId === 0) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'data' => [],
                    'month' => $month,
                    'debug' => [ 'info' => 'Menunggu pemilihan siswa tb_siswa' ]
                ]);
            }
            $tbRow = $this->tbSiswaModel->select('id,nisn,nama')->find($requestedTbId);
            if (!$tbRow) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Siswa (tb_siswa) tidak ditemukan'
                ]);
            }
        } else {
            // siswa biasa: cari tb_siswa dari nisn session
            $username = session('username');
            if ($username) {
                $tbRow = $this->tbSiswaModel->where('nisn',$username)->first();
            }
            if (!$tbRow) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error', 'message' => 'Data tb_siswa tidak ditemukan untuk akun ini'
                ]);
            }
        }

        $tbId = (int)$tbRow['id'];
        if ($requestedTbId && $requestedTbId !== $tbId) {
            // Mungkin yang dikirim adalah legacy siswa.id (pasca migrasi seharusnya tidak dipakai lagi)
            log_message('warning', 'monthlyData: requested student_id '.$requestedTbId.' tidak cocok tb_siswa id '.$tbId.' (kemungkinan legacy). Memakai tb_siswa id.');
        }
        if (!empty($tbRow['nama']) && session('student_name') !== $tbRow['nama']) {
            session()->set('student_name', $tbRow['nama']);
        }

        $startDate = $month.'-01';
        $endDate = date('Y-m-t', strtotime($startDate));
        $db = db_connect();

        // Kumpulkan kemungkinan ID yang dipakai di habit_logs (tb_siswa.id atau legacy siswa.id)
        $candidateIds = [$tbId];
        $legacyId = null;
        if (!empty($tbRow['nisn'])) {
            $legacyRow = db_connect()->table('siswa')->select('id,nisn')->where('nisn',$tbRow['nisn'])->get()->getFirstRow('array');
            if ($legacyRow) {
                $legacyId = (int)$legacyRow['id'];
                if (!in_array($legacyId, $candidateIds, true)) $candidateIds[] = $legacyId;
            }
        }

        // Bangun query dinamis untuk IN (...)
        $placeholders = implode(',', array_fill(0, count($candidateIds), '?'));
        $sql = "SELECT hl.student_id, hl.log_date, hl.habit_id, hl.value_bool as completed, hl.value_time as time, hl.value_number as duration, hl.notes, h.name as habit_name
                FROM habit_logs hl
                JOIN habits h ON h.id = hl.habit_id
                WHERE hl.student_id IN ($placeholders) AND hl.log_date BETWEEN ? AND ?
                ORDER BY hl.log_date ASC, hl.habit_id ASC";
        $params = array_merge($candidateIds, [$startDate, $endDate]);
        $rawResults = $db->query($sql, $params)->getResultArray();

        // Fallback tambahan: jika tidak ada hasil sama sekali dan kita punya nisn, cari kemungkinan id tb_siswa lain (misal setelah migrasi, row aktif berbeda id dari yang menyimpan log historis atau sebaliknya)
        if (empty($rawResults) && !empty($tbRow['nisn'])) {
            $altSql = "SELECT DISTINCT hl.student_id FROM habit_logs hl\n                        JOIN tb_siswa ts ON ts.id = hl.student_id\n                        WHERE ts.nisn = ? AND hl.log_date BETWEEN ? AND ?";
            $altIds = $db->query($altSql, [$tbRow['nisn'], $startDate, $endDate])->getResultArray();
            $alternativeIds = [];
            foreach ($altIds as $ai) {
                $aid = (int)$ai['student_id'];
                if (!in_array($aid, $candidateIds, true)) $alternativeIds[] = $aid;
            }
            if ($alternativeIds) {
                log_message('warning', 'monthlyData: fallback alternative student_ids ditemukan untuk nisn '.$tbRow['nisn'].': '.implode(',', $alternativeIds));
                $candidateIds = array_merge($candidateIds, $alternativeIds);
                $placeholders2 = implode(',', array_fill(0, count($candidateIds), '?'));
                $sql2 = "SELECT hl.student_id, hl.log_date, hl.habit_id, hl.value_bool as completed, hl.value_time as time, hl.value_number as duration, hl.notes, h.name as habit_name\n                        FROM habit_logs hl\n                        JOIN habits h ON h.id = hl.habit_id\n                        WHERE hl.student_id IN ($placeholders2) AND hl.log_date BETWEEN ? AND ?\n                        ORDER BY hl.log_date ASC, hl.habit_id ASC";
                $params2 = array_merge($candidateIds, [$startDate, $endDate]);
                $rawResults = $db->query($sql2, $params2)->getResultArray();
            }
        }

        // Merge (prioritaskan data dengan tb_siswa.id jika duplikat tanggal+habit)
        $results = [];
        $seen = [];
        foreach ($rawResults as $r) {
            $key = $r['log_date'].'_'.$r['habit_id'];
            if (!isset($seen[$key]) || ($r['student_id'] === $tbId && $seen[$key] !== $tbId)) {
                $results[] = $r;
                $seen[$key] = $r['student_id'];
            }
        }
        $usedId = $tbId;
        $fallbackUsed = ($legacyId !== null && in_array($legacyId, $seen, true) && $legacyId !== $tbId);
        $fallbackInfo = $fallbackUsed ? [ 'legacy_id_used' => $legacyId ] : null;

        $monthlyData = [];
        foreach ($results as $row) {
            $date = $row['log_date'];
            $habitKey = 'habit_'.$row['habit_id'];
            if (!isset($monthlyData[$date])) $monthlyData[$date] = [];
            $notes = '';
            if ($row['notes']) {
                $notesData = json_decode($row['notes'], true);
                if (is_array($notesData)) {
                    if (!empty($notesData['prayers'])) {
                        $prayers = array_keys(array_filter($notesData['prayers']));
                        if ($prayers) $notes .= 'Sholat: '.implode(', ',$prayers).'. ';
                    }
                    if (!empty($notesData['note'])) $notes .= $notesData['note'];
                } else {
                    $notes = $row['notes'];
                }
            }
            $completed = ($row['completed']==1) || $row['time'] || ($row['duration'] && $row['duration']>0);
            $monthlyData[$date][$habitKey] = [
                'completed'=>$completed,
                'time'=>$row['time'],
                'duration'=>$row['duration'],
                'notes'=>trim($notes),
                'habit_name'=>$row['habit_name']
            ];
        }

    return $this->response->setJSON([
            'status'=>'success',
            'data'=>$monthlyData,
            'month'=>$month,
            'debug'=>[
                'query_results_count'=>count($results),
                'tb_siswa_id'=>$tbId,
                'legacy_id'=>$legacyId,
                'candidate_ids'=>$candidateIds,
                'fallback_alternative_used'=> isset($alternativeIds) && !empty($alternativeIds),
                'alternative_ids'=> $alternativeIds ?? [],
                'date_range'=>[$startDate,$endDate],
                'fallback_used'=>$fallbackUsed,
                'fallback_info'=>$fallbackInfo,
        'sample_data'=>array_slice($results,0,3),
        'raw_rows'=>array_slice($rawResults,0,10)
            ]
        ]);
    }
    
    /**
     * Save habit data (for editing/adding from monthly report)
     */
    public function saveHabitData()
    {
        $studentId = $this->resolveStudentId();
        
        if (!$studentId) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => 'error',
                'message' => 'Sesi siswa tidak ditemukan'
            ]);
        }
        
        $json = $this->request->getJSON(true);
        
        // Validate required fields
        if (!isset($json['habit_id']) || !isset($json['log_date'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Data tidak lengkap'
            ]);
        }
        
        $habitId = (int) $json['habit_id'];
        $logDate = $json['log_date'];
        $completed = $json['completed'] ?? false;
        $time = $json['time'] ?? null;
        $duration = $json['duration'] ?? null;
        $notes = $json['notes'] ?? null;
        
        // Validate habit_id
        if ($habitId < 1 || $habitId > 7) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'ID kebiasaan tidak valid'
            ]);
        }
        
        // Validate date format
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $logDate)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Format tanggal tidak valid'
            ]);
        }
        
        try {
            // Prepare data for upsert
            $logData = [
                'student_id' => $studentId,
                'habit_id' => $habitId,
                'log_date' => $logDate,
                'value_bool' => $completed ? 1 : 0,
                'value_time' => $time,
                'value_number' => $duration ? (float) $duration : null,
                'notes' => $notes
            ];
            
            // Use upsertLog method from HabitLogModel
            $result = $this->habitLogModel->upsertLog($logData);
            
            if ($result) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Data kebiasaan berhasil disimpan',
                    'data' => $logData
                ]);
            } else {
                return $this->response->setStatusCode(500)->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal menyimpan data kebiasaan'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error saving habit data: ' . $e->getMessage());
            
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Delete habit data
     */
    public function deleteHabitData()
    {
        $studentId = $this->resolveStudentId();
        
        if (!$studentId) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => 'error',
                'message' => 'Sesi siswa tidak ditemukan'
            ]);
        }
        
        $json = $this->request->getJSON(true);
        
        // Validate required fields
        if (!isset($json['habit_id']) || !isset($json['log_date'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Data tidak lengkap'
            ]);
        }
        
        $habitId = (int) $json['habit_id'];
        $logDate = $json['log_date'];
        
        try {
            $db = db_connect();
            $deleted = $db->table('habit_logs')
                ->where('student_id', $studentId)
                ->where('habit_id', $habitId)
                ->where('log_date', $logDate)
                ->delete();
            
            if ($deleted) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Data kebiasaan berhasil dihapus'
                ]);
            } else {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error deleting habit data: ' . $e->getMessage());
            
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ]);
        }
    }
}
