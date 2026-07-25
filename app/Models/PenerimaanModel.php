<?php

namespace App\Models;

use CodeIgniter\Model;

class PenerimaanModel extends Model
{
    protected $table            = 'penerimaan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['id_barang', 'id_lokasi', 'harga_beli', 'harga_jual', 'jumlah', 'tanggal', 'keterangan'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'id_barang'  => 'required|numeric',
        'id_lokasi'  => 'required|numeric',
        'harga_beli' => 'required|numeric|greater_than_equal_to[0]',
        'harga_jual' => 'required|numeric|greater_than_equal_to[0]',
        'jumlah'     => 'required|numeric|greater_than[0]',
        'tanggal'    => 'required|valid_date',
    ];

    public function getWithRelations()
    {
        return $this->select('penerimaan.*, barang.nama_barang, lokasi.nama_lokasi')
            ->join('barang', 'barang.id = penerimaan.id_barang')
            ->join('lokasi', 'lokasi.id = penerimaan.id_lokasi')
            ->orderBy('penerimaan.created_at', 'DESC')
            ->findAll();
    }
}
