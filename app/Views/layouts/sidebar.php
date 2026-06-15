<!-- Sidebar untuk Dashboard Seller/Admin -->
<div class="sidebar py-3">
    <?php
    $currentPath = service('uri')->getPath();
    $isActive = function ($path) use ($currentPath) {
        return strpos($currentPath, $path) !== false ? 'active' : '';
    };
    ?>

    <?php if (is_seller() && !is_admin()): ?>
        <!-- Seller Sidebar -->
        <h6 class="px-3 text-muted text-uppercase small mb-3">Dashboard Seller</h6>
        <nav class="nav flex-column">
            <a class="nav-link <?= $isActive('seller/dashboard') ?>" href="<?= base_url('seller/dashboard') ?>">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </a>
            <a class="nav-link <?= $isActive('seller/products') || $isActive('seller/produk') ?>" href="<?= base_url('seller/products') ?>">
                <i class="bi bi-box me-2"></i>Produk Saya
            </a>
            <a class="nav-link <?= $isActive('seller/orders') || $isActive('seller/pesanan') ?>" href="<?= base_url('seller/orders') ?>">
                <i class="bi bi-bag-check me-2"></i>Pesanan
            </a>
            <a class="nav-link <?= $isActive('seller/store') || $isActive('seller/toko') ?>" href="<?= base_url('seller/store') ?>">
                <i class="bi bi-shop me-2"></i>Pengaturan Toko
            </a>
        </nav>
    <?php endif; ?>

    <?php if (is_admin()): ?>
        <!-- Admin Sidebar -->
        <h6 class="px-3 text-muted text-uppercase small mb-3">Admin Panel</h6>
        <nav class="nav flex-column">
            <a class="nav-link <?= $isActive('admin/dashboard') ?>" href="<?= base_url('admin/dashboard') ?>">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </a>
            <a class="nav-link <?= $isActive('admin/users') ?>" href="<?= base_url('admin/users') ?>">
                <i class="bi bi-people me-2"></i>Manajemen User
            </a>
            <a class="nav-link <?= $isActive('admin/stores') ?>" href="<?= base_url('admin/stores') ?>">
                <i class="bi bi-shop me-2"></i>Manajemen Toko
            </a>
            <a class="nav-link <?= $isActive('admin/products') ?>" href="<?= base_url('admin/products') ?>">
                <i class="bi bi-box me-2"></i>Manajemen Produk
            </a>
            <a class="nav-link <?= $isActive('admin/categories') ?>" href="<?= base_url('admin/categories') ?>">
                <i class="bi bi-tags me-2"></i>Kategori
            </a>
            <a class="nav-link <?= $isActive('admin/payments') ?>" href="<?= base_url('admin/payments') ?>">
                <i class="bi bi-credit-card me-2"></i>Verifikasi Pembayaran
            </a>
            <a class="nav-link <?= $isActive('admin/vouchers') ?>" href="<?= base_url('admin/vouchers') ?>">
                <i class="bi bi-ticket-perforated me-2"></i>Voucher
            </a>
            <li class="nav-item">
                <h6 class="px-3 text-muted text-uppercase small mt-3 mb-2">Laporan</h6>
            </li>
            <a class="nav-link <?= $isActive('admin/reports/sales') ?>" href="<?= base_url('admin/reports/sales') ?>">
                <i class="bi bi-graph-up me-2"></i>Laporan Penjualan
            </a>
            <a class="nav-link <?= $isActive('admin/reports/transactions') ?>" href="<?= base_url('admin/reports/transactions') ?>">
                <i class="bi bi-receipt me-2"></i>Laporan Transaksi
            </a>
            <a class="nav-link <?= $isActive('admin/survey') ?>" href="<?= base_url('admin/survey') ?>">
                <i class="bi bi-bar-chart me-2"></i>Data Survey
            </a>
            <a class="nav-link " href="<?= base_url('admin/users') ?>?"><i class="bi bi-person-badge me-2"></i>Kelola Kurir</a>
        </nav>
    <?php endif; ?>

    <hr class="my-3">
    <nav class="nav flex-column">
        <a class="nav-link" href="<?= base_url('/') ?>">
            <i class="bi bi-house me-2"></i>Ke Beranda
        </a>
    </nav>
</div>