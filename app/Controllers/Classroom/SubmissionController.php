<?php
namespace App\Controllers\Classroom;

use App\Controllers\BaseController;
use App\Models\Classroom\AssignmentModel;
use App\Models\Classroom\SubmissionModel;
use App\Models\Classroom\AttachmentModel;
use App\Models\Classroom\AssignmentAttemptModel;

class SubmissionController extends BaseController
{
    protected AssignmentModel $assignmentModel;
    protected SubmissionModel $submissionModel;
    protected AttachmentModel $attachmentModel;
    protected AssignmentAttemptModel $attemptModel;

    public function __construct()
    {
        $this->assignmentModel = new AssignmentModel();
    $this->submissionModel = new SubmissionModel();
    $this->attachmentModel = new AttachmentModel();
    $this->attemptModel = new AssignmentAttemptModel();
    }

    // Student form
    public function create($assignmentId)
    {
        $role = session('role');
        if ($role !== 'siswa') return redirect()->back()->with('error','Hanya siswa');
        $assignment = $this->assignmentModel->find($assignmentId);
        if (!$assignment || $assignment['visibility']!=='published') return redirect()->back()->with('error','Tugas tidak tersedia');
        $existing = $this->submissionModel->getUserSubmission($assignmentId, session('user_id'));
    $attachments = $existing ? $this->attachmentModel->for('submission',(int)$existing['id']) : [];
    return view('classroom/submissions/form',[ 'assignment'=>$assignment, 'submission'=>$existing, 'attachments'=>$attachments ]);
    }

    // Store or update submission
    public function store($assignmentId)
    {
        $role = session('role');
        if ($role !== 'siswa') return redirect()->back()->with('error','Hanya siswa');
        $assignment = $this->assignmentModel->find($assignmentId);
        if (!$assignment || $assignment['visibility']!=='published') return redirect()->back()->with('error','Tugas tidak tersedia');
        $userId = session('user_id');
        $late = 0;
        if (!empty($assignment['due_at'])) {
            $late = (strtotime('now') > strtotime($assignment['due_at'])) ? 1 : 0;
        }
        $data = [
            'assignment_id' => $assignmentId,
            'siswa_user_id' => $userId,
            'content_text' => trim($this->request->getPost('content_text')),
            'content_html' => $this->request->getPost('content_html') ?: null,
            'submitted_at' => date('Y-m-d H:i:s'),
            'late' => $late,
        ];
        $existing = $this->submissionModel->getUserSubmission($assignmentId, $userId);
        if ($existing) {
            $this->submissionModel->update($existing['id'], $data);
            $submissionId = $existing['id'];
        } else {
            $this->submissionModel->insert($data);
            $submissionId = $this->submissionModel->getInsertID();
        }
        // Handle attachments (single or multiple)
        $files = $this->request->getFiles();
        if (!empty($files['attachments'])) {
            foreach ($files['attachments'] as $file) {
                if (!$file->isValid()) continue;
                if ($file->getSize() > 5*1024*1024) continue;
                $ext = strtolower($file->getExtension());
                $allowed = ['pdf','jpg','jpeg','png','doc','docx','ppt','pptx','xls','xlsx','txt','zip'];
                if (!in_array($ext,$allowed)) continue;
                $newName = 'S'.$submissionId.'_'.session('user_id').'_'.time().'_'.random_string('alnum',8).'.'.$ext;
                $file->move(WRITEPATH.'uploads/classroom',$newName);
                $this->attachmentModel->insert([
                    'context_type' => 'submission',
                    'context_id' => $submissionId,
                    'original_name' => $file->getClientName(),
                    'stored_name' => $newName,
                    'mime_type' => $file->getMimeType(),
                    'size_bytes' => $file->getSize(),
                    'uploaded_by' => $userId,
                ]);
            }
        }
        return redirect()->to('/classroom/assignments/'.$assignmentId.'/submission')->with('success','Tugas dikirim');
    }

