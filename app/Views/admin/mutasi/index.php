<?= $this->extend('layouts/header') ?>
<?= $this->section('title') ?>Mutasi Barang<?= $this->endSection() ?>
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
            <h4 class="text-center print-title">Laporan Mutasi Barang Antar Outlet</h4>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Mutasi Barang Antar Outlet</h4>
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
                                <th>Outlet Asal</th>
                                <th>Outlet Tujuan</th>
                                <th class="text-end">Jumlah</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="mutasiBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="mutasiModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mutasiModalTitle">Tambah Mutasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="mutasiForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="mutasiId">
                    <div class="mb-3">
                        <label class="form-label">Barang <span class="text-danger">*</span></label>
                        <select name="id_barang" class="form-select" required>
                            <option value="">-- Pilih Barang --</option>
                            <?php foreach ($barang as $b): ?>
                                <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['nama_barang']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Outlet Asal <span class="text-danger">*</span></label>
                            <select name="id_lokasi_asal" class="form-select" required>
                                <option value="">-- Pilih Asal --</option>
                                <?php foreach ($lokasi as $l): ?>
                                    <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['nama_lokasi']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted" id="stokInfo"></small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Outlet Tujuan <span class="text-danger">*</span></label>
                            <select name="id_lokasi_tujuan" class="form-select" required>
                                <option value="">-- Pilih Tujuan --</option>
                                <?php foreach ($lokasi as $l): ?>
                                    <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['nama_lokasi']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                            <input type="text" name="jumlah" class="form-control number-format" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitMutasi">Simpan</button>
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
        }

        .table {
            border-collapse: collapse;
        }

        .table td,
        .table th {
            border: 1px solid #999 !important;
        }

        .table th:nth-child(n+7),
        .table td:nth-child(n+7) {
            display: none;
        }
    }
</style>

<?= $this->section('scripts') ?>
<script>
    let mutasiData = [];

    function loadData() {
        $.get('<?= base_url('admin/mutasi/data') ?>', function(res) {
            mutasiData = res.data;
            let html = '';
            res.data.forEach(function(v) {
                html += `<tr>
                <td>${v.tanggal ? new Date(v.tanggal).toLocaleDateString('id-ID') : '-'}</td>
                <td><strong>${escapeHtml(v.nama_barang)}</strong></td>
                <td><span class="badge bg-danger">${escapeHtml(v.lokasi_asal)}</span></td>
                <td><span class="badge bg-success">${escapeHtml(v.lokasi_tujuan)}</span></td>
                <td class="text-end">${parseInt(v.jumlah).toLocaleString('id-ID')}</td>
                <td>${escapeHtml(v.keterangan || '-')}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="editData(${v.id})"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-outline-danger" onclick="deleteData(${v.id})"><i class="bi bi-trash"></i></button>
                    </div>
                </td>
            </tr>`;
            });
            $('#mutasiBody').html(html || '<tr><td colspan="7" class="text-center">Tidak ada data</td></tr>');
        });
    }

    function showForm() {
        $('#mutasiModalTitle').text('Tambah Mutasi');
        $('#mutasiForm')[0].reset();
        $('#mutasiId').val('');
        $('#stokInfo').text('');
        $('input[name="tanggal"]').val(new Date().toISOString().split('T')[0]);
        new bootstrap.Modal($('#mutasiModal')).show();
    }

    function editData(id) {
        let d = mutasiData.find(v => v.id == id);
        if (!d) return;
        $('#mutasiModalTitle').text('Edit Mutasi');
        $('#mutasiId').val(d.id);
        $('select[name="id_barang"]').val(d.id_barang);
        $('select[name="id_lokasi_asal"]').val(d.id_lokasi_asal);
        $('select[name="id_lokasi_tujuan"]').val(d.id_lokasi_tujuan);
        $('input[name="jumlah"]').val(parseInt(d.jumlah).toLocaleString('id-ID'));
        $('input[name="tanggal"]').val(d.tanggal || '');
        $('textarea[name="keterangan"]').val(d.keterangan || '');
        cekStok();
        new bootstrap.Modal($('#mutasiModal')).show();
    }

    // Info stok tersedia di outlet asal
    function cekStok() {
        var idBarang = $('select[name="id_barang"]').val();
        var idLokasi = $('select[name="id_lokasi_asal"]').val();
        if (!idBarang || !idLokasi) {
            $('#stokInfo').text('');
            return;
        }
        $.get('<?= base_url('admin/mutasi/stok') ?>', {
            id_barang: idBarang,
            id_lokasi: idLokasi
        }, function(res) {
            if (res.status) {
                $('#stokInfo').text('Stok tersedia: ' + res.stok.toLocaleString('id-ID'));
            }
        });
    }

    $(document).on('change', 'select[name="id_barang"], select[name="id_lokasi_asal"]', cekStok);

    $(document).on('input', '.number-format', function() {
        let val = this.value.replace(/[^0-9]/g, '');
        if (val) this.value = parseInt(val).toLocaleString('id-ID');
    });

    $('#mutasiForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#mutasiId').val();
        if (id) {
            // Konfirmasi sebelum update
            Swal.fire({
                title: 'Perbarui data?',
                text: 'Data mutasi akan diperbarui',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Perbarui',
                cancelButtonText: 'Batal'
            }).then(r => {
                if (r.isConfirmed) submitMutasi(id);
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
                    if (r.isConfirmed) submitMutasi(id);
                });
            } else {
                submitMutasi(id);
            }
        }
    });

    function submitMutasi(id) {
        $('.number-format').each(function() {
            this.value = this.value.replace(/\./g, '');
        });
        let url = id ? '<?= base_url('admin/mutasi/update') ?>/' + id : '<?= base_url('admin/mutasi/store') ?>';
        $.post(url, $('#mutasiForm').serialize(), function(res) {
            if (res.status) {
                showToast(res.message, 'success');
                if (id) {
                    bootstrap.Modal.getInstance($('#mutasiModal')[0]).hide();
                } else {
                    // Tambah baru: modal tetap terbuka, hapus hanya barang & jumlah
                    $('#mutasiForm select[name="id_barang"]').val('');
                    $('#mutasiForm input[name="jumlah"]').val('');
                }
                loadData();
            } else {
                showToast(res.message, 'danger');
                // Format ulang tampilan jumlah
                $('.number-format').each(function() {
                    if (this.value) this.value = parseInt(this.value).toLocaleString('id-ID');
                });
            }
        });
    }

    function deleteData(id) {
        Swal.fire({
            title: 'Hapus data?',
            text: 'Data mutasi akan dihapus dan stok kedua outlet dikembalikan',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then(r => {
            if (r.isConfirmed) {
                $.post('<?= base_url('admin/mutasi/delete') ?>/' + id, function(res) {
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