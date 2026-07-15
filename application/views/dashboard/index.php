<?php
// Helper: badge status
function badge_status($status)
{
    $map = [
        'pengajuan_baru'      => ['primary',          'Pengajuan Baru'],
        'pengajuan_ulang'     => ['warning text-dark', 'Pengajuan Ulang'],
        'diterima_manager'    => ['info text-dark',    'Diterima Manager'],
        'dijadwalkan'         => ['primary',           'Terjadwal'],
        'selesai_inspeksi'    => ['info text-dark',    'Selesai Inspeksi'],
        'diterima_admin_ohs'  => ['info text-dark',    'Diterima Admin OHS'],
        'diterima_ohs_supt'   => ['info text-dark',    'Diterima OHS Supt'],
        'acc_ktt'             => ['success',           'ACC KTT'],
        'stiker_keluar'       => ['success',           'Stiker Keluar'],
        'ditolak_manager'     => ['danger',            'Ditolak Manager'],
        'ditolak_admin_ohs'   => ['danger',            'Ditolak Admin OHS'],
        'ditolak_ohs_supt'    => ['danger',            'Ditolak OHS Supt'],
        'ditolak_ktt'         => ['danger',            'Ditolak KTT'],
        'rejected'            => ['danger',            'Ditolak'],
    ];
    $c = isset($map[$status]) ? $map[$status] : ['secondary', $status];
    return '<span class="badge bg-' . $c[0] . '">' . $c[1] . '</span>';
}

// Helper: level approval label
function level_label($level)
{
    $map = [
        'pengajuan_baru'     => ['warning text-dark', 'Review Manager'],
        'pengajuan_ulang'    => ['warning text-dark', 'Review Manager'],
        'ditolak_admin_ohs'  => ['warning text-dark', 'Review Manager'],
        'diterima_manager'   => ['info text-dark',    'Review Admin OHS'],
        'selesai_inspeksi'   => ['info text-dark',    'Review Hasil'],
        'acc_ktt'            => ['dark',              'Release Stiker'],
        'diterima_admin_ohs' => ['info text-dark',    'OHS Superintendent'],
        'ditolak_ohs_supt'   => ['info text-dark',    'OHS Superintendent'],
        'diterima_ohs_supt'  => ['dark',              'KTT'],
        'dijadwalkan'        => ['primary',           'Mekanik'],
        'ditolak_manager'    => ['danger',            'Ditolak'],
    ];
    $c = isset($map[$level]) ? $map[$level] : ['secondary', $level];
    return '<span class="badge bg-' . $c[0] . '" style="font-size:10px;">' . $c[1] . '</span>';
}


// Helper: level → approval route
function approval_route($status)
{
    $map = [
        'pengajuan_baru'     => 'approval/manager',
        'pengajuan_ulang'    => 'approval/manager',
        'ditolak_admin_ohs'  => 'approval/manager',
        'diterima_manager'   => 'approval/admin_ohs',
        'selesai_inspeksi'   => 'approval/admin_hasil',
        'acc_ktt'            => 'approval/admin_ohs',
        'diterima_admin_ohs' => 'approval/ohs_supt',
        'diterima_ohs_supt'  => 'approval/ktt',
        'dijadwalkan'        => 'jadwal',
    ];
    return isset($map[$status]) ? $map[$status] : 'pengajuan';
}

// Helper: waktu relatif
function time_ago($datetime)
{
    $diff = time() - strtotime($datetime);
    if ($diff < 60)        return $diff . ' dtk';
    if ($diff < 3600)      return floor($diff / 60) . ' mnt';
    if ($diff < 86400)     return floor($diff / 3600) . ' jam';
    if ($diff < 2592000)   return floor($diff / 86400) . ' hari';
    return date('d M Y', strtotime($datetime));
}