    // AJAX save quick answers (JSON from assignment show page)
    public function ajaxSaveAnswers($assignmentId)
    {
        if(!$this->request->isAJAX()) return $this->response->setStatusCode(405)->setJSON(['error'=>'AJAX only']);
        if(session('role')!=='siswa') return $this->response->setStatusCode(403)->setJSON(['error'=>'Forbidden']);
        $assignment = $this->assignmentModel->find($assignmentId);
        if(!$assignment || $assignment['visibility']!=='published') return $this->response->setStatusCode(404)->setJSON(['error'=>'Tugas tidak tersedia']);
        $body = $this->request->getJSON(true) ?: [];
        $answers = $body['answers'] ?? [];
        if(!is_array($answers)) $answers = [];
        $userId = session('user_id');
        $existing = $this->submissionModel->getUserSubmission($assignmentId, $userId);
        $late = 0; if(!empty($assignment['due_at']) && strtotime('now')>strtotime($assignment['due_at'])) $late=1;
        // store answers as JSON in content_html for now (could use separate field later)
        $data = [
            'assignment_id'=>$assignmentId,
            'siswa_user_id'=>$userId,
            'content_text'=>null,
            'content_html'=>json_encode(['answers'=>$answers]),
            'submitted_at'=>date('Y-m-d H:i:s'),
            'late'=>$late,
        ];
        if($existing){
            $this->submissionModel->update($existing['id'],$data);
            $id=$existing['id'];
        } else {
            $this->submissionModel->insert($data); $id=$this->submissionModel->getInsertID();
        }
        return $this->response->setJSON(['success'=>true,'submission_id'=>$id]);
    }

    // Student view own submission
    public function showOwn($assignmentId)
    {
        $role = session('role');
        if ($role !== 'siswa') return redirect()->back()->with('error','Hanya siswa');
        $assignment = $this->assignmentModel->find($assignmentId);
        if (!$assignment) return redirect()->back()->with('error','Tugas tidak ditemukan');
        $submission = $this->submissionModel->getUserSubmission($assignmentId, session('user_id'));
    $attachments = $submission ? $this->attachmentModel->for('submission',(int)$submission['id']) : [];
    return view('classroom/submissions/show',[ 'assignment'=>$assignment, 'submission'=>$submission, 'attachments'=>$attachments ]);
    }

    // Teacher list submissions
    public function listForAssignment($assignmentId)
    {
        $role = session('role');
        if (!in_array($role,['guru','walikelas','admin'])) return redirect()->back()->with('error','Tidak diizinkan');
        $assignment = $this->assignmentModel->find($assignmentId);
        if (!$assignment) return redirect()->back()->with('error','Tugas tidak ditemukan');
        // Enrich with siswa nama + nisn (username is NISN for student user) try join siswa or tb_siswa
        $db = db_connect();
        $subs = $db->table('classroom_submissions cs')
            ->select('cs.*, u.nama as user_nama, u.username as user_username, s.nama as siswa_nama, s.nisn as siswa_nisn, tbs.nama as tbs_nama, tbs.nisn as tbs_nisn')
            ->join('users u','u.id=cs.siswa_user_id','left')
            ->join('siswa s','s.nisn = u.username OR s.nis = u.username','left')
            ->join('tb_siswa tbs','tbs.nisn = u.username','left')
            ->where('cs.assignment_id',$assignmentId)
            ->where('cs.deleted_at IS NULL')
            ->orderBy('cs.submitted_at','ASC')
            ->get()->getResultArray();
        // Attach counts
        $attachmentCounts = [];
        foreach ($subs as $s) {
            $attachmentCounts[$s['id']] = $this->attachmentModel->where('context_type','submission')->where('context_id',$s['id'])->countAllResults();
        }
    // Stats
    $submittedCount = 0; $scores=[];
    foreach($subs as $s){ if(!empty($s['submitted_at'])) $submittedCount++; if($s['score']!==null) $scores[] = (float)$s['score']; }
    $avgScore = $scores ? round(array_sum($scores)/count($scores),2) : null;
    return view('classroom/submissions/list',[ 'assignment'=>$assignment, 'submissions'=>$subs, 'role'=>$role, 'attachmentCounts'=>$attachmentCounts, 'submittedCount'=>$submittedCount, 'avgScore'=>$avgScore ]);
    }

