<?php
namespace App\Models\Classroom;

use CodeIgniter\Model;

class AssignmentAttemptModel extends Model
{
    protected $table = 'classroom_assignment_attempts';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'assignment_id','user_id','started_at','ended_at','remaining_seconds','answers_json','status'
    ];

    public function getActiveAttempt(int $assignmentId, int $userId)
    {
        return $this->where('assignment_id',$assignmentId)
            ->where('user_id',$userId)
            ->whereIn('status',['in_progress'])
            ->orderBy('id','DESC')->first();
    }
}
?>
