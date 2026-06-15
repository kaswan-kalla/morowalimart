<?php

/** @var array $order */
/** @var array $items */
$address = urlencode(trim(($order['shipping_address'] ?? '') . ', ' . ($order['recipient_name'] ?? '')));
?>
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('courier') ?>">Pengiriman</a></li>
            <li class="breadcrumb-item active"><?= esc($order['order_number']) ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-7">
            <!-- Info Penerima -->
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="bi bi-person me-2"></i>Info Penerima</h5>
                    <p class="mb-1"><strong>Nama:</strong> <?= esc($order['recipient_name'] ?? '-') ?></p>
                    <p class="mb-1"><strong>Telepon:</strong> <?= esc($order['phone'] ?? '-') ?></p>
                    <p class="mb-0"><strong>Alamat:</strong> <?= esc($order['shipping_address'] ?? '-') ?></p>
                </div>
            </div>

            <!-- Produk -->
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-box me-2"></i>Produk</h6>
                    <?php foreach ($items as $item): ?>
                        <div class="d-flex align-items-center gap-3 border-bottom pb-2 mb-2">
                            <img src="<?= base_url('uploads/products/' . ($item['image'] ?? 'default.png')) ?>"
                                class="rounded" width="50" height="50" style="object-fit:cover">
                            <div class="flex-grow-1">
                                <p class="mb-0 fw-semibold small"><?= esc($item['product_name']) ?></p>
                                <small class="text-muted"><?= $item['qty'] ?> x Rp <?= number_format($item['price'], 0, ',', '.') ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <!-- Status -->
            <div class="card shadow-sm mb-3">
                <div class="card-body text-center">
                    <h6 class="fw-bold mb-2">Status</h6>
                    <span class="badge bg-<?= $order['status'] === 'completed' ? 'success' : 'primary' ?> fs-6 px-3 py-2">
                        <?= $order['status'] === 'completed' ? 'Selesai' : 'Dalam Pengiriman' ?>
                    </span>
                    <?php if (!empty($order['shipped_at'])): ?>
                        <p class="text-muted small mt-2 mb-0">
                            Dikirim: <?= date('d M Y H:i', strtotime($order['shipped_at'])) ?>
                        </p>
                    <?php endif; ?>
                    <?php if ($order['status'] === 'completed' && !empty($order['completed_at'])): ?>
                        <p class="text-muted small mb-0">
                            Selesai: <?= date('d M Y H:i', strtotime($order['completed_at'])) ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Aksi -->
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Aksi</h6>

                    <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $address ?>"
                        target="_blank" class="btn btn-success w-100 mb-2">
                        <i class="bi bi-geo-alt me-1"></i>Navigasi Google Maps
                    </a>

                    <?php $wa = preg_replace('/\D/', '', $order['phone'] ?? ''); ?>
                    <?php if ($wa): ?>
                        <a href="https://wa.me/62<?= ltrim($wa, '0') ?>?text=Halo%20<?= urlencode($order['recipient_name'] ?? '') ?>%2C%20saya%20kurir%20Morowalimart"
                            target="_blank" class="btn btn-info text-white w-100 mb-2">
                            <i class="bi bi-whatsapp me-1"></i>Hubungi via WA
                        </a>
                    <?php endif; ?>

                    <?php if ($order['status'] === 'shipped'): ?>
                        <button class="btn btn-primary w-100" onclick="completeDelivery()">
                            <i class="bi bi-check-lg me-1"></i>Tandai Terkirim
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Info Pesanan -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Ringkasan Pesanan</h6>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span>Subtotal</span><span>Rp <?= number_format($order['subtotal'], 0, ',', '.') ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span>Ongkir</span><span>Rp <?= number_format($order['shipping_cost'], 0, ',', '.') ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total</span>
                        <span class="text-danger">Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<script>
    function completeDelivery() {
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Pastikan pesanan sudah diterima pembeli?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Terkirim',
            cancelButtonText: 'Batal'
        }).then(result => {
            if (result.isConfirmed) {
                $.post('<?= base_url('courier/complete') ?>', {
                    id: <?= $order['id'] ?>
                }, function(res) {
                    if (res.status) {
                        Swal.fire('Berhasil', 'Pengiriman selesai', 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                });
            }
        });
    }
</script>
<?= $this->endSection() ?>