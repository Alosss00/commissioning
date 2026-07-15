<main id="main" class="main">

    <div class="pagetitle">
        <h1>Jadwal Inspeksi</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                <li class="breadcrumb-item active">Jadwal Inspeksi</li>
            </ol>
        </nav>
    </div>

    <section class="section">

        <!-- STAT CARDS -->
        <div class="row g-3 mb-3">
            <?php
            $total     = count($jadwals);
            $scheduled = count(array_filter($jadwals, fn($j) => $j->status === 'scheduled'));
            $done      = count(array_filter($jadwals, fn($j) => $j->status === 'done'));
            $cancelled = count(array_filter($jadwals, fn($j) => $j->status === 'cancelled'));
            $perlu_jadwal = count($menunggu_jadwal);
            ?>
            <div class="col-sm-3">
                <div class="card border-0 bg-primary bg-opacity-10 text-center py-3">
                    <div class="fs-2 fw-bold text-primary"><?= $total ?></div>
                    <div class="text-muted small">Total Jadwal</div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card border-0 bg-info bg-opacity-10 text-center py-3">
                    <div class="fs-2 fw-bold text-info"><?= $scheduled ?></div>
                    <div class="text-muted small">Aktif Terjadwal</div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card border-0 bg-success bg-opacity-10 text-center py-3">
                    <div class="fs-2 fw-bold text-success"><?= $done ?></div>
                    <div class="text-muted small">Selesai Inspeksi</div>
                </div>
            </div>
            <div class="col-sm-3">
                <?php if ($perlu_jadwal > 0): ?>
                    <a href="#antriJadwal" class="text-decoration-none">
                        <div class="card border-0 bg-warning bg-opacity-25 text-center py-3">
                            <div class="fs-2 fw-bold text-warning"><?= $perlu_jadwal ?></div>
                            <div class="small text-warning fw-semibold">⚠ Perlu Dijadwalkan</div>
                        </div>
                    </a>
                <?php else: ?>
                    <div class="card border-0 bg-secondary bg-opacity-10 text-center py-3">
                        <div class="fs-2 fw-bold text-secondary">0</div>
                        <div class="text-muted small">Perlu Dijadwalkan</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ANTRIAN PERLU DIJADWALKAN -->
        <?php if ($perlu_jadwal > 0): ?>
            <div class="card mb-3 border-warning border-2" id="antriJadwal">
                <div class="card-body pt-3 pb-2">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-title mb-0 text-warning">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            Pengajuan Menunggu Jadwal
                            <span class="badge bg-warning text-dark ms-1"><?= $perlu_jadwal ?></span>
                        </h6>
                        <small class="text-muted">Sudah disetujui Admin OHS, belum ada jadwal aktif</small>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No. Polisi</th>
                                    <th>Kendaraan</th>
                                    <th>Pemohon</th>
                                    <th>Tgl Pengajuan</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($menunggu_jadwal as $a): ?>
                                    <tr>
                                        <td>
                                            <strong class="text-primary"><?= html_escape($a->no_polisi) ?></strong>
                                            <br><small class="text-muted">#PU-<?= str_pad($a->id_pengajuan, 4, '0', STR_PAD_LEFT) ?></small>
                                        </td>
                                        <td><small><?= html_escape($a->jenis_kendaraan) ?> — <?= html_escape($a->merk) ?> <?= html_escape($a->tipe) ?></small></td>
                                        <td><small><?= html_escape($a->nama_pemohon) ?></small></td>
                                        <td><small><?= date('d M Y', strtotime($a->tanggal_pengajuan)) ?></small></td>
                                        <td class="text-center">
                                            <a href="<?= site_url('jadwal/create/' . $a->id_pengajuan) ?>"
                                                class="btn btn-sm btn-primary py-0 px-2">
                                                <i class="bi bi-calendar-plus me-1"></i>Jadwalkan
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row g-3">

            <!-- KALENDER -->
            <div class="col-xl-8">
                <div class="card h-100">
                    <div class="card-body pt-4">
                        <h5 class="card-title">Kalender Jadwal</h5>
                        <div id="kalender"></div>
                    </div>
                </div>
            </div>

            <!-- DAFTAR JADWAL -->
            <div class="col-xl-4">
                <div class="card h-100">
                    <div class="card-body pt-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Daftar Jadwal</h5>
                            <select class="form-select form-select-sm w-auto" id="filterBulan">
                                <?php for ($m = 1; $m <= 12; $m++): ?>
                                    <option value="<?= $m ?>" <?= ($filter['bulan'] == $m || (!$filter['bulan'] && $m == date('n'))) ? 'selected' : '' ?>>
                                        <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div id="listJadwal" style="max-height:520px;overflow-y:auto;">
                            <?php if (empty($jadwals)): ?>
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-calendar-x fs-1 d-block mb-2 opacity-40"></i>
                                    Belum ada jadwal.
                                </div>
                            <?php else: ?>
                                <?php foreach ($jadwals as $j): ?>
                                    <?php
                                    $badge_color = $j->status === 'scheduled' ? 'bg-primary'
                                        : ($j->status === 'done'     ? 'bg-success' : 'bg-danger');
                                    $badge_label = $j->status === 'scheduled' ? 'Terjadwal'
                                        : ($j->status === 'done'     ? 'Selesai'    : 'Dibatalkan');
                                    ?>
                                    <div class="border rounded p-2 mb-2 jadwal-item" data-id="<?= $j->id_jadwal ?>" style="cursor:pointer;">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <span class="fw-bold text-primary small"><?= html_escape($j->no_polisi) ?></span>
                                                <span class="ms-1 text-muted small"><?= html_escape($j->jenis_kendaraan) ?></span>
                                            </div>
                                            <span class="badge <?= $badge_color ?> small"><?= $badge_label ?></span>
                                        </div>
                                        <div class="text-muted small mt-1">
                                            <i class="bi bi-calendar3 me-1"></i><?= date('d M Y H:i', strtotime($j->tanggal_uji)) ?>
                                        </div>
                                        <div class="text-muted small">
                                            <i class="bi bi-geo-alt me-1"></i><?= html_escape($j->lokasi ?: '—') ?>
                                        </div>
                                        <?php if (!empty($j->nama_mekanik_master) || !empty($j->nama_inspektor_user)): ?>
                                            <div class="text-muted small">
                                                <?php if (!empty($j->nama_mekanik_master)): ?>
                                                    <i class="bi bi-tools me-1 text-warning"></i><?= html_escape($j->nama_mekanik_master) ?><br>
                                                <?php endif; ?>
                                                <?php if (!empty($j->nama_inspektor_user)): ?>
                                                    <i class="bi bi-person-badge me-1 text-primary"></i><?= html_escape($j->nama_inspektor_user) ?>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($j->status === 'scheduled'): ?>
                                            <div class="d-flex gap-1 mt-2">
                                                <a href="<?= site_url('jadwal/edit/' . $j->id_jadwal) ?>"
                                                    class="btn btn-outline-secondary py-0 px-2" style="font-size:11px;">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                                <button class="btn btn-outline-danger py-0 px-2 btn-cancel"
                                                    data-id="<?= $j->id_jadwal ?>"
                                                    data-polisi="<?= html_escape($j->no_polisi) ?>"
                                                    style="font-size:11px;">
                                                    <i class="bi bi-x-circle"></i> Batal
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </section>
</main>


