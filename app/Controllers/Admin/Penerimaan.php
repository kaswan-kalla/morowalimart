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
}