// Helper: aktivitas label dari aksi audit
function aksi_label($aksi, $nama, $id_ref)
{
    $no = '#PU-' . str_pad($id_ref, 4, '0', STR_PAD_LEFT);
    $map = [
        'buat_pengajuan'   => "<strong>$nama</strong> membuat pengajuan baru <a href='" . site_url('pengajuan') . "' class='fw-bold text-dark'>$no</a>",
        'approve_manager'  => "<strong>$nama</strong> menyetujui (Manager) <a href='" . site_url('pengajuan') . "' class='fw-bold text-dark'>$no</a>",
        'reject_manager'   => "<strong>$nama</strong> menolak pengajuan <a href='" . site_url('pengajuan') . "' class='fw-bold text-dark'>$no</a>",
        'approve_admin_ohs' => "<strong>$nama</strong> menyetujui dokumen, jadwal dibuat <a href='" . site_url('pengajuan') . "' class='fw-bold text-dark'>$no</a>",
        'buat_jadwal'      => "<strong>$nama</strong> membuat jadwal inspeksi untuk <a href='" . site_url('jadwal') . "' class='fw-bold text-dark'>$no</a>",
        'submit_inspeksi'  => "<strong>$nama</strong> mengunggah hasil inspeksi untuk <a href='" . site_url('pengajuan') . "' class='fw-bold text-dark'>$no</a>",
        'approve_admin_ohs_hasil' => "<strong>$nama</strong> menyetujui hasil inspeksi <a href='" . site_url('pengajuan') . "' class='fw-bold text-dark'>$no</a>",
        'reject_admin_ohs_hasil'  => "<strong>$nama</strong> menolak hasil inspeksi <a href='" . site_url('pengajuan') . "' class='fw-bold text-dark'>$no</a>",
        'approve_ohs_supt' => "<strong>$nama</strong> (OHS Supt.) menyetujui <a href='" . site_url('pengajuan') . "' class='fw-bold text-dark'>$no</a>",
        'reject_ohs_supt'  => "<strong>$nama</strong> (OHS Supt.) mengembalikan ke Admin OHS <a href='" . site_url('pengajuan') . "' class='fw-bold text-dark'>$no</a>",
        'approve_ktt'      => "<strong>$nama</strong> (KTT) memberikan approval final <a href='" . site_url('pengajuan') . "' class='fw-bold text-dark'>$no</a>",
        'reject_ktt'       => "<strong>$nama</strong> (KTT) mengembalikan ke Admin OHS <a href='" . site_url('pengajuan') . "' class='fw-bold text-dark'>$no</a>",
    ];
    return isset($map[$aksi]) ? $map[$aksi] : "<strong>$nama</strong> melakukan aksi <em>$aksi</em>";
}

