<?php

defined('BASEPATH') or exit('No direct script access allowed');
?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Edit Pengajuan</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url('pengajuan') ?>">Daftar Pengajuan</a></li>
                <li class="breadcrumb-item active">#PU-<?= str_pad($pengajuan->id_pengajuan, 4, '0', STR_PAD_LEFT) ?></li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row justify-content-center">
            <div class="col-xl-9">

                <!-- Header info pengajuan -->
                <div class="card mb-3 border-danger">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-danger d-flex align-items-center justify-content-center text-white flex-shrink-0"
                                style="width:50px;height:50px;font-size:1.3rem;">
                                <i class="bi bi-pencil-square"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold"><?= html_escape($pengajuan->no_polisi) ?></h5>
                                <small class="text-muted">
                                    <?= html_escape($pengajuan->jenis_kendaraan) ?> —
                                    <?= html_escape($pengajuan->merk) ?> <?= html_escape($pengajuan->tipe) ?>
                                </small>
                            </div>
                            <div class="ms-auto text-end">
                                <span class="badge bg-danger text-white px-3 py-2">
                                    <i class="bi bi-x-circle me-1"></i>Ditolak Manager
                                </span>
                                <div><small class="text-muted">#PU-<?= str_pad($pengajuan->id_pengajuan, 4, '0', STR_PAD_LEFT) ?></small></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Catatan penolakan Manager -->
                <?php if (!empty($catatan_tolak)): ?>
                    <div class="alert alert-danger d-flex gap-2 mb-3">
                        <i class="bi bi-chat-quote-fill flex-shrink-0 mt-1"></i>
                        <div>
                            <strong>Catatan Penolakan Manager:</strong><br>
                            <em><?= html_escape($catatan_tolak) ?></em>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- ══════════════════════════════════════
                     CARD 1: EDIT LAMPIRAN PER JENIS
                ══════════════════════════════════════ -->
                <div class="card mb-3">
                    <div class="card-header bg-warning text-dark py-2 d-flex align-items-center gap-2">
                        <i class="bi bi-images"></i>
                        <h6 class="mb-0 fw-bold">Edit Lampiran Dokumen</h6>
                        <span class="badge bg-dark text-white ms-auto" style="font-size:10px;">
                            Klik gambar untuk lihat, klik "Ganti" untuk upload ulang
                        </span>
                    </div>
                    <div class="card-body pt-3">
                        <?php
                        $jenis_config = [
                            'stnk'               => ['label' => 'STNK',            'icon' => 'bi-card-text',         'accept' => '.jpg,.jpeg,.png,.pdf'],
                            'unit_depan'         => ['label' => 'Foto Depan',       'icon' => 'bi-camera',            'accept' => '.jpg,.jpeg,.png'],
                            'unit_belakang'      => ['label' => 'Foto Belakang',    'icon' => 'bi-camera',            'accept' => '.jpg,.jpeg,.png'],
                            'unit_kiri'          => ['label' => 'Foto Kiri',        'icon' => 'bi-camera',            'accept' => '.jpg,.jpeg,.png'],
                            'unit_kanan'         => ['label' => 'Foto Kanan',       'icon' => 'bi-camera',            'accept' => '.jpg,.jpeg,.png'],
                            'maintenance_record' => ['label' => 'Maintenance Record', 'icon' => 'bi-file-earmark-text', 'accept' => '.jpg,.jpeg,.png,.pdf,.doc,.docx'],
                        ];

                        // Buat map jenis → lampiran existing
                        $lampiran_map = [];
                        if (!empty($lampiran)) {
                            foreach ($lampiran as $l) {
                                $lampiran_map[$l->jenis_lampiran] = $l;
                            }
                        }
                        ?>
                        <div class="row g-3">
                            <?php foreach ($jenis_config as $jenis => $cfg_jenis):
                                $existing_lamp = $lampiran_map[$jenis] ?? null;
                                $ext = $existing_lamp ? strtolower(pathinfo($existing_lamp->file_path, PATHINFO_EXTENSION)) : '';
                                $is_img = in_array($ext, ['jpg', 'jpeg', 'png', 'webp']);
                            ?>
                                <div class="col-6 col-md-4">
                                    <div class="lampiran-item border rounded p-2 text-center"
                                        id="lamp_box_<?= $jenis ?>"
                                        style="min-height:140px; position:relative;">

                                        <!-- Existing file -->
                                        <div class="lamp-existing" id="lamp_existing_<?= $jenis ?>">
                                            <?php if ($existing_lamp): ?>
                                                <?php if ($is_img): ?>
                                                    <a href="<?= base_url($existing_lamp->file_path) ?>" target="_blank">
                                                        <img src="<?= base_url($existing_lamp->file_path) ?>"
                                                            class="img-fluid rounded mb-1"
                                                            style="height:80px;width:100%;object-fit:cover;"
                                                            alt="<?= $cfg_jenis['label'] ?>">
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?= base_url($existing_lamp->file_path) ?>" target="_blank"
                                                        class="d-flex align-items-center justify-content-center mb-1"
                                                        style="height:80px;">
                                                        <i class="bi bi-file-earmark-pdf text-danger fs-2"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <div class="small fw-semibold text-muted mb-1">
                                                    <i class="bi <?= $cfg_jenis['icon'] ?> me-1"></i><?= $cfg_jenis['label'] ?>
                                                </div>
                                                <span class="badge bg-success text-white mb-1" style="font-size:9px;">
                                                    <i class="bi bi-check-circle me-1"></i>Ada
                                                </span>
                                            <?php else: ?>
                                                <div class="d-flex align-items-center justify-content-center mb-1" style="height:80px;">
                                                    <i class="bi <?= $cfg_jenis['icon'] ?> text-muted opacity-50 fs-2"></i>
                                                </div>
                                                <div class="small fw-semibold text-muted mb-1">
                                                    <?= $cfg_jenis['label'] ?>
                                                </div>
                                                <span class="badge bg-secondary text-white mb-1" style="font-size:9px;">
                                                    <i class="bi bi-dash-circle me-1"></i>Belum Ada
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Preview setelah pilih file baru -->
                                        <div class="lamp-preview d-none" id="lamp_preview_<?= $jenis ?>">
                                            <div class="position-relative d-inline-block">
                                                <img class="lamp-preview-img rounded border mb-1" src=""
                                                    style="height:80px;width:100%;max-width:130px;object-fit:cover;">
                                                <!-- Badge untuk non-gambar -->
                                                <div class="lamp-preview-doc d-none mb-1">
                                                    <span class="badge bg-primary text-white px-2 py-2">
                                                        <i class="bi bi-file-earmark-check me-1"></i>
                                                        <span class="lamp-preview-fname"></span>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="small fw-semibold text-success mb-1">
                                                <i class="bi bi-check-circle me-1"></i><?= $cfg_jenis['label'] ?>
                                            </div>
                                            <span class="badge bg-info text-white mb-1" style="font-size:9px;">File Baru</span>
                                            <!-- Tombol batal ganti -->
                                            <br>
                                            <button type="button" class="btn btn-xs btn-outline-secondary btn-cancel-lamp mt-1"
                                                data-jenis="<?= $jenis ?>" style="font-size:11px;padding:1px 6px;">
                                                <i class="bi bi-x me-1"></i>Batal
                                            </button>
                                        </div>

                                        <!-- Input file (hidden trigger) -->
                                        <input type="file" class="d-none inp-lamp-file"
                                            id="lamp_file_<?= $jenis ?>"
                                            name="lampiran_<?= $jenis ?>"
                                            accept="<?= $cfg_jenis['accept'] ?>"
                                            data-jenis="<?= $jenis ?>">

                                        <!-- Tombol ganti -->
                                        <div class="mt-1">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-warning btn-ganti-lamp py-0"
                                                data-jenis="<?= $jenis ?>"
                                                style="font-size:11px;">
                                                <i class="bi bi-arrow-repeat me-1"></i>
                                                <?= $existing_lamp ? 'Ganti' : 'Upload' ?>
                                            </button>
                                        </div>

                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-info-circle me-1 text-primary"></i>
                            Klik <strong>Ganti</strong> untuk mengganti lampiran lama. Lampiran yang tidak diganti tetap tersimpan.
                        </small>
                    </div>
                </div>

                <!-- ══════════════════════════════════════
                     CARD 2: EDIT DATA PENGAJUAN
                ══════════════════════════════════════ -->
                <div class="card">
                    <div class="card-header bg-primary text-white py-2">
                        <h6 class="mb-0 fw-bold text-white">
                            <i class="bi bi-pencil me-2"></i>Perbaiki & Ajukan Ulang
                        </h6>
                    </div>
                    <div class="card-body pt-4">

                        <div class="row g-3">

                            <!-- Info Kendaraan (read-only) -->
                            <div class="col-12">
                                <div class="bg-light rounded p-3">
                                    <small class="fw-bold text-muted d-block mb-2">
                                        Informasi Kendaraan (tidak dapat diubah)
                                    </small>
                                    <div class="row g-2">
                                        <div class="col-6 col-md-3">
                                            <small class="text-muted d-block">No. Polisi</small>
                                            <strong><?= html_escape($pengajuan->no_polisi) ?></strong>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <small class="text-muted d-block">Jenis</small>
                                            <strong><?= html_escape($pengajuan->jenis_kendaraan) ?></strong>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <small class="text-muted d-block">Merk / Tipe</small>
                                            <strong><?= html_escape($pengajuan->merk) ?> <?= html_escape($pengajuan->tipe) ?></strong>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <small class="text-muted d-block">Tahun</small>
                                            <strong><?= $pengajuan->tahun ?></strong>
                                        </div>
                                        <?php if (!empty($pengajuan->nomor_unit)): ?>
                                            <div class="col-6 col-md-3">
                                                <small class="text-muted d-block">Nomor Unit</small>
                                                <strong><?= html_escape($pengajuan->nomor_unit) ?></strong>
                                            </div>
                                        <?php endif; ?>
                                        <div class="col-6 col-md-3">
                                            <small class="text-muted d-block">No. Rangka</small>
                                            <strong class="small"><?= html_escape($pengajuan->nomor_rangka) ?></strong>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <small class="text-muted d-block">No. Mesin</small>
                                            <strong class="small"><?= html_escape($pengajuan->nomor_mesin) ?></strong>
                                        </div>
                                        <?php if (!empty($kendaraan->perusahaan)): ?>
                                            <div class="col-6 col-md-3">
                                                <small class="text-muted d-block">Perusahaan</small>
                                                <strong><?= html_escape($kendaraan->perusahaan) ?></strong>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Tipe Akses -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tipe Akses</label>
                                <?php
                                $akses_opts = [
                                    'mining'      => 'Mining Access',
                                    'non_mining'  => 'Non Mining',
                                    'underground' => 'Underground',
                                ];
                                ?>
                                <select class="form-select" id="edit_tipe_akses">
                                    <?php foreach ($akses_opts as $v => $l): ?>
                                        <option value="<?= $v ?>" <?= $pengajuan->tipe_akses === $v ? 'selected' : '' ?>>
                                            <?= $l ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Email Pemohon -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Email Pemohon <span class="text-danger">*</span>
                                </label>
                                <input type="email" class="form-control" id="edit_email_pemohon"
                                    value="<?= html_escape($pengajuan->email_pemohon) ?>"
                                    placeholder="email@domain.com">
                            </div>

                            <!-- Tujuan Penggunaan -->
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    Tujuan Penggunaan <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="edit_tujuan" rows="4"
                                    placeholder="Jelaskan tujuan penggunaan kendaraan dan area operasi..."
                                    maxlength="1000"><?= html_escape($pengajuan->tujuan) ?></textarea>
                                <small class="text-muted">
                                    <span id="tujuanCount"><?= strlen($pengajuan->tujuan) ?></span>/1000
                                </small>
                            </div>

                            <!-- Alasan perbaikan — WAJIB -->
                            <div class="col-12">
                                <label class="form-label fw-semibold text-danger">
                                    <i class="bi bi-chat-text me-1"></i>
                                    Tindakan Perbaikan / Alasan Pengajuan Ulang
                                    <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control border-danger" id="edit_alasan" rows="3"
                                    placeholder="Jelaskan apa yang sudah diperbaiki atau klarifikasi atas penolakan Manager..."
                                    maxlength="500"></textarea>
                                <small class="text-muted">
                                    Wajib diisi. Akan dicatat dalam riwayat pengajuan.
                                </small>
                                <div class="text-danger small mt-1" id="err_alasan_edit"></div>
                            </div>

                        </div><!-- end row -->

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="<?= site_url('pengajuan') ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Batal
                            </a>
                            <button type="button" class="btn btn-primary text-white" id="btnUpdatePengajuan">
                                <i class="bi bi-send me-1"></i>Kirim Ulang ke Manager
                            </button>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>
