<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LokasiModel;

class LaporanFee extends BaseController
{
    protected $lokasiModel;

    public function __construct()
    {
        $this->lokasiModel = new LokasiModel();
        $this->ensureTables();
    }

    private function ensureTables()
    {
        $db = \Config\Database::connect();
        $exists = $db->query("SELECT COUNT(*) as c FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'penerimaan' AND COLUMN_NAME = 'fee_outlet'")->getRow()->c;
        if (!$exists) {
            $db->query("ALTER TABLE penerimaan ADD COLUMN fee_outlet DECIMAL(14,2) NOT NULL DEFAULT 0 AFTER harga_jual");
        }
    }

    public function index()
    {
        $db = \Config\Database::connect();

        // Filter periode penjualan
        $dari = $this->request->getGet('dari');
        $sampai = $this->request->getGet('sampai');
        $periodeSql = '';
        $params = [];
        if ($dari && $sampai) {
            $periodeSql = ' AND p.tanggal >= ? AND p.tanggal <= ?';
            $params = [$dari, $sampai];
        }

        // Fee terbaru per barang dari data pembelian (penerimaan)
        $feeRows = $db->query(
            "SELECT id_barang, fee_outlet FROM penerimaan WHERE deleted_at IS NULL AND fee_outlet > 0 ORDER BY tanggal DESC, id DESC"
        )->getResultArray();
        $feePerBarang = [];
        foreach ($feeRows as $f) {
            if (!isset($feePerBarang[$f['id_barang']])) {
                $feePerBarang[$f['id_barang']] = (float)$f['fee_outlet'];
            }
        }

        // Penjualan per outlet (filter periode)
        $penjualan = $db->query(
            "SELECT p.id_lokasi, l.nama_lokasi, p.id_barang, b.nama_barang, SUM(p.jumlah) as jumlah
             FROM pengeluaran p
             JOIN lokasi l ON l.id = p.id_lokasi
             JOIN barang b ON b.id = p.id_barang
             WHERE p.deleted_at IS NULL{$periodeSql}
             GROUP BY p.id_lokasi, l.nama_lokasi, p.id_barang, b.nama_barang
             ORDER BY l.nama_lokasi ASC, b.nama_barang ASC",
            $params
        )->getResultArray();

        // Susun per outlet: barang -> jumlah terjual, fee/unit, total fee
        $perOutlet = [];
        foreach ($penjualan as $p) {
            $feeUnit = isset($feePerBarang[$p['id_barang']]) ? $feePerBarang[$p['id_barang']] : 0;
            $perOutlet[$p['id_lokasi']]['nama_lokasi'] = $p['nama_lokasi'];
            $perOutlet[$p['id_lokasi']]['rows'][] = [
                'nama_barang' => $p['nama_barang'],
                'jumlah'      => (int)$p['jumlah'],
                'fee_unit'    => $feeUnit,
                'total_fee'   => $feeUnit * (int)$p['jumlah'],
            ];
        }

        return view('admin/laporan_fee/index', [
            'meta_title' => 'Laporan Fee Outlet',
            'perOutlet'  => $perOutlet,
            'dari'       => $dari,
            'sampai'     => $sampai,
        ]);
    }
}
