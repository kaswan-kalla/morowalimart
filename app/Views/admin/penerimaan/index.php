<?= $this->extend('layouts/header') ?>
<?= $this->section('title') ?>Pembelian Barang<?= $this->endSection() ?>
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
            <h4 class="text-center print-title">Laporan Pembelian Barang</h4>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div class="ms-auto">
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
                                <th class="text-end">Harga Beli</th>
                                <th class="text-end">Harga Jual</th>
                                <th class="text-end">Selisih</th>
                                <th class="text-end">Jumlah</th>
                                <th class="text-end">Total Beli</th>
                                <th class="text-end">Total Jual</th>
                                <th class="text-end">Fee Outlet</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="terimaBody"></tbody>
                        <tfoot id="terimaFoot" class="table-light fw-bold"></tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="terimaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="terimaModalTitle">Edit Pembelian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="terimaForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="terimaId">
                    <div class="mb-3">
                        <label class="form-label">Barang <span class="text-danger">*</span></label>
                        <select name="id_barang" class="form-select" required>
                            <option value="">-- Pilih Barang --</option>
                            <?php foreach ($barang as $b): ?>
                                <option value="<?= $b['id'] ?>" data-nama="<?= htmlspecialchars($b['nama_barang']) ?>"><?= htmlspecialchars($b['nama_barang']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lokasi <span class="text-danger">*</span></label>
                        <select name="id_lokasi" class="form-select" required>
                            <option value="">-- Pilih Lokasi --</option>
                            <?php foreach ($lokasi as $l): ?>
                                <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['nama_lokasi']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga Beli <span class="text-danger">*</span></label>
                            <input type="text" name="harga_beli" class="form-control price-format" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga Jual <span class="text-danger">*</span></label>
                            <input type="text" name="harga_jual" class="form-control price-format" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                            <input type="text" name="jumlah" class="form-control number-format" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fee Outlet</label>
                            <input type="text" name="fee_outlet" class="form-control price-format">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Keterangan</label>
                            <input type="text" name="keterangan" class="form-control" placeholder="Opsional">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning" id="btnSubmit">Perbarui</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Input Massal -->
<div class="modal fade" id="bulkTerimaModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-list-ol me-2"></i>Input Pembelian Barang</h5>
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
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-light" style="position:sticky;top:0;z-index:1;">
                            <tr>
                                <th style="width:35px">#</th>
                                <th>Nama Barang</th>
                                <th style="width:130px">Harga Beli</th>
                                <th style="width:130px">Harga Jual</th>
                                <th style="width:120px">Fee Outlet</th>
                                <th style="width:160px">Jumlah</th>
                                <th style="width:130px" class="text-end">Total Harga</th>
                                <th style="width:90px" class="text-end">Stok</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody id="bulkTerimaBody"></tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <small class="text-muted">Daftar barang otomatis dari lokasi terpilih. Isi jumlah, yang 0 dilewati.</small>
                    <div>
                        <button type="button" class="btn btn-outline-danger btn-sm me-1" onclick="resetBulkRows()"><i class="bi bi-arrow-counterclockwise"></i> Reset</button>
                        <button type="button" class="btn btn-primary" onclick="submitBulkTerima()"><i class="bi bi-check2"></i> Simpan Semua</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
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
            box-shadow: none !important;
        }

        .table {
            border-collapse: collapse;
        }

        .table td,
        .table th {
            border: 1px solid #999 !important;
        }

        .btn-group,
        .btn-group *,
        .badge.bg-success,
        .badge.bg-danger,
        .badge.bg-secondary {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .table th:nth-child(n+11),
        .table td:nth-child(n+11) {
            display: none;
        }

        .table tfoot th:last-child {
            display: none;
        }
    }
</style>

