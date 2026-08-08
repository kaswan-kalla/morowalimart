<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;

/**
 * API Lokasi - daftar outlet untuk dipilih petugas di app mobile
 */
class Lokasi extends BaseController
{
    /**
     * GET /api/v1/lokasi
     * Data: [{id, nama_lokasi}]
     */
    public function index()
    {
        $db = \Config\Database::connect();
        $rows = $db->query(
            "SELECT id, nama_lokasi FROM lokasi WHERE deleted_at IS NULL ORDER BY nama_lokasi ASC"
        )->getResultArray();

        $data = [];
        foreach ($rows as $r) {
            $data[] = [
                'id'          => (int) $r['id'],
                'nama_lokasi' => $r['nama_lokasi'],
            ];
        }

        return $this->response->setJSON(['status' => true, 'data' => $data]);
    }
}
