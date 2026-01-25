<?php

namespace App\Helpers;

/**
 * Attendance Helper - Utility functions for attendance views
 */
class AttendanceHelper
{
    /**
     * Get status color class for attendance cell
     */
    public static function getStatusColor($status, $isWeekend = false, $isHoliday = false): string
    {
        return match($status) {
            'hadir' => 'bg-green-100 text-green-800 hover:bg-green-200',
            'izin' => 'bg-blue-100 text-blue-800 hover:bg-blue-200',
            'sakit' => 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200',
            'alpha' => 'bg-red-100 text-red-800 hover:bg-red-200',
            'libur_nasional' => 'bg-purple-100 text-purple-800',
            'libur_sekolah' => 'bg-indigo-100 text-indigo-800',
            'off' => 'bg-gray-100 text-gray-600',
            'weekend' => 'bg-orange-100 text-orange-800',
            default => 'bg-gray-50 text-gray-400'
        };
    }

    /**
     * Get status symbol for attendance cell
     */
    public static function getStatusSymbol($status, $isWeekend = false, $isHoliday = false): string
    {
        return match($status) {
            'hadir' => '✓',
            'izin' => 'I',
            'sakit' => 'S',
            'alpha' => 'A',
            'libur_nasional' => '🏛️',
            'libur_sekolah' => '🏫',
            'off' => '📴',
            'weekend' => '🏠',
            default => '-'
        };
    }

    /**
     * Get status description for tooltip
     */
    public static function getStatusDescription($status): string
    {
        return match($status) {
            'hadir' => 'Hadir',
            'izin' => 'Izin',
            'sakit' => 'Sakit',
            'alpha' => 'Alpha/Tidak Hadir',
            'libur_nasional' => 'Libur Nasional',
            'libur_sekolah' => 'Libur Sekolah',
            'off' => 'Libur/Off',
            'weekend' => 'Weekend',
            default => 'Tidak ada data'
        };
    }

    /**
     * Get header date color for weekends and holidays
     */
    public static function getDateHeaderColor($isWeekend = false, $isHoliday = false): string
    {
        if ($isHoliday) return 'bg-red-500';
        if ($isWeekend) return 'bg-orange-500';
        return 'bg-blue-500';
    }

    /**
     * Get attendance percentage badge color
     */
    public static function getPercentageBadgeColor($percentage): string
    {
        if ($percentage >= 90) return 'bg-green-500';
        if ($percentage >= 80) return 'bg-blue-500';
        if ($percentage >= 70) return 'bg-yellow-500';
        return 'bg-red-500';
    }

    /**
     * Format date for display
     */
    public static function formatDate($date, $format = 'l, d F Y'): string
    {
        return date($format, strtotime($date));
    }

    /**
     * Check if date is weekend
     */
    public static function isWeekend($date): bool
    {
        $dayOfWeek = date('w', strtotime($date));
        return ($dayOfWeek == 0 || $dayOfWeek == 6);
    }

    /**
     * Check if date is holiday
     */
    public static function isHoliday($date, $holidays = []): bool
    {
        return in_array($date, $holidays);
    }

    /**
     * Calculate attendance statistics with correct percentage formula
     */
    public static function calculateStats($attendanceData, $effectiveDays = null): array
    {
        if (empty($attendanceData['students'])) {
            return [
                'total_students' => 0,
                'total_days' => 0,
                'effective_days' => 0,
                'total_hadir' => 0,
                'total_sakit' => 0,
                'total_izin' => 0,
                'total_alpha' => 0,
                'average_percentage' => 0,
                'percent_hadir' => 0,
                'percent_sakit' => 0,
                'percent_izin' => 0,
                'percent_alpha' => 0
            ];
        }

        $totalStudents = count($attendanceData['students']);
        $totalDays = count($attendanceData['days']);
        $effectiveDays = $effectiveDays ?? $totalDays; // Use provided effective days or fallback to total days

        $totalHadir = array_sum(array_map(fn($s) => $s['summary']['hadir'], $attendanceData['students']));
        $totalSakit = array_sum(array_map(fn($s) => $s['summary']['sakit'], $attendanceData['students']));
        $totalIzin = array_sum(array_map(fn($s) => $s['summary']['izin'], $attendanceData['students']));
        $totalAlpha = array_sum(array_map(fn($s) => $s['summary']['alpha'], $attendanceData['students']));

        $averagePercentage = array_sum(array_column($attendanceData['students'], 'percentage')) / $totalStudents;

        // Use correct formula: Total / (effective days * total students) * 100%
        $totalPossibleAttendance = $effectiveDays * $totalStudents;

        return [
            'total_students' => $totalStudents,
            'total_days' => $totalDays,
            'effective_days' => $effectiveDays,
            'total_hadir' => $totalHadir,
            'total_sakit' => $totalSakit,
            'total_izin' => $totalIzin,
            'total_alpha' => $totalAlpha,
            'average_percentage' => $averagePercentage,
            'percent_hadir' => $totalPossibleAttendance > 0 ? ($totalHadir / $totalPossibleAttendance) * 100 : 0,
            'percent_sakit' => $totalPossibleAttendance > 0 ? ($totalSakit / $totalPossibleAttendance) * 100 : 0,
            'percent_izin' => $totalPossibleAttendance > 0 ? ($totalIzin / $totalPossibleAttendance) * 100 : 0,
            'percent_alpha' => $totalPossibleAttendance > 0 ? ($totalAlpha / $totalPossibleAttendance) * 100 : 0,
        ];
    }