<?= $this->section('scripts') ?>
<script>
    // Override tanpa prefix Rp untuk laporan
    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka || 0);
    }

    let terimaData = [];

    function loadData() {
        $.get('<?= base_url('admin/penerimaan/data') ?>', function(res) {
            terimaData = res.data;
            let html = '';
            let grandTotalBeli = 0,
                grandTotalJual = 0;
            res.data.forEach(function(v) {
                let totalBeli = parseInt(v.jumlah) * parseFloat(v.harga_beli);
                let totalJual = parseInt(v.jumlah) * parseFloat(v.harga_jual);
                let selisih = parseFloat(v.harga_jual) - parseFloat(v.harga_beli);
                let fee = parseFloat(v.fee_outlet) || 0;
                grandTotalBeli += totalBeli;
                grandTotalJual += totalJual;
                html += `<tr>
                <td>${v.tanggal ? new Date(v.tanggal).toLocaleDateString('id-ID') : '-'}</td>
                <td><strong>${escapeHtml(v.nama_barang)}</strong></td>
                <td>${escapeHtml(v.nama_lokasi)}</td>
                <td class="text-end">${formatRupiah(v.harga_beli)}</td>
                <td class="text-end">${formatRupiah(v.harga_jual)}</td>
                <td class="text-end ${selisih >= 0 ? 'text-success' : 'text-danger'} fw-bold">${formatRupiah(selisih)}</td>
                <td class="text-end">${parseInt(v.jumlah).toLocaleString('id-ID')}</td>
                <td class="text-end">${formatRupiah(totalBeli)}</td>
                <td class="text-end">${formatRupiah(totalJual)}</td>
                <td class="text-end">${formatRupiah(fee)}</td>
                <td>${escapeHtml(v.keterangan || '-')}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="editData(${v.id})"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-outline-danger" onclick="deleteData(${v.id})"><i class="bi bi-trash"></i></button>
                    </div>
                </td>
            </tr>`;
            });
            $('#terimaBody').html(html || '<tr><td colspan="12" class="text-center">Tidak ada data</td></tr>');

            if (res.data.length) {
                let potensiLaba = grandTotalJual - grandTotalBeli;
                $('#terimaFoot').html(
                    '<tr><th colspan="7" class="text-end">Grand Total</th>' +
                    '<th class="text-end">' + formatRupiah(grandTotalBeli) + '</th>' +
                    '<th class="text-end">' + formatRupiah(grandTotalJual) + '</th>' +
                    '<th></th><th colspan="2"></th></tr>' +
                    '<tr class="text-success" style="--bs-table-bg: #d1e7dd;">' +
                    '<td colspan="7" class="text-end fw-bold">Potensi Laba</td>' +
                    '<td colspan="5" class="fw-bold text-end">' + formatRupiah(potensiLaba) + '</td></tr>'
                );
            } else {
                $('#terimaFoot').html('');
            }
        });
    }

    let stokMap = {};

    function showForm() {
        // Input massal: pilih lokasi dulu, daftar barang muncul otomatis
        $('select[name="bulk_lokasi"]').val('');
        $('input[name="bulk_tanggal"]').val(new Date().toISOString().split('T')[0]);
        $('#bulkTerimaBody').html('<tr><td colspan="9" class="text-center text-muted py-4">Pilih Lokasi untuk memuat daftar barang</td></tr>');
        stokMap = {};
        new bootstrap.Modal($('#bulkTerimaModal')).show();
    }

    // ===== Input massal =====
    // Baris dibuat otomatis per barang: nama text readonly, harga & stok sudah terisi
    function buildBulkRows() {
        let idLokasi = $('select[name="bulk_lokasi"]').val();
        let html = '';
        $('select[name="id_barang"] option[value!=""]').each(function() {
            let id = $(this).val();
            let nama = $(this).data('nama');
            let stok = stokMap[id] || 0;
            let last = lastPurchase(id, idLokasi);
            let fmt = function(v) {
                return v ? new Intl.NumberFormat('id-ID').format(v) : '0';
            };
            html += `<tr>
        <td class="bulk-no text-center text-muted"></td>
        <td><input type="hidden" class="bulk-id" name="id_barang[]" value="${id}">
            <input type="text" class="form-control form-control-sm bulk-nama" value="${nama}" readonly></td>
        <td><input type="text" class="form-control form-control-sm text-end bulk-harga-beli price-format" name="harga_beli[]" value="${fmt(last ? last.harga_beli : 0)}"></td>
        <td><input type="text" class="form-control form-control-sm text-end bulk-harga-jual price-format" name="harga_jual[]" value="${fmt(last ? last.harga_jual : 0)}"></td>
        <td><input type="text" class="form-control form-control-sm text-end bulk-fee price-format" name="fee_outlet[]" value="${fmt(last ? last.fee_outlet : 0)}"></td>
        <td>
            <div class="input-group input-group-sm">
                <button type="button" class="btn btn-outline-secondary bulk-minus"><i class="bi bi-dash-lg"></i></button>
                <input type="text" class="form-control text-center bulk-jumlah" name="jumlah[]" value="0" inputmode="numeric">
                <button type="button" class="btn btn-outline-secondary bulk-plus"><i class="bi bi-plus-lg"></i></button>
            </div>
        </td>
        <td class="text-end bulk-total fw-bold"></td>
        <td class="text-end bulk-stok"></td>
        <td><input type="text" class="form-control form-control-sm bulk-ket" name="keterangan[]"></td>
    </tr>`;
        });
        $('#bulkTerimaBody').html(html || '<tr><td colspan="9" class="text-center text-muted py-4">Tidak ada barang di lokasi ini</td></tr>');
        renumberBulkRows();
        $('#bulkTerimaBody tr').each(function() {
            updateBulkRow($(this));
        });
    }

    // Pembelian terakhir barang ini (prioritas lokasi terpilih, fallback semua lokasi)
    function lastPurchase(idBarang, idLokasi) {
        let pool = terimaData.filter(v => String(v.id_barang) === String(idBarang) && (!idLokasi || String(v.id_lokasi) === String(idLokasi)));
        if (!pool.length && idLokasi) pool = terimaData.filter(v => String(v.id_barang) === String(idBarang));
        if (!pool.length) return null;
        return pool.reduce(function(a, b) {
            let ka = (a.tanggal || '') + ':' + String(a.id).padStart(10, '0');
            let kb = (b.tanggal || '') + ':' + String(b.id).padStart(10, '0');
            return kb > ka ? b : a;
        });
    }

    function resetBulkRows() {
        $('#bulkTerimaBody tr').each(function() {
            $(this).find('.bulk-jumlah').val('0');
            $(this).find('.bulk-ket').val('');
            updateBulkRow($(this));
        });
    }

    function renumberBulkRows() {
        $('#bulkTerimaBody tr').each(function(i) {
            $(this).find('.bulk-no').text(i + 1);
        });
    }

    // Hitung ulang total harga (harga beli x jumlah) & stok akhir per baris
    function updateBulkRow($row) {
        let idBarang = $row.find('.bulk-id').val();
        let hargaBeli = parseInt(String($row.find('.bulk-harga-beli').val()).replace(/\./g, '')) || 0;
        let jumlah = parseInt(String($row.find('.bulk-jumlah').val()).replace(/[^0-9]/g, '')) || 0;
        let stok = stokMap[idBarang] || 0;
        $row.find('.bulk-total').text(hargaBeli && jumlah ? new Intl.NumberFormat('id-ID').format(hargaBeli * jumlah) : '-');
        let akhir = stok + jumlah;
        let $stok = $row.find('.bulk-stok');
        $stok.text(akhir.toLocaleString('id-ID'));
        $stok.removeClass('text-success fw-bold');
        if (jumlah > 0) $stok.addClass('text-success fw-bold');
    }

    function loadBulkStok() {
        let idLokasi = $('select[name="bulk_lokasi"]').val();
        if (!idLokasi) {
            stokMap = {};
            $('#bulkTerimaBody').html('<tr><td colspan="9" class="text-center text-muted py-4">Pilih Lokasi untuk memuat daftar barang</td></tr>');
            return;
        }
        $.get('<?= base_url('admin/penerimaan/getStokLokasi') ?>/' + idLokasi, function(res) {
            if (!res.status) return;
            stokMap = res.stok || {};
            buildBulkRows();
        });
    }

    // Event input massal
    $(document).on('click', '.bulk-plus', function() {
        let $row = $(this).closest('tr');
        let $jml = $row.find('.bulk-jumlah');
        $jml.val((parseInt(String($jml.val()).replace(/[^0-9]/g, '')) || 0) + 1);
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
        this.value = String(this.value).replace(/[^0-9]/g, '');
        updateBulkRow($row);
    });

    $(document).on('input', '.bulk-harga-beli, .bulk-harga-jual, .bulk-fee', function() {
        updateBulkRow($(this).closest('tr'));
    });

    function submitBulkTerima() {
        let idLokasi = $('select[name="bulk_lokasi"]').val();
        let tanggal = $('input[name="bulk_tanggal"]').val();
        if (!idLokasi || !tanggal) {
            showToast('Lokasi dan tanggal wajib diisi', 'warning');
            return;
        }
        let items = [];
        $('#bulkTerimaBody tr').each(function() {
            let $r = $(this);
            let idBarang = $r.find('.bulk-id').val();
            let jumlah = parseInt(String($r.find('.bulk-jumlah').val()).replace(/[^0-9]/g, '')) || 0;
            if (idBarang && jumlah > 0) {
                items.push({
                    id_barang: idBarang,
                    harga_beli: String($r.find('.bulk-harga-beli').val()).replace(/\./g, ''),
                    harga_jual: String($r.find('.bulk-harga-jual').val()).replace(/\./g, ''),
                    fee_outlet: String($r.find('.bulk-fee').val()).replace(/\./g, ''),
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
            harga_beli: items.map(i => i.harga_beli),
            harga_jual: items.map(i => i.harga_jual),
            fee_outlet: items.map(i => i.fee_outlet),
            jumlah: items.map(i => i.jumlah),
            keterangan: items.map(i => i.keterangan)
        };
        Swal.fire({
            title: 'Simpan pembelian?',
            text: items.length + ' item akan disimpan',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal'
        }).then(r => {
            if (!r.isConfirmed) return;
            $.post('<?= base_url('admin/penerimaan/store-bulk') ?>', data, function(res) {
                if (res.status) {
                    showToast(res.message, 'success');
                    // Muat ulang stok, modal tetap terbuka untuk input berikutnya
                    loadBulkStok();
                    loadData();
                } else {
                    showToast(res.message, 'danger');
                }
            });
        });
    }

    function editData(id) {
        let d = terimaData.find(v => v.id == id);
        if (!d) return;
        $('#terimaModalTitle').text('Edit Pembelian');
        $('#terimaId').val(d.id);
        $('select[name="id_barang"]').val(d.id_barang);
        $('select[name="id_lokasi"]').val(d.id_lokasi);
        $('input[name="harga_beli"]').val(new Intl.NumberFormat('id-ID').format(d.harga_beli));
        $('input[name="harga_jual"]').val(new Intl.NumberFormat('id-ID').format(d.harga_jual));
        $('input[name="fee_outlet"]').val(d.fee_outlet ? new Intl.NumberFormat('id-ID').format(d.fee_outlet) : '');
        $('input[name="jumlah"]').val(parseInt(d.jumlah).toLocaleString('id-ID'));
        $('input[name="tanggal"]').val(d.tanggal || '');
        $('textarea[name="keterangan"]').val(d.keterangan || '');
        $('#btnSubmit').text('Perbarui');
        new bootstrap.Modal($('#terimaModal')).show();
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

    $('#terimaForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#terimaId').val();
        if (!id) return;
        Swal.fire({
            title: 'Perbarui data?',
            text: 'Data pembelian akan diperbarui',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Perbarui',
            cancelButtonText: 'Batal'
        }).then(r => {
            if (r.isConfirmed) submitTerima(id);
        });
    });

    function submitTerima(id) {
        $('.price-format, .number-format').each(function() {
            this.value = this.value.replace(/\./g, '');
        });
        $.post('<?= base_url('admin/penerimaan/update') ?>/' + id, $('#terimaForm').serialize(), function(res) {
            if (res.status) {
                showToast(res.message, 'success');
                bootstrap.Modal.getInstance($('#terimaModal')[0]).hide();
                loadData();
            } else {
                showToast(res.message, 'danger');
            }
        });
    }

    function deleteData(id) {
        Swal.fire({
            title: 'Hapus data?',
            text: 'Data pembelian akan dihapus',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then(r => {
            if (r.isConfirmed) {
                $.post('<?= base_url('admin/penerimaan/delete') ?>/' + id, function(res) {
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

        // Load stok & opsi barang saat lokasi dipilih (input massal)
        $('select[name="bulk_lokasi"]').on('change', loadBulkStok);
    });
</script>
<?= $this->endSection() ?>
<?= $this->include('layouts/scripts') ?>