    public function gradeForm($submissionId)
    {
        $role = session('role');
        if (!in_array($role,['guru','walikelas','admin'])) return redirect()->back()->with('error','Tidak diizinkan');
        $submission = $this->submissionModel->find($submissionId);
        if (!$submission) return redirect()->back()->with('error','Submission tidak ditemukan');
        $assignment = $this->assignmentModel->find($submission['assignment_id']);
        return view('classroom/submissions/grade',[ 'submission'=>$submission, 'assignment'=>$assignment ]);
    }

    public function grade($submissionId)
    {
        $role = session('role');
        if (!in_array($role,['guru','walikelas','admin'])) return redirect()->back()->with('error','Tidak diizinkan');
        $submission = $this->submissionModel->find($submissionId);
        if (!$submission) return redirect()->back()->with('error','Submission tidak ditemukan');
        $score = $this->request->getPost('score');
        $feedback = $this->request->getPost('feedback_text');
        $this->submissionModel->grade($submissionId, $score, $feedback, session('user_id'));
        return redirect()->to('/classroom/assignments/'.$submission['assignment_id'].'/submissions')->with('success','Nilai disimpan');
    }

    // Reset attempt & submission so student can redo
    public function resetAttempt($assignmentId, $userId)
    {
        $role = session('role');
        if (!in_array($role,['guru','walikelas','admin'])) return redirect()->back()->with('error','Tidak diizinkan');
        $assignment = $this->assignmentModel->find($assignmentId);
        if(!$assignment) return redirect()->back()->with('error','Tugas tidak ditemukan');
        $uid = (int)$userId;
        // HARD RESET: remove submission row + attachments + all attempts for this assignment/user
        $db = db_connect();
        $db->transStart();
        $submission = $this->submissionModel
            ->where('assignment_id',$assignmentId)
            ->where('siswa_user_id',$uid)
            ->first();
        // Delete ALL submissions (could be multiple legacy rows)
        $allSubs = $this->submissionModel->where('assignment_id',$assignmentId)->where('siswa_user_id',$uid)->findAll();
        foreach($allSubs as $subRow){
            $atts = $this->attachmentModel->where('context_type','submission')->where('context_id',$subRow['id'])->findAll();
            foreach($atts as $att){
                $filePath = WRITEPATH.'uploads/classroom/'.$att['stored_name'];
                if(is_file($filePath)) @unlink($filePath);
                $this->attachmentModel->delete($att['id']);
            }
        }
        if(!empty($allSubs)){
            $db->table('classroom_submissions')->where('assignment_id',$assignmentId)->where('siswa_user_id',$uid)->delete();
        }
        // Delete all attempts rows for clean slate
        $db->table('classroom_assignment_attempts')
            ->where('assignment_id',$assignmentId)
            ->where('user_id',$uid)
            ->delete();
        $db->transComplete();
        if($db->transStatus()===false){
            return redirect()->to('/classroom/assignments/'.$assignmentId.'/submissions')->with('error','Gagal reset total siswa #'.$uid);
        }
        return redirect()->to('/classroom/assignments/'.$assignmentId.'/submissions')->with('success','Reset total: data latihan siswa #'.$uid.' dikosongkan');
    }

