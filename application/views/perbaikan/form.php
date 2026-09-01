<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Input Data Perbaikan Unit</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url('pengajuan') ?>">Daftar Pengajuan</a></li>
                <li class="breadcrumb-item active">Perbaikan #PU-<?= str_pad($pengajuan->id_pengajuan, 4, '0', STR_PAD_LEFT) ?></li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row justify-content-center">
            <div class="col-xl-10">

                <!-- ═══ HEADER KENDARAAN ═══ -->
                <div class="card mb-3 border-danger">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <div class="rounded-circle bg-danger d-flex align-items-center justify-content-center text-white flex-shrink-0"
                                style="width:50px;height:50px;font-size:1.3rem;">
                                <i class="bi bi-tools"></i>
                            </div>
                             <div>
                                 <h5 class="mb-0 fw-bold">
                                     <?= html_escape($pengajuan->no_polisi) ?>
                                     <?= badge_tipe_akses($pengajuan->tipe_akses, 'font-size:11px;') ?>
                                 </h5>
                                 <small class="text-muted">
                                     <?= html_escape($pengajuan->jenis_kendaraan) ?> —
                                     <?= html_escape($pengajuan->merk) ?> <?= html_escape($pengajuan->tipe) ?>
                                     (<?= $pengajuan->tahun ?>)
                                 </small>
                             </div>
                            <div class="ms-auto text-end">
                                <span class="badge bg-danger text-white px-3 py-2">
                                    <i class="bi bi-x-circle me-1"></i>Tidak Lulus Inspeksi
                                </span>
                                <div><small class="text-muted">#PU-<?= str_pad($pengajuan->id_pengajuan, 4, '0', STR_PAD_LEFT) ?></small></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ═══ INFO DEADLINE & INSPEKTOR (sudah diisi inspektor saat submit) ═══ -->
                <?php if (isset($tgl_maks) && $tgl_maks): ?>
                    <div class="card mb-3 border-warning">
                        <div class="card-body py-3">
                            <div class="row g-3 align-items-center">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-calendar-x-fill text-danger fs-4"></i>
                                        <div>
                                            <small class="text-muted d-block">Deadline Perbaikan (ditetapkan Inspektor)</small>
                                            <strong class="text-danger fs-6">
                                                <?= date('d M Y', strtotime($tgl_maks)) ?>
                                            </strong>
                                            <?php
                                            $sisa_hari = (int) ceil((strtotime($tgl_maks) - time()) / 86400);
                                            ?>
                                            <div>
                                                <?php if ($sisa_hari < 0): ?>
                                                    <span class="badge bg-danger" style="font-size:9px;">Terlewat <?= abs($sisa_hari) ?> hari</span>
                                                <?php elseif ($sisa_hari === 0): ?>
                                                    <span class="badge bg-danger" style="font-size:9px;">Hari ini!</span>
                                                <?php elseif ($sisa_hari <= 3): ?>
                                                    <span class="badge bg-warning text-dark" style="font-size:9px;">Sisa <?= $sisa_hari ?> hari</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary" style="font-size:9px;">Sisa <?= $sisa_hari ?> hari</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php if (isset($verifikator) && $verifikator): ?>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-person-check-fill text-primary fs-4"></i>
                                            <div>
                                                <small class="text-muted d-block">Akan Diverifikasi Oleh</small>
                                                <strong><?= html_escape($verifikator->nama) ?></strong>
                                                <div><small class="text-muted"><?= html_escape($verifikator->email ?? '') ?></small></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <div class="col-md-4">
                                    <div class="alert alert-info py-2 mb-0 small">
                                        <i class="bi bi-arrow-right-circle me-1"></i>
                                        Setelah simpan, inspektor di atas akan langsung memverifikasi — <strong>tanpa jadwal ulang</strong>.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- ═══ DAFTAR TEMUAN / ITEM TIDAK LULUS ═══ -->
                <?php if (!empty($checklist_no)): ?>
                    <div class="card mb-3 border-warning">
                        <div class="card-header bg-warning text-dark py-2 d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <h6 class="mb-0 fw-bold">Temuan / Item Tidak Memenuhi Syarat</h6>
                            <span class="badge bg-danger text-white ms-auto"><?= count($checklist_no) ?> item</span>
                        </div>
                        <div class="card-body py-3">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="80">Kategori</th>
                                            <th width="50">No.</th>
                                            <th>Kriteria / Temuan</th>
                                            <th>Keterangan Mekanik</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($checklist_no as $item): ?>
                                            <tr>
                                                <td>
                                                    <span class="badge bg-<?= $item->kategori === 'CRITICAL' ? 'danger' : 'warning text-dark' ?>" style="font-size:9px;">
                                                        <?= $item->kategori ?>
                                                    </span>
                                                </td>
                                                <td class="text-center fw-bold"><?= html_escape($item->no_urut) ?></td>
                                                <td class="small"><?= html_escape($item->kriteria) ?></td>
                                                <td class="small text-muted"><?= html_escape($item->keterangan ?: '—') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php if ($uji && !empty($uji->catatan_temuan)): ?>
                                <div class="alert alert-secondary py-2 mt-2 mb-0 small">
                                    <i class="bi bi-chat-text me-1"></i>
                                    <strong>Catatan Temuan Inspektor:</strong> <?= html_escape($uji->catatan_temuan) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- ═══ FOTO TEMUAN DARI INSPEKSI ═══ -->
                <!-- ═══ FOTO DOKUMENTASI DARI HASIL INSPEKSI ═══ -->
                <?php if (!empty($foto_mekanik) || !empty($foto_temuan)): ?>
                    <div class="card mb-3 border-danger">
                        <div class="card-header bg-danger text-white py-2 d-flex align-items-center gap-2">
                            <i class="bi bi-camera-fill"></i>
                            <h6 class="mb-0 fw-bold">Foto Dokumentasi dari Hasil Inspeksi</h6>
                            <span class="badge bg-white text-danger ms-auto">
                                <?= count($foto_mekanik) + count($foto_temuan) ?> foto
                            </span>
                        </div>
                        <div class="card-body py-3">
                            <p class="small text-muted mb-3">
                                <i class="bi bi-info-circle me-1"></i>
                                Foto-foto berikut diambil oleh inspektor saat pengujian.
                                Gunakan sebagai referensi untuk melakukan perbaikan.
                            </p>

                            <?php if (!empty($foto_mekanik)): ?>
                                <!-- Foto Mekanik -->
                                <div class="mb-3">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-person-badge me-1"></i>Foto Mekanik / Peserta
                                        </span>
                                        <small class="text-muted"><?= count($foto_mekanik) ?> foto</small>
                                    </div>
                                    <div class="row g-2">
                                        <?php foreach ($foto_mekanik as $foto): ?>
                                            <div class="col-6 col-sm-4 col-md-3">
                                                <div class="border rounded overflow-hidden foto-temuan-card">
                                                    <a href="<?= base_url($foto->file_path) ?>"
                                                        target="_blank" title="Lihat foto full size">
                                                        <img src="<?= base_url($foto->file_path) ?>"
                                                            class="img-fluid w-100"
                                                            style="height:120px;object-fit:cover;"
                                                            alt="Foto mekanik"
                                                            onerror="this.src='<?= base_url('assets/img/img-error.png') ?>'">
                                                    </a>
                                                    <div class="p-1 bg-light border-top">
                                                        <small class="text-muted" style="font-size:10px;line-height:1.2;">
                                                            <?= !empty($foto->keterangan)
                                                                ? html_escape($foto->keterangan)
                                                                : '<span class="fst-italic">Foto mekanik</span>' ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($foto_mekanik) && !empty($foto_temuan)): ?>
                                <hr class="my-3">
                            <?php endif; ?>

                            <?php if (!empty($foto_temuan)): ?>
                                <!-- Foto Temuan -->
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-danger text-white">
                                            <i class="bi bi-search me-1"></i>Foto Temuan / Kerusakan
                                        </span>
                                        <small class="text-muted"><?= count($foto_temuan) ?> foto</small>
                                    </div>
                                    <div class="row g-2">
                                        <?php foreach ($foto_temuan as $i => $foto): ?>
                                            <div class="col-6 col-sm-4 col-md-3">
                                                <div class="border rounded overflow-hidden foto-temuan-card">
                                                    <a href="<?= base_url($foto->file_path) ?>"
                                                        target="_blank" title="Lihat foto full size">
                                                        <img src="<?= base_url($foto->file_path) ?>"
                                                            class="img-fluid w-100"
                                                            style="height:120px;object-fit:cover;"
                                                            alt="Foto temuan <?= $i + 1 ?>"
                                                            onerror="this.src='<?= base_url('assets/img/img-error.png') ?>'">
                                                    </a>
                                                    <div class="p-1 bg-light border-top">
                                                        <small class="text-muted" style="font-size:10px;line-height:1.2;">
                                                            <?php if (!empty($foto->keterangan)): ?>
                                                                <i class="bi bi-chat-text me-1 text-danger"></i>
                                                                <?= html_escape($foto->keterangan) ?>
                                                            <?php else: ?>
                                                                <span class="fst-italic">Temuan #<?= $i + 1 ?></span>
                                                            <?php endif; ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                <?php endif; ?>

                <!-- ═══ FORM PERBAIKAN PER TEMUAN ═══ -->
                <form method="POST" action="<?= site_url('perbaikan/store') ?>"
                    enctype="multipart/form-data" id="formPerbaikan">
                    <?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()) ?>

                    <input type="hidden" name="id_pengajuan" value="<?= $pengajuan->id_pengajuan ?>">
                    <?php if ($uji): ?>
                        <input type="hidden" name="id_uji" value="<?= $uji->id_uji ?>">
                    <?php endif; ?>

                    <div class="card mb-3 border-0 shadow-sm rounded-3">
                        <div class="card-header bg-primary text-white py-3 px-3 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-clipboard-check fs-5"></i>
                                <h5 class="mb-0 fw-bold text-white">Input Tindakan &amp; Bukti Perbaikan per Temuan</h5>
                            </div>
                            <span class="badge bg-white text-primary fw-bold">
                                <?= !empty($checklist_no) ? count($checklist_no) . ' Temuan' : '0 Temuan' ?>
                            </span>
                        </div>
                        <div class="card-body p-3 p-md-4">
                            <div class="alert alert-primary bg-primary bg-opacity-10 border-primary border-opacity-25 py-2 px-3 mb-4 small">
                                <i class="bi bi-info-circle-fill me-1 text-primary"></i>
                                <strong>Petunjuk:</strong> Untuk setiap temuan di bawah ini, mohon jelaskan tindakan perbaikan yang telah dilakukan dan lampirkan foto/dokumen bukti fisik perbaikannya.
                            </div>

                            <?php if (!empty($checklist_no)): ?>
                                <div class="vstack gap-3 mb-4">
                                    <?php foreach ($checklist_no as $idx => $item): ?>
                                        <div class="card border rounded-3 temuan-card shadow-sm" data-item-id="<?= $item->id_item ?>">
                                            <div class="card-header bg-light py-2 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-<?= $item->kategori === 'CRITICAL' ? 'danger' : 'warning text-dark' ?> fw-bold">
                                                        <?= html_escape($item->kategori) ?>
                                                    </span>
                                                    <span class="badge bg-dark text-white" style="font-size:11px;">#<?= $idx + 1 ?></span>
                                                    <strong class="text-dark fs-6">
                                                        <?= html_escape($item->kriteria) ?>
                                                    </strong>
                                                    <?php if (!empty($item->no_urut)): ?>
                                                        <small class="text-muted">(Item No. <?= html_escape($item->no_urut) ?>)</small>
                                                    <?php endif; ?>
                                                </div>
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">
                                                    <i class="bi bi-x-circle me-1"></i>Tidak Memenuhi Syarat
                                                </span>
                                            </div>
                                            <div class="card-body p-3">
                                                <?php if (!empty($item->keterangan)): ?>
                                                    <div class="alert alert-warning py-2 px-3 mb-3 small d-flex align-items-start gap-2">
                                                        <i class="bi bi-chat-left-dots-fill text-warning fs-6 mt-1 flex-shrink-0"></i>
                                                        <div>
                                                            <strong class="text-dark">Catatan Temuan Inspektor:</strong>
                                                            <div class="text-muted mt-1"><?= nl2br(html_escape($item->keterangan)) ?></div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <div class="row g-3">
                                                    <!-- 1. Tindakan Perbaikan Item -->
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold small mb-1">
                                                            <i class="bi bi-wrench-adjustable me-1 text-primary"></i>Tindakan / Hasil Perbaikan <span class="text-danger">*</span>
                                                        </label>
                                                        <textarea class="form-control form-control-sm tindakan-item-input"
                                                            name="tindakan_item[<?= $item->id_item ?>]"
                                                            id="tindakan_<?= $item->id_item ?>"
                                                            rows="3"
                                                            placeholder="Contoh: Lampu rem diganti bohlam baru, soket dibersihkan, dan telah diuji menyala normal."
                                                            required></textarea>
                                                        <div class="text-danger small mt-1 d-none" id="err_tindakan_<?= $item->id_item ?>">
                                                            Tindakan perbaikan wajib diisi.
                                                        </div>
                                                    </div>

                                                    <!-- 2. Upload Bukti Perbaikan Item -->
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold small mb-1">
                                                            <i class="bi bi-camera me-1 text-success"></i>Upload Bukti Foto / Dokumen Perbaikan <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="file"
                                                            class="form-control form-control-sm bukti-item-file"
                                                            name="bukti_item_<?= $item->id_item ?>[]"
                                                            id="bukti_item_<?= $item->id_item ?>"
                                                            accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                                                            multiple
                                                            required
                                                            data-item-id="<?= $item->id_item ?>">
                                                        <small class="text-muted d-block mt-1" style="font-size:11px;">
                                                            Pilih 1 atau lebih file (JPG, PNG, PDF). Maks 10MB per file.
                                                        </small>
                                                        <div class="text-danger small mt-1 d-none" id="err_bukti_<?= $item->id_item ?>">
                                                            Foto/dokumen bukti perbaikan wajib diunggah.
                                                        </div>

                                                        <!-- Preview Container per Item -->
                                                        <div class="row g-2 mt-2 item-preview-grid" id="preview_grid_<?= $item->id_item ?>"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-secondary text-center py-4 mb-4">
                                    <i class="bi bi-info-circle fs-3 text-muted d-block mb-2"></i>
                                    Tidak ada item temuan checklist spesifik yang tercatat. Silakan gunakan form catatan umum di bawah.
                                </div>
                            <?php endif; ?>

                            <!-- Catatan Tambahan & Dokumen Pendukung Opsional -->
                            <div class="card border rounded-3 bg-light mb-3">
                                <div class="card-header bg-white py-2 px-3">
                                    <h6 class="mb-0 fw-bold text-secondary">
                                        <i class="bi bi-paperclip me-1 text-primary"></i>Catatan Tambahan &amp; Lampiran Umum (Opsional)
                                    </h6>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small mb-1">Catatan Tambahan</label>
                                            <textarea class="form-control form-control-sm"
                                                name="catatan_perbaikan"
                                                rows="3"
                                                placeholder="Catatan umum atau informasi tambahan perbaikan unit..."></textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small mb-1">Lampiran Umum Tambahan (Work Order / SPK / dsb.)</label>
                                            <input type="file"
                                                class="form-control form-control-sm"
                                                name="bukti_perbaikan[]"
                                                id="bukti_perbaikan_umum"
                                                accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                                                multiple>
                                            <small class="text-muted d-block mt-1" style="font-size:11px;">
                                                Opsional. Bisa memilih file PDF/dokumen SPK bengkel.
                                            </small>
                                            <div class="row g-2 mt-2" id="preview_grid_umum"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Info alur -->
                            <div class="alert alert-success py-2 px-3 mb-0 small">
                                <i class="bi bi-arrow-right-circle-fill me-2"></i>
                                Setelah disimpan, inspektor
                                <strong><?= isset($verifikator) && $verifikator ? html_escape($verifikator->nama) : 'yang ditugaskan' ?></strong>
                                akan langsung dapat memverifikasi perbaikan ini secara fisik — <strong>tanpa perlu membuat jadwal baru</strong>.
                            </div>

                            <!-- Tombol aksi -->
                            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                <a href="<?= site_url('pengajuan') ?>" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-primary text-white fw-semibold px-4" id="btnSimpanPerbaikan">
                                    <i class="bi bi-send me-1"></i>Simpan &amp; Teruskan ke Inspektor
                                </button>
                            </div>

                        </div><!-- end card-body -->
                    </div><!-- end card -->

                </form>

            </div>
        </div>
    </section>
