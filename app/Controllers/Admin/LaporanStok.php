<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class LaporanStok extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        $lokasi = $db->query("SELECT * FROM lokasi WHERE deleted_at IS NULL ORDER BY nama_lokasi")->getResultArray();

        $perLokasi = [];
        foreach ($lokasi as $l) {
            $stok = $db->query("
                SELECT
                    b.id,
                    b.nama_barang,
                    b.satuan,
                    COALESCE(p.jumlah, 0) as total_masuk,
                    COALESCE(k.jumlah, 0) as total_keluar,
                    COALESCE(p.jumlah, 0) - COALESCE(k.jumlah, 0) as stok,
                    COALESCE(p.harga_beli, 0) as harga_beli,
                    COALESCE(k.harga_jual, p.harga_jual, 0) as harga_jual
                FROM barang b
                LEFT JOIN (
                    SELECT id_barang, SUM(jumlah) as jumlah, AVG(harga_beli) as harga_beli, AVG(harga_jual) as harga_jual
                    FROM penerimaan WHERE deleted_at IS NULL AND id_lokasi = ?
                    GROUP BY id_barang
                ) p ON p.id_barang = b.id
                LEFT JOIN (
                    SELECT id_barang, SUM(jumlah) as jumlah, AVG(harga_jual) as harga_jual
                    FROM pengeluaran WHERE deleted_at IS NULL AND id_lokasi = ?
                    GROUP BY id_barang
                ) k ON k.id_barang = b.id
                WHERE b.deleted_at IS NULL
                    AND (COALESCE(p.jumlah, 0) > 0 OR COALESCE(k.jumlah, 0) > 0)
                ORDER BY b.nama_barang ASC
            ", [$l['id'], $l['id']])->getResultArray();

            $perLokasi[] = [
                'lokasi' => $l,
                'stok'   => $stok,
            ];
        }

        // Akumulasi stok global (semua outlet digabung per barang)
        $stokGlobal = $db->query("
            SELECT
                b.id,
                b.nama_barang,
                b.satuan,
                COALESCE(p.jumlah, 0) as total_masuk,
                COALESCE(k.jumlah, 0) as total_keluar,
                COALESCE(p.jumlah, 0) - COALESCE(k.jumlah, 0) as stok,
                COALESCE(p.harga_beli, 0) as harga_beli,
                COALESCE(k.harga_jual, p.harga_jual, 0) as harga_jual
            FROM barang b
            LEFT JOIN (
                SELECT id_barang, SUM(jumlah) as jumlah, AVG(harga_beli) as harga_beli, AVG(harga_jual) as harga_jual
                FROM penerimaan WHERE deleted_at IS NULL
                GROUP BY id_barang
            ) p ON p.id_barang = b.id
            LEFT JOIN (
                SELECT id_barang, SUM(jumlah) as jumlah, AVG(harga_jual) as harga_jual
                FROM pengeluaran WHERE deleted_at IS NULL
                GROUP BY id_barang
            ) k ON k.id_barang = b.id
            WHERE b.deleted_at IS NULL
                AND (COALESCE(p.jumlah, 0) > 0 OR COALESCE(k.jumlah, 0) > 0)
            ORDER BY b.nama_barang ASC
        ")->getResultArray();

        return view('admin/laporan_stok/index', [
            'meta_title' => 'Laporan Stok',
            'perLokasi'  => $perLokasi,
            'stokGlobal' => $stokGlobal,
        ]);
    }
}