function aksi_color($aksi)
{
    if (strpos($aksi, 'reject') !== false) return 'danger';
    if (strpos($aksi, 'approve') !== false || strpos($aksi, 'sticker') !== false) return 'success';
    if (strpos($aksi, 'submit') !== false || strpos($aksi, 'buat') !== false) return 'primary';
    return 'secondary';
}
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Dashboard</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div>

    <section class="section dashboard">
        <div class="row">

            <!-- ═══════════════ LEFT COLUMN ═══════════════ -->
            <div class="col-lg-8">
                <div class="row">

                    <!-- STAT: Total Pengajuan -->
                    <div class="col-xxl-4 col-md-6">
                        <div class="card info-card sales-card">
                            <div class="card-body">
                                <h5 class="card-title">Total Pengajuan <span>| Bulan Ini</span></h5>
                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6><?= $total_bulan ?></h6>
                                        <?php if ($delta_pengajuan >= 0): ?>
                                            <span class="text-success small pt-1 fw-bold">+<?= $delta_pengajuan ?></span>
                                        <?php else: ?>
                                            <span class="text-danger small pt-1 fw-bold"><?= $delta_pengajuan ?></span>
                                        <?php endif; ?>
                                        <span class="text-muted small pt-2 ps-1">dari bulan lalu</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STAT: Lulus Uji -->
                    <div class="col-xxl-4 col-md-6">
                        <div class="card info-card revenue-card">
                            <div class="card-body">
                                <h5 class="card-title">Lulus Uji <span>| Bulan Ini</span></h5>
                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-patch-check"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6><?= $lulus_bulan ?></h6>
                                        <span class="text-<?= $pass_rate >= 70 ? 'success' : ($pass_rate >= 50 ? 'warning' : 'danger') ?> small pt-1 fw-bold"><?= $pass_rate ?>%</span>
                                        <span class="text-muted small pt-2 ps-1">pass rate</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STAT: Menunggu Review -->
                    <div class="col-xxl-4 col-xl-12">
                        <div class="card info-card customers-card">
                            <div class="card-body">
                                <h5 class="card-title">Menunggu Review <span>| Sekarang</span></h5>
                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-hourglass-split"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6><?= $menunggu ?></h6>
                                        <?php if ($perlu_tindakan > 0): ?>
                                            <span class="text-danger small pt-1 fw-bold"><?= $perlu_tindakan ?></span>
                                            <span class="text-muted small pt-2 ps-1">perlu tindakan segera</span>
                                        <?php else: ?>
                                            <span class="text-success small pt-1 fw-bold">Aman</span>
                                            <span class="text-muted small pt-2 ps-1">semua dalam waktu normal</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CHART: Tren Pengajuan -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Tren Pengajuan <span>/ 12 Bulan Terakhir</span></h5>
                                <div id="trendChart"></div>
                                <script>
                                    document.addEventListener("DOMContentLoaded", function() {
                                        new ApexCharts(document.querySelector("#trendChart"), {
                                            series: [{
                                                    name: 'Pengajuan Masuk',
                                                    data: <?= $trend_masuk ?>
                                                },
                                                {
                                                    name: 'Lulus Uji',
                                                    data: <?= $trend_lulus ?>
                                                },
                                                {
                                                    name: 'Ditolak',
                                                    data: <?= $trend_ditolak ?>
                                                }
                                            ],
                                            chart: {
                                                height: 320,
                                                type: 'area',
                                                toolbar: {
                                                    show: false
                                                }
                                            },
                                            markers: {
                                                size: 4
                                            },
                                            colors: ['#4154f1', '#2eca6a', '#ff771d'],
                                            fill: {
                                                type: "gradient",
                                                gradient: {
                                                    shadeIntensity: 1,
                                                    opacityFrom: 0.3,
                                                    opacityTo: 0.05,
                                                    stops: [0, 90, 100]
                                                }
                                            },
                                            dataLabels: {
                                                enabled: false
                                            },
                                            stroke: {
                                                curve: 'smooth',
                                                width: 2
                                            },
                                            xaxis: {
                                                categories: <?= $trend_labels ?>
                                            },
                                            tooltip: {
                                                x: {
                                                    format: 'MMM'
                                                }
                                            }
                                        }).render();
                                    });
                                </script>
                            </div>
                        </div>
                    </div>

                    <!-- PIPELINE STATUS -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Status Pipeline <span>| Pengajuan Aktif</span></h5>
                                <div class="row g-2 text-center align-items-center">
                                    <?php
                                    $stages = [
                                        ['label' => 'Pengajuan<br>Masuk',  'key' => 'pengajuan_masuk', 'color' => 'warning'],
                                        ['label' => 'Review<br>Manager',   'key' => 'review_manager',  'color' => 'info'],
                                        ['label' => 'Terjadwal<br>/ Uji',  'key' => 'dijadwalkan',     'color' => 'primary'],
                                        ['label' => 'Inspeksi<br>/Review', 'key' => 'inspeksi',        'color' => 'warning'],
                                        ['label' => 'Stiker<br>Keluar',    'key' => 'stiker_keluar',   'color' => 'success'],
                                    ];
                                    foreach ($stages as $i => $s):
                                        $cnt = $pipeline[$s['key']] ?? 0;
                                    ?>
                                        <div class="col">
                                            <a href="<?= site_url('pengajuan?status=' . $s['key']) ?>" class="text-decoration-none">
                                                <div class="border rounded p-2 <?= $cnt > 0 ? 'border-' . $s['color'] : '' ?>">
                                                    <div class="fs-3 fw-bold text-<?= $s['color'] ?>"><?= $cnt ?></div>
                                                    <div class="small text-muted" style="line-height:1.2;"><?= $s['label'] ?></div>
                                                </div>
                                            </a>
                                        </div>
                                        <?php if ($i < count($stages) - 1): ?>
                                            <div class="col-auto px-0 text-muted"><i class="bi bi-chevron-right"></i></div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                                    <h5 class="card-title mb-0">
                                        Rekap Commissioning
                                        <span class="text-muted fw-normal small ms-1">| Statistik Periodik</span>
                                    </h5>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <!-- Pilih mode periode -->
                                        <div class="btn-group btn-group-sm" id="rekapModeGroup" role="group">
                                            <input type="radio" class="btn-check" name="rekapMode"
                                                id="mode_hari" value="hari" autocomplete="off">
                                            <label class="btn btn-outline-primary" for="mode_hari">Hari</label>

                                            <input type="radio" class="btn-check" name="rekapMode"
                                                id="mode_minggu" value="minggu" autocomplete="off">
                                            <label class="btn btn-outline-primary" for="mode_minggu">Minggu</label>

                                            <input type="radio" class="btn-check" name="rekapMode"
                                                id="mode_bulan" value="bulan" autocomplete="off" checked>
                                            <label class="btn btn-outline-primary" for="mode_bulan">Bulan</label>

                                            <input type="radio" class="btn-check" name="rekapMode"
                                                id="mode_tahun" value="tahun" autocomplete="off">
                                            <label class="btn btn-outline-primary" for="mode_tahun">Tahun</label>
                                        </div>
                                        <!-- Input rentang tanggal (untuk mode hari/minggu) -->
                                        <div id="rekapDateRange" class="d-flex gap-1 align-items-center" style="display:none!important;">
                                            <input type="date" class="form-control form-control-sm"
                                                id="rekapDari" style="width:130px;">
                                            <span class="text-muted small">s/d</span>
                                            <input type="date" class="form-control form-control-sm"
                                                id="rekapSampai" style="width:130px;">
                                        </div>
                                        <button class="btn btn-sm btn-primary" id="btnLoadRekap">
                                            <i class="bi bi-arrow-clockwise me-1"></i>Tampilkan
                                        </button>
                                    </div>
                                </div>

                                <!-- Summary stat row -->
                                <div class="row g-2 mb-3" id="rekapSummaryRow">
                                    <div class="col-6 col-md-3">
                                        <div class="border rounded p-2 text-center">
                                            <div class="fs-4 fw-bold text-primary" id="rs_total">—</div>
                                            <div class="small text-muted">Total Pengajuan</div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="border rounded p-2 text-center">
                                            <div class="fs-4 fw-bold text-success" id="rs_lulus">—</div>
                                            <div class="small text-muted">Lulus / Stiker Keluar</div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="border rounded p-2 text-center">
                                            <div class="fs-4 fw-bold text-danger" id="rs_tidak_lulus">—</div>
                                            <div class="small text-muted">Tidak Lulus</div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="border rounded p-2 text-center">
                                            <div class="fs-4 fw-bold text-info" id="rs_pass_rate">—</div>
                                            <div class="small text-muted">Pass Rate</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Chart -->
                                <div id="rekapChart" style="min-height:260px;"></div>

                                <!-- Breakdown per jenis -->
                                <div class="mt-3">
                                    <div class="fw-semibold small text-muted mb-2">
                                        <i class="bi bi-truck me-1"></i>Breakdown per Jenis Kendaraan
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover align-middle mb-0" id="rekapJenisTable">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Jenis</th>
                                                    <th class="text-center">Total</th>
                                                    <th class="text-center">Lulus</th>
                                                    <th class="text-center">Tidak Lulus</th>
                                                    <th class="text-center">Pass Rate</th>
                                                </tr>
                                            </thead>
                                            <tbody id="rekapJenisTbody">
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-3">
                                                        Pilih periode dan klik Tampilkan
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- PENGAJUAN TERBARU -->
                    <div class="col-12">
                        <div class="card recent-sales overflow-auto">
                            <div class="card-body">
                                <h5 class="card-title">Pengajuan Terbaru <span>| <?= count($pengajuan_terbaru) ?> Data</span></h5>
                                <?php if (empty($pengajuan_terbaru)): ?>
                                    <p class="text-muted text-center py-3">Belum ada pengajuan.</p>
                                <?php else: ?>
                                    <table class="table table-borderless table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Pemohon</th>
                                                <th>No. Polisi</th>
                                                <th>Jenis</th>
                                                <th>Status</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($pengajuan_terbaru as $p): ?>
                                                <tr>
                                                    <td><span class="fw-bold text-primary">#PU-<?= str_pad($p->id_pengajuan, 4, '0', STR_PAD_LEFT) ?></span></td>
                                                    <td class="small"><?= html_escape($p->nama_pemohon) ?></td>
                                                    <td><span class="badge bg-secondary font-monospace"><?= html_escape($p->no_polisi) ?></span></td>
                                                    <td class="small"><?= html_escape($p->jenis_kendaraan) ?></td>
                                                    <td><?= badge_status($p->status) ?></td>
                                                    <td class="text-center">
                                                        <button class="btn btn-sm btn-outline-primary py-0 btn-detail" data-id="<?= $p->id_pengajuan ?>">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <div class="text-end mt-2">
                                        <a href="<?= site_url('pengajuan') ?>" class="small text-primary">Lihat semua →</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- JADWAL MENDATANG -->
                    <div class="col-12">
                        <div class="card top-selling overflow-auto">
                            <div class="card-body pb-2">
                                <h5 class="card-title">Jadwal Uji Mendatang <span>| Terjadwal</span></h5>
                                <?php if (empty($jadwal_mendatang)): ?>
                                    <p class="text-muted text-center py-3">Tidak ada jadwal mendatang.</p>
                                <?php else: ?>
                                    <table class="table table-borderless table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Kendaraan</th>
                                                <th>Lokasi</th>
                                                <th>Mekanik</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($jadwal_mendatang as $j): ?>
                                                <tr>
                                                    <td>
                                                        <span class="fw-bold text-primary"><?= date('d M Y', strtotime($j->tanggal_uji)) ?></span>
                                                        <br><small class="text-muted"><?= date('H:i', strtotime($j->tanggal_uji)) ?> WIB</small>
                                                    </td>
                                                    <td>
                                                        <span class="fw-bold small"><?= html_escape($j->no_polisi) ?></span>
                                                        <br><small class="text-muted"><?= html_escape($j->jenis_kendaraan) ?> <?= html_escape($j->merk) ?></small>
                                                    </td>
                                                    <td class="small"><?= html_escape($j->lokasi) ?></td>
                                                    <td class="small"><?= html_escape($j->nama_mekanik ?? '-') ?></td>
                                                    <td><span class="badge bg-primary">Terjadwal</span></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <div class="text-end mt-2">
                                        <a href="<?= site_url('jadwal') ?>" class="small text-primary">Lihat semua →</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div><!-- End Left Column -->

            <!-- ═══════════════ RIGHT COLUMN ═══════════════ -->
            <div class="col-lg-4">

                <!-- APPROVAL QUEUE -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            Approval Queue
                            <?php if (!empty($approval_queue)): ?>
                                <span class="badge bg-danger rounded-pill ms-1" style="font-size:11px;"><?= count($approval_queue) ?></span>
                            <?php endif; ?>
                            <span class="text-muted small fw-normal ms-1">| Menunggu Tindakan</span>
                        </h5>

                        <?php if (empty($approval_queue)): ?>
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-check2-all fs-2 d-block mb-2 text-success opacity-75"></i>
                                <small>Tidak ada pengajuan yang menunggu tindakan Anda.</small>
                            </div>
                        <?php else: ?>
                            <?php foreach ($approval_queue as $q):
                                $elapsed = time_ago($q->tanggal_pengajuan);
                            ?>
                                <div class="border rounded p-3 mb-2">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <span class="fw-bold text-primary small">#PU-<?= str_pad($q->id_pengajuan, 4, '0', STR_PAD_LEFT) ?></span>
                                        <?= level_label($q->status) ?>
                                    </div>
                                    <div class="fw-semibold mb-1" style="font-size:13px;">
                                        <?= html_escape($q->jenis_kendaraan) ?> <?= html_escape($q->merk) ?>
                                        <span class="badge bg-secondary font-monospace ms-1" style="font-size:10px;"><?= html_escape($q->no_polisi) ?></span>
                                    </div>
                                    <div class="small text-muted mb-2">
                                        <i class="bi bi-person me-1"></i><?= html_escape($q->nama_pemohon) ?>
                                        &nbsp;·&nbsp;
                                        <i class="bi bi-clock me-1"></i><?= $elapsed ?> lalu
                                    </div>
                                    <div class="d-flex gap-1">
                                        <a href="<?= site_url(approval_route($q->status)) ?>"
                                            class="btn btn-sm btn-outline-primary flex-fill py-1" style="font-size:12px;">
                                            <i class="bi bi-eye me-1"></i>Review
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div class="text-end mt-1">
                                <a href="<?= site_url('pengajuan') ?>" class="small text-primary">Lihat semua →</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- STICKER SIAP DITERBITKAN -->
                <?php if (!empty($siap_stiker)): ?>
                    <div class="card">
                        <div class="card-body pb-2">
                            <h5 class="card-title">
                                Sticker Siap Diterbitkan
                                <span class="badge bg-success rounded-pill ms-1" style="font-size:11px;"><?= count($siap_stiker) ?></span>
                            </h5>
                            <?php foreach ($siap_stiker as $s): ?>
                                <div class="border border-success rounded p-3 mb-2">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <i class="bi bi-patch-check-fill text-success"></i>
                                        <span class="fw-semibold" style="font-size:13px;">
                                            <?= html_escape($s->no_polisi) ?> — <?= html_escape($s->jenis_kendaraan) ?>
                                        </span>
                                    </div>
                                    <div class="small text-muted mb-2">
                                        <i class="bi bi-person me-1"></i><?= html_escape($s->nama_pemohon) ?>
                                        &nbsp;·&nbsp;<i class="bi bi-check-circle me-1 text-success"></i>Lulus Uji
                                    </div>
                                    <button class="btn btn-sm btn-success w-100 py-1 btn-sticker"
                                        data-id="<?= $s->id_pengajuan ?>">
                                        <i class="bi bi-patch-check me-1"></i>Terbitkan Sticker
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- AKTIVITAS TERBARU -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Aktivitas Terbaru <span>| Log Sistem</span></h5>
                        <?php if (empty($aktivitas)): ?>
                            <p class="text-muted text-center py-3 small">Belum ada aktivitas.</p>
                        <?php else: ?>
                            <div class="activity">
                                <?php foreach ($aktivitas as $a): ?>
                                    <div class="activity-item d-flex">
                                        <div class="activite-label"><?= time_ago($a->created_at) ?></div>
                                        <i class="bi bi-circle-fill activity-badge text-<?= aksi_color($a->aksi) ?> align-self-start"></i>
                                        <div class="activity-content small">
                                            <?= aksi_label($a->aksi, html_escape($a->nama_user ?? 'System'), $a->id_ref) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- REKAP STATUS PIE CHART -->
                <div class="card">
                    <div class="card-body pb-0">
                        <h5 class="card-title">Rekap Status <span>| Bulan Ini</span></h5>
                        <div id="rekapPieChart" style="min-height:280px;" class="echart"></div>
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                var chart = echarts.init(document.querySelector("#rekapPieChart"));
                                chart.setOption({
                                    tooltip: {
                                        trigger: 'item',
                                        formatter: '{b}: {c} ({d}%)'
                                    },
                                    legend: {
                                        top: '5%',
                                        left: 'center',
                                        textStyle: {
                                            fontSize: 11
                                        }
                                    },
                                    series: [{
                                        name: 'Status Pengajuan',
                                        type: 'pie',
                                        radius: ['40%', '68%'],
                                        avoidLabelOverlap: false,
                                        label: {
                                            show: false,
                                            position: 'center'
                                        },
                                        emphasis: {
                                            label: {
                                                show: true,
                                                fontSize: 14,
                                                fontWeight: 'bold'
                                            }
                                        },
                                        labelLine: {
                                            show: false
                                        },
                                        data: [{
                                                value: <?= $rekap_lulus ?>,
                                                name: 'Lulus Uji',
                                                itemStyle: {
                                                    color: '#2eca6a'
                                                }
                                            },
                                            {
                                                value: <?= $rekap_proses ?>,
                                                name: 'Dalam Proses',
                                                itemStyle: {
                                                    color: '#4154f1'
                                                }
                                            },
                                            {
                                                value: <?= $rekap_jadwal ?>,
                                                name: 'Terjadwal/Uji',
                                                itemStyle: {
                                                    color: '#0dcaf0'
                                                }
                                            },
                                            {
                                                value: <?= $rekap_tolak ?>,
                                                name: 'Ditolak',
                                                itemStyle: {
                                                    color: '#ff771d'
                                                }
                                            },
                                        ]
                                    }]
                                });
                            });
                        </script>
                    </div>
                </div>

            </div><!-- End Right Column -->

        </div>
    </section>
