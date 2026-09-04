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

                <?php $is_draft_mode = ($pengajuan->status === 'draft'); ?>
                <!-- Header info pengajuan -->
                <div class="card mb-3 <?= $is_draft_mode ? 'border-primary' : 'border-danger' ?>">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle <?= $is_draft_mode ? 'bg-primary' : 'bg-danger' ?> d-flex align-items-center justify-content-center text-white flex-shrink-0"
                                style="width:50px;height:50px;font-size:1.3rem;">
                                <i class="bi <?= $is_draft_mode ? 'bi-file-earmark-text' : 'bi-pencil-square' ?>"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold"><?= html_escape($pengajuan->no_polisi ?: $pengajuan->nomor_unit) ?></h5>
                                <small class="text-muted">
                                    <?= html_escape($pengajuan->jenis_kendaraan) ?> —
                                    <?= html_escape($pengajuan->merk) ?> <?= html_escape($pengajuan->tipe) ?>
                                </small>
                            </div>
                            <div class="ms-auto text-end">
                                <span class="badge <?= $is_draft_mode ? 'bg-secondary' : 'bg-danger' ?> text-white px-3 py-2">
                                    <i class="bi <?= $is_draft_mode ? 'bi-clock-history' : 'bi-x-circle' ?> me-1"></i><?= $is_draft_mode ? 'Draft Pengajuan' : 'Ditolak Manager' ?>
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
                            'sertifikasi'        => ['label' => 'Sertifikasi Alat Berat', 'icon' => 'bi-award-fill',       'accept' => '.jpg,.jpeg,.png,.pdf,.doc,.docx'],
                            'stnk'               => ['label' => 'STNK',            'icon' => 'bi-card-text',         'accept' => '.jpg,.jpeg,.png,.pdf'],
                            'unit_depan'         => ['label' => 'Foto Depan',       'icon' => 'bi-camera',            'accept' => '.jpg,.jpeg,.png'],
                            'unit_belakang'      => ['label' => 'Foto Belakang',    'icon' => 'bi-camera',            'accept' => '.jpg,.jpeg,.png'],
                            'unit_kiri'          => ['label' => 'Foto Kiri',        'icon' => 'bi-camera',            'accept' => '.jpg,.jpeg,.png'],
                            'unit_kanan'         => ['label' => 'Foto Kanan',       'icon' => 'bi-camera',            'accept' => '.jpg,.jpeg,.png'],
                            'maintenance_record' => ['label' => 'Maintenance Record', 'icon' => 'bi-file-earmark-text', 'accept' => '.jpg,.jpeg,.png,.pdf,.doc,.docx'],
                        ];

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
                                $is_img = $existing_lamp ? in_array(strtolower(pathinfo($existing_lamp->file_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp']) : false;
                            ?>
                                <div class="col-6 col-md-4">
                                    <div class="lampiran-item border rounded p-2 text-center"
                                        id="lamp_box_<?= $jenis ?>"
                                        style="min-height:140px; position:relative;">

                                        <!-- Existing file -->
                                        <div id="lamp_existing_<?= $jenis ?>">
                                            <?php if ($existing_lamp): ?>
                                                <?php if ($is_img): ?>
                                                    <a href="<?= base_url($existing_lamp->file_path) ?>" target="_blank" title="Lihat ukuran penuh">
                                                        <img src="<?= base_url($existing_lamp->file_path) ?>"
                                                            class="img-fluid rounded mb-1"
                                                            style="max-height:80px; object-fit:cover; width:100%;">
                                                    </a>
                                                <?php else: ?>
                                                    <div class="p-2 bg-light rounded mb-1">
                                                        <i class="bi <?= $cfg_jenis['icon'] ?> fs-2 text-primary"></i>
                                                        <div class="small text-truncate" title="<?= html_escape(basename($existing_lamp->file_path)) ?>">
                                                            <?= html_escape(basename($existing_lamp->file_path)) ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="badge bg-success mb-1" style="font-size:10px;">
                                                    <i class="bi bi-check-circle me-1"></i>Sudah Ada
                                                </div>
                                            <?php else: ?>
                                                <div class="p-3 bg-light rounded text-muted mb-1">
                                                    <i class="bi <?= $cfg_jenis['icon'] ?> fs-3 opacity-50"></i>
                                                    <div class="small">Belum ada file</div>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Preview file baru yang dipilih -->
                                        <div id="lamp_preview_<?= $jenis ?>" class="d-none">
                                            <img src="" class="img-fluid rounded mb-1 lamp-preview-img d-none"
                                                style="max-height:80px; object-fit:cover; width:100%;">
                                            <div class="p-2 bg-light rounded mb-1 lamp-preview-doc d-none">
                                                <i class="bi <?= $cfg_jenis['icon'] ?> fs-2 text-warning"></i>
                                                <div class="small text-truncate lamp-preview-fname"></div>
                                            </div>
                                            <div class="badge bg-warning text-dark mb-1" style="font-size:10px;">
                                                <i class="bi bi-arrow-clockwise me-1"></i>File Baru
                                            </div>
                                        </div>

                                        <div class="fw-semibold small mt-1"><?= $cfg_jenis['label'] ?></div>

                                        <!-- Input file tersembunyi -->
                                        <input type="file"
                                            id="lamp_file_<?= $jenis ?>"
                                            class="d-none inp-lamp-file"
                                            data-jenis="<?= $jenis ?>"
                                            accept="<?= $cfg_jenis['accept'] ?>">

                                        <!-- Tombol aksi -->
                                        <div class="mt-1 d-flex gap-1 justify-content-center">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-primary py-0 px-2 btn-ganti-lamp"
                                                data-jenis="<?= $jenis ?>"
                                                title="Ganti file">
                                                <i class="bi bi-upload me-1"></i><?= $existing_lamp ? 'Ganti' : 'Upload' ?>
                                            </button>
                                            <button type="button"
                                                class="btn btn-sm btn-outline-secondary py-0 px-1 btn-cancel-lamp d-none"
                                                id="btn_cancel_<?= $jenis ?>"
                                                data-jenis="<?= $jenis ?>"
                                                title="Batalkan penggantian file">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </div>

                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════
                     CARD 2: EDIT DATA PENGAJUAN
                ══════════════════════════════════════ -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white py-2">
                        <h6 class="mb-0 fw-bold text-white">
                            <i class="bi bi-pencil me-2"></i><?= $is_draft_mode ? 'Data Pengajuan & Berkas' : 'Perbaiki & Ajukan Ulang' ?>
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
                                            <strong><?= html_escape($pengajuan->no_polisi ?: 'N/A') ?></strong>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <small class="text-muted d-block">Jenis</small>
                                            <strong><?= html_escape($pengajuan->jenis_kendaraan ?: '-') ?></strong>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <small class="text-muted d-block">Merk / Tipe</small>
                                            <strong><?= html_escape($pengajuan->merk) ?> <?= html_escape($pengajuan->tipe) ?></strong>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <small class="text-muted d-block">Tahun</small>
                                            <strong><?= $pengajuan->tahun ?: '-' ?></strong>
                                        </div>
                                        <?php if (!empty($pengajuan->nomor_unit)): ?>
                                            <div class="col-6 col-md-3">
                                                <small class="text-muted d-block">Nomor Unit</small>
                                                <strong><?= html_escape($pengajuan->nomor_unit) ?></strong>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Tipe Akses -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tipe Akses</label>
                                <select class="form-select" id="edit_tipe_akses">
                                    <option value="mining" <?= $pengajuan->tipe_akses === 'mining' ? 'selected' : '' ?>>Mining Access</option>
                                    <option value="non_mining" <?= $pengajuan->tipe_akses === 'non_mining' ? 'selected' : '' ?>>Non Mining</option>
                                    <option value="underground" <?= $pengajuan->tipe_akses === 'underground' ? 'selected' : '' ?>>Underground</option>
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
                                <div class="d-flex justify-content-between mt-1">
                                    <small class="text-muted">Jelaskan secara spesifik area kerja dan fungsi unit.</small>
                                    <small class="text-muted"><span id="tujuanCount"><?= strlen((string)$pengajuan->tujuan) ?></span>/1000</small>
                                </div>
                            </div>

                            <?php if (!$is_draft_mode): ?>
                            <!-- Alasan perbaikan — WAJIB untuk yang ditolak manager -->
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
                            <?php endif; ?>

                        </div><!-- end row -->

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top flex-wrap gap-2">
                            <a href="<?= site_url('pengajuan') ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Batal
                            </a>
                            <div class="d-flex gap-2">
                                <?php if ($is_draft_mode): ?>
                                    <button type="button" class="btn btn-outline-primary" id="btnSaveDraftPengajuan">
                                        <i class="bi bi-save me-1"></i>Simpan Perubahan Draft
                                    </button>
                                    <button type="button" class="btn btn-primary text-white" id="btnSubmitPengajuan">
                                        <i class="bi bi-send me-1"></i>Submit ke Manager
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-primary text-white" id="btnUpdatePengajuan">
                                        <i class="bi bi-send me-1"></i>Kirim Ulang ke Manager
                                    </button>
                                <?php endif; ?>
                            </div>
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
        var isDraftMode = <?= json_encode($is_draft_mode) ?>;

        // ── Char counter tujuan ───────────────────────────────────────────
        $('#edit_tujuan').on('input', function() {
            $('#tujuanCount').text($(this).val().length);
        });

        // ── Tombol "Ganti" lampiran ───────────────────────────────────────
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
                    $('#btn_cancel_' + jenis).removeClass('d-none');
                };
                reader.readAsDataURL(file);
            } else {
                var fname = file.name.length > 18 ? file.name.substring(0, 16) + '…' : file.name;
                $preview.find('.lamp-preview-fname').text(fname);
                $preview.find('.lamp-preview-img').addClass('d-none');
                $preview.find('.lamp-preview-doc').removeClass('d-none');
                $existing.addClass('d-none');
                $preview.removeClass('d-none');
                $box.addClass('border-success');
                $('#btn_cancel_' + jenis).removeClass('d-none');
            }
        });

        // ── Batal ganti lampiran ──────────────────────────────────────────
        $(document).on('click', '.btn-cancel-lamp', function() {
            var jenis = $(this).data('jenis');
            var el = document.getElementById('lamp_file_' + jenis);
            var neu = el.cloneNode(true);
            el.parentNode.replaceChild(neu, el);

            $('#lamp_preview_' + jenis).addClass('d-none');
            $('#lamp_existing_' + jenis).removeClass('d-none');
            $('#lamp_box_' + jenis).removeClass('border-success');
            $(this).addClass('d-none');
        });

        // ── Process Update / Submit ───────────────────────────────────────
        function processUpdate(isDraft) {
            var tujuan = $('#edit_tujuan').val().trim();
            var email = $('#edit_email_pemohon').val().trim();
            var alasan = ($('#edit_alasan').length) ? $('#edit_alasan').val().trim() : '';
            var errors = false;

            if ($('#err_alasan_edit').length) $('#err_alasan_edit').text('');

            if (!isDraft) {
                if (!tujuan) { toastr.warning('Tujuan penggunaan wajib diisi.'); errors = true; }
                if (!email) { toastr.warning('Email pemohon wajib diisi.'); errors = true; }
                if (!isDraftMode && !alasan) { $('#err_alasan_edit').text('Penjelasan perbaikan wajib diisi.'); errors = true; }
                if (!isDraftMode && alasan && alasan.length < 10) { $('#err_alasan_edit').text('Penjelasan minimal 10 karakter.'); errors = true; }
            }

            if (errors) return;

            var confirmTitle = isDraft ? 'Simpan Perubahan Draft?' : (isDraftMode ? 'Submit Pengajuan ke Manager?' : 'Kirim Ulang ke Manager?');
            var confirmText = isDraft ? 'Perubahan akan disimpan sebagai draft.' : 'Pengajuan akan dikirim ke <strong>Dept Manager</strong> untuk direview.';

            Swal.fire({
                title: confirmTitle,
                html: confirmText,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4154f1',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-check-lg me-1"></i>Ya, Lanjutkan'
            }).then(function(r) {
                if (!r.isConfirmed) return;

                NProgress.start();
                var $btnActive = isDraft ? $('#btnSaveDraftPengajuan') : ($('#btnSubmitPengajuan').length ? $('#btnSubmitPengajuan') : $('#btnUpdatePengajuan'));
                $btnActive.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');

                var fd = new FormData();
                fd.append(csrfName, csrfHash);
                fd.append('id_pengajuan', '<?= $pengajuan->id_pengajuan ?>');
                fd.append('is_draft', isDraft ? '1' : '0');
                fd.append('tujuan', tujuan);
                fd.append('email_pemohon', email);
                fd.append('tipe_akses', $('#edit_tipe_akses').val());
                fd.append('alasan_edit', alasan);

                var jenis_list = ['sertifikasi', 'stnk', 'unit_depan', 'unit_belakang', 'unit_kiri', 'unit_kanan', 'maintenance_record'];
                jenis_list.forEach(function(jenis) {
                    var el = document.getElementById('lamp_file_' + jenis);
                    if (el && el.files && el.files[0]) fd.append('lampiran_' + jenis, el.files[0]);
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
                        $btnActive.prop('disabled', false).html(isDraft ? '<i class="bi bi-save me-1"></i>Simpan Perubahan Draft' : '<i class="bi bi-send me-1"></i>Submit ke Manager');
                        if (res.status === 'success') {
                            Swal.fire({ title: 'Berhasil!', html: res.message, icon: 'success' }).then(function() {
                                window.location.href = res.redirect || '<?= site_url('pengajuan') ?>';
                            });
                        } else {
                            Swal.fire({ title: 'Gagal', html: res.message, icon: 'error' });
                        }
                    },
                    error: function() {
                        NProgress.done();
                        $btnActive.prop('disabled', false).html(isDraft ? '<i class="bi bi-save me-1"></i>Simpan Perubahan Draft' : '<i class="bi bi-send me-1"></i>Submit ke Manager');
                        toastr.error('Terjadi kesalahan server.');
                    }
                });
            });
        });
    });
</script>