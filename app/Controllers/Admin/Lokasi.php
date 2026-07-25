<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LokasiModel;

class Lokasi extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new LokasiModel();
    }

    public function index()
    {
        return view('admin/lokasi/index', [
            'meta_title' => 'Data Lokasi',
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
            return $this->response->setJSON(['status' => false, 'message' => 'Lokasi tidak ditemukan']);
        }
        return $this->response->setJSON(['status' => true, 'data' => $data]);
    }

    public function store()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }

        if (!$this->validate([
            'nama_lokasi' => 'required|max_length[255]',
            'alamat'      => 'permit_empty',
        ])) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => implode('<br>', $this->validator->getErrors()),
            ]);
        }

        $this->model->insert([
            'nama_lokasi' => $this->request->getPost('nama_lokasi'),
            'alamat'      => $this->request->getPost('alamat'),
        ]);

        return $this->response->setJSON(['status' => true, 'message' => 'Lokasi berhasil disimpan']);
    }

    public function update($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }

        if (!$this->validate([
            'nama_lokasi' => 'required|max_length[255]',
            'alamat'      => 'permit_empty',
        ])) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => implode('<br>', $this->validator->getErrors()),
            ]);
        }

        $this->model->update($id, [
            'nama_lokasi' => $this->request->getPost('nama_lokasi'),
            'alamat'      => $this->request->getPost('alamat'),
        ]);

        return $this->response->setJSON(['status' => true, 'message' => 'Lokasi berhasil diperbarui']);
    }

    public function delete($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }
        $this->model->delete($id);
        return $this->response->setJSON(['status' => true, 'message' => 'Lokasi berhasil dihapus']);
    }
}
