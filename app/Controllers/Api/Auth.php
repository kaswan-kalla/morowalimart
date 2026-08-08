<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\UserModel;

/**
 * API Auth - login/logout petugas outlet (token bearer)
 */
class Auth extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->ensureSchema();
    }

    /**
     * Pastikan tabel token & kolom id_lokasi tersedia (tanpa migration)
     */
    private function ensureSchema()
    {
        $db = \Config\Database::connect();
        $db->query("CREATE TABLE IF NOT EXISTS api_tokens (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            token VARCHAR(64) NOT NULL,
            expires_at DATETIME NULL,
            last_used_at DATETIME NULL,
            created_at DATETIME NULL,
            UNIQUE KEY token (token)
        )");

        $exists = $db->query("SELECT COUNT(*) as c FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'id_lokasi'")->getRow()->c;
        if (!$exists) {
            $db->query("ALTER TABLE users ADD COLUMN id_lokasi INT NULL AFTER role");
        }
    }

    /**
     * POST /api/v1/auth/login
     * Body: {"email": "...", "password": "..."}
     */
    public function login()
    {
        $json = $this->request->getJSON(true);
        if (!$json) {
            $json = $this->request->getPost();
        }

        $email    = trim($json['email'] ?? '');
        $password = $json['password'] ?? '';

        if (!$email || !$password) {
            return $this->response->setJSON(['status' => false, 'message' => 'Email dan password wajib diisi']);
        }

        $user = $this->userModel->findByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            return $this->response->setJSON(['status' => false, 'message' => 'Email atau password salah']);
        }

        if (!$user['is_active']) {
            return $this->response->setJSON(['status' => false, 'message' => 'Akun Anda telah dinonaktifkan']);
        }

        if ($user['role'] !== 'petugas') {
            return $this->response->setJSON(['status' => false, 'message' => 'Akun bukan petugas outlet']);
        }

        if (empty($user['id_lokasi'])) {
            return $this->response->setJSON(['status' => false, 'message' => 'Akun belum terhubung ke outlet. Hubungi admin']);
        }

        $db = \Config\Database::connect();
        $lokasi = $db->query("SELECT nama_lokasi FROM lokasi WHERE id = ? AND deleted_at IS NULL", [$user['id_lokasi']])->getRow();

        // Hapus token lama, buat token baru (berlaku 30 hari)
        $db->query("DELETE FROM api_tokens WHERE user_id = ?", [$user['id']]);
        $token      = bin2hex(random_bytes(32));
        $expiresAt  = date('Y-m-d H:i:s', strtotime('+30 days'));
        $db->query(
            "INSERT INTO api_tokens (user_id, token, expires_at, created_at) VALUES (?, ?, ?, NOW())",
            [$user['id'], $token, $expiresAt]
        );

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Login berhasil',
            'data'    => [
                'token'      => $token,
                'expires_at' => $expiresAt,
                'user'       => [
                    'id'        => (int) $user['id'],
                    'name'      => $user['name'],
                    'email'     => $user['email'],
                    'id_lokasi' => (int) $user['id_lokasi'],
                    'lokasi'    => $lokasi ? $lokasi->nama_lokasi : '',
                ],
            ],
        ]);
    }

    /**
     * POST /api/v1/auth/logout (Bearer token) - hapus token
     */
    public function logout()
    {
        $user = \App\Libraries\ApiContext::user();
        if ($user) {
            $db = \Config\Database::connect();
            $db->query("DELETE FROM api_tokens WHERE user_id = ?", [$user['id']]);
        }
        return $this->response->setJSON(['status' => true, 'message' => 'Logout berhasil']);
    }
}
