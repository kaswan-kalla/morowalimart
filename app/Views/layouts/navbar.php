<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="<?= base_url('/') ?>">
            <span class="logo-wrap bg-white rounded-2 p-1 d-inline-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                <img src="<?= base_url('asset/img/logo.png') ?>" alt="Morowalimart" height="30" class="m-auto">
            </span>
            <span class="fw-bold fs-5 ms-2">Morowalimart2</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Search Form -->
            <form class="d-flex mx-auto" style="max-width: 400px; width: 100%;" id="searchForm">
                <div class="input-group">
                    <input type="text" class="form-control" id="searchInput" placeholder="Cari produk..." value="<?= esc($search_query ?? '') ?>">
                    <button class="btn btn-light" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>

            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('produk') ?>">
                        <i class="bi bi-grid"></i> Produk
                    </a>
                </li>

                <?php if (is_logged_in()): ?>
                    <?php $user = get_user(); ?>

                    <!-- Cart -->
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="<?= base_url('cart') ?>">
                            <i class="bi bi-cart3"></i>
                            <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle" id="cartBadge" style="font-size: 0.65rem;">
                                <?= get_cart_count() ?>
                            </span>
                        </a>
                    </li>

                    <!-- Wishlist -->
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('wishlist') ?>">
                            <i class="bi bi-heart"></i>
                        </a>
                    </li>

                    <!-- Dropdown User -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                            <?php if (!empty($user['photo'])): ?>
                                <img src="<?= base_url($user['photo']) ?>" class="rounded-circle me-1" width="28" height="28" alt="Avatar">
                            <?php else: ?>
                                <i class="bi bi-person-circle me-1"></i>
                            <?php endif; ?>
                            <?= esc($user['name']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= base_url('profile') ?>"><i class="bi bi-person me-2"></i>Profil</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('order') ?>"><i class="bi bi-bag me-2"></i>Pesanan</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('address') ?>"><i class="bi bi-geo-alt me-2"></i>Alamat</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <?php if (is_seller()): ?>
                                <li><a class="dropdown-item" href="<?= base_url('seller/dashboard') ?>"><i class="bi bi-speedometer2 me-2"></i>Dashboard Seller</a></li>
                            <?php endif; ?>
                            <?php if (is_admin()): ?>
                                <li><a class="dropdown-item" href="<?= base_url('admin/dashboard') ?>"><i class="bi bi-shield-lock me-2"></i>Admin Panel</a></li>
                            <?php endif; ?>

                            <?php if (!is_seller() && !is_admin()): ?>
                                <li><a class="dropdown-item" href="<?= base_url('seller/toko') ?>"><i class="bi bi-shop-window me-2"></i>Buka Toko</a></li>
                            <?php endif; ?>

                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="<?= base_url('logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-light btn-sm ms-2 px-3" href="<?= base_url('login') ?>">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-light btn-sm ms-2 px-3" href="<?= base_url('register') ?>">
                            <i class="bi bi-person-plus me-1"></i>Daftar
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Toast Container -->
<div class="toast-container"></div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>