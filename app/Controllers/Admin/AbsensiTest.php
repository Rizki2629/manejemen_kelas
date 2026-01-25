<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class AbsensiTest extends BaseController
{
    public function rekap()
    {
        // Mock data untuk testing tanpa database
        $attendanceData = [
            'kelas' => '5A',
            'year' => 2025,
            'month' => 7,
            'days' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
            'students' => [
                [
                    'nama' => 'Ahmad Rizki Pratama',
                    'daily' => [
                        '01' => 'hadir',
                        '02' => 'hadir', 
                        '03' => 'sakit',
                        '04' => 'hadir',
                        '05' => '',
                        '06' => '',
                        '07' => 'hadir',
                        '08' => 'izin',
                        '09' => 'hadir',
                        '10' => 'hadir'
                    ],
                    'summary' => [
                        'hadir' => 6,
                        'sakit' => 1,
                        'izin' => 1,
                        'alpha' => 0
                    ],
                    'percentage' => 75.0
                ],
                [
                    'nama' => 'Siti Nurhaliza',
                    'daily' => [
                        '01' => 'hadir',
                        '02' => 'hadir',
                        '03' => 'hadir',
                        '04' => 'hadir',
                        '05' => '',
                        '06' => '',
                        '07' => 'hadir',
                        '08' => 'hadir',
                        '09' => 'hadir',
                        '10' => 'hadir'
                    ],
                    'summary' => [
                        'hadir' => 8,
                        'sakit' => 0,
                        'izin' => 0,
                        'alpha' => 0
                    ],
                    'percentage' => 100.0
                ],
                [
                    'nama' => 'Budi Santoso',
                    'daily' => [
                        '01' => 'hadir',
                        '02' => 'alpha',
                        '03' => 'hadir',
                        '04' => 'hadir',
                        '05' => '',
                        '06' => '',
                        '07' => 'sakit',
                        '08' => 'sakit',
                        '09' => 'hadir',
                        '10' => 'hadir'
                    ],
                    'summary' => [
                        'hadir' => 5,
                        'sakit' => 2,
                        'izin' => 0,
                        'alpha' => 1
                    ],
                    'percentage' => 62.5
                ]
            ]
        ];

        $data = [
            'title' => 'Rekap Absensi',
            'userRole' => 'admin',
            'userKelas' => null,
            'allKelas' => [
                ['kelas' => '5A'],
                ['kelas' => '5B'],
                ['kelas' => '6A']
            ],
            'filterKelas' => '5A',
            'filterBulan' => '2025-07',
            'attendanceData' => $attendanceData
        ];

        return view('admin/absensi/rekap-test', $data);
    }
}
