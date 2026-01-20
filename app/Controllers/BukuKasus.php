<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BukuKasusModel;
use App\Models\SiswaModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class BukuKasus extends BaseController
{
    protected $bukuKasusModel;
    protected $siswaModel;
    
    public function __construct()
    {
        $this->bukuKasusModel = new BukuKasusModel();
        $this->siswaModel = new SiswaModel();
    }

    public function index()
    {
        // Check role
        $session = session();
        $userRole = $session->get('role');
        $userId = $session->get('user_id');
        
        // Get filter parameters
        $selectedKelas = $this->request->getGet('kelas') ?? '';
        
        if ($userRole === 'admin') {
            // Admin sees all cases
            $kasusList = $this->bukuKasusModel->getKasusWithDetails($selectedKelas, '');
        } else if ($userRole === 'walikelas') {
            // Walikelas only sees their class's cases
            $kelas = $this->getKelasByGuruId($userId);
            if ($kelas) {
                $kasusList = $this->bukuKasusModel->getKasusByKelas($kelas, '');
            } else {
                $kasusList = [];
            }
        } else {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak');
        }
        
        $data = [
            'title' => 'Buku Kasus',
            'kasusList' => $kasusList,
            'kelasList' => $this->getAvailableClasses(),
            'selectedKelas' => $selectedKelas,
        ];
        
        return view('admin/buku_kasus/index', $data);
    }

    public function tambah()
    {
        $session = session();
        $userRole = $session->get('role');
        $userId = $session->get('user_id');
        
        // Only admin and walikelas can add cases
        if (!in_array($userRole, ['admin', 'walikelas'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak');
        }
        
        $data = [
            'title' => 'Tambah Kasus Baru',
            'validation' => \Config\Services::validation(),
        ];
        
        if ($userRole === 'walikelas') {
            $kelas = $this->getKelasByGuruId($userId);
            if ($kelas) {
                // Provide kelasList with a single class for walikelas
                $data['kelasList'] = [[ 'nama' => $kelas ]];
                $data['siswaList'] = $this->siswaModel->getSiswaByClass($kelas);
            } else {
                $data['kelasList'] = [];
                $data['siswaList'] = [];
            }
        } else {
            // Admin can see all classes and select students by class
            $data['kelasList'] = $this->getAvailableClasses();
            $data['siswaList'] = [];
        }
        
        return view('admin/buku_kasus/tambah', $data);
    }

    public function simpan()
    {
        $rules = [
            'siswa_id' => 'required|is_natural_no_zero',
            'tanggal_kejadian' => 'required|valid_date',
            'deskripsi_kasus' => 'required|min_length[5]',
            'tindakan_yang_diambil' => 'permit_empty|string',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $siswaId = (int) $this->request->getPost('siswa_id');
        $siswa = $this->siswaModel->find($siswaId);
        if (! $siswa) {
            return redirect()->back()->withInput()->with('error', 'Siswa tidak ditemukan');
        }

        $session = session();
        $userId = (int) $session->get('user_id');

        $payload = [
            'siswa_id' => $siswaId,
            'guru_id' => $userId,
            'tanggal_kejadian' => $this->request->getPost('tanggal_kejadian'),
            'deskripsi_kasus' => $this->request->getPost('deskripsi_kasus'),
            'tindakan_yang_diambil' => $this->request->getPost('tindakan_yang_diambil'),
            // Set default values for fields we're not using
            'jenis_kasus' => '-',
            'status' => 'selesai',
            'tingkat_keparahan' => '-',
            'catatan_guru' => null,
        ];

        $this->bukuKasusModel->insert($payload);

        return redirect()->to('/buku-kasus')->with('success', 'Kasus berhasil ditambahkan');
    }

    public function detail($id)
    {
        $session = session();
        $userRole = $session->get('role');
        $userId = $session->get('user_id');
        
        // Check permission
        if (!in_array($userRole, ['admin', 'walikelas'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak');
        }
        
        $kasus = $this->bukuKasusModel->getKasusWithDetails('', '', $id);
        
        if (!$kasus) {
            return redirect()->to('/buku-kasus')->with('error', 'Kasus tidak ditemukan');
        }
        
        // If walikelas, verify this is their class's case
        if ($userRole === 'walikelas') {
            $kelasGuru = $this->getKelasByGuruId($userId);
            if ($kelasGuru !== $kasus['kelas']) {
                return redirect()->to('/buku-kasus')->with('error', 'Akses ditolak');
            }
        }
        
        $data = [
            'title' => 'Detail Kasus',
            'kasus' => $kasus,
        ];
        
        return view('admin/buku_kasus/detail', $data);
    }

    public function edit($id)
    {
        $session = session();
        $userRole = $session->get('role');
        $userId = $session->get('user_id');
        
        // Check permission - both admin and walikelas can edit
        if (!in_array($userRole, ['admin', 'walikelas'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak');
        }
        
        $kasus = $this->bukuKasusModel->find($id);
        
        if (!$kasus) {
            return redirect()->to('/buku-kasus')->with('error', 'Kasus tidak ditemukan');
        }
        
        // If walikelas, verify this is their class's case  
        if ($userRole === 'walikelas') {
            $siswa = $this->siswaModel->find($kasus['siswa_id']);
            $kelas = $this->getKelasByGuruId($userId);
            if ($kelas !== $siswa['kelas']) {
                return redirect()->to('/buku-kasus')->with('error', 'Akses ditolak');
            }
        }
        
        $siswa = $this->siswaModel->find($kasus['siswa_id']);
        
        $data = [
            'title' => 'Edit Kasus',
            'kasus' => $kasus,
            'kelasList' => $this->getAvailableClasses(),
            'siswaList' => $this->siswaModel->getSiswaByClass($siswa['kelas']),
            'kelasDipilih' => $siswa['kelas'],
            'validation' => \Config\Services::validation(),
        ];
        
        return view('admin/buku_kasus/edit', $data);
    }

    public function update($id)
    {
        $rules = [
            'tanggal_kejadian' => 'required|valid_date',
            'deskripsi_kasus' => 'required|min_length[5]',
            'tindakan_yang_diambil' => 'permit_empty|string',
            'siswa_id' => 'permit_empty|if_exist|is_natural_no_zero',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'tanggal_kejadian' => $this->request->getPost('tanggal_kejadian'),
            'deskripsi_kasus' => $this->request->getPost('deskripsi_kasus'),
            'tindakan_yang_diambil' => $this->request->getPost('tindakan_yang_diambil'),
        ];

        $siswaId = $this->request->getPost('siswa_id');
        if (!empty($siswaId)) {
            $data['siswa_id'] = (int) $siswaId;
        }

        $this->bukuKasusModel->update($id, $data);

        return redirect()->to('/buku-kasus')->with('success', 'Kasus berhasil diperbarui');
    }

    public function hapus($id)
    {
        $session = session();
        $userRole = $session->get('role');
        $userId = $session->get('user_id');
        
        // Both admin and walikelas can delete
        if (!in_array($userRole, ['admin', 'walikelas'])) {
            return redirect()->to('/buku-kasus')->with('error', 'Akses ditolak');
        }
        
        // If walikelas, verify this is their class's case
        if ($userRole === 'walikelas') {
            $kasus = $this->bukuKasusModel->find($id);
            if ($kasus) {
                $siswa = $this->siswaModel->find($kasus['siswa_id']);
                $kelas = $this->getKelasByGuruId($userId);
                if ($kelas !== $siswa['kelas']) {
                    return redirect()->to('/buku-kasus')->with('error', 'Akses ditolak');
                }
            }
        }
        
        $this->bukuKasusModel->delete($id);
        
        return redirect()->to('/buku-kasus')->with('success', 'Kasus berhasil dihapus');
    }

    public function getSiswaByKelas()
    {
        $kelas = $this->request->getPost('kelas_nama');
        
        $siswaList = $this->siswaModel->getSiswaByClass($kelas);
        
        return $this->response->setJSON($siswaList);
    }

    public function cetak($id)
    {
        $session = session();
        $userRole = $session->get('role');
        $userId = $session->get('user_id');
        
        // Check permission
        if (!in_array($userRole, ['admin', 'walikelas'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak');
        }
        
        $kasus = $this->bukuKasusModel->getKasusWithDetails('', '', $id);
        
        if (!$kasus) {
            return redirect()->to('/buku-kasus')->with('error', 'Kasus tidak ditemukan');
        }
        
        // If walikelas, verify this is their class's case
        if ($userRole === 'walikelas') {
            $kelasGuru = $this->getKelasByGuruId($userId);
            if ($kelasGuru !== $kasus['kelas']) {
                return redirect()->to('/buku-kasus')->with('error', 'Akses ditolak');
            }
        }
        
        $data = [
            'kasus' => $kasus,
        ];
        $html = view('admin/buku_kasus/cetak_pdf', $data);
        
        // Generate PDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper([0, 0, 612, 936], 'portrait'); // F4/Folio: 215.9mm x 330mm = 612pt x 936pt
        $dompdf->render();
        
        // Stream the PDF to the browser
        $dompdf->stream("Catatan_Kasus_" . $kasus['nama_siswa'] . ".pdf", ['Attachment' => false]);
        
        exit();
    }

    // Helper function to get kelas by guru_id (walikelas)
    private function getKelasByGuruId($guruId)
    {
        $db = \Config\Database::connect();

        // First try walikelas.user_id mapping
        $row = $db->table('walikelas')->select('kelas')->where('user_id', $guruId)->get()->getRowArray();
        if ($row && !empty($row['kelas'])) {
            return $row['kelas'];
        }

        // Fallback: users.walikelas_id -> walikelas.id
        $row = $db->table('users u')
            ->select('w.kelas')
            ->join('walikelas w', 'w.id = u.walikelas_id', 'left')
            ->where('u.id', $guruId)
            ->get()->getRowArray();
        return $row['kelas'] ?? null;
    }

    // Helper function to get available classes
    private function getAvailableClasses()
    {
        // Get distinct class names from tb_siswa
        $db = \Config\Database::connect();
        $builder = $db->table('tb_siswa');
        $builder->select('kelas');
        $builder->distinct();
        $builder->where('deleted_at IS NULL');
        $classes = $builder->get()->getResultArray();
        
        $classList = [];
        foreach ($classes as $class) {
            $classList[] = ['nama' => $class['kelas']];
        }
        
        return $classList;
    }
}
