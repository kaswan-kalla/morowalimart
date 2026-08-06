<?= $this->extend('layouts/header') ?>
<?= $this->section('title') ?>Laporan Fee Outlet<?= $this->endSection() ?>
<?= $this->include('layouts/navbar') ?>
<?= $this->include('layouts/sidebar') ?>
<?php
/** @var array $perOutlet */
/** @var string $bulan */
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
            <h4 class="text-center print-title">Laporan Fee Outlet</h4>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="ms-auto">
                <button class="btn btn-outline-primary" onclick="openPrintPreview()"><i class="bi bi-printer"></i> Cetak</button>
                <button class="btn btn-outline-success" onclick="copyReportImage()"><i class="bi bi-clipboard"></i> Salin Gambar</button>
            </div>
        </div>

        <!-- Filter periode bulan/tahun -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="get" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label mb-1">Bulan</label>
                        <select name="bulan" class="form-select">
                            <?php foreach ($namaBulan as $i => $n): ?>
                                <?php $val = str_pad($i + 1, 2, '0', STR_PAD_LEFT); ?>
                                <option value="<?= $val ?>" <?= $bulan == $val ? 'selected' : '' ?>><?= $n ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1">Tahun</label>
                        <select name="tahun" class="form-select">
                            <?php for ($i = (int)date('Y'); $i >= (int)date('Y') - 5; $i--): ?>
                                <option value="<?= $i ?>" <?= (int)$tahun == $i ? 'selected' : '' ?>><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary me-1"><i class="bi bi-funnel me-1"></i>Filter</button>
                        <a href="<?= base_url('admin/laporan-fee') ?>" class="btn btn-outline-secondary"><i class="bi bi-x-circle me-1"></i>Reset</a>
                        <span class="badge bg-info text-dark ms-2"><?= $namaBulan[(int)$bulan - 1] ?> <?= $tahun ?></span>
                    </div>
                </form>
            </div>
        </div>

        <?php
        $grandTotal = 0;
        foreach ($perOutlet as $lok) {
            foreach ($lok['rows'] as $r) {
                $grandTotal += $r['total_fee'];
            }
        }
        ?>

        <!-- Ringkasan global -->
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-globe me-2"></i>Total Fee Semua Outlet</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Outlet</th>
                                <th class="text-end">Jumlah Barang Terjual</th>
                                <th class="text-end">Total Fee</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $totalItemGlobal = 0;
                            $totalFeeGlobal = 0;
                            ?>
                            <?php foreach ($perOutlet as $lok): ?>
                                <?php
                                $totalItem = 0;
                                $totalFee = 0;
                                foreach ($lok['rows'] as $r) {
                                    $totalItem += $r['jumlah'];
                                    $totalFee += $r['total_fee'];
                                }
                                $totalItemGlobal += $totalItem;
                                $totalFeeGlobal += $totalFee;
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><strong><?= htmlspecialchars($lok['nama_lokasi']) ?></strong></td>
                                    <td class="text-end"><?= number_format($totalItem, 0, ',', '.') ?></td>
                                    <td class="text-end"><?= number_format($totalFee, 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="2" class="text-end">Grand Total</th>
                                <th class="text-end"><?= number_format($totalItemGlobal, 0, ',', '.') ?></th>
                                <th class="text-end"><?= number_format($totalFeeGlobal, 0, ',', '.') ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Detail per outlet -->
        <?php foreach ($perOutlet as $lok): ?>
            <?php
            $totalItem = 0;
            $totalFee = 0;
            foreach ($lok['rows'] as $r) {
                $totalItem += $r['jumlah'];
                $totalFee += $r['total_fee'];
            }
            ?>
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-shop me-2"></i><?= htmlspecialchars($lok['nama_lokasi']) ?></h5>
                </div>
                <div class="card-body">
                    <?php if (empty($lok['rows'])): ?>
                        <p class="text-muted mb-0">Belum ada penjualan di outlet ini.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Barang</th>
                                        <th class="text-end">Jumlah Terjual</th>
                                        <th class="text-end">Fee / Unit</th>
                                        <th class="text-end">Total Fee</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; ?>
                                    <?php foreach ($lok['rows'] as $r): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><strong><?= htmlspecialchars($r['nama_barang']) ?></strong></td>
                                            <td class="text-end"><?= number_format($r['jumlah'], 0, ',', '.') ?></td>
                                            <td class="text-end"><?= number_format($r['fee_unit'], 0, ',', '.') ?></td>
                                            <td class="text-end"><?= number_format($r['total_fee'], 0, ',', '.') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="2" class="text-end">Total <?= htmlspecialchars($lok['nama_lokasi']) ?></th>
                                        <th class="text-end"><?= number_format($totalItem, 0, ',', '.') ?></th>
                                        <th></th>
                                        <th class="text-end"><?= number_format($totalFee, 0, ',', '.') ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($perOutlet)): ?>
            <div class="card shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-0">Belum ada data penjualan.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    /* Rata kanan kolom angka */
    .table thead th:nth-child(n+3),
    .table tbody td:nth-child(n+3) {
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
            box-shadow: none !important;
        }

        .table {
            border-collapse: collapse;
        }

        .table td,
        .table th {
            border: 1px solid #999 !important;
        }
    }
</style>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {});
</script>
<?= $this->endSection() ?>
<?= $this->include('layouts/scripts') ?>