    // Auto-grade multiple choice (type=kuis) questions for a student
    public function autoGrade($assignmentId, $userId)
    {
        $role = session('role');
        if (!in_array($role,['guru','walikelas','admin'])) return redirect()->back()->with('error','Tidak diizinkan');
        $assignment = $this->assignmentModel->find($assignmentId);
        if(!$assignment) return redirect()->back()->with('error','Tugas tidak ditemukan');
        $uid = (int)$userId;
        $questions = json_decode($assignment['questions_json'] ?? '[]', true) ?: [];
        // Build index of MC questions with answer key
        $mc = [];
        foreach($questions as $q){
            if(($q['type'] ?? '')==='kuis' && isset($q['answer']) && ($q['answer']!=='') ){
                $mc[] = $q;
            }
        }
        if(empty($mc)){
            return redirect()->to('/classroom/assignments/'.$assignmentId.'/submissions')->with('error','Tidak ada soal pilihan ganda dengan kunci jawaban');
        }
        // Grab latest attempt (in_progress or expired) or any
        $attempt = $this->attemptModel->where('assignment_id',$assignmentId)->where('user_id',$uid)->orderBy('id','DESC')->first();
        if(!$attempt || empty($attempt['answers_json'])){
            return redirect()->to('/classroom/assignments/'.$assignmentId.'/submissions')->with('error','Tidak ada jawaban attempt siswa');
        }
        $answers = json_decode($attempt['answers_json'], true) ?: [];
        $correct = 0; $total = count($mc);
        foreach($mc as $q){
            $uidQ = $q['uid'] ?? null; if(!$uidQ) continue;
            $studentAns = $answers['q'.$uidQ] ?? null;
            if($studentAns !== null && $studentAns === ($q['answer'] ?? null)) $correct++;
        }
        $score = $total>0 ? round(($correct/$total)*100,2) : 0;
        // Ensure submission row exists
        $submission = $this->submissionModel->getUserSubmission($assignmentId, $uid);
        if(!$submission){
            $this->submissionModel->insert([
                'assignment_id'=>$assignmentId,
                'siswa_user_id'=>$uid,
                'content_text'=>null,
                'content_html'=>json_encode(['answers'=>$answers]),
                'submitted_at'=>date('Y-m-d H:i:s'),
                'late'=>0,
            ]);
            $submissionId = $this->submissionModel->getInsertID();
        } else {
            $submissionId = $submission['id'];
        }
        $this->submissionModel->grade($submissionId, $score, 'Auto-grade PG ('.$correct.'/'.$total.')', session('user_id'));
        return redirect()->to('/classroom/assignments/'.$assignmentId.'/submissions')->with('success','Nilai otomatis dihitung: '.$score);
    }

    // Hard delete a single submission (without touching attempts) for manual cleanup
    public function deleteSubmission($assignmentId, $submissionId)
    {
        $role = session('role');
        if (!in_array($role,['guru','walikelas','admin'])) return redirect()->back()->with('error','Tidak diizinkan');
        $assignment = $this->assignmentModel->find($assignmentId);
        if(!$assignment) return redirect()->back()->with('error','Tugas tidak ditemukan');
        $submission = $this->submissionModel->find($submissionId);
        if(!$submission || (int)$submission['assignment_id'] !== (int)$assignmentId){
            return redirect()->to('/classroom/assignments/'.$assignmentId.'/submissions')->with('error','Submission tidak ditemukan');
        }
        // Remove attachments + file
        $atts = $this->attachmentModel->where('context_type','submission')->where('context_id',$submissionId)->findAll();
        foreach($atts as $att){
            $filePath = WRITEPATH.'uploads/classroom/'.$att['stored_name'];
            if(is_file($filePath)) @unlink($filePath);
            $this->attachmentModel->delete($att['id']);
        }
        // Hard delete ignoring soft delete to ensure row gone
        $db = db_connect();
        $db->table('classroom_submissions')->where('id',$submissionId)->delete();
        return redirect()->to('/classroom/assignments/'.$assignmentId.'/submissions')->with('success','Submission dihapus permanen');
    }
}
