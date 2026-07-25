<?php

namespace App\Models;

use CodeIgniter\Model;

class InvestorModel extends Model
{
    protected $table            = 'investors';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields    = [
        'investor_name',
        'phone',
        'email',
        'address',
        'notes',
        'is_active',
    ];

    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $validationRules  = [
        'investor_name' => 'required|min_length[3]|max_length[150]',
        'phone'         => 'permit_empty|min_length[10]|max_length[20]',
        'email'         => 'permit_empty|valid_email|max_length[100]',
    ];
}
