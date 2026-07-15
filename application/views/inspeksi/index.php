<main id="main" class="main">

    <div class="pagetitle">
        <h1>Form Inspeksi</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                <li class="breadcrumb-item active">Form Inspeksi</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body pt-4">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">
                                Kendaraan Siap Diinspeksi
                                <?php if (!empty($list_inspeksi)): ?>
                                    <span class="badge bg-primary ms-2"><?= count($list_inspeksi) ?></span>
                                <?php endif; ?>
                            </h5>
                        </div>

                        <?php if (empty($list_inspeksi)): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 opacity-40"></i>
                                <p class="mb-1 fw-semibold">Tidak ada kendaraan yang dijadwalkan untuk inspeksi.</p>
                                <small>Kendaraan akan muncul di sini setelah Admin OHS membuat jadwal.</small>
                            </div>
                        <?php else: ?>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>No. Polisi</th>
                                            <th>Jenis / Merk</th>
                                            <th>Pemohon</th>
                                            <th>Jadwal Inspeksi</th>
                                            <th>Lokasi</th>
                                            <th>Mekanik</th>
                                            <th>Inspektor</th>
                                            <th>Status Form</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($list_inspeksi as $i => $row): ?>
                                            <?php
                                            // Tentukan apakah jadwal sudah lewat
                                            $sudah_lewat = $row->tanggal_uji && strtotime($row->tanggal_uji) < time();
                                            $belum_waktunya = $row->tanggal_uji && strtotime($row->tanggal_uji) > time();
                                            ?>
                                            <tr>
                                                <td><?= $i + 1 ?></td>
                                                <td>
                                                    <strong class="text-primary"><?= html_escape($row->no_polisi) ?></strong>
                                                    <br><small class="text-muted">#PU-<?= str_pad($row->id_pengajuan, 4, '0', STR_PAD_LEFT) ?></small>
                                                </td>
                                                <td>
                                                    <?= html_escape($row->jenis_kendaraan) ?>
                                                    <br><small class="text-muted"><?= html_escape($row->merk) ?> <?= html_escape($row->tipe) ?> (<?= $row->tahun ?>)</small>
                                                </td>
                                                <td><?= html_escape($row->nama_pemohon) ?></td>
                                                <td>
                                                    <?php if ($row->tanggal_uji): ?>
                                                        <strong class="<?= $sudah_lewat ? 'text-success' : ($belum_waktunya ? 'text-primary' : '') ?>">
                                                            <?= date('d M Y', strtotime($row->tanggal_uji)) ?>
                                                        </strong>
                                                        <br><small class="text-muted"><?= date('H:i', strtotime($row->tanggal_uji)) ?> WIB</small>
                                                        <?php if ($belum_waktunya): ?>
                                                            <br><small class="text-info">
                                                                <i class="bi bi-clock me-1"></i>
                                                                <?= ceil((strtotime($row->tanggal_uji) - time()) / 86400) ?> hari lagi
                                                            </small>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="text-warning small"><i class="bi bi-exclamation-triangle me-1"></i>Belum dijadwalkan</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($row->lokasi): ?>
                                                        <i class="bi bi-geo-alt text-muted me-1"></i><?= html_escape($row->lokasi) ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">—</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($row->nama_mekanik_master)): ?>
                                                        <i class="bi bi-wrench text-warning me-1"></i><?= html_escape($row->nama_mekanik_master) ?>
                                                        <?php if (!empty($row->perusahaan_mekanik_master)): ?>
                                                            <br><small class="text-muted"><?= html_escape($row->perusahaan_mekanik_master) ?></small>
                                                        <?php endif; ?>
                                                    <?php elseif (!empty($row->nama_mekanik)): ?>
                                                        <i class="bi bi-person-gear text-muted me-1"></i><?= html_escape($row->nama_mekanik) ?>
                                                    <?php else: ?>
                                                        <span class="text-warning small"><i class="bi bi-exclamation-triangle me-1"></i>Belum ditugaskan</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($row->nama_inspektor_user)): ?>
                                                        <i class="bi bi-person-badge text-primary me-1"></i><?= html_escape($row->nama_inspektor_user) ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">—</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($row->id_uji): ?>
                                                        <span class="badge bg-warning text-dark">
                                                            <i class="bi bi-pencil-square me-1"></i>Draft Tersimpan
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-info text-dark">
                                                            <i class="bi bi-clipboard me-1"></i>Belum Diisi
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($row->tanggal_uji): ?>
                                                        <a href="<?= site_url('checklist/form/' . $row->id_pengajuan) ?>"
                                                            class="btn btn-sm <?= $row->id_uji ? 'btn-warning' : 'btn-primary' ?>">
                                                            <i class="bi bi-clipboard2-check me-1"></i>
                                                            <?= $row->id_uji ? 'Lanjutkan' : 'Mulai Inspeksi' ?>
                                                        </a>
                                                    <?php else: ?>
                                                        <button class="btn btn-sm btn-outline-secondary" disabled title="Tunggu jadwal dari Admin OHS">
                                                            <i class="bi bi-hourglass me-1"></i>Menunggu Jadwal
                                                        </button>
                                                    <?php endif; ?>
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