<!-- Modal Detail Jadwal -->
<div class="modal fade" id="modalDetailJadwal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-calendar-check me-2"></i>Detail Jadwal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailJadwalBody">
                <div class="text-center py-3"><span class="spinner-border text-primary"></span></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                <a href="#" class="btn btn-outline-secondary btn-sm" id="btnEditJadwal">
                    <i class="bi bi-pencil me-1"></i>Edit
                </a>
                <button type="button" class="btn btn-danger btn-sm" id="btnCancelJadwal">
                    <i class="bi bi-x-circle me-1"></i>Batalkan
                </button>
            </div>
        </div>
    </div>
</div>


<!-- <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet"> -->
<script src="<?= base_url('assets/js/fullcalendar.js') ?>"></script>

<script>
    $(function() {

        var csrfName = '<?= $this->security->get_csrf_token_name() ?>';
        var csrfHash = '<?= $this->security->get_csrf_hash() ?>';
        var modalDetail = new bootstrap.Modal(document.getElementById('modalDetailJadwal'));
        var activeJadwalId = null;

        // ── FullCalendar ───────────────────────────────────────────────────────
        var calendar = new FullCalendar.Calendar(document.getElementById('kalender'), {
            initialView: 'dayGridMonth',
            locale: 'id',
            height: 500,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,listWeek'
            },
            events: <?= $events_json ?>,
            eventClick: function(info) {
                bukaDetailJadwal(info.event.extendedProps.id_jadwal);
            },
            eventDidMount: function(info) {
                new bootstrap.Tooltip(info.el, {
                    title: (info.event.extendedProps.pemohon || '') + ' | ' + (info.event.extendedProps.lokasi || '—'),
                    placement: 'top',
                    trigger: 'hover',
                    container: 'body',
                });
            },
        });
        calendar.render();

        // ── Klik item list jadwal ──────────────────────────────────────────────
        $(document).on('click', '.jadwal-item', function(e) {
            if ($(e.target).closest('.btn-cancel, .btn-outline-secondary, a').length) return;
            bukaDetailJadwal($(this).data('id'));
        });

        // ── Buka modal detail ──────────────────────────────────────────────────
        function bukaDetailJadwal(id) {
            activeJadwalId = id;
            $('#detailJadwalBody').html('<div class="text-center py-3"><span class="spinner-border text-primary"></span></div>');
            $('#btnEditJadwal').attr('href', '<?= site_url('jadwal/edit') ?>/' + id);
            modalDetail.show();

            var post = {};
            post[csrfName] = csrfHash;
            post.id_jadwal = id;
            $.post('<?= site_url('jadwal/detail') ?>', post, function(res) {
                if (!res.status || !res.data) return;
                var d = res.data;

                // Badge status jadwal
                var statusBadge = {
                    scheduled: '<span class="badge bg-primary">Terjadwal</span>',
                    done: '<span class="badge bg-success">Selesai</span>',
                    cancelled: '<span class="badge bg-danger">Dibatalkan</span>',
                } [d.status] || '<span class="badge bg-secondary">' + d.status + '</span>';

                // Badge status pengajuan (bahasa Indonesia)
                var statusPengajuanLabel = {
                    dijadwalkan: '<span class="badge bg-primary">Dijadwalkan Inspeksi</span>',
                    selesai_inspeksi: '<span class="badge bg-warning text-dark">Selesai Inspeksi</span>',
                    diterima_admin_ohs: '<span class="badge bg-info text-dark">Diterima Admin OHS</span>',
                } [d.status_pengajuan] || '<span class="badge bg-secondary">' + (d.status_pengajuan || '') + '</span>';

                var html = '<div class="row g-2">' +
                    '<div class="col-6"><small class="text-muted d-block">No. Polisi</small><strong class="text-primary">' + d.no_polisi + '</strong></div>' +
                    '<div class="col-6"><small class="text-muted d-block">Jenis Kendaraan</small><strong>' + d.jenis_kendaraan + '</strong></div>' +
                    '<div class="col-6"><small class="text-muted d-block">Merk / Tipe</small><strong>' + d.merk + ' ' + d.tipe_kendaraan + '</strong></div>' +
                    '<div class="col-6"><small class="text-muted d-block">Pemohon</small><strong>' + d.nama_pemohon + '</strong></div>' +
                    '<div class="col-12"><hr class="my-2"></div>' +
                    '<div class="col-6"><small class="text-muted d-block">Tanggal & Jam</small><strong>' + formatTgl(d.tanggal_uji) + '</strong></div>' +
                    '<div class="col-6"><small class="text-muted d-block">Lokasi</small><strong>' + (d.lokasi || '—') + '</strong></div>' +
                    '<div class="col-6"><small class="text-muted d-block"><i class="bi bi-tools me-1 text-warning"></i>Mekanik Lapangan</small><strong>' + (d.nama_mekanik_master || '—') + '</strong>' +
                    (d.perusahaan_mekanik ? '<br><small class="text-muted">' + d.perusahaan_mekanik + '</small>' : '') + '</div>' +
                    '<div class="col-6"><small class="text-muted d-block"><i class="bi bi-person-badge me-1 text-primary"></i>Inspektor Sistem</small><strong>' + (d.nama_inspektor_user || '—') + '</strong></div>' +
                    '<div class="col-6"><small class="text-muted d-block">Dibuat oleh</small><strong>' + (d.dibuat_oleh_nama || '—') + '</strong></div>' +
                    '<div class="col-6"><small class="text-muted d-block">Status Jadwal</small>' + statusBadge + '</div>' +
                    '<div class="col-6"><small class="text-muted d-block">Status Pengajuan</small>' + statusPengajuanLabel + '</div>' +
                    (d.keterangan ? '<div class="col-12"><small class="text-muted d-block">Keterangan</small><p class="mb-0 small">' + d.keterangan + '</p></div>' : '') +
                    '</div>';

                $('#detailJadwalBody').html(html);

                if (d.status !== 'scheduled') {
                    $('#btnEditJadwal, #btnCancelJadwal').hide();
                } else {
                    $('#btnEditJadwal, #btnCancelJadwal').show();
                }
            }, 'json');
        }

        // ── Cancel dari modal ──────────────────────────────────────────────────
        $('#btnCancelJadwal').on('click', function() {
            doCancel(activeJadwalId, null);
        });

        $(document).on('click', '.btn-cancel', function(e) {
            e.stopPropagation(); // cukup ini, tidak perlu onclick di HTML
            doCancel($(this).data('id'), $(this).data('polisi'));
        });

        function doCancel(id, polisi) {
            Swal.fire({
                title: 'Batalkan Jadwal?',
                html: polisi ?
                    'Jadwal untuk <strong>' + polisi + '</strong> akan dibatalkan.<br><small class="text-muted">Pengajuan akan kembali ke antrian penjadwalan.</small>' : 'Jadwal ini akan dibatalkan. Pengajuan kembali ke antrian penjadwalan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Batalkan',
                cancelButtonText: 'Tidak',
            }).then(function(r) {
                if (!r.isConfirmed) return;
                var post = {};
                post[csrfName] = csrfHash;
                post.id_jadwal = id;
                $.post('<?= site_url('jadwal/cancel') ?>', post, function(res) {
                    if (res.status === 'success') {
                        toastr.success(res.message);
                        modalDetail.hide();
                        setTimeout(function() {
                            location.reload();
                        }, 900);
                    } else {
                        toastr.error(res.message);
                    }
                }, 'json');
            });
        }

        // ── Filter bulan ──────────────────────────────────────────────────────
        $('#filterBulan').on('change', function() {
            window.location.href = '<?= site_url('jadwal') ?>?bulan=' + $(this).val() + '&tahun=<?= $filter['tahun'] ?>';
        });

        // ── Format tanggal helper ─────────────────────────────────────────────
        function formatTgl(str) {
            if (!str) return '—';
            var d = new Date(str);
            var bln = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            return d.getDate() + ' ' + bln[d.getMonth()] + ' ' + d.getFullYear() +
                ' ' + String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0');
        }

    });
</script>