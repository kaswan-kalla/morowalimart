<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $meta_title ?? 'Marketplace - Multi Vendor' ?></title>
    <meta name="description" content="<?= $meta_description ?? 'Marketplace Multi Vendor - Belanja Online Terpercaya' ?>">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" href="<?= asset_url('asset/pavicon.ico') ?>" type="image/x-icon">

    <style>
        :root {
            --primary: #0d6efd;
            --secondary: #6c757d;
            --success: #198754;
            --warning: #ffc107;
            --danger: #dc3545;
            --bg-light: #f8f9fa;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 70px;
        }

        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        }

        .product-card img {
            border-radius: 12px 12px 0 0;
            height: 200px;
            object-fit: cover;
            width: 100%;
        }

        .badge-discount {
            position: absolute;
            top: 10px;
            left: 10px;
            background: var(--danger);
            color: white;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .btn-wishlist {
            position: absolute;
            top: 52px;
            right: 10px;
            background: white;
            border: none;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: all 0.2s;
            z-index: 2;
        }

        .btn-cart-add {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            cursor: pointer;
            transition: all 0.2s;
            z-index: 2;
        }

        .btn-cart-add:hover {
            background: #0b5ed7;
            transform: scale(1.1);
        }

        .btn-wishlist:hover,
        .btn-wishlist.active {
            color: var(--danger);
        }

        .price-original {
            text-decoration: line-through;
            color: var(--secondary);
            font-size: 0.85rem;
        }

        .price-current {
            font-weight: 700;
            color: var(--danger);
            font-size: 1.1rem;
        }

        .toast-container {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 9999;
        }

        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.7);
            z-index: 9998;
            justify-content: center;
            align-items: center;
        }

        .loading-overlay.show {
            display: flex;
        }

        .img-placeholder {
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #adb5bd;
            font-size: 2rem;
        }

        footer {
            background: #343a40;
            color: #adb5bd;
            padding: 40px 0 20px;
            margin-top: 60px;
        }

        footer a {
            color: #adb5bd;
            text-decoration: none;
        }

        footer a:hover {
            color: white;
        }
    </style>
    <?= $this->renderSection('styles') ?>
</head>

<body>

    <?= $this->include('layouts/navbar') ?>

    <?= $this->renderSection('content') ?>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="text-white"><i class="bi bi-shop"></i> Morowalimart</h5>
                    <p class="small">Platform marketplace terpercaya. Belanja online mudah, aman, dan nyaman.</p>
                </div>
                <div class="col-md-2 mb-4">
                    <h6 class="text-white">Belanja</h6>
                    <ul class="list-unstyled small">
                        <li><a href="<?= base_url('produk') ?>">Semua Produk</a></li>
                        <li><a href="<?= base_url('search') ?>">Pencarian</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h6 class="text-white">Bantuan</h6>
                    <ul class="list-unstyled small">
                        <li><a href="#">Cara Belanja</a></li>
                        <li><a href="#">Cara Jual</a></li>
                        <li><a href="#">Kebijakan Privasi</a></li>
                        <li><a href="#">Syarat & Ketentuan</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h6 class="text-white">Hubungi Kami</h6>
                    <ul class="list-unstyled small">
                        <li><i class="bi bi-envelope me-2"></i>support@marketplace.com</li>
                        <li><i class="bi bi-telephone me-2"></i>0800-1234-5678</li>
                    </ul>
                </div>
            </div>
            <hr class="border-secondary">
            <div class="text-center small">
                &copy; <?= date('Y') ?> Marketplace Multi Vendor. All rights reserved.
            </div>
        </div>
    </footer>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const base_url = '<?= rtrim(base_url(), '/') ?>/';
        const csrfName = '<?= csrf_hash() ? csrf_token() : '' ?>';
        const csrfHash = '<?= csrf_hash() ?>';

        $.ajaxSetup({
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            beforeSend: function() {
                $('#loadingOverlay').addClass('show');
            },
            complete: function() {
                $('#loadingOverlay').removeClass('show');
            },
            error: function(xhr) {
                if (xhr.status === 401) window.location.href = '<?= base_url('login') ?>';
            }
        });

        function showToast(message, type = 'success') {
            const icons = {
                success: 'bi-check-circle-fill',
                error: 'bi-x-circle-fill',
                warning: 'bi-exclamation-triangle-fill',
                info: 'bi-info-circle-fill'
            };
            const colors = {
                success: 'bg-success',
                error: 'bg-danger',
                warning: 'bg-warning',
                info: 'bg-primary'
            };
            const toast = $(`<div class="toast align-items-center text-white ${colors[type] || colors.info} border-0 show" role="alert">
        <div class="d-flex"><div class="toast-body"><i class="bi ${icons[type] || icons.info} me-2"></i>${message}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>`);
            $('.toast-container').append(toast);
            setTimeout(() => toast.fadeOut(300, function() {
                $(this).remove();
            }), 3000);
        }

        $('#searchForm').on('submit', function(e) {
            e.preventDefault();
            const query = $('#searchInput').val().trim();
            if (query) window.location.href = '<?= base_url('search') ?>?q=' + encodeURIComponent(query);
        });

        document.addEventListener('DOMContentLoaded', function() {
            if ('IntersectionObserver' in window) {
                const imgObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            if (img.dataset.src) {
                                img.src = img.dataset.src;
                                img.removeAttribute('data-src');
                            }
                            imgObserver.unobserve(img);
                        }
                    });
                });
                document.querySelectorAll('img[data-src]').forEach(img => imgObserver.observe(img));
            }
        });

        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }

        function updateCartBadge(count) {
            if (count > 0) {
                $('#cartBadge').text(count).show();
                $('#cartBadgeMobile').text(count).show();
            } else {
                $('#cartBadge').hide();
                $('#cartBadgeMobile').hide();
            }
        }

        function addToCart(productId, qty) {
            qty = qty || 1;
            $.post(
                base_url + 'cart/add', {
                    product_id: productId,
                    quantity: qty
                },
                function(res) {
                    if (!res.status && typeof showToast === 'function') {
                        showToast(res.message, 'error');
                    }
                    if (res.status && res.data && res.data.cart_count) {
                        updateCartBadge(res.data.cart_count);
                    }
                },
                'json'
            );
        }

        function formatRupiah(angka) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
        }

        // Auto thousand-separator: tambahkan class "auto-separator" pada input
        $(document).on('input', '.auto-separator', function() {
            var raw = this.value.replace(/\D/g, '');
            this.value = raw ? Number(raw).toLocaleString('id-ID') : '';
        });
        $(document).on('submit', 'form', function() {
            $(this).find('.auto-separator').each(function() {
                this.value = this.value.replace(/\D/g, '');
            });
        });
    </script>

    <!-- View JS -->
    <?php if (isset($content)): ?>
        <?php if (isset($snapToken) && !empty($snapToken)): ?>
            <script src="<?= $snapUrl ?? '' ?>" data-client-key="<?= $clientKey ?? '' ?>"></script>
        <?php endif; ?>
        <script src="<?= asset_url('asset/js/view/' . ucfirst($content) . '.js') ?>"></script>
    <?php endif; ?>

    <?= $this->renderSection('scripts') ?>

</body>

</html>