</main>


<style>
    .temuan-card {
        border-color: #f1aeb5 !important;
        background-color: #fff;
    }

    .bukti-thumb {
        position: relative;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        overflow: hidden;
        background: #f8f9fa;
        text-align: center;
        padding: 6px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .bukti-thumb img {
        width: 100%;
        height: 75px;
        object-fit: cover;
        border-radius: 4px;
        margin-bottom: 4px;
    }

    .bukti-thumb .bukti-name {
        font-size: 10px;
        color: #6c757d;
        word-break: break-all;
        line-height: 1.2;
    }

    .foto-temuan-card {
        transition: transform .2s;
    }

    .foto-temuan-card:hover {
        transform: scale(1.03);
    }
</style>


<script>
    $(function() {

        // ════════════════════════════════════════════════════════
        // Live Preview per Item Bukti Upload
        // ════════════════════════════════════════════════════════
        $(document).on('change', '.bukti-item-file', function() {
            var itemId = $(this).data('item-id');
            var $grid  = $('#preview_grid_' + itemId).empty();
            var files  = Array.from(this.files || []);

            if (files.length === 0) return;

            $('#err_bukti_' + itemId).addClass('d-none');

            var imgTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];

            files.forEach(function(file) {
                var $col = $('<div class="col-6 col-md-4 col-lg-3"></div>');
                var $box = $('<div class="bukti-thumb shadow-sm"></div>');

                if (imgTypes.indexOf(file.type) >= 0) {
                    var reader = new FileReader();
                    (function(b, f) {
                        reader.onload = function(e) {
                            b.prepend('<img src="' + e.target.result + '" alt="Bukti">');
                        };
                        reader.readAsDataURL(f);
                    })($box, file);
                } else {
                    var iconCls = file.type === 'application/pdf' ?
                        'bi-file-earmark-pdf text-danger' :
                        (file.type.indexOf('word') >= 0 ? 'bi-file-earmark-word text-primary' : 'bi-file-earmark text-secondary');
                    $box.append('<i class="bi ' + iconCls + ' d-block mb-1" style="font-size:1.8rem;"></i>');
                }

                var fname = file.name.length > 14 ? file.name.substring(0, 12) + '…' : file.name;
                var fsize = file.size < 1024 * 1024 ?
                    Math.round(file.size / 1024) + ' KB' :
                    (file.size / (1024 * 1024)).toFixed(1) + ' MB';
                $box.append('<div class="bukti-name">' + fname + '<br><span class="text-primary fw-bold">' + fsize + '</span></div>');

                $col.append($box);
                $grid.append($col);
            });
        });

        // Preview untuk Bukti Umum
        $('#bukti_perbaikan_umum').on('change', function() {
            var $grid = $('#preview_grid_umum').empty();
            var files = Array.from(this.files || []);
            if (files.length === 0) return;

            var imgTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            files.forEach(function(file) {
                var $col = $('<div class="col-6 col-md-4 col-lg-3"></div>');
                var $box = $('<div class="bukti-thumb shadow-sm"></div>');

                if (imgTypes.indexOf(file.type) >= 0) {
                    var reader = new FileReader();
                    (function(b, f) {
                        reader.onload = function(e) {
                            b.prepend('<img src="' + e.target.result + '" alt="Bukti">');
                        };
                        reader.readAsDataURL(f);
                    })($box, file);
                } else {
                    var iconCls = file.type === 'application/pdf' ?
                        'bi-file-earmark-pdf text-danger' :
                        (file.type.indexOf('word') >= 0 ? 'bi-file-earmark-word text-primary' : 'bi-file-earmark text-secondary');
                    $box.append('<i class="bi ' + iconCls + ' d-block mb-1" style="font-size:1.8rem;"></i>');
                }

                var fname = file.name.length > 14 ? file.name.substring(0, 12) + '…' : file.name;
                $box.append('<div class="bukti-name">' + fname + '</div>');
                $col.append($box);
                $grid.append($col);
            });
        });

        // ════════════════════════════════════════════════════════
        // Validasi dan Konfirmasi Submit
        // ════════════════════════════════════════════════════════
        $('#formPerbaikan').on('submit', function(e) {
            e.preventDefault();

            var isValid = true;
            var missingCount = 0;

            // Periksa setiap item temuan
            $('.temuan-card').each(function() {
                var itemId = $(this).data('item-id');
                var tindakanVal = ($('#tindakan_' + itemId).val() || '').trim();
                var fileInput   = document.getElementById('bukti_item_' + itemId);
                var hasFile     = fileInput && fileInput.files && fileInput.files.length > 0;

                if (!tindakanVal) {
                    $('#err_tindakan_' + itemId).removeClass('d-none');
                    $('#tindakan_' + itemId).addClass('is-invalid');
                    isValid = false;
                    missingCount++;
                } else {
                    $('#err_tindakan_' + itemId).addClass('d-none');
                    $('#tindakan_' + itemId).removeClass('is-invalid');
                }

                if (!hasFile) {
                    $('#err_bukti_' + itemId).removeClass('d-none');
                    $('#bukti_item_' + itemId).addClass('is-invalid');
                    isValid = false;
                    missingCount++;
                } else {
                    $('#err_bukti_' + itemId).addClass('d-none');
                    $('#bukti_item_' + itemId).removeClass('is-invalid');
                }
            });

            if (!isValid) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Belum Lengkap',
                    text: 'Mohon isi deskripsi tindakan dan unggah foto/dokumen bukti perbaikan untuk setiap item temuan.',
                });
                return;
            }

            var totalItems = $('.temuan-card').length;

            Swal.fire({
                title: 'Simpan Data Perbaikan?',
                html: 'Tindakan dan bukti perbaikan untuk <strong>' + totalItems + ' item temuan</strong> akan disimpan.<br>' +
                    'Pengajuan akan langsung diteruskan ke Inspektor untuk <strong>verifikasi fisik</strong>.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4154f1',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-send me-1"></i>Ya, Simpan & Teruskan',
                cancelButtonText: 'Batal',
            }).then(function(r) {
                if (!r.isConfirmed) return;

                NProgress.start();
                var $btn = $('#btnSimpanPerbaikan');
                $btn.prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');

                document.getElementById('formPerbaikan').submit();
            });
        });

    });
</script>