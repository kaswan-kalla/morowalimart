<?= $this->extend('layouts/header') ?>
<?= $this->section('title') ?>Data Survey<?= $this->endSection() ?>
<?= $this->include('layouts/navbar') ?>
<?= $this->include('layouts/sidebar') ?>

<div class="main-content">
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Data Survey Pelanggan</h4>
            <button class="btn btn-outline-primary" onclick="refreshData()"><i class="bi bi-arrow-clockwise"></i> Refresh</button>
        </div>

        <!-- Stat Cards -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card shadow-sm border-0 bg-primary text-white">
                    <div class="card-body">
                        <small>Total Responden</small>
                        <h3 class="fw-bold mb-0" id="totalResponden">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card shadow-sm border-0 bg-success text-white">
                    <div class="card-body">
                        <small>Jumlah Desa</small>
                        <h3 class="fw-bold mb-0" id="totalDesa">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card shadow-sm border-0 bg-warning text-white">
                    <div class="card-body">
                        <small>Kategori Pengeluaran</small>
                        <h3 class="fw-bold mb-0" id="totalPengeluaran">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card shadow-sm border-0 bg-info text-white">
                    <div class="card-body">
                        <small>Status Menikah</small>
                        <h3 class="fw-bold mb-0" id="totalStatus">0</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h6 class="fw-bold mb-3"><i class="bi bi-geo-alt me-2"></i>Jumlah & Persentase per Desa</h6>
                        <div style="max-height:260px;position:relative;">
                            <canvas id="pieDesa"></canvas>
                        </div>
                        <div class="mt-3" id="desaLegend"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h6 class="fw-bold mb-3"><i class="bi bi-wallet2 me-2"></i>Pengeluaran Rutin</h6>
                        <div style="max-height:260px;position:relative;">
                            <canvas id="piePengeluaran"></canvas>
                        </div>
                        <div class="mt-3" id="pengeluaranLegend"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h6 class="fw-bold mb-0"><i class="bi bi-table me-2"></i>Data Responden Terbaru</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>No. WA</th>
                                <th>Desa</th>
                                <th>Pengeluaran</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody id="surveyBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let chartDesa = null;
    let chartPengeluaran = null;

    const COLORS = [
        '#0d6efd', '#198754', '#ffc107', '#dc3545', '#0dcaf0', '#6f42c1',
        '#fd7e14', '#20c997', '#e83e8c', '#6610f2'
    ];
    const PENGELUARAN_COLORS = ['#28a745', '#17a2b8', '#ffc107', '#dc3545'];

    function refreshData() {
        $.get('<?= base_url('admin/survey/data') ?>', function(res) {
            console.log('Survey API response:', res);
            if (!res.status) return;

            // Stat cards
            $('#totalResponden').text(res.total);
            $('#totalDesa').text(res.per_desa.length);
            $('#totalPengeluaran').text(res.per_pengeluaran.length + ' kategori');
            $('#totalStatus').text(res.per_status.length + ' kategori');

            // Pie Desa
            let desaLabels = res.per_desa.map(d => d.alamat);
            let desaData = res.per_desa.map(d => d.jumlah);
            renderPieDesa(desaLabels, desaData, res.total);

            // Pie Pengeluaran
            let pengeluaranLabels = res.per_pengeluaran.map(p => p.pengeluaran_perbulan);
            let pengeluaranData = res.per_pengeluaran.map(p => p.jumlah);
            renderPiePengeluaran(pengeluaranLabels, pengeluaranData, res.total);

            // Table
            let html = '';
            res.latest.forEach(function(r, i) {
                html += `<tr>
                <td>${i + 1}</td>
                <td>${escapeHtml(r.nama)}</td>
                <td>${escapeHtml(r.no_wa)}</td>
                <td>${escapeHtml(r.alamat)}</td>
                <td>${escapeHtml(r.pengeluaran_perbulan)}</td>
                <td><span class="badge bg-${r.status_menikah === 'Menikah' ? 'primary' : 'secondary'}">${escapeHtml(r.status_menikah)}</span></td>
                <td>${new Date(r.created_at).toLocaleDateString('id-ID', {day:'numeric',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'})}</td>
            </tr>`;
            });
            $('#surveyBody').html(html || '<tr><td colspan="7" class="text-center">Belum ada data survey</td></tr>');
        }).fail(function(xhr) {
            console.error('Survey API error:', xhr.status, xhr.responseText.substring(0, 500));
            $('#totalResponden').text('ERR');
            $('#surveyBody').html('<tr><td colspan="7" class="text-center text-danger">Gagal load data: HTTP ' + xhr.status + '</td></tr>');
        });
    }

    function renderPieDesa(labels, data, total) {
        let ctx = document.getElementById('pieDesa').getContext('2d');
        if (chartDesa) chartDesa.destroy();
        chartDesa = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: COLORS.slice(0, labels.length),
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
                                return ctx.label + ': ' + val + ' (' + pct + '%)';
                            }
                        }
                    }
                }
            }
        });

        // Custom legend below
        let html = '<div class="row g-1">';
        labels.forEach(function(lbl, i) {
            let pct = ((data[i] / total) * 100).toFixed(1);
            html += `<div class="col-6"><span class="d-inline-block rounded-circle me-1" style="width:10px;height:10px;background:${COLORS[i]}"></span> ${lbl}: <strong>${data[i]}</strong> (${pct}%)</div>`;
        });
        html += '</div>';
        $('#desaLegend').html(html);
    }

    function renderPiePengeluaran(labels, data, total) {
        let ctx = document.getElementById('piePengeluaran').getContext('2d');
        if (chartPengeluaran) chartPengeluaran.destroy();
        chartPengeluaran = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: PENGELUARAN_COLORS.slice(0, labels.length),
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
                                return ctx.label + ': ' + val + ' (' + pct + '%)';
                            }
                        }
                    }
                }
            }
        });

        let html = '<div class="row g-1">';
        labels.forEach(function(lbl, i) {
            let pct = ((data[i] / total) * 100).toFixed(1);
            let bg = PENGELUARAN_COLORS[i] || COLORS[i];
            html += `<div class="col-6"><span class="d-inline-block rounded-circle me-1" style="width:10px;height:10px;background:${bg}"></span> ${lbl}: <strong>${data[i]}</strong> (${pct}%)</div>`;
        });
        html += '</div>';
        $('#pengeluaranLegend').html(html);
    }

    function escapeHtml(str) {
        if (!str) return '';
        return $('<span>').text(str).html();
    }

    $(document).ready(refreshData);
</script>
<?= $this->endSection() ?>
<?= $this->include('layouts/scripts') ?>