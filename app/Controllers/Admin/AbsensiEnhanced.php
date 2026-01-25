<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AbsensiEnhanced extends BaseController
{
    private $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];

    public function rekap()
    {
        $kelas = $this->request->getGet('kelas') ?? '5A';
        $bulan = $this->request->getGet('bulan') ?? 7;
        $tahun = $this->request->getGet('tahun') ?? 2025;

        // Get attendance data (mock for now, replace with real data)
        $data = $this->getAttendanceData($kelas, $bulan, $tahun);

        $viewData = [
            'title' => 'Rekap Absensi Enhanced',
            'kelas' => $kelas,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'bulan_nama' => $this->months[$bulan],
            'attendanceData' => $data,
            'kelasOptions' => $this->getKelasOptions(),
            'months' => $this->months,
            // Additional variables for Tailwind view compatibility
            'userRole' => 'admin',
            'userKelas' => null,
            'allKelas' => $this->getKelasOptions(),
            'filterKelas' => $kelas,
            'filterBulan' => sprintf('%04d-%02d', $tahun, $bulan)
        ];

        return view('admin/absensi/rekap_tailwind', $viewData);
    }

    public function rekapClean()
    {
        $kelas = $this->request->getGet('kelas') ?? '5A';
        $bulan = $this->request->getGet('bulan') ?? 7;
        $tahun = $this->request->getGet('tahun') ?? 2025;

        // Get attendance data (mock for now, replace with real data)
        $data = $this->getAttendanceData($kelas, $bulan, $tahun);

        $viewData = [
            'title' => 'Rekap Absensi Clean',
            'kelas' => $kelas,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'bulan_nama' => $this->months[$bulan],
            'attendanceData' => $data,
            'kelasOptions' => $this->getKelasOptions(),
            'months' => $this->months,
            // Additional variables for Tailwind view compatibility
            'userRole' => 'admin',
            'userKelas' => null,
            'allKelas' => $this->getKelasOptions(),
            'filterKelas' => $kelas,
            'filterBulan' => sprintf('%04d-%02d', $tahun, $bulan)
        ];

        return view('admin/absensi/rekap_tailwind', $viewData);
    }

    public function exportExcel()
    {
        $kelas = $this->request->getGet('kelas') ?? '5A';
        $bulan = (int)($this->request->getGet('bulan') ?? 7);
        $tahun = (int)($this->request->getGet('tahun') ?? 2025);

        // Load models for getting signature data
        $absensiModel = new \App\Models\AbsensiModel();
        $walikelasModel = new \App\Models\WalikelasModel();
        $profilSekolahModel = new \App\Models\ProfilSekolahModel();
        
        // Get real attendance data using the same method as the view
        $data = $absensiModel->getDetailedAttendanceRecap($tahun, $bulan, $kelas);
        
        // Get walikelas data for the class
        $walikelasData = $walikelasModel->where('kelas', $kelas)->first();
        
        // Get school profile data for kepala sekolah
        $profilSekolah = $profilSekolahModel->getProfilSekolah();
        
        // Determine signature date
        $currentDate = date('Y-m-d');
        $currentMonth = date('n');
        $currentYear = date('Y');
        
        // If we're in the same month and year, use current date; otherwise use end of month
        if ($currentMonth == $bulan && $currentYear == $tahun) {
            $signatureDate = $currentDate;
        } else {
            $lastDayOfMonth = date('t', mktime(0, 0, 0, $bulan, 1, $tahun));
            $signatureDate = sprintf('%04d-%02d-%02d', $tahun, $bulan, $lastDayOfMonth);
        }

        // Calculate days in month for display
        $daysInMonth = date('t', mktime(0, 0, 0, $bulan, 1, $tahun));

        // Create new spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('SDN GROGOL UTARA 09')
            ->setTitle('Rekap Daftar Hadir ' . $kelas . ' - ' . $this->months[$bulan] . ' ' . $tahun)
            ->setSubject('Rekap Daftar Hadir Siswa')
            ->setDescription('Rekap daftar hadir siswa kelas ' . $kelas . ' bulan ' . $this->months[$bulan] . ' tahun ' . $tahun);

        // Header sekolah
        $lastHeaderCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(5 + $daysInMonth + 2); // Include NISN, NIS columns and summary columns
        
        $sheet->setCellValue('A1', 'REKAP DAFTAR HADIR SISWA');
        $sheet->mergeCells('A1:' . $lastHeaderCol . '1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $sheet->setCellValue('A2', 'SDN GROGOL UTARA 09');
        $sheet->mergeCells('A2:' . $lastHeaderCol . '2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $sheet->setCellValue('A3', 'KELAS: ' . $kelas . ' | BULAN: ' . strtoupper($this->months[$bulan]) . ' ' . $tahun);
        $sheet->mergeCells('A3:' . $lastHeaderCol . '3');
        $sheet->getStyle('A3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Add HBE (Effective Days) information
        $effectiveDays = $data['effective_days'] ?? $daysInMonth;
        $totalHolidays = count($data['holidays'] ?? []);
        $sheet->setCellValue('A4', 'HBE (Hari Efektif): ' . $effectiveDays . ' hari | Total Hari: ' . $daysInMonth . ' | Hari Libur: ' . $totalHolidays);
        $sheet->mergeCells('A4:' . $lastHeaderCol . '4');
        $sheet->getStyle('A4')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'CC0000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFEEEE']
            ]
        ]);

        // Header tabel
        $startRow = 6; // Updated to account for the HBE row
        $sheet->setCellValue('A' . $startRow, 'NO');
        $sheet->setCellValue('B' . $startRow, 'NISN');
        $sheet->setCellValue('C' . $startRow, 'NIS');
        $sheet->setCellValue('D' . $startRow, 'NAMA SISWA');
        
        // Header tanggal
        $col = 'E';
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $sheet->setCellValue($col . $startRow, $day);
            $dayOfWeek = date('w', mktime(0, 0, 0, $bulan, $day, $tahun));
            
            // Weekend styling
            if ($dayOfWeek == 0 || $dayOfWeek == 6) {
                $sheet->getStyle($col . $startRow)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E74C3C']
                    ],
                    'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true]
                ]);
            }
            $col++;
        }

        // Header S, I, A
        $sheet->setCellValue($col . $startRow, 's');
        $col++;
        $sheet->setCellValue($col . $startRow, 'i');
        $col++;
        $sheet->setCellValue($col . $startRow, 'a');

        // Style header
        $headerRange = 'A' . $startRow . ':' . $col . $startRow;
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Data siswa
        $row = $startRow + 1;
        $no = 1;
        foreach ($data['students'] as $student) {
            $sheet->setCellValue('A' . $row, $no);
            // Get NISN from student data (from database), fallback to generated if not available
            $nisn = $student['nisn'] ?? ('0' . str_pad($no, 9, '0', STR_PAD_LEFT));
            // Use NIPD as NIS, fallback to generated if not available
            $nis = $student['nipd'] ?? str_pad($no, 4, '0', STR_PAD_LEFT);
            $sheet->setCellValue('B' . $row, $nisn);
            $sheet->setCellValue('C' . $row, $nis);
            // Make student name proper case (title case)
            $sheet->setCellValue('D' . $row, ucwords(strtolower($student['nama'])));
            
            // Set alignment for NO, NISN, NIS columns (center and middle)
            $sheet->getStyle('A' . $row)->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ]);
            $sheet->getStyle('B' . $row)->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ]);
            $sheet->getStyle('C' . $row)->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ]);
            // Set middle alignment for name column
            $sheet->getStyle('D' . $row)->applyFromArray([
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ]);
            
            // Daily attendance
            $col = 'E';
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $dayStr = str_pad($day, 2, '0', STR_PAD_LEFT);
                $attendance = $student['daily'][$dayStr] ?? '';
                
                $mark = '';
                if ($attendance === 'hadir') $mark = '•';
                elseif ($attendance === 'sakit') $mark = 's'; // lowercase
                elseif ($attendance === 'izin') $mark = 'i'; // lowercase
                elseif ($attendance === 'alpha') $mark = 'a'; // lowercase
                
                $sheet->setCellValue($col . $row, $mark);
                
                // Center alignment for attendance marks
                $sheet->getStyle($col . $row)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);
                
                // Weekend styling
                $dayOfWeek = date('w', mktime(0, 0, 0, $bulan, $day, $tahun));
                if ($dayOfWeek == 0 || $dayOfWeek == 6) {
                    $sheet->getStyle($col . $row)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'FFEBEE']
                        ]
                    ]);
                }
                $col++;
            }
            
            // Summary
            $sheet->setCellValue($col . $row, $student['summary']['sakit']);
            $sheet->getStyle($col . $row)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            $col++;
            $sheet->setCellValue($col . $row, $student['summary']['izin']);
            $sheet->getStyle($col . $row)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            $col++;
            $sheet->setCellValue($col . $row, $student['summary']['alpha']);
            $sheet->getStyle($col . $row)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            
            $row++;
            $no++;
        }

        // Summary rows
        $totalRow = $row + 1;
        $sheet->setCellValue('A' . $totalRow, 'TOTAL');
        $sheet->mergeCells('A' . $totalRow . ':D' . $totalRow);
        
        // Calculate totals with correct array access
        $totalSakit = array_sum(array_map(function($s) { return $s['summary']['sakit']; }, $data['students']));
        $totalIzin = array_sum(array_map(function($s) { return $s['summary']['izin']; }, $data['students']));
        $totalAlpha = array_sum(array_map(function($s) { return $s['summary']['alpha']; }, $data['students']));

        // Position totals in correct columns (same as individual students)
        $startSummaryCol = 5 + $daysInMonth; // E + days = first summary column
        $sakitTotalCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startSummaryCol);
        $izinTotalCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startSummaryCol + 1);
        $alphaTotalCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startSummaryCol + 2);
        
        $sheet->setCellValue($sakitTotalCol . $totalRow, $totalSakit);
        $sheet->setCellValue($izinTotalCol . $totalRow, $totalIzin);
        $sheet->setCellValue($alphaTotalCol . $totalRow, $totalAlpha);

        // Merge cells after totals to match the image (from day columns)
        $startDayCol = 5; // Column E (first day)
        $endDayCol = 4 + $daysInMonth; // Last day column
        $dayMergeRange = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startDayCol) . $totalRow . ':' . 
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($endDayCol) . $totalRow;
        $sheet->mergeCells($dayMergeRange);

        // Apply borders only to data table area (not headers)
        $dataStartRow = $startRow; // Header row
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startSummaryCol + 2);
        $dataRange = 'A' . $dataStartRow . ':' . $lastCol . $totalRow;
        
        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->getStyle($dataRange)->applyFromArray($borderStyle);

        // Set column widths for better appearance
        $sheet->getColumnDimension('A')->setWidth(5); // No
        $sheet->getColumnDimension('B')->setWidth(12); // NISN
        $sheet->getColumnDimension('C')->setWidth(8); // NIS
        $sheet->getColumnDimension('D')->setWidth(25); // Nama
        
        // Date columns
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(4 + $i);
            $sheet->getColumnDimension($col)->setWidth(3);
        }
        
        // Summary columns
        $sheet->getColumnDimension($sakitTotalCol)->setWidth(5);
        $sheet->getColumnDimension($izinTotalCol)->setWidth(5);
        $sheet->getColumnDimension($alphaTotalCol)->setWidth(5);

        // Page setup for F4 paper
        $sheet->getPageSetup()
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_FOLIO) // F4 paper
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
            ->setFitToWidth(1)
            ->setFitToHeight(0);
            
        // Set margins - smaller left and right margins
        $sheet->getPageMargins()
            ->setTop(0.75)
            ->setRight(0.5)
            ->setBottom(0.75)
            ->setLeft(0.5);

        // Center header text (no borders for headers)
        $lastColForMerge = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startSummaryCol + 2);
        $sheet->getStyle('A1:' . $lastColForMerge . '1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:' . $lastColForMerge . '4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Add percentage row with correct formula
        $percentageRow = $totalRow + 1;
        $sheet->setCellValue('A' . $percentageRow, 'PERSENTASE');
        $sheet->mergeCells('A' . $percentageRow . ':D' . $percentageRow);
        
        // Merge cells after percentage to match the image (from day columns)
        $dayMergeRangePercentage = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startDayCol) . $percentageRow . ':' . 
                                  \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($endDayCol) . $percentageRow;
        $sheet->mergeCells($dayMergeRangePercentage);
        
        $totalStudents = count($data['students']);
        $effectiveDays = $data['effective_days'] ?? $daysInMonth;
        $totalPossibleAttendance = $totalStudents * $effectiveDays;
        
        // Calculate correct percentages: Total / (effective days * total students) * 100%
        $percentSakit = $totalPossibleAttendance > 0 ? ($totalSakit / $totalPossibleAttendance) * 100 : 0;
        $percentIzin = $totalPossibleAttendance > 0 ? ($totalIzin / $totalPossibleAttendance) * 100 : 0;
        $percentAlpha = $totalPossibleAttendance > 0 ? ($totalAlpha / $totalPossibleAttendance) * 100 : 0;

        // Calculate column positions correctly
        $startCol = 5 + $daysInMonth; // E + number of days
        $sakitCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startCol);
        $izinCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startCol + 1);
        $alphaCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startCol + 2);
        $lastCol = $alphaCol; // Last column for ranges (removed extra column)

        $sheet->setCellValue($sakitCol . $percentageRow, number_format($percentSakit, 1) . '%');
        $sheet->setCellValue($izinCol . $percentageRow, number_format($percentIzin, 1) . '%');
        $sheet->setCellValue($alphaCol . $percentageRow, number_format($percentAlpha, 1) . '%');

        // Add explanation of formula with merge
        $formulaRow = $percentageRow + 1;
        $sheet->setCellValue('A' . $formulaRow, 'Rumus: Total / (' . $effectiveDays . ' hari efektif × ' . $totalStudents . ' siswa) × 100%');
        $sheet->mergeCells('A' . $formulaRow . ':' . $lastCol . $formulaRow);
        $sheet->getStyle('A' . $formulaRow)->applyFromArray([
            'font' => ['italic' => true, 'size' => 9],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Apply borders to percentage and total rows (extend the table borders)
        $summaryBorderRange = 'A' . $totalRow . ':' . $lastCol . $percentageRow;
        $sheet->getStyle($summaryBorderRange)->applyFromArray($borderStyle);

        // Style summary rows with proper colors
        $summaryRange = 'A' . $totalRow . ':' . $lastCol . $totalRow;
        $sheet->getStyle($summaryRange)->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D4EDDA'] // Light green background like in image
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        $percentageRange = 'A' . $percentageRow . ':' . $lastCol . $percentageRow;
        $sheet->getStyle($percentageRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '000000']], // Black text
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFF3CD'] // Light yellow background like in image
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Add signature section
        $signatureStartRow = $formulaRow + 3; // Add some space after formula
        
        // Calculate signature positions based on table width
        $totalColumns = 5 + $daysInMonth + 2; // NO, NISN, NIS, NAMA + days + S, I, A columns
        $walikelasColumn = max(1, $totalColumns - 9); // Position walikelas 9 columns from the right
        $walikelasColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($walikelasColumn);
        
        // Format signature date for display (Indonesian format)
        $formattedDate = $this->formatIndonesianDate($signatureDate);
        
        // Add signature headers - aligned properly
        $sheet->setCellValue('B' . $signatureStartRow, 'Mengetahui,');
        $sheet->setCellValue($walikelasColLetter . $signatureStartRow, 'Jakarta, ' . $formattedDate);
        
        $sheet->getStyle('B' . $signatureStartRow)->applyFromArray([
            'font' => ['bold' => false, 'size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
        ]);
        $sheet->getStyle($walikelasColLetter . $signatureStartRow)->applyFromArray([
            'font' => ['bold' => false, 'size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
        ]);
        
        $signatureStartRow++;
        
        // Add position titles
        $sheet->setCellValue('B' . $signatureStartRow, 'Kepala SDN Grogol Utara 09');
        $sheet->setCellValue($walikelasColLetter . $signatureStartRow, 'Wali Kelas ' . $kelas);
        
        $sheet->getStyle('B' . $signatureStartRow)->applyFromArray([
            'font' => ['bold' => false, 'size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
        ]);
        $sheet->getStyle($walikelasColLetter . $signatureStartRow)->applyFromArray([
            'font' => ['bold' => false, 'size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
        ]);
        
        // Add empty space for signature
        $signatureStartRow += 4; // Leave space for signature
        
        // Add names
        $kepalaSekolahNama = $profilSekolah['nama_kepala_sekolah'] ?? 'Muhammad Rizki Pratama, S.Pd';
        $walikelasNama = $walikelasData['nama'] ?? 'Elva Dumaria, S.Pd';
        
        $sheet->setCellValue('B' . $signatureStartRow, $kepalaSekolahNama);
        $sheet->setCellValue($walikelasColLetter . $signatureStartRow, $walikelasNama);
        
        $sheet->getStyle('B' . $signatureStartRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
        ]);
        $sheet->getStyle($walikelasColLetter . $signatureStartRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
        ]);
        
        $signatureStartRow++;
        
        // Add NIPs
        $kepalaSekolahNIP = $profilSekolah['nip_kepala_sekolah'] ?? '199303292019031011';
        $walikelasNIP = $walikelasData['nip'] ?? '';
        
        $sheet->setCellValue('B' . $signatureStartRow, 'NIP. ' . $kepalaSekolahNIP);
        if (!empty($walikelasNIP)) {
            $sheet->setCellValue($walikelasColLetter . $signatureStartRow, 'NIP. ' . $walikelasNIP);
        }
        
        $sheet->getStyle('B' . $signatureStartRow)->applyFromArray([
            'font' => ['bold' => false, 'size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
        ]);
        $sheet->getStyle($walikelasColLetter . $signatureStartRow)->applyFromArray([
            'font' => ['bold' => false, 'size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
        ]);

        // Generate filename
        $filename = 'rekap-absensi-' . $kelas . '-' . $this->months[$bulan] . '-' . $tahun . '.xlsx';

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Save file
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function getAttendanceData($kelas, $bulan, $tahun)
    {
        // Mock data - replace with real database queries
        return [
            'kelas' => $kelas,
            'year' => $tahun,
            'month' => $bulan,
            'students' => [
                [
                    'nama' => 'Ahmad Rizki Pratama',
                    'daily' => [
                        '01' => 'hadir', '02' => 'hadir', '03' => 'sakit', '04' => 'hadir',
                        '05' => '', '06' => '', '07' => 'hadir', '08' => 'izin',
                        '09' => 'hadir', '10' => 'hadir', '11' => 'hadir', '12' => 'hadir',
                        '13' => '', '14' => '', '15' => 'hadir', '16' => 'hadir',
                        '17' => 'hadir', '18' => 'hadir', '19' => 'hadir', '20' => 'hadir',
                        '21' => '', '22' => '', '23' => 'hadir', '24' => 'hadir',
                        '25' => 'hadir', '26' => 'hadir', '27' => 'hadir', '28' => 'hadir',
                        '29' => '', '30' => '', '31' => 'hadir'
                    ],
                    'summary' => ['hadir' => 20, 'sakit' => 1, 'izin' => 1, 'alpha' => 0],
                    'percentage' => 90.9
                ],
                [
                    'nama' => 'Siti Nurhaliza',
                    'daily' => [
                        '01' => 'hadir', '02' => 'hadir', '03' => 'hadir', '04' => 'hadir',
                        '05' => '', '06' => '', '07' => 'hadir', '08' => 'hadir',
                        '09' => 'hadir', '10' => 'hadir', '11' => 'hadir', '12' => 'hadir',
                        '13' => '', '14' => '', '15' => 'hadir', '16' => 'hadir',
                        '17' => 'hadir', '18' => 'hadir', '19' => 'hadir', '20' => 'hadir',
                        '21' => '', '22' => '', '23' => 'hadir', '24' => 'hadir',
                        '25' => 'hadir', '26' => 'hadir', '27' => 'hadir', '28' => 'hadir',
                        '29' => '', '30' => '', '31' => 'hadir'
                    ],
                    'summary' => ['hadir' => 22, 'sakit' => 0, 'izin' => 0, 'alpha' => 0],
                    'percentage' => 100.0
                ],
                [
                    'nama' => 'Budi Santoso',
                    'daily' => [
                        '01' => 'hadir', '02' => 'hadir', '03' => 'hadir', '04' => 'alpha',
                        '05' => '', '06' => '', '07' => 'hadir', '08' => 'hadir',
                        '09' => 'hadir', '10' => 'sakit', '11' => 'hadir', '12' => 'hadir',
                        '13' => '', '14' => '', '15' => 'hadir', '16' => 'hadir',
                        '17' => 'hadir', '18' => 'hadir', '19' => 'izin', '20' => 'hadir',
                        '21' => '', '22' => '', '23' => 'hadir', '24' => 'hadir',
                        '25' => 'hadir', '26' => 'hadir', '27' => 'hadir', '28' => 'hadir',
                        '29' => '', '30' => '', '31' => 'hadir'
                    ],
                    'summary' => ['hadir' => 19, 'sakit' => 1, 'izin' => 1, 'alpha' => 1],
                    'percentage' => 86.4
                ]
            ]
        ];
    }

    private function getKelasOptions()
    {
        $simpleKelas = [
            '1A', '1B', '2A', '2B', '3A', '3B', 
            '4A', '4B', '5A', '5B', '6A', '6B'
        ];
        
        // Convert to format expected by view (with kelas property)
        $kelasOptions = [];
        foreach ($simpleKelas as $kelas) {
            $kelasOptions[] = ['kelas' => $kelas];
        }
        
        return $kelasOptions;
    }

    /**
     * Format date to Indonesian format
     */
    private function formatIndonesianDate($date)
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $timestamp = strtotime($date);
        $day = date('j', $timestamp);
        $month = $months[(int)date('n', $timestamp)];
        $year = date('Y', $timestamp);

        return "$day $month $year";
    }
}
