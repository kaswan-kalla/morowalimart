<?php

namespace App\Models;

use CodeIgniter\Model;

class InvestmentDetailModel extends Model
{
    protected $table            = 'investment_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields    = [
        'id_investor',
        'nomor_invest',
        'total_invest',
        'tgl_invest',
        'notes',
    ];

    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $validationRules  = [
        'id_investor'  => 'required|numeric',
        'nomor_invest' => 'required|max_length[50]',
        'total_invest' => 'required|numeric|greater_than[0]',
        'tgl_invest'   => 'permit_empty|valid_date',
    ];

    public function getWithInvestor()
    {
        return $this->select('investment_details.*, investors.investor_name')
            ->join('investors', 'investors.id = investment_details.id_investor')
            ->orderBy('investment_details.created_at', 'DESC')
            ->findAll();
    }

    public function getDistinctNumbers()
    {
        return $this->select('nomor_invest')
            ->distinct()
            ->orderBy('nomor_invest', 'ASC')
            ->findAll();
    }
}
