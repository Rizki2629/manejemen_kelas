<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'username', 
        'password', 
        'nama', 
        'email', 
        'role', 
        'walikelas_id', 
        'is_active', 
        'last_login'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username,id,{id}]',
        'password' => 'required|min_length[6]',
        'nama' => 'required|min_length[3]|max_length[100]',
        'email' => 'permit_empty|valid_email|is_unique[users.email,id,{id}]',
    'role' => 'required|in_list[admin,walikelas,guru,siswa]',
        'is_active' => 'in_list[0,1]'
    ];

    protected $validationMessages = [
        'username' => [
            'required' => 'Username harus diisi',
            'min_length' => 'Username minimal 3 karakter',
            'max_length' => 'Username maksimal 50 karakter',
            'is_unique' => 'Username sudah digunakan'
        ],
        'password' => [
            'required' => 'Password harus diisi',
            'min_length' => 'Password minimal 6 karakter'
        ],
        'nama' => [
            'required' => 'Nama harus diisi',
            'min_length' => 'Nama minimal 3 karakter',
            'max_length' => 'Nama maksimal 100 karakter'
        ],
        'email' => [
            'valid_email' => 'Email tidak valid',
            'is_unique' => 'Email sudah digunakan'
        ],
        'role' => [
            'required' => 'Role harus dipilih',
            'in_list' => 'Role tidak valid'
        ]
    ];

    /**
     * Verify user credentials and update last login.
     *
     * @param string $username
     * @param string $password
     * @return array|null User data if valid, null otherwise
     */
    public function authenticate(string $username, string $password): ?array
    {
        $user = $this->where('username', $username)
                     ->where('is_active', 1)
                     ->first();

        if ($user && password_verify($password, $user['password'])) {
            // Update last login
            $this->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);
            
            return $user;
        }

        return null;
    }

    /**
     * Get user with walikelas information (for walikelas role).
     *
     * @param int $userId
     * @return array|null
     */
    public function getUserWithWalikelas(int $userId): ?array
    {
        return $this->select('users.*, walikelas.kelas, walikelas.nip as walikelas_nip')
                    ->join('walikelas', 'users.walikelas_id = walikelas.id', 'left')
                    ->where('users.id', $userId)
                    ->first();
    }

    /**
     * Get all users by role.
     *
     * @param string $role
     * @return array
     */
    public function getUsersByRole(string $role): array
    {
        return $this->where('role', $role)
                    ->where('is_active', 1)
                    ->findAll();
    }

    /**
     * Get walikelas users with their class information.
     *
     * @return array
     */
    public function getWalikelasUsers(): array
    {
        return $this->select('users.*, walikelas.kelas, walikelas.nip as walikelas_nip')
                    ->join('walikelas', 'users.walikelas_id = walikelas.id')
                    ->where('users.role', 'walikelas')
                    ->where('users.is_active', 1)
                    ->findAll();
    }
}
