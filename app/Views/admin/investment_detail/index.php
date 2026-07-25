<?= $this->extend('layouts/header') ?>
<?= $this->section('title') ?>Penambahan Modal<?= $this->endSection() ?>
<?= $this->include('layouts/navbar') ?>
<?= $this->include('layouts/sidebar') ?>
<?php
/** @var array $investors */
?>

<div class="main-content">
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Penambahan Modal</h4>
            <button class="btn btn-primary" onclick="showForm()"><i class="bi bi-plus"></i> Tambah</button>
        </div>

        <!-- Stat Cards -->
        <div class="row g-3 mb-4" id="statsContainer">
            <div class="col-md-3">
                <div class="card shadow-sm border-0 bg-success text-white">
                    <div class="card-body">
                        <small>Total Modal Investasi</small>
                        <h3 class="fw-bold mb-0" id="totalInvestment">Rp 0</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 bg-info text-white">
                    <div class="card-body">
                        <small>Jumlah Transaksi</small>
                        <h3 class="fw-bold mb-0" id="totalCount">0</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart me-2"></i>Distribusi Modal Investor</h6>
                        <div class="mb-2">
                            <select id="filterNumber" class="form-select form-select-sm w-auto" onchange="loadChart()">
                                <option value="">-- Semua No. Invest --</option>
                            </select>
                        </div>
                        <div style="max-height:240px;position:relative;">
                            <canvas id="pieInvestor"></canvas>
                        </div>
                        <div class="mt-3" id="investorLegend"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart me-2"></i>Total per No. Invest</h6>
                        <p class="text-muted small">Akumulasi modal berdasarkan nomor investasi</p>
                        <div style="max-height:240px;position:relative;">
                            <canvas id="pieNumber"></canvas>
                        </div>
                        <div class="mt-3" id="numberLegend"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No. Invest</th>
                                <th>Nama Investor</th>
                                <th>Nilai Modal</th>
                                <th>Tanggal Invest</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="detailBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalTitle">Tambah Penambahan Modal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="detailForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="detailId">
                    <div class="mb-3">
                        <label class="form-label">Nama Investor <span class="text-danger">*</span></label>
                        <select name="id_investor" class="form-select" required>
                            <option value="">-- Pilih Investor --</option>
                            <?php foreach ($investors as $inv): ?>
                                <option value="<?= $inv['id'] ?>"><?= htmlspecialchars($inv['investor_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. Invest <span class="text-danger">*</span></label>
                        <input type="text" name="nomor_invest" class="form-control" placeholder="Contoh: INV-001" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nilai Modal <span class="text-danger">*</span></label>
                        <input type="text" name="total_invest" class="form-control" id="investAmount" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Invest</label>
                        <input type="date" name="tgl_invest" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let chartInvestor = null;
    let chartNumber = null;

    const CHART_COLORS = [
        '#0d6efd', '#198754', '#ffc107', '#dc3545', '#0dcaf0', '#6f42c1',
        '#fd7e14', '#20c997', '#e83e8c', '#6610f2', '#adb5bd'
    ];

    function loadChart() {
        let filterVal = $('#filterNumber').val();
        let url = '<?= base_url('admin/investments/chart') ?>';
        if (filterVal) url += '?nomor_invest=' + encodeURIComponent(filterVal);

        $.get(url, function(res) {
            if (!res.status) return;

            $('#totalInvestment').text(formatRupiah(res.total_investment));
            $('#totalCount').text(res.total_count);

            // Pie per investor
            let labels = res.per_investor.map(p => p.name);
            let data = res.per_investor.map(p => p.amount);
            renderPieInvestor(labels, data, res.total_investment);

            // Pie per nomor invest
            let numLabels = res.per_number.map(p => p.name);
            let numData = res.per_number.map(p => p.amount);
            renderPieNumber(numLabels, numData, res.total_investment);
        });
    }

    function renderPieInvestor(labels, data, total) {
        let ctx = document.getElementById('pieInvestor').getContext('2d');
        if (chartInvestor) chartInvestor.destroy();
        chartInvestor = new Chart(ctx, {
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
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                let val = ctx.parsed;
                                let pct = ((val / total) * 100).toFixed(1);
                                return ctx.label + ': ' + formatRupiah(val) + ' (' + pct + '%)';
                            }
                        }
                    }
                }
            }
        });

        let html = '<div class="row g-1">';
        labels.forEach(function(lbl, i) {
            let pct = ((data[i] / total) * 100).toFixed(1);
            html += '<div class="col-6"><span class="d-inline-block rounded-circle me-1" style="width:10px;height:10px;background:' + CHART_COLORS[i] + '"></span> ' + lbl + ': <strong>' + formatRupiah(data[i]) + '</strong> (' + pct + '%)</div>';
        });
        html += '</div>';
        $('#investorLegend').html(html);
    }

    function renderPieNumber(labels, data, total) {
        let ctx = document.getElementById('pieNumber').getContext('2d');
        if (chartNumber) chartNumber.destroy();
        chartNumber = new Chart(ctx, {
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
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                let val = ctx.parsed;
                                let pct = ((val / total) * 100).toFixed(1);
                                return ctx.label + ': ' + formatRupiah(val) + ' (' + pct + '%)';
                            }
                        }
                    }
                }
            }
        });

        let html = '<div class="row g-1">';
        labels.forEach(function(lbl, i) {
            let pct = ((data[i] / total) * 100).toFixed(1);
            html += '<div class="col-6"><span class="d-inline-block rounded-circle me-1" style="width:10px;height:10px;background:' + CHART_COLORS[i] + '"></span> ' + lbl + ': <strong>' + formatRupiah(data[i]) + '</strong> (' + pct + '%)</div>';
        });
        html += '</div>';
        $('#numberLegend').html(html);
    }

    function loadDetails() {
        $.get('<?= base_url('admin/investments/data') ?>', function(res) {
            populateFilter(res.numbers);

            let html = '';
            res.data.forEach(function(v) {
                html += `<tr>
                    <td>${escapeHtml(v.nomor_invest)}</td>
                    <td>${escapeHtml(v.investor_name)}</td>
                    <td>${formatRupiah(v.total_invest)}</td>
                    <td>${v.tgl_invest ? new Date(v.tgl_invest).toLocaleDateString('id-ID') : '-'}</td>
                    <td>${escapeHtml(v.notes || '-')}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" onclick="editDetail(${v.id})"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-outline-danger" onclick="deleteDetail(${v.id})"><i class="bi bi-trash"></i></button>
                        </div>
                    </td>
                </tr>`;
            });
            $('#detailBody').html(html || '<tr><td colspan="6" class="text-center">Tidak ada data</td></tr>');
        });
    }

    function populateFilter(numbers) {
        let sel = $('#filterNumber');
        let current = sel.val();
        sel.find('option:not(:first)').remove();
        (numbers || []).forEach(function(n) {
            sel.append('<option value="' + escapeHtml(n.nomor_invest) + '">' + escapeHtml(n.nomor_invest) + '</option>');
        });
        sel.val(current);
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function showForm() {
        $('#detailModalTitle').text('Tambah Penambahan Modal');
        $('#detailForm')[0].reset();
        $('#detailId').val('');
        $('#investAmount').val('');
        new bootstrap.Modal($('#detailModal')).show();
    }

    function editDetail(id) {
        $.get('<?= base_url('admin/investments/get') ?>/' + id, function(res) {
            if (res.status) {
                let d = res.data;
                $('#detailModalTitle').text('Edit Penambahan Modal');
                $('#detailId').val(d.id);
                $('select[name="id_investor"]').val(d.id_investor);
                $('input[name="nomor_invest"]').val(d.nomor_invest);
                $('#investAmount').val(new Intl.NumberFormat('id-ID').format(d.total_invest));
                $('input[name="tgl_invest"]').val(d.tgl_invest || '');
                $('textarea[name="notes"]').val(d.notes || '');
                new bootstrap.Modal($('#detailModal')).show();
            }
        });
    }

    $('#investAmount').on('input', function() {
        let val = this.value.replace(/[^0-9]/g, '');
        if (val) this.value = new Intl.NumberFormat('id-ID').format(parseInt(val));
    });

    $('#detailForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#detailId').val();
        let url = id ? '<?= base_url('admin/investments/update') ?>/' + id : '<?= base_url('admin/investments/store') ?>';
        let amountInput = $('input[name="total_invest"]');
        amountInput.val(amountInput.val().replace(/\./g, ''));
        $.post(url, $(this).serialize(), function(res) {
            if (res.status) {
                showToast(res.message, 'success');
                bootstrap.Modal.getInstance($('#detailModal')[0]).hide();
                loadDetails();
                loadChart();
            } else {
                showToast(res.message, 'danger');
            }
        });
    });

    function deleteDetail(id) {
        Swal.fire({
            title: 'Hapus data?',
            text: 'Data penambahan modal akan dihapus',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then(r => {
            if (r.isConfirmed) {
                $.post('<?= base_url('admin/investments/delete') ?>/' + id, function(res) {
                    if (res.status) {
                        showToast('Data berhasil dihapus', 'success');
                        loadDetails();
                        loadChart();
                    } else {
                        showToast(res.message, 'danger');
                    }
                });
            }
        });
    }

    $(document).ready(function() {
        loadDetails();
        loadChart();
    });
</script>
<?= $this->endSection() ?>
<?= $this->include('layouts/scripts') ?>