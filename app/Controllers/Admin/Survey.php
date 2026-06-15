<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SurveyModel;

class Survey extends BaseController
{
    public function index()
    {
        $data = [
            'meta_title' => 'Data Survey',
        ];
        return view('admin/survey/index', $data);
    }

    public function data()
    {
        $model = new SurveyModel();
        $db = \Config\Database::connect();

        // Total responden
        $total = $model->countAllResults();

        // Per desa
        $query = $db->query("SELECT alamat, COUNT(*) as jumlah FROM surveys GROUP BY alamat ORDER BY jumlah DESC");
        $perDesa = $query->getResultArray();

        // Status menikah
        $query = $db->query("SELECT status_menikah, COUNT(*) as jumlah FROM surveys GROUP BY status_menikah");
        $perStatus = $query->getResultArray();

        // Rentang pengeluaran
        $query = $db->query("SELECT pengeluaran_perbulan, COUNT(*) as jumlah FROM surveys GROUP BY pengeluaran_perbulan ORDER BY FIELD(pengeluaran_perbulan, 'Dibawah 1jt','1jt - 2jt','2jt - 3jt','Diatas 3jt')");
        $perPengeluaran = $query->getResultArray();

        // Data terbaru
        $latest = $model->orderBy('created_at', 'DESC')->findAll(50);

        return $this->response->setJSON([
            'status' => true,
            'total' => (int)$total,
            'per_desa' => $perDesa,
            'per_status' => $perStatus,
            'per_pengeluaran' => $perPengeluaran,
            'latest' => $latest,
        ]);
    }
}
