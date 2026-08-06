<?= $this->extend('layouts/header') ?>
<?= $this->section('title') ?>Laporan Laba Rugi<?= $this->endSection() ?>
<?= $this->include('layouts/navbar') ?>
<?= $this->include('layouts/sidebar') ?>
<?php
/** @var float $total_penjualan */
/** @var float $total_hpp */
/** @var float $total_fee */
/** @var float $laba_kotor */
/** @var float $laba_bersih */
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
            <button class="btn btn-outline-primary" onclick="openPrintPreview()"><i class="bi bi-printer"></i> Cetak</button>
            <button class="btn btn-outline-success" onclick="copyReportImage()"><i class="bi bi-clipboard"></i> Salin Gambar</button>
        </div>

        <!-- Ringkasan -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm border-0 bg-danger text-white">
                    <div class="card-body">
                        <h6 class="card-title mb-1"><i class="bi bi-arrow-down-circle me-1"></i>HPP (Harga Pokok Penjualan)</h6>
                        <h3 class="mb-0"><?= number_format($total_hpp, 0, ',', '.') ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 bg-info text-white">
                    <div class="card-body">
                        <h6 class="card-title mb-1"><i class="bi bi-graph-up me-1"></i>Laba Kotor</h6>
                        <h3 class="mb-0"><?= number_format($laba_kotor, 0, ',', '.') ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 bg-warning text-white">
                    <div class="card-body">
                        <h6 class="card-title mb-1"><i class="bi bi-cash-coin me-1"></i>Fee Outlet</h6>
                        <h3 class="mb-0"><?= number_format($total_fee, 0, ',', '.') ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 bg-<?= $laba_bersih >= 0 ? 'primary' : 'secondary' ?> text-white">
                    <div class="card-body">
                        <h6 class="card-title mb-1"><i class="bi bi-graph-up-arrow me-1"></i>Laba / Rugi Bersih</h6>
                        <h3 class="mb-0"><?= number_format(abs($laba_bersih), 0, ',', '.') ?></h3>
                        <small><?= $laba_bersih >= 0 ? 'Laba' : 'Rugi' ?></small>
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
                                <th class="text-end">Laba Kotor</th>
                                <th class="text-end">Fee Outlet</th>
                                <th class="text-end">Laba Bersih</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($perOutlet)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">Belum ada data</td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; ?>
                                <?php foreach ($perOutlet as $o):
                                    $labaKotor = $o['total_penjualan'] - $o['total_hpp'];
                                    $laba = $labaKotor - $o['total_fee'];
                                ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><strong><?= htmlspecialchars($o['nama_lokasi']) ?></strong></td>
                                        <td class="text-end"><?= number_format($o['total_penjualan'], 0, ',', '.') ?></td>
                                        <td class="text-end"><?= number_format($o['total_hpp'], 0, ',', '.') ?></td>
                                        <td class="text-end <?= $labaKotor >= 0 ? 'text-success' : 'text-danger' ?>">
                                            <?= number_format(abs($labaKotor), 0, ',', '.') ?>
                                        </td>
                                        <td class="text-end"><?= number_format($o['total_fee'], 0, ',', '.') ?></td>
                                        <td class="text-end fw-bold <?= $laba >= 0 ? 'text-success' : 'text-danger' ?>">
                                            <?= number_format(abs($laba), 0, ',', '.') ?>
                                        </td>
                                        <td><span class="badge bg-<?= $laba >= 0 ? 'success' : 'danger' ?>"><?= $laba >= 0 ? 'Laba' : 'Rugi' ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <?php if (!empty($perOutlet)): ?>
                            <?php
                            $gtPenjualan = 0;
                            $gtHpp = 0;
                            $gtLabaKotor = 0;
                            $gtFee = 0;
                            $gtLaba = 0;
                            foreach ($perOutlet as $o) {
                                $gtPenjualan += $o['total_penjualan'];
                                $gtHpp += $o['total_hpp'];
                                $gtLabaKotor += $o['total_penjualan'] - $o['total_hpp'];
                                $gtFee += $o['total_fee'];
                                $gtLaba += $o['total_penjualan'] - $o['total_hpp'] - $o['total_fee'];
                            }
                            ?>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <th colspan="2" class="text-end">Grand Total</th>
                                    <th class="text-end"><?= number_format($gtPenjualan, 0, ',', '.') ?></th>
                                    <th class="text-end"><?= number_format($gtHpp, 0, ',', '.') ?></th>
                                    <th class="text-end <?= $gtLabaKotor >= 0 ? 'text-success' : 'text-danger' ?>"><?= number_format(abs($gtLabaKotor), 0, ',', '.') ?></th>
                                    <th class="text-end"><?= number_format($gtFee, 0, ',', '.') ?></th>
                                    <th class="text-end <?= $gtLaba >= 0 ? 'text-success' : 'text-danger' ?>"><?= number_format(abs($gtLaba), 0, ',', '.') ?></th>
                                    <th><span class="badge bg-<?= $gtLaba >= 0 ? 'success' : 'danger' ?>"><?= $gtLaba >= 0 ? 'Laba' : 'Rugi' ?></span></th>
                                </tr>
                            </tfoot>
                        <?php endif; ?>
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

        .bg-success,
        .bg-danger,
        .bg-primary,
        .bg-warning,
        .bg-info,
        .bg-secondary {
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