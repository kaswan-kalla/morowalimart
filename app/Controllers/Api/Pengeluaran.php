<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\PengeluaranModel;

/**
 * API Pengeluaran - input barang keluar oleh petugas outlet (tanpa login)
 * id_lokasi dikirim dari app; stok divalidasi di server
 */
class Pengeluaran extends BaseController
{
    /**
     * POST /api/v1/pengeluaran
     * Body: {"id_lokasi":1, "tanggal": "2026-08-08", "items": [{"id_barang":1,"harga_jual":1500,"jumlah":5,"keterangan":""}]}
     */
    public function store()
    {
        $json = $this->request->getJSON(true);
        if (!$json) {
            $json = $this->request->getPost();
        }

        $idLokasi = (int) ($json['id_lokasi'] ?? 0);
        $tanggal  = $json['tanggal'] ?? '';
        $items    = $json['items'] ?? [];

        if (!$idLokasi) {
            return $this->response->setJSON(['status' => false, 'message' => 'id_lokasi wajib diisi']);
        }
        if (!$tanggal) {
            return $this->response->setJSON(['status' => false, 'message' => 'Tanggal wajib diisi']);
        }
        if (!is_array($items) || !count($items)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Minimal satu item barang']);
        }

        $db = \Config\Database::connect();
        $lokasi = $db->query("SELECT id FROM lokasi WHERE id = ? AND deleted_at IS NULL", [$idLokasi])->getRow();
        if (!$lokasi) {
            return $this->response->setJSON(['status' => false, 'message' => 'Lokasi tidak ditemukan']);
        }

        $model = new PengeluaranModel();
        $stokMap = stok_outlet($idLokasi);
        $saved = 0;
        foreach ($items as $i => $item) {
            if (!is_array($item)) {
                continue;
            }
            $idBarang = $item['id_barang'] ?? 0;
            $jumlah   = (int) ($item['jumlah'] ?? 0);
            if (!$idBarang || $jumlah <= 0) {
                continue;
            }

            $stok = $stokMap[$idBarang] ?? 0;
            if ($jumlah > $stok) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Item #' . ($i + 1) . ': jumlah melebihi stok (sisa ' . number_format($stok) . ')',
                ]);
            }

            $model->insert([
                'id_barang'  => $idBarang,
                'id_lokasi'  => $idLokasi,
                'harga_jual' => (float) ($item['harga_jual'] ?? 0),
                'jumlah'     => $jumlah,
                'tanggal'    => $tanggal,
                'keterangan' => $item['keterangan'] ?? '',
            ]);
            $saved++;
        }

        return $this->response->setJSON([
            'status'  => $saved > 0,
            'message' => $saved > 0 ? $saved . ' item penjualan berhasil disimpan' : 'Tidak ada item valid untuk disimpan',
        ]);
    }
}
