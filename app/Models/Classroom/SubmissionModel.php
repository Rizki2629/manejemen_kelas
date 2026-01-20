<?php
namespace App\Models\Classroom;

use CodeIgniter\Model;

class SubmissionModel extends Model
{
    protected $table = 'classroom_submissions';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'assignment_id','siswa_user_id','content_text','content_html','submitted_at','late','graded_at','grader_user_id','score','feedback_text'
    ];
    protected $useTimestamps = true;

    protected $validationRules = [
        'assignment_id' => 'required|integer',
        'siswa_user_id' => 'required|integer',
    ];

    public function getByAssignment($assignmentId)
    {
        return $this->where('assignment_id',$assignmentId)->orderBy('submitted_at ASC')->findAll();
    }

    public function getUserSubmission($assignmentId, $userId)
    {
        return $this->where('assignment_id',$assignmentId)->where('siswa_user_id',$userId)->first();
    }

    public function grade($id, $score, $feedback, $graderId)
    {
        return $this->update($id, [
            'score' => $score,
            'feedback_text' => $feedback,
            'grader_user_id' => $graderId,
            'graded_at' => date('Y-m-d H:i:s')
        ]);
    }
}
