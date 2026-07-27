<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MutasiModel;
use App\Models\BarangModel;
use App\Models\LokasiModel;

class Mutasi extends BaseController
{
    protected $model;
    protected $barangModel;
    protected $lokasiModel;

    public function __construct()
    {
        $this->ensureTable();
        $this->model       = new MutasiModel();
        $this->barangModel = new BarangModel();
        $this->lokasiModel = new LokasiModel();
    }

    private function ensureTable()
    {
        $db = \Config\Database::connect();
        $db->query("CREATE TABLE IF NOT EXISTS mutasi (
            id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            id_barang INT(11) UNSIGNED NOT NULL,
            id_lokasi_asal INT(11) UNSIGNED NOT NULL,
            id_lokasi_tujuan INT(11) UNSIGNED NOT NULL,
            jumlah INT(11) NOT NULL DEFAULT 0,
            tanggal DATE NOT NULL,
            keterangan TEXT NULL,
            created_at DATETIME NULL,
            updated_at DATETIME NULL,
            deleted_at DATETIME NULL,
            PRIMARY KEY (id),
            KEY idx_barang (id_barang),
            KEY idx_asal (id_lokasi_asal),
            KEY idx_tujuan (id_lokasi_tujuan)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    public function index()
    {
        return view('admin/mutasi/index', [
            'meta_title' => 'Mutasi Barang',
            'barang'     => $this->barangModel->where('deleted_at', null)->orderBy('nama_barang', 'ASC')->findAll(),
            'lokasi'     => $this->lokasiModel->where('deleted_at', null)->orderBy('nama_lokasi', 'ASC')->findAll(),
        ]);
    }

    public function data()
    {
        return $this->response->setJSON([
            'status' => true,
            'data'   => $this->model->getWithRelations(),
        ]);
    }

    // Hitung stok tersedia barang di suatu lokasi (masuk - keluar + mutasi masuk - mutasi keluar)
    private function getStok($idBarang, $idLokasi, $excludeMutasiId = null)
    {
        $db = \Config\Database::connect();

        $masuk = $db->query(
            "SELECT COALESCE(SUM(jumlah),0) as t FROM penerimaan WHERE deleted_at IS NULL AND id_barang = ? AND id_lokasi = ?",
            [$idBarang, $idLokasi]
        )->getRow()->t;

        $keluar = $db->query(
            "SELECT COALESCE(SUM(jumlah),0) as t FROM pengeluaran WHERE deleted_at IS NULL AND id_barang = ? AND id_lokasi = ?",
            [$idBarang, $idLokasi]
        )->getRow()->t;

        $exclude = $excludeMutasiId ? " AND id != " . (int)$excludeMutasiId : "";

        $mutasiMasuk = $db->query(
            "SELECT COALESCE(SUM(jumlah),0) as t FROM mutasi WHERE deleted_at IS NULL AND id_barang = ? AND id_lokasi_tujuan = ?" . $exclude,
            [$idBarang, $idLokasi]
        )->getRow()->t;

        $mutasiKeluar = $db->query(
            "SELECT COALESCE(SUM(jumlah),0) as t FROM mutasi WHERE deleted_at IS NULL AND id_barang = ? AND id_lokasi_asal = ?" . $exclude,
            [$idBarang, $idLokasi]
        )->getRow()->t;

        return (int)$masuk - (int)$keluar + (int)$mutasiMasuk - (int)$mutasiKeluar;
    }

    public function stok()
    {
        $idBarang = $this->request->getGet('id_barang');
        $idLokasi = $this->request->getGet('id_lokasi');
        if (!$idBarang || !$idLokasi) {
            return $this->response->setJSON(['status' => false, 'stok' => 0]);
        }
        return $this->response->setJSON([
            'status' => true,
            'stok'   => $this->getStok($idBarang, $idLokasi),
        ]);
    }

    public function store()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }

        if (!$this->validate([
            'id_barang'        => 'required|numeric',
            'id_lokasi_asal'   => 'required|numeric',
            'id_lokasi_tujuan' => 'required|numeric|differs[id_lokasi_asal]',
            'jumlah'           => 'required|numeric|greater_than[0]',
            'tanggal'          => 'required|valid_date',
        ])) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => implode('<br>', $this->validator->getErrors()),
            ]);
        }

        $idBarang = $this->request->getPost('id_barang');
        $idAsal   = $this->request->getPost('id_lokasi_asal');
        $jumlah   = (int)str_replace(['.', ','], '', $this->request->getPost('jumlah'));

        $stokAsal = $this->getStok($idBarang, $idAsal);
        if ($jumlah > $stokAsal) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Stok di outlet asal tidak cukup (tersedia: ' . number_format($stokAsal, 0, ',', '.') . ')',
            ]);
        }

        $this->model->insert([
            'id_barang'        => $idBarang,
            'id_lokasi_asal'   => $idAsal,
            'id_lokasi_tujuan' => $this->request->getPost('id_lokasi_tujuan'),
            'jumlah'           => $jumlah,
            'tanggal'          => $this->request->getPost('tanggal'),
            'keterangan'       => $this->request->getPost('keterangan'),
        ]);

        return $this->response->setJSON(['status' => true, 'message' => 'Mutasi barang berhasil disimpan']);
    }

    public function update($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }

        if (!$this->validate([
            'id_barang'        => 'required|numeric',
            'id_lokasi_asal'   => 'required|numeric',
            'id_lokasi_tujuan' => 'required|numeric|differs[id_lokasi_asal]',
            'jumlah'           => 'required|numeric|greater_than[0]',
            'tanggal'          => 'required|valid_date',
        ])) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => implode('<br>', $this->validator->getErrors()),
            ]);
        }

        $idBarang = $this->request->getPost('id_barang');
        $idAsal   = $this->request->getPost('id_lokasi_asal');
        $jumlah   = (int)str_replace(['.', ','], '', $this->request->getPost('jumlah'));

        // Hitung stok asal tanpa memperhitungkan mutasi yang sedang diedit
        $stokAsal = $this->getStok($idBarang, $idAsal, $id);
        if ($jumlah > $stokAsal) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Stok di outlet asal tidak cukup (tersedia: ' . number_format($stokAsal, 0, ',', '.') . ')',
            ]);
        }

        $this->model->update($id, [
            'id_barang'        => $idBarang,
            'id_lokasi_asal'   => $idAsal,
            'id_lokasi_tujuan' => $this->request->getPost('id_lokasi_tujuan'),
            'jumlah'           => $jumlah,
            'tanggal'          => $this->request->getPost('tanggal'),
            'keterangan'       => $this->request->getPost('keterangan'),
        ]);

        return $this->response->setJSON(['status' => true, 'message' => 'Mutasi barang berhasil diperbarui']);
    }

    public function delete($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }
        $this->model->delete($id);
        return $this->response->setJSON(['status' => true, 'message' => 'Data berhasil dihapus']);
    }
}
