<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PenerimaanModel;
use App\Models\BarangModel;
use App\Models\LokasiModel;

class Penerimaan extends BaseController
{
    protected $model;
    protected $barangModel;
    protected $lokasiModel;

    public function __construct()
    {
        $this->model       = new PenerimaanModel();
        $this->barangModel = new BarangModel();
        $this->lokasiModel = new LokasiModel();
        $this->ensureFeeColumn();
    }

    private function ensureFeeColumn()
    {
        $db = \Config\Database::connect();
        $exists = $db->query("SELECT COUNT(*) as c FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'penerimaan' AND COLUMN_NAME = 'fee_outlet'")->getRow()->c;
        if (!$exists) {
            $db->query("ALTER TABLE penerimaan ADD COLUMN fee_outlet DECIMAL(14,2) NOT NULL DEFAULT 0 AFTER harga_jual");
        }
    }

    public function index()
    {
        return view('admin/penerimaan/index', [
            'meta_title' => 'Pembelian Barang',
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
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'fee_outlet' => 'permit_empty|numeric',
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
            'harga_beli' => str_replace(',', '', $this->request->getPost('harga_beli')),
            'harga_jual' => str_replace(',', '', $this->request->getPost('harga_jual')),
            'fee_outlet' => str_replace(',', '', $this->request->getPost('fee_outlet')),
            'jumlah'     => str_replace(',', '', $this->request->getPost('jumlah')),
            'tanggal'    => $this->request->getPost('tanggal'),
            'keterangan' => $this->request->getPost('keterangan'),
        ]);

        return $this->response->setJSON(['status' => true, 'message' => 'Pembelian berhasil disimpan']);
    }

    public function update($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }

        if (!$this->validate([
            'id_barang'  => 'required|numeric',
            'id_lokasi'  => 'required|numeric',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'fee_outlet' => 'permit_empty|numeric',
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
            'harga_beli' => str_replace(',', '', $this->request->getPost('harga_beli')),
            'harga_jual' => str_replace(',', '', $this->request->getPost('harga_jual')),
            'fee_outlet' => str_replace(',', '', $this->request->getPost('fee_outlet')),
            'jumlah'     => str_replace(',', '', $this->request->getPost('jumlah')),
            'tanggal'    => $this->request->getPost('tanggal'),
            'keterangan' => $this->request->getPost('keterangan'),
        ]);

        return $this->response->setJSON(['status' => true, 'message' => 'Pembelian berhasil diperbarui']);
    }

    public function delete($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }
        $this->model->delete($id);
        return $this->response->setJSON(['status' => true, 'message' => 'Data berhasil dihapus']);
    }

    // Stok per barang di lokasi (untuk preview input massal)
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

    // Simpan beberapa item pembelian sekaligus (input massal)
    public function storeBulk()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }

        $idLokasi = $this->request->getPost('id_lokasi');
        $tanggal  = $this->request->getPost('tanggal');
        if (!$idLokasi || !$tanggal) {
            return $this->response->setJSON(['status' => false, 'message' => 'Lokasi dan tanggal wajib diisi']);
        }

        $idBarang   = $this->request->getPost('id_barang') ?: [];
        $hargaBeli  = $this->request->getPost('harga_beli') ?: [];
        $hargaJual  = $this->request->getPost('harga_jual') ?: [];
        $feeOutlet  = $this->request->getPost('fee_outlet') ?: [];
        $jumlah     = $this->request->getPost('jumlah') ?: [];
        $keterangan = $this->request->getPost('keterangan') ?: [];

        $saved = 0;
        foreach ($idBarang as $i => $barangId) {
            $jml = (int) str_replace(',', '', $jumlah[$i] ?? 0);
            if (!$barangId || $jml <= 0) {
                continue;
            }
            $this->model->insert([
                'id_barang'  => $barangId,
                'id_lokasi'  => $idLokasi,
                'harga_beli' => (float) str_replace(',', '', $hargaBeli[$i] ?? 0),
                'harga_jual' => (float) str_replace(',', '', $hargaJual[$i] ?? 0),
                'fee_outlet' => (float) str_replace(',', '', $feeOutlet[$i] ?? 0),
                'jumlah'     => $jml,
                'tanggal'    => $tanggal,
                'keterangan' => $keterangan[$i] ?? '',
            ]);
            $saved++;
        }

        return $this->response->setJSON([
            'status'  => $saved > 0,
            'message' => $saved > 0 ? $saved . ' item pembelian berhasil disimpan' : 'Tidak ada item dengan jumlah di atas 0',
        ]);
    }
}
