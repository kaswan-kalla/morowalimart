<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InvestorModel;

class Investor extends BaseController
{
    protected $investorModel;

    public function __construct()
    {
        $this->investorModel = new InvestorModel();
    }

    public function index()
    {
        return view('admin/investor/index', [
            'meta_title' => 'Manajemen Investor',
        ]);
    }

    public function data()
    {
        $db = \Config\Database::connect();
        $investors = $this->investorModel->orderBy('created_at', 'DESC')->findAll();

        // Ambil total per investor dari investment_details
        $ids = array_column($investors, 'id');
        $totals = [];
        if (!empty($ids)) {
            $rows = $db->query(
                "SELECT id_investor, COALESCE(SUM(total_invest),0) as total
                FROM investment_details
                WHERE deleted_at IS NULL AND id_investor IN (" . implode(',', $ids) . ")
                GROUP BY id_investor"
            )->getResultArray();
            foreach ($rows as $r) {
                $totals[(int) $r['id_investor']] = (float) $r['total'];
            }
        }

        foreach ($investors as &$inv) {
            $inv['total_modal'] = $totals[(int) $inv['id']] ?? 0;
        }

        return $this->response->setJSON([
            'status' => true,
            'data'   => $investors,
        ]);
    }

    public function get($id)
    {
        $data = $this->investorModel->find($id);
        if (!$data) {
            return $this->response->setJSON(['status' => false, 'message' => 'Investor tidak ditemukan']);
        }
        return $this->response->setJSON(['status' => true, 'data' => $data]);
    }

    public function save()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }

        if (!$this->validate([
            'investor_name' => 'required|min_length[3]|max_length[150]',
            'phone'         => 'permit_empty|min_length[10]|max_length[20]',
            'email'         => 'permit_empty|valid_email|max_length[100]',
        ])) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => implode('<br>', $this->validator->getErrors()),
            ]);
        }

        $this->investorModel->insert([
            'investor_name' => $this->request->getPost('investor_name'),
            'phone'         => $this->request->getPost('phone'),
            'email'         => $this->request->getPost('email'),
            'address'       => $this->request->getPost('address'),
            'notes'         => $this->request->getPost('notes'),
            'is_active'     => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return $this->response->setJSON(['status' => true, 'message' => 'Investor berhasil ditambahkan']);
    }

    public function update($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }

        if (!$this->validate([
            'investor_name' => 'required|min_length[3]|max_length[150]',
            'phone'         => 'permit_empty|min_length[10]|max_length[20]',
            'email'         => 'permit_empty|valid_email|max_length[100]',
        ])) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => implode('<br>', $this->validator->getErrors()),
            ]);
        }

        $this->investorModel->update($id, [
            'investor_name' => $this->request->getPost('investor_name'),
            'phone'         => $this->request->getPost('phone'),
            'email'         => $this->request->getPost('email'),
            'address'       => $this->request->getPost('address'),
            'notes'         => $this->request->getPost('notes'),
            'is_active'     => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return $this->response->setJSON(['status' => true, 'message' => 'Investor berhasil diperbarui']);
    }

    public function delete($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }
        $this->investorModel->delete($id);
        return $this->response->setJSON(['status' => true, 'message' => 'Investor berhasil dihapus']);
    }

    public function chart()
    {
        $db = \Config\Database::connect();

        $totalInvestors = $this->investorModel->countAll();
        $activeCount    = $this->investorModel->where('is_active', 1)->countAllResults();
        $inactiveCount  = $this->investorModel->where('is_active', 0)->countAllResults();

        // Total akumulasi dari semua investment_details
        $totalAccumulated = $db->query("
            SELECT COALESCE(SUM(total_invest),0) as total
            FROM investment_details WHERE deleted_at IS NULL
        ")->getRow()->total;

        // Per investor
        $accInvestors = $db->query("
            SELECT i.investor_name as name, SUM(d.total_invest) as amount
            FROM investment_details d
            JOIN investors i ON i.id = d.id_investor
            WHERE d.deleted_at IS NULL AND i.deleted_at IS NULL
            GROUP BY d.id_investor, i.investor_name
            ORDER BY amount DESC
        ")->getResultArray();

        $accPerInvestor = [];
        $accOtherAmount = 0;
        $accOtherCount  = 0;
        foreach ($accInvestors as $i => $inv) {
            $amt = (float) $inv['amount'];
            if ($i < 10) {
                $accPerInvestor[] = ['name' => $inv['name'], 'amount' => $amt];
            } else {
                $accOtherAmount += $amt;
                $accOtherCount++;
            }
        }
        if ($accOtherCount > 0) {
            $accPerInvestor[] = ['name' => "Lainnya ($accOtherCount investor)", 'amount' => $accOtherAmount];
        }

        // Range distribusi
        $accRanges = $db->query("
            SELECT
                SUM(CASE WHEN total_invest < 10000000 THEN 1 ELSE 0 END) as range_under_10jt,
                SUM(CASE WHEN total_invest >= 10000000 AND total_invest < 50000000 THEN 1 ELSE 0 END) as range_10_50jt,
                SUM(CASE WHEN total_invest >= 50000000 AND total_invest < 100000000 THEN 1 ELSE 0 END) as range_50_100jt,
                SUM(CASE WHEN total_invest >= 100000000 THEN 1 ELSE 0 END) as range_above_100jt,
                COUNT(*) as total
            FROM investment_details WHERE deleted_at IS NULL
        ")->getRowArray();

        return $this->response->setJSON([
            'status'              => true,
            'total_investors'     => (int) $totalInvestors,
            'active_count'        => (int) $activeCount,
            'inactive_count'      => (int) $inactiveCount,
            'total_accumulated'   => (float) $totalAccumulated,
            'acc_per_investor'    => $accPerInvestor,
            'acc_ranges'          => $accRanges,
        ]);
    }
}
