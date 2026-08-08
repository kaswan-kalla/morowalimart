<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filter API - validasi Bearer token petugas outlet
 * Token disimpan di tabel api_tokens (dibuat otomatis bila belum ada)
 */
class ApiAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
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

        if (!$request instanceof \CodeIgniter\HTTP\IncomingRequest) {
            return $this->deny('Request tidak valid');
        }

        $header = $request->getHeaderLine('Authorization');
        $token  = '';
        if (preg_match('/Bearer\s+(.+)/i', $header, $m)) {
            $token = trim($m[1]);
        }

        if (!$token) {
            return $this->deny('Token tidak ditemukan. Kirim header Authorization: Bearer <token>');
        }

        $row = $db->query(
            "SELECT u.id, u.name, u.email, u.id_lokasi, l.nama_lokasi
             FROM api_tokens t
             JOIN users u ON u.id = t.user_id AND u.deleted_at IS NULL AND u.is_active = 1
             LEFT JOIN lokasi l ON l.id = u.id_lokasi
             WHERE t.token = ? AND (t.expires_at IS NULL OR t.expires_at > NOW())",
            [$token]
        )->getRow();

        if (!$row) {
            return $this->deny('Token tidak valid atau sudah kadaluarsa');
        }

        if (!$row->id_lokasi) {
            return $this->deny('Akun belum terhubung ke outlet');
        }

        $db->query("UPDATE api_tokens SET last_used_at = NOW() WHERE token = ?", [$token]);

        // Data user tersedia di controller via ApiContext::user()
        \App\Libraries\ApiContext::setUser([
            'id'         => (int) $row->id,
            'name'       => $row->name,
            'email'      => $row->email,
            'id_lokasi'  => (int) $row->id_lokasi,
            'lokasi'     => $row->nama_lokasi,
        ]);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}

    private function deny(string $message)
    {
        $response = \Config\Services::response();
        return $response->setStatusCode(401)
            ->setJSON(['status' => false, 'message' => $message]);
    }
}
