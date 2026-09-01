<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Verifikasi Fisik Hasil Perbaikan</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url('perbaikan') ?>">Daftar Perbaikan</a></li>
                <li class="breadcrumb-item active">Verifikasi #PU-<?= str_pad($pengajuan->id_pengajuan, 4, '0', STR_PAD_LEFT) ?></li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row justify-content-center">
            <div class="col-xl-10">

                <!-- ═══ HEADER KENDARAAN ═══ -->
                <div class="card mb-3 border-info">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <div class="rounded-circle bg-info d-flex align-items-center justify-content-center text-white flex-shrink-0"
                                style="width:50px;height:50px;font-size:1.3rem;">
                                <i class="bi bi-patch-check"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">
                                    <?= html_escape($pengajuan->no_polisi) ?>
                                    <?= badge_tipe_akses($pengajuan->tipe_akses, 'font-size:11px;') ?>
                                </h5>
                                <small class="text-muted">
                                    <?= html_escape($pengajuan->jenis_kendaraan) ?> —
                                    <?= html_escape($pengajuan->merk) ?> <?= html_escape($pengajuan->tipe) ?>
                                    (<?= $pengajuan->tahun ?>) · <?= html_escape($pengajuan->perusahaan) ?>
                                </small>
                            </div>
                            <div class="ms-auto text-end">
                                <span class="badge bg-info text-white px-3 py-2">
                                    <i class="bi bi-patch-check me-1"></i>Siap Verifikasi Fisik
                                </span>
                                <div><small class="text-muted">#PU-<?= str_pad($pengajuan->id_pengajuan, 4, '0', STR_PAD_LEFT) ?></small></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ═══ REVIEW TEMUAN & BUKTI PERBAIKAN PER ITEM ═══ -->
                <div class="card mb-3 border-0 shadow-sm rounded-3">
                    <div class="card-header bg-primary text-white py-3 px-3 d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-bold text-white">
                            <i class="bi bi-tools me-2"></i>Review Hasil Perbaikan per Temuan
                        </h6>
                        <span class="badge bg-white text-primary fw-bold">
                            <?= !empty($checklist_no) ? count($checklist_no) . ' Item Temuan' : '0 Item' ?>
                        </span>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        
                        <?php if (!empty($checklist_no)): ?>
                            <div class="vstack gap-3">
                                <?php foreach ($checklist_no as $idx => $item): ?>
                                    <?php
                                    // Ambil lampiran khusus item ini
                                    $lampiran_item = [];
                                    if (!empty($lampiran_perbaikan)) {
                                        foreach ($lampiran_perbaikan as $lp) {
                                            if ((int)$lp->id_item === (int)$item->id_item) {
                                                $lampiran_item[] = $lp;
                                            }
                                        }
                                    }
                                    ?>
                                    <div class="card border rounded-3 shadow-sm mb-0">
                                        <div class="card-header bg-light py-2 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-<?= $item->kategori === 'CRITICAL' ? 'danger' : 'warning text-dark' ?>">
                                                    <?= html_escape($item->kategori) ?>
                                                </span>
                                                <span class="badge bg-dark text-white" style="font-size:11px;">#<?= $idx + 1 ?></span>
                                                <strong class="text-dark">
                                                    <?= html_escape($item->kriteria) ?>
                                                </strong>
                                                <?php if (!empty($item->no_urut)): ?>
                                                    <small class="text-muted">(Item No. <?= html_escape($item->no_urut) ?>)</small>
                                                <?php endif; ?>
                                            </div>
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 small">
                                                Temuan Inspeksi
                                            </span>
                                        </div>
                                        <div class="card-body p-3">
                                            <!-- Catatan temuan inspektor sebelumnya -->
                                            <?php if (!empty($item->keterangan)): ?>
                                                <div class="alert alert-warning py-2 px-3 mb-3 small">
                                                    <strong class="text-dark"><i class="bi bi-exclamation-triangle me-1"></i>Catatan Temuan Inspeksi:</strong>
                                                    <div class="mt-1"><?= nl2br(html_escape($item->keterangan)) ?></div>
                                                </div>
                                            <?php endif; ?>

                                            <!-- Rincian tindakan yang dilakukan pemohon -->
                                            <?php
                                            $tindakan_item_text = '';
                                            if (!empty($lampiran_item) && !empty($lampiran_item[0]->keterangan)) {
                                                $tindakan_item_text = $lampiran_item[0]->keterangan;
                                            }
                                            ?>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold small text-primary mb-1">
                                                    <i class="bi bi-wrench-adjustable me-1"></i>Tindakan Perbaikan dari Pemohon:
                                                </label>
                                                <div class="p-2 bg-light rounded border small">
                                                    <?= !empty($tindakan_item_text) ? nl2br(html_escape($tindakan_item_text)) : '<em class="text-muted">Tercatat dalam ringkasan perbaikan</em>' ?>
                                                </div>
                                            </div>

                                            <!-- Foto Bukti Perbaikan Item Ini -->
                                            <div>
                                                <label class="form-label fw-semibold small text-success mb-2">
                                                    <i class="bi bi-camera me-1"></i>Foto / Bukti Perbaikan Item Ini (<?= count($lampiran_item) ?> file):
                                                </label>
                                                <?php if (!empty($lampiran_item)): ?>
                                                    <div class="row g-2">
                                                        <?php foreach ($lampiran_item as $lp): ?>
                                                            <?php
                                                            $ext = strtolower(pathinfo($lp->file_path, PATHINFO_EXTENSION));
                                                            $is_img = in_array($ext, ['jpg', 'jpeg', 'png', 'webp']);
                                                            ?>
                                                            <div class="col-6 col-sm-4 col-md-3">
                                                                <div class="border rounded overflow-hidden shadow-sm bg-white text-center">
                                                                    <?php if ($is_img): ?>
                                                                        <a href="<?= base_url($lp->file_path) ?>" target="_blank" title="Klik untuk memperbesar">
                                                                            <img src="<?= base_url($lp->file_path) ?>" class="img-fluid w-100" style="height:100px;object-fit:cover;">
                                                                        </a>
                                                                    <?php else: ?>
                                                                        <a href="<?= base_url($lp->file_path) ?>" target="_blank" class="d-flex flex-column align-items-center justify-content-center p-3 text-decoration-none bg-light" style="height:100px;">
                                                                            <i class="bi bi-file-earmark-pdf text-danger fs-2"></i>
                                                                            <span class="small mt-1 text-truncate w-100"><?= html_escape(basename($lp->file_path)) ?></span>
                                                                        </a>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="text-muted small fst-italic">
                                                        <i class="bi bi-dash me-1"></i>Tidak ada lampiran spesifik untuk item ini (terlampir pada lampiran umum).
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Ringkasan Catatan Umum Perbaikan -->
                        <?php if ($perbaikan && !empty($perbaikan->catatan_perbaikan)): ?>
                            <div class="card border rounded-3 bg-light mt-3">
                                <div class="card-header bg-white py-2 px-3">
                                    <h6 class="mb-0 fw-bold small text-secondary">
                                        <i class="bi bi-chat-left-text me-1 text-primary"></i>Catatan Lengkap / Tambahan Perbaikan
                                    </h6>
                                </div>
                                <div class="card-body p-3 small">
                                    <?= nl2br(html_escape($perbaikan->catatan_perbaikan)) ?>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

                <!-- ═══ KEPUTUSAN VERIFIKASI FISIK ═══ -->
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-dark text-white py-3 px-3">
                        <h6 class="mb-0 fw-bold text-white">
                            <i class="bi bi-check2-circle me-2"></i>Keputusan Verifikasi Fisik oleh Inspektor
                        </h6>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <p class="small text-muted mb-3">
                            Pastikan Anda telah melakukan pemeriksaan fisik langsung terhadap komponen yang diperbaiki di unit kendaraan.
                        </p>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Catatan / Keterangan Verifikasi</label>
                            <textarea class="form-control" id="catatan_verifikasi" rows="3"
                                placeholder="Masukkan catatan hasil verifikasi fisik (Wajib diisi jika menolak verifikasi)..."></textarea>
                        </div>

                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-3 border-top">
                            <a href="<?= site_url('perbaikan') ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Kembali
                            </a>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-danger text-white px-3" id="btnTolakVerifikasi">
                                    <i class="bi bi-x-circle me-1"></i>Tolak Verifikasi
                                </button>
                                <button type="button" class="btn btn-success text-white px-4 fw-semibold" id="btnAccVerifikasi">
                                    <i class="bi bi-check-circle me-1"></i>ACC Verifikasi Fisik (Lulus)
                                </button>
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
        var idPengajuan = <?= (int)$pengajuan->id_pengajuan ?>;
        var csrfName = '<?= $this->security->get_csrf_token_name() ?>';
        var csrfHash = '<?= $this->security->get_csrf_hash() ?>';

        function kirimKeputusan(aksi) {
            var catatan = ($('#catatan_verifikasi').val() || '').trim();

            if (aksi === 'tolak' && !catatan) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Catatan Diperlukan',
                    text: 'Mohon isi catatan alasan penolakan verifikasi fisik.',
                });
                return;
            }

            var confirmTitle = aksi === 'acc' ? 'ACC Verifikasi Fisik?' : 'Tolak Verifikasi Fisik?';
            var confirmText  = aksi === 'acc' ?
                'Unit akan dinyatakan LULUS verifikasi fisik dan siap untuk inspeksi ulang / diteruskan ke OHS.' :
                'Unit akan dikembalikan ke status Perlu Perbaikan agar diperbaiki ulang oleh Admin Departemen.';
            var confirmColor = aksi === 'acc' ? '#198754' : '#dc3545';

            Swal.fire({
                title: confirmTitle,
                html: confirmText,
                icon: aksi === 'acc' ? 'question' : 'warning',
                showCancelButton: true,
                confirmButtonColor: confirmColor,
                cancelButtonColor: '#6c757d',
                confirmButtonText: aksi === 'acc' ? '<i class="bi bi-check-circle me-1"></i>Ya, ACC Verifikasi' : '<i class="bi bi-x-circle me-1"></i>Ya, Tolak',
                cancelButtonText: 'Batal',
            }).then(function(r) {
                if (!r.isConfirmed) return;

                NProgress.start();
                var postData = {
                    id_pengajuan: idPengajuan,
                    aksi: aksi,
                    catatan: catatan
                };
                postData[csrfName] = csrfHash;

                $.ajax({
                    url: '<?= site_url('perbaikan/acc_verifikasi') ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: postData,
                    success: function(res) {
                        NProgress.done();
                        if (res.csrfHash) csrfHash = res.csrfHash;

                        if (res.status === 'ok') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                            }).then(function() {
                                window.location.href = res.redirect || '<?= site_url('perbaikan') ?>';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: res.message || 'Terjadi kesalahan sistem.',
                            });
                        }
                    },
                    error: function() {
                        NProgress.done();
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan Jaringan',
                            text: 'Gagal menghubungi server.',
                        });
                    }
                });
            });
        }

        $('#btnAccVerifikasi').on('click', function() {
            kirimKeputusan('acc');
        });

        $('#btnTolakVerifikasi').on('click', function() {
            kirimKeputusan('tolak');
        });
    });
</script>
