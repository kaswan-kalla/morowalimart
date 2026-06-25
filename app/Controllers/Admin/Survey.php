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

        // Preferensi belanja
        $query = $db->query("SELECT preferensi_belanja, COUNT(*) as jumlah FROM surveys GROUP BY preferensi_belanja");
        $perPreferensi = $query->getResultArray();

        // Siap investasi
        $query = $db->query("SELECT siap_investasi, COUNT(*) as jumlah FROM surveys GROUP BY siap_investasi");
        $perInvestasi = $query->getResultArray();

        // Siap member
        $siapMember = $model->where('siap_member', 1)->countAllResults();

        // Data terbaru
        $latest = $model->orderBy('created_at', 'DESC')->findAll(50);

        return $this->response->setJSON([
            'status' => true,
            'total' => (int)$total,
            'per_desa' => $perDesa,
            'per_status' => $perStatus,
            'per_pengeluaran' => $perPengeluaran,
            'per_preferensi'   => $perPreferensi,
            'per_investasi'    => $perInvestasi,
            'siap_member'      => (int)$siapMember,
            'latest' => $latest,
        ]);
    }
}
