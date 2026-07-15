<main id="main" class="main">
    <div class="pagetitle">
        <h1>Profil Saya</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Home</a></li>
                <li class="breadcrumb-item active">Profil</li>
            </ol>
        </nav>
    </div>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i><?= $this->session->flashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <section class="section profile">
        <div class="row">

            <!-- ── Kartu Info Kiri ── -->
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

                        <!-- Foto & upload -->
                        <div class="position-relative mb-3" style="cursor:pointer;" id="fotoWrapper">
                            <?php if ($profil->foto): ?>
                                <img src="<?= base_url($profil->foto) ?>?v=<?= time() ?>" id="fotoProfilImg"
                                    class="rounded-circle shadow" style="width:110px;height:110px;object-fit:cover;">
                            <?php else: ?>
                                <div id="fotoProfilInitial"
                                    class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white shadow"
                                    style="width:110px;height:110px;font-size:2.5rem;">
                                    <?= strtoupper(substr($profil->nama, 0, 1)) ?>
                                </div>
                                <img id="fotoProfilImg" src="" class="rounded-circle shadow d-none"
                                    style="width:110px;height:110px;object-fit:cover;">
                            <?php endif; ?>
                            <label for="inputFotoProfil"
                                class="position-absolute bottom-0 end-0 rounded-circle bg-primary text-white
                            d-flex align-items-center justify-content-center shadow"
                                style="width:32px;height:32px;cursor:pointer;font-size:14px;" title="Ganti foto">
                                <i class="bi bi-camera"></i>
                            </label>
                            <input type="file" id="inputFotoProfil" accept="image/jpeg,image/png,image/webp" class="d-none">
                        </div>

                        <h4 class="fw-bold mb-1"><?= html_escape($profil->nama) ?></h4>
                        <div class="text-muted mb-2"><?= html_escape($profil->jabatan ?? '—') ?></div>

                        <!-- Roles badges -->
                        <div class="d-flex flex-wrap justify-content-center gap-1 mb-3">
                            <?php
                            $role_icons  = [
                                1 => 'bi-shield-fill-check text-danger',
                                2 => 'bi-person-fill text-primary',
                                3 => 'bi-tools text-warning',
                                4 => 'bi-heart-pulse-fill text-success',
                                5 => 'bi-star-fill text-dark'
                            ];
                            if ($profil->roles_label) {
                                $rids   = explode(',', $profil->roles_ids ?? '');
                                $rnames = explode(', ', $profil->roles_label);
                                foreach ($rnames as $i => $rname):
                                    $rid  = trim($rids[$i] ?? '');
                                    $icon = $role_icons[$rid] ?? 'bi-person text-secondary';
                            ?>
                                    <span class="badge bg-light text-dark border px-2 py-1" style="font-size:12px;">
                                        <i class="bi <?= $icon ?> me-1"></i><?= html_escape($rname) ?>
                                    </span>
                            <?php endforeach;
                            } ?>
                        </div>

                        <!-- Info singkat -->
                        <div class="w-100 border-top pt-3">
                            <?php if ($profil->departemen): ?>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="bi bi-building text-muted" style="width:18px;"></i>
                                    <span class="small"><?= html_escape($profil->departemen) ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if ($profil->no_hp): ?>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="bi bi-telephone text-muted" style="width:18px;"></i>
                                    <span class="small"><?= html_escape($profil->no_hp) ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-envelope text-muted" style="width:18px;"></i>
                                <span class="small"><?= html_escape($profil->email) ?></span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-calendar text-muted" style="width:18px;"></i>
                                <span class="small">Bergabung <?= $profil->created_at ? date('d M Y', strtotime($profil->created_at)) : '—' ?></span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- ── Tab Edit Kanan ── -->
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-body pt-3">
                        <ul class="nav nav-tabs nav-tabs-bordered" id="profilTabs">
                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-info">
                                    <i class="bi bi-person me-1"></i>Informasi Akun
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-password">
                                    <i class="bi bi-lock me-1"></i>Ganti Password
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content pt-3">

                            <!-- TAB INFO -->
                            <div class="tab-pane fade show active" id="tab-info">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="pro_nama" value="<?= html_escape($profil->nama) ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Username</label>
                                        <input type="text" class="form-control bg-light" value="<?= html_escape($profil->username) ?>" readonly>
                                        <div class="form-text">Username tidak dapat diubah</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="pro_email" value="<?= html_escape($profil->email) ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">No. HP</label>
                                        <input type="text" class="form-control" id="pro_no_hp" value="<?= html_escape($profil->no_hp ?? '') ?>" placeholder="+62...">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Jabatan</label>
                                        <input type="text" class="form-control" id="pro_jabatan" value="<?= html_escape($profil->jabatan ?? '') ?>" placeholder="Jabatan / posisi">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Departemen</label>
                                        <input type="text" class="form-control" id="pro_departemen" value="<?= html_escape($profil->departemen ?? '') ?>" placeholder="Nama departemen">
                                    </div>

                                    <!-- Role (read-only) -->
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Role / Hak Akses</label>
                                        <div class="form-control bg-light" style="min-height:42px;">
                                            <?php if ($profil->roles_label): ?>
                                                <?php foreach (explode(', ', $profil->roles_label) as $rn): ?>
                                                    <span class="badge bg-secondary me-1"><?= html_escape($rn) ?></span>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <span class="text-muted small">Belum ada role</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-text">Role hanya dapat diubah oleh Administrator</div>
                                    </div>

                                    <div class="col-12 text-end">
                                        <button type="button" class="btn btn-primary px-4" id="btnSaveProfil">
                                            <i class="bi bi-save me-1"></i>Simpan Perubahan
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB PASSWORD -->
                            <div class="tab-pane fade" id="tab-password">
                                <div class="row g-3" style="max-width:480px;">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Password Lama <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="pwd_lama" placeholder="Password saat ini">
                                            <span class="input-group-text toggle-pwd" data-target="pwd_lama" style="cursor:pointer;">
                                                <i class="bi bi-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Password Baru <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="pwd_baru" placeholder="Min. 6 karakter">
                                            <span class="input-group-text toggle-pwd" data-target="pwd_baru" style="cursor:pointer;">
                                                <i class="bi bi-eye"></i>
                                            </span>
                                        </div>
                                        <div id="pwdStrength" class="mt-1"></div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Konfirmasi Password <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="pwd_konfirm" placeholder="Ulangi password baru">
                                            <span class="input-group-text toggle-pwd" data-target="pwd_konfirm" style="cursor:pointer;">
                                                <i class="bi bi-eye"></i>
                                            </span>
                                        </div>
                                        <div id="pwdMatch" class="small mt-1"></div>
                                    </div>
                                    <div class="col-12">
                                        <button type="button" class="btn btn-warning px-4" id="btnGantiPassword">
                                            <i class="bi bi-lock me-1"></i>Ubah Password
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</main>