    /**
     * Generate legend data
     */
    public static function getLegendData(): array
    {
        return [
            ['color' => 'bg-green-200', 'label' => 'Hadir (✓)', 'icon' => 'fa-check'],
            ['color' => 'bg-yellow-200', 'label' => 'Sakit (S)', 'icon' => 'fa-thermometer-half'],
            ['color' => 'bg-blue-200', 'label' => 'Izin (I)', 'icon' => 'fa-info-circle'],
            ['color' => 'bg-red-200', 'label' => 'Alpha (A)', 'icon' => 'fa-times'],
            ['color' => 'bg-purple-200', 'label' => 'Libur Nasional (🏛️)', 'icon' => 'fa-flag'],
            ['color' => 'bg-indigo-200', 'label' => 'Libur Sekolah (🏫)', 'icon' => 'fa-school'],
            ['color' => 'bg-orange-200', 'label' => 'Weekend (🏠)', 'icon' => 'fa-home'],
            ['color' => 'bg-gray-200', 'label' => 'Off/Libur (📴)', 'icon' => 'fa-power-off'],
        ];
    }

    /**
     * Generate month name in Indonesian
     */
    public static function getIndonesianMonthName($month, $year): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return $months[$month] . ' ' . $year;
    }

    /**
     * Get alert class based on attendance percentage
     */
    public static function getAlertClass($percentage): array
    {
        if ($percentage >= 90) {
            return ['class' => 'bg-green-50 border-green-200 text-green-800', 'icon' => 'fa-check-circle'];
        }
        if ($percentage >= 80) {
            return ['class' => 'bg-blue-50 border-blue-200 text-blue-800', 'icon' => 'fa-info-circle'];
        }
        if ($percentage >= 70) {
            return ['class' => 'bg-yellow-50 border-yellow-200 text-yellow-800', 'icon' => 'fa-exclamation-triangle'];
        }
        
        return ['class' => 'bg-red-50 border-red-200 text-red-800', 'icon' => 'fa-times-circle'];
    }

    /**
     * Format number with proper decimal places
     */
    public static function formatNumber($number, $decimals = 1): string
    {
        return number_format($number, $decimals);
    }

    /**
     * Get responsive class for table columns
     */
    public static function getResponsiveClass($index, $total): string
    {
        $classes = ['px-2 py-3 text-center font-bold border-r border-gray-100 attendance-cell'];
        
        // Add responsive classes based on position
        if ($index > 15) {
            $classes[] = 'hidden lg:table-cell';
        } elseif ($index > 10) {
            $classes[] = 'hidden md:table-cell';
        }
        
        return implode(' ', $classes);
    }

    /**
     * Generate table export data
     */
    public static function prepareExportData($attendanceData): array
    {
        $exportData = [];
        
        // Header row
        $header = ['No', 'Nama Siswa'];
        foreach ($attendanceData['days'] as $day) {
            $header[] = str_pad($day, 2, '0', STR_PAD_LEFT);
        }
        $header = array_merge($header, ['S', 'I', 'A', 'Total', '%']);
        $exportData[] = $header;

        // Data rows
        $no = 1;
        foreach ($attendanceData['students'] as $student) {
            $row = [$no++, $student['nama']];
            
            // Daily attendance
            foreach ($attendanceData['days'] as $day) {
                $dayStr = str_pad($day, 2, '0', STR_PAD_LEFT);
                $status = $student['daily'][$dayStr] ?? '';
                $row[] = self::getStatusSymbol($status);
            }
            
            // Summary
            $row[] = $student['summary']['sakit'];
            $row[] = $student['summary']['izin'];
            $row[] = $student['summary']['alpha'];
            $row[] = $student['summary']['hadir'];
            $row[] = number_format($student['percentage'], 1) . '%';
            
            $exportData[] = $row;
        }

        return $exportData;
    }

    /**
     * Check if attendance is low (below threshold)
     */
    public static function isLowAttendance($percentage, $threshold = 75): bool
    {
        return $percentage < $threshold;
    }

    /**
     * Generate CSS classes for animated elements
     */
    public static function getAnimationClasses($index = 0): string
    {
        $delay = $index * 50; // Stagger animation
        return "fade-in-up" . ($delay > 0 ? " delay-{$delay}" : "");
    }

    /**
     * Calculate effective days (HBE) for a month - total days minus holidays
     */
    public static function calculateEffectiveDays($year, $month, $holidays = []): int
    {
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
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
        
        return $effectiveDays;
    }
}
