<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nama',
        'username', 
        'email',
        'password',
        'role',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'nama' => 'required|min_length[3]|max_length[100]',
        'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username,id,{id}]',
        'email' => 'required|valid_email|max_length[100]|is_unique[users.email,id,{id}]',
        'password' => 'permit_empty|min_length[6]',
        'role' => 'required|in_list[admin,walikelas]'
    ];

    protected $validationMessages = [
        'nama' => [
            'required' => 'Nama harus diisi',
            'min_length' => 'Nama minimal 3 karakter',
            'max_length' => 'Nama maksimal 100 karakter'
        ],
        'username' => [
            'required' => 'Username harus diisi',
            'min_length' => 'Username minimal 3 karakter',
            'max_length' => 'Username maksimal 50 karakter',
            'is_unique' => 'Username sudah digunakan'
        ],
        'email' => [
            'required' => 'Email harus diisi',
            'valid_email' => 'Format email tidak valid',
            'max_length' => 'Email maksimal 100 karakter',
            'is_unique' => 'Email sudah digunakan'
        ],
        'password' => [
            'min_length' => 'Password minimal 6 karakter'
        ],
        'role' => [
            'required' => 'Role harus dipilih',
            'in_list' => 'Role tidak valid'
        ]
    ];

    /**
     * Get admin by username
     */
    public function getByUsername($username)
    {
        return $this->where('username', $username)->first();
    }

    /**
     * Get admin by email
     */
    public function getByEmail($email)
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Check if username exists (excluding specific ID)
     */
    public function isUsernameExists($username, $excludeId = null)
    {
        $builder = $this->where('username', $username);
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Check if email exists (excluding specific ID)
     */
    public function isEmailExists($email, $excludeId = null)
    {
        $builder = $this->where('email', $email);
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Get admins only (role = admin)
     */
    public function getAdmins()
    {
        return $this->where('role', 'admin')->findAll();
    }

    /**
     * Get walikelas only (role = walikelas)
     */
    public function getWalikelas()
    {
        return $this->where('role', 'walikelas')->findAll();
    }

    /**
     * Update password
     */
    public function updatePassword($id, $newPassword)
    {
        return $this->update($id, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Verify password
     */
    public function verifyPassword($userId, $password)
    {
        $user = $this->find($userId);
        
        if (!$user) {
            return false;
        }
        
        return password_verify($password, $user['password']);
    }
}
