<?php
namespace App\Models\Classroom;

use CodeIgniter\Model;

class LessonModel extends Model
{
    protected $table = 'classroom_lessons';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'kelas','mapel','judul','slug','konten','video_url','author_user_id','visibility','published_at','view_count'
    ];
    protected $useTimestamps = true;

    protected $validationRules = [
        'kelas' => 'required|min_length[1]|max_length[50]',
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
            // show own drafts plus published of that kelas
            $builder->groupStart()
                ->where('author_user_id', $userId)
                ->orWhere('visibility', 'published')
            ->groupEnd();
        }
        $builder->orderBy('published_at DESC, created_at DESC');
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
