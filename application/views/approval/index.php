<main id="main" class="main">

    <div class="pagetitle">
        <h1><?= html_escape($cfg['label']) ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                <li class="breadcrumb-item active"><?= html_escape($cfg['label']) ?></li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-12">

                <?php if (!empty($cfg['desc'])): ?>
                    <div class="alert alert-info py-2 d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-info-circle-fill flex-shrink-0"></i>
                        <span class="small"><?= html_escape($cfg['desc']) ?></span>
                    </div>
                <?php endif; ?>

                <?php
                $current_roles_raw = $this->session->userdata('roles');
                $current_role_int  = (int) $this->session->userdata('role');
                if (is_array($current_roles_raw) && !empty($current_roles_raw)) {
                    $current_roles = array_map('intval', $current_roles_raw);
                } elseif ($current_role_int > 0) {
                    $current_roles = [$current_role_int];
                } else {
                    $current_roles = [];
                }
                $is_ktt_or_admin = in_array(1, $current_roles) || in_array(2, $current_roles);
                ?>

                <div class="card">
                    <div class="card-body pt-4">

                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <h5 class="card-title mb-0">
                                <?= html_escape($cfg['label']) ?>
                                <?php if ($pending_count > 0): ?>
                                    <span class="badge bg-danger ms-2"><?= $pending_count ?> menunggu</span>
                                <?php endif; ?>
                            </h5>
                            <form method="get" class="d-flex gap-2">
                                <input type="text" name="search" class="form-control form-control-sm"
                                    placeholder="Cari no. polisi / pemohon..."
                                    value="<?= html_escape($this->input->get('search') ?? '') ?>"
                                    style="width:220px;">
                                <button class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i></button>
                            </form>
                        </div>

                        <?php if ($this->session->flashdata('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show py-2">
                                <?= $this->session->flashdata('success') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (empty($list)): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 opacity-40"></i>
                                <p class="mb-0">Tidak ada pengajuan yang perlu direview.</p>
                            </div>
                        <?php else: ?>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:40px;">#</th>
                                            <th>No. Pengajuan</th>
                                            <th>Kendaraan</th>
                                            <th>Pemohon</th>
                                            <th>Tgl Pengajuan</th>
                                            <th>Status</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($list as $i => $row):
                                            $sl         = $status_labels[$row->status] ?? ['bg-secondary text-white', $row->status];
                                            $is_pending = in_array($row->status, $status_masuk);
                                        ?>
                                            <tr class="<?= !$is_pending ? 'opacity-75' : '' ?>">
                                                <td><?= $i + 1 ?></td>
                                                <td>
                                                    <strong class="text-primary">
                                                        #PU-<?= str_pad($row->id_pengajuan, 4, '0', STR_PAD_LEFT) ?>
                                                    </strong>
                                                    <?php if (!empty($row->is_unit_baru)): ?>
                                                        <span class="badge bg-warning text-dark ms-1" style="font-size:10px;">Unit Baru</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="fw-semibold"><?= html_escape($row->no_polisi) ?></span><br>
                                                    <small class="text-muted">
                                                        <?= html_escape($row->jenis_kendaraan ?? '') ?>
                                                        — <?= html_escape($row->merk ?? '') ?>
                                                        <?= html_escape($row->tipe ?? '') ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <?= html_escape($row->nama_pemohon) ?><br>
                                                    <small class="text-muted"><?= html_escape($row->email_pemohon) ?></small>
                                                </td>
                                                <td>
                                                    <small><?= date('d M Y H:i', strtotime($row->tanggal_pengajuan)) ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge <?= $sl[0] ?>"><?= $sl[1] ?></span>
                                                    <?php if ($row->status === 'menunggu_ktt_2'): ?>
                                                        <br><small class="text-muted" style="font-size:10px;">
                                                            <i class="bi bi-person-check me-1"></i>1/2 KTT sudah approve
                                                        </small>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex gap-1 justify-content-center flex-wrap">

                                                        <!-- Tombol Detail -->
                                                        <a href="<?= site_url('approval/detail/' . $level . '/' . $row->id_pengajuan) ?>"
                                                            class="btn btn-sm btn-outline-primary py-0">
                                                            <i class="bi bi-eye me-1"></i>Detail
                                                        </a>

                                                        <?php if ($is_pending): ?>

                                                            <?php if ($level === 'release_stiker'): ?>
                                                                <button class="btn btn-sm btn-success py-0 btn-release-stiker text-white"
                                                                    data-id="<?= $row->id_pengajuan ?>"
                                                                    data-polisi="<?= html_escape($row->no_polisi) ?>">
                                                                    <i class="bi bi-patch-check me-1"></i>Terbitkan Stiker
                                                                </button>

                                                            <?php elseif ($level === 'verif_perbaikan'): ?>
                                                                <!-- Inspektor: Verifikasi Fisik Perbaikan -->
                                                                <button class="btn btn-sm btn-success py-0 btn-approve text-white"
                                                                    data-id="<?= $row->id_pengajuan ?>"
                                                                    data-polisi="<?= html_escape($row->no_polisi) ?>"
                                                                    title="Perbaikan OK — Lanjut Pengujian Ulang">
                                                                    <i class="bi bi-check-circle me-1"></i>ACC
                                                                </button>
                                                                <button class="btn btn-sm btn-danger py-0 btn-reject text-white"
                                                                    data-id="<?= $row->id_pengajuan ?>"
                                                                    data-polisi="<?= html_escape($row->no_polisi) ?>"
                                                                    title="Perbaikan Belum Sesuai">
                                                                    <i class="bi bi-x-circle me-1"></i>Tolak
                                                                </button>

                                                            <?php else: ?>
                                                                <!-- Tombol Approve/Reject standar -->
                                                                <?php if ($level === 'ktt' && in_array($row->id_pengajuan, $my_approvals)): ?>
                                                                    <span class="badge bg-success py-1 px-2"><i class="bi bi-check-circle me-1"></i>Sudah Approve</span>
                                                                <?php else: ?>
                                                                    <button class="btn btn-sm btn-success py-0 btn-approve text-white"
                                                                        data-id="<?= $row->id_pengajuan ?>"
                                                                        data-polisi="<?= html_escape($row->no_polisi) ?>">
                                                                        <i class="bi bi-check-lg"></i>
                                                                    </button>
                                                                    <?php if (!is_null($cfg['status_reject'] ?? null)): ?>
                                                                        <button class="btn btn-sm btn-danger py-0 btn-reject text-white"
                                                                            data-id="<?= $row->id_pengajuan ?>"
                                                                            data-polisi="<?= html_escape($row->no_polisi) ?>">
                                                                            <i class="bi bi-x-lg"></i>
                                                                        </button>
                                                                    <?php endif; ?>
                                                                <?php endif; ?>
                                                            <?php endif; ?>

                                                        <?php endif; ?>

                                                        <?php if ($row->status === 'stiker_keluar' && $is_ktt_or_admin): ?>
                                                            <button class="btn btn-sm btn-outline-dark py-0 btn-cabut-stiker"
                                                                data-id="<?= $row->id_pengajuan ?>"
                                                                data-polisi="<?= html_escape($row->no_polisi) ?>">
                                                                <i class="bi bi-scissors me-1"></i>Cabut Stiker
                                                            </button>
                                                        <?php endif; ?>

                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Modal Reject -->
<div class="modal fade" id="modalReject" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white">
                    <i class="bi bi-x-circle me-2"></i>
                    <?= $level === 'verif_perbaikan' ? 'Tolak — Perbaikan Belum Sesuai' : 'Tolak Pengajuan' ?>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-1">Pengajuan: <strong id="rejectPolisi"></strong></p>
                <?php if (!empty($cfg['reject_label'])): ?>
                    <p class="text-muted small mb-2"><?= html_escape($cfg['reject_label']) ?></p>
                <?php endif; ?>
                <label class="form-label fw-semibold">
                    <?= $level === 'verif_perbaikan' ? 'Catatan Temuan (apa yang masih kurang)' : 'Alasan Penolakan' ?>
                    <span class="text-danger">*</span>
                </label>
                <textarea class="form-control" id="rejectCatatan" rows="3"
                    placeholder="<?= $level === 'verif_perbaikan'
                                        ? 'Tuliskan apa yang masih perlu diperbaiki...'
                                        : 'Tuliskan alasan penolakan secara jelas...' ?>"
                    maxlength="500"></textarea>
                <small class="text-muted">Catatan ini akan tercatat dalam riwayat approval.</small>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-danger btn-sm text-white" id="btnKonfirmasiReject">
                    <i class="bi bi-x-circle me-1"></i>
                    <?= $level === 'verif_perbaikan' ? 'Konfirmasi Tolak' : 'Tolak' ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Release Stiker -->
<div class="modal fade" id="modalStiker" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title text-white"><i class="bi bi-patch-check me-2"></i>Terbitkan Stiker Kelayakan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="stikerModalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-success" role="status"></div>
                    <div class="text-muted small mt-2">Memuat detail...</div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-success btn-sm text-white" id="btnKonfirmasiStiker">
                    <i class="bi bi-patch-check me-1"></i>Terbitkan Stiker
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    var siteUrl = '<?= site_url() ?>';

    $(function() {
        var csrfName = '<?= $this->security->get_csrf_token_name() ?>';
        var csrfHash = '<?= $this->security->get_csrf_hash() ?>';
        var level = '<?= $level ?>';
        var modalReject = new bootstrap.Modal(document.getElementById('modalReject'));
        var modalStiker = new bootstrap.Modal(document.getElementById('modalStiker'));
        var activeId = null;

        // ── Approve ──────────────────────────────────────────────────
        $(document).on('click', '.btn-approve', function() {
            var id = $(this).data('id');
            var polisi = $(this).data('polisi');

            var title, html, btnText;

            if (level === 'verif_perbaikan') {
                title = 'ACC Verifikasi Fisik?';
                html = 'Perbaikan unit <strong>' + polisi + '</strong> dinyatakan <strong>SESUAI</strong>.<br>' +
                    'Unit akan berstatus <strong>Siap Pengujian Ulang</strong> dan inspektor akan diarahkan ke form checklist ulang.';
                btnText = '<i class="bi bi-check-circle me-1"></i>Ya, ACC';
            } else {
                title = 'Setujui Pengajuan?';
                html = 'Kendaraan <strong>' + polisi + '</strong> akan disetujui dan diteruskan ke tahap berikutnya.';
                btnText = '<i class="bi bi-check-lg me-1"></i>Ya, Setujui';
            }

            Swal.fire({
                title: title,
                html: html,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2eca6a',
                cancelButtonColor: '#6c757d',
                confirmButtonText: btnText,
                cancelButtonText: 'Batal',
            }).then(function(r) {
                if (r.isConfirmed) prosesApproval(id, 'approve', '', '');
            });
        });

        // ── Reject ───────────────────────────────────────────────────
        $(document).on('click', '.btn-reject', function() {
            activeId = $(this).data('id');
            $('#rejectPolisi').text($(this).data('polisi'));
            $('#rejectCatatan').val('');
            modalReject.show();
        });

        $('#btnKonfirmasiReject').on('click', function() {
            var catatan = $('#rejectCatatan').val().trim();
            if (!catatan) {
                toastr.warning('Catatan wajib diisi.');
                $('#rejectCatatan').focus();
                return;
            }
            modalReject.hide();
            prosesApproval(activeId, 'reject', catatan, '');
        });

        // ── Release Stiker ───────────────────────────────────────────
        $(document).on('click', '.btn-release-stiker', function() {
            activeId = $(this).data('id');
            $('#stikerModalBody').html(
                '<div class="text-center py-4"><div class="spinner-border text-success" role="status"></div>' +
                '<div class="text-muted small mt-2">Memuat detail...</div></div>'
            );
            modalStiker.show();

            var post = {};
            post[csrfName] = csrfHash;
            post.id_pengajuan = activeId;

            $.ajax({
                url: siteUrl + 'approval/get_detail_stiker',
                type: 'POST',
                data: post,
                dataType: 'json',
                success: function(res) {
                    if (!res || res.status !== 'success') {
                        $('#stikerModalBody').html('<div class="alert alert-danger">Gagal memuat detail.</div>');
                        return;
                    }
                    var d = res.data;
                    var tipeLabel = d.tipe_pengajuan === 'new_commissioning' ? 'New Commissioning' : 'Recommissioning';
                    var aksesIcon = d.tipe_akses === 'mining' ?
                        'bi-minecart-loaded text-danger' :
                        (d.tipe_akses === 'underground' ? 'bi-arrow-down-circle text-dark' : 'bi-building text-secondary');

                    $('#stikerModalBody').html(
                        '<div class="row g-3 mb-3">' +
                        '<div class="col-md-6"><div class="bg-light rounded p-3">' +
                        '<h6 class="fw-bold text-primary mb-2"><i class="bi bi-truck me-2"></i>Informasi Kendaraan</h6>' +
                        '<table class="table table-sm table-borderless mb-0">' +
                        '<tr><td class="text-muted fw-semibold" style="width:40%">No. Polisi</td>' +
                        '<td><span class="badge bg-dark font-monospace fs-6">' + (d.no_polisi || '—') + '</span></td></tr>' +
                        '<tr><td class="text-muted fw-semibold">Jenis</td><td><strong>' + (d.jenis_kendaraan || '—') + '</strong></td></tr>' +
                        '<tr><td class="text-muted fw-semibold">Merk / Tipe</td><td>' + (d.merk || '') + ' ' + (d.tipe_kendaraan || '') + '</td></tr>' +
                        '<tr><td class="text-muted fw-semibold">Tahun</td><td>' + (d.tahun || '—') + '</td></tr>' +
                        '<tr><td class="text-muted fw-semibold">Nomor Unit</td><td>' + (d.nomor_unit || '—') + '</td></tr>' +
                        '<tr><td class="text-muted fw-semibold">Perusahaan</td><td>' + (d.perusahaan || '—') + '</td></tr>' +
                        '<tr><td class="text-muted fw-semibold">No. Rangka</td><td class="small">' + (d.nomor_rangka || '—') + '</td></tr>' +
                        '<tr><td class="text-muted fw-semibold">No. Mesin</td><td class="small">' + (d.nomor_mesin || '—') + '</td></tr>' +
                        '</table></div></div>' +
                        '<div class="col-md-6"><div class="bg-light rounded p-3 mb-2">' +
                        '<h6 class="fw-bold text-success mb-2"><i class="bi bi-person me-2"></i>Pemohon</h6>' +
                        '<div class="small"><strong>' + (d.nama_pemohon || '—') + '</strong><br>' +
                        '<span class="text-muted">' + (d.email_pemohon || '—') + '</span></div>' +
                        '<div class="mt-2">' +
                        '<span class="badge bg-info text-white me-1">' + tipeLabel + '</span>' +
                        '<span class="badge bg-secondary text-white"><i class="bi ' + aksesIcon.split(' ')[0] + ' me-1"></i>' + d.tipe_akses + '</span>' +
                        '</div></div>' +
                        (res.tgl_expired ?
                            '<div class="alert alert-info py-2 small"><i class="bi bi-calendar-check me-1"></i>' +
                            '<strong>Stiker akan expired:</strong> ' + res.tgl_expired + ' (6 bulan dari ACC KTT)</div>' :
                            '') +
                        '</div></div>' +
                        '<hr class="my-2">' +
                        '<label class="form-label fw-semibold">Nomor Stiker <span class="text-danger">*</span></label>' +
                        '<input type="text" class="form-control" id="stikerNomor" placeholder="Contoh: STK-2026-0001" maxlength="50">' +
                        '<small class="text-muted">Email notifikasi akan dikirim ke Admin Departemen setelah stiker diterbitkan.</small>'
                    );
                    $('#stikerNomor').focus();
                },
                error: function() {
                    $('#stikerModalBody').html('<div class="alert alert-danger">Gagal memuat detail kendaraan.</div>');
                }
            });
        });

        $('#btnKonfirmasiStiker').on('click', function() {
            var nomor = $('#stikerNomor').val().trim();
            if (!nomor) {
                toastr.warning('Nomor stiker wajib diisi.');
                $('#stikerNomor').focus();
                return;
            }
            modalStiker.hide();
            prosesApproval(activeId, 'approve', '', nomor);
        });

        // ── Cabut Stiker ─────────────────────────────────────────────
        $(document).on('click', '.btn-cabut-stiker', function() {
            var id = $(this).data('id');
            var polisi = $(this).data('polisi');

            Swal.fire({
                title: 'Perintah Pencabutan Stiker?',
                html: 'Stiker kendaraan <strong>' + polisi + '</strong> akan dicabut.<br>' +
                    '<textarea id="alasanCabut" class="form-control mt-2" rows="3"' +
                    ' placeholder="Alasan pencabutan wajib diisi..."></textarea>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#212529',
                confirmButtonText: '<i class="bi bi-scissors me-1"></i>Cabut Stiker',
                cancelButtonText: 'Batal',
                preConfirm: function() {
                    var alasan = document.getElementById('alasanCabut').value.trim();
                    if (!alasan) {
                        Swal.showValidationMessage('Alasan pencabutan wajib diisi!');
                        return false;
                    }
                    return alasan;
                }
            }).then(function(r) {
                if (!r.isConfirmed) return;
                NProgress.start();
                var post = {};
                post[csrfName] = csrfHash;
                post.id_pengajuan = id;
                post.alasan = r.value;

                $.ajax({
                    url: siteUrl + 'approval/cabut_stiker',
                    type: 'POST',
                    data: post,
                    dataType: 'json',
                    success: function(res) {
                        NProgress.done();
                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                html: res.message,
                                timer: 2500,
                                showConfirmButton: false
                            });
                            setTimeout(function() {
                                location.reload();
                            }, 2600);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                html: res.message
                            });
                        }
                    },
                    error: function() {
                        NProgress.done();
                        toastr.error('Server error.');
                    }
                });
            });
        });

        // ── Kirim ke server ──────────────────────────────────────────
        function prosesApproval(id_pengajuan, aksi, catatan, nomor_stiker) {
            NProgress.start();
            var post = {
                level: level,
                id_pengajuan: id_pengajuan,
                aksi: aksi,
                catatan: catatan,
                nomor_stiker: nomor_stiker
            };
            post[csrfName] = csrfHash;

            $.ajax({
                url: siteUrl + 'approval/proses',
                type: 'POST',
                data: post,
                dataType: 'json',
                success: function(res) {
                    NProgress.done();
                    if (res.status === 'success') {
                        if (res.redirect_jadwal) {
                            Swal.fire({
                                title: 'Disetujui!',
                                html: res.message + '<br><small class="text-muted">Silakan buat jadwal inspeksi.</small>',
                                icon: 'success',
                                confirmButtonColor: '#4154f1',
                                confirmButtonText: 'Buat Jadwal Sekarang',
                            }).then(function() {
                                window.location.href = res.redirect_jadwal;
                            });
                            return;
                        }
                        // verif_perbaikan ACC → redirect langsung ke form checklist
                        if (aksi === 'approve' && res.redirect && res.redirect.indexOf('checklist/form') !== -1) {
                            Swal.fire({
                                title: 'Verifikasi Diterima!',
                                html: res.message,
                                icon: 'success',
                                confirmButtonColor: '#4154f1',
                                confirmButtonText: 'Isi Form Checklist Ulang',
                            }).then(function() {
                                window.location.href = res.redirect;
                            });
                            return;
                        }
                        var icon = aksi === 'approve' ? 'success' : 'warning';
                        var title = aksi === 'approve' ?
                            (level === 'verif_perbaikan' ? 'Verifikasi Diterima!' : 'Disetujui!') :
                            (level === 'verif_perbaikan' ? 'Perbaikan Ditolak' : 'Ditolak');
                        Swal.fire({
                            title: title,
                            html: res.message,
                            icon: icon,
                            timer: 2200,
                            showConfirmButton: false
                        });
                        setTimeout(function() {
                            window.location.href = res.redirect || location.href;
                        }, 2300);
                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            html: res.message,
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                },
                error: function() {
                    NProgress.done();
                    toastr.error('Terjadi kesalahan server.');
                }
            });
        }
    });
</script>