<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class User extends BaseController
{
    protected $userModel;
    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        return view('admin/user/index', ['meta_title' => 'Manajemen User']);
    }

    /** Data untuk DataTable (server-side) */
    public function data()
    {
        $search = $this->request->getGet('search')['value'] ?? '';
        $start  = (int) $this->request->getGet('start');
        $limit  = (int) $this->request->getGet('length');

        $builder = $this->userModel->builder();
        $builder->select('users.*, l.nama_lokasi as lokasi_name');
        $builder->join('lokasi l', 'l.id = users.id_lokasi', 'left');
        if ($search) {
            $builder->groupStart()->like('name', $search)->orLike('email', $search)->groupEnd();
        }

        $total = (clone $builder)->countAllResults(false);
        $users = $builder->orderBy('created_at', 'DESC')->get($limit, $start)->getResultArray();

        return $this->response->setJSON([
            'draw'            => (int) $this->request->getGet('draw'),
            'recordsTotal'    => $total,
            'recordsFiltered' => $total,
            'data'            => $users,
        ]);
    }

    /** Toggle status aktif/nonaktif */
    public function toggleStatus($id)
    {
        if (!$this->request->isAJAX()) return $this->response->setJSON(['status' => false]);
        $user = $this->userModel->find($id);
        if (!$user) return $this->response->setJSON(['status' => false, 'message' => 'User tidak ditemukan']);

        $this->userModel->update($id, ['is_active' => !$user['is_active']]);
        return $this->response->setJSON(['status' => true, 'message' => 'Status user diperbarui']);
    }

    /** Ubah role user (mendukung petugas outlet + pengaturan outlet) */
    public function updateRole()
    {
        if (!$this->request->isAJAX()) return $this->response->setJSON(['status' => false]);

        $id   = (int) $this->request->getPost('id');
        $role = $this->request->getPost('role');

        $allowed = ['buyer', 'seller', 'admin', 'courier', 'petugas'];
        if (!in_array($role, $allowed)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Role tidak valid']);
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->response->setJSON(['status' => false, 'message' => 'User tidak ditemukan']);
        }

        // Pastikan kolom id_lokasi tersedia
        $db = \Config\Database::connect();
        $exists = $db->query("SELECT COUNT(*) as c FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'id_lokasi'")->getRow()->c;
        if (!$exists) {
            $db->query("ALTER TABLE users ADD COLUMN id_lokasi INT NULL AFTER role");
        }

        $data = ['role' => $role];
        if ($role === 'petugas') {
            $idLokasi = (int) $this->request->getPost('id_lokasi');
            $lokasi = $db->query("SELECT id FROM lokasi WHERE id = ? AND deleted_at IS NULL", [$idLokasi])->getRow();
            if (!$lokasi) {
                return $this->response->setJSON(['status' => false, 'message' => 'Pilih outlet untuk petugas']);
            }
            $data['id_lokasi'] = $idLokasi;
        } else {
            $data['id_lokasi'] = null;
        }

        $this->userModel->update($id, $data);
        return $this->response->setJSON(['status' => true, 'message' => 'Role diperbarui']);
    }
}
