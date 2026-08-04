<?= $this->extend('layouts/header') ?>
<?= $this->section('title') ?>Laporan Laba Bulanan<?= $this->endSection() ?>
<?= $this->include('layouts/navbar') ?>
<?= $this->include('layouts/sidebar') ?>
<?php
/** @var array $bulanan */
/** @var string $tahun */
$namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
?>

<div class="main-content">
    <div class="container-fluid py-4">
        <!-- Kop surat hanya saat print -->
        <div class="print-kop">
            <h2 class="text-center mb-1">UD. MOROWALIMART</h2>
            <p class="text-center mb-1">Jl. Trans Sulawesi Bahodopi - Morowali</p>
            <hr class="my-2">
            <h4 class="text-center print-title">Laporan Laba Bulanan</h4>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <button class="btn btn-outline-primary" onclick="window.print()"><i class="bi bi-printer"></i> Cetak</button>
            </div>
        </div>

        <!-- Filter tahun -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="get" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label mb-1">Tahun</label>
                        <select name="tahun" class="form-select">
                            <?php for ($i = (int)date('Y'); $i >= (int)date('Y') - 5; $i--): ?>
                                <option value="<?= $i ?>" <?= (int)$tahun == $i ? 'selected' : '' ?>><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-9">
                        <button type="submit" class="btn btn-primary me-1"><i class="bi bi-funnel me-1"></i>Filter</button>
                        <a href="<?= base_url('admin/laba-bulanan') ?>" class="btn btn-outline-secondary"><i class="bi bi-x-circle me-1"></i>Reset</a>
                        <span class="badge bg-info text-dark ms-2">Tahun <?= htmlspecialchars($tahun) ?></span>
                    </div>
                </form>
            </div>
        </div>

        <?php
        $gtPenjualan = 0;
        $gtHpp = 0;
        $gtLabaKotor = 0;
        $gtFee = 0;
        $gtLabaBersih = 0;
        foreach ($bulanan as $b) {
            $gtPenjualan += $b['penjualan'];
            $gtHpp += $b['hpp'];
            $gtLabaKotor += $b['laba_kotor'];
            $gtFee += $b['fee'];
            $gtLabaBersih += $b['laba_bersih'];
        }
        ?>

        <!-- Ringkasan -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm border-0 bg-danger text-white">
                    <div class="card-body">
                        <h6 class="card-title mb-1"><i class="bi bi-arrow-down-circle me-1"></i>HPP (Harga Pokok Penjualan)</h6>
                        <h3 class="mb-0"><?= number_format($gtHpp, 0, ',', '.') ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 bg-info text-white">
                    <div class="card-body">
                        <h6 class="card-title mb-1"><i class="bi bi-graph-up me-1"></i>Laba Kotor</h6>
                        <h3 class="mb-0"><?= number_format($gtLabaKotor, 0, ',', '.') ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 bg-warning text-white">
                    <div class="card-body">
                        <h6 class="card-title mb-1"><i class="bi bi-cash-coin me-1"></i>Fee Outlet</h6>
                        <h3 class="mb-0"><?= number_format($gtFee, 0, ',', '.') ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 bg-<?= $gtLabaBersih >= 0 ? 'primary' : 'secondary' ?> text-white">
                    <div class="card-body">
                        <h6 class="card-title mb-1"><i class="bi bi-graph-up-arrow me-1"></i>Laba / Rugi Bersih</h6>
                        <h3 class="mb-0"><?= number_format(abs($gtLabaBersih), 0, ',', '.') ?></h3>
                        <small><?= $gtLabaBersih >= 0 ? 'Laba' : 'Rugi' ?></small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail per Bulan -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-calendar-month me-2"></i>Detail Laba Bulanan Tahun <?= htmlspecialchars($tahun) ?></h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Bulan</th>
                                <th class="text-end">Total Penjualan</th>
                                <th class="text-end">HPP</th>
                                <th class="text-end">Laba Kotor</th>
                                <th class="text-end">Fee Outlet</th>
                                <th class="text-end">Laba Bersih</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($bulanan as $m => $b): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><strong><?= $namaBulan[$m - 1] ?></strong></td>
                                    <td class="text-end"><?= number_format($b['penjualan'], 0, ',', '.') ?></td>
                                    <td class="text-end"><?= number_format($b['hpp'], 0, ',', '.') ?></td>
                                    <td class="text-end <?= $b['laba_kotor'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                        <?= number_format(abs($b['laba_kotor']), 0, ',', '.') ?>
                                    </td>
                                    <td class="text-end"><?= number_format($b['fee'], 0, ',', '.') ?></td>
                                    <td class="text-end fw-bold <?= $b['laba_bersih'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                        <?= number_format(abs($b['laba_bersih']), 0, ',', '.') ?>
                                    </td>
                                    <td><span class="badge bg-<?= $b['laba_bersih'] >= 0 ? 'success' : 'danger' ?>"><?= $b['laba_bersih'] >= 0 ? 'Laba' : 'Rugi' ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <th colspan="2" class="text-end">Grand Total</th>
                                <th class="text-end"><?= number_format($gtPenjualan, 0, ',', '.') ?></th>
                                <th class="text-end"><?= number_format($gtHpp, 0, ',', '.') ?></th>
                                <th class="text-end <?= $gtLabaKotor >= 0 ? 'text-success' : 'text-danger' ?>"><?= number_format(abs($gtLabaKotor), 0, ',', '.') ?></th>
                                <th class="text-end"><?= number_format($gtFee, 0, ',', '.') ?></th>
                                <th class="text-end <?= $gtLabaBersih >= 0 ? 'text-success' : 'text-danger' ?>"><?= number_format(abs($gtLabaBersih), 0, ',', '.') ?></th>
                                <th><span class="badge bg-<?= $gtLabaBersih >= 0 ? 'success' : 'danger' ?>"><?= $gtLabaBersih >= 0 ? 'Laba' : 'Rugi' ?></span></th>
                            </tr>
                        </tfoot>
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
            box-shadow: none !important;
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
    $(document).ready(function() {});
</script>
<?= $this->endSection() ?>
<?= $this->include('layouts/scripts') ?>