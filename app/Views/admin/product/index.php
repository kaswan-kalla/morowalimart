<?= $this->extend('layouts/header') ?>
<?= $this->section('title') ?>Input Data Barang<?= $this->endSection() ?>
<?= $this->include('layouts/navbar') ?>
<?= $this->include('layouts/sidebar') ?>

<div class="main-content">
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Input Data Barang</h4>
            <button class="btn btn-primary" onclick="showForm()"><i class="bi bi-plus-lg"></i> Tambah Barang</button>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <input type="text" id="searchProduct" class="form-control" placeholder="Cari produk, SKU, atau toko...">
                </div>
                <div class="table-responsive">
                    <table class="table table-hover" id="productTable">
                        <thead class="table-light">
                            <tr>
                                <th>Gambar</th>
                                <th>Produk</th>
                                <th>SKU</th>
                                <th>Toko</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="productBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="productModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalTitle">Tambah Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="productForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id" id="productId">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">SKU</label>
                            <input type="text" name="sku" id="sku" class="form-control" placeholder="Auto">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Stok <span class="text-danger">*</span></label>
                            <input type="number" name="stock" id="stock" class="form-control" min="0" value="0" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Toko <span class="text-danger">*</span></label>
                            <select name="store_id" id="store_id" class="form-select" required>
                                <option value="">-- Pilih Toko --</option>
                                <?php foreach ($stores as $s): ?>
                                    <option value="<?= $s['id'] ?>" <?= strtolower($s['name']) === 'morowalimart' ? ' selected' : '' ?>><?= esc($s['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="category_id" id="category_id" class="form-select">
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach ($categories as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= esc($c['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Harga <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="price" id="price" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Harga Diskon</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="discount_price" id="discount_price" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Berat (gram) <span class="text-danger">*</span></label>
                            <input type="text" name="weight" id="weight" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" id="description" class="form-control" rows="4"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Gambar Utama</label>
                            <input type="file" name="main_image" id="main_image" class="form-control" accept="image/jpeg,image/png,image/webp">
                            <small class="text-muted">Max 2MB. Format: JPG, PNG, WebP</small>
                            <div id="previewImage" class="mt-2"></div>
                        </div>
                        <div class="col-md-6 mb-3 d-flex align-items-end">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                                <label class="form-check-label" for="is_active">Aktif</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSave"><i class="bi bi-floppy"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>



<?= $this->section('scripts') ?>
<script>
    // Format rupiah on input
    function formatInputRupiah(el) {
        let val = el.value.replace(/[^0-9]/g, '');
        if (val) {
            el.value = new Intl.NumberFormat('id-ID').format(parseInt(val));
        } else {
            el.value = '';
        }
    }

    document.querySelectorAll('#price, #discount_price, #weight').forEach(el => {
        el.addEventListener('keyup', function() {
            formatInputRupiah(this);
        });
    });

    // Load produk
    function loadProducts() {
        $.get('<?= base_url('admin/products/data') ?>', {
            search: {
                value: $('#searchProduct').val()
            }
        }, function(res) {
            let html = '';
            res.data.forEach(function(p) {
                let imgSrc = p.main_image ?
                    base_url + p.main_image :
                    base_url + 'uploads/products/default.jpg';
                let price = p.discount_price > 0 ? p.discount_price : p.price;
                let badge = p.stock <= 5 ? 'bg-danger' : (p.stock <= 10 ? 'bg-warning text-dark' : 'bg-success');

                html += `<tr>
                <td><img src="${imgSrc}" class="rounded" width="50" height="50" style="object-fit:cover" onerror="this.src='${base_url}uploads/products/default.jpg'"></td>
                <td><strong>${escHtml(p.name)}</strong><br><small class="text-muted">${escHtml(p.category_name || '-')}</small></td>
                <td><span class="text-muted">${p.sku || '-'}</span></td>
                <td>${escHtml(p.store_name || '-')}</td>
                <td>${escHtml(p.category_name || '-')}</td>
                <td class="text-danger fw-bold">${formatRupiah(price)}</td>
                <td><span class="badge ${badge}">${p.stock}</span></td>
                <td><span class="badge bg-${p.is_active ? 'success' : 'secondary'}">${p.is_active ? 'Aktif' : 'Nonaktif'}</span></td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="editProduct(${p.id})" title="Edit"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-outline-${p.is_active ? 'warning' : 'success'}" onclick="toggleProduct(${p.id})" title="${p.is_active ? 'Nonaktifkan' : 'Aktifkan'}">
                            <i class="bi bi-${p.is_active ? 'pause' : 'play'}"></i>
                        </button>
                        <button class="btn btn-outline-danger" onclick="deleteProduct(${p.id})" title="Hapus"><i class="bi bi-trash"></i></button>
                    </div>
                </td>
            </tr>`;
            });
            $('#productBody').html(html || '<tr><td colspan="9" class="text-center py-4 text-muted">Tidak ada data</td></tr>');
        });
    }

    // Escape HTML
    function escHtml(str) {
        if (!str) return '';
        return $('<span>').text(str).html();
    }

    // Show form modal
    function showForm(data) {
        $('#productModalTitle').text('Tambah Barang');
        $('#productForm')[0].reset();
        $('#productId').val('');
        $('#previewImage').html('');
        $('#btnSave').html('<i class="bi bi-floppy"></i> Simpan');
        $('#is_active').prop('checked', true);

        if (data) {
            $('#productModalTitle').text('Edit Barang');
            $('#productId').val(data.id);
            $('#name').val(data.name);
            $('#sku').val(data.sku || '');
            $('#store_id').val(data.store_id);
            $('#category_id').val(data.category_id || '');
            $('#stock').val(data.stock);
            $('#description').val(data.description || '');
            $('#is_active').prop('checked', data.is_active ? true : false);

            // Format rupiah
            $('#price').val(new Intl.NumberFormat('id-ID').format(data.price));
            if (data.discount_price > 0) {
                $('#discount_price').val(new Intl.NumberFormat('id-ID').format(data.discount_price));
            }
            $('#weight').val(new Intl.NumberFormat('id-ID').format(data.weight));

            // Preview image
            if (data.main_image) {
                $('#previewImage').html(`<img src="${base_url}${data.main_image}" class="rounded" width="100" height="100" style="object-fit:cover">`);
            }

            $('#btnSave').html('<i class="bi bi-floppy"></i> Update');
        }

        new bootstrap.Modal($('#productModal')).show();
    }

    // Edit product
    function editProduct(id) {
        $.get('<?= base_url('admin/products/get') ?>/' + id, function(res) {
            if (res.status) {
                showForm(res.data);
            } else {
                showToast(res.message, 'error');
            }
        });
    }

    // Submit form
    $('#productForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#productId').val();
        let url = id ?
            '<?= base_url('admin/products/update') ?>/' + id :
            '<?= base_url('admin/products/store') ?>';

        // Bersihkan format rupiah before submit
        $('#price').val($('#price').val().replace(/[^0-9]/g, ''));
        let disc = $('#discount_price').val().replace(/[^0-9]/g, '');
        $('#discount_price').val(disc);
        $('#weight').val($('#weight').val().replace(/[^0-9]/g, ''));

        $.ajax({
            url: url,
            method: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.status) {
                    showToast(res.message, 'success');
                    bootstrap.Modal.getInstance($('#productModal')[0]).hide();
                    loadProducts();
                } else {
                    showToast(res.message, 'error');
                }
            },
            error: function() {
                showToast('Terjadi kesalahan server', 'error');
            }
        });
    });

    // Toggle status
    function toggleProduct(id) {
        $.post('<?= base_url('admin/products/toggle') ?>', {
            id: id
        }, function(res) {
            if (res.status) {
                showToast(res.message, 'success');
                loadProducts();
            } else {
                showToast(res.message || 'Gagal mengubah status', 'error');
            }
        });
    }

    // Delete product
    function deleteProduct(id) {
        Swal.fire({
            title: 'Hapus barang?',
            text: 'Data produk akan dihapus permanen',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc3545'
        }).then(r => {
            if (r.isConfirmed) {
                $.post('<?= base_url('admin/products/delete') ?>', {
                    id: id
                }, function(res) {
                    if (res.status) {
                        showToast(res.message, 'success');
                        loadProducts();
                    } else {
                        showToast(res.message, 'error');
                    }
                });
            }
        });
    }

    // Search with debounce
    $('#searchProduct').on('keyup', function() {
        clearTimeout(window.searchTimer);
        window.searchTimer = setTimeout(loadProducts, 500);
    });

    $(document).ready(loadProducts);

    // Image preview on file select
    $('#main_image').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#previewImage').html(`<img src="${e.target.result}" class="rounded" width="100" height="100" style="object-fit:cover">`);
            };
            reader.readAsDataURL(file);
        } else {
            $('#previewImage').html('');
        }
    });
</script>
<?= $this->endSection() ?>
<?= $this->include('layouts/scripts') ?>