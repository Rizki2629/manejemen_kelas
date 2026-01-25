<?php
namespace App\Controllers\Classroom;

use App\Controllers\BaseController;
use App\Models\Classroom\LessonModel;
use App\Models\Classroom\AttachmentModel;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Services;

class LessonController extends BaseController
{
    protected LessonModel $lessonModel;
    protected AttachmentModel $attachmentModel;
    protected \App\Models\Classroom\LessonViewModel $lessonViewModel;

    public function __construct()
    {
    $this->lessonModel = new LessonModel();
    $this->attachmentModel = new AttachmentModel();
    $this->lessonViewModel = new \App\Models\Classroom\LessonViewModel();
    // Ensure helper for random string OR use native method later
    helper('text');
    }

    public function index()
    {
        $session = session();
        $role = $session->get('role');
        $userId = $session->get('user_id');
        $kelas = trim((string)$this->request->getGet('kelas')) ?: null;
        $q = trim((string)$this->request->getGet('q')) ?: null;
        $page = (int)$this->request->getGet('page');
        if ($page < 1) $page = 1;
        $perPage = 9; // grid 3x3

        // Build base query (mirror logic of forListing but with search + pagination)
        $builder = $this->lessonModel->builder();
        $builder->where('deleted_at', null); // ensure soft-deleted excluded
        if ($kelas) {
            $builder->where('kelas', $kelas);
        }
        if ($role === 'siswa') {
            $builder->where('visibility', 'published');
        } elseif ($role === 'guru' || $role === 'walikelas') {
            $builder->groupStart()
                ->where('author_user_id', $userId)
                ->orWhere('visibility', 'published')
            ->groupEnd();
        }
        if ($q) {
            $builder->groupStart()
                ->like('judul', $q)
                ->orLike('konten', $q)
            ->groupEnd();
        }

        // Clone for counting total before limiting
        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults();
        $pages = (int)ceil($total / $perPage);
        if ($pages < 1) $pages = 1;
        if ($page > $pages) $page = $pages;
        $offset = ($page - 1) * $perPage;

        $builder->orderBy('published_at DESC, created_at DESC')
                ->limit($perPage, $offset);
        $lessons = $builder->get()->getResultArray();

        // Stats
        $stats = [];
        if ($role === 'siswa') {
            $stats['published'] = $total;
        } else {
            // published count with same kelas filter (but independent of search term)
            $pubBuilder = $this->lessonModel->builder()->where('deleted_at', null)->where('visibility','published');
            if ($kelas) $pubBuilder->where('kelas',$kelas);
            $stats['published'] = $pubBuilder->countAllResults();
            $draftBuilder = $this->lessonModel->builder()->where('deleted_at', null)->where('visibility','draft')->where('author_user_id',$userId);
            if ($kelas) $draftBuilder->where('kelas',$kelas);
            $stats['drafts'] = $draftBuilder->countAllResults();
        }

        return view('classroom/lessons/index', [
            'lessons' => $lessons,
            'role' => $role,
            'kelasFilter' => $kelas,
            'q' => $q,
            'pagination' => [
                'page' => $page,
                'pages' => $pages,
                'perPage' => $perPage,
                'total' => $total,
            ],
            'stats' => $stats,
        ]);
    }

    public function create()
    {
        $role = session('role');
        if (!in_array($role, ['guru','walikelas','admin'])) {
            return redirect()->back()->with('error','Tidak diizinkan');
        }
        // Ambil list mapel dari NilaiModel bila tersedia
        $mapelList = [];
        try {
            $nilaiModel = new \App\Models\NilaiModel();
            if (method_exists($nilaiModel, 'getMataPelajaranList')) {
                $mapelList = $nilaiModel->getMataPelajaranList(); // associative (kode=>nama)
            }
        } catch(\Throwable $e) {}

        // Auto kelas untuk walikelas
        $autoKelas = null;
        if (in_array($role, ['walikelas','wali_kelas'])) {
            try {
                $db = \Config\Database::connect();
                $row = $db->query("SELECT w.kelas FROM users u JOIN walikelas w ON u.walikelas_id = w.id WHERE u.id=?", [session('user_id')])->getRowArray();
                if($row){ $autoKelas = $row['kelas']; }
            } catch(\Throwable $e) {}
        }
        return view('classroom/lessons/form', [ 'lesson' => null, 'mapelList' => $mapelList, 'autoKelas' => $autoKelas ]);
    }

