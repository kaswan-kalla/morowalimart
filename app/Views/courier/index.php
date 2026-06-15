<?php

/** @var array $orders */
/** @var int $total */
/** @var int $limit */
/** @var int $page */
?>
<div class="container py-4">
    <h5 class="fw-bold mb-4"><i class="bi bi-truck me-2"></i>Pengiriman Saya</h5>

    <?php if (empty($orders)): ?>
        <div class="text-center py-5">
            <i class="bi bi-box-seam fs-1 text-muted"></i>
            <p class="text-muted mt-2">Belum ada pengiriman</p>
        </div>
    <?php endif; ?>

    <div class="row g-3">
        <?php foreach ($orders as $o): ?>
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="fw-bold mb-0"><?= esc($o['order_number']) ?></h6>
                            <span class="badge bg-<?= $o['status'] === 'completed' ? 'success' : 'primary' ?>">
                                <?= $o['status'] === 'completed' ? 'Selesai' : 'Dikirim' ?>
                            </span>
                        </div>
                        <p class="mb-1 small">
                            <i class="bi bi-person me-1"></i><?= esc($o['recipient_name'] ?? '-') ?>
                        </p>
                        <p class="mb-1 small text-muted">
                            <i class="bi bi-geo-alt me-1"></i><?= esc($o['shipping_address'] ?? '-') ?>
                        </p>
                        <p class="mb-2 small text-muted">
                            <i class="bi bi-calendar me-1"></i><?= date('d M Y H:i', strtotime($o['shipped_at'] ?? $o['created_at'])) ?>
                        </p>
                        <a href="<?= base_url('courier/' . $o['id']) ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye me-1"></i>Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($total > $limit): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= ceil($total / $limit); $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>