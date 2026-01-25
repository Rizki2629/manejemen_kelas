<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SiswaModel;
use App\Models\WalikelasModel;

class DataSiswa extends BaseController
{
    protected $siswaModel;
    protected $walikelasModel;

    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->walikelasModel = new WalikelasModel();
    }

    public function index()
    {
        $session = session();
        
        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        // Get user info from session
        $userRole = $session->get('role');
        $userId = $session->get('user_id');
        $walikelasId = $session->get('walikelas_id');

        // Get parameters for pagination and search
        $perPage = 20;
        $page = $this->request->getVar('page') ?? 1;
        $search = $this->request->getVar('search') ?? '';
        $kelas = $this->request->getVar('kelas') ?? '';
        $jk = $this->request->getVar('jk') ?? '';

        // Build query with filters
        $builder = $this->siswaModel;

        // Apply role-based access control
        if ($userRole === 'walikelas' && !empty($walikelasId)) {
            // Get wali kelas data to find the class they manage
            $walikelasData = $this->walikelasModel->find($walikelasId);
            if ($walikelasData && !empty($walikelasData['kelas'])) {
                // Restrict to only students in this wali kelas's class
                $builder = $builder->where('kelas', $walikelasData['kelas']);
                
                // If kelas filter is provided, it must match the wali kelas's class
                if (!empty($kelas) && $kelas !== $walikelasData['kelas']) {
                    // Override kelas filter to match wali kelas's class
                    $kelas = $walikelasData['kelas'];
                }
                
                // Set default kelas filter for wali kelas
                if (empty($kelas)) {
                    $kelas = $walikelasData['kelas'];
                }
            } else {
                // If wali kelas data not found, show no data
                $builder = $builder->where('1', '0'); // This will return no results
            }
        }
        // For admin role, no restrictions are applied

        // Apply search filter
        if (!empty($search)) {
            $builder = $builder->groupStart()
                             ->like('nama', $search)
                             ->orLike('nisn', $search)
                             ->orLike('nipd', $search)
                             ->groupEnd();
        }

        // Apply class filter (already applied for walikelas above)
        if (!empty($kelas) && $userRole !== 'walikelas') {
            $builder = $builder->where('kelas', $kelas);
        }

        // Apply gender filter
        if (!empty($jk)) {
            $builder = $builder->where('jk', $jk);
        }

        // Get paginated data
        $siswa = $builder->paginate($perPage, 'default', $page);
        $pager = $this->siswaModel->pager;

        // Get filter options based on user role
        if ($userRole === 'walikelas' && !empty($walikelasId)) {
            // For wali kelas, only show their class as option
            $walikelasData = $this->walikelasModel->find($walikelasId);
            $kelasOnly = $walikelasData && !empty($walikelasData['kelas']) ? [$walikelasData['kelas']] : [];
        } else {
            // For admin, show all available classes
            $kelasOptions = $this->siswaModel->select('kelas')
                                            ->distinct()
                                            ->where('kelas IS NOT NULL')
                                            ->orderBy('kelas')
                                            ->findAll();
            
            $kelasOnly = [];
            foreach ($kelasOptions as $item) {
                $kelasOnly[] = $item['kelas'];
            }
        }

        // Get statistics based on user role
        if ($userRole === 'walikelas' && !empty($walikelasId)) {
            // For wali kelas, only count students in their class
            $walikelasData = $this->walikelasModel->find($walikelasId);
            if ($walikelasData && !empty($walikelasData['kelas'])) {
                $siswaModelStats = new SiswaModel();
                $totalSiswa = $siswaModelStats->where('kelas', $walikelasData['kelas'])->countAllResults();
                
                $siswaModelLaki = new SiswaModel();
                $siswaLaki = $siswaModelLaki->where('kelas', $walikelasData['kelas'])->where('jk', 'L')->countAllResults();
                
                $siswaModelPerempuan = new SiswaModel();
                $siswaPerempuan = $siswaModelPerempuan->where('kelas', $walikelasData['kelas'])->where('jk', 'P')->countAllResults();
            } else {
                $totalSiswa = 0;
                $siswaLaki = 0;
                $siswaPerempuan = 0;
            }
        } else {
            // For admin, count all students
            $siswaModelStats = new SiswaModel();
            $totalSiswa = $siswaModelStats->countAllResults();
            
            $siswaModelLaki = new SiswaModel();
            $siswaLaki = $siswaModelLaki->where('jk', 'L')->countAllResults();
            
            $siswaModelPerempuan = new SiswaModel();
            $siswaPerempuan = $siswaModelPerempuan->where('jk', 'P')->countAllResults();
        }

        // Get user wali kelas data if applicable
        $userWalikelasData = null;
        if ($userRole === 'walikelas' && !empty($walikelasId)) {
            $userWalikelasData = $this->walikelasModel->find($walikelasId);
        }

        $data = [
            'siswa' => $siswa,
            'pager' => $pager,
            'currentPage' => $page,
            'perPage' => $perPage,
            'search' => $search,
            'selectedKelas' => $kelas,
            'selectedJk' => $jk,
            'kelasOptions' => $kelasOnly,
            'totalSiswa' => $totalSiswa,
            'siswaLaki' => $siswaLaki,
            'siswaPerempuan' => $siswaPerempuan,
            'userRole' => $userRole,
            'userWalikelasData' => $userWalikelasData,
            'title' => 'Data Siswa'
        ];

        return view('admin/data-siswa/index', $data);
    }

    public function create()
    {
        $session = session();
        
        // Check if user is logged in and has permission
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        // Only admin can create new students
        $userRole = $session->get('role');
        if ($userRole === 'walikelas') {
            return redirect()->to('/admin/data-siswa')->with('error', 'Anda tidak memiliki izin untuk menambah data siswa');
        }

        $data = [
            'title' => 'Tambah Siswa Baru'
        ];
        
        return view('admin/data-siswa/create', $data);
    }

    public function store()
    {
        $session = session();
        
        // Check if user is logged in and has permission
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        // Only admin can create new students
        $userRole = $session->get('role');
        if ($userRole === 'walikelas') {
            return redirect()->to('/admin/data-siswa')->with('error', 'Anda tidak memiliki izin untuk menambah data siswa');
        }

        // Validation rules
        $validation = \Config\Services::validation();
        $validation->setRules([
            'nama' => 'required|min_length[3]|max_length[100]',
            'nisn' => 'required|numeric|is_unique[tb_siswa.nisn]',
            'jk' => 'required|in_list[L,P]',
            'kelas' => 'required|max_length[20]',
            'email' => 'permit_empty|valid_email|is_unique[tb_siswa.email]',
            'hp' => 'required|numeric|min_length[10]|max_length[15]',
            'tanggal_lahir' => 'permit_empty|valid_date'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('validation', $validation);
        }

        // Prepare data for insertion
        $data = [
            'nama' => $this->request->getPost('nama'),
            'nisn' => $this->request->getPost('nisn'),
            'nipd' => $this->request->getPost('nipd'),
            'jk' => $this->request->getPost('jk'),
            'kelas' => $this->request->getPost('kelas'),
            'tempat_lahir' => $this->request->getPost('tempat_lahir'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
            'nik' => $this->request->getPost('nik'),
            'agama' => $this->request->getPost('agama'),
            'alamat' => $this->request->getPost('alamat'),
            'email' => $this->request->getPost('email'),
            'hp' => $this->request->getPost('hp')
        ];

        // Insert data
        if ($this->siswaModel->insert($data)) {
            return redirect()->to('/admin/data-siswa')->with('success', 'Data siswa berhasil ditambahkan');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan data siswa');
        }
    }

    public function edit($id)
    {
        $session = session();
        
        // Check if user is logged in and has permission
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        // Only admin can edit students
        $userRole = $session->get('role');
        if ($userRole === 'walikelas') {
            return redirect()->to('/admin/data-siswa')->with('error', 'Anda tidak memiliki izin untuk mengedit data siswa');
        }

        $siswa = $this->siswaModel->find($id);
        
        if (!$siswa) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Siswa tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Data Siswa - ' . $siswa['nama'],
            'siswa' => $siswa
        ];
        
        return view('admin/data-siswa/edit', $data);
    }

    public function update($id)
    {
        $session = session();
        
        // Check if user is logged in and has permission
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        // Only admin can update students
        $userRole = $session->get('role');
        if ($userRole === 'walikelas') {
            return redirect()->to('/admin/data-siswa')->with('error', 'Anda tidak memiliki izin untuk mengedit data siswa');
        }

        $siswa = $this->siswaModel->find($id);
        
        if (!$siswa) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Siswa tidak ditemukan');
        }

        // Validation rules (with exceptions for current record)
        $validation = \Config\Services::validation();
        $validation->setRules([
            'nama' => 'required|min_length[3]|max_length[100]',
            'nisn' => "required|numeric|is_unique[tb_siswa.nisn,id,{$id}]",
            'jk' => 'required|in_list[L,P]',
            'kelas' => 'required|max_length[20]',
            'email' => "permit_empty|valid_email|is_unique[tb_siswa.email,id,{$id}]",
            'hp' => 'required|numeric|min_length[10]|max_length[15]',
            'tanggal_lahir' => 'permit_empty|valid_date'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('validation', $validation);
        }

        // Prepare data for update
        $data = [
            'nama' => $this->request->getPost('nama'),
            'nisn' => $this->request->getPost('nisn'),
            'nipd' => $this->request->getPost('nipd'),
            'jk' => $this->request->getPost('jk'),
            'kelas' => $this->request->getPost('kelas'),
            'tempat_lahir' => $this->request->getPost('tempat_lahir'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
            'nik' => $this->request->getPost('nik'),
            'agama' => $this->request->getPost('agama'),
            'alamat' => $this->request->getPost('alamat'),
            'email' => $this->request->getPost('email'),
            'hp' => $this->request->getPost('hp')
        ];

        // Update data
        if ($this->siswaModel->update($id, $data)) {
            return redirect()->to('/admin/data-siswa')->with('success', 'Data siswa berhasil diperbarui');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data siswa');
        }
    }

    public function delete($id)
    {
        $session = session();
        
        // Check if user is logged in and has permission
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        // Only admin can delete students
        $userRole = $session->get('role');
        if ($userRole === 'walikelas') {
            return redirect()->to('/admin/data-siswa')->with('error', 'Anda tidak memiliki izin untuk menghapus data siswa');
        }

        $siswa = $this->siswaModel->find($id);
        
        if (!$siswa) {
            return redirect()->to('/admin/data-siswa')->with('error', 'Data siswa tidak ditemukan');
        }

        // Delete student data
        if ($this->siswaModel->delete($id)) {
            return redirect()->to('/admin/data-siswa')->with('success', 'Data siswa berhasil dihapus');
        } else {
            return redirect()->to('/admin/data-siswa')->with('error', 'Gagal menghapus data siswa');
        }
    }

    public function detail($id)
    {
        $session = session();
        
        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        // Get user info from session
        $userRole = $session->get('role');
        $walikelasId = $session->get('walikelas_id');

        $siswa = $this->siswaModel->find($id);
        
        if (!$siswa) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Siswa tidak ditemukan');
        }

        // Apply role-based access control for detail view
        if ($userRole === 'walikelas' && !empty($walikelasId)) {
            // Get wali kelas data to check if the student belongs to their class
            $walikelasData = $this->walikelasModel->find($walikelasId);
            if (!$walikelasData || $siswa['kelas'] !== $walikelasData['kelas']) {
                // If student is not in wali kelas's class, deny access
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Akses ditolak: Siswa bukan dari kelas yang Anda wali');
            }
        }

        $data = [
            'siswa' => $siswa,
            'userRole' => $userRole,
            'title' => 'Detail Siswa - ' . $siswa['nama']
        ];

        return view('admin/data-siswa/detail', $data);
    }

    public function export()
    {
        $session = session();
        
        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        // Get user info from session
        $userRole = $session->get('role');
        $walikelasId = $session->get('walikelas_id');

        // Build query based on user role
        $builder = $this->siswaModel;

        // Apply role-based access control for export
        if ($userRole === 'walikelas' && !empty($walikelasId)) {
            // Get wali kelas data to find the class they manage
            $walikelasData = $this->walikelasModel->find($walikelasId);
            if ($walikelasData && !empty($walikelasData['kelas'])) {
                // Restrict export to only students in this wali kelas's class
                $builder = $builder->where('kelas', $walikelasData['kelas']);
                $filenamePrefix = 'data_siswa_' . str_replace(' ', '_', $walikelasData['kelas']) . '_';
            } else {
                // If wali kelas data not found, export nothing
                $builder = $builder->where('1', '0');
                $filenamePrefix = 'data_siswa_empty_';
            }
        } else {
            // For admin, export all students
            $filenamePrefix = 'data_siswa_all_';
        }

        // Get siswa data based on access control
        $siswa = $builder->orderBy('nama', 'ASC')->findAll();
        
        // Set headers for CSV download
        $filename = $filenamePrefix . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV Headers
        $headers = [
            'ID', 'Nama', 'NIPD', 'NISN', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir',
            'NIK', 'Agama', 'Alamat', 'RT', 'RW', 'Kelurahan', 'Kecamatan', 'Kode Pos',
            'Kelas', 'Nama Ayah', 'Nama Ibu', 'Telepon', 'Email'
        ];
        fputcsv($output, $headers);
        
        // Data rows
        foreach ($siswa as $row) {
            $csvRow = [
                $row['id'],
                $row['nama'],
                $row['nipd'],
                $row['nisn'],
                $row['jk'] == 'L' ? 'Laki-laki' : 'Perempuan',
                $row['tempat_lahir'],
                $row['tanggal_lahir'],
                $row['nik'],
                $row['agama'],
                $row['alamat'],
                $row['rt'],
                $row['rw'],
                $row['kelurahan'],
                $row['kecamatan'],
                $row['kode_pos'],
                $row['kelas'],
                $row['nama_ayah'],
                $row['nama_ibu'],
                $row['hp'],
                $row['email']
            ];
            fputcsv($output, $csvRow);
        }
        
        fclose($output);
        exit();
    }

    public function exportExcel()
    {
        $session = session();
        
        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        // Get user info from session
        $userRole = $session->get('role');
        $walikelasId = $session->get('walikelas_id');

        // Get filter parameters
        $search = $this->request->getVar('search') ?? '';
        $kelas = $this->request->getVar('kelas') ?? '';
        $jk = $this->request->getVar('jk') ?? '';

        // Build query with filters
        $builder = $this->siswaModel;

        // Apply role-based access control
        if ($userRole === 'walikelas' && !empty($walikelasId)) {
            // Get wali kelas data to find the class they manage
            $walikelasData = $this->walikelasModel->find($walikelasId);
            if ($walikelasData && !empty($walikelasData['kelas'])) {
                // Restrict to only students in this wali kelas's class
                $builder = $builder->where('kelas', $walikelasData['kelas']);
                $filenamePrefix = 'data_siswa_' . str_replace(' ', '_', $walikelasData['kelas']);
                
                // If kelas filter is provided, it must match the wali kelas's class
                if (!empty($kelas) && $kelas !== $walikelasData['kelas']) {
                    $kelas = $walikelasData['kelas'];
                }
                
                // Set default kelas filter for wali kelas
                if (empty($kelas)) {
                    $kelas = $walikelasData['kelas'];
                }
            } else {
                // If wali kelas data not found, export nothing
                $builder = $builder->where('1', '0');
                $filenamePrefix = 'data_siswa_empty';
            }
        } else {
            // For admin, export based on filters
            $filenamePrefix = 'data_siswa';
            if (!empty($kelas)) {
                $filenamePrefix .= '_' . str_replace(' ', '_', $kelas);
            }
        }

        // Apply search filter
        if (!empty($search)) {
            $builder = $builder->groupStart()
                             ->like('nama', $search)
                             ->orLike('nisn', $search)
                             ->orLike('nipd', $search)
                             ->groupEnd();
        }

        // Apply class filter (already applied for walikelas above)
        if (!empty($kelas) && $userRole !== 'walikelas') {
            $builder = $builder->where('kelas', $kelas);
        }

        // Apply gender filter
        if (!empty($jk)) {
            $builder = $builder->where('jk', $jk);
        }

        // Get filtered data
        $siswa = $builder->orderBy('nama', 'ASC')->findAll();

        // Load PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('Sistem Absensi Sekolah')
            ->setLastModifiedBy('Sistem Absensi Sekolah')
            ->setTitle('Data Siswa')
            ->setSubject('Data Siswa')
            ->setDescription('Data siswa yang telah difilter')
            ->setKeywords('siswa data export excel')
            ->setCategory('Data Export');

        // Set headers
        $headers = [
            'A1' => 'No',
            'B1' => 'Nama',
            'C1' => 'NIPD',
            'D1' => 'NISN',
            'E1' => 'Jenis Kelamin',
            'F1' => 'Tempat Lahir',
            'G1' => 'Tanggal Lahir',
            'H1' => 'NIK',
            'I1' => 'Agama',
            'J1' => 'Alamat',
            'K1' => 'RT',
            'L1' => 'RW',
            'M1' => 'Kelurahan',
            'N1' => 'Kecamatan',
            'O1' => 'Kode Pos',
            'P1' => 'Kelas',
            'Q1' => 'Nama Ayah',
            'R1' => 'Nama Ibu',
            'S1' => 'Telepon',
            'T1' => 'Email'
        ];

        // Apply headers
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style headers
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2563EB']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        ];

        $sheet->getStyle('A1:T1')->applyFromArray($headerStyle);

        // Auto-size columns
        foreach (range('A', 'T') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set row height for header
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Fill data
        $row = 2;
        $no = 1;
        foreach ($siswa as $student) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $student['nama']);
            $sheet->setCellValue('C' . $row, $student['nipd']);
            $sheet->setCellValue('D' . $row, $student['nisn']);
            $sheet->setCellValue('E' . $row, $student['jk'] == 'L' ? 'Laki-laki' : 'Perempuan');
            $sheet->setCellValue('F' . $row, $student['tempat_lahir']);
            $sheet->setCellValue('G' . $row, $student['tanggal_lahir']);
            $sheet->setCellValue('H' . $row, $student['nik']);
            $sheet->setCellValue('I' . $row, $student['agama']);
            $sheet->setCellValue('J' . $row, $student['alamat']);
            $sheet->setCellValue('K' . $row, $student['rt']);
            $sheet->setCellValue('L' . $row, $student['rw']);
            $sheet->setCellValue('M' . $row, $student['kelurahan']);
            $sheet->setCellValue('N' . $row, $student['kecamatan']);
            $sheet->setCellValue('O' . $row, $student['kode_pos']);
            $sheet->setCellValue('P' . $row, $student['kelas']);
            $sheet->setCellValue('Q' . $row, $student['nama_ayah']);
            $sheet->setCellValue('R' . $row, $student['nama_ibu']);
            $sheet->setCellValue('S' . $row, $student['hp']);
            $sheet->setCellValue('T' . $row, $student['email']);
            $row++;
        }

        // Style data rows
        if ($row > 2) {
            $dataStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ],
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'wrapText' => true
                ]
            ];

            $sheet->getStyle('A2:T' . ($row - 1))->applyFromArray($dataStyle);

            // Alternate row colors
            for ($i = 2; $i < $row; $i++) {
                if ($i % 2 == 0) {
                    $sheet->getStyle('A' . $i . ':T' . $i)->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('F3F4F6');
                }
            }
        }

        // Add filter description if any filters are applied
        if (!empty($search) || !empty($kelas) || !empty($jk)) {
            $filterRow = $row + 1;
            $sheet->setCellValue('A' . $filterRow, 'Filter yang diterapkan:');
            $sheet->getStyle('A' . $filterRow)->getFont()->setBold(true);
            
            $filterInfo = [];
            if (!empty($search)) $filterInfo[] = 'Pencarian: ' . $search;
            if (!empty($kelas)) $filterInfo[] = 'Kelas: ' . $kelas;
            if (!empty($jk)) $filterInfo[] = 'Jenis Kelamin: ' . ($jk == 'L' ? 'Laki-laki' : 'Perempuan');
            
            $sheet->setCellValue('B' . $filterRow, implode(' | ', $filterInfo));
        }

        // Add export timestamp
        $timestampRow = $row + 3;
        $sheet->setCellValue('A' . $timestampRow, 'Diekspor pada: ' . date('d/m/Y H:i:s'));
        $sheet->getStyle('A' . $timestampRow)->getFont()->setItalic(true);

        // Set filename
        $filename = $filenamePrefix . '_' . date('Y-m-d_H-i-s') . '.xlsx';

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Write file to output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }
}
