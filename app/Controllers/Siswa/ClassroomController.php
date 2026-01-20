<?php
namespace App\Controllers\Siswa;

use App\Controllers\BaseController;
use App\Models\Classroom\LessonModel;
use App\Models\Classroom\AssignmentModel;

class ClassroomController extends BaseController
{
    protected LessonModel $lessonModel;
    protected AssignmentModel $assignmentModel;

    public function __construct()
    {
        $this->lessonModel = new LessonModel();
        $this->assignmentModel = new AssignmentModel();
    }

    /**
     * Halaman Classroom siswa: menampilkan materi & tugas terpublish sesuai kelas siswa.
     */
    public function index()
    {
        if (session('role') !== 'siswa') {
            return redirect()->to('/')->with('error','Hanya untuk siswa');
        }

        // Opsi 1: Jika user meminta tab tugas -> arahkan ke dashboard tugas baru
        $tab = strtolower((string)($this->request->getGet('tab') ?? ''));
        if ($tab === 'tugas') {
            // Pastikan tidak terjadi loop: dashboard siswa tugas ada di /classroom/assignments
            return redirect()->to('/classroom/assignments');
        }

        // Kelas siswa diambil dari session. Jika belum ada, coba resolve dari tb_siswa berdasarkan nisn.
        $kelas = session('kelas');
        if (!$kelas) {
            try {
                $username = session('username'); // NISN
                if ($username) {
                    $db = \Config\Database::connect();
                    $row = $db->table('tb_siswa')->select('kelas')->where('nisn', $username)->get()->getFirstRow('array');
                    if ($row && !empty($row['kelas'])) {
                        $kelas = $row['kelas'];
                        session()->set('kelas', $kelas); // cache ringan
                    }
                }
            } catch (\Throwable $e) {
                log_message('error','ClassroomController index resolve kelas error: '.$e->getMessage());
            }
        }

        if (!$kelas) {
            return view('siswa/classroom/index', [
                'kelas' => null,
                'lessons' => [],
                'assignments' => [],
                'message' => 'Kelas siswa belum terdeteksi. Hubungi wali kelas.'
            ]);
        }

        // Ambil materi terpublish untuk kelas siswa
        $lessons = $this->lessonModel->where('visibility','published')
            ->where('kelas', $kelas)
            ->orderBy('published_at DESC, created_at DESC')
            ->limit(30)
            ->findAll();

        // Ambil tugas terpublish untuk kelas siswa
        $assignments = $this->assignmentModel->where('visibility','published')
            ->where('kelas', $kelas)
            // Custom ordering: tasks with due date first (earliest), then those without due date, then newest created
            ->orderBy('(CASE WHEN due_at IS NULL THEN 1 ELSE 0 END)', 'ASC')
            ->orderBy('due_at','ASC')
            ->orderBy('created_at','DESC')
            ->limit(30)
            ->findAll();

        return view('siswa/classroom/index', [
            'kelas' => $kelas,
            'lessons' => $lessons,
            'assignments' => $assignments,
            'message' => null,
        ]);
    }
}
