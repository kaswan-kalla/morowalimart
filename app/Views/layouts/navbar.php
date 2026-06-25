<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="<?= base_url('/') ?>">
            <span class="logo-wrap bg-white rounded-2 p-1 d-inline-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                <img src="<?= asset_url('asset/img/logo.png') ?>" alt="Morowalimart" height="30" class="m-auto">
            </span>
            <div class="ms-2">
                <div class="fw-bold fs-5" style="margin-bottom:0px;">Morowali<span style="color: #fdc306ff;">mart</span></div>
                <small class="d-block" style="font-size:0.6rem;line-height:1.2;color: mistyrose;">Sinergi Dakwah </br> <span style="color: #fdc306ff;">Membangun Ekonomi Umat</span></small>
            </div>
        </a>
        <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Desktop: normal navbar -->
        <div class="collapse navbar-collapse d-none d-lg-flex" id="navbarNav">
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
                            <span class="badge bg-danger rounded-pill position-absolute translate-middle" id="cartBadge" style="font-size: 0.65rem; <?= get_cart_count() > 0 ? '' : 'display:none;' ?>">
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

                    <!-- User Avatar — buka sidebar -->
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNav" role="button" title="Menu Akun">
                            <?php if (!empty($user['photo'])): ?>
                                <img src="<?= base_url($user['photo']) ?>" class="rounded-circle me-1" width="28" height="28" alt="Avatar">
                            <?php else: ?>
                                <i class="bi bi-person-circle me-1"></i>
                            <?php endif; ?>
                            <span class="d-none d-xl-inline"><?= esc($user['name']) ?></span>
                        </a>
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

<!-- Sidebar Kiri (Desktop & Mobile) -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNav" style="width: 280px;">
    <div class="offcanvas-header bg-primary text-white">
        <?php if (is_logged_in()): ?>
            <?php $user = get_user(); ?>
            <div class="d-flex align-items-center">
                <?php if (!empty($user['photo'])): ?>
                    <img src="<?= base_url($user['photo']) ?>" class="rounded-circle me-2" width="40" height="40" alt="Avatar" style="object-fit:cover;">
                <?php else: ?>
                    <i class="bi bi-person-circle fs-3 me-2"></i>
                <?php endif; ?>
                <div>
                    <div class="fw-bold"><?= esc($user['name']) ?></div>
                    <small class="opacity-75"><?= esc($user['email'] ?? '') ?></small>
                </div>
            </div>
        <?php else: ?>
            <h5 class="offcanvas-title mb-0">Menu</h5>
        <?php endif; ?>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0">
        <!-- Search Form -->
        <div class="p-3 border-bottom">
            <form id="searchFormMobile">
                <div class="input-group">
                    <input type="text" class="form-control" id="searchInputMobile" placeholder="Cari produk..." value="<?= esc($search_query ?? '') ?>">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>

        <ul class="nav flex-column py-2">
            <li class="nav-item">
                <a class="nav-link sidebar-link px-3 py-2" href="<?= base_url('produk') ?>">
                    <i class="bi bi-grid me-2"></i> Produk
                </a>
            </li>

            <?php if (is_logged_in()): ?>
                <li class="nav-item">
                    <div class="px-3 py-2 small text-uppercase text-muted fw-bold">Akun Saya</div>
                </li>
                <li class="nav-item">
                    <a class="nav-link sidebar-link px-3 py-2" href="<?= base_url('profile') ?>">
                        <i class="bi bi-person me-2"></i>Profil
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link sidebar-link px-3 py-2" href="<?= base_url('cart') ?>">
                        <i class="bi bi-cart3 me-2"></i>Keranjang
                        <span class="badge bg-danger rounded-pill float-end" id="cartBadgeMobile" style="<?= get_cart_count() > 0 ? '' : 'display:none;' ?>"><?= get_cart_count() ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link sidebar-link px-3 py-2" href="<?= base_url('wishlist') ?>">
                        <i class="bi bi-heart me-2"></i>Wishlist
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link sidebar-link px-3 py-2" href="<?= base_url('order') ?>">
                        <i class="bi bi-bag me-2"></i>Pesanan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link sidebar-link px-3 py-2" href="<?= base_url('address') ?>">
                        <i class="bi bi-geo-alt me-2"></i>Alamat
                    </a>
                </li>

                <?php if (is_seller()): ?>
                    <li class="nav-item">
                        <div class="px-3 py-2 small text-uppercase text-muted fw-bold mt-2">Seller</div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link sidebar-link px-3 py-2" href="<?= base_url('seller/dashboard') ?>">
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard Seller
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (is_admin()): ?>
                    <li class="nav-item">
                        <div class="px-3 py-2 small text-uppercase text-muted fw-bold mt-2">Admin</div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link sidebar-link px-3 py-2" href="<?= base_url('admin/dashboard') ?>">
                            <i class="bi bi-shield-lock me-2"></i>Admin Panel
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (!is_seller() && !is_admin()): ?>
                    <li class="nav-item">
                        <a class="nav-link sidebar-link px-3 py-2" href="<?= base_url('seller/toko') ?>">
                            <i class="bi bi-shop-window me-2"></i>Buka Toko
                        </a>
                    </li>
                <?php endif; ?>

                <li class="nav-item border-top mt-3 pt-2">
                    <a class="nav-link sidebar-link px-3 py-2 text-danger" href="<?= base_url('logout') ?>">
                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                    </a>
                </li>

            <?php else: ?>
                <li class="nav-item border-top mt-2">
                    <a class="nav-link sidebar-link px-3 py-2" href="<?= base_url('login') ?>">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Login
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link sidebar-link px-3 py-2" href="<?= base_url('register') ?>">
                        <i class="bi bi-person-plus me-2"></i>Daftar
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container"></div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>