<script>
    var CSRF_NAME = '<?= $this->security->get_csrf_token_name() ?>';
    var CSRF_TOKEN = '<?= $this->security->get_csrf_hash() ?>';
    var SITE_URL = '<?= site_url() ?>';

    // ── Upload foto langsung saat pilih ──────────────────────
    $('#inputFotoProfil').on('change', function() {
        var file = this.files[0];
        if (!file) return;

        // Preview lokal dulu
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#fotoProfilImg').attr('src', e.target.result).removeClass('d-none');
            $('#fotoProfilInitial').addClass('d-none');
        };
        reader.readAsDataURL(file);

        // Upload ke server
        var fd = new FormData();
        fd.append(CSRF_NAME, CSRF_TOKEN);
        fd.append('foto', file);
        NProgress.start();
        $.ajax({
            url: SITE_URL + '/profil/update_foto',
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                NProgress.done();
                if (res.status === 'success') {
                    toastr.success(res.message);
                    // Update semua foto di header tanpa reload penuh
                    $('img.rounded-circle[src*="foto_user"]').attr('src', res.foto_url + '?v=' + Date.now());
                    $('.nav-profile img').attr('src', res.foto_url + '?v=' + Date.now());
                } else {
                    toastr.error(res.message);
                }
            },
            error: function() {
                NProgress.done();
                toastr.error('Gagal upload foto.');
            }
        });
    });

    // ── Simpan profil ─────────────────────────────────────────
    $('#btnSaveProfil').on('click', function() {
        NProgress.start();
        $.ajax({
            url: SITE_URL + '/profil/update',
            type: 'POST',
            data: {
                [CSRF_NAME]: CSRF_TOKEN,
                nama: $('#pro_nama').val(),
                email: $('#pro_email').val(),
                no_hp: $('#pro_no_hp').val(),
                jabatan: $('#pro_jabatan').val(),
                departemen: $('#pro_departemen').val(),
            },
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
                        })
                        .then(() => location.reload());
                } else {
                    toastr.error(res.message);
                }
            },
            error: function() {
                NProgress.done();
                toastr.error('Gagal menyimpan.');
            }
        });
    });

    // ── Toggle password visibility ────────────────────────────
    $(document).on('click', '.toggle-pwd', function() {
        var targetId = $(this).data('target');
        var inp = $('#' + targetId);
        var isText = inp.attr('type') === 'text';
        inp.attr('type', isText ? 'password' : 'text');
        $(this).find('i').toggleClass('bi-eye bi-eye-slash');
    });

    // ── Password strength ─────────────────────────────────────
    $('#pwd_baru').on('input', function() {
        var v = $(this).val();
        var strength = 0;
        if (v.length >= 6) strength++;
        if (v.length >= 10) strength++;
        if (/[A-Z]/.test(v)) strength++;
        if (/[0-9]/.test(v)) strength++;
        if (/[^A-Za-z0-9]/.test(v)) strength++;
        var colors = ['', 'danger', 'warning', 'warning', 'success', 'success'];
        var labels = ['', 'Sangat Lemah', 'Lemah', 'Sedang', 'Kuat', 'Sangat Kuat'];
        $('#pwdStrength').html(v ? '<div class="progress" style="height:4px;"><div class="progress-bar bg-' + colors[strength] + '" style="width:' + (strength * 20) + '%"></div></div><small class="text-' + colors[strength] + '">' + labels[strength] + '</small>' : '');
        checkMatch();
    });
    $('#pwd_konfirm').on('input', checkMatch);

    function checkMatch() {
        var b = $('#pwd_baru').val(),
            k = $('#pwd_konfirm').val();
        if (!k) {
            $('#pwdMatch').html('');
            return;
        }
        $('#pwdMatch').html(b === k ?
            '<span class="text-success"><i class="bi bi-check-circle me-1"></i>Password cocok</span>' :
            '<span class="text-danger"><i class="bi bi-x-circle me-1"></i>Password tidak cocok</span>');
    }

    // ── Ganti password ────────────────────────────────────────
    $('#btnGantiPassword').on('click', function() {
        var lama = $('#pwd_lama').val();
        var baru = $('#pwd_baru').val();
        var kfm = $('#pwd_konfirm').val();
        if (!lama || !baru || !kfm) {
            toastr.warning('Semua field wajib diisi.');
            return;
        }
        if (baru !== kfm) {
            toastr.error('Konfirmasi password tidak cocok.');
            return;
        }
        if (baru.length < 6) {
            toastr.error('Password baru minimal 6 karakter.');
            return;
        }

        NProgress.start();
        $.ajax({
            url: SITE_URL + '/profil/ganti_password',
            type: 'POST',
            data: {
                [CSRF_NAME]: CSRF_TOKEN,
                password_lama: lama,
                password_baru: baru,
                password_konfirm: kfm
            },
            dataType: 'json',
            success: function(res) {
                NProgress.done();
                if (res.status === 'success') {
                    Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message
                        })
                        .then(() => window.location.href = SITE_URL + '/auth/logout');
                } else {
                    toastr.error(res.message);
                }
            },
            error: function() {
                NProgress.done();
                toastr.error('Gagal.');
            }
        });
    });
</script>