<?php

/**
 * View partial: checklist/history.php
 *
 * Tampilkan:
 *   1. Riwayat semua versi checklist inspeksi (dari uji_checklist_history)
 *   2. Riwayat perbaikan unit (dari perbaikan_unit + perbaikan_lampiran)
 *
 * Variabel yang dibutuhkan (pass dari controller atau $data):
 *   $history_versions   — Checklist_model::get_history_versions($id_uji)
 *   $history_detail     — Checklist_model::get_checklist_history($id_uji)   [versi => [rows]]
 *   $perbaikan_list     — (opsional) array perbaikan_unit rows, tiap row punya ->lampiran[]
 *   $id_pengajuan       — (opsional) int, untuk query perbaikan jika $perbaikan_list tidak ada
 *
 * Embed di detail.php / approval/detail.php:
 *   <?php $this->load->view('checklist/history', [
 *       'history_versions' => $history_versions,
 *       'history_detail'   => $history_detail,
 *       'perbaikan_list'   => $perbaikan_list ?? [],
 *   ]); ?>
 */
defined('BASEPATH') or exit('No direct script access allowed');

$_has_history   = !empty($history_versions);
$_has_perbaikan = !empty($perbaikan_list);

if (!$_has_history && !$_has_perbaikan): ?>
    <!-- Tidak ada data → tidak render apapun -->
<?php return;
endif; ?>

<!-- ════════════════════════════════════════════════════════════════════
     SECTION: RIWAYAT INSPEKSI & PERBAIKAN
     ════════════════════════════════════════════════════════════════════ -->
