<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\GuruModel;
use App\Models\SiswaModel;
use App\Models\AbsensiModel;

class Dashboard extends BaseController
{
    protected $cache;

    public function __construct()
    {
        $this->cache = \Config\Services::cache();
    }

    public function index()
    {
        $session = session();

        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        // Initialize data with session info
        $data = [
            'currentUser' => [
                'id' => $session->get('user_id'),
                'username' => $session->get('username'),
                'nama' => $session->get('nama'),
                'role' => $session->get('role')
            ],
            'dbError' => false,
            'errorMessage' => '',
            'lastUpdated' => date('Y-m-d H:i:s')
        ];

        // Check database connection first
        $db = \Config\Database::connect();
        try {
            // Test connection with a simple query
            $db->query("SELECT 1");

            // Connection OK, get dashboard data
            $data = array_merge($data, $this->getDashboardData($session->get('user_id')));
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            // Database connection failed
            log_message('error', 'Dashboard database connection error: ' . $e->getMessage());

            $data = array_merge($data, $this->getFallbackData());
            $data['dbError'] = true;
            $data['errorMessage'] = 'Tidak dapat mengambil data dari database. Menampilkan data default.';
        } catch (\Exception $e) {
            // Other errors (not connection related) - log but don't show warning
            log_message('error', 'Dashboard error: ' . $e->getMessage());

            // Try to get data anyway, if it fails use fallback without showing error
            try {
                $data = array_merge($data, $this->getDashboardData($session->get('user_id')));
            } catch (\Exception $e2) {
                $data = array_merge($data, $this->getFallbackData());
            }
        }

        return view('admin/dashboard', $data);
    }

    /**
     * Get dashboard data from database with caching
     */
    private function getDashboardData($userId)
    {
        // Try to get cached data first
        $cacheKey = "dashboard_data_{$userId}";
        $cachedData = $this->cache->get($cacheKey);

        if ($cachedData !== null) {
            $cachedData['fromCache'] = true;
            return $cachedData;
        }

        // Get fresh data from database
        $userModel = new UserModel();
        $guruModel = new GuruModel();
        $siswaModel = new SiswaModel();
        $absensiModel = new AbsensiModel();

        // Get current user info with walikelas data if applicable
        $currentUser = $userModel->getUserWithWalikelas($userId);

        // Check if user is walikelas
        $isWalikelas = $currentUser && $currentUser['role'] === 'walikelas';

        if ($isWalikelas && !empty($currentUser['walikelas_id'])) {
            // Get walikelas-specific data
            $data = $this->getWalikelasData($currentUser, $siswaModel, $absensiModel);
        } else {
            // Get admin data (original functionality)
            $data = $this->getAdminData($currentUser, $guruModel, $userModel, $siswaModel, $absensiModel);
        }

        // Cache the data for 5 minutes
        $this->cache->save($cacheKey, $data, 300);

        return $data;
    }

    /**
     * Get walikelas-specific dashboard data
     */
    private function getWalikelasData($currentUser, $siswaModel, $absensiModel)
    {
        // Get walikelas class info
        $walikelasModel = new \App\Models\WalikelasModel();
        $walikelasInfo = $walikelasModel->find($currentUser['walikelas_id']);

        if (!$walikelasInfo) {
            // If walikelas info not found, return empty data (not an error, just no data)
            // Don't throw exception, just return minimal valid data
            return [
                'currentUser' => $currentUser,
                'walikelasInfo' => [
                    'kelas' => 'Tidak ada kelas',
                    'nama' => $currentUser['nama']
                ],
                'totalSiswa' => 0,
                'siswaLaki' => 0,
                'siswaPerempuan' => 0,
                'attendanceData' => [
                    'dates' => [],
                    'percentages' => [],
                    'totals' => []
                ],
                'dailyProgress' => [
                    'attendance_present' => 0,
                    'attendance_marked' => 0,
                    'habit_logged' => 0,
                    'total_students' => 0,
                    'attendance_percentage' => 0,
                    'habit_percentage' => 0
                ],
                'isWalikelas' => true,
                'fromCache' => false
            ];
        }

        // Get students in walikelas's class
        $classStudents = $siswaModel->where('kelas', $walikelasInfo['kelas'])->findAll();
        $totalSiswa = count($classStudents);

        // Count by gender
        $siswaLaki = count(array_filter($classStudents, fn($s) => $s['jk'] === 'L'));
        $siswaPerempuan = count(array_filter($classStudents, fn($s) => $s['jk'] === 'P'));

        // Get attendance data for the class (last 7 days)
        $attendanceData = $this->getWalikelasAttendanceData($absensiModel, $walikelasInfo['kelas']);

        // Get daily progress (today attendance & habit logs)
        $dailyProgress = $this->getWalikelasDailyProgress($walikelasInfo['kelas'], $totalSiswa);

        return [
            'currentUser' => $currentUser,
            'walikelasInfo' => $walikelasInfo,
            'totalSiswa' => $totalSiswa,
            'siswaLaki' => $siswaLaki,
            'siswaPerempuan' => $siswaPerempuan,
            'attendanceData' => $attendanceData,
            'dailyProgress' => $dailyProgress,
            'isWalikelas' => true,
            'fromCache' => false
        ];
    }

