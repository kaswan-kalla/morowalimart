<?= $this->extend('layouts/header') ?>
<?= $this->section('title') ?>Manajemen Investor<?= $this->endSection() ?>
<?= $this->include('layouts/navbar') ?>
<?= $this->include('layouts/sidebar') ?>

<div class="main-content">
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Manajemen Investor</h4>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary" onclick="loadChart()"><i class="bi bi-arrow-clockwise"></i> Refresh</button>
                <button class="btn btn-primary" onclick="showForm()"><i class="bi bi-plus"></i> Tambah Investor</button>
            </div>
        </div>

        <!-- Stat Cards -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card shadow-sm border-0 bg-primary text-white">
                    <div class="card-body">
                        <small>Total Investor</small>
                        <h3 class="fw-bold mb-0" id="totalInvestors">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card shadow-sm border-0 bg-warning text-white">
                    <div class="card-body">
                        <small>Total Modal Terkumpul</small>
                        <h3 class="fw-bold mb-0" id="totalAccumulated">Rp 0</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card shadow-sm border-0 bg-info text-white">
                    <div class="card-body">
                        <small>Aktif</small>
                        <h3 class="fw-bold mb-0" id="activeCount">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card shadow-sm border-0 bg-secondary text-white">
                    <div class="card-body">
                        <small>Nonaktif</small>
                        <h3 class="fw-bold mb-0" id="inactiveCount">0</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row g-3 mb-4">
            <div class="col-md-12">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart me-2"></i>Distribusi Modal Terkumpul</h6>
                        <p class="text-muted small">Berdasarkan total investasi per investor</p>
                        <div style="max-height:260px;position:relative;">
                            <canvas id="pieAccumulated"></canvas>
                        </div>
                        <div class="mt-3" id="accumulatedLegend"></div>
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
                                <th>Nama Investor</th>
                                <th>Kontak</th>
                                <th>Email</th>
                                <th>Total Modal</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="investorBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="investorModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="investorModalTitle">Tambah Investor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="investorForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="investorId">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Investor <span class="text-danger">*</span></label>
                            <input type="text" name="investor_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check mt-4">
                                <input type="checkbox" name="is_active" class="form-check-input" id="investorActive" value="1" checked>
                                <label class="form-check-label" for="investorActive">Aktif</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="address" class="form-control" rows="2"></textarea>
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
    let chartAccumulated = null;

    const CHART_COLORS = [
        '#0d6efd', '#198754', '#ffc107', '#dc3545', '#0dcaf0', '#6f42c1',
        '#fd7e14', '#20c997', '#e83e8c', '#6610f2', '#adb5bd'
    ];

    function loadChart() {
        $.get('<?= base_url('admin/investors/chart') ?>', function(res) {
            if (!res.status) return;

            // Stat cards
            $('#totalInvestors').text(res.total_investors);
            $('#totalAccumulated').text(formatRupiah(res.total_accumulated));
            $('#activeCount').text(res.active_count);
            $('#inactiveCount').text(res.inactive_count);

            // Pie Distribusi Modal
            let labels = res.acc_per_investor.map(p => p.name);
            let data = res.acc_per_investor.map(p => p.amount);
            renderPieAccumulated(labels, data, res.total_accumulated);
        });
    }

    function renderPieAccumulated(labels, data, total) {
        let ctx = document.getElementById('pieAccumulated').getContext('2d');
        if (chartAccumulated) chartAccumulated.destroy();
        chartAccumulated = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: CHART_COLORS.slice(0, labels.length),
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
        $('#accumulatedLegend').html(html);
    }

    function loadInvestors() {
        $.get('<?= base_url('admin/investors/data') ?>', function(res) {
            let html = '';
            res.data.forEach(function(v) {
                html += `<tr>
                    <td><strong>${escapeHtml(v.investor_name)}</strong></td>
                    <td>${escapeHtml(v.phone || '-')}</td>
                    <td>${escapeHtml(v.email || '-')}</td>
                    <td>${formatRupiah(v.total_modal)}</td>
                    <td><span class="badge bg-${v.is_active ? 'success' : 'secondary'}">${v.is_active ? 'Aktif' : 'Nonaktif'}</span></td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" onclick="editInvestor(${v.id})"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-outline-danger" onclick="deleteInvestor(${v.id})"><i class="bi bi-trash"></i></button>
                        </div>
                    </td>
                </tr>`;
            });
            $('#investorBody').html(html || '<tr><td colspan="6" class="text-center">Tidak ada data</td></tr>');
        });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function showForm() {
        $('#investorModalTitle').text('Tambah Investor');
        $('#investorForm')[0].reset();
        $('#investorId').val('');
        $('#investorActive').prop('checked', true);
        new bootstrap.Modal($('#investorModal')).show();
    }

    function editInvestor(id) {
        $.get('<?= base_url('admin/investors/get') ?>/' + id, function(res) {
            if (res.status) {
                let d = res.data;
                $('#investorModalTitle').text('Edit Investor');
                $('#investorId').val(d.id);
                $('input[name="investor_name"]').val(d.investor_name);
                $('input[name="phone"]').val(d.phone || '');
                $('input[name="email"]').val(d.email || '');
                $('textarea[name="address"]').val(d.address || '');
                $('textarea[name="notes"]').val(d.notes || '');
                $('#investorActive').prop('checked', d.is_active == 1);
                new bootstrap.Modal($('#investorModal')).show();
            }
        });
    }

    $('#investorForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#investorId').val();
        let url = id ? '<?= base_url('admin/investors/update') ?>/' + id : '<?= base_url('admin/investors/store') ?>';
        $.post(url, $(this).serialize(), function(res) {
            if (res.status) {
                showToast(res.message, 'success');
                bootstrap.Modal.getInstance($('#investorModal')[0]).hide();
                loadInvestors();
                loadChart();
            } else {
                showToast(res.message, 'danger');
            }
        });
    });

    function deleteInvestor(id) {
        Swal.fire({
            title: 'Hapus investor?',
            text: 'Data investor akan dihapus secara permanen',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then(r => {
            if (r.isConfirmed) {
                $.post('<?= base_url('admin/investors/delete') ?>/' + id, function(res) {
                    if (res.status) {
                        showToast('Investor berhasil dihapus', 'success');
                        loadInvestors();
                        loadChart();
                    } else {
                        showToast(res.message, 'danger');
                    }
                });
            }
        });
    }

    $(document).ready(function() {
        loadInvestors();
        loadChart();
    });
</script>
<?= $this->endSection() ?>
<?= $this->include('layouts/scripts') ?>