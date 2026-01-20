<?php
namespace App\Controllers\Classroom;

use App\Controllers\BaseController;
use App\Models\Classroom\AssignmentModel;
use App\Models\Classroom\AttachmentModel;
use App\Models\Classroom\SubmissionModel;
use App\Models\Classroom\AssignmentAttemptModel;

class AssignmentController extends BaseController
{
    protected AssignmentModel $assignmentModel;
    protected AttachmentModel $attachmentModel;
    protected SubmissionModel $submissionModel;
    protected AssignmentAttemptModel $attemptModel;

    public function __construct()
    {
        $this->assignmentModel = new AssignmentModel();
        $this->attachmentModel = new AttachmentModel();
        $this->submissionModel = new SubmissionModel();
    $this->attemptModel = new AssignmentAttemptModel();
    }

    public function index()
    {
        $role = session('role');
        if (!in_array($role, ['guru','walikelas','admin','siswa'])) {
            return redirect()->to('/')->with('error', 'Akses ditolak');
        }

        // Handle kelas filter
        $kelasFilter = $this->request->getGet('kelas') ?? '';
        
        $data = [];
        if (in_array($role, ['guru','walikelas'])) {
            $userId = session('user_id');
            $builder = $this->assignmentModel->where('author_user_id', $userId);
            if (!empty($kelasFilter)) {
                $builder = $builder->where('kelas', $kelasFilter);
            }
            $assignments = $builder->findAll();
        } else {
            $builder = $this->assignmentModel;
            if (!empty($kelasFilter)) {
                $builder = $builder->where('kelas', $kelasFilter);
            }
            if ($role === 'siswa') {
                $builder = $builder->where('visibility', 'published');
            }
            $assignments = $builder->findAll();
        }

        // Basic submission counts (for guru/admin views)
        foreach ($assignments as &$assignment) {
            $submissionCount = $this->submissionModel->where('assignment_id', $assignment['id'])->countAllResults();
            $assignment['submission_count'] = $submissionCount;
        }

        // Student personalization: attach user's own submission & compute dashboard stats
        $stats = [ 'total'=>count($assignments), 'belum'=>0, 'dikirim'=>0, 'dinilai'=>0 ];
        if ($role === 'siswa') {
            $userId = session('user_id');
            if (!empty($assignments)) {
                $ids = array_column($assignments, 'id');
                // Fetch all submissions for these assignments by this user in one query
                $subs = $this->submissionModel
                    ->whereIn('assignment_id', $ids)
                    ->where('siswa_user_id', $userId)
                    ->findAll();
                $subsByAid = [];
                foreach ($subs as $s) { $subsByAid[$s['assignment_id']] = $s; }
                foreach ($assignments as &$a) {
                    $sub = $subsByAid[$a['id']] ?? null;
                    $a['user_submission'] = $sub; // may be null
                    if (!$sub) { $stats['belum']++; continue; }
                    if ($sub && $sub['score'] !== null) { $stats['dinilai']++; }
                    else { $stats['dikirim']++; }
                }
            }
        }

        $data['assignments'] = $assignments;
        $data['kelasFilter'] = $kelasFilter;
        $data['role'] = $role;
        if ($role === 'siswa') {
            $data['stats'] = $stats;
            return view('classroom/assignments/index_student_dashboard', $data);
        }
        return view('classroom/assignments/index', $data);
    }

    public function create()
    {
        $role = session('role');
        if (!in_array($role,['guru','walikelas','admin'])) {
            return redirect()->back()->with('error','Tidak diizinkan');
        }
        return view('classroom/assignments/form');
    }

