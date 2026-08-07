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

<!-- Modal -->
<div class="modal fade" id="terimaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="terimaModalTitle">Tambah Pembelian</h5>
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
                                <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['nama_barang']) ?></option>
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
                    <button type="submit" class="btn btn-primary" id="btnSubmit">Tambah Baru</button>
                    <button type="button" class="btn btn-success" id="btnAddNew" style="display:none" onclick="resetToAdd()">+ Tambah Baru</button>
                </div>
            </form>
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

    function showForm() {
        $('#terimaModalTitle').text('Tambah Pembelian');
        $('#terimaForm')[0].reset();
        $('#terimaId').val('');
        $('input[name="tanggal"]').val(new Date().toISOString().split('T')[0]);
        $('#btnSubmit').text('Tambah Baru').removeClass('btn-warning').addClass('btn-primary');
        $('#btnAddNew').hide();
        new bootstrap.Modal($('#terimaModal')).show();
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
        $('#btnSubmit').text('Perbarui').removeClass('btn-primary').addClass('btn-warning');
        $('#btnAddNew').show();
        new bootstrap.Modal($('#terimaModal')).show();
    }

    function resetToAdd() {
        // Simpan data form saat ini sebagai entri baru
        $('.price-format, .number-format').each(function() {
            this.value = this.value.replace(/\./g, '');
        });
        var data = $('#terimaForm').serialize();
        // Format ulang tampilan
        $('.price-format').each(function() {
            if (this.value) this.value = new Intl.NumberFormat('id-ID').format(parseInt(this.value));
        });
        $('.number-format').each(function() {
            if (this.value) this.value = parseInt(this.value).toLocaleString('id-ID');
        });
        // Konfirmasi jika tanggal bukan hari ini
        let tgl = $('input[name="tanggal"]').val();
        let today = new Date().toISOString().split('T')[0];
        if (tgl && tgl !== today) {
            Swal.fire({
                title: 'Tanggal bukan hari ini?',
                text: 'Data akan disimpan dengan tanggal ' + tgl,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Batal'
            }).then(r => {
                if (r.isConfirmed) saveCopy(data);
            });
        } else {
            saveCopy(data);
        }
    }

    function saveCopy(data) {
        $.post('<?= base_url('admin/penerimaan/store') ?>', data, function(res) {
            if (res.status) {
                showToast(res.message, 'success');
                loadData();
            } else {
                showToast(res.message, 'danger');
            }
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

    // Default harga dari pembelian terakhir outlet yang sama (hanya mode tambah)
    function applyLastPurchaseDefaults() {
        if ($('#terimaId').val()) return; // jangan saat edit
        let idLokasi = $('select[name="id_lokasi"]').val();
        let idBarang = $('select[name="id_barang"]').val();
        if (!idLokasi) return;
        // Prioritas: barang yang sama di outlet yang sama, fallback semua barang outlet itu
        let sameBarang = terimaData.filter(v =>
            String(v.id_lokasi) === String(idLokasi) && String(v.id_barang) === String(idBarang));
        let pool = sameBarang.length ? sameBarang :
            terimaData.filter(v => String(v.id_lokasi) === String(idLokasi));
        if (!pool.length) return;
        // Pembelian terakhir: tanggal terbaru, tie-break id terbesar
        let last = pool.reduce(function(a, b) {
            let keyA = (a.tanggal || '') + ':' + String(a.id).padStart(10, '0');
            let keyB = (b.tanggal || '') + ':' + String(b.id).padStart(10, '0');
            return keyB > keyA ? b : a;
        });
        $('input[name="harga_beli"]').val(last.harga_beli ? new Intl.NumberFormat('id-ID').format(last.harga_beli) : '');
        $('input[name="harga_jual"]').val(last.harga_jual ? new Intl.NumberFormat('id-ID').format(last.harga_jual) : '');
        $('input[name="fee_outlet"]').val(last.fee_outlet ? new Intl.NumberFormat('id-ID').format(last.fee_outlet) : '');
    }

    $('#terimaForm select[name="id_barang"], #terimaForm select[name="id_lokasi"]').on('change', applyLastPurchaseDefaults);

    $('#terimaForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#terimaId').val();
        if (id) {
            // Konfirmasi sebelum update
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
        } else {
            // Konfirmasi jika tanggal bukan hari ini
            let tgl = $('input[name="tanggal"]').val();
            let today = new Date().toISOString().split('T')[0];
            if (tgl && tgl !== today) {
                Swal.fire({
                    title: 'Tanggal bukan hari ini?',
                    text: 'Data akan disimpan dengan tanggal ' + tgl,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal'
                }).then(r => {
                    if (r.isConfirmed) submitTerima(id);
                });
            } else {
                submitTerima(id);
            }
        }
    });

    function submitTerima(id) {
        // Remove formatting before submit
        $('.price-format, .number-format').each(function() {
            this.value = this.value.replace(/\./g, '');
        });
        let url = id ? '<?= base_url('admin/penerimaan/update') ?>/' + id : '<?= base_url('admin/penerimaan/store') ?>';
        $.post(url, $('#terimaForm').serialize(), function(res) {
            if (res.status) {
                showToast(res.message, 'success');
                if (id) {
                    bootstrap.Modal.getInstance($('#terimaModal')[0]).hide();
                } else {
                    // Tambah baru: modal tetap terbuka, hapus hanya barang & jumlah
                    $('#terimaForm select[name="id_barang"]').val('');
                    $('#terimaForm input[name="jumlah"]').val('');
                }
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
    });
</script>
<?= $this->endSection() ?>
<?= $this->include('layouts/scripts') ?>