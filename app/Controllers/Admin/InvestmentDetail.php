<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InvestmentDetailModel;
use App\Models\InvestorModel;

class InvestmentDetail extends BaseController
{
    protected $detailModel;
    protected $investorModel;

    public function __construct()
    {
        $this->detailModel   = new InvestmentDetailModel();
        $this->investorModel = new InvestorModel();
    }

    public function index()
    {
        return view('admin/investment_detail/index', [
            'meta_title' => 'Penambahan Modal',
            'investors'  => $this->investorModel->where('is_active', 1)->orderBy('investor_name', 'ASC')->findAll(),
        ]);
    }

    public function data()
    {
        return $this->response->setJSON([
            'status'  => true,
            'data'    => $this->detailModel->getWithInvestor(),
            'numbers' => $this->detailModel->getDistinctNumbers(),
        ]);
    }

    public function get($id)
    {
        $data = $this->detailModel->find($id);
        if (!$data) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data tidak ditemukan']);
        }
        return $this->response->setJSON(['status' => true, 'data' => $data]);
    }

    public function save()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }

        if (!$this->validate([
            'id_investor'  => 'required|numeric',
            'nomor_invest' => 'required|max_length[50]',
            'total_invest' => 'required|numeric|greater_than[0]',
            'tgl_invest'   => 'permit_empty|valid_date',
        ])) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => implode('<br>', $this->validator->getErrors()),
            ]);
        }

        $this->detailModel->insert([
            'id_investor'  => $this->request->getPost('id_investor'),
            'nomor_invest' => $this->request->getPost('nomor_invest'),
            'total_invest' => str_replace(',', '', $this->request->getPost('total_invest')),
            'tgl_invest'   => $this->request->getPost('tgl_invest') ?: null,
            'notes'        => $this->request->getPost('notes'),
        ]);

        return $this->response->setJSON(['status' => true, 'message' => 'Penambahan modal berhasil disimpan']);
    }

    public function update($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }

        if (!$this->validate([
            'id_investor'  => 'required|numeric',
            'nomor_invest' => 'required|max_length[50]',
            'total_invest' => 'required|numeric|greater_than[0]',
            'tgl_invest'   => 'permit_empty|valid_date',
        ])) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => implode('<br>', $this->validator->getErrors()),
            ]);
        }

        $this->detailModel->update($id, [
            'id_investor'  => $this->request->getPost('id_investor'),
            'nomor_invest' => $this->request->getPost('nomor_invest'),
            'total_invest' => str_replace(',', '', $this->request->getPost('total_invest')),
            'tgl_invest'   => $this->request->getPost('tgl_invest') ?: null,
            'notes'        => $this->request->getPost('notes'),
        ]);

        return $this->response->setJSON(['status' => true, 'message' => 'Penambahan modal berhasil diperbarui']);
    }

    public function delete($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }
        $this->detailModel->delete($id);
        return $this->response->setJSON(['status' => true, 'message' => 'Data berhasil dihapus']);
    }

    public function chart()
    {
        $db = \Config\Database::connect();
        $filterNumber = $this->request->getGet('nomor_invest');

        $where = 'WHERE d.deleted_at IS NULL';
        $bind  = [];
        if ($filterNumber) {
            $where .= ' AND d.nomor_invest = :number:';
            $bind['number'] = $filterNumber;
        }

        // Total amount
        $totalQuery = $db->query(
            "SELECT COALESCE(SUM(d.total_invest),0) as total, COUNT(*) as total_count
             FROM investment_details d $where",
            $bind
        )->getRow();

        // Per investor for pie
        $perInvestor = $db->query(
            "SELECT i.investor_name as name, SUM(d.total_invest) as amount
             FROM investment_details d
             JOIN investors i ON i.id = d.id_investor
             $where
             GROUP BY d.id_investor, i.investor_name
             ORDER BY amount DESC",
            $bind
        )->getResultArray();

        // Top 10 + Others
        $topInvestors = [];
        $otherAmount  = 0;
        $otherCount   = 0;
        foreach ($perInvestor as $idx => $inv) {
            if ($idx < 10) {
                $topInvestors[] = [
                    'name'   => $inv['name'],
                    'amount' => (float) $inv['amount'],
                ];
            } else {
                $otherAmount += (float) $inv['amount'];
                $otherCount++;
            }
        }
        if ($otherCount > 0) {
            $topInvestors[] = [
                'name'   => "Lainnya ($otherCount investor)",
                'amount' => $otherAmount,
            ];
        }

        // Per nomor_invest (ignore filter, show all numbers)
        $perNumber = $db->query(
            "SELECT d.nomor_invest as name, SUM(d.total_invest) as amount
             FROM investment_details d
             WHERE d.deleted_at IS NULL
             GROUP BY d.nomor_invest
             ORDER BY amount DESC"
        )->getResultArray();
        foreach ($perNumber as &$pn) {
            $pn['amount'] = (float) $pn['amount'];
        }

        // Range distribution
        $ranges = $db->query(
            "SELECT
                SUM(CASE WHEN d.total_invest < 10000000 THEN 1 ELSE 0 END) as range_under_10jt,
                SUM(CASE WHEN d.total_invest >= 10000000 AND d.total_invest < 50000000 THEN 1 ELSE 0 END) as range_10_50jt,
                SUM(CASE WHEN d.total_invest >= 50000000 AND d.total_invest < 100000000 THEN 1 ELSE 0 END) as range_50_100jt,
                SUM(CASE WHEN d.total_invest >= 100000000 THEN 1 ELSE 0 END) as range_above_100jt,
                COUNT(*) as total
             FROM investment_details d
             LEFT JOIN investors i ON i.id = d.id_investor
             $where",
            $bind
        )->getRowArray();

        $numbers = $this->detailModel->getDistinctNumbers();

        return $this->response->setJSON([
            'status'           => true,
            'total_investment' => (float) $totalQuery->total,
            'total_count'      => (int) $totalQuery->total_count,
            'per_investor'     => $topInvestors,
            'per_number'       => $perNumber,
            'ranges'           => $ranges,
            'numbers'          => $numbers,
            'filter_number'    => $filterNumber,
        ]);
    }
}
