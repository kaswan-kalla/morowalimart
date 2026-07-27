<?= $this->extend('layouts/header') ?>
<?= $this->section('title') ?>Laporan Laba Rugi<?= $this->endSection() ?>
<?= $this->include('layouts/navbar') ?>
<?= $this->include('layouts/sidebar') ?>
<?php
/** @var float $total_penjualan */
/** @var float $total_hpp */
/** @var float $laba_kotor */
/** @var array $perOutlet */
?>

<div class="main-content">
    <div class="container-fluid py-4">
        <!-- Kop surat hanya saat print -->
        <div class="print-kop">
            <h2 class="text-center mb-1">UD. MOROWALIMART</h2>
            <p class="text-center mb-1">Jl. Trans Sulawesi Bahodopi - Morowali</p>
            <hr class="my-2">
            <h4 class="text-center print-title">Laporan Laba Rugi</h4>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0"></h4>
            <button class="btn btn-outline-primary" onclick="window.print()"><i class="bi bi-printer"></i> Cetak</button>
        </div>

        <!-- Ringkasan -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 bg-success text-white">
                    <div class="card-body">
                        <h6 class="card-title mb-1"><i class="bi bi-arrow-up-circle me-1"></i>Total Penjualan</h6>
                        <h3 class="mb-0"><?= number_format($total_penjualan, 0, ',', '.') ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 bg-danger text-white">
                    <div class="card-body">
                        <h6 class="card-title mb-1"><i class="bi bi-arrow-down-circle me-1"></i>HPP (Harga Pokok Penjualan)</h6>
                        <h3 class="mb-0"><?= number_format($total_hpp, 0, ',', '.') ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 bg-<?= $laba_kotor >= 0 ? 'primary' : 'warning' ?> text-white">
                    <div class="card-body">
                        <h6 class="card-title mb-1"><i class="bi bi-graph-up-arrow me-1"></i>Laba / Rugi Kotor</h6>
                        <h3 class="mb-0"><?= number_format(abs($laba_kotor), 0, ',', '.') ?></h3>
                        <small><?= $laba_kotor >= 0 ? 'Laba' : 'Rugi' ?></small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail per Outlet -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-shop me-2"></i>Detail Laba Rugi per Outlet</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Outlet</th>
                                <th class="text-end">Total Penjualan</th>
                                <th class="text-end">HPP</th>
                                <th class="text-end">Laba / Rugi</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($perOutlet)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada data</td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; ?>
                                <?php foreach ($perOutlet as $o):
                                    $laba = $o['total_penjualan'] - $o['total_hpp'];
                                ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><strong><?= htmlspecialchars($o['nama_lokasi']) ?></strong></td>
                                        <td class="text-end"><?= number_format($o['total_penjualan'], 0, ',', '.') ?></td>
                                        <td class="text-end"><?= number_format($o['total_hpp'], 0, ',', '.') ?></td>
                                        <td class="text-end fw-bold <?= $laba >= 0 ? 'text-success' : 'text-danger' ?>">
                                            <?= number_format(abs($laba), 0, ',', '.') ?>
                                        </td>
                                        <td><span class="badge bg-<?= $laba >= 0 ? 'success' : 'danger' ?>"><?= $laba >= 0 ? 'Laba' : 'Rugi' ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .print-kop {
        display: none;
    }

    .print-title {
        display: none;
    }

    @media print {
        @page {
            margin: 15mm;
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

        .bg-success,
        .bg-danger,
        .bg-primary,
        .bg-warning {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // nothing
    });
</script>
<?= $this->endSection() ?>
<?= $this->include('layouts/scripts') ?>