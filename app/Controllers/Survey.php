<?php

namespace App\Controllers;

use App\Models\SurveyModel;

class Survey extends BaseController
{
    protected $surveyModel;

    public function __construct()
    {
        $this->surveyModel = new SurveyModel();
    }

    public function index()
    {
        $data = [
            'content'    => 'survey',
            'meta_title' => 'Survey Pelanggan',
        ];
        return view('layout/marketplace_content', $data);
    }

    public function submit()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
        }

        $data = [
            'nama'                 => $this->request->getPost('nama'),
            'alamat'               => $this->request->getPost('alamat'),
            'pengeluaran_perbulan'  => $this->request->getPost('pengeluaran_perbulan'),
            'status_menikah'        => $this->request->getPost('status_menikah'),
            'no_wa'                => '62' . ltrim($this->request->getPost('no_wa'), '0'),
            'siap_member'          => $this->request->getPost('siap_member') ? 1 : 0,
            'preferensi_belanja'   => $this->request->getPost('preferensi_belanja'),
            'siap_investasi'       => $this->request->getPost('siap_investasi'),
        ];

        if (!$this->surveyModel->insert($data)) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Gagal menyimpan: ' . implode(', ', $this->surveyModel->errors()),
            ]);
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Terima kasih! Data survey berhasil disimpan.',
        ]);
    }
}
