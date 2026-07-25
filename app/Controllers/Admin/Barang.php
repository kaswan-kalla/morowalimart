<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BarangModel;

class Barang extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new BarangModel();
    }

    public function index()
    {
        return view('admin/barang/index', [
            'meta_title' => 'Data Barang',
        ]);
    }

    public function data()
    {
        return $this->response->setJSON([
            'status' => true,
            'data'   => $this->model->orderBy('created_at', 'DESC')->findAll(),
        ]);
    }

    public function get($id)
    {
        $data = $this->model->find($id);
        if (!$data) {
            return $this->response->setJSON(['status' => false, 'message' => 'Barang tidak ditemukan']);
        }
        return $this->response->setJSON(['status' => true, 'data' => $data]);
    }

    public function store()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }

        if (!$this->validate([
            'nama_barang' => 'required|max_length[255]',
            'satuan'      => 'permit_empty|max_length[50]',
        ])) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => implode('<br>', $this->validator->getErrors()),
            ]);
        }

        $this->model->insert([
            'nama_barang' => $this->request->getPost('nama_barang'),
            'satuan'      => $this->request->getPost('satuan') ?: 'pcs',
        ]);

        return $this->response->setJSON(['status' => true, 'message' => 'Barang berhasil disimpan']);
    }

    public function update($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }

        if (!$this->validate([
            'nama_barang' => 'required|max_length[255]',
            'satuan'      => 'permit_empty|max_length[50]',
        ])) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => implode('<br>', $this->validator->getErrors()),
            ]);
        }

        $this->model->update($id, [
            'nama_barang' => $this->request->getPost('nama_barang'),
            'satuan'      => $this->request->getPost('satuan') ?: 'pcs',
        ]);

        return $this->response->setJSON(['status' => true, 'message' => 'Barang berhasil diperbarui']);
    }

    public function delete($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }
        $this->model->delete($id);
        return $this->response->setJSON(['status' => true, 'message' => 'Barang berhasil dihapus']);
    }
}
