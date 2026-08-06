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

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Penjualan Barang</h4>
            <div>
                <button class="btn btn-outline-primary me-1" onclick="openPrintPreview()"><i class="bi bi-printer"></i> Cetak</button>
                <button class="btn btn-outline-success me-1" onclick="copyReportImage()"><i class="bi bi-clipboard"></i> Salin Gambar</button>
                <button class="btn btn-primary" onclick="showForm()"><i class="bi bi-plus"></i> Tambah</button>
            </div>
        </div>

        <!-- Pie Chart -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3">
                    <i class="bi bi-pie-chart me-2"></i>Total Item Keluar per Outlet
                </h6>
                <div style="max-height:400px;">
                    <canvas id="pieOutlet"></canvas>
                </div>
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

<!-- Modal -->
<div class="modal fade" id="keluarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="keluarModalTitle">Tambah Penjualan</h5>
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
                    <button type="submit" class="btn btn-primary" id="btnSubmitKeluar">Tambah Baru</button>
                    <button type="button" class="btn btn-success" id="btnAddNewKeluar" style="display:none" onclick="resetToAdd()">+ Tambah Baru</button>
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
    let chartOutlet = null;

    const CHART_COLORS = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#0dcaf0', '#6f42c1', '#fd7e14', '#20c997', '#e83e8c', '#6610f2'];

    function loadChart() {
        $.get('<?= base_url('admin/pengeluaran/chart') ?>', function(res) {
            if (!res.status || !res.per_lokasi.length) return;
            let labels = res.per_lokasi.map(p => p.name);
            let data = res.per_lokasi.map(p => parseInt(p.total));
            let total = res.total_all;

            let ctx = document.getElementById('pieOutlet').getContext('2d');
            if (chartOutlet) chartOutlet.destroy();

            chartOutlet = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels,
                    datasets: [{
                        data,
                        backgroundColor: CHART_COLORS.slice(0, labels.length)
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'right',
                            labels: {
                                font: {
                                    size: 13
                                },
                                generateLabels: function(chart) {
                                    let orig = Chart.defaults.plugins.legend.labels.generateLabels(chart);
                                    orig.forEach(function(o, i) {
                                        let val = data[i];
                                        let pct = ((val / total) * 100).toFixed(1);
                                        o.text = o.text + ': ' + val.toLocaleString('id-ID') + ' (' + pct + '%)';
                                    });
                                    return orig;
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    let pct = ((ctx.parsed / total) * 100).toFixed(1);
                                    return ctx.label + ': ' + ctx.parsed.toLocaleString('id-ID') + ' item (' + pct + '%)';
                                }
                            }
                        }
                    }
                }
            });
        });
    }

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

    function showForm() {
        $('#keluarModalTitle').text('Tambah Penjualan');
        $('#keluarForm')[0].reset();
        $('#keluarId').val('');
        $('input[name="tanggal"]').val(new Date().toISOString().split('T')[0]);
        $('#btnSubmitKeluar').text('Tambah Baru').removeClass('btn-warning').addClass('btn-primary');
        $('#btnAddNewKeluar').hide();
        updateStokBarang();
        new bootstrap.Modal($('#keluarModal')).show();
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
        $('#btnSubmitKeluar').text('Perbarui').removeClass('btn-primary').addClass('btn-warning');
        $('#btnAddNewKeluar').show();
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

    function resetToAdd() {
        // Simpan data form saat ini sebagai entri baru (copy)
        $('.price-format, .number-format').each(function() {
            this.value = this.value.replace(/\./g, '');
        });
        var data = $('#keluarForm').serialize();
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
                if (r.isConfirmed) saveCopyKeluar(data);
            });
        } else {
            saveCopyKeluar(data);
        }
    }

    function saveCopyKeluar(data) {
        $.post('<?= base_url('admin/pengeluaran/store') ?>', data, function(res) {
            if (res.status) {
                showToast(res.message, 'success');
                loadData();
                loadChart();
            } else {
                showToast(res.message, 'danger');
            }
        });
    }

    $('#keluarForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#keluarId').val();
        if (id) {
            // Konfirmasi sebelum update
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
                    if (r.isConfirmed) submitKeluar(id);
                });
            } else {
                submitKeluar(id);
            }
        }
    });

    function submitKeluar(id) {
        $('.price-format, .number-format').each(function() {
            this.value = this.value.replace(/\./g, '');
        });
        let url = id ? '<?= base_url('admin/pengeluaran/update') ?>/' + id : '<?= base_url('admin/pengeluaran/store') ?>';
        $.post(url, $('#keluarForm').serialize(), function(res) {
            if (res.status) {
                showToast(res.message, 'success');
                if (id) {
                    bootstrap.Modal.getInstance($('#keluarModal')[0]).hide();
                } else {
                    // Tambah baru: modal tetap terbuka, hapus hanya barang & jumlah
                    $('#keluarForm select[name="id_barang"]').val('');
                    $('#keluarForm input[name="jumlah"]').val('');
                    updateStokBarang();
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
        loadChart();

        // Update label stok barang saat outlet dipilih
        $('select[name="id_lokasi"]').on('change', updateStokBarang);

        // Auto-fill harga jual dari data pembelian terbaru
        $('select[name="id_barang"]').on('change', function() {
            var idBarang = $(this).val();
            if (!idBarang) return;
            $.get('<?= base_url('admin/pengeluaran/getHargaJual') ?>/' + idBarang, function(res) {
                if (res.status && res.harga_jual > 0) {
                    $('input[name="harga_jual"]').val(new Intl.NumberFormat('id-ID').format(res.harga_jual));
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
<?= $this->include('layouts/scripts') ?>