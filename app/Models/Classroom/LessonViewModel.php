<?php
namespace App\Models\Classroom;

use CodeIgniter\Model;

class LessonViewModel extends Model
{
    protected $table = 'classroom_lesson_views';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['lesson_id','user_id','viewed_at','created_at','updated_at'];
    protected $useTimestamps = true; // manages created_at & updated_at
}
