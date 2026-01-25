<?php
namespace App\Models\Classroom;

use CodeIgniter\Model;

class AttachmentModel extends Model
{
    protected $table = 'classroom_attachments';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'context_type','context_id','original_name','stored_name','mime_type','size_bytes','uploaded_by'
    ];
    protected $useTimestamps = true;

    public function for(string $type, int $id): array
    {
        return $this->where('context_type',$type)->where('context_id',$id)->orderBy('created_at','ASC')->findAll();
    }
}
?>