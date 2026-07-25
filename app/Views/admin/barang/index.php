<?= $this->extend('layouts/header') ?>
<?= $this->section('title') ?>Data Barang<?= $this->endSection() ?>
<?= $this->include('layouts/navbar') ?>
<?= $this->include('layouts/sidebar') ?>

<div class="main-content">
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Data Barang</h4>
            <button class="btn btn-primary" onclick="showForm()"><i class="bi bi-plus"></i> Tambah Barang</button>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Barang</th>
                                <th>Satuan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="barangBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="barangModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="barangModalTitle">Tambah Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="barangForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="barangId">
                    <div class="mb-3">
                        <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" name="nama_barang" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Satuan</label>
                        <select name="satuan" class="form-select">
                            <option value="pcs">Pcs</option>
                            <option value="kg">Kg</option>
                            <option value="liter">Liter</option>
                            <option value="meter">Meter</option>
                            <option value="dus">Dus</option>
                            <option value="pack">Pack</option>
                            <option value="box">Box</option>
                            <option value="sak">Sak</option>
                        </select>
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
    let barangData = [];

    function loadData() {
        $.get('<?= base_url('admin/barang/data') ?>', function(res) {
            barangData = res.data;
            let html = '';
            res.data.forEach(function(v) {
                html += `<tr>
                <td><strong>${escapeHtml(v.nama_barang)}</strong></td>
                <td>${escapeHtml(v.satuan || '-')}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="editData(${v.id})"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-outline-danger" onclick="deleteData(${v.id})"><i class="bi bi-trash"></i></button>
                    </div>
                </td>
            </tr>`;
            });
            $('#barangBody').html(html || '<tr><td colspan="3" class="text-center">Tidak ada data</td></tr>');
        });
    }

    function showForm() {
        $('#barangModalTitle').text('Tambah Barang');
        $('#barangForm')[0].reset();
        $('#barangId').val('');
        new bootstrap.Modal($('#barangModal')).show();
    }

    function editData(id) {
        let d = barangData.find(v => v.id == id);
        if (!d) return;
        $('#barangModalTitle').text('Edit Barang');
        $('#barangId').val(d.id);
        $('input[name="nama_barang"]').val(d.nama_barang);
        $('select[name="satuan"]').val(d.satuan || 'pcs');
        new bootstrap.Modal($('#barangModal')).show();
    }

    $('#barangForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#barangId').val();
        let url = id ? '<?= base_url('admin/barang/update') ?>/' + id : '<?= base_url('admin/barang/store') ?>';
        $.post(url, $(this).serialize(), function(res) {
            if (res.status) {
                showToast(res.message, 'success');
                bootstrap.Modal.getInstance($('#barangModal')[0]).hide();
                loadData();
            } else {
                showToast(res.message, 'danger');
            }
        });
    });

    function deleteData(id) {
        Swal.fire({
            title: 'Hapus barang?',
            text: 'Data barang akan dihapus',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then(r => {
            if (r.isConfirmed) {
                $.post('<?= base_url('admin/barang/delete') ?>/' + id, function(res) {
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