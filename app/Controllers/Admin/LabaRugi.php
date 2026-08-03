<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class LabaRugi extends BaseController
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

        // Total Penjualan (Revenue)
        $totalPenjualan = $db->query(
            "SELECT COALESCE(SUM(jumlah * harga_jual), 0) as total
             FROM pengeluaran WHERE deleted_at IS NULL"
        )->getRow()->total;

        // HPP = jumlah terjual × harga_beli rata-rata per barang
        $totalHpp = $db->query(
            "SELECT COALESCE(SUM(pj.jumlah * COALESCE(hb.avg_harga_beli, 0)), 0) as total
             FROM pengeluaran pj
             LEFT JOIN (
                 SELECT id_barang, AVG(harga_beli) as avg_harga_beli
                 FROM penerimaan WHERE deleted_at IS NULL
                 GROUP BY id_barang
             ) hb ON hb.id_barang = pj.id_barang
             WHERE pj.deleted_at IS NULL"
        )->getRow()->total;

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

        // Total Fee Outlet = fee/unit × jumlah terjual
        $totalFee = 0;
        $jualPerBarang = $db->query(
            "SELECT id_barang, SUM(jumlah) as j FROM pengeluaran WHERE deleted_at IS NULL GROUP BY id_barang"
        )->getResultArray();
        foreach ($jualPerBarang as $r) {
            $totalFee += (isset($feePerBarang[$r['id_barang']]) ? $feePerBarang[$r['id_barang']] : 0) * (int)$r['j'];
        }

        $labaKotor = $totalPenjualan - $totalHpp;
        $labaBersih = $labaKotor - $totalFee;

        // Per outlet
        $perOutlet = $db->query(
            "SELECT l.id, l.nama_lokasi,
                    COALESCE((SELECT SUM(jumlah * harga_jual) FROM pengeluaran WHERE id_lokasi = l.id AND deleted_at IS NULL), 0) as total_penjualan,
                    COALESCE((SELECT SUM(pj.jumlah * COALESCE(hb.avg_harga_beli, 0))
                        FROM pengeluaran pj
                        LEFT JOIN (
                            SELECT id_barang, AVG(harga_beli) as avg_harga_beli
                            FROM penerimaan WHERE deleted_at IS NULL
                            GROUP BY id_barang
                        ) hb ON hb.id_barang = pj.id_barang
                        WHERE pj.id_lokasi = l.id AND pj.deleted_at IS NULL
                    ), 0) as total_hpp
             FROM lokasi l
             WHERE l.deleted_at IS NULL
             ORDER BY l.nama_lokasi"
        )->getResultArray();

        // Fee per outlet = fee/unit × jumlah terjual per outlet
        $jualPerOutlet = $db->query(
            "SELECT id_lokasi, id_barang, SUM(jumlah) as j FROM pengeluaran WHERE deleted_at IS NULL GROUP BY id_lokasi, id_barang"
        )->getResultArray();
        $feePerOutlet = [];
        foreach ($jualPerOutlet as $r) {
            if (!isset($feePerOutlet[$r['id_lokasi']])) {
                $feePerOutlet[$r['id_lokasi']] = 0;
            }
            $feePerOutlet[$r['id_lokasi']] += (isset($feePerBarang[$r['id_barang']]) ? $feePerBarang[$r['id_barang']] : 0) * (int)$r['j'];
        }
        foreach ($perOutlet as &$o) {
            $o['total_fee'] = isset($feePerOutlet[$o['id']]) ? $feePerOutlet[$o['id']] : 0;
        }
        unset($o);

        return view('admin/laba_rugi/index', [
            'meta_title'      => 'Laporan Laba Rugi',
            'total_penjualan' => (float) $totalPenjualan,
            'total_hpp'       => (float) $totalHpp,
            'total_fee'       => (float) $totalFee,
            'laba_kotor'      => (float) $labaKotor,
            'laba_bersih'     => (float) $labaBersih,
            'perOutlet'       => $perOutlet,
        ]);
    }
}
