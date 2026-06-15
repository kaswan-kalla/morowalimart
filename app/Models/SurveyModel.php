<?php

namespace App\Models;

use CodeIgniter\Model;

class SurveyModel extends Model
{
    protected $table            = 'surveys';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'nama',
        'alamat',
        'pengeluaran_perbulan',
        'status_menikah',
        'no_wa',
    ];

    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $validationRules  = [
        'nama'                => 'required|min_length[3]|max_length[100]',
        'alamat'              => 'required|in_list[Desa Labota,Desa Keurea,Desa Makarti,Desa Baho Makmur,Desa Bahodopi,Desa Fatufia]',
        'pengeluaran_perbulan' => 'required|in_list[Dibawah 1jt,1jt - 2jt,2jt - 3jt,Diatas 3jt]',
        'status_menikah'       => 'required|in_list[Menikah,Belum Menikah]',
        'no_wa'               => 'required|min_length[11]|max_length[15]',
    ];
}
