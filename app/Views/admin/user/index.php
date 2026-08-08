<?= $this->extend('layouts/header') ?>
<?= $this->section('title') ?>Kelola User<?= $this->endSection() ?>
<?= $this->include('layouts/navbar') ?>
<?= $this->include('layouts/sidebar') ?>

<div class="main-content">
    <div class="container-fluid py-4">
        <h4 class="mb-4">Kelola User</h4>
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <input type="text" id="searchUser" class="form-control" placeholder="Cari user...">
                </div>
                <div class="table-responsive">
                    <table class="table table-hover" id="userTable">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Terdaftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<?= $this->include('layouts/scripts') ?>

<?= $this->section('scripts') ?>
<script>
    function loadUsers() {
        let search = $('#searchUser').val();
        $.get('<?= base_url('admin/users/data') ?>', {
            search: search
        }, function(res) {
            let html = '';
            res.data.forEach(function(u) {
                html += `<tr>
                <td>${u.id}</td>
                <td>${u.name}</td>
                <td>${u.email}</td>
                <td><span class="badge bg-${u.role === 'admin' ? 'danger' : (u.role === 'petugas' ? 'info' : (u.role === 'seller' ? 'primary' : 'secondary'))}">${u.role}</span>${u.role === 'petugas' && u.lokasi_name ? ' <small class="text-muted">(' + u.lokasi_name + ')</small>' : ''}</td>
                <td><span class="badge bg-${u.is_active ? 'success' : 'secondary'}">${u.is_active ? 'Aktif' : 'Nonaktif'}</span></td>
                <td>${new Date(u.created_at).toLocaleDateString('id-ID')}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="editRole(${u.id}, '${u.role}')"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-outline-${u.is_active ? 'warning' : 'success'}" onclick="toggleStatus(${u.id})">
                            <i class="bi bi-${u.is_active ? 'lock' : 'unlock'}"></i>
                        </button>
                    </div>
                </td>
            </tr>`;
            });
            $('#userTable tbody').html(html || '<tr><td colspan="7" class="text-center">Tidak ada data</td></tr>');
        });
    }

    function editRole(id, currentRole) {
        let roles = ['buyer', 'seller', 'petugas', 'admin'];
        Swal.fire({
            title: 'Ubah Role',
            input: 'select',
            inputOptions: Object.fromEntries(roles.map(r => [r, r])),
            inputValue: currentRole,
            showCancelButton: true,
            confirmButtonText: 'Lanjut'
        }).then(result => {
            if (!result.isConfirmed) return;
            if (result.value !== 'petugas') {
                saveRole(id, result.value);
                return;
            }
            // Petugas wajib pilih outlet
            $.get('<?= base_url('admin/lokasi/data') ?>', function(res) {
                Swal.fire({
                    title: 'Pilih Outlet',
                    input: 'select',
                    inputOptions: Object.fromEntries(res.data.map(l => [l.id, l.nama_lokasi])),
                    showCancelButton: true,
                    confirmButtonText: 'Simpan'
                }).then(r2 => {
                    if (r2.isConfirmed) saveRole(id, 'petugas', r2.value);
                });
            });
        });
    }

    function saveRole(id, role, idLokasi) {
        $.post('<?= base_url('admin/users/update-role') ?>', {
            id: id,
            role: role,
            id_lokasi: idLokasi || ''
        }, function(res) {
            if (res.status) {
                showToast('Role diperbarui', 'success');
                loadUsers();
            } else showToast(res.message, 'danger');
        });
    }

    function toggleStatus(id) {
        $.post('<?= base_url('admin/users/toggle-status') ?>', {
            id: id
        }, function(res) {
            if (res.status) {
                showToast('Status diperbarui', 'success');
                loadUsers();
            } else showToast(res.message, 'danger');
        });
    }

    $('#searchUser').on('keyup', function() {
        clearTimeout(window.timer);
        window.timer = setTimeout(loadUsers, 500);
    });
    $(document).ready(loadUsers);
</script>
<?= $this->endSection() ?>