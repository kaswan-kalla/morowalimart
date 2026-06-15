<?php

/** @var array $categories */
/** @var array $latest_products */
/** @var array $popular_products */
?>

<!-- Hero Section -->
<div class="bg-primary text-white py-5 mb-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="fw-bold">Belanja Online Terpercaya</h1>
                <p class="lead">Temukan produk terbaik dari kami. Harga bersaing, kualitas terjamin.</p>
                <a href="<?= base_url('produk') ?>" class="btn btn-light btn-lg">
                    <i class="bi bi-grid me-1"></i> Jelajahi Produk
                </a>
            </div>
            <div class="col-md-6 text-center d-none d-md-block">
                <i class="bi bi-shop-window" style="font-size: 8rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- Kategori -->
    <section class="mb-5">
        <h4 class="fw-bold mb-3"><i class="bi bi-tags me-2"></i>Kategori</h4>
        <div class="row g-3">
            <?php foreach ($categories as $cat): ?>
                <div class="col-6 col-md-2">
                    <a href="<?= base_url('kategori/' . $cat['slug']) ?>" class="text-decoration-none">
                        <div class="card text-center p-3 h-100">
                            <div class="card-body">
                                <?php if ($cat['icon']): ?>
                                    <img src="<?= base_url($cat['icon']) ?>" alt="<?= esc($cat['name']) ?>" class="mb-2" style="width:40px;height:40px;object-fit:cover;">
                                <?php else: ?>
                                    <i class="bi bi-tag fs-1 text-primary"></i>
                                <?php endif; ?>
                                <p class="mb-0 small fw-semibold text-dark"><?= esc($cat['name']) ?></p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Produk Terbaru -->
    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i>Produk Terbaru</h4>
            <a href="<?= base_url('produk') ?>" class="btn btn-outline-primary btn-sm">Lihat Semua</a>
        </div>
        <div class="row g-3">
            <?php foreach ($latest_products as $p): ?>
                <div class="col-6 col-md-3">
                    <?php $price = $p['discount_price'] ?: $p['price']; ?>
                    <div class="card product-card position-relative h-100">
                        <?php if ($p['discount_price']): ?>
                            <span class="badge-discount">-<?= round((1 - $p['discount_price'] / $p['price']) * 100) ?>%</span>
                        <?php endif; ?>
                        <?php if (is_logged_in()): ?>
                            <button class="btn-wishlist" onclick="toggleWishlist(<?= $p['id'] ?>, this)">
                                <i class="bi bi-heart"></i>
                            </button>
                        <?php endif; ?>
                        <a href="<?= base_url('produk/' . $p['slug']) ?>" class="text-decoration-none">
                            <?php if ($p['main_image']): ?>
                                <img data-src="<?= base_url($p['main_image']) ?>" alt="<?= esc($p['name']) ?>" class="card-img-top" loading="lazy">
                            <?php else: ?>
                                <div class="img-placeholder card-img-top" style="height:200px;"><i class="bi bi-image"></i></div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h6 class="card-title text-dark" style="font-size:0.9rem;"><?= esc($p['name']) ?></h6>
                                <?php if ($p['discount_price']): ?>
                                    <div class="price-current"><?= format_rupiah($price) ?></div>
                                    <div class="price-original"><?= format_rupiah($p['price']) ?></div>
                                <?php else: ?>
                                    <div class="price-current"><?= format_rupiah($price) ?></div>
                                <?php endif; ?>
                                <small class="text-muted"><i class="bi bi-shop me-1"></i><?= esc($p['store_name']) ?></small>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Produk Terlaris -->
    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0"><i class="bi bi-fire me-2"></i>Produk Terlaris</h4>
        </div>
        <div class="row g-3">
            <?php foreach ($popular_products as $p): ?>
                <div class="col-6 col-md-3">
                    <?php $price = $p['discount_price'] ?: $p['price']; ?>
                    <div class="card product-card position-relative h-100">
                        <?php if ($p['discount_price']): ?>
                            <span class="badge-discount">-<?= round((1 - $p['discount_price'] / $p['price']) * 100) ?>%</span>
                        <?php endif; ?>
                        <a href="<?= base_url('produk/' . $p['slug']) ?>" class="text-decoration-none">
                            <?php if ($p['main_image']): ?>
                                <img data-src="<?= base_url($p['main_image']) ?>" alt="<?= esc($p['name']) ?>" class="card-img-top" loading="lazy">
                            <?php else: ?>
                                <div class="img-placeholder card-img-top" style="height:200px;"><i class="bi bi-image"></i></div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h6 class="card-title text-dark" style="font-size:0.9rem;"><?= esc($p['name']) ?></h6>
                                <div class="price-current"><?= format_rupiah($price) ?></div>
                                <small class="text-muted"><i class="bi bi-bag-check me-1"></i>Terjual <?= $p['sold'] ?></small>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>