    public function store()
    {
        // Force log to file for debugging
        log_message('info', 'Assignment store() called');
        log_message('info', 'POST data: ' . json_encode($this->request->getPost()));
        log_message('info', '$_FILES data: ' . json_encode($_FILES));
        
        $role = session('role');
        $userId = session('user_id');
        if (!in_array($role,['guru','walikelas','admin'])) {
            return redirect()->back()->with('error','Tidak diizinkan');
        }
        
        $rawQuestions = $this->request->getPost('questions_json');
        $questions = $rawQuestions ? json_decode($rawQuestions, true) : [];
        if (!is_array($questions)) $questions = [];
        
        $data = [
            'kelas' => trim($this->request->getPost('kelas')),
            'mapel' => trim($this->request->getPost('mapel')),
            'judul' => trim($this->request->getPost('judul')),
            'slug' => url_title($this->request->getPost('judul'), '-', true) . '-' . substr(sha1(uniqid()),0,6),
            'deskripsi_html' => $this->request->getPost('deskripsi_html'),
            'questions_json' => json_encode($questions), // temporary until images processed
            'due_at' => $this->request->getPost('due_at') ?: null,
            'work_duration_minutes' => $this->request->getPost('work_duration_minutes') ?: null,
            'allow_late' => $this->request->getPost('allow_late') ? 1 : 0,
            'author_user_id' => $userId,
            'visibility' => $this->request->getPost('action') === 'publish' ? 'published' : 'draft',
            'published_at' => $this->request->getPost('action') === 'publish' ? date('Y-m-d H:i:s') : null,
        ];
        
        if (!$this->assignmentModel->insert($data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan tugas.');
        }

        $assignmentId = $this->assignmentModel->getInsertID();

        // Prepare for processing images
        $debugInfo = [];
        $debugInfo[] = '=== FILE UPLOAD PROCESS ===';
        $debugInfo[] = 'Assignment ID: '.$assignmentId;
        $debugInfo[] = 'Question count: '.count($questions);
        $debugInfo[] = 'PHP upload_max_filesize='.ini_get('upload_max_filesize').', post_max_size='.ini_get('post_max_size').', max_file_uploads='.ini_get('max_file_uploads');

        $baseDir = WRITEPATH.'uploads/classroom/questions';
        if (!is_dir($baseDir)) {
            if (@mkdir($baseDir, 0775, true)) {
                $debugInfo[] = 'Created directory: '.$baseDir;
            } else {
                $debugInfo[] = 'FAILED creating directory: '.$baseDir;
            }
        }

        $haveFiles = !empty($_FILES);
        $debugInfo[] = '$_FILES empty? '.($haveFiles?'NO':'YES');
        if ($haveFiles) {
            foreach ($_FILES as $key => $info) {
                $debugInfo[] = "FIELD '$key' structure keys: ".implode(',', array_keys($info));
            }
        }

        $allowedExt = ['jpg','jpeg','png','gif','webp'];
        $savedAny = false;

        // Process question images
        if (isset($_FILES['question_images']) && isset($_FILES['question_images']['name']) && is_array($_FILES['question_images']['name'])) {
            foreach ($_FILES['question_images']['name'] as $uid => $filename) {
                if (empty($filename)) continue;
                $tmp  = $_FILES['question_images']['tmp_name'][$uid] ?? null;
                $err  = $_FILES['question_images']['error'][$uid] ?? UPLOAD_ERR_NO_FILE;
                $size = $_FILES['question_images']['size'][$uid] ?? 0;
                $debugInfo[] = "Question image candidate uid=$uid name=$filename size=$size err=$err";
                if ($err !== UPLOAD_ERR_OK || !$tmp || !is_uploaded_file($tmp)) {
                    $debugInfo[] = "  -> skipped (error or not uploaded file)";
                    continue;
                }
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                if (!in_array($ext, $allowedExt)) { $debugInfo[] = "  -> invalid ext $ext"; continue; }
                $newName = 'q_'.$assignmentId.'_'.$uid.'_'.uniqid().'.'.$ext;
                if (move_uploaded_file($tmp, $baseDir.DIRECTORY_SEPARATOR.$newName)) {
                    $debugInfo[] = "  -> saved as $newName";
                    $savedAny = true;
                    // inject into questions array
                    foreach ($questions as &$qRef) {
                        if (($qRef['uid']??'') === $uid) { $qRef['image'] = $newName; break; }
                    }
                } else {
                    $debugInfo[] = "  -> FAILED to move_uploaded_file";
                }
            }
        } else {
            $debugInfo[] = 'No question_images field present in $_FILES';
        }

        // Process option images
        if (isset($_FILES['option_images']) && isset($_FILES['option_images']['name']) && is_array($_FILES['option_images']['name'])) {
            foreach ($_FILES['option_images']['name'] as $uid => $options) {
                if (!is_array($options)) continue;
                foreach ($options as $optKey => $filename) {
                    if (empty($filename)) continue;
                    $tmp  = $_FILES['option_images']['tmp_name'][$uid][$optKey] ?? null;
                    $err  = $_FILES['option_images']['error'][$uid][$optKey] ?? UPLOAD_ERR_NO_FILE;
                    $size = $_FILES['option_images']['size'][$uid][$optKey] ?? 0;
                    $debugInfo[] = "Option image candidate uid=$uid opt=$optKey name=$filename size=$size err=$err";
                    if ($err !== UPLOAD_ERR_OK || !$tmp || !is_uploaded_file($tmp)) { $debugInfo[]='  -> skipped'; continue; }
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    if (!in_array($ext, $allowedExt)) { $debugInfo[] = "  -> invalid ext $ext"; continue; }
                    $newName = 'opt_'.$assignmentId.'_'.$uid.'_'.$optKey.'_'.uniqid().'.'.$ext;
                    if (move_uploaded_file($tmp, $baseDir.DIRECTORY_SEPARATOR.$newName)) {
                        $debugInfo[] = "  -> saved as $newName";
                        $savedAny = true;
                        foreach ($questions as &$qRef) {
                            if (($qRef['uid']??'') === $uid && !empty($qRef['options'])) {
                                foreach ($qRef['options'] as &$opRef) {
                                    if (($opRef['key']??'') === $optKey) { $opRef['image'] = $newName; break; }
                                }
                            }
                        }
                    } else {
                        $debugInfo[] = "  -> FAILED to move_uploaded_file";
                    }
                }
            }
        } else {
            $debugInfo[] = 'No option_images field present in $_FILES';
        }

        // Save updated questions if changed
        $this->assignmentModel->update($assignmentId, ['questions_json' => json_encode($questions)]);
        $debugInfo[] = 'Updated questions_json saved. SavedAny=' . ($savedAny?'YES':'NO');

        foreach ($debugInfo as $l) log_message('info',$l);

        return redirect()->to('/classroom/assignments/'.$assignmentId)->with('info', implode("\n", $debugInfo));
    }

    public function update($id)
    {
        $role = session('role');
        $userId = session('user_id');
        if (!in_array($role,['guru','walikelas','admin'])) {
            return redirect()->back()->with('error','Tidak diizinkan');
        }

        $assignment = $this->assignmentModel->find($id);
        if (!$assignment) {
            return redirect()->to('/classroom/assignments')->with('error', 'Tugas tidak ditemukan');
        }

        if ($role !== 'admin' && $assignment['author_user_id'] != $userId) {
            return redirect()->to('/classroom/assignments')->with('error', 'Tidak diizinkan mengedit tugas ini');
        }

        $rawQuestions = $this->request->getPost('questions_json');
        $questions = $rawQuestions ? json_decode($rawQuestions, true) : [];
        if (!is_array($questions)) $questions = [];

        $data = [
            'kelas' => trim($this->request->getPost('kelas')),
            'mapel' => trim($this->request->getPost('mapel')),
            'judul' => trim($this->request->getPost('judul')),
            'deskripsi_html' => $this->request->getPost('deskripsi_html'),
            'questions_json' => json_encode($questions),
            'due_at' => $this->request->getPost('due_at') ?: null,
            'work_duration_minutes' => $this->request->getPost('work_duration_minutes') ?: null,
            'allow_late' => $this->request->getPost('allow_late') ? 1 : 0,
            'visibility' => $this->request->getPost('action') === 'publish' ? 'published' : 'draft',
            'published_at' => $this->request->getPost('action') === 'publish' ? date('Y-m-d H:i:s') : null,
        ];

        if (!$this->assignmentModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui tugas.');
        }

        return redirect()->to('/classroom/assignments')->with('success', 'Tugas berhasil diperbarui!');
    }

    public function edit($id)
    {
        $role = session('role');
        $userId = session('user_id');
        if (!in_array($role,['guru','walikelas','admin'])) {
            return redirect()->back()->with('error','Tidak diizinkan');
        }

        $assignment = $this->assignmentModel->find($id);
        if (!$assignment) {
            return redirect()->to('/classroom/assignments')->with('error', 'Tugas tidak ditemukan');
        }

        if ($role !== 'admin' && $assignment['author_user_id'] != $userId) {
            return redirect()->to('/classroom/assignments')->with('error', 'Tidak diizinkan mengedit tugas ini');
        }

        $data['assignment'] = $assignment;
        return view('classroom/assignments/form', $data);
    }

    public function delete($id)
    {
        $role = session('role');
        $userId = session('user_id');
        if (!in_array($role,['guru','walikelas','admin'])) {
            return redirect()->back()->with('error','Tidak diizinkan');
        }

        $assignment = $this->assignmentModel->find($id);
        if (!$assignment) {
            return redirect()->to('/classroom/assignments')->with('error', 'Tugas tidak ditemukan');
        }

        if ($role !== 'admin' && $assignment['author_user_id'] != $userId) {
            return redirect()->to('/classroom/assignments')->with('error', 'Tidak diizinkan menghapus tugas ini');
        }

        if (!$this->assignmentModel->delete($id)) {
            return redirect()->to('/classroom/assignments')->with('error', 'Gagal menghapus tugas.');
        }

        return redirect()->to('/classroom/assignments')->with('success', 'Tugas berhasil dihapus!');
    }

    public function questionImage($filename)
    {
        $filepath = WRITEPATH . 'uploads/classroom/questions/' . $filename;
        
        // Security: only allow specific extensions
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allowedExts) || !file_exists($filepath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        
        return $this->response->setHeader('Content-Type', mime_content_type($filepath))
                             ->setBody(file_get_contents($filepath));
    }

    public function debugQuestions($id)
    {
        $assignment = $this->assignmentModel->find($id);
        if (!$assignment) {
            return $this->response->setJSON(['error' => 'Assignment not found']);
        }
        
        $questions = json_decode($assignment['questions_json'], true);
        return $this->response->setJSON([
            'assignment_id' => $id,
            'questions' => $questions,
            'raw_json' => $assignment['questions_json']
        ]);
    }

    public function testUpload()
    {
        if ($this->request->getMethod() === 'POST') {
            $debugInfo = [
                'received_files' => count($_FILES),
                'file_keys' => array_keys($_FILES),
                'test_file_exists' => isset($_FILES['test_file']),
                'file_valid' => false,
                'file_error' => null,
                'file_size' => null,
                'php_settings' => [
                    'upload_max_filesize' => ini_get('upload_max_filesize'),
                    'post_max_size' => ini_get('post_max_size'),
                    'max_file_uploads' => ini_get('max_file_uploads')
                ]
            ];
            
            if (isset($_FILES['test_file'])) {
                $debugInfo['file_valid'] = $_FILES['test_file']['error'] === 0;
                $debugInfo['file_error'] = $_FILES['test_file']['error'];
                $debugInfo['file_size'] = $_FILES['test_file']['size'];
            }
            
            return $this->response->setJSON($debugInfo);
        }
        
        return view('classroom/assignments/test_upload');
    }

    public function show($id)
    {
        $assignment = $this->assignmentModel->find($id);
        if (!$assignment) {
            return redirect()->to('/classroom/assignments')->with('error', 'Tugas tidak ditemukan');
        }

        // Check if user is student and assignment is published
        $role = session('role');
    if ($role === 'siswa' && $assignment['visibility'] !== 'published') {
            return redirect()->to('/classroom/assignments')->with('error', 'Tugas belum dipublikasikan');
        }

        // Get submission if student
        $submission = null;
        $attempt = null;
        if ($role === 'siswa') {
            $userId = session('user_id');
            $submission = $this->submissionModel->where([
                'assignment_id' => $id,
                'siswa_user_id' => $userId
            ])->first();
            $attempt = $this->attemptModel->getActiveAttempt($id, $userId);
            // Recalculate remaining time on-the-fly based on started_at + duration if duration set
            if($attempt && $assignment['work_duration_minutes']){
                $durationSeconds = (int)$assignment['work_duration_minutes'] * 60;
                if(!empty($attempt['started_at'])){
                    $elapsed = time() - strtotime($attempt['started_at']);
                    $remaining = $durationSeconds - $elapsed;
                    if($remaining < 0) $remaining = 0;
                    // Only override if stored remaining_seconds is null OR larger (i.e., stale)
                    if($attempt['remaining_seconds'] === null || $attempt['remaining_seconds'] > $remaining){
                        $attempt['remaining_seconds'] = $remaining;
                    }
                }
            }
        }

        // Get all submissions if teacher/admin
        $submissions = [];
        if (in_array($role, ['guru', 'walikelas', 'admin'])) {
            $submissions = $this->submissionModel->where('assignment_id', $id)
                                                 ->join('users', 'users.id = classroom_submissions.siswa_user_id')
                                                 ->select('classroom_submissions.*, users.nama as student_name')
                                                 ->findAll();
        }

        $questions = json_decode($assignment['questions_json'], true) ?: [];
        // attachments (if any)
        $attachments = $this->attachmentModel->for('assignment', $id);
        $data = [
            'assignment' => $assignment,
            'questions' => $questions,
            'submission' => $submission,
            'submissions' => $submissions,
            'attachments' => $attachments,
            'role' => $role,
            'attempt' => $attempt,
            'alreadySubmitted' => ($role==='siswa' && $submission && !empty($submission['submitted_at'])),
            'asSiswa' => $this->request->getGet('as') === 'siswa' // simple preview flag
        ];

        return view('classroom/assignments/show', $data);
    }

    // Start or resume attempt (AJAX)
    public function startAttempt($id)
    {
    // Route already restricts to POST; skip extra method guard (fix for method detection issues)
        $role = session('role');
        if ($role !== 'siswa') return $this->response->setJSON(['error'=>'Hanya siswa']);
        $assignment = $this->assignmentModel->find($id);
        if(!$assignment || $assignment['visibility']!=='published') return $this->response->setJSON(['error'=>'Tugas tidak tersedia']);
        $userId = session('user_id');
        // Block if already submitted
        $existingSubmission = $this->submissionModel->where('assignment_id',$id)->where('siswa_user_id',$userId)->where('submitted_at IS NOT', null, false)->first();
        if($existingSubmission){
            return $this->response->setJSON(['error'=>'Kamu telah menyelesaikan tugas ini']);
        }
        $attempt = $this->attemptModel->getActiveAttempt($id,$userId);
        $duration = (int)($assignment['work_duration_minutes'] ?? 0);
        if(!$attempt){
            $remaining = $duration>0 ? $duration*60 : null;
            $startedAt = date('Y-m-d H:i:s');
            $this->attemptModel->insert([
                'assignment_id'=>$id,
                'user_id'=>$userId,
                'started_at'=>$startedAt,
                'remaining_seconds'=>$remaining,
                'answers_json'=>null,
                'status'=>'in_progress'
            ]);
            $attempt = $this->attemptModel->find($this->attemptModel->getInsertID());
        } else {
            // Recompute remaining if duration exists to prevent reset exploit
            if($duration>0 && !empty($attempt['started_at'])){
                $elapsed = time() - strtotime($attempt['started_at']);
                $calcRemaining = ($duration*60) - $elapsed;
                if($calcRemaining < 0) $calcRemaining = 0;
                $attempt['remaining_seconds'] = $calcRemaining;
            }
        }
        return $this->response->setJSON([
            'success'=>true,
            'remaining_seconds'=>$attempt['remaining_seconds'],
            'answers'=>json_decode($attempt['answers_json']??'[]',true)
        ]);
    }

    // Save answers + remaining time periodically
    public function ajaxSaveAnswers($id)
    {
    // Route restricts to POST
        $role = session('role'); if($role!=='siswa') return $this->response->setJSON(['error'=>'Hanya siswa']);
        $assignment = $this->assignmentModel->find($id); if(!$assignment) return $this->response->setJSON(['error'=>'Tugas tidak ditemukan']);
        $payload = $this->request->getJSON(true) ?: [];
        $answers = $payload['answers'] ?? [];
        $remaining = isset($payload['remaining_seconds']) ? (int)$payload['remaining_seconds'] : null;
        $userId = session('user_id');
        $attempt = $this->attemptModel->getActiveAttempt($id,$userId);
        if(!$attempt){
            return $this->response->setJSON(['error'=>'Belum mulai']);
        }
        // Update attempt
        $upd = [
            'answers_json'=>json_encode($answers),
            'updated_at'=>date('Y-m-d H:i:s')
        ];
        if($remaining!==null) $upd['remaining_seconds']=$remaining;
        // Auto mark finished if remaining <=0
        if($remaining===0){
            $upd['status']='expired';
            $upd['ended_at']=date('Y-m-d H:i:s');
        }
        $this->attemptModel->update($attempt['id'],$upd);
        return $this->response->setJSON(['success'=>true]);
    }

    public function publish($id)
    {
        $role = session('role');
        $userId = session('user_id');
        if (!in_array($role,['guru','walikelas','admin'])) {
            return redirect()->back()->with('error','Tidak diizinkan');
        }

        $assignment = $this->assignmentModel->find($id);
        if (!$assignment) {
            return redirect()->to('/classroom/assignments')->with('error', 'Tugas tidak ditemukan');
        }

        if ($role !== 'admin' && $assignment['author_user_id'] != $userId) {
            return redirect()->to('/classroom/assignments')->with('error', 'Tidak diizinkan mempublikasikan tugas ini');
        }

        $data = [
            'visibility' => 'published',
            'published_at' => date('Y-m-d H:i:s')
        ];

        if (!$this->assignmentModel->update($id, $data)) {
            return redirect()->back()->with('error', 'Gagal mempublikasikan tugas.');
        }

        return redirect()->to('/classroom/assignments')->with('success', 'Tugas berhasil dipublikasikan!');
    }

    // Final submit attempt -> create/update submission, lock attempt
    public function submitAttempt($id)
    {
        $role = session('role'); if($role!=='siswa') return $this->response->setJSON(['error'=>'Hanya siswa']);
        $assignment = $this->assignmentModel->find($id); if(!$assignment) return $this->response->setJSON(['error'=>'Tugas tidak ditemukan']);
        $userId = session('user_id');
        $attempt = $this->attemptModel->getActiveAttempt($id,$userId);
        if(!$attempt){ return $this->response->setJSON(['error'=>'Attempt tidak aktif']); }
        // Decode answers
        $answers = json_decode($attempt['answers_json'] ?? '[]', true) ?: [];
        // Build / update submission
        $existing = $this->submissionModel->getUserSubmission($id, $userId);
        $data = [
            'assignment_id'=>$id,
            'siswa_user_id'=>$userId,
            'content_text'=>null,
            'content_html'=>json_encode(['answers'=>$answers]),
            'submitted_at'=>date('Y-m-d H:i:s'),
            'late'=> (!empty($assignment['due_at']) && time()>strtotime($assignment['due_at'])) ? 1:0
        ];
        if($existing){ $this->submissionModel->update($existing['id'],$data); $submissionId=$existing['id']; }
        else { $this->submissionModel->insert($data); $submissionId=$this->submissionModel->getInsertID(); }
        // Mark attempt ended
        $this->attemptModel->update($attempt['id'], [ 'status'=>'finished', 'ended_at'=>date('Y-m-d H:i:s') ]);
        return $this->response->setJSON(['success'=>true,'submission_id'=>$submissionId]);
    }
}