</main>

<!-- Modal Detail (reuse dari pengajuan) -->
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetailLabel">
                    <i class="bi bi-file-earmark-text me-2"></i>Detail Pengajuan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalDetailBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).on('click', '.btn-detail', function() {
        var id = $(this).data('id');
        $('#modalDetailLabel').html('<i class="bi bi-file-earmark-text me-2"></i>Detail Pengajuan #PU-' + String(id).padStart(4, '0'));
        $('#modalDetailBody').html('<div class="text-center py-5"><div class="spinner-border text-primary"></div><div class="mt-2 text-muted small">Memuat...</div></div>');
        $('#modalDetail').modal('show');
        $.ajax({
            url: '<?= site_url('pengajuan/detail') ?>/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    // Redirect ke pengajuan index untuk lihat detail lengkap
                    window.location.href = '<?= site_url('pengajuan') ?>?detail=' + id;
                } else {
                    $('#modalDetailBody').html('<div class="alert alert-danger">' + res.message + '</div>');
                }
            },
            error: function() {
                $('#modalDetailBody').html('<div class="alert alert-danger">Gagal memuat data.</div>');
            }
        });
    });
    $(function() {

        // Set default date range: 30 hari terakhir
        var today = new Date();
        var dari30 = new Date(today);
        dari30.setDate(dari30.getDate() - 29);
        $('#rekapDari').val(dari30.toISOString().slice(0, 10));
        $('#rekapSampai').val(today.toISOString().slice(0, 10));

        var rekapChart = null;

        // Toggle date range input
        $('input[name="rekapMode"]').on('change', function() {
            var mode = $(this).val();
            if (mode === 'hari' || mode === 'minggu') {
                $('#rekapDateRange').css('display', 'flex');
            } else {
                $('#rekapDateRange').css('display', 'none!important').hide();
            }
        });

        // Load saat pertama kali
        loadRekap();

        $('#btnLoadRekap').on('click', function() {
            loadRekap();
        });

        function loadRekap() {
            var mode = $('input[name="rekapMode"]:checked').val() || 'bulan';
            var dari = $('#rekapDari').val();
            var sampai = $('#rekapSampai').val();

            $('#rs_total, #rs_lulus, #rs_tidak_lulus, #rs_pass_rate').text('…');

            $.ajax({
                url: '<?= site_url('dashboard/rekap_commissioning') ?>',
                type: 'POST',
                data: {
                    '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>',
                    mode: mode,
                    dari: dari,
                    sampai: sampai,
                },
                dataType: 'json',
                success: function(res) {
                    if (!res || res.status !== 'success') return;
                    renderRekap(res.data, mode);
                },
                error: function() {
                    toastr.error('Gagal memuat rekap.');
                }
            });
        }

        function renderRekap(d, mode) {
            // Summary
            var total = d.summary.total || 0;
            var lulus = d.summary.lulus || 0;
            var tdk_lulus = d.summary.tidak_lulus || 0;
            var pass_rate = total > 0 ? Math.round(lulus / total * 100) : 0;

            $('#rs_total').text(total);
            $('#rs_lulus').text(lulus);
            $('#rs_tidak_lulus').text(tdk_lulus);
            $('#rs_pass_rate').text(pass_rate + '%');

            // Chart
            var labels = d.chart.map(function(r) {
                return r.label;
            });
            var s_masuk = d.chart.map(function(r) {
                return r.masuk;
            });
            var s_lulus = d.chart.map(function(r) {
                return r.lulus;
            });
            var s_tidak = d.chart.map(function(r) {
                return r.tidak_lulus;
            });

            var chartOpts = {
                series: [{
                        name: 'Masuk',
                        data: s_masuk
                    },
                    {
                        name: 'Lulus',
                        data: s_lulus
                    },
                    {
                        name: 'Tidak Lulus',
                        data: s_tidak
                    },
                ],
                chart: {
                    type: 'bar',
                    height: 260,
                    toolbar: {
                        show: false
                    },
                    animations: {
                        enabled: true,
                        speed: 400
                    },
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        columnWidth: '55%'
                    }
                },
                colors: ['#4154f1', '#2eca6a', '#dc3545'],
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: labels,
                    labels: {
                        rotate: -30,
                        style: {
                            fontSize: '11px'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function(v) {
                            return Math.round(v);
                        }
                    }
                },
                legend: {
                    position: 'top'
                },
                tooltip: {
                    shared: true,
                    intersect: false
                },
            };

            if (rekapChart) {
                rekapChart.updateOptions(chartOpts, true);
            } else {
                rekapChart = new ApexCharts(document.getElementById('rekapChart'), chartOpts);
                rekapChart.render();
            }

            // Tabel per jenis
            var tbody = '';
            if (d.per_jenis && d.per_jenis.length) {
                d.per_jenis.forEach(function(r) {
                    var pr = r.total > 0 ? Math.round(r.lulus / r.total * 100) : 0;
                    var prClass = pr >= 70 ? 'text-success' : (pr >= 50 ? 'text-warning' : 'text-danger');
                    tbody +=
                        '<tr>' +
                        '<td>' + r.jenis + '</td>' +
                        '<td class="text-center">' + r.total + '</td>' +
                        '<td class="text-center text-success fw-bold">' + r.lulus + '</td>' +
                        '<td class="text-center text-danger">' + r.tidak_lulus + '</td>' +
                        '<td class="text-center ' + prClass + ' fw-bold">' + pr + '%</td>' +
                        '</tr>';
                });
            } else {
                tbody = '<tr><td colspan="5" class="text-center text-muted py-3">Tidak ada data</td></tr>';
            }
            $('#rekapJenisTbody').html(tbody);
        }

    });
</script>