<?php
namespace App\Models;

use CodeIgniter\Model;

class SubjectModel extends Model
{
    protected $table = 'subjects';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
    protected $allowedFields = ['name','grades'];

    public function listAll()
    {
        return $this->orderBy('name','ASC')->findAll();
    }

    public function createOrUpdate(string $name, array $grades)
    {
        $grades = array_values(array_unique(array_filter($grades, fn($g)=>in_array($g,[1,2,3,4,5,6]))));
        sort($grades);
        $data = [ 'name'=>$name, 'grades'=> implode(',', $grades) ];
        $existing = $this->where('name',$name)->first();
        if($existing){
            $this->update($existing['id'],$data);
            return $existing['id'];
        }
        return $this->insert($data, true);
    }
}