    /**
     * Get daily progress for walikelas class (attendance present count & habit log completion)
     */
    private function getWalikelasDailyProgress(string $kelas, int $totalSiswa): array
    {
        $today = date('Y-m-d');
        $db = \Config\Database::connect();

        $result = [
            'attendance_present' => 0,
            'attendance_marked' => 0,
            'habit_logged' => 0,
            'total_students' => $totalSiswa,
            'attendance_percentage' => 0,
            'habit_percentage' => 0,
        ];

        try {
            // Count attendance (present) and any attendance marked today.
            // New structure: absensi.siswa_id now references tb_siswa.id (based on AbsensiModel usage).
            // Use tb_siswa directly; keep legacy mapping fallback if needed.
            $attendanceRow = null;
            try {
                $attendanceSqlNew = "
                    SELECT
                        SUM(CASE WHEN a.status = 'hadir' THEN 1 ELSE 0 END) AS hadir_count,
                        COUNT(DISTINCT a.siswa_id) AS marked_count
                    FROM absensi a
                    JOIN tb_siswa t ON t.id = a.siswa_id
                    WHERE REPLACE(UPPER(t.kelas),' ','') = REPLACE(UPPER(?),' ','') AND DATE(a.tanggal) = ?
                ";
                $attendanceRow = $db->query($attendanceSqlNew, [$kelas, $today])->getRowArray();
            } catch (\Throwable $e) {
            }
            // Fallback to legacy join path if new query returned null or zero rows but there might be legacy data
            if (!$attendanceRow || ($attendanceRow['hadir_count'] ?? 0) + ($attendanceRow['marked_count'] ?? 0) === 0) {
                try {
                    $attendanceSqlLegacy = "
                        SELECT
                            SUM(CASE WHEN a.status = 'hadir' THEN 1 ELSE 0 END) AS hadir_count,
                            COUNT(DISTINCT a.siswa_id) AS marked_count
                        FROM absensi a
                        JOIN siswa ls ON ls.id = a.siswa_id
                        JOIN tb_siswa t ON t.nisn = ls.nisn
                        WHERE REPLACE(UPPER(t.kelas),' ','') = REPLACE(UPPER(?),' ','') AND DATE(a.tanggal) = ?
                    ";
                    $attendanceRowLegacy = $db->query($attendanceSqlLegacy, [$kelas, $today])->getRowArray();
                    if ($attendanceRowLegacy) {
                        $attendanceRow = $attendanceRowLegacy;
                    }
                } catch (\Throwable $e2) {
                }
            }

            if ($attendanceRow) {
                $result['attendance_present'] = (int)$attendanceRow['hadir_count'];
                $result['attendance_marked'] = (int)$attendanceRow['marked_count'];
            }

            // Count students with at least one habit log today
            $habitRow = null;
            // Primary (legacy mapping) query
            try {
                $habitSql = "
                    SELECT COUNT(DISTINCT hl.student_id) AS habit_logged
                    FROM habit_logs hl
                    JOIN siswa ls ON ls.id = hl.student_id
                    JOIN tb_siswa t ON t.nisn = ls.nisn
                    WHERE REPLACE(UPPER(t.kelas),' ','') = REPLACE(UPPER(?),' ','') AND hl.log_date = ?
                ";
                $habitRow = $db->query($habitSql, [$kelas, $today])->getRowArray();
            } catch (\Throwable $e) {
            }
            // If schema already migrated (habit_logs.student_id references tb_siswa.id) attempt direct join
            if ((!$habitRow || ($habitRow['habit_logged'] ?? 0) === 0)) {
                try {
                    $habitSqlNew = "
                        SELECT COUNT(DISTINCT hl.student_id) AS habit_logged
                        FROM habit_logs hl
                        JOIN tb_siswa t ON t.id = hl.student_id
                        WHERE REPLACE(UPPER(t.kelas),' ','') = REPLACE(UPPER(?),' ','') AND hl.log_date = ?
                    ";
                    $habitRowNew = $db->query($habitSqlNew, [$kelas, $today])->getRowArray();
                    if ($habitRowNew && ($habitRowNew['habit_logged'] ?? 0) > 0) {
                        $habitRow = $habitRowNew;
                    }
                } catch (\Throwable $e3) {
                }
            }
            if ($habitRow) {
                $result['habit_logged'] = (int)$habitRow['habit_logged'];
            }

            // Count students with at least one habit log today
            if ($totalSiswa > 0) {
                $result['attendance_percentage'] = round(($result['attendance_present'] / $totalSiswa) * 100, 1);
                $result['habit_percentage'] = round(($result['habit_logged'] / $totalSiswa) * 100, 1);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error getting walikelas daily progress: ' . $e->getMessage());
        }

        return $result;
    }

    /**
     * Get admin dashboard data (original functionality)
     */
    private function getAdminData($currentUser, $guruModel, $userModel, $siswaModel, $absensiModel)
    {
        // Dashboard statistics
        $totalGuru = $guruModel->countAllResults();
        $totalWalikelas = $userModel->where('role', 'walikelas')->where('is_active', 1)->countAllResults();
        $totalUsers = $userModel->where('is_active', 1)->countAllResults();
        $siswaStats = $siswaModel->getStatistics();

        // Get attendance data for current week
        $attendanceData = $this->getWeeklyAttendanceData($absensiModel);

        // Get recent activities
        $recentGuru = $guruModel->orderBy('created_at', 'DESC')->findAll(5);
        $recentSiswa = $siswaModel->orderBy('created_at', 'DESC')->findAll(5);

        return [
            'currentUser' => $currentUser ?: [
                'id' => session('user_id'),
                'username' => session('username'),
                'nama' => session('nama'),
                'role' => session('role')
            ],
            'totalGuru' => $totalGuru,
            'totalWalikelas' => $totalWalikelas,
            'totalUsers' => $totalUsers,
            'totalSiswa' => $siswaStats['total'],
            'siswaLaki' => $siswaStats['laki_laki'],
            'siswaPerempuan' => $siswaStats['perempuan'],
            'recentGuru' => $recentGuru,
            'recentSiswa' => $recentSiswa,
            'attendanceData' => $attendanceData,
            'isWalikelas' => false,
            'fromCache' => false
        ];
    }

    /**
     * Get attendance data specific to walikelas class for last 7 days
     */
    private function getWalikelasAttendanceData($absensiModel, $kelas)
    {
        try {
            $db = \Config\Database::connect();

            // Get last 7 days attendance for the specific class
            $startDate = date('Y-m-d', strtotime('-6 days'));
            $endDate = date('Y-m-d');

            $query = "
                SELECT 
                    DATE(a.tanggal) as tanggal,
                    COUNT(CASE WHEN a.status = 'hadir' THEN 1 END) as hadir,
                    COUNT(CASE WHEN a.status = 'sakit' THEN 1 END) as sakit,
                    COUNT(CASE WHEN a.status = 'izin' THEN 1 END) as izin,
                    COUNT(CASE WHEN a.status = 'alpha' THEN 1 END) as alpha,
                    COUNT(a.id) as total
                FROM absensi a
                JOIN siswa s ON a.siswa_id = s.id
                WHERE s.kelas = ? AND a.tanggal >= ? AND a.tanggal <= ?
                GROUP BY DATE(a.tanggal)
                ORDER BY a.tanggal ASC
            ";

            $weeklyData = $db->query($query, [$kelas, $startDate, $endDate])->getResultArray();

            // Format data for last 7 days
            $chartData = [];
            $dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

            for ($i = 0; $i < 7; $i++) {
                $currentDate = date('Y-m-d', strtotime($startDate . " +{$i} days"));
                $dayName = $dayNames[date('w', strtotime($currentDate))];

                // Find data for this day
                $dayData = null;
                foreach ($weeklyData as $data) {
                    if ($data['tanggal'] === $currentDate) {
                        $dayData = $data;
                        break;
                    }
                }

                $chartData[] = [
                    'day' => $dayName,
                    'date' => $currentDate,
                    'hadir' => $dayData ? (int)$dayData['hadir'] : 0,
                    'sakit' => $dayData ? (int)$dayData['sakit'] : 0,
                    'izin' => $dayData ? (int)$dayData['izin'] : 0,
                    'alpha' => $dayData ? (int)$dayData['alpha'] : 0,
                    'total' => $dayData ? (int)$dayData['total'] : 0,
                    'percentage' => $dayData && $dayData['total'] > 0 ?
                        round(($dayData['hadir'] / $dayData['total']) * 100) : 0
                ];
            }

            // Get total attendance summary for the class
            $totalQuery = "
                SELECT 
                    COUNT(CASE WHEN a.status = 'hadir' THEN 1 END) as total_hadir,
                    COUNT(CASE WHEN a.status = 'sakit' THEN 1 END) as total_sakit,
                    COUNT(CASE WHEN a.status = 'izin' THEN 1 END) as total_izin,
                    COUNT(CASE WHEN a.status = 'alpha' THEN 1 END) as total_alpha,
                    COUNT(a.id) as total_records
                FROM absensi a
                JOIN siswa s ON a.siswa_id = s.id
                WHERE s.kelas = ? AND a.tanggal >= ? AND a.tanggal <= ?
            ";

            $totalData = $db->query($totalQuery, [$kelas, $startDate, $endDate])->getRowArray();

            // -------------------------------------------------------------
            // Weekly absence breakdown (Mon-Fri) listing names per status
            // Structure requested: Senin (Alpa: ..., Sakit: ..., Izin: ...)
            // -------------------------------------------------------------
            $weekStart = date('Y-m-d', strtotime('monday this week'));
            $weekEnd = date('Y-m-d', strtotime('friday this week'));

            // Ensure we don't go into the future (e.g. viewing mid-week)
            $today = date('Y-m-d');
            if ($weekEnd > $today) {
                $weekEnd = $today; // clamp to today if before Friday
            }

            $absenceSql = "
                SELECT DATE(a.tanggal) AS tanggal, a.status, s.nama
                FROM absensi a
                JOIN tb_siswa s ON s.id = a.siswa_id
                WHERE s.kelas = ?
                  AND a.tanggal BETWEEN ? AND ?
                  AND a.status IN ('sakit','izin','alpha')
                ORDER BY a.tanggal ASC, s.nama ASC
            ";
            $absenceRows = $db->query($absenceSql, [$kelas, $weekStart, $weekEnd])->getResultArray();

            // Prepare map date => categories
            $dayNameMap = [
                'Mon' => 'Senin',
                'Tue' => 'Selasa',
                'Wed' => 'Rabu',
                'Thu' => 'Kamis',
                'Fri' => 'Jumat',
            ];

            $weekAbsence = [];
            // Initialize Monday-Friday entries even if empty
            $period = new \DatePeriod(
                new \DateTime($weekStart),
                new \DateInterval('P1D'),
                (new \DateTime($weekEnd))->modify('+1 day')
            );
            foreach ($period as $dt) {
                $d = $dt->format('Y-m-d');
                $dowShort = $dt->format('D');
                if (!isset($dayNameMap[$dowShort])) continue; // skip weekend if weekEnd < Friday
                $weekAbsence[$d] = [
                    'date' => $d,
                    'day' => $dayNameMap[$dowShort],
                    'alpa' => [], // label uses 'alpa' (DB field is 'alpha')
                    'sakit' => [],
                    'izin' => []
                ];
            }

            foreach ($absenceRows as $row) {
                $d = $row['tanggal'];
                if (!isset($weekAbsence[$d])) continue; // outside Mon-Fri or clamped range
                $status = $row['status'];
                if ($status === 'alpha') {
                    $weekAbsence[$d]['alpa'][] = $row['nama'];
                } elseif ($status === 'sakit') {
                    $weekAbsence[$d]['sakit'][] = $row['nama'];
                } elseif ($status === 'izin') {
                    $weekAbsence[$d]['izin'][] = $row['nama'];
                }
            }

            // Re-index to simple array ordered by date
            $weeklyAbsenceList = array_values($weekAbsence);

            return [
                'weekly' => $chartData,
                'summary' => $totalData ?: [
                    'total_hadir' => 0,
                    'total_sakit' => 0,
                    'total_izin' => 0,
                    'total_alpha' => 0,
                    'total_records' => 0
                ],
                'weekly_absence' => $weeklyAbsenceList,
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error getting walikelas attendance data: ' . $e->getMessage());
            return $this->getFallbackWalikelasAttendanceData();
        }
    }

    /**
     * Get fallback data for walikelas when database is not available
     */
    private function getFallbackWalikelasData($currentUser)
    {
        return [
            'currentUser' => $currentUser,
            'walikelasInfo' => [
                'kelas' => 'Kelas tidak ditemukan',
                'nama' => $currentUser['nama']
            ],
            'totalSiswa' => 0,
            'siswaLaki' => 0,
            'siswaPerempuan' => 0,
            'attendanceData' => $this->getFallbackWalikelasAttendanceData(),
            'isWalikelas' => true,
            'fromCache' => false
        ];
    }

    /**
     * Get fallback attendance data for walikelas
     */
    private function getFallbackWalikelasAttendanceData()
    {
        $dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        $chartData = [];

        for ($i = 0; $i < 7; $i++) {
            $currentDate = date('Y-m-d', strtotime('-6 days +' . $i . ' days'));
            $dayName = $dayNames[date('w', strtotime($currentDate))];

            $chartData[] = [
                'day' => $dayName,
                'date' => $currentDate,
                'hadir' => rand(15, 25),
                'sakit' => rand(1, 3),
                'izin' => rand(0, 2),
                'alpha' => rand(0, 1),
                'total' => rand(20, 30),
                'percentage' => rand(80, 95)
            ];
        }

        return [
            'weekly' => $chartData,
            'summary' => [
                'total_hadir' => 140,
                'total_sakit' => 12,
                'total_izin' => 8,
                'total_alpha' => 5,
                'total_records' => 165
            ],
            'period' => [
                'start_date' => date('Y-m-d', strtotime('-6 days')),
                'end_date' => date('Y-m-d')
            ]
        ];
    }

    /**
     * Get weekly attendance data for charts
     */
    private function getWeeklyAttendanceData($absensiModel)
    {
        try {
            $startDate = date('Y-m-d', strtotime('monday this week'));
            $endDate = date('Y-m-d', strtotime('sunday this week'));

            $db = \Config\Database::connect();

            // Get daily attendance summary for current week
            $query = "
                SELECT 
                    DATE(a.tanggal) as tanggal,
                    COUNT(CASE WHEN a.status = 'hadir' THEN 1 END) as hadir,
                    COUNT(CASE WHEN a.status = 'sakit' THEN 1 END) as sakit,
                    COUNT(CASE WHEN a.status = 'izin' THEN 1 END) as izin,
                    COUNT(CASE WHEN a.status = 'alpha' THEN 1 END) as alpha,
                    COUNT(a.id) as total
                FROM absensi a
                WHERE a.tanggal >= ? AND a.tanggal <= ?
                GROUP BY DATE(a.tanggal)
                ORDER BY a.tanggal ASC
            ";

            $weeklyData = $db->query($query, [$startDate, $endDate])->getResultArray();

            // Format data for charts
            $chartData = [];
            $weekDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

            for ($i = 0; $i < 7; $i++) {
                $currentDate = date('Y-m-d', strtotime($startDate . " +{$i} days"));
                $dayName = $weekDays[$i];

                // Find data for this day
                $dayData = null;
                foreach ($weeklyData as $data) {
                    if ($data['tanggal'] === $currentDate) {
                        $dayData = $data;
                        break;
                    }
                }

                $chartData[] = [
                    'day' => substr($dayName, 0, 1), // M, T, W, T, F, S, S
                    'date' => $currentDate,
                    'hadir' => $dayData ? (int)$dayData['hadir'] : 0,
                    'sakit' => $dayData ? (int)$dayData['sakit'] : 0,
                    'izin' => $dayData ? (int)$dayData['izin'] : 0,
                    'alpha' => $dayData ? (int)$dayData['alpha'] : 0,
                    'total' => $dayData ? (int)$dayData['total'] : 0,
                    'percentage' => $dayData && $dayData['total'] > 0 ?
                        round(($dayData['hadir'] / $dayData['total']) * 100) : 0
                ];
            }

            // Get monthly summary
            $monthStart = date('Y-m-01');
            $monthEnd = date('Y-m-t');

            $monthlyQuery = "
                SELECT 
                    COUNT(CASE WHEN a.status = 'hadir' THEN 1 END) as total_hadir,
                    COUNT(CASE WHEN a.status = 'sakit' THEN 1 END) as total_sakit,
                    COUNT(CASE WHEN a.status = 'izin' THEN 1 END) as total_izin,
                    COUNT(CASE WHEN a.status = 'alpha' THEN 1 END) as total_alpha,
                    COUNT(a.id) as total_records
                FROM absensi a
                WHERE a.tanggal >= ? AND a.tanggal <= ?
            ";

            $monthlyData = $db->query($monthlyQuery, [$monthStart, $monthEnd])->getRowArray();

            return [
                'weekly' => $chartData,
                'monthly' => $monthlyData ?: [
                    'total_hadir' => 0,
                    'total_sakit' => 0,
                    'total_izin' => 0,
                    'total_alpha' => 0,
                    'total_records' => 0
                ],
                'period' => [
                    'week_start' => $startDate,
                    'week_end' => $endDate,
                    'month_start' => $monthStart,
                    'month_end' => $monthEnd
                ]
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error getting attendance data: ' . $e->getMessage());
            return $this->getFallbackAttendanceData();
        }
    }

    /**
     * Get fallback data when database is not available
     */
    private function getFallbackData()
    {
        return [
            'totalGuru' => 6,
            'totalWalikelas' => 6,
            'totalUsers' => 4,
            'totalSiswa' => 6,
            'siswaLaki' => 3,
            'siswaPerempuan' => 3,
            'recentGuru' => [],
            'recentSiswa' => [],
            'attendanceData' => $this->getFallbackAttendanceData(),
            'fromCache' => false
        ];
    }

    /**
     * Get fallback attendance data
     */
    private function getFallbackAttendanceData()
    {
        $weekDays = ['M', 'T', 'W', 'T', 'F', 'S', 'S'];
        $chartData = [];

        foreach ($weekDays as $day) {
            $chartData[] = [
                'day' => $day,
                'date' => date('Y-m-d'),
                'hadir' => rand(80, 95),
                'sakit' => rand(2, 8),
                'izin' => rand(1, 5),
                'alpha' => rand(0, 3),
                'total' => 100,
                'percentage' => rand(85, 95)
            ];
        }

        return [
            'weekly' => $chartData,
            'monthly' => [
                'total_hadir' => 850,
                'total_sakit' => 45,
                'total_izin' => 25,
                'total_alpha' => 15,
                'total_records' => 935
            ],
            'period' => [
                'week_start' => date('Y-m-d', strtotime('monday this week')),
                'week_end' => date('Y-m-d', strtotime('sunday this week')),
                'month_start' => date('Y-m-01'),
                'month_end' => date('Y-m-t')
            ]
        ];
    }

    /**
     * AJAX endpoint to refresh dashboard data
     */
    public function refresh()
    {
        $session = session();

        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        try {
            // Clear cache
            $cacheKey = "dashboard_data_{$session->get('user_id')}";
            $this->cache->delete($cacheKey);

            // Get fresh data
            $data = $this->getDashboardData($session->get('user_id'));
            $data['lastUpdated'] = date('Y-m-d H:i:s');

            return $this->response->setJSON([
                'success' => true,
                'data' => $data,
                'message' => 'Data berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Dashboard refresh error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'error' => 'Gagal memperbarui data',
                'message' => $e->getMessage()
            ]);
        }
    }
}
