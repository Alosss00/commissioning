<main id="main" class="main">
    <div class="pagetitle">
        <h1>Manajemen User</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Home</a></li>
                <li class="breadcrumb-item active">Manajemen User</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body pt-4">

                        <!-- Toolbar -->
                        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
                            <div class="d-flex gap-2 align-items-center">
                                <div class="input-group input-group-sm" style="width:220px;">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="text" class="form-control" id="searchUser" placeholder="Cari nama/username/email...">
                                </div>
                                <select class="form-select form-select-sm" id="filterStatus" style="width:130px;">
                                    <option value="">Semua Status</option>
                                    <option value="1">Aktif</option>
                                    <option value="0">Nonaktif</option>
                                </select>
                            </div>
                            <button class="btn btn-primary btn-sm" id="btnTambahUser">
                                <i class="bi bi-person-plus me-1"></i>Tambah User
                            </button>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="tblUser">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:50px;"></th>
                                        <th>Nama / Username</th>
                                        <th>Email</th>
                                        <th>Jabatan</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tblUserBody">
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="spinner-border spinner-border-sm text-primary me-2"></div>Memuat...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- ═══ MODAL TAMBAH / EDIT USER ═══ -->
<div class="modal fade" id="modalUser" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUserTitle">
                    <i class="bi bi-person-plus me-2 text-primary"></i>Tambah User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="hid_id_user">

                <div class="row g-3">
                    <!-- Foto -->
                    <div class="col-12 text-center mb-2">
                        <div class="position-relative d-inline-block">
                            <div id="fotoPreviewContainer">
                                <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center text-white"
                                    id="fotoInitial"
                                    style="width:90px;height:90px;font-size:2rem;cursor:pointer;">
                                    <i class="bi bi-person"></i>
                                </div>
                                <img id="fotoPreview" src="" class="rounded-circle d-none"
                                    style="width:90px;height:90px;object-fit:cover;cursor:pointer;">
                            </div>
                            <label for="inputFotoUser" class="position-absolute bottom-0 end-0
                     rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                style="width:28px;height:28px;cursor:pointer;font-size:12px;">
                                <i class="bi bi-camera"></i>
                            </label>
                            <input type="file" id="inputFotoUser" name="foto" accept="image/*" class="d-none">
                        </div>
                        <div class="small text-muted mt-1">Klik ikon kamera untuk ganti foto</div>
                    </div>

                    <!-- Info dasar -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="inp_nama" placeholder="Nama lengkap">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="inp_username" placeholder="username (tanpa spasi)">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="inp_email" placeholder="email@domain.com">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">No. HP</label>
                        <input type="text" class="form-control" id="inp_no_hp" placeholder="+62...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Jabatan</label>
                        <input type="text" class="form-control" id="inp_jabatan" placeholder="Jabatan / posisi">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Departemen / perusahaan</label>
                        <select class="form-select" id="inp_departemen">
                            <option value="">-- Pilih Departemen / Perusahaan --</option>
                            <?php foreach ($perusahaan as $p): ?>
                                <option value="<?= html_escape($p->nama_perusahaan) ?>">
                                    <?= html_escape($p->nama_perusahaan) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Password -->
                    <div class="col-12">
                        <hr class="my-1">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Password <span class="text-danger" id="pwdRequired">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="inp_password" placeholder="Kosongkan jika tidak diubah">
                            <button class="btn btn-outline-secondary" type="button" id="btnTogglePwd">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="form-text" id="pwdHint">Kosongkan jika tidak ingin mengubah password</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="inp_password_konfirm" placeholder="Ulangi password">
                    </div>

                    <!-- Roles -->
                    <div class="col-12">
                        <hr class="my-1">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Role / Hak Akses <span class="text-danger">*</span></label>
                        <div class="alert alert-info py-2 small mb-2">
                            <i class="bi bi-info-circle me-1"></i>
                            User bisa memiliki lebih dari satu role. Role pertama yang dipilih menjadi role utama.
                        </div>
                        <div class="row g-2" id="rolesContainer">
                            <?php foreach ($roles as $r): ?>
                                <div class="col-md-4 col-6">
                                    <div class="form-check border rounded p-2 role-card" data-role="<?= $r->id_role ?>">
                                        <input class="form-check-input role-checkbox" type="checkbox"
                                            name="roles[]" value="<?= $r->id_role ?>"
                                            id="role_<?= $r->id_role ?>">
                                        <label class="form-check-label w-100" for="role_<?= $r->id_role ?>">
                                            <?php
                                            $icons = [
                                                1 => 'bi-shield-fill-check',
                                                2 => 'bi-person-fill',
                                                3 => 'bi-tools',
                                                4 => 'bi-heart-pulse-fill',
                                                5 => 'bi-star-fill'
                                            ];
                                            $colors = [1 => 'danger', 2 => 'primary', 3 => 'warning', 4 => 'success', 5 => 'dark'];
                                            $icon  = $icons[$r->id_role]  ?? 'bi-person';
                                            $color = $colors[$r->id_role] ?? 'secondary';
                                            ?>
                                            <i class="bi <?= $icon ?> text-<?= $color ?> me-1"></i>
                                            <span class="small fw-semibold"><?= html_escape($r->nama_role) ?></span>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="text-danger small mt-1 d-none" id="err_roles">Pilih minimal satu role.</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSaveUser">
                    <i class="bi bi-save me-1"></i>Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    var CSRF_NAME = '<?= $this->security->get_csrf_token_name() ?>';
    var CSRF_TOKEN = '<?= $this->security->get_csrf_hash() ?>';
    var BASE_URL = '<?= base_url() ?>';
    var SITE_URL = '<?= site_url() ?>';

    $('#inp_departemen').select2({
        placeholder: '-- Pilih Departemen / Perusahaan --',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#modalUser'),
        tags: true
    });

    // ── Load tabel ────────────────────────────────────────────
    function loadUsers() {
        var search = $('#searchUser').val();
        var isActive = $('#filterStatus').val();
        $.ajax({
            url: SITE_URL + '/usermanagement/get_data',
            type: 'POST',
            data: {
                [CSRF_NAME]: CSRF_TOKEN,
                search: search,
                is_active: isActive
            },
            dataType: 'json',
            success: function(res) {
                var html = '';
                if (!res.data || res.data.length === 0) {
                    html = '<tr><td colspan="7" class="text-center text-muted py-4"><i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>Tidak ada data user.</td></tr>';
                } else {
                    $.each(res.data, function(i, r) {
                        html += '<tr>' +
                            '<td class="text-center">' + r.foto + '</td>' +
                            '<td>' + r.nama + '</td>' +
                            '<td class="small">' + r.email + '</td>' +
                            '<td class="small">' + r.jabatan + '</td>' +
                            '<td>' + r.roles + '</td>' +
                            '<td>' + r.status + '</td>' +
                            '<td class="text-center">' + r.aksi + '</td>' +
                            '</tr>';
                    });
                }
                $('#tblUserBody').html(html);
                CSRF_TOKEN = res.csrf_token || CSRF_TOKEN;
            }
        });
    }
    loadUsers();
    $('#searchUser').on('input', debounce(loadUsers, 400));
    $('#filterStatus').on('change', loadUsers);

    // ── Modal buka tambah ─────────────────────────────────────
    $('#btnTambahUser').on('click', function() {
        resetModalUser();
        $('#modalUserTitle').html('<i class="bi bi-person-plus me-2 text-primary"></i>Tambah User');
        $('#pwdRequired').show();
        $('#pwdHint').text('Password wajib diisi untuk user baru.');
        $('#modalUser').modal('show');
    });

    // ── Modal buka edit ───────────────────────────────────────
    $(document).on('click', '.btn-edit-user', function() {
        var id = $(this).data('id');
        resetModalUser();
        $('#modalUserTitle').html('<i class="bi bi-pencil me-2 text-warning"></i>Edit User');
        $('#pwdRequired').hide();
        $('#pwdHint').text('Kosongkan jika tidak ingin mengubah password.');

        $.ajax({
            url: SITE_URL + '/usermanagement/get_detail',
            type: 'POST',
            data: {
                [CSRF_NAME]: CSRF_TOKEN,
                id_user: id
            },
            dataType: 'json',
            success: function(res) {
                if (res.status !== 'success') {
                    toastr.error(res.message);
                    return;
                }
                var d = res.data;
                $('#hid_id_user').val(d.id_user);
                $('#inp_nama').val(d.nama);
                $('#inp_username').val(d.username);
                $('#inp_email').val(d.email);
                $('#inp_jabatan').val(d.jabatan || '');
                $('#inp_no_hp').val(d.no_hp || '');
                $('#inp_departemen').val(d.departemen || '').trigger('change');

                // Foto
                if (d.foto) {
                    $('#fotoPreview').attr('src', BASE_URL + d.foto).removeClass('d-none');
                    $('#fotoInitial').addClass('d-none');
                }

                // Roles
                $('.role-checkbox').prop('checked', false);
                $('.role-card').removeClass('border-primary bg-primary bg-opacity-10');
                if (d.roles_ids) {
                    d.roles_ids.split(',').forEach(function(rid) {
                        $('#role_' + rid.trim()).prop('checked', true);
                        $('#role_' + rid.trim()).closest('.role-card').addClass('border-primary bg-primary bg-opacity-10');
                    });
                }

                $('#modalUser').modal('show');
            }
        });
    });

    // ── Simpan user ───────────────────────────────────────────
    $('#btnSaveUser').on('click', function() {
        var roles = [];
        $('.role-checkbox:checked').each(function() {
            roles.push($(this).val());
        });
        if (roles.length === 0) {
            $('#err_roles').removeClass('d-none');
            return;
        }
        $('#err_roles').addClass('d-none');

        var pw = $('#inp_password').val();
        var pw2 = $('#inp_password_konfirm').val();
        if (pw && pw !== pw2) {
            toastr.error('Konfirmasi password tidak cocok.');
            return;
        }

        var fd = new FormData();
        fd.append(CSRF_NAME, CSRF_TOKEN);
        fd.append('id_user', $('#hid_id_user').val());
        fd.append('nama', $('#inp_nama').val());
        fd.append('username', $('#inp_username').val());
        fd.append('email', $('#inp_email').val());
        fd.append('jabatan', $('#inp_jabatan').val());
        fd.append('no_hp', $('#inp_no_hp').val());
        fd.append('departemen', $('#inp_departemen').val());
        fd.append('password', pw);
        roles.forEach(function(r) {
            fd.append('roles[]', r);
        });

        var fotoFile = document.getElementById('inputFotoUser').files[0];
        if (fotoFile) fd.append('foto', fotoFile);

        NProgress.start();
        $.ajax({
            url: SITE_URL + '/usermanagement/save',
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                NProgress.done();
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    $('#modalUser').modal('hide');
                    loadUsers();
                } else {
                    toastr.error(res.message);
                }
            },
            error: function() {
                NProgress.done();
                toastr.error('Terjadi kesalahan server.');
            }
        });
    });

    // ── Toggle aktif ──────────────────────────────────────────
    $(document).on('click', '.btn-toggle-user', function() {
        var id = $(this).data('id');
        var active = $(this).data('active');
        var label = active == 1 ? 'nonaktifkan' : 'aktifkan';
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Yakin ingin ' + label + ' user ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal',
            confirmButtonColor: active == 1 ? '#ffc107' : '#198754',
        }).then(function(r) {
            if (!r.isConfirmed) return;
            $.ajax({
                url: SITE_URL + '/usermanagement/toggle_active',
                type: 'POST',
                data: {
                    [CSRF_NAME]: CSRF_TOKEN,
                    id_user: id
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        toastr.success('Status user diperbarui.');
                        loadUsers();
                    } else toastr.error(res.message);
                }
            });
        });
    });

    // ── Delete user ───────────────────────────────────────────
    $(document).on('click', '.btn-delete-user', function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Hapus User?',
            text: 'Tindakan ini tidak dapat dibatalkan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
        }).then(function(r) {
            if (!r.isConfirmed) return;
            $.ajax({
                url: SITE_URL + '/user_management/delete',
                type: 'POST',
                data: {
                    [CSRF_NAME]: CSRF_TOKEN,
                    id_user: id
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        toastr.success(res.message);
                        loadUsers();
                    } else toastr.error(res.message);
                }
            });
        });
    });

    // ── Preview foto ──────────────────────────────────────────
    $('#inputFotoUser').on('change', function() {
        var file = this.files[0];
        if (!file) return;
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#fotoPreview').attr('src', e.target.result).removeClass('d-none');
            $('#fotoInitial').addClass('d-none');
        };
        reader.readAsDataURL(file);
    });

    // ── Toggle password visibility ────────────────────────────
    $('#btnTogglePwd').on('click', function() {
        var inp = $('#inp_password');
        var isText = inp.attr('type') === 'text';
        inp.attr('type', isText ? 'password' : 'text');
        $(this).find('i').toggleClass('bi-eye bi-eye-slash');
    });

    // ── Role card highlight ───────────────────────────────────
    $(document).on('change', '.role-checkbox', function() {
        var card = $(this).closest('.role-card');
        if ($(this).is(':checked')) {
            card.addClass('border-primary bg-primary bg-opacity-10');
        } else {
            card.removeClass('border-primary bg-primary bg-opacity-10');
        }
        if ($('.role-checkbox:checked').length > 0) $('#err_roles').addClass('d-none');
    });

    // ── Reset modal ───────────────────────────────────────────
    function resetModalUser() {
        $('#hid_id_user').val('');
        $('#inp_nama, #inp_username, #inp_email, #inp_jabatan, #inp_no_hp, #inp_password, #inp_password_konfirm').val('');
        $('#inp_departemen').val('').trigger('change');
        $('#fotoPreview').addClass('d-none').attr('src', '');
        $('#fotoInitial').removeClass('d-none');
        document.getElementById('inputFotoUser').value = '';
        $('.role-checkbox').prop('checked', false);
        $('.role-card').removeClass('border-primary bg-primary bg-opacity-10');
        $('#err_roles').addClass('d-none');
        $('#inp_password').attr('type', 'password');
        $('#btnTogglePwd i').removeClass('bi-eye-slash').addClass('bi-eye');
    }

    // ── Debounce helper ───────────────────────────────────────
    function debounce(fn, delay) {
        var t;
        return function() {
            clearTimeout(t);
            t = setTimeout(fn, delay);
        };
    }
</script>