<?php
namespace App\Models\Classroom;

use CodeIgniter\Model;

class AssignmentModel extends Model
{
    protected $table = 'classroom_assignments';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'kelas','mapel','judul','slug','deskripsi_html','questions_json','due_at','work_duration_minutes','allow_late','author_user_id','visibility','published_at'
    ];
    protected $useTimestamps = true;
    protected $validationRules = [
        'kelas' => 'required|min_length[1]|max_length[50]',
    'mapel' => 'permit_empty|max_length[120]',
        'judul' => 'required|min_length[3]|max_length[200]',
        'slug'  => 'required|min_length[3]|max_length[220]',
    ];

    public function forListing(array $filters, $role, $userId)
    {
        $builder = $this->builder();
        if (!empty($filters['kelas'])) {
            $builder->where('kelas', $filters['kelas']);
        }
        if ($role === 'siswa') {
            $builder->where('visibility', 'published');
        }
        if ($role === 'guru' || $role === 'walikelas') {
            $builder->groupStart()
                ->where('author_user_id', $userId)
                ->orWhere('visibility', 'published')
            ->groupEnd();
        }
    // Order: due_at not null first (earliest), then those without due_at, then newest created
    // Use CASE for portability (previous raw 'due_at IS NULL ASC' caused syntax issue on some MariaDB versions)
    $builder->orderBy('(CASE WHEN due_at IS NULL THEN 1 ELSE 0 END) ASC', false)
        ->orderBy('due_at', 'ASC')
        ->orderBy('created_at', 'DESC');
        return $builder->get()->getResultArray();
    }

    public function publish($id)
    {
        return $this->update($id, [
            'visibility' => 'published',
            'published_at' => date('Y-m-d H:i:s')
        ]);
    }
}
