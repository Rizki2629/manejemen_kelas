<?php

namespace App\Models;

use CodeIgniter\Model;

class AbsensiModel extends Model
{
    protected $table = 'absensi';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'siswa_id',
        'tanggal',
        'status',
        'keterangan',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'siswa_id' => 'required|integer',
        'tanggal' => 'required|valid_date',
        'status' => 'required|in_list[hadir,izin,sakit,alpha]',
        'keterangan' => 'permit_empty|string|max_length[255]',
        'created_by' => 'required|integer'
    ];

    protected $validationMessages = [
        'siswa_id' => [
            'required' => 'ID siswa harus diisi',
            'integer' => 'ID siswa harus berupa angka'
        ],
        'tanggal' => [
            'required' => 'Tanggal harus diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ],
        'status' => [
            'required' => 'Status kehadiran harus diisi',
            'in_list' => 'Status harus salah satu dari: hadir, izin, sakit, alpha'
        ]
    ];

    /**
     * Get attendance by class and date
     */
    public function getAttendanceByClassAndDate($kelasId, $tanggal)
    {
        $db = \Config\Database::connect();

        $query = "
            SELECT a.*, s.nama, s.nipd
            FROM absensi a
            JOIN tb_siswa s ON s.id = a.siswa_id
            WHERE s.kelas = ? AND a.tanggal = ?
            ORDER BY s.nama ASC
        ";

        return $db->query($query, [$kelasId, $tanggal])->getResultArray();
    }

    /**
     * Get students with their attendance status for a specific date and class
     */
    public function getStudentsWithAttendance($kelasId, $tanggal)
    {
        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT 
                s.id as siswa_id,
                s.nipd,
                s.nama,
                s.jk,
                s.nisn,
                s.kelas,
                COALESCE(a.status, 'belum_absen') as status,
                a.keterangan,
                a.id as absensi_id
            FROM tb_siswa s
            LEFT JOIN absensi a ON s.id = a.siswa_id AND a.tanggal = ?
            WHERE s.kelas = ? AND s.deleted_at IS NULL
            ORDER BY s.nama ASC
        ", [$tanggal, $kelasId]);

        return $query->getResultArray();
    }

    /**
     * Save or update attendance
     */
    public function saveAttendance($data)
    {
        log_message('debug', 'AbsensiModel::saveAttendance - Input data: ' . json_encode($data));

        // Validate data first
        if (!$this->validate($data)) {
            $errors = $this->errors();
            log_message('debug', 'AbsensiModel::saveAttendance - Validation failed: ' . json_encode($errors));
            return false;
        }

        try {
            // Check if attendance already exists
            $existing = $this->where('siswa_id', $data['siswa_id'])
                ->where('tanggal', $data['tanggal'])
                ->first();

            log_message('debug', 'AbsensiModel::saveAttendance - Existing record: ' . json_encode($existing));

            if ($existing) {
                // Update existing record
                $updateData = [
                    'status' => $data['status'],
                    'keterangan' => $data['keterangan'] ?? '',
                    'created_by' => $data['created_by']
                ];

                log_message('debug', 'AbsensiModel::saveAttendance - Updating existing record ID: ' . $existing['id']);
                $result = $this->update($existing['id'], $updateData);
                log_message('debug', 'AbsensiModel::saveAttendance - Update result: ' . ($result ? 'success' : 'failed'));

                if (!$result) {
                    $errors = $this->errors();
                    log_message('debug', 'AbsensiModel::saveAttendance - Update errors: ' . json_encode($errors));
                }

                return $result;
            } else {
                // Insert new record
                log_message('debug', 'AbsensiModel::saveAttendance - Inserting new record');
                $result = $this->insert($data);
                log_message('debug', 'AbsensiModel::saveAttendance - Insert result: ' . ($result ? 'success' : 'failed'));

                if (!$result) {
                    $errors = $this->errors();
                    log_message('debug', 'AbsensiModel::saveAttendance - Insert errors: ' . json_encode($errors));
                }

                return $result;
            }
        } catch (\Exception $e) {
            log_message('error', 'AbsensiModel::saveAttendance - Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get attendance recap by date range and optional class filter
     */
    public function getAttendanceRecap($startDate, $endDate, $kelasId = null)
    {
        $db = \Config\Database::connect();

        $query = "
            SELECT 
                a.tanggal,
                s.nipd,
                s.nama,
                s.kelas,
                a.status,
                a.keterangan
            FROM absensi a
            JOIN tb_siswa s ON s.id = a.siswa_id
            WHERE a.tanggal >= ? AND a.tanggal <= ? 
            AND s.deleted_at IS NULL
        ";

        $params = [$startDate, $endDate];

        if ($kelasId) {
            $query .= " AND s.kelas = ?";
            $params[] = $kelasId;
        }

        $query .= " ORDER BY a.tanggal ASC, s.kelas ASC, s.nama ASC";

        return $db->query($query, $params)->getResultArray();
    }

    /**
     * Get attendance summary by class for a specific month
     */
    public function getAttendanceSummary($year, $month, $kelasId = null)
    {
        $startDate = "$year-$month-01";
        $endDate = date('Y-m-t', strtotime($startDate));

        $db = \Config\Database::connect();

        $query = "
            SELECT 
                s.nipd,
                s.nama,
                s.kelas,
                COUNT(CASE WHEN a.status = 'hadir' THEN 1 END) as total_hadir,
                COUNT(CASE WHEN a.status = 'izin' THEN 1 END) as total_izin,
                COUNT(CASE WHEN a.status = 'sakit' THEN 1 END) as total_sakit,
                COUNT(a.id) as total_hari
            FROM absensi a
            JOIN tb_siswa s ON s.id = a.siswa_id
            WHERE a.tanggal >= ? AND a.tanggal <= ?
            AND s.deleted_at IS NULL
        ";

        $params = [$startDate, $endDate];

        if ($kelasId) {
            $query .= " AND s.kelas = ?";
            $params[] = $kelasId;
        }

        $query .= " GROUP BY s.id ORDER BY s.kelas ASC, s.nama ASC";

        return $db->query($query, $params)->getResultArray();
    }

    /**
     * Check if user can access class attendance
     */
    public function canAccessClass($userId, $kelasId, $userRole)
    {
        // Admin can access all classes
        if ($userRole === 'admin') {
            return true;
        }

        // Wali kelas can only access their own class
        if ($userRole === 'wali_kelas' || $userRole === 'walikelas') {
            $db = \Config\Database::connect();
            $query = $db->query("
                SELECT COUNT(*) as count 
                FROM users u 
                JOIN walikelas w ON u.walikelas_id = w.id 
                WHERE u.id = ? AND w.kelas = ?
            ", [$userId, $kelasId]);

            $result = $query->getRowArray();
            return $result['count'] > 0;
        }

        return false;
    }

    /**
     * Get detailed attendance data for recap table format (like Excel format)
     */
    public function getDetailedAttendanceRecap($year, $month, $kelasId = null)
    {
        // Ensure month is zero-padded
        $monthStr = str_pad($month, 2, '0', STR_PAD_LEFT);
        $startDate = "$year-$monthStr-01";
        $endDate = date('Y-m-t', strtotime($startDate));
        $daysInMonth = date('t', strtotime($startDate));

        // Create array of days for the month
        $days = [];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $days[] = $i;
        }

        // Get all students for the class
        $studentBuilder = db_connect()->table('tb_siswa')
            ->select('id, nipd, nama, kelas, nisn')
            ->where('deleted_at IS NULL');

        if ($kelasId) {
            $studentBuilder->where('kelas', $kelasId);
        }

        $students = $studentBuilder->orderBy('nama', 'ASC')->get()->getResultArray();

        // Get all attendance data for the month
        $db = \Config\Database::connect();

        $attendanceQuery = "
            SELECT a.siswa_id, a.tanggal, a.status
            FROM absensi a
            JOIN tb_siswa s ON s.id = a.siswa_id
            WHERE a.tanggal >= ? AND a.tanggal <= ? 
            AND s.deleted_at IS NULL
        ";

        $attendanceParams = [$startDate, $endDate];

        if ($kelasId) {
            $attendanceQuery .= " AND s.kelas = ?";
            $attendanceParams[] = $kelasId;
        }

        $attendanceData = $db->query($attendanceQuery, $attendanceParams)->getResultArray();

        // Organize attendance by student and date
        $attendanceByStudent = [];
        foreach ($attendanceData as $record) {
            $day = (int)date('j', strtotime($record['tanggal']));
            $attendanceByStudent[$record['siswa_id']][$day] = $record['status'];
        }

        // Get holidays and weekends for the month
        $holidayData = $this->getHolidaysForMonth($year, $month);
        $holidays = $holidayData['dates'];
        $holidayDetails = $holidayData['details'];

        // Build final result with daily attendance for each student
        $studentsData = [];
        foreach ($students as $student) {
            $studentData = [
                'siswa_id' => $student['id'],
                'nipd' => $student['nipd'],
                'nama' => $student['nama'],
                'kelas' => $student['kelas'],
                'nisn' => $student['nisn'], // Add NISN from database
                'daily' => [],
                'summary' => [
                    'hadir' => 0,
                    'sakit' => 0,
                    'izin' => 0,
                    'alpha' => 0,
                    'total' => 0
                ],
                'percentage' => 0
            ];

            $totalDaysCount = 0;
            $hadirCount = 0;

            // Fill in daily attendance
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $dayStr = str_pad($day, 2, '0', STR_PAD_LEFT);
                $currentDate = "$year-$monthStr-$dayStr";
                $dayOfWeek = date('w', strtotime($currentDate));

                // Check if holiday or weekend FIRST (before checking future date)
                $isHoliday = in_array($currentDate, $holidays);
                $isWeekend = ($dayOfWeek == 0 || $dayOfWeek == 6); // Sunday = 0, Saturday = 6

                if ($isHoliday || $isWeekend) {
                    // Set appropriate status based on type
                    if ($isHoliday && isset($holidayDetails[$currentDate])) {
                        $status = $holidayDetails[$currentDate]['status'];
                        $studentData['daily'][$dayStr] = $status;
                    } else if ($isWeekend) {
                        $studentData['daily'][$dayStr] = 'weekend';
                    } else {
                        $studentData['daily'][$dayStr] = 'off';
                    }
                    continue;
                }

                // Check if there's actual attendance data for this day
                $hasAttendanceData = isset($attendanceByStudent[$student['id']][$day]);

                // Check if future date (only for non-holidays and no existing data)
                if (strtotime($currentDate) > time() && !$hasAttendanceData) {
                    $studentData['daily'][$dayStr] = null;
                    continue;
                }

                $totalDaysCount++;
                $status = $attendanceByStudent[$student['id']][$day] ?? 'alpha';
                $studentData['daily'][$dayStr] = $status;

                // Count summary
                switch ($status) {
                    case 'hadir':
                        $studentData['summary']['hadir']++;
                        $hadirCount++;
                        break;
                    case 'sakit':
                        $studentData['summary']['sakit']++;
                        break;
                    case 'izin':
                        $studentData['summary']['izin']++;
                        break;
                    default:
                        $studentData['summary']['alpha']++;
                        break;
                }
            }

            $studentData['summary']['total'] = $totalDaysCount;
            $studentData['percentage'] = $totalDaysCount > 0 ? ($hadirCount / $totalDaysCount) * 100 : 0;

            $studentsData[] = $studentData;
        }

        // Calculate effective days (total days minus holidays and weekends)
        $effectiveDays = 0;
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDate = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $dayOfWeek = date('w', strtotime($currentDate));
            $isWeekend = ($dayOfWeek == 0 || $dayOfWeek == 6);
            $isHoliday = in_array($currentDate, $holidays);

            // Count as effective day if not weekend and not holiday
            if (!$isWeekend && !$isHoliday) {
                $effectiveDays++;
            }
        }

        return [
            'students' => $studentsData,
            'year' => (int)$year,
            'month' => (int)$month,
            'kelas' => $kelasId,
            'days' => $days,
            'holidays' => $holidays,
            'holidayDetails' => $holidayDetails,
            'effective_days' => $effectiveDays,
            'total_days' => $daysInMonth
        ];
    }

    /**
     * Get attendance percentage per class for a specific month
     */
    public function getClassAttendancePercentages($year, $month, $kelasId = null)
    {
        $monthStr = str_pad($month, 2, '0', STR_PAD_LEFT);
        $startDate = "$year-$monthStr-01";
        $endDate = date('Y-m-t', strtotime($startDate));

        $effectiveDays = $this->getEffectiveDaysForMonth($year, $month);

        // Get total students per class
        $classBuilder = db_connect()->table('tb_siswa')
            ->select('kelas, COUNT(*) as total_students')
            ->where('deleted_at IS NULL');

        if ($kelasId) {
            $classBuilder->where('kelas', $kelasId);
        }

        $classes = $classBuilder->groupBy('kelas')->orderBy('kelas', 'ASC')->get()->getResultArray();

        // Get total hadir per class for the month
        $db = \Config\Database::connect();
        $hadirQuery = "
            SELECT s.kelas, COUNT(*) as total_hadir
            FROM absensi a
            JOIN tb_siswa s ON s.id = a.siswa_id
            WHERE a.tanggal >= ? AND a.tanggal <= ?
            AND a.status = 'hadir'
            AND s.deleted_at IS NULL
        ";

        $params = [$startDate, $endDate];

        if ($kelasId) {
            $hadirQuery .= " AND s.kelas = ?";
            $params[] = $kelasId;
        }

        $hadirQuery .= " GROUP BY s.kelas";
        $hadirRows = $db->query($hadirQuery, $params)->getResultArray();

        $hadirByClass = [];
        foreach ($hadirRows as $row) {
            $hadirByClass[$row['kelas']] = (int)$row['total_hadir'];
        }

        $result = [];
        foreach ($classes as $classRow) {
            $kelas = $classRow['kelas'];
            $totalStudents = (int)$classRow['total_students'];
            $totalHadir = $hadirByClass[$kelas] ?? 0;
            $totalPossible = $totalStudents * $effectiveDays;
            $percentage = $totalPossible > 0 ? ($totalHadir / $totalPossible) * 100 : 0;

            $result[] = [
                'kelas' => $kelas,
                'total_students' => $totalStudents,
                'total_hadir' => $totalHadir,
                'effective_days' => $effectiveDays,
                'percentage' => $percentage
            ];
        }

        return $result;
    }

    /**
     * Get holidays for a specific month from academic calendar
     */
    private function getHolidaysForMonth($year, $month)
    {
        // Ensure month is zero-padded
        $monthStr = str_pad($month, 2, '0', STR_PAD_LEFT);
        $startDate = "$year-$monthStr-01";
        $endDate = date('Y-m-t', strtotime($startDate));

        $db = \Config\Database::connect();

        // Get all holidays from academic calendar for the month
        $query = "
            SELECT tanggal_mulai, tanggal_selesai, status, keterangan
            FROM kalender_akademik 
            WHERE (
                (tanggal_mulai >= ? AND tanggal_mulai <= ?) OR
                (tanggal_selesai >= ? AND tanggal_selesai <= ?) OR
                (tanggal_mulai <= ? AND tanggal_selesai >= ?)
            )
            AND status IN ('libur_nasional', 'libur_sekolah', 'off')
            ORDER BY tanggal_mulai
        ";

        $holidayRecords = $db->query($query, [
            $startDate,
            $endDate,
            $startDate,
            $endDate,
            $startDate,
            $endDate
        ])->getResultArray();

        $holidays = [];
        $holidayDetails = [];

        foreach ($holidayRecords as $record) {
            $start = strtotime($record['tanggal_mulai']);
            $end = strtotime($record['tanggal_selesai']);

            // Add each day in the holiday range
            for ($current = $start; $current <= $end; $current = strtotime('+1 day', $current)) {
                $dateStr = date('Y-m-d', $current);

                // Only include dates within our target month
                if (date('Y-m', $current) === "$year-$monthStr") {
                    $holidays[] = $dateStr;
                    $holidayDetails[$dateStr] = [
                        'status' => $record['status'],
                        'keterangan' => $record['keterangan']
                    ];
                }
            }
        }

        return [
            'dates' => array_unique($holidays),
            'details' => $holidayDetails
        ];
    }

    /**
     * Calculate effective school days for a specific month
     */
    private function getEffectiveDaysForMonth($year, $month)
    {
        $monthStr = str_pad($month, 2, '0', STR_PAD_LEFT);
        $startDate = "$year-$monthStr-01";
        $daysInMonth = (int)date('t', strtotime($startDate));

        $holidayData = $this->getHolidaysForMonth($year, $month);
        $holidays = $holidayData['dates'];

        $effectiveDays = 0;
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDate = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $dayOfWeek = date('w', strtotime($currentDate));
            $isWeekend = ($dayOfWeek == 0 || $dayOfWeek == 6);
            $isHoliday = in_array($currentDate, $holidays);

            if (!$isWeekend && !$isHoliday) {
                $effectiveDays++;
            }
        }

        return $effectiveDays;
    }

    /**
     * Get holiday details for a specific date
     */
    public function getHolidayDetails($date)
    {
        $db = \Config\Database::connect();

        $query = "
            SELECT status, keterangan
            FROM kalender_akademik 
            WHERE ? BETWEEN tanggal_mulai AND tanggal_selesai
            AND status IN ('libur_nasional', 'libur_sekolah', 'off')
            LIMIT 1
        ";

        $result = $db->query($query, [$date])->getRowArray();

        if ($result) {
            return $result;
        }

        // Check if it's weekend
        $dayOfWeek = date('w', strtotime($date));
        if ($dayOfWeek == 0 || $dayOfWeek == 6) {
            return [
                'status' => 'weekend',
                'keterangan' => $dayOfWeek == 0 ? 'Hari Minggu' : 'Hari Sabtu'
            ];
        }

        return null;
    }
}
