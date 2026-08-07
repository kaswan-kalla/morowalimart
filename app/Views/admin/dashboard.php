<?= $this->extend('layouts/header') ?>
<?= $this->section('title') ?>Admin Dashboard<?= $this->endSection() ?>
<?= $this->include('layouts/navbar') ?>
<?= $this->include('layouts/sidebar') ?>

<div class="main-content">
    <div class="container-fluid py-4">
        <h4 class="mb-4">Dashboard Admin</h4>

        <!-- Stats -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card shadow-sm border-0 bg-primary text-white">
                    <div class="card-body">
                        <small>Total User</small>
                        <h3 class="fw-bold mb-0"><?= $stats['total_users'] ?? 0 ?></h3>
                        <i class="bi bi-people fs-4"></i>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card shadow-sm border-0 bg-success text-white">
                    <div class="card-body">
                        <small>Total Toko</small>
                        <h3 class="fw-bold mb-0"><?= $stats['total_stores'] ?? 0 ?></h3>
                        <i class="bi bi-shop fs-4"></i>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card shadow-sm border-0 bg-warning text-white">
                    <div class="card-body">
                        <small>Total Produk</small>
                        <h3 class="fw-bold mb-0"><?= $stats['total_products'] ?? 0 ?></h3>
                        <i class="bi bi-box fs-4"></i>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card shadow-sm border-0 bg-info text-white">
                    <div class="card-body">
                        <small>Total Pesanan</small>
                        <h3 class="fw-bold mb-0"><?= $stats['total_orders'] ?? 0 ?></h3>
                        <i class="bi bi-receipt fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <small class="text-muted">Pendapatan Bulan Ini</small>
                        <h4 class="text-success fw-bold">Rp <?= number_format($stats['monthly_revenue'] ?? 0, 0, ',', '.') ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <small class="text-muted">Pembayaran Pending</small>
                        <h4 class="text-warning fw-bold"><?= $stats['pending_payments'] ?? 0 ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <small class="text-muted">Kategori</small>
                        <h4 class="text-primary fw-bold"><?= $stats['total_categories'] ?? 0 ?></h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Akses Cepat</h6>
                <div class="row g-2">
                    <div class="col-6 col-md-3"><a href="<?= base_url('admin/users') ?>" class="btn btn-outline-primary w-100"><i class="bi bi-people"></i> Kelola User</a></div>
                    <div class="col-6 col-md-3"><a href="<?= base_url('admin/stores') ?>" class="btn btn-outline-success w-100"><i class="bi bi-shop"></i> Kelola Toko</a></div>
                    <div class="col-6 col-md-3"><a href="<?= base_url('admin/products') ?>" class="btn btn-outline-warning w-100"><i class="bi bi-box"></i> Kelola Produk</a></div>
                    <div class="col-6 col-md-3"><a href="<?= base_url('admin/payments') ?>" class="btn btn-outline-info w-100"><i class="bi bi-credit-card"></i> Verifikasi Bayar</a></div>
                    <div class="col-6 col-md-3"><a href="<?= base_url('admin/reports/transactions') ?>" class="btn btn-outline-secondary w-100"><i class="bi bi-cart"></i> Pembelian</a></div>
                    <div class="col-6 col-md-3"><a href="<?= base_url('admin/reports/sales') ?>" class="btn btn-outline-danger w-100"><i class="bi bi-graph-up"></i> Penjualan</a></div>
                </div>
            </div>
        </div>
    </div>
</div>


<?= $this->include('layouts/scripts') ?>