    public function store(): RedirectResponse
    {
        $role = session('role');
        $userId = session('user_id');
        if (!in_array($role, ['guru','walikelas','admin'])) {
            return redirect()->back()->with('error','Tidak diizinkan');
        }
        // Jika role walikelas dan kelas tidak dikirim, set otomatis
        $kelas = trim($this->request->getPost('kelas'));
        if(!$kelas && in_array($role,['walikelas','wali_kelas'])){
            try {
                $db = \Config\Database::connect();
                $row = $db->query("SELECT w.kelas FROM users u JOIN walikelas w ON u.walikelas_id = w.id WHERE u.id=?", [$userId])->getRowArray();
                if($row){ $kelas = $row['kelas']; }
            } catch(\Throwable $e) {}
        }
        // Accept both legacy form field name 'konten_html' and new 'konten'
        $kontenInput = $this->request->getPost('konten');
        if ($kontenInput === null) {
            $kontenInput = $this->request->getPost('konten_html'); // backward compatibility
        }
        $data = [
            'kelas' => $kelas,
            'mapel' => trim($this->request->getPost('mapel')),
            'judul' => trim($this->request->getPost('judul')),
            'slug' => url_title($this->request->getPost('judul'), '-', true) . '-' . substr(sha1(uniqid()),0,6),
            'konten' => $kontenInput,
            'video_url' => trim($this->request->getPost('video_url')) ?: null,
            'author_user_id' => $userId,
            'visibility' => $this->request->getPost('action') === 'publish' ? 'published' : 'draft',
            'published_at' => $this->request->getPost('action') === 'publish' ? date('Y-m-d H:i:s') : null,
        ];
        if (!$this->lessonModel->insert($data)) {
            return redirect()->back()->withInput()->with('error', implode('\n', $this->lessonModel->errors()));
        }
        $lessonId = $this->lessonModel->getInsertID();

        // Handle attachments (multiple files optional)
        $files = $this->request->getFiles();
        if (!empty($files['attachments']) && is_array($files['attachments'])) {
            foreach ($files['attachments'] as $file) {
                // Guard: ensure we have a valid uploaded file instance
                if (! $file instanceof \CodeIgniter\HTTP\Files\UploadedFile) {
                    continue;
                }
                if (! $file->isValid()) {
                    // skip empty / error entries (UPLOAD_ERR_NO_FILE etc.)
                    continue;
                }
                if ($file->hasMoved()) {
                    // already handled somewhere else
                    continue;
                }
                if ($file->getSize() > 5 * 1024 * 1024) { // 5MB
                    continue;
                }
                $ext = strtolower($file->getExtension());
                $allowed = ['pdf','jpg','jpeg','png','doc','docx','ppt','pptx','xls','xlsx','txt'];
                if (! in_array($ext, $allowed)) {
                    continue;
                }
                // Get MIME before moving (avoids finfo on moved/non-existent tmp file)
                try {
                    $mime = $file->getMimeType();
                } catch (\Throwable $e) {
                    $mime = $file->getClientMimeType(); // fallback
                }
                // random_string() provided by text helper; fallback native if unavailable
                $rand = function_exists('random_string') ? random_string('alnum', 8) : bin2hex(random_bytes(4));
                $newName = 'L' . $lessonId . '_' . $userId . '_' . time() . '_' . $rand . '.' . $ext;
                try {
                    $file->move(WRITEPATH . 'uploads/classroom', $newName, true);
                } catch (\Throwable $e) {
                    // Skip file if move fails
                    continue;
                }
                $this->attachmentModel->insert([
                    'context_type' => 'lesson',
                    'context_id' => $lessonId,
                    'original_name' => $file->getClientName(),
                    'stored_name' => $newName,
                    'mime_type' => $mime,
                    'size_bytes' => $file->getSize(),
                    'uploaded_by' => $userId,
                ]);
            }
        }
        return redirect()->to('/classroom/lessons/'.$lessonId)->with('success','Materi disimpan');
    }

    public function show($id)
    {
        $role = session('role');
        $userId = session('user_id');
        $lesson = $this->lessonModel->find($id);
        if (!$lesson) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Materi tidak ditemukan');
        }
        if ($role === 'siswa' && $lesson['visibility'] !== 'published') {
            return redirect()->back()->with('error','Materi belum dipublish');
        }
        if (in_array($role, ['guru','walikelas']) && $lesson['author_user_id'] !== $userId && $lesson['visibility'] !== 'published') {
            return redirect()->back()->with('error','Akses ditolak');
        }
        // Track unique student views
        if ($role === 'siswa') {
            $existing = $this->lessonViewModel->where('lesson_id', $id)->where('user_id', $userId)->first();
            if (!$existing) {
                // Insert view row
                $this->lessonViewModel->insert([
                    'lesson_id' => $id,
                    'user_id' => $userId,
                    'viewed_at' => date('Y-m-d H:i:s'),
                ]);
                // Increment aggregated counter (ignore failures)
                try {
                    $this->lessonModel->set('view_count', 'view_count+1', false)->where('id', $id)->update();
                    // Refresh lesson array with new count
                    $lesson['view_count'] = (int)($lesson['view_count'] ?? 0) + 1;
                } catch (\Throwable $e) {}
            }
        }
        $attachments = $this->attachmentModel->for('lesson',(int)$id);
        // Ambil daftar siswa yang sudah melihat (hanya nama unik) untuk sisi kanan tampilan
        $viewers = [];
        try {
            $db = \Config\Database::connect();
            $viewers = $db->table('classroom_lesson_views v')
                ->select('u.id, COALESCE(u.nama, u.username) as nama, v.viewed_at')
                ->join('users u','u.id = v.user_id','left')
                ->where('v.lesson_id', $id)
                ->orderBy('v.viewed_at','DESC')
                ->get()->getResultArray();
        } catch(\Throwable $e) { log_message('error','Fetch lesson viewers failed: '.$e->getMessage()); }
        return view('classroom/lessons/show', ['lesson' => $lesson, 'role' => $role, 'attachments'=>$attachments, 'viewers'=>$viewers]);
    }

    public function publish($id)
    {
        $role = session('role');
        $userId = session('user_id');
        $lesson = $this->lessonModel->find($id);
        if (!$lesson) {
            return redirect()->back()->with('error','Materi tidak ditemukan');
        }
        if (!in_array($role,['guru','walikelas','admin']) || ($lesson['author_user_id'] !== $userId && $role !== 'admin')) {
            return redirect()->back()->with('error','Tidak diizinkan');
        }
        $this->lessonModel->publish($id);
        return redirect()->back()->with('success','Materi dipublish');
    }
}