<div id="sectionHistoryInspeksi" class="mt-4">

    <!-- ── HEADER SECTION ───────────────────────────────────────────── -->
    <div class="d-flex align-items-center gap-3 mb-3">
        <div class="d-flex align-items-center justify-content-center rounded-circle bg-secondary text-white flex-shrink-0"
            style="width:40px;height:40px;font-size:1.1rem;">
            <i class="bi bi-clock-history"></i>
        </div>
        <div>
            <h5 class="mb-0 fw-bold">Riwayat Inspeksi &amp; Perbaikan</h5>
            <small class="text-muted">
                <?php
                $parts = [];
                if ($_has_history)   $parts[] = count($history_versions) . ' snapshot inspeksi';
                if ($_has_perbaikan) $parts[] = count($perbaikan_list) . ' data perbaikan';
                echo implode(' · ', $parts);
                ?>
            </small>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════════════
         BAGIAN 1 — RIWAYAT CHECKLIST (dari uji_checklist_history)
         ════════════════════════════════════════════════════════════════ -->
    <?php if ($_has_history): ?>
        <div class="card border-secondary mb-3">
            <div class="card-header bg-secondary bg-opacity-10 border-secondary py-2
                    d-flex align-items-center justify-content-between">
                <span class="fw-bold text-secondary">
                    <i class="bi bi-clipboard2-pulse me-2"></i>Snapshot Inspeksi Sebelumnya
                </span>
                <span class="badge bg-secondary"><?= count($history_versions) ?> versi</span>
            </div>
            <div class="card-body p-0">

                <!-- Nav Tabs -->
                <ul class="nav nav-tabs px-3 pt-2 border-bottom-0" id="histTabs" role="tablist">
                    <?php foreach ($history_versions as $idx => $ver): ?>
                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link <?= $idx === 0 ? 'active' : '' ?> small
                                   d-flex align-items-center gap-1 py-2 px-3"
                                id="ht-tab-<?= $ver->versi ?>"
                                data-bs-toggle="tab"
                                data-bs-target="#ht-pane-<?= $ver->versi ?>"
                                type="button" role="tab">

                                <?php if ($ver->hasil_uji === 'lulus'): ?>
                                    <i class="bi bi-check-circle-fill text-success" title="Lulus"></i>
                                <?php elseif ($ver->hasil_uji === 'tidak_lulus'): ?>
                                    <i class="bi bi-x-circle-fill text-danger" title="Tidak Lulus"></i>
                                <?php else: ?>
                                    <i class="bi bi-dash-circle text-secondary"></i>
                                <?php endif; ?>

                                Inspeksi #<?= $ver->versi ?>

                                <?php if ($ver->total_no > 0): ?>
                                    <span class="badge bg-danger ms-1" style="font-size:9px;">
                                        <?= $ver->total_no ?> NO
                                    </span>
                                <?php endif; ?>
                            </button>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <!-- Tab Panes -->
                <div class="tab-content px-3 pb-3 pt-3" id="histTabContent">
                    <?php foreach ($history_versions as $idx => $ver): ?>
                        <div class="tab-pane fade <?= $idx === 0 ? 'show active' : '' ?>"
                            id="ht-pane-<?= $ver->versi ?>"
                            role="tabpanel">

                            <!-- Meta baris -->
                            <div class="row g-2 mb-3 align-items-center">
                                <div class="col-auto">
                                    <small class="text-muted">
                                        <i class="bi bi-calendar-check me-1"></i>
                                        <strong><?= date('d M Y H:i', strtotime($ver->snapshot_at)) ?></strong>
                                    </small>
                                </div>
                                <?php if (!empty($ver->nama_inspektor)): ?>
                                    <div class="col-auto">
                                        <small class="text-muted">
                                            <i class="bi bi-person me-1"></i>
                                            <?= html_escape($ver->nama_inspektor) ?>
                                            <?php if (!empty($ver->perusahaan_inspektor)): ?>
                                                <span class="text-muted">(<?= html_escape($ver->perusahaan_inspektor) ?>)</span>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                <?php endif; ?>
                                <div class="col-auto ms-auto d-flex gap-1 flex-wrap">
                                    <?php if ($ver->hasil_uji === 'lulus'): ?>
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i>LULUS
                                        </span>
                                    <?php elseif ($ver->hasil_uji === 'tidak_lulus'): ?>
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle me-1"></i>TIDAK LULUS
                                        </span>
                                    <?php endif; ?>
                                    <span class="badge bg-success bg-opacity-75">YES: <?= $ver->total_yes ?></span>
                                    <span class="badge bg-danger bg-opacity-75">NO: <?= $ver->total_no ?></span>
                                    <span class="badge bg-secondary bg-opacity-75">N/A: <?= $ver->total_na ?></span>
                                </div>
                            </div>

                            <!-- Catatan temuan -->
                            <?php if (!empty($ver->catatan_temuan)): ?>
                                <div class="alert alert-secondary py-2 mb-3 small">
                                    <i class="bi bi-chat-text me-1"></i>
                                    <strong>Catatan Temuan:</strong>
                                    <?= html_escape($ver->catatan_temuan) ?>
                                </div>
                            <?php endif; ?>

                            <!-- Tabel detail checklist versi ini -->
                            <?php
                            $rows_versi = $history_detail[$ver->versi] ?? [];
                            $gh = ['CRITICAL' => [], 'GENERAL' => []];
                            foreach ($rows_versi as $r) {
                                $gh[$r->kategori][] = $r;
                            }
                            ?>

                            <?php if (empty($rows_versi)): ?>
                                <p class="text-muted small mb-0">
                                    <i class="bi bi-info-circle me-1"></i>Data detail tidak tersedia.
                                </p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered table-hover align-middle mb-0"
                                        style="font-size:0.8rem;">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="70" class="text-center">Kat.</th>
                                                <th width="40" class="text-center">No.</th>
                                                <th>Kriteria</th>
                                                <th width="60" class="text-center">Hasil</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach (['CRITICAL', 'GENERAL'] as $kat): ?>
                                                <?php if (empty($gh[$kat])) continue; ?>
                                                <tr class="<?= $kat === 'CRITICAL' ? 'table-danger' : 'table-warning' ?>
                                                        bg-opacity-25">
                                                    <td colspan="5" class="fw-bold py-1 text-center"
                                                        style="font-size:0.72rem;letter-spacing:.05em;">
                                                        <?= $kat === 'CRITICAL'
                                                            ? '★ CRITICAL ITEMS'
                                                            : 'GENERAL REQUIREMENTS' ?>
                                                    </td>
                                                </tr>
                                                <?php foreach ($gh[$kat] as $item): ?>
                                                    <tr class="<?= $item->hasil === 'no'
                                                                    ? 'table-danger'
                                                                    : ($item->hasil === 'na' ? 'table-light text-muted' : '') ?>">
                                                        <td class="text-center">
                                                            <span class="badge bg-<?= $kat === 'CRITICAL'
                                                                                        ? 'danger'
                                                                                        : 'warning text-dark' ?>"
                                                                style="font-size:8px;">
                                                                <?= $kat ?>
                                                            </span>
                                                        </td>
                                                        <td class="text-center fw-bold">
                                                            <?= html_escape($item->no_urut) ?>
                                                        </td>
                                                        <td><?= html_escape($item->kriteria) ?></td>
                                                        <td class="text-center">
                                                            <?php if ($item->hasil === 'yes'): ?>
                                                                <span class="badge bg-success px-2">YES</span>
                                                            <?php elseif ($item->hasil === 'no'): ?>
                                                                <span class="badge bg-danger px-2">NO</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-secondary px-2">N/A</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="text-muted" style="font-size:0.75rem;">
                                                            <?= html_escape($item->keterangan ?: '—') ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>

                        </div><!-- /tab-pane -->
                    <?php endforeach; ?>
                </div><!-- /tab-content -->

            </div><!-- /card-body -->
        </div><!-- /card -->
    <?php endif; // end history 
    ?>


    <!-- ════════════════════════════════════════════════════════════════
         BAGIAN 2 — RIWAYAT PERBAIKAN UNIT
         ════════════════════════════════════════════════════════════════ -->
    <?php if ($_has_perbaikan): ?>
        <div class="card border-warning">
            <div class="card-header bg-warning bg-opacity-10 border-warning py-2
                    d-flex align-items-center justify-content-between">
                <span class="fw-bold text-warning">
                    <i class="bi bi-tools me-2"></i>Riwayat Perbaikan Unit
                </span>
                <span class="badge bg-warning text-dark"><?= count($perbaikan_list) ?> entri</span>
            </div>
            <div class="card-body p-0">

                <?php foreach ($perbaikan_list as $pb_idx => $pb): ?>
                    <?php
                    $pb_status_map = [
                        'menunggu'    => ['bg-secondary', 'Menunggu'],
                        'selesai'     => ['bg-info text-white', 'Selesai'],
                        'diverifikasi' => ['bg-success text-white', 'Diverifikasi ✓'],
                    ];
                    $pb_sc = $pb_status_map[$pb->status] ?? ['bg-light text-dark', $pb->status];

                    // Hitung sisa hari deadline
                    $deadline_ts  = strtotime($pb->tgl_max_perbaikan ?? 'now');
                    $selesai_ts   = !empty($pb->tgl_selesai) ? strtotime($pb->tgl_selesai) : null;
                    $ref_ts       = $selesai_ts ?? time();
                    $sisa         = (int) ceil(($deadline_ts - $ref_ts) / 86400);
                    $tepat_waktu  = $selesai_ts ? ($selesai_ts <= $deadline_ts) : null;
                    ?>

                    <div class="p-3 <?= $pb_idx > 0 ? 'border-top' : '' ?>">

                        <!-- Baris atas: nomor + status + tanggal -->
                        <div class="d-flex align-items-start justify-content-between gap-2 mb-2 flex-wrap">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center
                                        text-white fw-bold flex-shrink-0"
                                    style="width:28px;height:28px;font-size:.8rem;">
                                    <?= $pb_idx + 1 ?>
                                </div>
                                <div>
                                    <span class="fw-semibold small">Perbaikan #<?= $pb->id_perbaikan ?></span>
                                    <div class="d-flex gap-1 flex-wrap mt-1">
                                        <span class="badge <?= $pb_sc[0] ?> badge-sm" style="font-size:10px;">
                                            <?= $pb_sc[1] ?>
                                        </span>
                                        <?php if ($tepat_waktu === true): ?>
                                            <span class="badge bg-success text-white" style="font-size:10px;">
                                                <i class="bi bi-clock me-1"></i>Tepat Waktu
                                            </span>
                                        <?php elseif ($tepat_waktu === false): ?>
                                            <span class="badge bg-danger text-white" style="font-size:10px;">
                                                <i class="bi bi-alarm me-1"></i>Terlambat <?= abs($sisa) ?> hari
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end small text-muted">
                                <?php if (!empty($pb->tgl_max_perbaikan)): ?>
                                    <div>
                                        <i class="bi bi-calendar-x text-danger me-1"></i>
                                        Deadline: <strong><?= date('d M Y', strtotime($pb->tgl_max_perbaikan)) ?></strong>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($pb->tgl_selesai)): ?>
                                    <div>
                                        <i class="bi bi-calendar-check text-success me-1"></i>
                                        Selesai: <strong><?= date('d M Y', strtotime($pb->tgl_selesai)) ?></strong>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Catatan perbaikan -->
                        <?php if (!empty($pb->catatan_perbaikan)): ?>
                            <div class="alert alert-light border py-2 mb-2 small">
                                <i class="bi bi-chat-left-text me-1 text-warning"></i>
                                <strong>Catatan:</strong> <?= html_escape($pb->catatan_perbaikan) ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted small mb-2 fst-italic">
                                <i class="bi bi-dash me-1"></i>Tidak ada catatan perbaikan.
                            </p>
                        <?php endif; ?>

                        <!-- Verifikator -->
                        <?php if (!empty($pb->nama_verifikator)): ?>
                            <div class="small text-muted mb-2">
                                <i class="bi bi-person-check me-1 text-primary"></i>
                                Diverifikasi oleh: <strong><?= html_escape($pb->nama_verifikator) ?></strong>
                            </div>
                        <?php endif; ?>

                        <!-- Bukti perbaikan (lampiran) -->
                        <?php if (!empty($pb->lampiran)): ?>
                            <div class="mt-2">
                                <div class="small fw-semibold text-muted mb-2">
                                    <i class="bi bi-paperclip me-1"></i>
                                    Bukti Perbaikan (<?= count($pb->lampiran) ?> file):
                                </div>
                                <div class="row g-2">
                                    <?php foreach ($pb->lampiran as $lamp): ?>
                                        <?php
                                        $ext   = strtolower(pathinfo($lamp->file_path, PATHINFO_EXTENSION));
                                        $is_img = in_array($ext, ['jpg', 'jpeg', 'png', 'webp']);
                                        ?>
                                        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                            <?php if ($is_img): ?>
                                                <a href="<?= base_url($lamp->file_path) ?>" target="_blank"
                                                    class="d-block border rounded overflow-hidden"
                                                    title="Lihat bukti perbaikan">
                                                    <img src="<?= base_url($lamp->file_path) ?>"
                                                        class="img-fluid w-100"
                                                        style="height:80px;object-fit:cover;"
                                                        alt="Bukti perbaikan"
                                                        onerror="this.src='<?= base_url('assets/img/img-error.png') ?>'">
                                                </a>
                                            <?php else: ?>
                                                <a href="<?= base_url($lamp->file_path) ?>" target="_blank"
                                                    class="d-flex flex-column align-items-center justify-content-center
                                                      border rounded text-decoration-none text-muted bg-light"
                                                    style="height:80px;" title="Download bukti">
                                                    <i class="bi bi-<?= $ext === 'pdf'
                                                                        ? 'file-earmark-pdf text-danger'
                                                                        : ($ext === 'doc' || $ext === 'docx'
                                                                            ? 'file-earmark-word text-primary'
                                                                            : 'file-earmark') ?> fs-3"></i>
                                                    <span style="font-size:9px;" class="text-truncate w-100 text-center px-1">
                                                        <?= html_escape(basename($lamp->file_path)) ?>
                                                    </span>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="small text-muted fst-italic">
                                <i class="bi bi-images me-1"></i>Tidak ada bukti perbaikan yang diupload.
                            </div>
                        <?php endif; ?>

                    </div><!-- /perbaikan entri -->

                <?php endforeach; ?>

            </div><!-- /card-body -->
        </div><!-- /card perbaikan -->
    <?php endif; // end perbaikan 
    ?>

</div><!-- /#sectionHistoryInspeksi -->