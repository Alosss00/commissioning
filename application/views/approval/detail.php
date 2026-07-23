<main id="main" class="main">

    <div class="pagetitle">
        <h1>Detail Pengajuan</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                <?php
                $back_url_map = [
                    'dept_manager'   => 'approval/manager',
                    'admin_ohs'      => 'approval/admin_ohs',
                    'ohs_supt'       => 'approval/ohs_supt',
                    'ktt'            => 'approval/ktt',
                    'release_stiker' => 'approval/stiker',
                    'inspeksi_verif' => 'approval/verif_perbaikan',
                ];
                $back_url = site_url($back_url_map[$level] ?? 'dashboard');
                ?>
                <li class="breadcrumb-item"><a href="<?= $back_url ?>"><?= html_escape($cfg['label']) ?></a></li>
                <li class="breadcrumb-item active">#PU-<?= str_pad($pengajuan->id_pengajuan, 4, '0', STR_PAD_LEFT) ?></li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row justify-content-center">
            <div class="col-xl-9">

                <?php
                $status_masuk  = is_array($cfg['status_masuk']) ? $cfg['status_masuk'] : [$cfg['status_masuk']];
                $is_pending    = in_array($pengajuan->status, $status_masuk);
                $boleh_approve = true;
                if ($level === 'ktt' && isset($has_approved) && $has_approved) {
                    $boleh_approve = false;
                }

                $sl = $status_labels[$pengajuan->status] ?? ['bg-secondary text-white', $pengajuan->status];

                // Deteksi role user saat ini (untuk tombol cabut stiker)
                $cur_roles_raw = $this->session->userdata('roles');
                $cur_role_int  = (int) $this->session->userdata('role');
                if (is_array($cur_roles_raw) && !empty($cur_roles_raw)) {
                    $cur_roles = array_map('intval', $cur_roles_raw);
                } elseif ($cur_role_int > 0) {
                    $cur_roles = [$cur_role_int];
                } else {
                    $cur_roles = [];
                }
                $is_ktt_or_admin = in_array(1, $cur_roles) || in_array(2, $cur_roles);
                ?>

                <!-- HEADER CARD -->
                <div class="card mb-3 border-<?= $is_pending ? 'primary' : 'secondary' ?>">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white"
                                    style="width:50px;height:50px;font-size:1.4rem;">
                                    <i class="bi bi-truck"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold"><?= html_escape($pengajuan->no_polisi) ?></h5>
                                    <small class="text-muted">
                                        <?= html_escape($pengajuan->jenis_kendaraan ?? '') ?> —
                                        <?= html_escape($pengajuan->merk ?? '') ?>
                                        <?= html_escape($pengajuan->tipe ?? '') ?>
                                        (<?= $pengajuan->tahun ?>)
                                    </small>
                                </div>
                            </div>
                            <div class="text-end">
                                <div><span class="badge <?= $sl[0] ?> px-3 py-2"><?= $sl[1] ?></span></div>
                                <small class="text-muted">#PU-<?= str_pad($pengajuan->id_pengajuan, 4, '0', STR_PAD_LEFT) ?></small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BANNER INFO KHUSUS inspeksi_verif -->
                <?php if ($level === 'inspeksi_verif'): ?>
                    <div class="alert alert-warning d-flex align-items-start gap-2 mb-3">
                        <i class="bi bi-wrench-adjustable-circle-fill fs-5 flex-shrink-0 mt-1"></i>
                        <div>
                            <strong>Mode Verifikasi Perbaikan</strong><br>
                            <small>
                                Unit ini telah menjalani perbaikan atas temuan inspeksi sebelumnya.
                                Periksa daftar perbaikan di bawah, lalu tentukan apakah unit
                                <strong>LULUS</strong> atau <strong>TIDAK LULUS</strong> verifikasi.
                            </small>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="row g-3">

                    <!-- INFO PENGAJUAN -->
                    <div class="col-md-7">
                        <div class="card h-100">
                            <div class="card-body pt-4">
                                <h6 class="card-title mb-3">
                                    <i class="bi bi-truck me-2 text-primary"></i>Informasi Pengajuan
                                </h6>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <small class="text-muted d-block">No. Polisi</small>
                                        <span class="badge bg-dark font-monospace fs-6">
                                            <?= html_escape($pengajuan->no_polisi) ?>
                                        </span>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Jenis Kendaraan</small>
                                        <strong><?= html_escape($pengajuan->jenis_kendaraan ?? '—') ?></strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Merk</small>
                                        <strong><?= html_escape($pengajuan->merk ?? '—') ?></strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Tipe / Model</small>
                                        <strong><?= html_escape($pengajuan->tipe ?? '—') ?></strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Tahun</small>
                                        <strong><?= $pengajuan->tahun ?: '—' ?></strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Nomor Unit</small>
                                        <strong class="font-monospace text-primary"><?= html_escape(!empty($pengajuan->nomor_unit) ? $pengajuan->nomor_unit : '—') ?></strong>
                                    </div>
                                    <?php if (!empty($pengajuan->perusahaan)): ?>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Perusahaan Unit</small>
                                            <strong><?= html_escape($pengajuan->perusahaan) ?></strong>
                                        </div>
                                    <?php endif; ?>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Nomor Rangka</small>
                                        <span class="small font-monospace">
                                            <?= html_escape($pengajuan->nomor_rangka ?: '—') ?>
                                        </span>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Nomor Mesin</small>
                                        <span class="small font-monospace">
                                            <?= html_escape($pengajuan->nomor_mesin ?: '—') ?>
                                        </span>
                                    </div>
                                    <div class="col-12">
                                        <hr class="my-1">
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Pemohon</small>
                                        <strong><?= html_escape($pengajuan->nama_pemohon) ?></strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Email</small>
                                        <strong class="small"><?= html_escape($pengajuan->email_pemohon) ?></strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Tipe Pengajuan</small>
                                        <?php $tipe_label = $pengajuan->tipe_pengajuan === 'new_commissioning'
                                            ? 'New Commissioning' : 'Recommissioning'; ?>
                                        <span class="badge bg-primary text-white"><?= $tipe_label ?></span>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Tipe Akses</small>
                                        <?php
                                        $akses_map = [
                                            'mining'      => ['bg-danger',    'bi-minecart-loaded',   'Mining Access'],
                                            'non_mining'  => ['bg-secondary', 'bi-building',          'Non Mining'],
                                            'underground' => ['bg-dark',      'bi-arrow-down-circle', 'Underground'],
                                        ];
                                        $ak = $akses_map[$pengajuan->tipe_akses] ?? ['bg-secondary', 'bi-circle', $pengajuan->tipe_akses];
                                        ?>
                                        <span class="badge <?= $ak[0] ?> text-white">
                                            <i class="bi <?= $ak[1] ?> me-1"></i><?= $ak[2] ?>
                                        </span>
                                    </div>
                                    <div class="col-12">
                                        <small class="text-muted d-block">Tujuan Penggunaan</small>
                                        <span class="small"><?= html_escape($pengajuan->tujuan ?? '—') ?></span>
                                    </div>
                                    <?php if (!empty($pengajuan->alasan_pengajuan_ulang)): ?>
                                        <div class="col-12">
                                            <small class="text-muted d-block">Alasan Pengajuan Ulang</small>
                                            <div class="alert alert-warning py-2 mb-0 small">
                                                <i class="bi bi-arrow-repeat me-1"></i>
                                                <?= html_escape($pengajuan->alasan_pengajuan_ulang) ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Tanggal Pengajuan</small>
                                        <strong><?= date('d M Y H:i', strtotime($pengajuan->tanggal_pengajuan)) ?></strong>
                                    </div>
                                    <?php if (!empty($pengajuan->tgl_acc_ktt)): ?>
                                        <div class="col-6">
                                            <small class="text-muted d-block">
                                                <i class="bi bi-calendar-check text-success me-1"></i>Tanggal ACC KTT
                                            </small>
                                            <strong class="text-success">
                                                <?= date('d M Y H:i', strtotime($pengajuan->tgl_acc_ktt)) ?>
                                            </strong>
                                        </div>
                                        <div class="col-12">
                                            <div class="alert alert-info py-2 mb-0 small">
                                                <i class="bi bi-clock me-1"></i>
                                                <strong>Estimasi Expired Stiker:</strong>
                                                <?= date('d M Y', strtotime($pengajuan->tgl_acc_ktt . ' + 6 months')) ?>
                                                (6 bulan dari ACC KTT)
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($pengajuan->is_unit_baru)): ?>
                                        <div class="col-12">
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-star-fill me-1"></i>Unit Baru
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- LAMPIRAN -->
                    <div class="col-md-5">
                        <div class="card h-100">
                            <div class="card-body pt-4">
                                <h6 class="card-title mb-3">Lampiran Dokumen</h6>
                                <?php if (empty($lampiran)): ?>
                                    <p class="text-muted small">Tidak ada lampiran (unit lama).</p>
                                <?php else: ?>
                                    <div class="row g-2">
                                        <?php
                                        $jenis_label = [
                                            'stnk'               => 'STNK',
                                            'unit_depan'         => 'Foto Depan',
                                            'unit_belakang'      => 'Foto Belakang',
                                            'unit_kiri'          => 'Foto Kiri',
                                            'unit_kanan'         => 'Foto Kanan',
                                            'maintenance_record' => 'Maintenance Record',
                                        ];
                                        foreach ($lampiran as $lamp):
                                            $label  = $jenis_label[$lamp->jenis_lampiran] ?? $lamp->jenis_lampiran;
                                            $ext    = strtolower(pathinfo($lamp->file_path, PATHINFO_EXTENSION));
                                            $is_img = in_array($ext, ['jpg', 'jpeg', 'png', 'webp']);
                                        ?>
                                            <div class="col-6">
                                                <div class="border rounded p-1 text-center" style="font-size:11px;">
                                                    <?php if ($is_img): ?>
                                                        <a href="<?= base_url($lamp->file_path) ?>" target="_blank">
                                                            <img src="<?= base_url($lamp->file_path) ?>"
                                                                class="img-fluid rounded mb-1"
                                                                style="max-height:80px;object-fit:cover;width:100%;"
                                                                alt="<?= $label ?>">
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="<?= base_url($lamp->file_path) ?>" target="_blank" class="d-block py-2">
                                                            <i class="bi bi-file-earmark-pdf text-danger fs-3 d-block"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <span class="text-muted"><?= $label ?></span>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- ================================================ -->
                    <!-- DAFTAR PERBAIKAN — tampil menonjol di inspeksi_verif -->
                    <!-- ================================================ -->
                    <?php if (!empty($perbaikan_list)): ?>
                        <div class="col-12">
                            <?php
                            $perbaikan_all_done = true;
                            foreach ($perbaikan_list as $pb) {
                                if (empty($pb->status) || $pb->status !== 'selesai') {
                                    $perbaikan_all_done = false;
                                    break;
                                }
                            }
                            ?>
                            <div class="card border-<?= $level === 'inspeksi_verif' ? 'warning' : 'secondary' ?>">
                                <div class="card-header py-2 bg-<?= $level === 'inspeksi_verif' ? 'warning' : 'secondary' ?> <?= $level === 'inspeksi_verif' ? 'text-dark' : 'text-white' ?> d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <i class="bi bi-wrench me-2"></i>Daftar Perbaikan Unit
                                        <span class="badge bg-dark ms-2"><?= count($perbaikan_list) ?> item</span>
                                    </h6>
                                    <?php if ($level === 'inspeksi_verif'): ?>
                                        <span class="badge <?= $perbaikan_all_done ? 'bg-success' : 'bg-danger' ?>">
                                            <i class="bi bi-<?= $perbaikan_all_done ? 'check-circle' : 'exclamation-circle' ?> me-1"></i>
                                            <?= $perbaikan_all_done ? 'Semua Selesai' : 'Ada yang Belum Selesai' ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Item Perbaikan</th>
                                                    <th>Keterangan</th>
                                                    <th>Status</th>
                                                    <th>Verifikator</th>
                                                    <th>Foto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($perbaikan_list as $idx => $pb): ?>
                                                    <tr>
                                                        <td><?= $idx + 1 ?></td>
                                                        <td>
                                                            <strong><?= html_escape($pb->nama_item ?? $pb->deskripsi ?? '—') ?></strong>
                                                        </td>
                                                        <td class="small text-muted">
                                                            <?= html_escape($pb->keterangan ?? '—') ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $st_pb = $pb->status ?? 'pending';
                                                            $st_class = $st_pb === 'selesai' ? 'bg-success' : ($st_pb === 'proses' ? 'bg-warning text-dark' : 'bg-secondary');
                                                            $st_label = $st_pb === 'selesai' ? 'Selesai' : ($st_pb === 'proses' ? 'Dalam Proses' : 'Belum');
                                                            ?>
                                                            <span class="badge <?= $st_class ?> text-white" style="font-size:10px;">
                                                                <?= $st_label ?>
                                                            </span>
                                                        </td>
                                                        <td class="small">
                                                            <?= html_escape($pb->nama_verifikator ?? '—') ?>
                                                        </td>
                                                        <td>
                                                            <?php if (!empty($pb->lampiran)): ?>
                                                                <div class="d-flex gap-1 flex-wrap">
                                                                    <?php foreach ($pb->lampiran as $lp): ?>
                                                                        <?php
                                                                        $lp_ext    = strtolower(pathinfo($lp->file_path, PATHINFO_EXTENSION));
                                                                        $lp_is_img = in_array($lp_ext, ['jpg', 'jpeg', 'png', 'webp']);
                                                                        ?>
                                                                        <?php if ($lp_is_img): ?>
                                                                            <a href="<?= base_url($lp->file_path) ?>" target="_blank">
                                                                                <img src="<?= base_url($lp->file_path) ?>"
                                                                                    style="width:40px;height:40px;object-fit:cover;border-radius:4px;"
                                                                                    alt="foto perbaikan">
                                                                            </a>
                                                                        <?php else: ?>
                                                                            <a href="<?= base_url($lp->file_path) ?>" target="_blank"
                                                                                class="btn btn-sm btn-outline-secondary py-0 px-1">
                                                                                <i class="bi bi-file-earmark"></i>
                                                                            </a>
                                                                        <?php endif; ?>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            <?php else: ?>
                                                                <span class="text-muted small">—</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <?php if ($level === 'inspeksi_verif' && !$perbaikan_all_done): ?>
                                    <div class="card-footer py-2">
                                        <div class="alert alert-danger mb-0 py-2 small">
                                            <i class="bi bi-exclamation-triangle me-1"></i>
                                            <strong>Perhatian:</strong> Masih ada item perbaikan yang belum berstatus "Selesai".
                                            Pastikan seluruh perbaikan sudah diverifikasi sebelum memberikan keputusan.
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php elseif ($level === 'inspeksi_verif'): ?>
                        <div class="col-12">
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Belum ada data perbaikan tercatat untuk pengajuan ini.
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- HASIL INSPEKSI -->
                    <div class="col-12">
                        <?php
                        $inspeksi_ada   = !empty($uji);
                        $inspeksi_lulus = $inspeksi_ada && !empty($summary) && $summary['lulus'];
                        $inspeksi_gagal = $inspeksi_ada && !empty($summary) && !$summary['lulus'];
                        $header_class   = $inspeksi_lulus ? 'bg-success' : ($inspeksi_gagal ? 'bg-danger' : 'bg-secondary');
                        $border_class   = $inspeksi_lulus ? 'border-success' : ($inspeksi_gagal ? 'border-danger' : 'border-secondary');
                        ?>
                        <div class="card <?= $border_class ?>">
                            <div class="card-header py-2 <?= $header_class ?> text-white d-flex align-items-center justify-content-between">
                                <h6 class="mb-0">
                                    <i class="bi bi-clipboard2-check me-2"></i>Hasil Inspeksi Mekanik
                                </h6>
                                <?php if (!$inspeksi_ada): ?>
                                    <span class="badge bg-light text-dark" style="font-size:10px;">Belum ada data inspeksi</span>
                                <?php elseif ($inspeksi_lulus): ?>
                                    <span class="badge bg-light text-success fw-bold" style="font-size:11px;">
                                        <i class="bi bi-check-circle-fill me-1"></i>LULUS
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-light text-danger fw-bold" style="font-size:11px;">
                                        <i class="bi bi-x-circle-fill me-1"></i>TIDAK LULUS
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body pt-3">
                                <?php if (!$inspeksi_ada): ?>
                                    <div class="text-center py-3 text-muted">
                                        <i class="bi bi-clipboard-x fs-2 d-block mb-2 opacity-50"></i>
                                        <small>Mekanik belum mengisi form inspeksi.</small>
                                    </div>
                                <?php else: ?>
                                    <div class="row g-2 mb-3">
                                        <div class="col-6 col-sm-3">
                                            <div class="border rounded p-2 text-center <?= $inspeksi_lulus ? 'bg-success bg-opacity-10 border-success' : 'bg-danger bg-opacity-10 border-danger' ?>">
                                                <div class="fs-4 fw-bold <?= $inspeksi_lulus ? 'text-success' : 'text-danger' ?>">
                                                    <i class="bi bi-<?= $inspeksi_lulus ? 'check-circle-fill' : 'x-circle-fill' ?>"></i>
                                                </div>
                                                <div class="fw-semibold small"><?= $inspeksi_lulus ? 'LULUS' : 'TIDAK LULUS' ?></div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-sm-3">
                                            <div class="border rounded p-2 text-center">
                                                <div class="fs-4 fw-bold text-primary"><?= $summary['total'] ?? 0 ?></div>
                                                <div class="text-muted small">Total Item</div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-sm-3">
                                            <div class="border rounded p-2 text-center bg-success bg-opacity-10">
                                                <div class="fs-4 fw-bold text-success"><?= $summary['yes'] ?? 0 ?></div>
                                                <div class="text-muted small">OK / Yes</div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-sm-3">
                                            <div class="border rounded p-2 text-center bg-danger bg-opacity-10">
                                                <div class="fs-4 fw-bold text-danger"><?= $summary['no'] ?? 0 ?></div>
                                                <div class="text-muted small">Tidak OK / No</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-2 mb-3">
                                        <div class="col-md-4">
                                            <small class="text-muted d-block">Mekanik</small>
                                            <strong><?= html_escape($uji->nama_mekanik ?? '—') ?></strong>
                                        </div>
                                        <div class="col-md-4">
                                            <small class="text-muted d-block">Tanggal Inspeksi</small>
                                            <strong>
                                                <?= !empty($uji->updated_at)
                                                    ? date('d M Y H:i', strtotime($uji->updated_at))
                                                    : (!empty($uji->created_at) ? date('d M Y H:i', strtotime($uji->created_at)) : '—') ?>
                                            </strong>
                                        </div>
                                        <div class="col-md-4">
                                            <small class="text-muted d-block">Catatan Mekanik</small>
                                            <span><?= html_escape($uji->catatan_umum ?? '—') ?></span>
                                        </div>
                                    </div>

                                    <?php if (!empty($summary['items_no'])): ?>
                                        <div class="alert alert-danger py-2 mb-2">
                                            <strong><i class="bi bi-exclamation-triangle me-1"></i>Item Tidak Memenuhi Syarat:</strong>
                                            <ul class="mb-0 mt-1 ps-3">
                                                <?php foreach ($summary['items_no'] as $item): ?>
                                                    <li class="small">
                                                        <span class="badge bg-<?= $item->kategori === 'CRITICAL' ? 'danger' : 'warning text-dark' ?> me-1"
                                                            style="font-size:9px;"><?= $item->kategori ?></span>
                                                        <?= html_escape($item->kriteria) ?>
                                                        <?php if (!empty($item->keterangan)): ?>
                                                            — <em class="text-muted"><?= html_escape($item->keterangan) ?></em>
                                                        <?php endif; ?>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>

                                    <div class="d-flex gap-2 flex-wrap mt-3">
                                        <a href="<?= site_url('checklist/detail/' . $uji->id_uji) ?>"
                                            class="btn btn-sm btn-outline-info" target="_blank">
                                            <i class="bi bi-eye me-1"></i>Lihat Detail Checklist
                                        </a>
                                        <?php if (!empty($history_versions) || !empty($perbaikan_list)): ?>
                                            <a href="#sectionHistoryInspeksi"
                                                class="btn btn-sm btn-outline-secondary"
                                                onclick="var el=document.getElementById('sectionHistoryInspeksi');
                                                    if(el){el.scrollIntoView({behavior:'smooth',block:'start'});}
                                                    return false;">
                                                <i class="bi bi-clock-history me-1"></i>Riwayat
                                                <?php if (!empty($history_versions)): ?>
                                                    <span class="badge bg-secondary ms-1"><?= count($history_versions) ?></span>
                                                <?php endif; ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- RIWAYAT APPROVAL -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body pt-4">
                                <h6 class="card-title mb-3">Riwayat Approval</h6>
                                <?php if (empty($riwayat)): ?>
                                    <p class="text-muted small">Belum ada riwayat.</p>
                                <?php else: ?>
                                    <div class="timeline-approval">
                                        <?php
                                        $level_label_map = [
                                            'dept_manager'    => 'Dept Manager',
                                            'admin_ohs'       => 'Admin OHS (Verifikasi Dokumen)',
                                            'admin_ohs_hasil' => 'Admin OHS (Review Hasil)',
                                            'ohs_supt'        => 'OHS Superintendent',
                                            'ktt'             => 'KTT',
                                            'release_stiker'  => 'Penerbitan Stiker',
                                            'inspeksi_verif'  => 'Inspektor (Verifikasi Perbaikan)',
                                            'manager'         => 'Manager',
                                            'admin'           => 'Admin OHS',
                                            'ohs'             => 'OHS Superintendent',
                                        ];
                                        foreach ($riwayat as $r):
                                            $icon = $r->status === 'approved'
                                                ? 'bi-check-circle-fill text-success'
                                                : ($r->status === 'rejected' ? 'bi-x-circle-fill text-danger' : 'bi-clock-fill text-warning');
                                        ?>
                                            <div class="d-flex gap-3 mb-3">
                                                <div class="pt-1"><i class="bi <?= $icon ?> fs-5"></i></div>
                                                <div class="flex-grow-1">
                                                    <div class="fw-semibold small">
                                                        <?= $level_label_map[$r->level_approval] ?? $r->level_approval ?>
                                                        —
                                                        <?php
                                                        if ($r->status === 'approved')      echo '<span class="text-success">Disetujui</span>';
                                                        elseif ($r->status === 'rejected')  echo '<span class="text-danger">Ditolak</span>';
                                                        else                                 echo '<span class="text-warning">Menunggu</span>';
                                                        ?>
                                                    </div>
                                                    <?php if ($r->nama_approver): ?>
                                                        <small class="text-muted">oleh <?= html_escape($r->nama_approver) ?></small>
                                                    <?php endif; ?>
                                                    <?php if ($r->catatan): ?>
                                                        <div class="mt-1 p-2 bg-light rounded small"><?= html_escape($r->catatan) ?></div>
                                                    <?php endif; ?>
                                                    <?php if ($r->created_at): ?>
                                                        <small class="text-muted"><?= date('d M Y H:i', strtotime($r->created_at)) ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                </div><!-- end row -->

                <!-- History inspeksi (versi/perbaikan) -->
                <?php if (!empty($history_versions) || !empty($perbaikan_list)): ?>
                    <div class="col-12 mt-3" id="sectionHistoryInspeksi">
                        <?php $this->load->view('checklist/history', [
                            'history_versions' => $history_versions ?? [],
                            'history_detail'   => $history_detail   ?? [],
                            'perbaikan_list'   => $perbaikan_list   ?? [],
                        ]); ?>
                    </div>
                <?php endif; ?>

                <!-- ================================================ -->
                <!-- TOMBOL AKSI -->
                <!-- ================================================ -->
                <?php if ($is_pending): ?>
                    <div class="card mt-3 border-primary">
                        <div class="card-body py-3">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <?php if ($level === 'inspeksi_verif'): ?>
                                        <strong><i class="bi bi-patch-check me-1 text-primary"></i>Keputusan Verifikasi:</strong>
                                        <span class="text-muted small ms-2">Nyatakan unit lulus atau tidak lulus perbaikan</span>
                                    <?php else: ?>
                                        <strong>Tindakan Anda:</strong>
                                        <span class="text-muted small ms-2">Setujui atau tolak pengajuan ini</span>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="<?= $back_url ?>" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-arrow-left me-1"></i>Kembali
                                    </a>
                                    <?php if ($is_pending && !is_null($cfg['status_reject'] ?? null) && $boleh_approve): ?>
                                        <button class="btn btn-danger btn-sm text-white" id="btnRejectDetail"
                                            data-id="<?= $pengajuan->id_pengajuan ?>"
                                            data-polisi="<?= html_escape($pengajuan->no_polisi) ?>">
                                            <i class="bi bi-<?= $level === 'inspeksi_verif' ? 'x-circle' : 'x-circle' ?> me-1"></i>
                                            <?= $level === 'inspeksi_verif' ? 'Tidak Lulus' : 'Tolak' ?>
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($boleh_approve): ?>
                                        <button class="btn btn-success btn-sm text-white" id="btnApproveDetail"
                                            data-id="<?= $pengajuan->id_pengajuan ?>"
                                            data-polisi="<?= html_escape($pengajuan->no_polisi) ?>">
                                            <i class="bi bi-check-circle me-1"></i>
                                            <?= $level === 'inspeksi_verif' ? 'Lulus Verifikasi' : 'Setujui' ?>
                                        </button>
                                    <?php else: ?>
                                        <?php if ($level === 'ktt' && isset($has_approved) && $has_approved): ?>
                                            <span class="badge bg-success p-2 fs-6"><i class="bi bi-check-circle me-1"></i>Anda Sudah Approve Pengajuan Ini</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Jika sudah diproses, tampilkan tombol Cabut Stiker untuk KTT jika status stiker_keluar -->
                    <div class="mt-3 d-flex gap-2 flex-wrap">
                        <a href="<?= $back_url ?>" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                        <?php if ($pengajuan->status === 'stiker_keluar' && $is_ktt_or_admin): ?>
                            <button class="btn btn-sm btn-outline-dark btn-cabut-stiker"
                                data-id="<?= $pengajuan->id_pengajuan ?>"
                                data-polisi="<?= html_escape($pengajuan->no_polisi) ?>">
                                <i class="bi bi-scissors me-1"></i>Cabut Stiker
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </section>
</main>


