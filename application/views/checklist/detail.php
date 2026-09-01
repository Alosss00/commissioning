<main id="main" class="main">

    <div class="pagetitle">
        <h1>Detail Hasil Inspeksi</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Home</a></li>
                <li class="breadcrumb-item active">Detail Inspeksi</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-xl-12">

                <!-- INFO HEADER -->
                <div class="card mb-3">
                    <div class="card-body py-3">
                        <div class="row align-items-center g-3">
                            <div class="col-md-5 d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width:52px;height:52px;font-size:1.4rem;">
                                    <i class="bi bi-truck text-primary"></i>
                                </div>
                                <div>
                                    <div class="fw-bold fs-6 text-primary"><?= html_escape($uji->no_polisi) ?></div>
                                    <small class="text-muted">
                                        <?= html_escape($uji->jenis_kendaraan) ?>
                                        <?= $uji->nomor_unit ? ' — ' . html_escape($uji->nomor_unit) : '' ?>
                                        | <?= html_escape($uji->merk) ?> <?= html_escape($uji->tipe) ?>
                                        (<?= $uji->tahun ?? '' ?>)
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Inspektor</small>
                                        <strong class="small"><?= html_escape($uji->nama_inspektor ?: ($uji->nama_user_login ?? '—')) ?></strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Perusahaan Inspektor</small>
                                        <strong class="small"><?= html_escape($uji->perusahaan_inspektor ?? '—') ?></strong>
                                    </div>
                                    <?php
                                    $nm_mekanik = $uji->nama_mekanik ?? ($uji->nama_mekanik_master ?? '');
                                    $nm_perus   = $uji->perusahaan_mekanik ?? ($uji->perusahaan_mekanik_master ?? '');
                                    ?>
                                    <?php if (!empty($nm_mekanik)): ?>
                                        <div class="col-6">
                                            <small class="text-muted d-block"><i class="bi bi-tools me-1 text-warning"></i>Mekanik</small>
                                            <strong class="small"><?= html_escape($nm_mekanik) ?></strong>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Perusahaan Mekanik</small>
                                            <strong class="small"><?= html_escape($nm_perus ?: '—') ?></strong>
                                        </div>
                                    <?php endif; ?>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Tanggal</small>
                                        <strong class="small">
                                            <?= !empty($uji->updated_at)
                                                ? date('d M Y H:i', strtotime($uji->updated_at))
                                                : (!empty($uji->created_at) ? date('d M Y H:i', strtotime($uji->created_at)) : '—') ?>
                                        </strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">No. Pengajuan</small>
                                        <strong class="small text-primary">#PU-<?= str_pad($uji->id_pengajuan, 4, '0', STR_PAD_LEFT) ?></strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 text-md-end">
                                <?php if ($summary['lulus']): ?>
                                    <span class="badge bg-success text-white px-3 py-2 fs-6">
                                        <i class="bi bi-check-circle-fill me-1"></i>LULUS
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger text-white px-3 py-2 fs-6">
                                        <i class="bi bi-x-circle-fill me-1"></i>TIDAK LULUS
                                    </span>
                                <?php endif; ?>
                                <div class="mt-1">
                                    <small class="text-muted">
                                        <?= $summary['yes'] ?> Yes &nbsp;|&nbsp;
                                        <span class="text-danger"><?= $summary['no'] ?> No</span> &nbsp;|&nbsp;
                                        <?= $summary['na'] ?> N/A
                                    </small>
                                </div>
                                <div class="mt-2 d-flex gap-2 justify-content-end flex-wrap">
                                    <a href="<?= site_url('checklist/pdf/' . $uji->id_uji) ?>"
                                        class="btn btn-sm btn-danger text-white"
                                        target="_blank"
                                        title="Download / Print PDF Hasil Inspeksi">
                                        <i class="bi bi-file-earmark-pdf me-1"></i>Download PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STAT CARDS -->
                <div class="row g-3 mb-3">
                    <div class="col-6 col-md-3">
                        <div class="card border-0 bg-primary bg-opacity-10 text-center py-3">
                            <div class="fs-2 fw-bold text-primary"><?= $summary['total'] ?></div>
                            <div class="text-muted small">Total Item</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border-0 bg-success bg-opacity-10 text-center py-3">
                            <div class="fs-2 fw-bold text-success"><?= $summary['yes'] ?></div>
                            <div class="text-muted small">OK / Yes</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border-0 bg-danger bg-opacity-10 text-center py-3">
                            <div class="fs-2 fw-bold text-danger"><?= $summary['no'] ?></div>
                            <div class="text-muted small">Tidak OK / No</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border-0 bg-secondary bg-opacity-10 text-center py-3">
                            <div class="fs-2 fw-bold text-secondary"><?= $summary['na'] ?></div>
                            <div class="text-muted small">N/A</div>
                        </div>
                    </div>
                </div>

                <!-- TEMUAN / CATATAN -->
                <?php $catatan_val = $uji->catatan_temuan ?? $uji->catatan_umum ?? ''; ?>
                <?php if (!empty($catatan_val)): ?>
                    <div class="alert alert-warning py-2 mb-3">
                        <i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i>
                        <strong>Temuan Inspeksi:</strong> <?= html_escape($catatan_val) ?>
                    </div>
                <?php endif; ?>

                <!-- TOMBOL TOGGLE JIKA TIDAK LULUS -->
                <?php if (!$summary['lulus']): ?>
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 p-3 bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded-3">
                        <div>
                            <span class="fw-bold text-danger"><i class="bi bi-x-circle-fill me-1"></i>Hasil Inspeksi: Tidak Lulus</span>
                            <span class="badge bg-danger ms-2"><?= $summary['no'] ?> Item Temuan</span>
                            <div class="small text-muted mt-1">Klik tombol di samping untuk membuka rincian form checklist hasil inspeksi.</div>
                        </div>
                        <button class="btn btn-danger text-white btn-sm px-3 fw-semibold"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapseChecklistFormAktif"
                            aria-expanded="false"
                            aria-controls="collapseChecklistFormAktif">
                            <i class="bi bi-card-checklist me-1"></i>Lihat Form Hasil Inspeksi (<?= $summary['total'] ?> Item)
                        </button>
                    </div>
                <?php endif; ?>

                <div class="collapse <?= $summary['lulus'] ? 'show' : '' ?>" id="collapseChecklistFormAktif">
                    <!-- CHECKLIST PER KATEGORI -->
                    <?php
                    $kategori_order = ['CRITICAL', 'GENERAL'];
                    $kategori_style = [
                        'CRITICAL' => ['bg-danger',  'text-danger',  'danger'],
                        'GENERAL'  => ['bg-primary', 'text-primary', 'primary'],
                    ];
                    foreach ($kategori_order as $kat):
                        if (empty($grouped[$kat])) continue;
                        $items_kat = $grouped[$kat];
                        $col       = $kategori_style[$kat] ?? ['bg-secondary', 'text-secondary', 'secondary'];
                        $cnt_no    = count(array_filter($items_kat, fn($i) => $i->hasil === 'no'));
                    ?>
                        <div class="card mb-3 shadow-sm">
                            <div class="card-header d-flex justify-content-between align-items-center py-2
                            <?= $col[0] ?> bg-opacity-15 border-<?= $col[2] ?>">
                                <span class="fw-bold <?= $col[1] ?>">
                                    <i class="bi bi-tag-fill me-1"></i><?= $kat ?>
                                </span>
                                <div class="d-flex gap-2 align-items-center">
                                    <?php if ($cnt_no > 0): ?>
                                        <span class="badge bg-danger text-white"><?= $cnt_no ?> Tidak OK</span>
                                    <?php endif; ?>
                                    <span class="badge bg-secondary text-white"><?= count($items_kat) ?> item</span>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width:50px;" class="text-center">No.</th>
                                                <th>Kriteria Pemeriksaan</th>
                                                <th style="width:100px;" class="text-center">Hasil</th>
                                                <th>Catatan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($items_kat as $item): ?>
                                                <tr class="<?= $item->hasil === 'no' ? 'table-danger' : ($item->hasil === 'na' ? 'table-secondary' : '') ?>">
                                                    <td class="text-center text-muted small"><?= html_escape($item->no_urut) ?></td>
                                                    <td><?= html_escape($item->kriteria) ?></td>
                                                    <td class="text-center">
                                                        <?php if ($item->hasil === 'yes'): ?>
                                                            <span class="badge bg-success text-white px-2">
                                                                <i class="bi bi-check-lg me-1"></i>YES
                                                            </span>
                                                        <?php elseif ($item->hasil === 'no'): ?>
                                                            <span class="badge bg-danger text-white px-2">
                                                                <i class="bi bi-x-lg me-1"></i>NO
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary text-white px-2">N/A</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted"><?= html_escape($item->keterangan ?? '') ?></small>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- DOKUMENTASI FOTO INSPEKSI & LAMPIRAN -->
                <div class="card mb-3">
                    <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-dark">
                            <i class="bi bi-camera-fill me-2 text-primary"></i>Dokumentasi Foto Inspeksi & Lampiran
                        </h6>
                        <?php if (!empty($foto_list)): ?>
                            <span class="badge bg-primary text-white"><?= count($foto_list) ?> foto inspeksi</span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body pt-3">
                        <?php if (empty($foto_list) && empty($lampiran_pengajuan)): ?>
                            <div class="text-center text-muted py-3">
                                <i class="bi bi-image-alt fs-2 d-block mb-1 opacity-50"></i>
                                Belum ada foto dokumentasi inspeksi atau lampiran yang diupload.
                            </div>
                        <?php else: ?>
                            
                            <?php
                            $foto_mekanik = [];
                            $foto_temuan  = [];
                            if (!empty($foto_list)) {
                                foreach ($foto_list as $f) {
                                    if ($f->jenis === 'mekanik') {
                                        $foto_mekanik[] = $f;
                                    } else {
                                        $foto_temuan[] = $f;
                                    }
                                }
                            }
                            ?>

                            <!-- FOTO MEKANIK / PESERTA COMMISSIONING -->
                            <?php if (!empty($foto_mekanik)): ?>
                                <div class="mb-3">
                                    <h6 class="fw-bold small text-muted text-uppercase mb-2">
                                        <i class="bi bi-person-badge me-1 text-warning"></i>Foto Mekanik / Peserta Commissioning
                                    </h6>
                                    <div class="row g-2">
                                        <?php foreach ($foto_mekanik as $fm): ?>
                                            <div class="col-6 col-sm-4 col-md-3">
                                                <div class="border rounded p-2 text-center h-100 bg-light">
                                                    <a href="<?= base_url($fm->file_path) ?>" target="_blank" class="d-block">
                                                        <img src="<?= base_url($fm->file_path) ?>" 
                                                             class="img-fluid rounded border mb-2" 
                                                             style="height:140px;width:100%;object-fit:cover;"
                                                             alt="Foto Mekanik"
                                                             onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'140\' viewBox=\'0 0 100 140\'><rect width=\'100%\' height=\'100%\' fill=\'%23f8f9fa\'/><text x=\'50%\' y=\'50%\' dominant-baseline=\'middle\' text-anchor=\'middle\' fill=\'%23dc3545\' font-size=\'10\' font-family=\'sans-serif\'>File Tidak Ada</text></svg>';">
                                                    </a>
                                                    <div class="fw-semibold small text-truncate" title="<?= html_escape(basename($fm->file_path)) ?>"><?= html_escape(basename($fm->file_path)) ?></div>
                                                    <?php if (!empty($fm->keterangan)): ?>
                                                        <small class="text-muted d-block mt-1" style="font-size:11px;"><?= html_escape($fm->keterangan) ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- FOTO TEMUAN / HASIL INSPEKSI -->
                            <?php if (!empty($foto_temuan)): ?>
                                <div class="mb-3">
                                    <h6 class="fw-bold small text-muted text-uppercase mb-2">
                                        <i class="bi bi-images me-1 text-danger"></i>Foto Temuan / Dokumentasi Pemeriksaan Unit
                                    </h6>
                                    <div class="row g-2">
                                        <?php foreach ($foto_temuan as $ft): ?>
                                            <div class="col-6 col-sm-4 col-md-3">
                                                <div class="border rounded p-2 text-center h-100 bg-light">
                                                    <a href="<?= base_url($ft->file_path) ?>" target="_blank" class="d-block">
                                                        <img src="<?= base_url($ft->file_path) ?>" 
                                                             class="img-fluid rounded border mb-2" 
                                                             style="height:140px;width:100%;object-fit:cover;"
                                                             alt="Foto Temuan"
                                                             onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'140\' viewBox=\'0 0 100 140\'><rect width=\'100%\' height=\'100%\' fill=\'%23f8f9fa\'/><text x=\'50%\' y=\'50%\' dominant-baseline=\'middle\' text-anchor=\'middle\' fill=\'%23dc3545\' font-size=\'10\' font-family=\'sans-serif\'>File Tidak Ada</text></svg>';">
                                                    </a>
                                                    <div class="fw-semibold small text-truncate" title="<?= html_escape(basename($ft->file_path)) ?>"><?= html_escape(basename($ft->file_path)) ?></div>
                                                    <?php if (!empty($ft->keterangan)): ?>
                                                        <small class="text-muted d-block mt-1" style="font-size:11px;"><?= html_escape($ft->keterangan) ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- LAMPIRAN UNIT PENGAJUAN -->
                            <?php if (!empty($lampiran_pengajuan)): ?>
                                <div>
                                    <h6 class="fw-bold small text-muted text-uppercase mb-2">
                                        <i class="bi bi-paperclip me-1 text-info"></i>Foto Unit & Lampiran Dokumen Pengajuan
                                    </h6>
                                    <div class="row g-2">
                                        <?php 
                                        $jenis_label = [
                                            'sertifikasi'        => 'Sertifikat / Layak Operasi',
                                            'stnk'               => 'STNK Kendaraan',
                                            'unit_depan'         => 'Foto Depan',
                                            'unit_belakang'      => 'Foto Belakang',
                                            'unit_kiri'          => 'Foto Kiri',
                                            'unit_kanan'         => 'Foto Kanan',
                                            'maintenance_record' => 'Maintenance Record',
                                        ];
                                        foreach ($lampiran_pengajuan as $lamp):
                                            $label  = $jenis_label[$lamp->jenis_lampiran] ?? ucfirst(str_replace('_', ' ', $lamp->jenis_lampiran));
                                            $ext    = strtolower(pathinfo($lamp->file_path, PATHINFO_EXTENSION));
                                            $is_img = in_array($ext, ['jpg', 'jpeg', 'png', 'webp']);
                                        ?>
                                            <div class="col-6 col-sm-4 col-md-3">
                                                <div class="border rounded p-2 text-center h-100">
                                                    <?php if ($is_img): ?>
                                                        <a href="<?= base_url($lamp->file_path) ?>" target="_blank">
                                                            <img src="<?= base_url($lamp->file_path) ?>" 
                                                                 class="img-fluid rounded mb-1" 
                                                                 style="height:100px;width:100%;object-fit:cover;"
                                                                 alt="<?= html_escape($label) ?>"
                                                                 onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\' viewBox=\'0 0 100 100\'><rect width=\'100%\' height=\'100%\' fill=\'%23f8f9fa\'/><text x=\'50%\' y=\'50%\' dominant-baseline=\'middle\' text-anchor=\'middle\' fill=\'%23dc3545\' font-size=\'10\' font-family=\'sans-serif\'>File Tidak Ada</text></svg>';">
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="<?= base_url($lamp->file_path) ?>" target="_blank" class="d-block py-3 text-danger">
                                                            <i class="bi bi-file-earmark-pdf fs-1"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <div class="fw-semibold small"><?= html_escape($label) ?></div>
                                                    <small class="text-muted d-block text-truncate" style="font-size:10px;"><?= html_escape(basename($lamp->file_path)) ?></small>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                        <?php endif; ?>
                    </div>
                </div>

                <!-- TOMBOL BAWAH -->
                <div class="d-flex gap-2 mb-3 flex-wrap align-items-center">
                    <button onclick="history.back();" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </button>
                    <?php if (!empty($uji->id_pengajuan)): ?>
                        <a href="<?= site_url('pengajuan') ?>" class="btn btn-outline-primary">
                            <i class="bi bi-list-ul me-1"></i>Ke Daftar Pengajuan
                        </a>
                    <?php endif; ?>

                    <?php
                    /* Tampilkan tombol riwayat hanya jika ada history atau perbaikan */
                    $_has_hist = !empty($history_versions);
                    $_has_perb = !empty($perbaikan_list);
                    ?>
                    <?php if ($_has_hist || $_has_perb): ?>
                        <a href="#sectionHistoryInspeksi"
                            class="btn btn-outline-secondary"
                            onclick="
               var el = document.getElementById('sectionHistoryInspeksi');
               if(el){ el.scrollIntoView({behavior:'smooth',block:'start'}); }
               return false;">
                            <i class="bi bi-clock-history me-1"></i>
                            Riwayat Inspeksi
                            <?php if ($_has_hist): ?>
                                <span class="badge bg-secondary ms-1"><?= count($history_versions) ?></span>
                            <?php endif; ?>
                            <?php if ($_has_perb): ?>
                                <span class="badge bg-warning text-dark ms-1">
                                    <i class="bi bi-tools"></i> <?= count($perbaikan_list) ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>

                    <a href="<?= site_url('checklist/pdf/' . $uji->id_uji) ?>"
                        class="btn btn-danger text-white"
                        target="_blank">
                        <i class="bi bi-file-earmark-pdf me-1"></i>Download PDF Hasil Inspeksi
                    </a>
                </div>

            </div>
        </div>
    </section>
    <?php if (!empty($history_versions) || !empty($perbaikan_list)):
        $this->load->view('checklist/history', [
            'history_versions' => $history_versions ?? [],
            'history_detail'   => $history_detail   ?? [],
            'perbaikan_list'   => $perbaikan_list   ?? [],
        ]);
    endif; ?>
</main>