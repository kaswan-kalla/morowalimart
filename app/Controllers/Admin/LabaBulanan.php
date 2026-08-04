<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class LabaBulanan extends BaseController
{
    public function __construct()
    {
        $this->ensureFeeColumn();
    }

    private function ensureFeeColumn()
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
        $tahun = $this->request->getGet('tahun') ?: date('Y');

        // Penjualan per bulan
        $penjualanRows = $db->query(
            "SELECT MONTH(tanggal) as m, SUM(jumlah * harga_jual) as total
             FROM pengeluaran WHERE deleted_at IS NULL AND YEAR(tanggal) = ?
             GROUP BY MONTH(tanggal)",
            [$tahun]
        )->getResultArray();

        // HPP per bulan = jumlah terjual × harga_beli rata-rata per barang
        $hppRows = $db->query(
            "SELECT MONTH(pj.tanggal) as m, SUM(pj.jumlah * COALESCE(hb.avg_harga_beli, 0)) as total
             FROM pengeluaran pj
             LEFT JOIN (
                 SELECT id_barang, AVG(harga_beli) as avg_harga_beli
                 FROM penerimaan WHERE deleted_at IS NULL
                 GROUP BY id_barang
             ) hb ON hb.id_barang = pj.id_barang
             WHERE pj.deleted_at IS NULL AND YEAR(pj.tanggal) = ?
             GROUP BY MONTH(pj.tanggal)",
            [$tahun]
        )->getResultArray();

        // Fee per unit terbaru per barang (dari data pembelian)
        $feeRows = $db->query(
            "SELECT id_barang, fee_outlet FROM penerimaan WHERE deleted_at IS NULL AND fee_outlet > 0 ORDER BY tanggal DESC, id DESC"
        )->getResultArray();
        $feePerBarang = [];
        foreach ($feeRows as $f) {
            if (!isset($feePerBarang[$f['id_barang']])) {
                $feePerBarang[$f['id_barang']] = (float)$f['fee_outlet'];
            }
        }

        // Jumlah terjual per bulan per barang
        $jualRows = $db->query(
            "SELECT MONTH(tanggal) as m, id_barang, SUM(jumlah) as j
             FROM pengeluaran WHERE deleted_at IS NULL AND YEAR(tanggal) = ?
             GROUP BY MONTH(tanggal), id_barang",
            [$tahun]
        )->getResultArray();

        // Susun per bulan Jan-Des
        $bulanan = [];
        for ($m = 1; $m <= 12; $m++) {
            $bulanan[$m] = ['penjualan' => 0, 'hpp' => 0, 'fee' => 0];
        }
        foreach ($penjualanRows as $r) {
            $bulanan[(int)$r['m']]['penjualan'] = (float)$r['total'];
        }
        foreach ($hppRows as $r) {
            $bulanan[(int)$r['m']]['hpp'] = (float)$r['total'];
        }
        foreach ($jualRows as $r) {
            $feeUnit = isset($feePerBarang[$r['id_barang']]) ? $feePerBarang[$r['id_barang']] : 0;
            $bulanan[(int)$r['m']]['fee'] += $feeUnit * (int)$r['j'];
        }
        foreach ($bulanan as &$b) {
            $b['laba_kotor'] = $b['penjualan'] - $b['hpp'];
            $b['laba_bersih'] = $b['laba_kotor'] - $b['fee'];
        }
        unset($b);

        return view('admin/laba_bulanan/index', [
            'meta_title' => 'Laporan Laba Bulanan',
            'bulanan'    => $bulanan,
            'tahun'      => $tahun,
        ]);
    }
}
