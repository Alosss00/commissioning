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
                                <h5 class="mb-0 fw-bold"><?= html_escape($pengajuan->no_polisi) ?></h5>
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

                <!-- ═══ FORM PERBAIKAN ═══ -->
                <form method="POST" action="<?= site_url('perbaikan/store') ?>"
                    enctype="multipart/form-data" id="formPerbaikan">
                    <?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()) ?>

                    <input type="hidden" name="id_pengajuan" value="<?= $pengajuan->id_pengajuan ?>">
                    <?php if ($uji): ?>
                        <input type="hidden" name="id_uji" value="<?= $uji->id_uji ?>">
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-header bg-primary text-white py-2">
                            <h6 class="mb-0 fw-bold text-white">
                                <i class="bi bi-clipboard-check me-2"></i>Data Perbaikan Unit
                            </h6>
                        </div>
                        <div class="card-body pt-4">
                            <div class="row g-3">

                                <!-- Catatan Perbaikan -->
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Catatan Perbaikan</label>
                                    <textarea class="form-control"
                                        name="catatan_perbaikan"
                                        rows="4"
                                        maxlength="1000"
                                        placeholder="Deskripsikan perbaikan yang telah dilakukan pada setiap item temuan di atas. Misalnya: lampu rem diganti, ban kanan depan diganti, dll."></textarea>
                                    <small class="text-muted">Jelaskan tindakan perbaikan yang sudah dilakukan untuk setiap temuan di atas.</small>
                                </div>

                                <!-- Upload Bukti Perbaikan (multiple, maks 10) -->
                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        Bukti Perbaikan
                                        <span class="badge bg-warning text-dark ms-1">Disarankan · Maks 10 file</span>
                                    </label>

                                    <div class="border rounded p-3 border-warning" id="buktiDropZone">
                                        <div class="d-flex align-items-center gap-3 mb-2">
                                            <i class="bi bi-camera-fill text-warning flex-shrink-0" style="font-size:2rem;"></i>
                                            <div class="flex-grow-1">
                                                <input type="file"
                                                    class="form-control"
                                                    name="bukti_perbaikan[]"
                                                    id="bukti_perbaikan"
                                                    accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                                                    multiple>
                                                <small class="text-muted">
                                                    Pilih 1–10 file sekaligus. Format: JPG, PNG, PDF, Word. Maks 10MB per file.<br>
                                                    Contoh: foto unit setelah diperbaiki, work order bengkel, laporan mekanik.
                                                </small>
                                            </div>
                                        </div>

                                        <!-- Counter file -->
                                        <div class="d-flex align-items-center gap-2 mb-2" id="buktiCounter" style="display:none!important;">
                                            <span class="badge bg-primary" id="buktiCountBadge">0 file dipilih</span>
                                            <button type="button" class="btn btn-sm btn-outline-danger py-0" id="btnHapusSemua">
                                                <i class="bi bi-trash me-1"></i>Hapus Semua
                                            </button>
                                        </div>

                                        <!-- Preview grid -->
                                        <div id="buktiPreviewWrap" class="d-none">
                                            <div class="small fw-semibold text-muted mb-2">File dipilih:</div>
                                            <div class="row g-2" id="buktiPreviewGrid"></div>
                                        </div>
                                    </div>

                                    <!-- Warning maks -->
                                    <div class="text-danger small mt-1 d-none" id="warnMaks">
                                        <i class="bi bi-exclamation-triangle me-1"></i>Maksimal 10 file. File ke-11 dan seterusnya tidak akan diupload.
                                    </div>
                                </div>

                                <!-- Info alur -->
                                <div class="col-12">
                                    <div class="alert alert-success py-2 mb-0 small">
                                        <i class="bi bi-arrow-right-circle-fill me-2"></i>
                                        Setelah disimpan, inspektor
                                        <strong><?= isset($verifikator) && $verifikator ? html_escape($verifikator->nama) : 'yang ditugaskan' ?></strong>
                                        akan langsung dapat memverifikasi perbaikan — <strong>tanpa perlu membuat jadwal ulang</strong>.
                                        Jika lulus verifikasi → langsung diteruskan ke OHS Superintendent.
                                    </div>
                                </div>

                            </div><!-- end row -->

                            <!-- Tombol aksi -->
                            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                <a href="<?= site_url('pengajuan') ?>" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-primary text-white" id="btnSimpanPerbaikan">
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
    .bukti-thumb {
        position: relative;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        overflow: hidden;
        background: #f8f9fa;
        text-align: center;
        padding: 8px;
        min-height: 110px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .bukti-thumb img {
        width: 100%;
        height: 80px;
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

    .bukti-thumb .btn-remove-bukti {
        position: absolute;
        top: 4px;
        right: 4px;
        width: 20px;
        height: 20px;
        font-size: 10px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
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
        // Bukti Perbaikan — multiple file, maks 10
        // Menggunakan DataTransfer API untuk track file array
        // ════════════════════════════════════════════════════════
        var selectedFiles = []; // array of File objects

        $('#bukti_perbaikan').on('change', function() {
            var newFiles = Array.from(this.files);
            var added = 0;

            newFiles.forEach(function(file) {
                if (selectedFiles.length >= 10) {
                    $('#warnMaks').removeClass('d-none');
                    return;
                }
                // Cek duplikat nama + size
                var isDup = selectedFiles.some(function(f) {
                    return f.name === file.name && f.size === file.size;
                });
                if (!isDup) {
                    selectedFiles.push(file);
                    added++;
                }
            });

            if (selectedFiles.length <= 10) $('#warnMaks').addClass('d-none');

            renderBuktiPreview();

            // Reset input supaya bisa pilih file yang sama lagi
            var el = document.getElementById('bukti_perbaikan');
            var neu = el.cloneNode(true);
            el.parentNode.replaceChild(neu, el);
            // Re-bind event ke input baru
            document.getElementById('bukti_perbaikan').addEventListener('change', function() {
                $('#bukti_perbaikan').trigger('change');
            });
            rebindFileInput();
        });

        function rebindFileInput() {
            $('#bukti_perbaikan').off('change').on('change', function() {
                var newFiles = Array.from(this.files);
                newFiles.forEach(function(file) {
                    if (selectedFiles.length >= 10) {
                        $('#warnMaks').removeClass('d-none');
                        return;
                    }
                    var isDup = selectedFiles.some(function(f) {
                        return f.name === file.name && f.size === file.size;
                    });
                    if (!isDup) selectedFiles.push(file);
                });
                if (selectedFiles.length <= 10) $('#warnMaks').addClass('d-none');
                renderBuktiPreview();
                // Reset input
                var el = document.getElementById('bukti_perbaikan');
                var neu = el.cloneNode(true);
                el.parentNode.replaceChild(neu, el);
                rebindFileInput();
            });
        }
        rebindFileInput();

        function renderBuktiPreview() {
            var $grid = $('#buktiPreviewGrid').empty();
            var $wrap = $('#buktiPreviewWrap');
            var $cnt = $('#buktiCountBadge');
            var $ctr = $('#buktiCounter');

            if (selectedFiles.length === 0) {
                $wrap.addClass('d-none');
                $ctr.hide();
                return;
            }

            $wrap.removeClass('d-none');
            $ctr.show().css('display', 'flex');
            $cnt.text(selectedFiles.length + ' file dipilih');

            var imgTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];

            selectedFiles.forEach(function(file, idx) {
                var $col = $('<div class="col-6 col-sm-4 col-md-3 col-lg-2"></div>');
                var $box = $('<div class="bukti-thumb"></div>');

                // Tombol hapus per file
                $box.append(
                    '<button type="button" class="btn btn-sm btn-outline-danger btn-remove-bukti"' +
                    ' data-idx="' + idx + '" title="Hapus file ini">' +
                    '<i class="bi bi-x"></i></button>'
                );

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
                    $box.append('<i class="bi ' + iconCls + ' d-block mb-1" style="font-size:2rem;"></i>');
                }

                var fname = file.name.length > 18 ? file.name.substring(0, 16) + '…' : file.name;
                var fsize = file.size < 1024 * 1024 ?
                    Math.round(file.size / 1024) + ' KB' :
                    (file.size / (1024 * 1024)).toFixed(1) + ' MB';
                $box.append('<div class="bukti-name">' + fname + '<br><span class="text-primary">' + fsize + '</span></div>');

                $col.append($box);
                $grid.append($col);
            });

            // Sync file input dengan DataTransfer (untuk form submit)
            syncFileInput();
        }

        // Hapus file per item
        $(document).on('click', '.btn-remove-bukti', function() {
            var idx = parseInt($(this).data('idx'));
            selectedFiles.splice(idx, 1);
            if (selectedFiles.length <= 10) $('#warnMaks').addClass('d-none');
            renderBuktiPreview();
        });

        // Hapus semua
        $('#btnHapusSemua').on('click', function() {
            selectedFiles = [];
            renderBuktiPreview();
            $('#warnMaks').addClass('d-none');
        });

        // Sync selectedFiles ke actual file input via DataTransfer
        // Diperlukan agar form POST mengirim file yang benar
        function syncFileInput() {
            try {
                var dt = new DataTransfer();
                selectedFiles.forEach(function(f) {
                    dt.items.add(f);
                });
                var el = document.getElementById('bukti_perbaikan');
                if (!el) return;
                el.files = dt.files;
            } catch (e) {
                // DataTransfer tidak didukung di browser lama — fallback: biarkan input biasa
            }
        }

        // ════════════════════════════════════════════════════════
        // Drag & Drop zone
        // ════════════════════════════════════════════════════════
        var $zone = $('#buktiDropZone');
        $zone.on('dragover dragenter', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $zone.addClass('border-primary bg-primary bg-opacity-10');
        }).on('dragleave drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $zone.removeClass('border-primary bg-primary bg-opacity-10');
            if (e.type === 'drop') {
                var files = Array.from(e.originalEvent.dataTransfer.files);
                files.forEach(function(file) {
                    if (selectedFiles.length >= 10) {
                        $('#warnMaks').removeClass('d-none');
                        return;
                    }
                    var isDup = selectedFiles.some(function(f) {
                        return f.name === file.name && f.size === file.size;
                    });
                    if (!isDup) selectedFiles.push(file);
                });
                renderBuktiPreview();
            }
        });

        // ════════════════════════════════════════════════════════
        // Form Submit — konfirmasi
        // ════════════════════════════════════════════════════════
        $('#formPerbaikan').on('submit', function(e) {
            e.preventDefault();

            // Sync file sebelum submit
            syncFileInput();

            Swal.fire({
                title: 'Simpan Data Perbaikan?',
                html: 'Setelah disimpan, inspektor akan langsung dapat memverifikasi perbaikan ini ' +
                    '— <strong>tanpa jadwal ulang</strong>.<br>' +
                    (selectedFiles.length > 0 ?
                        '<small class="text-muted">' + selectedFiles.length + ' file bukti akan diupload.</small>' :
                        '<small class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>Belum ada bukti perbaikan yang diupload.</small>'),
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

                // Sync sekali lagi lalu submit
                syncFileInput();
                document.getElementById('formPerbaikan').submit();
            });
        });

    });
</script>