<?php
// Roles array check
$_roles_raw = $this->session->userdata('roles');
$_role_int  = (int)$this->session->userdata('role');
$_roles     = is_array($_roles_raw) ? array_map('intval', $_roles_raw) : [$_role_int];

$isAdmin     = in_array(1, $_roles);
$isKTT       = in_array(2, $_roles);
$isOHSSupt   = in_array(3, $_roles);
$isInspektor = in_array(4, $_roles);
$isAdminOHS  = in_array(5, $_roles);
?>

<main id="main" class="main">

    <div class="pagetitle d-flex align-items-center justify-content-between">
        <div>
            <h1>Pencabutan Stiker Kelayakan</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item">Approval</li>
                    <li class="breadcrumb-item active">Pencabutan Stiker</li>
                </ol>
            </nav>
        </div>
        <?php if ($isAdmin || $isKTT || $isOHSSupt || $isInspektor): ?>
            <div>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalRequestCabut">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Request Pencabutan Stiker
                </button>
            </div>
        <?php endif; ?>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <?php if ($this->session->flashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-1"></i>
                        <?= $this->session->flashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-octagon me-1"></i>
                        <?= $this->session->flashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body pt-3">

                        <!-- Filter Status -->
                        <form method="get" action="<?= site_url('approval/pencabutan') ?>" class="row g-2 mb-3">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Cari no. stiker / no. polisi..." value="<?= html_escape($this->input->get('search')) ?>">
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-select" onchange="this.form.submit()">
                                    <option value="">-- Semua Status Request --</option>
                                    <option value="menunggu_ohs_supt" <?= $this->input->get('status') === 'menunggu_ohs_supt' ? 'selected' : '' ?>>Menunggu OHS Supt</option>
                                    <option value="menunggu_ktt_1" <?= $this->input->get('status') === 'menunggu_ktt_1' ? 'selected' : '' ?>>Menunggu KTT 1</option>
                                    <option value="menunggu_ktt_2" <?= $this->input->get('status') === 'menunggu_ktt_2' ? 'selected' : '' ?>>Menunggu KTT 2</option>
                                    <option value="siap_dicabut" <?= $this->input->get('status') === 'siap_dicabut' ? 'selected' : '' ?>>Siap Eksekusi (Admin OHS)</option>
                                    <option value="dilaksanakan" <?= $this->input->get('status') === 'dilaksanakan' ? 'selected' : '' ?>>Telah Dicabut</option>
                                    <option value="ditolak" <?= $this->input->get('status') === 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-secondary w-100"><i class="bi bi-search"></i> Cari</button>
                            </div>
                        </form>

                        <!-- Tabel Daftar Request Pencabutan -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>No. Stiker / Unit</th>
                                        <th>Pengaju</th>
                                        <th>Alasan Pencabutan</th>
                                        <th>Tanggal Request</th>
                                        <th>Status Workflow</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($requests)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="bi bi-inbox fs-2 d-block mb-1"></i>
                                                Belum ada data permohonan pencabutan stiker.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php $no = 1; foreach ($requests as $r): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td>
                                                    <span class="badge bg-primary text-wrap mb-1"><?= html_escape($r->nomor_sticker ?? '-') ?></span><br>
                                                    <strong><?= html_escape($r->no_polisi ?? '-') ?></strong><br>
                                                    <small class="text-muted">No. Unit: </small><span class="badge bg-dark font-monospace" style="font-size:10px;"><?= html_escape(!empty($r->nomor_unit) ? $r->nomor_unit : '-') ?></span><br>
                                                    <small class="text-muted"><?= html_escape($r->jenis_kendaraan ?? '') ?> (<?= html_escape($r->perusahaan ?? '') ?>)</small>
                                                </td>
                                                <td>
                                                    <strong><?= html_escape($r->nama_pemungut_cabut ?? 'User System') ?></strong><br>
                                                    <?php
                                                    $role_labels = [2 => 'KTT', 3 => 'OHS Superintendent', 4 => 'Inspektor'];
                                                    $lbl = $role_labels[$r->role_pemohon] ?? 'Petugas';
                                                    ?>
                                                    <span class="badge bg-light text-dark border"><?= $lbl ?></span>
                                                </td>
                                                <td>
                                                    <div style="max-width: 250px; font-size: 13px;">
                                                        <?= nl2br(html_escape($r->alasan)) ?>
                                                    </div>
                                                </td>
                                                <td><small><?= date('d M Y H:i', strtotime($r->tgl_perintah)) ?></small></td>
                                                <td>
                                                    <?php if ($r->status_request === 'menunggu_ohs_supt'): ?>
                                                        <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Menunggu OHS Supt</span>
                                                    <?php elseif ($r->status_request === 'menunggu_ktt_1'): ?>
                                                        <span class="badge bg-info text-white"><i class="bi bi-clock me-1"></i>Menunggu KTT 1</span>
                                                    <?php elseif ($r->status_request === 'menunggu_ktt_2'): ?>
                                                        <span class="badge bg-primary text-white"><i class="bi bi-clock me-1"></i>Menunggu KTT 2</span>
                                                    <?php elseif ($r->status_request === 'siap_dicabut' || $r->status === 'diperintahkan'): ?>
                                                        <span class="badge bg-danger text-white pulse"><i class="bi bi-exclamation-circle me-1"></i>Siap Eksekusi Admin OHS</span>
                                                    <?php elseif ($r->status_request === 'dilaksanakan' || $r->status === 'dilaksanakan'): ?>
                                                        <span class="badge bg-dark text-white"><i class="bi bi-check-circle me-1"></i>Telah Dicabut</span>
                                                        <?php if ($r->nama_eksekutor): ?>
                                                            <br><small class="text-muted" style="font-size: 10px;">oleh <?= html_escape($r->nama_eksekutor) ?></small>
                                                        <?php endif; ?>
                                                    <?php elseif ($r->status_request === 'ditolak'): ?>
                                                        <span class="badge bg-secondary text-white"><i class="bi bi-x-circle me-1"></i>Ditolak</span>
                                                        <?php if ($r->catatan_penolakan): ?>
                                                            <br><small class="text-danger" style="font-size: 10px;"><?= html_escape($r->catatan_penolakan) ?></small>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <!-- Aksi OHS Supt Approve -->
                                                    <?php if (($isAdmin || $isOHSSupt) && $r->status_request === 'menunggu_ohs_supt'): ?>
                                                        <button class="btn btn-sm btn-success btn-approve-cabut me-1" data-id="<?= $r->id_cabut ?>" title="Approve ke KTT">
                                                            <i class="bi bi-check-lg"></i> Approve
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger btn-reject-cabut" data-id="<?= $r->id_cabut ?>" title="Tolak">
                                                            <i class="bi bi-x-lg"></i>
                                                        </button>
                                                    <?php endif; ?>

                                                    <!-- Aksi KTT Approve (KTT 1 & KTT 2) -->
                                                    <?php if (($isAdmin || $isKTT) && ($r->status_request === 'menunggu_ktt_1' || $r->status_request === 'menunggu_ktt_2')): ?>
                                                        <button class="btn btn-sm btn-success btn-approve-cabut me-1" data-id="<?= $r->id_cabut ?>" title="Approve KTT">
                                                            <i class="bi bi-check-circle-fill"></i> Approve KTT
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger btn-reject-cabut" data-id="<?= $r->id_cabut ?>" title="Tolak">
                                                            <i class="bi bi-x-lg"></i>
                                                        </button>
                                                    <?php endif; ?>

                                                    <!-- Aksi Admin OHS Eksekusi Pencabutan Stiker -->
                                                    <?php if (($isAdmin || $isAdminOHS) && ($r->status_request === 'siap_dicabut' || ($r->status === 'diperintahkan' && $r->status_request !== 'dilaksanakan'))): ?>
                                                        <button class="btn btn-sm btn-danger btn-eksekusi-cabut" data-id="<?= $r->id_cabut ?>" data-stiker="<?= html_escape($r->nomor_sticker) ?>" data-nopol="<?= html_escape($r->no_polisi) ?>">
                                                            <i class="bi bi-scissors"></i> Eksekusi Pencabutan
                                                        </button>
                                                    <?php endif; ?>

                                                    <?php if ($r->status_request === 'dilaksanakan'): ?>
                                                        <span class="text-muted small"><i class="bi bi-check-all"></i> Selesai</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

</main>

<!-- Modal Request Pencabutan Stiker -->
<div class="modal fade" id="modalRequestCabut" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-1"></i> Form Request Pencabutan Stiker</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formRequestCabut">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label font-semibold">Pilih Unit Kendaraan Stiker Aktif <span class="text-danger">*</span></label>
                        <select name="id_pengajuan" id="req_id_pengajuan" class="form-select" required>
                            <option value="">-- Pilih Unit / Nomor Stiker --</option>
                            <?php
                            $stiker_aktif = $this->db
                                ->select('sr.id_sticker, sr.nomor_sticker, pu.id_pengajuan, k.no_polisi, k.merk, k.tipe, t.nama_tipe AS jenis_kendaraan')
                                ->from('sticker_release sr')
                                ->join('pengajuan_uji pu', 'pu.id_pengajuan = sr.id_pengajuan')
                                ->join('kendaraan k', 'k.id_kendaraan = pu.id_kendaraan')
                                ->join('tipe_kendaraan t', 't.id_tipe_kendaraan = k.id_tipe_kendaraan', 'left')
                                ->where('sr.dicabut', 0)
                                ->where('pu.status', 'stiker_keluar')
                                ->get()->result();
                            foreach ($stiker_aktif as $sa):
                            ?>
                                <option value="<?= $sa->id_pengajuan ?>">
                                    <?= html_escape($sa->nomor_sticker) ?> — <?= html_escape($sa->no_polisi) ?> (<?= html_escape($sa->merk . ' ' . $sa->tipe) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-semibold">Alasan Pencabutan Stiker <span class="text-danger">*</span></label>
                        <textarea name="alasan" id="req_alasan" class="form-control" rows="4" placeholder="Jelaskan alasan detail kenapa stiker unit ini harus dicabut..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger" id="btnSubmitRequest">
                        <i class="bi bi-send me-1"></i> Kirim Request Pencabutan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Reject Request -->
<div class="modal fade" id="modalRejectCabut" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title">Tolak Permohonan Pencabutan Stiker</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formRejectCabut">
                <input type="hidden" name="id_cabut" id="rej_id_cabut">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan Permohonan <span class="text-danger">*</span></label>
                        <textarea name="catatan" id="rej_catatan" class="form-control" rows="3" required placeholder="Tuliskan catatan kenapa permohonan pencabutan ini ditolak..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var siteUrl = "<?= site_url() ?>";

    // Form Request Pencabutan
    $('#formRequestCabut').on('submit', function(e) {
        e.preventDefault();
        var btn = $('#btnSubmitRequest');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Mengirim...');

        $.ajax({
            url: siteUrl + 'approval/request_cabut',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                btn.prop('disabled', false).html('<i class="bi bi-send me-1"></i> Kirim Request Pencabutan');
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        html: res.message,
                        confirmButtonText: 'OK'
                    }).then(function() {
                        location.reload();
                    });
                } else {
                    Swal.fire('Gagal!', res.message, 'error');
                }
            },
            error: function() {
                btn.prop('disabled', false).html('<i class="bi bi-send me-1"></i> Kirim Request Pencabutan');
                Swal.fire('Error!', 'Terjadi kesalahan sistem.', 'error');
            }
        });
    });

    // Approve Request (OHS Supt / KTT)
    $('.btn-approve-cabut').on('click', function() {
        var id_cabut = $(this).data('id');
        Swal.fire({
            title: 'Setujui Permohonan?',
            text: "Permohonan pencabutan stiker ini akan disetujui dan diteruskan ke tahap berikutnya.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Setujui'
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: siteUrl + 'approval/approve_cabut',
                    type: 'POST',
                    data: { id_cabut: id_cabut },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire('Berhasil!', res.message, 'success').then(function() {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal!', res.message, 'error');
                        }
                    }
                });
            }
        });
    });

    // Reject Request Modal
    $('.btn-reject-cabut').on('click', function() {
        var id_cabut = $(this).data('id');
        $('#rej_id_cabut').val(id_cabut);
        $('#modalRejectCabut').modal('show');
    });

    $('#formRejectCabut').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: siteUrl + 'approval/reject_cabut',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    Swal.fire('Ditolak!', res.message, 'success').then(function() {
                        location.reload();
                    });
                } else {
                    Swal.fire('Gagal!', res.message, 'error');
                }
            }
        });
    });

    // Eksekusi Pencabutan (Admin OHS)
    $('.btn-eksekusi-cabut').on('click', function() {
        var id_cabut = $(this).data('id');
        var stiker = $(this).data('stiker');
        var nopol = $(this).data('nopol');

        Swal.fire({
            title: 'Eksekusi Pencabutan Stiker?',
            html: "Stiker <strong>" + stiker + "</strong> (Unit: " + nopol + ") akan <strong>RESMI DICABUT</strong>.<br>Notifikasi email akan dikirimkan ke pihak-pihak terkait.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Cabut Stiker Sekarang!'
        }).then(function(result) {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses Pencabutan...',
                    text: 'Mohon tunggu, sistem sedang mencabut stiker dan mengirimkan email notifikasi.',
                    allowOutsideClick: false,
                    didOpen: function() { Swal.showLoading(); }
                });

                $.ajax({
                    url: siteUrl + 'approval/eksekusi_cabut',
                    type: 'POST',
                    data: { id_cabut: id_cabut },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire('Stiker Dicabut!', res.message, 'success').then(function() {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal!', res.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Terjadi kesalahan sistem saat mengeksekusi pencabutan.', 'error');
                    }
                });
            }
        });
    });
});
</script>
