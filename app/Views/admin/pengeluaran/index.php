<?= $this->extend('layouts/header') ?>
<?= $this->section('title') ?>Penjualan Barang<?= $this->endSection() ?>
<?= $this->include('layouts/navbar') ?>
<?= $this->include('layouts/sidebar') ?>
<?php
/** @var array $barang */
/** @var array $lokasi */
?>

<div class="main-content">
    <div class="container-fluid py-4">
        <!-- Kop surat hanya saat print -->
        <div class="print-kop">
            <h2 class="text-center mb-1">UD. MOROWALIMART</h2>
            <p class="text-center mb-1">Jl. Trans Sulawesi Bahodopi - Morowali</p>
            <hr class="my-2">
            <h4 class="text-center print-title">Laporan Penjualan Barang</h4>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4 page-actions">
            <h4 class="mb-0">Penjualan Barang</h4>
            <div>
                <button class="btn btn-outline-primary me-1" onclick="openPrintPreview()"><i class="bi bi-printer"></i> Cetak</button>
                <button class="btn btn-outline-success me-1" onclick="copyReportImage()"><i class="bi bi-clipboard"></i> Salin Gambar</button>
                <button class="btn btn-primary" onclick="showForm()"><i class="bi bi-plus"></i> Tambah</button>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Barang</th>
                                <th>Lokasi</th>
                                <th class="text-end">Harga Jual</th>
                                <th class="text-end">Jumlah</th>
                                <th class="text-end">Total</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="keluarBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="keluarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="keluarModalTitle">Edit Penjualan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="keluarForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="keluarId">
                    <div class="mb-3">
                        <label class="form-label">Lokasi <span class="text-danger">*</span></label>
                        <select name="id_lokasi" class="form-select" required>
                            <option value="">-- Pilih Lokasi --</option>
                            <?php foreach ($lokasi as $l): ?>
                                <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['nama_lokasi']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Barang <span class="text-danger">*</span></label>
                        <select name="id_barang" class="form-select" required>
                            <option value="">-- Pilih Barang --</option>
                            <?php foreach ($barang as $b): ?>
                                <option value="<?= $b['id'] ?>" data-nama="<?= htmlspecialchars($b['nama_barang']) ?>"><?= htmlspecialchars($b['nama_barang']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga Jual <span class="text-danger">*</span></label>
                            <input type="text" name="harga_jual" class="form-control price-format" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                            <input type="text" name="jumlah" class="form-control number-format" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning" id="btnSubmitKeluar">Perbarui</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Input Massal -->
<div class="modal fade" id="bulkKeluarModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-fullscreen-md-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-list-ol me-2"></i>Input Penjualan Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Lokasi <span class="text-danger">*</span></label>
                        <select name="bulk_lokasi" class="form-select" required>
                            <option value="">-- Pilih Lokasi --</option>
                            <?php foreach ($lokasi as $l): ?>
                                <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['nama_lokasi']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="bulk_tanggal" class="form-control" required>
                    </div>
                </div>
                <div class="table-responsive" style="max-height:55vh;overflow-y:auto;">
                    <table class="table table-bordered align-middle mb-0 bulk-table">
                        <thead class="table-light" style="position:sticky;top:0;z-index:1;">
                            <tr>
                                <th style="width:40px">#</th>
                                <th>Nama Barang</th>
                                <th style="width:150px">Harga Satuan</th>
                                <th style="width:180px">Jumlah</th>
                                <th style="width:140px" class="text-end">Total Harga</th>
                                <th style="width:100px" class="text-end">Stok</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody id="bulkKeluarBody"></tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2 bulk-actions">
                    <small class="text-muted">Daftar barang otomatis dari stok lokasi terpilih. Isi jumlah, yang 0 dilewati.</small>
                    <div>
                        <button type="button" class="btn btn-outline-danger btn-sm me-1" onclick="resetBulkRows()"><i class="bi bi-arrow-counterclockwise"></i> Reset</button>
                        <button type="button" class="btn btn-primary" onclick="submitBulkKeluar()"><i class="bi bi-check2"></i> Simpan Semua</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* ===== View mobile: form input massal jadi kartu bertumpuk ===== */
    @media (max-width: 767.98px) {
        .page-actions {
            flex-wrap: wrap;
            gap: .5rem;
        }

        .page-actions .btn {
            flex: 1 1 auto;
        }

        .bulk-table thead {
            display: none;
        }

        .bulk-table,
        .bulk-table tbody,
        .bulk-table tr,
        .bulk-table td {
            display: block;
            width: 100%;
        }

        .bulk-table tr {
            border: 1px solid #dee2e6 !important;
            border-radius: .5rem;
            margin-bottom: .75rem;
            padding: .4rem .6rem;
        }

        .bulk-table td {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .5rem;
            border: 0 !important;
            padding: .35rem 0;
        }

        .bulk-table td::before {
            content: attr(data-label);
            font-weight: 600;
            font-size: .78rem;
            color: #6c757d;
            flex: 0 0 auto;
        }

        .bulk-table td .form-control {
            font-size: 1rem;
        }

        .bulk-table td > .form-control,
        .bulk-table td > .input-group {
            flex: 1 1 auto;
            min-width: 0;
        }

        .bulk-table td .bulk-nama {
            font-weight: 600;
            background: #f8f9fa;
        }

        .bulk-table td .input-group {
            max-width: 100%;
        }

        .bulk-table td .input-group .btn {
            min-width: 46px;
            min-height: 46px;
        }

        .bulk-table td .input-group .bulk-jumlah {
            font-size: 1.1rem;
        }

        .bulk-actions {
            flex-direction: column;
            align-items: stretch !important;
            gap: .5rem;
        }

        .bulk-actions .btn {
            width: 100%;
        }

        .bulk-actions small {
            text-align: center;
        }
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
        .btn-primary,
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
        }

        .card-chart {
            display: none !important;
        }

        .table {
            border-collapse: collapse;
        }

        .table td,
        .table th {
            border: 1px solid #999 !important;
        }

        .btn-group,
        .btn-group * {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .table th:nth-child(n+8),
        .table td:nth-child(n+8) {
            display: none;
        }
    }
</style>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Override tanpa prefix Rp untuk laporan
    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka || 0);
    }

    let keluarData = [];

    function loadData() {
        $.get('<?= base_url('admin/pengeluaran/data') ?>', function(res) {
            keluarData = res.data;
            let html = '';
            res.data.forEach(function(v) {
                let total = parseInt(v.jumlah) * parseFloat(v.harga_jual);
                html += `<tr>
                <td>${v.tanggal ? new Date(v.tanggal).toLocaleDateString('id-ID') : '-'}</td>
                <td><strong>${escapeHtml(v.nama_barang)}</strong></td>
                <td>${escapeHtml(v.nama_lokasi)}</td>
                <td class="text-end">${formatRupiah(v.harga_jual)}</td>
                <td class="text-end">${parseInt(v.jumlah).toLocaleString('id-ID')}</td>
                <td class="text-end">${formatRupiah(total)}</td>
                <td>${escapeHtml(v.keterangan || '-')}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="editData(${v.id})"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-outline-danger" onclick="deleteData(${v.id})"><i class="bi bi-trash"></i></button>
                    </div>
                </td>
            </tr>`;
            });
            $('#keluarBody').html(html || '<tr><td colspan="8" class="text-center">Tidak ada data</td></tr>');
        });
    }

    let stokMap = {};
    let hargaMap = {};

    function showForm() {
        // Input massal: pilih lokasi dulu, daftar barang muncul otomatis
        $('select[name="bulk_lokasi"]').val('');
        $('input[name="bulk_tanggal"]').val(new Date().toISOString().split('T')[0]);
        $('#bulkKeluarBody').html('<tr><td colspan="7" class="text-center text-muted py-4">Pilih Lokasi untuk memuat daftar barang</td></tr>');
        stokMap = {};
        hargaMap = {};
        new bootstrap.Modal($('#bulkKeluarModal')).show();
    }

    // ===== Input massal =====
    // Baris dibuat otomatis per barang: nama text readonly, harga & stok sudah terisi
    function buildBulkRows() {
        let html = '';
        $('select[name="id_barang"] option[value!=""]').each(function() {
            let id = $(this).val();
            let nama = $(this).data('nama');
            let stok = stokMap[id] || 0;
            if (stok <= 0) return; // tanpa stok jangan ditampilkan
            html += `<tr>
        <td class="bulk-no text-center text-muted" data-label="No"></td>
        <td data-label="Nama Barang"><input type="hidden" class="bulk-id" name="id_barang[]" value="${id}">
            <input type="text" class="form-control form-control-sm bulk-nama" value="${nama}" readonly></td>
        <td data-label="Harga Satuan"><input type="text" class="form-control form-control-sm text-end bulk-harga price-format" name="harga_jual[]" value="${hargaMap[id] ? new Intl.NumberFormat('id-ID').format(hargaMap[id]) : '0'}"></td>
        <td data-label="Jumlah">
            <div class="input-group input-group-sm">
                <button type="button" class="btn btn-outline-secondary bulk-minus"><i class="bi bi-dash-lg"></i></button>
                <input type="text" class="form-control text-center bulk-jumlah" name="jumlah[]" value="0" inputmode="numeric">
                <button type="button" class="btn btn-outline-secondary bulk-plus"><i class="bi bi-plus-lg"></i></button>
            </div>
        </td>
        <td data-label="Total Harga" class="text-end bulk-total fw-bold"></td>
        <td data-label="Stok" class="text-end bulk-stok"></td>
        <td data-label="Keterangan"><input type="text" class="form-control form-control-sm bulk-ket" name="keterangan[]"></td>
    </tr>`;
        });
        $('#bulkKeluarBody').html(html || '<tr><td colspan="7" class="text-center text-muted py-4">Tidak ada barang dengan stok di lokasi ini</td></tr>');
        renumberBulkRows();
        $('#bulkKeluarBody tr').each(function() {
            updateBulkRow($(this));
        });
    }

    function resetBulkRows() {
        $('#bulkKeluarBody tr').each(function() {
            $(this).find('.bulk-jumlah').val('0');
            $(this).find('.bulk-ket').val('');
            updateBulkRow($(this));
        });
    }

    function renumberBulkRows() {
        $('#bulkKeluarBody tr').each(function(i) {
            $(this).find('.bulk-no').text(i + 1);
        });
    }

    // Hitung ulang total harga & sisa stok per baris
    function updateBulkRow($row) {
        let idBarang = $row.find('.bulk-id').val();
        let harga = parseInt(String($row.find('.bulk-harga').val()).replace(/\./g, '')) || 0;
        let jumlah = parseInt(String($row.find('.bulk-jumlah').val()).replace(/[^0-9]/g, '')) || 0;
        let stok = stokMap[idBarang] || 0;
        $row.find('.bulk-total').text(harga && jumlah ? new Intl.NumberFormat('id-ID').format(harga * jumlah) : '-');
        let sisa = stok - jumlah;
        let $stok = $row.find('.bulk-stok');
        $stok.text(sisa.toLocaleString('id-ID'));
        $stok.removeClass('text-danger text-success fw-bold');
        $stok.addClass(sisa < 0 ? 'text-danger fw-bold' : (jumlah > 0 ? 'text-success fw-bold' : ''));
    }

    function loadBulkStok() {
        let idLokasi = $('select[name="bulk_lokasi"]').val();
        if (!idLokasi) {
            stokMap = {};
            hargaMap = {};
            $('#bulkKeluarBody').html('<tr><td colspan="7" class="text-center text-muted py-4">Pilih Lokasi untuk memuat daftar barang</td></tr>');
            return;
        }
        $.get('<?= base_url('admin/pengeluaran/getStokLokasi') ?>/' + idLokasi, function(res) {
            if (!res.status) return;
            stokMap = res.stok || {};
            hargaMap = res.harga || {};
            buildBulkRows();
        });
    }

    // Event input massal
    $(document).on('click', '.bulk-plus', function() {
        let $row = $(this).closest('tr');
        let $jml = $row.find('.bulk-jumlah');
        let val = (parseInt(String($jml.val()).replace(/[^0-9]/g, '')) || 0) + 1;
        let stok = stokMap[$row.find('.bulk-id').val()] || 0;
        if (val > stok) val = stok;
        $jml.val(val);
        updateBulkRow($row);
    });

    $(document).on('click', '.bulk-minus', function() {
        let $row = $(this).closest('tr');
        let $jml = $row.find('.bulk-jumlah');
        let val = (parseInt(String($jml.val()).replace(/[^0-9]/g, '')) || 0) - 1;
        if (val < 0) val = 0;
        $jml.val(val);
        updateBulkRow($row);
    });

    $(document).on('input', '.bulk-jumlah', function() {
        let $row = $(this).closest('tr');
        let val = String(this.value).replace(/[^0-9]/g, '');
        let stok = stokMap[$row.find('.bulk-id').val()] || 0;
        if (val && parseInt(val) > stok) val = String(stok);
        this.value = val;
        updateBulkRow($row);
    });

    $(document).on('input', '.bulk-harga', function() {
        updateBulkRow($(this).closest('tr'));
    });

    function submitBulkKeluar() {
        let idLokasi = $('select[name="bulk_lokasi"]').val();
        let tanggal = $('input[name="bulk_tanggal"]').val();
        if (!idLokasi || !tanggal) {
            showToast('Lokasi dan tanggal wajib diisi', 'warning');
            return;
        }
        let items = [];
        $('#bulkKeluarBody tr').each(function() {
            let $r = $(this);
            let idBarang = $r.find('.bulk-id').val();
            let jumlah = parseInt(String($r.find('.bulk-jumlah').val()).replace(/[^0-9]/g, '')) || 0;
            if (idBarang && jumlah > 0) {
                items.push({
                    id_barang: idBarang,
                    harga_jual: String($r.find('.bulk-harga').val()).replace(/\./g, ''),
                    jumlah: jumlah,
                    keterangan: $r.find('.bulk-ket').val() || ''
                });
            }
        });
        if (!items.length) {
            showToast('Tidak ada item dengan jumlah di atas 0', 'warning');
            return;
        }
        let data = {
            id_lokasi: idLokasi,
            tanggal: tanggal,
            id_barang: items.map(i => i.id_barang),
            harga_jual: items.map(i => i.harga_jual),
            jumlah: items.map(i => i.jumlah),
            keterangan: items.map(i => i.keterangan)
        };
        Swal.fire({
            title: 'Simpan penjualan?',
            text: items.length + ' item akan disimpan',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal'
        }).then(r => {
            if (!r.isConfirmed) return;
            $.post('<?= base_url('admin/pengeluaran/store-bulk') ?>', data, function(res) {
                if (res.status) {
                    showToast(res.message, 'success');
                    // Muat ulang stok & harga, modal tetap terbuka untuk input berikutnya
                    loadBulkStok();
                    loadData();
                } else {
                    showToast(res.message, 'danger');
                }
            });
        });
    }

    function editData(id) {
        let d = keluarData.find(v => v.id == id);
        if (!d) return;
        $('#keluarModalTitle').text('Edit Penjualan');
        $('#keluarId').val(d.id);
        $('select[name="id_barang"]').val(d.id_barang);
        $('select[name="id_lokasi"]').val(d.id_lokasi);
        $('input[name="harga_jual"]').val(new Intl.NumberFormat('id-ID').format(d.harga_jual));
        $('input[name="jumlah"]').val(parseInt(d.jumlah).toLocaleString('id-ID'));
        $('input[name="tanggal"]').val(d.tanggal || '');
        $('textarea[name="keterangan"]').val(d.keterangan || '');
        $('#btnSubmitKeluar').text('Perbarui');
        updateStokBarang();
        new bootstrap.Modal($('#keluarModal')).show();
    }

    // Tampilkan stok akhir outlet terpilih pada option barang: "BERAS -> 10"
    function updateStokBarang() {
        var idLokasi = $('select[name="id_lokasi"]').val();
        var $opts = $('select[name="id_barang"] option[value!=""]');
        if (!idLokasi) {
            $opts.each(function() {
                $(this).text($(this).data('nama'));
            });
            return;
        }
        $.get('<?= base_url('admin/pengeluaran/getStokLokasi') ?>/' + idLokasi, function(res) {
            if (!res.status) return;
            $opts.each(function() {
                var stok = res.stok[$(this).val()] || 0;
                $(this).text($(this).data('nama') + ' -> ' + stok.toLocaleString('id-ID'));
            });
        });
    }

    // Rupiah formatting on input
    $(document).on('input', '.price-format', function() {
        let val = this.value.replace(/[^0-9]/g, '');
        if (val) this.value = new Intl.NumberFormat('id-ID').format(parseInt(val));
    });

    $(document).on('input', '.number-format', function() {
        let val = this.value.replace(/[^0-9]/g, '');
        if (val) this.value = parseInt(val).toLocaleString('id-ID');
    });

    $('#keluarForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#keluarId').val();
        if (!id) return;
        Swal.fire({
            title: 'Perbarui data?',
            text: 'Data penjualan akan diperbarui',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Perbarui',
            cancelButtonText: 'Batal'
        }).then(r => {
            if (r.isConfirmed) submitKeluar(id);
        });
    });

    function submitKeluar(id) {
        $('.price-format, .number-format').each(function() {
            this.value = this.value.replace(/\./g, '');
        });
        $.post('<?= base_url('admin/pengeluaran/update') ?>/' + id, $('#keluarForm').serialize(), function(res) {
            if (res.status) {
                showToast(res.message, 'success');
                bootstrap.Modal.getInstance($('#keluarModal')[0]).hide();
                loadData();
            } else {
                showToast(res.message, 'danger');
            }
        });
    }

    function deleteData(id) {
        Swal.fire({
            title: 'Hapus data?',
            text: 'Data penjualan akan dihapus',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then(r => {
            if (r.isConfirmed) {
                $.post('<?= base_url('admin/pengeluaran/delete') ?>/' + id, function(res) {
                    if (res.status) {
                        showToast(res.message, 'success');
                        loadData();
                    } else {
                        showToast(res.message, 'danger');
                    }
                });
            }
        });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    $(document).ready(function() {
        loadData();

        // Update label stok barang saat outlet dipilih (modal edit)
        $('select[name="id_lokasi"]').on('change', updateStokBarang);

        // Auto-fill harga jual dari data pembelian terbaru (modal edit)
        $('select[name="id_barang"]').on('change', function() {
            var idBarang = $(this).val();
            if (!idBarang) return;
            $.get('<?= base_url('admin/pengeluaran/getHargaJual') ?>/' + idBarang, function(res) {
                if (res.status && res.harga_jual > 0) {
                    $('input[name="harga_jual"]').val(new Intl.NumberFormat('id-ID').format(res.harga_jual));
                }
            });
        });

        // Load stok & opsi barang saat lokasi dipilih (input massal)
        $('select[name="bulk_lokasi"]').on('change', loadBulkStok);
    });
</script>
<?= $this->endSection() ?>
<?= $this->include('layouts/scripts') ?>