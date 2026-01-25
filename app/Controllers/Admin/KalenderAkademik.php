<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KalenderAkademikModel;

class KalenderAkademik extends BaseController
{
    protected $kalenderModel;

    public function __construct()
    {
        $this->kalenderModel = new KalenderAkademikModel();
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
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Akses ditolak: Hanya admin yang dapat mengakses kalender akademik');
        }

        // Get current month and year or from request
        $currentMonth = $this->request->getGet('month') ?? date('n');
        $currentYear = $this->request->getGet('year') ?? date('Y');

        // Get calendar events for current month
        $events = $this->kalenderModel->getCalendarEvents($currentYear, $currentMonth);

        // Generate calendar data
        $calendarData = $this->generateCalendarData($currentYear, $currentMonth, $events);

        $data = [
            'title' => 'Kalender Akademik',
            'currentMonth' => $currentMonth,
            'currentYear' => $currentYear,
            'monthName' => $this->getMonthName($currentMonth),
            'calendarData' => $calendarData,
            'events' => $events,
            'statusOptions' => [
                'off' => 'Off',
                'libur_sekolah' => 'Libur Sekolah',
                'ujian' => 'Ujian',
                'kegiatan' => 'Kegiatan'
            ]
        ];

        return view('admin/kalender-akademik/index', $data);
    }

    public function getEventsByDate()
    {
        $session = session();
        
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setJSON(['error' => 'Unauthorized access']);
        }

        $date = $this->request->getPost('date');
        
        if (empty($date)) {
            return $this->response->setJSON(['error' => 'Tanggal harus diisi']);
        }

        $events = $this->kalenderModel->getEventsByDate($date);
        $dayOfWeek = date('w', strtotime($date));
        $isWeekend = ($dayOfWeek == 0 || $dayOfWeek == 6);

        return $this->response->setJSON([
            'success' => true,
            'date' => $date,
            'events' => $events,
            'isWeekend' => $isWeekend,
            'dayName' => $this->getDayName($dayOfWeek)
        ]);
    }

    public function saveEvent()
    {
        $session = session();
        
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setJSON(['error' => 'Unauthorized access']);
        }

        $tanggalMulai = $this->request->getPost('tanggal_mulai');
        $tanggalSelesai = $this->request->getPost('tanggal_selesai');
        $status = $this->request->getPost('status');
        $keterangan = $this->request->getPost('keterangan');

        if (empty($tanggalMulai) || empty($tanggalSelesai) || empty($status)) {
            return $this->response->setJSON(['error' => 'Semua field harus diisi']);
        }

        // Validate date range
        if (strtotime($tanggalSelesai) < strtotime($tanggalMulai)) {
            return $this->response->setJSON(['error' => 'Tanggal selesai tidak boleh lebih kecil dari tanggal mulai']);
        }

        try {
            $eventData = [
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
                'status' => $status,
                'keterangan' => $keterangan,
                'is_manual' => 1,
                'created_by' => $session->get('user_id')
            ];

            $result = $this->kalenderModel->insertWithOverlapCheck($eventData);

            if ($result) {
                // Log the action
                log_message('info', "Calendar event created: {$tanggalMulai} to {$tanggalSelesai}, status: {$status} by user " . $session->get('username'));

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Event berhasil disimpan',
                    'data' => $eventData
                ]);
            } else {
                return $this->response->setJSON(['error' => 'Gagal menyimpan event']);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function deleteEvent()
    {
        $session = session();
        
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setJSON(['error' => 'Unauthorized access']);
        }

        $date = $this->request->getPost('date');
        $deleteAll = $this->request->getPost('delete_all') === 'true'; // New parameter

        if (empty($date)) {
            return $this->response->setJSON(['error' => 'Tanggal harus diisi']);
        }

        try {
            // Determine which events to delete based on delete_all parameter
            if ($deleteAll) {
                // Delete ALL events (including built-in holidays) for this date
                $result = $this->kalenderModel->where('tanggal_mulai <=', $date)
                                            ->where('tanggal_selesai >=', $date)
                                            ->delete();
                $message = 'Semua status (termasuk libur bawaan) berhasil dihapus';
                $logMessage = "All calendar events (including built-in) deleted for date: {$date}";
            } else {
                // Delete only manual events for this date (original behavior)
                $result = $this->kalenderModel->where('tanggal_mulai <=', $date)
                                            ->where('tanggal_selesai >=', $date)
                                            ->where('is_manual', 1)
                                            ->delete();
                $message = 'Status manual berhasil dihapus';
                $logMessage = "Manual calendar events deleted for date: {$date}";
            }

            if ($result) {
                log_message('info', $logMessage . " by user " . $session->get('username'));

                return $this->response->setJSON([
                    'success' => true,
                    'message' => $message
                ]);
            } else {
                return $this->response->setJSON(['error' => 'Tidak ada event yang dihapus']);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Update or override existing event (including built-in holidays)
     */
    public function updateEvent()
    {
        $session = session();
        
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setJSON(['error' => 'Unauthorized access']);
        }

        $eventId = $this->request->getPost('event_id');
        $tanggalMulai = $this->request->getPost('tanggal_mulai');
        $tanggalSelesai = $this->request->getPost('tanggal_selesai');
        $status = $this->request->getPost('status');
        $keterangan = $this->request->getPost('keterangan');

        if (empty($eventId) || empty($tanggalMulai) || empty($tanggalSelesai) || empty($status)) {
            return $this->response->setJSON(['error' => 'Semua field wajib harus diisi']);
        }

        try {
            // Check if event exists
            $existingEvent = $this->kalenderModel->find($eventId);
            if (!$existingEvent) {
                return $this->response->setJSON(['error' => 'Event tidak ditemukan']);
            }

            $updateData = [
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
                'status' => $status,
                'keterangan' => $keterangan,
                'is_manual' => 1, // Mark as manual when updated
                'created_by' => $session->get('user_id')
            ];

            $result = $this->kalenderModel->update($eventId, $updateData);

            if ($result) {
                log_message('info', "Calendar event updated (ID: {$eventId}) by user " . $session->get('username'));

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Event berhasil diupdate'
                ]);
            } else {
                return $this->response->setJSON(['error' => 'Gagal mengupdate event']);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => 'Error: ' . $e->getMessage()]);
        }
    }

    private function generateCalendarData($year, $month, $events)
    {
        $firstDay = mktime(0, 0, 0, $month, 1, $year);
        $daysInMonth = date('t', $firstDay);
        $dayOfWeek = date('w', $firstDay);
        
        $calendar = [];
        
        // Add empty cells for days before the first day of the month
        for ($i = 0; $i < $dayOfWeek; $i++) {
            $calendar[] = null;
        }
        
        // Add days of the month
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $dayOfWeek = date('w', strtotime($date));
            $isWeekend = ($dayOfWeek == 0 || $dayOfWeek == 6);
            
            // Find events for this date
            $dayEvents = array_filter($events, function($event) use ($date) {
                return $date >= $event['tanggal_mulai'] && $date <= $event['tanggal_selesai'];
            });
            
            $calendar[] = [
                'day' => $day,
                'date' => $date,
                'isWeekend' => $isWeekend,
                'events' => array_values($dayEvents),
                'isToday' => $date === date('Y-m-d')
            ];
        }
        
        return $calendar;
    }

    private function getMonthName($month)
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return $months[$month];
    }

    private function getDayName($dayOfWeek)
    {
        $days = [
            0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu',
            4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'
        ];
        
        return $days[$dayOfWeek];
    }
}
