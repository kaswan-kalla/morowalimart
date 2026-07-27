<?php

namespace App\Models;

use CodeIgniter\Model;

class MutasiModel extends Model
{
    protected $table            = 'mutasi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['id_barang', 'id_lokasi_asal', 'id_lokasi_tujuan', 'jumlah', 'tanggal', 'keterangan'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function getWithRelations()
    {
        return $this->select('mutasi.*, barang.nama_barang, asal.nama_lokasi as lokasi_asal, tujuan.nama_lokasi as lokasi_tujuan')
            ->join('barang', 'barang.id = mutasi.id_barang')
            ->join('lokasi asal', 'asal.id = mutasi.id_lokasi_asal')
            ->join('lokasi tujuan', 'tujuan.id = mutasi.id_lokasi_tujuan')
            ->orderBy('mutasi.created_at', 'DESC')
            ->findAll();
    }
}
