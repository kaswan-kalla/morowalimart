<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model untuk tabel users
 */
class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'name',
        'email',
        'password',
        'phone',
        'photo',
        'role',
        'is_active',
        'reset_token',
        'reset_expires',
        'id_lokasi'
    ];
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $validationRules  = [
        'name'     => 'required|min_length[3]|max_length[100]',
        'email'    => 'required|valid_email|max_length[100]',
        'phone'    => 'permit_empty|max_length[20]',
    ];

    protected $validationMessages = [
        'name'  => ['required' => 'Nama wajib diisi'],
        'email' => [
            'required'    => 'Email wajib diisi',
            'valid_email' => 'Format email tidak valid'
        ],
    ];

    /**
     * Cari user berdasarkan email
     */
    public function findByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Cari user berdasarkan reset token
     */
    public function findByResetToken(string $token)
    {
        return $this->where('reset_token', $token)
            ->where('reset_expires >', date('Y-m-d H:i:s'))
            ->first();
    }

    /**
     * Hitung total user per role
     */
    public function countByRole(string $role = null)
    {
        $builder = $this->builder();
        if ($role) {
            $builder->where('role', $role);
        }
        return $builder->countAllResults();
    }
}
