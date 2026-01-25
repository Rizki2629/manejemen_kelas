<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\ClassModel;
use App\Models\HabitModel;
use App\Models\TbSiswaModel;

class ProfileController extends BaseController
{
    protected $studentModel;
    protected $classModel;
    protected $habitModel;
    protected $tbSiswaModel;

    public function __construct()
    {
    $this->studentModel = new StudentModel();
    $this->classModel = new ClassModel();
    $this->habitModel = new HabitModel();
    $this->tbSiswaModel = new TbSiswaModel();
    }

    public function index()
    {
        $studentId = session('student_id');
        $username = session('username'); // diisi NISN saat login siswa
        if (!$username && !$studentId) {
            return redirect()->to('/login')->with('error', 'Sesi siswa berakhir');
        }

        // Ambil data dasar dari tabel siswa (aplikasi internal)
        $student = null;
        if ($username) {
            $student = $this->studentModel->where('nisn', $username)->first();
            if (!$student) {
                $student = $this->studentModel->where('nis', $username)->first();
            }
        }
        if (!$student && $studentId) {
            $student = $this->studentModel->find($studentId);
        }

        // Ambil data lengkap dari tb_siswa (sumber resmi dapodik) berdasar NISN
        $tbRow = null;
        if ($username) {
            $tbRow = $this->tbSiswaModel->where('nisn', $username)->first();
        } elseif (!empty($student['nisn'])) {
            $tbRow = $this->tbSiswaModel->where('nisn', $student['nisn'])->first();
        }

        // Sinkronkan session student_id bila perlu
        if ($student && (!session()->has('student_id') || session('student_id') != $student['id'])) {
            session()->set('student_id', $student['id']);
        }

        // Gabungkan / override field dengan prioritas tb_siswa
        if ($student || $tbRow) {
            $merged = [
                'nama' => $tbRow['nama'] ?? ($student['nama'] ?? null),
                // NIPD disimpan sebagai 'nis' untuk tampilan sesuai permintaan
                'nis' => $tbRow['nipd'] ?? ($student['nis'] ?? null),
                'nisn' => $tbRow['nisn'] ?? ($student['nisn'] ?? null),
                'jenis_kelamin' => $tbRow['jk'] ?? ($student['jenis_kelamin'] ?? null),
                'tempat_lahir' => $tbRow['tempat_lahir'] ?? ($student['tempat_lahir'] ?? null),
                'tanggal_lahir' => $tbRow['tanggal_lahir'] ?? ($student['tanggal_lahir'] ?? null),
                'alamat' => trim(($tbRow['alamat'] ?? '') . ' ' . ($tbRow['dusun'] ?? '')), // sederhana
                'agama' => $tbRow['agama'] ?? ($student['agama'] ?? null),
                'kelas' => $tbRow['kelas'] ?? null,
            ];
            // Simpan kembali ke $student untuk view (tanpa memodifikasi DB)
            $student = array_merge($student ?? [], $merged);
        }

        if (!$student) {
            return redirect()->to('/login')->with('error', 'Data siswa tidak ditemukan');
        }

        // Pastikan nama lengkap tersedia di session untuk header global (prioritas tb_siswa)
        $canonicalName = $tbRow['nama'] ?? ($student['nama'] ?? null);
        if ($canonicalName && session('student_name') !== $canonicalName) {
            session()->set('student_name', $canonicalName);
        }

        $class = null;
        if (!empty($student['kelas_id'])) {
            $class = $this->classModel->find($student['kelas_id']);
        } elseif (!empty($student['kelas'])) {
            // Kelas string dari tb_siswa
            $class = ['nama' => $student['kelas']];
        }

        // detect Islam
        $isIslam = false;
    $fromTb = $tbRow;
    $agama = $student['agama'] ?? ($tbRow['agama'] ?? null);
        if ($agama && stripos($agama, 'islam') !== false) $isIslam = true;

        // Fallback Tempat/Tanggal Lahir
    $ttlTempat = $student['tempat_lahir'] ?? ($tbRow['tempat_lahir'] ?? null);
    $ttlTanggal = $student['tanggal_lahir'] ?? ($tbRow['tanggal_lahir'] ?? null);

        // get beribadah habit id (fallback to berdoa)
        $ibadahHabit = $this->habitModel->where('code', 'beribadah')->first();
        if (!$ibadahHabit) $ibadahHabit = $this->habitModel->where('code', 'berdoa')->first();
        $ibadahHabitId = $ibadahHabit['id'] ?? null;

        return view('siswa/profile/index', [
            'student' => $student,
            'class' => $class,
            'isIslam' => $isIslam,
            'ibadahHabitId' => $ibadahHabitId,
            'today' => date('Y-m-d'),
            'agama' => $agama,
            'ttlTempat' => $ttlTempat,
            'ttlTanggal' => $ttlTanggal,
        ]);
    }

    /**
     * Endpoint ringan untuk mengambil nama kanonik siswa (dipakai dashboard refreshName)
     */
    public function basicJson()
    {
        $username = session('username');
        if (!$username) {
            return $this->response->setJSON(['nama' => session('student_name') ?? 'Siswa']);
        }
        $db = db_connect();
        $tb = $db->table('tb_siswa')->select('nama, id')->where('nisn', $username)->get()->getFirstRow('array');
        $legacy = $db->table('siswa')->select('id, nama')->where('nisn', $username)->orWhere('nis', $username)->get()->getFirstRow('array');
        $name = $tb['nama'] ?? ($legacy['nama'] ?? (session('student_name') ?? $username));
        if ($name && session('student_name') !== $name) session()->set('student_name', $name);
        if ($legacy && !session()->has('student_id')) session()->set('student_id', (int)$legacy['id']);
        return $this->response->setJSON([
            'nama' => $name,
            'student_id' => session('student_id'),
            'tb_id' => $tb['id'] ?? null
        ]);
    }
}
