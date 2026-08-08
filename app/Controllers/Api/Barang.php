<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\BarangModel;

/**
 * API Barang - daftar barang + stok outlet + harga pembelian terakhir
 * Dipakai app mobile petugas outlet (id_lokasi dikirim dari app)
 */
class Barang extends BaseController
{
    /**
     * GET /api/v1/barang?id_lokasi=1
     * Data: {lokasi, barang: [{id, nama_barang, satuan, stok, harga_beli, harga_jual, fee_outlet}]}
     */
    public function index()
    {
        $idLokasi = (int) $this->request->getGet('id_lokasi');
        if (!$idLokasi) {
            $idLokasi = (int) $this->request->getPost('id_lokasi');
        }
        if (!$idLokasi) {
            return $this->response->setJSON(['status' => false, 'message' => 'Parameter id_lokasi wajib diisi']);
        }

        $db = \Config\Database::connect();
        $lokasi = $db->query("SELECT nama_lokasi FROM lokasi WHERE id = ? AND deleted_at IS NULL", [$idLokasi])->getRow();
        if (!$lokasi) {
            return $this->response->setJSON(['status' => false, 'message' => 'Lokasi tidak ditemukan']);
        }

        $barang = (new BarangModel())
            ->where('deleted_at', null)
            ->orderBy('nama_barang', 'ASC')
            ->findAll();

        $stok = stok_outlet($idLokasi);

        // Harga pembelian terakhir per barang (harga_beli, harga_jual, fee_outlet)
        $last = [];
        $rows = $db->query(
            "SELECT id_barang, harga_beli, harga_jual, fee_outlet FROM penerimaan WHERE deleted_at IS NULL ORDER BY tanggal DESC, id DESC"
        )->getResultArray();
        foreach ($rows as $r) {
            if (!isset($last[$r['id_barang']])) {
                $last[$r['id_barang']] = $r;
            }
        }

        $data = [];
        foreach ($barang as $b) {
            $h = $last[$b['id']] ?? null;
            $data[] = [
                'id'          => (int) $b['id'],
                'nama_barang' => $b['nama_barang'],
                'satuan'      => $b['satuan'] ?? '',
                'stok'        => $stok[$b['id']] ?? 0,
                'harga_beli'  => $h ? (int) $h['harga_beli'] : 0,
                'harga_jual'  => $h ? (int) $h['harga_jual'] : 0,
                'fee_outlet'  => $h ? (int) $h['fee_outlet'] : 0,
            ];
        }

        return $this->response->setJSON([
            'status' => true,
            'data'   => [
                'lokasi' => [
                    'id'   => $idLokasi,
                    'nama' => $lokasi->nama_lokasi,
                ],
                'barang' => $data,
            ],
        ]);
    }
}
