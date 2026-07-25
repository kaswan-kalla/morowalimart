<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class LabaRugi extends BaseController
{
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

        $labaKotor = $totalPenjualan - $totalHpp;

        // Per outlet
        $perOutlet = $db->query(
            "SELECT l.nama_lokasi,
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

        return view('admin/laba_rugi/index', [
            'meta_title'      => 'Laporan Laba Rugi',
            'total_penjualan' => (float) $totalPenjualan,
            'total_hpp'       => (float) $totalHpp,
            'laba_kotor'      => (float) $labaKotor,
            'perOutlet'       => $perOutlet,
        ]);
    }
}
