<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PengeluaranModel;
use App\Models\BarangModel;
use App\Models\LokasiModel;

class Pengeluaran extends BaseController
{
    protected $model;
    protected $barangModel;
    protected $lokasiModel;

    public function __construct()
    {
        $this->model       = new PengeluaranModel();
        $this->barangModel = new BarangModel();
        $this->lokasiModel = new LokasiModel();
    }

    public function index()
    {
        return view('admin/pengeluaran/index', [
            'meta_title' => 'Penjualan Barang',
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

    public function get($id)
    {
        $data = $this->model->find($id);
        if (!$data) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data tidak ditemukan']);
        }
        return $this->response->setJSON(['status' => true, 'data' => $data]);
    }

    public function store()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }

        if (!$this->validate([
            'id_barang'  => 'required|numeric',
            'id_lokasi'  => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'jumlah'     => 'required|numeric|greater_than[0]',
            'tanggal'    => 'required|valid_date',
        ])) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => implode('<br>', $this->validator->getErrors()),
            ]);
        }

        $this->model->insert([
            'id_barang'  => $this->request->getPost('id_barang'),
            'id_lokasi'  => $this->request->getPost('id_lokasi'),
            'harga_jual' => str_replace(',', '', $this->request->getPost('harga_jual')),
            'jumlah'     => str_replace(',', '', $this->request->getPost('jumlah')),
            'tanggal'    => $this->request->getPost('tanggal'),
            'keterangan' => $this->request->getPost('keterangan'),
        ]);

        return $this->response->setJSON(['status' => true, 'message' => 'Penjualan berhasil disimpan']);
    }

    public function update($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }

        if (!$this->validate([
            'id_barang'  => 'required|numeric',
            'id_lokasi'  => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'jumlah'     => 'required|numeric|greater_than[0]',
            'tanggal'    => 'required|valid_date',
        ])) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => implode('<br>', $this->validator->getErrors()),
            ]);
        }

        $this->model->update($id, [
            'id_barang'  => $this->request->getPost('id_barang'),
            'id_lokasi'  => $this->request->getPost('id_lokasi'),
            'harga_jual' => str_replace(',', '', $this->request->getPost('harga_jual')),
            'jumlah'     => str_replace(',', '', $this->request->getPost('jumlah')),
            'tanggal'    => $this->request->getPost('tanggal'),
            'keterangan' => $this->request->getPost('keterangan'),
        ]);

        return $this->response->setJSON(['status' => true, 'message' => 'Penjualan berhasil diperbarui']);
    }

    public function delete($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }
        $this->model->delete($id);
        return $this->response->setJSON(['status' => true, 'message' => 'Data berhasil dihapus']);
    }

    public function chart()
    {
        $db = \Config\Database::connect();
        $perLokasi = $db->query(
            "SELECT l.nama_lokasi as name, SUM(p.jumlah) as total
             FROM pengeluaran p
             JOIN lokasi l ON l.id = p.id_lokasi
             WHERE p.deleted_at IS NULL
             GROUP BY p.id_lokasi, l.nama_lokasi
             ORDER BY total DESC"
        )->getResultArray();

        $totalAll = array_sum(array_column($perLokasi, 'total'));

        return $this->response->setJSON([
            'status'     => true,
            'per_lokasi' => $perLokasi,
            'total_all'  => (int) $totalAll,
        ]);
    }

    public function getHargaJual($id_barang)
    {
        $db = \Config\Database::connect();
        $row = $db->query(
            "SELECT harga_jual FROM penerimaan WHERE id_barang = ? AND deleted_at IS NULL ORDER BY tanggal DESC, id DESC LIMIT 1",
            [$id_barang]
        )->getRow();

        return $this->response->setJSON([
            'status'     => true,
            'harga_jual' => $row ? (int)$row->harga_jual : 0,
        ]);
    }

    public function getStokLokasi($id_lokasi)
    {
        $db = \Config\Database::connect();
        $db->query("CREATE TABLE IF NOT EXISTS mutasi (
            id INT AUTO_INCREMENT PRIMARY KEY,
            id_barang INT NOT NULL,
            id_lokasi_asal INT NOT NULL,
            id_lokasi_tujuan INT NOT NULL,
            jumlah INT NOT NULL DEFAULT 0,
            tanggal DATE NULL,
            keterangan TEXT NULL,
            created_at DATETIME NULL,
            updated_at DATETIME NULL,
            deleted_at DATETIME NULL
        )");

        // stok per barang di lokasi: pembelian - penjualan + mutasi masuk - mutasi keluar
        $rows = $db->query(
            "SELECT b.id,
                COALESCE(p.j,0) - COALESCE(k.j,0) + COALESCE(mi.j,0) - COALESCE(mo.j,0) as stok
             FROM barang b
             LEFT JOIN (SELECT id_barang, SUM(jumlah) j FROM penerimaan WHERE id_lokasi = ? AND deleted_at IS NULL GROUP BY id_barang) p ON p.id_barang = b.id
             LEFT JOIN (SELECT id_barang, SUM(jumlah) j FROM pengeluaran WHERE id_lokasi = ? AND deleted_at IS NULL GROUP BY id_barang) k ON k.id_barang = b.id
             LEFT JOIN (SELECT id_barang, SUM(jumlah) j FROM mutasi WHERE id_lokasi_tujuan = ? AND deleted_at IS NULL GROUP BY id_barang) mi ON mi.id_barang = b.id
             LEFT JOIN (SELECT id_barang, SUM(jumlah) j FROM mutasi WHERE id_lokasi_asal = ? AND deleted_at IS NULL GROUP BY id_barang) mo ON mo.id_barang = b.id
             WHERE b.deleted_at IS NULL",
            [$id_lokasi, $id_lokasi, $id_lokasi, $id_lokasi]
        )->getResultArray();

        $stok = [];
        foreach ($rows as $r) {
            $stok[$r['id']] = (int)$r['stok'];
        }

        return $this->response->setJSON(['status' => true, 'stok' => $stok]);
    }
}