<!-- Modal Reject / Tidak Lulus -->
<div class="modal fade" id="modalReject" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white">
                    <i class="bi bi-x-circle me-2"></i>
                    <?= $level === 'inspeksi_verif' ? 'Tidak Lulus Verifikasi Perbaikan' : 'Tolak Pengajuan' ?>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Pengajuan <strong><?= html_escape($pengajuan->no_polisi) ?></strong>
                    <?= $level === 'inspeksi_verif' ? 'akan dinyatakan TIDAK LULUS verifikasi.' : 'akan ditolak.' ?>
                </p>
                <?php if (!empty($cfg['reject_label'])): ?>
                    <p class="text-muted small mb-2"><?= html_escape($cfg['reject_label']) ?></p>
                <?php endif; ?>
                <label class="form-label fw-semibold">
                    <?= $level === 'inspeksi_verif' ? 'Catatan Temuan Perbaikan' : 'Alasan Penolakan' ?>
                    <span class="text-danger">*</span>
                </label>
                <textarea class="form-control" id="rejectCatatan" rows="4"
                    placeholder="<?= $level === 'inspeksi_verif'
                                        ? 'Tuliskan temuan yang masih perlu diperbaiki...'
                                        : 'Tuliskan alasan penolakan secara jelas...' ?>"
                    maxlength="500"></textarea>
                <small class="text-muted">Catatan ini akan tercatat dalam riwayat approval.</small>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-danger btn-sm text-white" id="btnKonfirmasiReject">
                    <i class="bi bi-x-circle me-1"></i>
                    <?= $level === 'inspeksi_verif' ? 'Konfirmasi Tidak Lulus' : 'Tolak Pengajuan' ?>
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
        var backUrl = '<?= $back_url ?>';
        var modalReject = new bootstrap.Modal(document.getElementById('modalReject'));
        var id_pengajuan = <?= $pengajuan->id_pengajuan ?>;

        // ── Approve / Lulus ──────────────────────────────────────────
        $('#btnApproveDetail').on('click', function() {
            var title = level === 'inspeksi_verif' ? 'Konfirmasi Lulus Verifikasi?' : 'Setujui Pengajuan?';
            var html = level === 'inspeksi_verif' ?
                'Unit <strong><?= html_escape($pengajuan->no_polisi) ?></strong> dinyatakan <strong>LULUS</strong> verifikasi perbaikan.<br>Pengajuan akan diteruskan ke <strong>OHS Superintendent</strong>.' :
                'Kendaraan <strong><?= html_escape($pengajuan->no_polisi) ?></strong> akan diteruskan ke tahap berikutnya.';
            var btnText = level === 'inspeksi_verif' ?
                '<i class="bi bi-check-circle me-1"></i>Ya, Lulus' :
                '<i class="bi bi-check-lg me-1"></i>Ya, Setujui';

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
                if (r.isConfirmed) proses('approve', '');
            });
        });

        // ── Reject / Tidak Lulus ─────────────────────────────────────
        $('#btnRejectDetail').on('click', function() {
            $('#rejectCatatan').val('');
            modalReject.show();
        });

        $('#btnKonfirmasiReject').on('click', function() {
            var catatan = $('#rejectCatatan').val().trim();
            if (!catatan) {
                toastr.warning(level === 'inspeksi_verif' ?
                    'Catatan temuan wajib diisi.' :
                    'Alasan penolakan wajib diisi.');
                return;
            }
            modalReject.hide();
            proses('reject', catatan);
        });

        // ── Cabut Stiker (hanya tampil jika KTT/Admin) ───────────────
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
                        Swal.showValidationMessage('Alasan wajib diisi!');
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
                                window.location.href = backUrl;
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
        function proses(aksi, catatan) {
            NProgress.start();
            var post = {
                level: level,
                id_pengajuan: id_pengajuan,
                aksi: aksi,
                catatan: catatan
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
                                html: res.message + '<br><small class="text-muted">Anda akan diarahkan ke form jadwal uji.</small>',
                                icon: 'success',
                                confirmButtonColor: '#4154f1',
                                confirmButtonText: 'Buat Jadwal',
                            }).then(function() {
                                window.location.href = res.redirect_jadwal;
                            });
                            return;
                        }
                        Swal.fire({
                            title: aksi === 'approve' ?
                                (level === 'inspeksi_verif' ? 'Lulus Verifikasi!' : 'Disetujui!') : (level === 'inspeksi_verif' ? 'Tidak Lulus' : 'Ditolak'),
                            html: res.message,
                            icon: aksi === 'approve' ? 'success' : 'warning',
                            confirmButtonColor: '#4154f1',
                        }).then(function() {
                            window.location.href = res.redirect || backUrl;
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
                    toastr.error('Terjadi kesalahan server.');
                }
            });
        }
    });
</script>