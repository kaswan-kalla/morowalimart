<?= $this->extend('layouts/header') ?>
<?= $this->section('title') ?>Laporan Stok<?= $this->endSection() ?>
<?= $this->include('layouts/navbar') ?>
<?= $this->include('layouts/sidebar') ?>
<?php
/** @var array $perLokasi */
/** @var array $stokGlobal */
?>

<div class="main-content">
    <div class="container-fluid py-4">
        <!-- Kop surat hanya saat print -->
        <div class="print-kop">
            <h2 class="text-center mb-1">UD. MOROWALIMART</h2>
            <p class="text-center mb-1">Jl. Trans Sulawesi Bahodopi - Morowali</p>
            <hr class="my-2">
            <h4 class="text-center print-title">Laporan Stok Barang </h4>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="ms-auto">
                <button class="btn btn-outline-primary" onclick="openPrintPreview()"><i class="bi bi-printer"></i> Cetak</button>
                <button class="btn btn-outline-success" onclick="copyReportImage()"><i class="bi bi-clipboard"></i> Salin Gambar</button>
            </div>
        </div>

        <!-- Stok Global (akumulasi semua outlet) -->
        <div class="card shadow-sm mb-4 border-primary">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-globe me-2"></i>Stok Global (Akumulasi Semua Outlet)</h5>
            </div>
            <div class="card-body">
                <?php if (empty($stokGlobal)): ?>
                    <p class="text-muted mb-0">Belum ada data stok.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Barang</th>
                                    <th>Satuan</th>
                                    <th>Total Masuk</th>
                                    <th>Total Keluar</th>
                                    <th>Stok Akhir</th>
                                    <th>Harga Beli</th>
                                    <th>Harga Jual</th>
                                    <th>Nilai Stok Beli</th>
                                    <th>Nilai Stok Jual</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $noG = 1;
                                $totalNilaiG = 0;
                                $totalNilaiJualG = 0; ?>
                                <?php foreach ($stokGlobal as $s): ?>
                                    <?php
                                    $nilaiG = (int)$s['stok'] * (float)$s['harga_beli'];
                                    $nilaiJualG = (int)$s['stok'] * (float)$s['harga_jual'];
                                    $totalNilaiG += $nilaiG;
                                    $totalNilaiJualG += $nilaiJualG;
                                    ?>
                                    <tr>
                                        <td><?= $noG++ ?></td>
                                        <td><strong><?= htmlspecialchars($s['nama_barang']) ?></strong></td>
                                        <td><?= htmlspecialchars($s['satuan'] ?? 'pcs') ?></td>
                                        <td><?= number_format($s['total_masuk'], 0, ',', '.') ?></td>
                                        <td><?= number_format($s['total_keluar'], 0, ',', '.') ?></td>
                                        <td>
                                            <span class="badge bg-<?= $s['stok'] > 0 ? 'success' : ($s['stok'] < 0 ? 'danger' : 'secondary') ?>">
                                                <?= number_format($s['stok'], 0, ',', '.') ?>
                                            </span>
                                        </td>
                                        <td><?= number_format($s['harga_beli'], 0, ',', '.') ?></td>
                                        <td><?= number_format($s['harga_jual'], 0, ',', '.') ?></td>
                                        <td><?= number_format($nilaiG, 0, ',', '.') ?></td>
                                        <td><?= number_format($nilaiJualG, 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="8" class="text-end">Total Nilai Stok Global</th>
                                    <th class="text-end"><strong><?= number_format($totalNilaiG, 0, ',', '.') ?></strong></th>
                                    <th class="text-end"><strong><?= number_format($totalNilaiJualG, 0, ',', '.') ?></strong></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php foreach ($perLokasi as $item): ?>
            <?php $lok = $item['lokasi'];
            $stok = $item['stok']; ?>
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-shop me-2"></i><?= htmlspecialchars($lok['nama_lokasi']) ?></h5>
                </div>
                <div class="card-body">
                    <?php if (empty($stok)): ?>
                        <p class="text-muted mb-0">Belum ada stok barang di lokasi ini.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Barang</th>
                                        <th>Satuan</th>
                                        <th>Total Masuk</th>
                                        <th>Total Keluar</th>
                                        <th>Stok Akhir</th>
                                        <th>Harga Beli</th>
                                        <th>Harga Jual</th>
                                        <th>Nilai Stok Beli</th>
                                        <th>Nilai Stok Jual</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1;
                                    $totalNilai = 0;
                                    $totalNilaiJual = 0; ?>
                                    <?php foreach ($stok as $s): ?>
                                        <?php
                                        $nilai = (int)$s['stok'] * (float)$s['harga_beli'];
                                        $nilaiJual = (int)$s['stok'] * (float)$s['harga_jual'];
                                        $totalNilai += $nilai;
                                        $totalNilaiJual += $nilaiJual;
                                        ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><strong><?= htmlspecialchars($s['nama_barang']) ?></strong></td>
                                            <td><?= htmlspecialchars($s['satuan'] ?? 'pcs') ?></td>
                                            <td><?= number_format($s['total_masuk'], 0, ',', '.') ?></td>
                                            <td><?= number_format($s['total_keluar'], 0, ',', '.') ?></td>
                                            <td>
                                                <span class="badge bg-<?= $s['stok'] > 0 ? 'success' : ($s['stok'] < 0 ? 'danger' : 'secondary') ?>">
                                                    <?= number_format($s['stok'], 0, ',', '.') ?>
                                                </span>
                                            </td>
                                            <td><?= number_format($s['harga_beli'], 0, ',', '.') ?></td>
                                            <td><?= number_format($s['harga_jual'], 0, ',', '.') ?></td>
                                            <td><?= number_format($nilai, 0, ',', '.') ?></td>
                                            <td><?= number_format($nilaiJual, 0, ',', '.') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="8" class="text-end">Total Nilai Stok</th>
                                        <th class="text-end"><strong><?= number_format($totalNilai, 0, ',', '.') ?></strong></th>
                                        <th class="text-end"><strong><?= number_format($totalNilaiJual, 0, ',', '.') ?></strong></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
    /* Rata kanan kolom angka (Total Masuk s/d Nilai Stok) */
    .table thead th:nth-child(n+4),
    .table tbody td:nth-child(n+4) {
        text-align: right;
    }

    .print-kop {
        display: none;
    }

    .print-title {
        display: none;
    }

    @media print {
        @page {
            size: 21cm 33cm;
            margin: 10mm;
        }

        .sidebar,
        .navbar,
        .btn-outline-primary,
        .toast-container,
        .loading-overlay {
            display: none !important;
        }

        .main-content {
            margin-left: 0 !important;
        }

        .print-kop {
            display: block !important;
            text-align: center;
            margin-bottom: 10px;
        }

        .print-kop h2 {
            font-size: 22pt;
            margin: 0;
            font-weight: bold;
        }

        .print-kop p {
            font-size: 11pt;
            margin: 2px 0;
        }

        .print-kop hr {
            border-top: 2px solid #000;
            margin-bottom: 8px;
        }

        .print-title {
            display: block !important;
            text-align: center;
            font-size: 14pt;
            margin-bottom: 15px;
        }

        .card {
            break-inside: avoid;
            border: 1px solid #ddd !important;
        }

        .badge.bg-success,
        .badge.bg-danger,
        .badge.bg-secondary {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>

<?= $this->section('scripts') ?>
<script>
    // Print styling
    $(document).ready(function() {
        // No special actions needed
    });
</script>
<?= $this->endSection() ?>
<?= $this->include('layouts/scripts') ?>