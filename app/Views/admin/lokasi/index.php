<?= $this->extend('layouts/header') ?>
<?= $this->section('title') ?>Data Lokasi<?= $this->endSection() ?>
<?= $this->include('layouts/navbar') ?>
<?= $this->include('layouts/sidebar') ?>

<div class="main-content">
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Data Lokasi</h4>
            <button class="btn btn-primary" onclick="showForm()"><i class="bi bi-plus"></i> Tambah Lokasi</button>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Lokasi</th>
                                <th>Alamat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="lokasiBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="lokasiModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lokasiModalTitle">Tambah Lokasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="lokasiForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="lokasiId">
                    <div class="mb-3">
                        <label class="form-label">Nama Lokasi <span class="text-danger">*</span></label>
                        <input type="text" name="nama_lokasi" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2"></textarea>
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
<script>
    let lokasiData = [];

    function loadData() {
        $.get('<?= base_url('admin/lokasi/data') ?>', function(res) {
            lokasiData = res.data;
            let html = '';
            res.data.forEach(function(v) {
                html += `<tr>
                <td><strong>${escapeHtml(v.nama_lokasi)}</strong></td>
                <td>${escapeHtml(v.alamat || '-')}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="editData(${v.id})"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-outline-danger" onclick="deleteData(${v.id})"><i class="bi bi-trash"></i></button>
                    </div>
                </td>
            </tr>`;
            });
            $('#lokasiBody').html(html || '<tr><td colspan="3" class="text-center">Tidak ada data</td></tr>');
        });
    }

    function showForm() {
        $('#lokasiModalTitle').text('Tambah Lokasi');
        $('#lokasiForm')[0].reset();
        $('#lokasiId').val('');
        new bootstrap.Modal($('#lokasiModal')).show();
    }

    function editData(id) {
        let d = lokasiData.find(v => v.id == id);
        if (!d) return;
        $('#lokasiModalTitle').text('Edit Lokasi');
        $('#lokasiId').val(d.id);
        $('input[name="nama_lokasi"]').val(d.nama_lokasi);
        $('textarea[name="alamat"]').val(d.alamat || '');
        new bootstrap.Modal($('#lokasiModal')).show();
    }

    $('#lokasiForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#lokasiId').val();
        let url = id ? '<?= base_url('admin/lokasi/update') ?>/' + id : '<?= base_url('admin/lokasi/store') ?>';
        $.post(url, $(this).serialize(), function(res) {
            if (res.status) {
                showToast(res.message, 'success');
                bootstrap.Modal.getInstance($('#lokasiModal')[0]).hide();
                loadData();
            } else {
                showToast(res.message, 'danger');
            }
        });
    });

    function deleteData(id) {
        Swal.fire({
            title: 'Hapus lokasi?',
            text: 'Data lokasi akan dihapus',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then(r => {
            if (r.isConfirmed) {
                $.post('<?= base_url('admin/lokasi/delete') ?>/' + id, function(res) {
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