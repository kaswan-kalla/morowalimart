<?php

namespace App\Models;

use CodeIgniter\Model;

class PengeluaranModel extends Model
{
    protected $table            = 'pengeluaran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['id_barang', 'id_lokasi', 'harga_jual', 'jumlah', 'tanggal', 'keterangan'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'id_barang'  => 'required|numeric',
        'id_lokasi'  => 'required|numeric',
        'harga_jual' => 'required|numeric|greater_than_equal_to[0]',
        'jumlah'     => 'required|numeric|greater_than[0]',
        'tanggal'    => 'required|valid_date',
    ];

    public function getWithRelations()
    {
        return $this->select('pengeluaran.*, barang.nama_barang, lokasi.nama_lokasi')
            ->join('barang', 'barang.id = pengeluaran.id_barang')
            ->join('lokasi', 'lokasi.id = pengeluaran.id_lokasi')
            ->orderBy('pengeluaran.created_at', 'DESC')
            ->findAll();
    }
}
