<?php
/**
 * Test Holiday Integration via Web Interface
 */

class TestController extends \CodeIgniter\Controller
{
    public function holidayIntegration()
    {
        $output = [];
        
        try {
            $output[] = "=== Testing Holiday Integration ===\n";

            // Test 1: Check if academic calendar has data
            $output[] = "1. Checking Academic Calendar Data:";
            $kalenderModel = new \App\Models\KalenderAkademikModel();
            
            $holidays = $kalenderModel->where('YEAR(tanggal_mulai)', 2025)
                ->where('MONTH(tanggal_mulai)', 7)
                ->findAll();

            $output[] = "Found " . count($holidays) . " holidays in July 2025:";
            foreach ($holidays as $holiday) {
                $output[] = "- {$holiday['tanggal_mulai']} to {$holiday['tanggal_selesai']}: {$holiday['status']} - {$holiday['keterangan']}";
            }

            $output[] = "\n2. Testing Attendance Model Holiday Integration:";
            $absensiModel = new \App\Models\AbsensiModel();

            // Test getHolidayDetails method
            $testDate = '2025-07-04'; // A Friday
            $holidayDetails = $absensiModel->getHolidayDetails($testDate);
            
            if ($holidayDetails) {
                $output[] = "Holiday details for $testDate: {$holidayDetails['status']} - {$holidayDetails['keterangan']}";
            } else {
                $output[] = "No holiday found for $testDate";
            }
            
            // Test weekend detection
            $weekendDate = '2025-07-05'; // A Saturday
            $weekendDetails = $absensiModel->getHolidayDetails($weekendDate);
            
            if ($weekendDetails) {
                $output[] = "Weekend details for $weekendDate: {$weekendDetails['status']} - {$weekendDetails['keterangan']}";
            }

            $output[] = "\n3. Testing Full Attendance Recap:";
            $attendanceData = $absensiModel->getDetailedAttendanceRecap(2025, 7, '5A');

            $output[] = "Students: " . count($attendanceData['students']);
            $output[] = "Days: " . count($attendanceData['days']);
            $output[] = "Holidays: " . count($attendanceData['holidays']);

            if (isset($attendanceData['holidayDetails'])) {
                $output[] = "Holiday details in attendance data:";
                foreach ($attendanceData['holidayDetails'] as $date => $details) {
                    $output[] = "- $date: {$details['status']} - {$details['keterangan']}";
                }
            }

            $output[] = "\n4. Testing AttendanceHelper Integration:";
            
            // Test status handling
            $statuses = ['hadir', 'sakit', 'izin', 'alpha', 'libur_nasional', 'libur_sekolah', 'weekend', 'off'];
            
            foreach ($statuses as $status) {
                $color = \App\Helpers\AttendanceHelper::getStatusColor($status);
                $symbol = \App\Helpers\AttendanceHelper::getStatusSymbol($status);
                $description = \App\Helpers\AttendanceHelper::getStatusDescription($status);
                
                $output[] = "Status '$status': Color='$color', Symbol='$symbol', Description='$description'";
            }

            $output[] = "\n5. Testing Legend Generation:";
            $legend = \App\Helpers\AttendanceHelper::getLegendData();
            
            $output[] = "Legend items:";
            foreach ($legend as $item) {
                $output[] = "- {$item['label']}: {$item['color']} (icon: {$item['icon']})";
            }

        } catch (Exception $e) {
            $output[] = "Error: " . $e->getMessage();
            $output[] = "Stack trace: " . $e->getTraceAsString();
        }

        $output[] = "\n=== Test Complete ===";
        
        // Return as HTML with proper formatting
        $html = '<!DOCTYPE html>
<html>
<head>
    <title>Holiday Integration Test</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .output { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .line { margin: 5px 0; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
    </style>
</head>
<body>
    <h1>Holiday Integration Test Results</h1>
    <div class="output">';
        
        foreach ($output as $line) {
            $class = 'line';
            if (strpos($line, 'Error:') === 0) $class .= ' error';
            elseif (strpos($line, 'Found') === 0 || strpos($line, 'Students:') === 0) $class .= ' success';
            elseif (strpos($line, '===') !== false) $class .= ' info';
            
            $html .= '<div class="' . $class . '">' . htmlspecialchars($line) . '</div>';
        }
        
        $html .= '</div></body></html>';
        
        return $html;
    }
}