</main>


<script>
    $(function() {
        var csrfName = '<?= $this->security->get_csrf_token_name() ?>';
        var csrfHash = '<?= $this->security->get_csrf_hash() ?>';

        // ── Char counter tujuan ───────────────────────────────────────────
        $('#edit_tujuan').on('input', function() {
            $('#tujuanCount').text($(this).val().length);
        });

        // ── Tombol "Ganti" lampiran — trigger input file ──────────────────
        $(document).on('click', '.btn-ganti-lamp', function() {
            var jenis = $(this).data('jenis');
            $('#lamp_file_' + jenis).trigger('click');
        });

        // ── Preview file yang dipilih ─────────────────────────────────────
        $(document).on('change', '.inp-lamp-file', function() {
            var jenis = $(this).data('jenis');
            var file = this.files[0];
            if (!file) return;

            var $box = $('#lamp_box_' + jenis);
            var $existing = $('#lamp_existing_' + jenis);
            var $preview = $('#lamp_preview_' + jenis);

            var imgTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];

            if (imgTypes.indexOf(file.type) >= 0) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $preview.find('.lamp-preview-img').attr('src', e.target.result).removeClass('d-none');
                    $preview.find('.lamp-preview-doc').addClass('d-none');
                    $existing.addClass('d-none');
                    $preview.removeClass('d-none');
                    $box.addClass('border-success');
                };
                reader.readAsDataURL(file);
            } else {
                // Non-gambar (PDF, doc, dll)
                var fname = file.name.length > 18 ? file.name.substring(0, 16) + '…' : file.name;
                $preview.find('.lamp-preview-fname').text(fname);
                $preview.find('.lamp-preview-img').addClass('d-none');
                $preview.find('.lamp-preview-doc').removeClass('d-none');
                $existing.addClass('d-none');
                $preview.removeClass('d-none');
                $box.addClass('border-success');
            }
        });

        // ── Batal ganti lampiran ──────────────────────────────────────────
        $(document).on('click', '.btn-cancel-lamp', function() {
            var jenis = $(this).data('jenis');
            // Reset file input
            var el = document.getElementById('lamp_file_' + jenis);
            var neu = el.cloneNode(true);
            el.parentNode.replaceChild(neu, el);

            $('#lamp_preview_' + jenis).addClass('d-none');
            $('#lamp_existing_' + jenis).removeClass('d-none');
            $('#lamp_box_' + jenis).removeClass('border-success');
        });

        // ── Submit ────────────────────────────────────────────────────────
        $('#btnUpdatePengajuan').on('click', function() {
            var tujuan = $('#edit_tujuan').val().trim();
            var email = $('#edit_email_pemohon').val().trim();
            var alasan = $('#edit_alasan').val().trim();
            var errors = false;

            $('#err_alasan_edit').text('');

            if (!tujuan) {
                toastr.warning('Tujuan penggunaan wajib diisi.');
                errors = true;
            }
            if (!email) {
                toastr.warning('Email pemohon wajib diisi.');
                errors = true;
            }
            if (!alasan) {
                $('#err_alasan_edit').text('Penjelasan perbaikan wajib diisi.');
                errors = true;
            }
            if (alasan && alasan.length < 10) {
                $('#err_alasan_edit').text('Penjelasan minimal 10 karakter.');
                errors = true;
            }
            if (errors) return;

            Swal.fire({
                title: 'Kirim Ulang ke Manager?',
                html: 'Pengajuan <strong>#PU-<?= str_pad($pengajuan->id_pengajuan, 4, '0', STR_PAD_LEFT) ?></strong> ' +
                    'akan dikirim ulang ke <strong>Dept Manager</strong> untuk direview kembali.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4154f1',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-send me-1"></i>Ya, Kirim',
                cancelButtonText: 'Batal',
            }).then(function(r) {
                if (!r.isConfirmed) return;

                NProgress.start();
                var $btn = $('#btnUpdatePengajuan');
                $btn.prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');

                var fd = new FormData();
                fd.append(csrfName, csrfHash);
                fd.append('id_pengajuan', '<?= $pengajuan->id_pengajuan ?>');
                fd.append('tujuan', tujuan);
                fd.append('email_pemohon', email);
                fd.append('tipe_akses', $('#edit_tipe_akses').val());
                fd.append('alasan_edit', alasan);

                // Lampiran yang diganti — cek tiap jenis
                var jenis_list = ['stnk', 'unit_depan', 'unit_belakang', 'unit_kiri', 'unit_kanan', 'maintenance_record'];
                jenis_list.forEach(function(jenis) {
                    var el = document.getElementById('lamp_file_' + jenis);
                    if (el && el.files && el.files[0]) {
                        fd.append('lampiran_' + jenis, el.files[0]);
                    }
                });

                $.ajax({
                    url: '<?= site_url('pengajuan/update') ?>',
                    type: 'POST',
                    data: fd,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(res) {
                        NProgress.done();
                        $btn.prop('disabled', false)
                            .html('<i class="bi bi-send me-1"></i>Kirim Ulang ke Manager');
                        if (res.status === 'success') {
                            Swal.fire({
                                title: 'Berhasil!',
                                html: res.message,
                                icon: 'success',
                                confirmButtonColor: '#4154f1',
                            }).then(function() {
                                window.location.href = res.redirect;
                            });
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
                        $btn.prop('disabled', false)
                            .html('<i class="bi bi-send me-1"></i>Kirim Ulang ke Manager');
                        toastr.error('Terjadi kesalahan server.');
                    }
                });
            });
        });
    });
</script>