<main id="main" class="main">

    <div class="pagetitle">
        <h1>Daftar Pengajuan</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Home</a></li>
                <li class="breadcrumb-item active">Daftar Pengajuan</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center pt-3 mb-3">
                            <h5 class="card-title mb-0">Daftar Pengajuan Uji Kelayakan</h5>
                            <?php
                            $roles_sess = $this->session->userdata('roles');
                            $role_int   = (int)$this->session->userdata('role');
                            $_r = is_array($roles_sess) ? array_map('intval', $roles_sess) : ($role_int > 0 ? [$role_int] : []);
                            $canCreate = in_array(1, $_r) || in_array(7, $_r);
                            ?>
                            <?php if ($canCreate): ?>
                                <a href="<?= site_url('pengajuan/create') ?>" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-circle me-1"></i>Buat Pengajuan
                                </a>
                            <?php endif; ?>
                        </div>

                        <!-- Filter -->
                        <div class="row g-2 mb-3">
                            <div class="col-sm-6 col-md-3">
                                <select class="form-select form-select-sm" id="filterStatus">
                                    <option value="">— Semua Status —</option>
                                    <option value="draft">Draft</option>
                                    <option value="pengajuan_baru">Pengajuan Baru</option>
                                    <option value="pengajuan_ulang">Pengajuan Ulang</option>
                                    <option value="diterima_manager">Diterima Manager</option>
                                    <option value="ditolak_manager">Ditolak Manager</option>
                                    <option value="dijadwalkan">Dijadwalkan Inspeksi</option>
                                    <option value="lulus_inspeksi">Lulus — Menunggu OHS Supt</option>
                                    <option value="tidak_lulus_inspeksi">Tidak Lulus — Dikembalikan</option>
                                    <option value="selesai_inspeksi">Selesai Inspeksi</option>
                                    <option value="diterima_admin_ohs">Diterima Admin OHS</option>
                                    <option value="ditolak_admin_ohs">Ditolak Admin OHS</option>
                                    <option value="diterima_ohs_supt">Diterima OHS Superintendent</option>
                                    <option value="ditolak_ohs_supt">Ditolak OHS Superintendent</option>
                                    <option value="acc_ktt">Disetujui KTT</option>
                                    <option value="ditolak_ktt">Ditolak KTT</option>
                                    <option value="stiker_keluar">Stiker Sudah Keluar</option>
                                    <option value="rejected">Ditolak</option>
                                </select>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <select class="form-select form-select-sm" id="filterJenis">
                                    <option value="">— Semua Jenis —</option>
                                    <option>Light Vehicle</option>
                                    <option>Light Truck</option>
                                    <option>Bus</option>
                                    <option>Dump Truck</option>
                                    <option>Haul Truck</option>
                                    <option>Excavator</option>
                                    <option>Bulldozer</option>
                                    <option>Motor Grader</option>
                                    <option>Wheel Loader</option>
                                    <option>Forklift</option>
                                    <option>Crane Truck</option>
                                    <option>Compactor</option>
                                </select>
                            </div>
                            <div class="col-sm-6 col-md-2">
                                <input type="text" class="form-control form-control-sm flatpickr-date" id="filterTglDari" placeholder="Dari Tanggal">
                            </div>
                            <div class="col-sm-6 col-md-2">
                                <input type="text" class="form-control form-control-sm flatpickr-date" id="filterTglSampai" placeholder="Sampai Tanggal">
                            </div>
                            <div class="col-sm-12 col-md-2 d-flex gap-2">
                                <button class="btn btn-primary btn-sm flex-fill" id="btnFilter">
                                    <i class="bi bi-search me-1"></i>Filter
                                </button>
                                <button class="btn btn-outline-secondary btn-sm" id="btnReset" title="Reset">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Tabel -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle" id="tabelPengajuan" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40">No</th>
                                        <th>ID</th>
                                        <th>Pemohon</th>
                                        <th>No. Polisi</th>
                                        <th>Kendaraan</th>
                                        <th>Unit</th>
                                        <th>Status</th>
                                        <th>Tgl Pengajuan</th>
                                        <th width="140">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<!-- Modal Detail -->
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

<!-- Modal Release Stiker (dari halaman pengajuan) -->
<div class="modal fade" id="modalReleaseStiker" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title text-white"><i class="bi bi-patch-check me-2"></i>Terbitkan Stiker Kelayakan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="rsModalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-success" role="status"></div>
                    <div class="text-muted small mt-2">Memuat detail...</div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-success btn-sm text-white" id="btnKonfirmasiRS">
                    <i class="bi bi-patch-check me-1"></i>Terbitkan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pengajuan Ulang (untuk tidak_lulus, ditolak_ktt, ditolak_ohs_supt) -->
<div class="modal fade" id="modalResubmit" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-arrow-repeat me-2"></i>Ajukan Ulang Pengajuan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="resubmitInfoBox" class="alert py-2 mb-3" style="font-size:13px;"></div>
                <p class="mb-1">
                    Kendaraan: <strong id="resubmitPolisi"></strong>
                </p>
                <div class="alert alert-info py-2 mb-3" style="font-size:13px;">
                    <i class="bi bi-info-circle me-1"></i>
                    Setelah diajukan ulang, pengajuan akan langsung masuk ke antrian
                    <strong>Dept Manager</strong> untuk direview kembali.
                </div>
                <label class="form-label fw-semibold">
                    Alasan / Tindakan Perbaikan <span class="text-danger">*</span>
                </label>
                <textarea class="form-control" id="resubmitAlasan" rows="4"
                    placeholder="Jelaskan perbaikan yang telah dilakukan pada unit / alasan pengajuan ulang..."
                    maxlength="1000"></textarea>
                <small class="text-muted">
                    <span id="resubmitCharCount">0</span>/1000 karakter
                </small>
                <div class="text-danger small mt-1" id="resubmitErr"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-warning btn-sm fw-bold" id="btnKonfirmasiResubmit">
                    <i class="bi bi-send me-1"></i>Ajukan Ulang
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        // ── Init Flatpickr ────────────────────────────────────────────────
        if (typeof flatpickr !== 'undefined') {
            flatpickr('.flatpickr-date', {
                dateFormat: 'Y-m-d',
                allowInput: true
            });
        }

        // ── DataTable ─────────────────────────────────────────────────────
        var table = $('#tabelPengajuan').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= site_url('pengajuan/get_data') ?>',
                type: 'POST',
                data: function(d) {
                    d.filter_status = $('#filterStatus').val();
                    d.filter_jenis = $('#filterJenis').val();
                    d.filter_tgl_dari = $('#filterTglDari').val();
                    d.filter_tgl_sampai = $('#filterTglSampai').val();
                    d['<?= $this->security->get_csrf_token_name() ?>'] = '<?= $this->security->get_csrf_hash() ?>';
                },
                error: function() {
                    toastr.error('Gagal memuat data.');
                }
            },
            columns: [{
                    data: 'no',
                    orderable: false,
                    className: 'text-center'
                },
                {
                    data: 'id_display',
                    orderable: false
                },
                {
                    data: 'pemohon'
                },
                {
                    data: 'no_polisi',
                    className: 'text-center'
                },
                {
                    data: 'jenis_kendaraan'
                },
                {
                    data: 'unit_baru',
                    className: 'text-center',
                    orderable: false
                },
                {
                    data: 'status',
                    className: 'text-center',
                    orderable: false
                },
                {
                    data: 'tgl_pengajuan',
                    className: 'text-center'
                },
                {
                    data: 'aksi',
                    orderable: false,
                    className: 'text-center'
                },
            ],
            order: [
                [7, 'desc']
            ],
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            },
        });

        $('#btnFilter').on('click', function() {
            table.ajax.reload();
        });
        $('#btnReset').on('click', function() {
            $('#filterStatus, #filterJenis').val('');
            $('#filterTglDari, #filterTglSampai').val('');
            table.ajax.reload();
        });

        // ── Detail Modal ──────────────────────────────────────────────────
        $(document).on('click', '.btn-detail', function() {
            var id = $(this).data('id');
            $('#modalDetailLabel').html('<i class="bi bi-file-earmark-text me-2"></i>Detail #PU-' + String(id).padStart(4, '0'));
            $('#modalDetailBody').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>');
            $('#modalDetail').modal('show');
            $.getJSON('<?= site_url('pengajuan/detail') ?>/' + id, function(res) {
                if (res.status === 'success') renderDetail(res.data);
                else $('#modalDetailBody').html('<div class="alert alert-danger">' + res.message + '</div>');
            });
        });

        // ── Approve ───────────────────────────────────────────────────────
        $(document).on('click', '.btn-approve', function() {
            var id = $(this).data('id');
            var level = $(this).data('level');
            Swal.fire({
                title: 'Konfirmasi Persetujuan',
                text: 'Setujui pengajuan #PU-' + String(id).padStart(4, '0') + '?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-check-lg me-1"></i>Ya, Setujui',
                cancelButtonText: 'Batal',
            }).then(function(r) {
                if (r.isConfirmed) doApproval(id, level, 'approve', '', '');
            });
        });

        // ── Reject ────────────────────────────────────────────────────────
        $(document).on('click', '.btn-reject', function() {
            var id = $(this).data('id');
            var level = $(this).data('level');
            Swal.fire({
                title: 'Konfirmasi Penolakan',
                html: '<p class="text-muted">Alasan penolakan pengajuan <strong>#PU-' + String(id).padStart(4, '0') + '</strong>:</p>' +
                    '<textarea id="catatanTolak" class="form-control mt-2" rows="3" placeholder="Tulis alasan..."></textarea>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-x-lg me-1"></i>Tolak',
                cancelButtonText: 'Batal',
                preConfirm: function() {
                    var c = document.getElementById('catatanTolak').value.trim();
                    if (!c) {
                        Swal.showValidationMessage('Alasan penolakan wajib diisi!');
                        return false;
                    }
                    return c;
                }
            }).then(function(r) {
                if (r.isConfirmed) doApproval(id, level, 'reject', r.value, '');
            });
        });

        // ── Release Stiker ────────────────────────────────────────────────
        var rsId = null;
        var modalRS = new bootstrap.Modal(document.getElementById('modalReleaseStiker'));

        function renderStikerModalBody(res) {
            var d = res.data;
            var tipeLabel = d.tipe_pengajuan === 'new_commissioning' ? 'New Commissioning' : 'Recommissioning';
            return '<div class="row g-3 mb-3">' +
                '<div class="col-md-6"><div class="bg-light rounded p-3">' +
                '<h6 class="fw-bold text-primary mb-2"><i class="bi bi-truck me-2"></i>Informasi Kendaraan</h6>' +
                '<table class="table table-sm table-borderless mb-0">' +
                '<tr><td class="text-muted fw-semibold" style="width:42%">No. Polisi</td><td><span class="badge bg-dark font-monospace">' + (d.no_polisi || '—') + '</span></td></tr>' +
                '<tr><td class="text-muted fw-semibold">Jenis</td><td><strong>' + (d.jenis_kendaraan || '—') + '</strong></td></tr>' +
                '<tr><td class="text-muted fw-semibold">Merk / Tipe</td><td>' + (d.merk || '') + ' ' + (d.tipe_kendaraan || '') + '</td></tr>' +
                '<tr><td class="text-muted fw-semibold">Tahun</td><td>' + (d.tahun || '—') + '</td></tr>' +
                '<tr><td class="text-muted fw-semibold">Nomor Unit</td><td>' + (d.nomor_unit || '—') + '</td></tr>' +
                '<tr><td class="text-muted fw-semibold">Perusahaan</td><td>' + (d.perusahaan || '—') + '</td></tr>' +
                '<tr><td class="text-muted fw-semibold">No. Rangka</td><td class="small">' + (d.nomor_rangka || '—') + '</td></tr>' +
                '<tr><td class="text-muted fw-semibold">No. Mesin</td><td class="small">' + (d.nomor_mesin || '—') + '</td></tr>' +
                '</table></div></div>' +
                '<div class="col-md-6"><div class="bg-light rounded p-3 mb-2">' +
                '<h6 class="fw-bold text-success mb-2"><i class="bi bi-person me-2"></i>Pemohon</h6>' +
                '<strong>' + (d.nama_pemohon || '—') + '</strong><br>' +
                '<span class="text-muted small">' + (d.email_pemohon || '—') + '</span>' +
                '<div class="mt-2"><span class="badge bg-info text-white me-1">' + tipeLabel + '</span>' +
                '<span class="badge bg-secondary text-white">' + (d.tipe_akses || '') + '</span></div>' +
                '</div>' +
                (res.tgl_expired ? '<div class="alert alert-info py-2 small mb-0"><i class="bi bi-calendar-check me-1"></i><strong>Expired:</strong> ' + res.tgl_expired + ' (6 bulan dari ACC KTT)</div>' : '') +
                '</div></div>' +
                '<hr class="my-2">' +
                '<label class="form-label fw-semibold">Nomor Stiker <span class="text-danger">*</span></label>' +
                '<input type="text" class="form-control" id="rsNomor" placeholder="Contoh: STK-2026-0001" maxlength="50">' +
                '<small class="text-muted">Email notifikasi dikirim ke Admin Departemen setelah stiker diterbitkan.</small>';
        }

        $(document).on('click', '.btn-release-stiker', function() {
            rsId = $(this).data('id');
            $('#rsModalBody').html('<div class="text-center py-4"><div class="spinner-border text-success" role="status"></div><div class="text-muted small mt-2">Memuat detail...</div></div>');
            modalRS.show();
            var post = {};
            post['<?= $this->security->get_csrf_token_name() ?>'] = '<?= $this->security->get_csrf_hash() ?>';
            post.id_pengajuan = rsId;
            $.ajax({
                url: '<?= site_url('approval/get_detail_stiker') ?>',
                type: 'POST',
                data: post,
                dataType: 'json',
                success: function(res) {
                    if (!res || res.status !== 'success') {
                        $('#rsModalBody').html('<div class="alert alert-danger">Gagal memuat detail.</div>');
                        return;
                    }
                    $('#rsModalBody').html(renderStikerModalBody(res));
                    $('#rsNomor').focus();
                },
                error: function() {
                    $('#rsModalBody').html('<div class="alert alert-danger">Gagal memuat detail kendaraan.</div>');
                }
            });
        });

        $('#btnKonfirmasiRS').on('click', function() {
            var nomor = $('#rsNomor').val().trim();
            if (!nomor) {
                toastr.warning('Nomor stiker wajib diisi.');
                $('#rsNomor').focus();
                return;
            }
            modalRS.hide();
            doApproval(rsId, 'release_stiker', 'approve', '', nomor);
        });

        // ── Kirim AJAX Approval ───────────────────────────────────────────
        function doApproval(id, level, aksi, catatan, nomor_stiker) {
            NProgress.start();
            $.ajax({
                url: '<?= site_url('approval/proses') ?>',
                type: 'POST',
                data: {
                    <?= $this->security->get_csrf_token_name() ?>: '<?= $this->security->get_csrf_hash() ?>',
                    id_pengajuan: id,
                    level: level,
                    aksi: aksi,
                    catatan: catatan,
                    nomor_stiker: nomor_stiker,
                },
                dataType: 'json',
                success: function(res) {
                    NProgress.done();
                    if (res.status === 'success') {
                        if (res.redirect_jadwal) {
                            Swal.fire({
                                    title: 'Disetujui!',
                                    html: res.message + '<br><small>Silakan buat jadwal.</small>',
                                    icon: 'success',
                                    confirmButtonText: 'Buat Jadwal'
                                })
                                .then(function() {
                                    window.location.href = res.redirect_jadwal;
                                });
                            return;
                        }
                        Swal.fire({
                            icon: aksi === 'approve' ? 'success' : 'warning',
                            title: aksi === 'approve' ? 'Disetujui!' : 'Ditolak',
                            html: res.message,
                            timer: 1800,
                            showConfirmButton: false
                        });
                        setTimeout(function() {
                            table.ajax.reload(null, false);
                        }, 1900);
                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            html: res.message,
                            icon: 'error'
                        });
                    }
                },
                error: function() {
                    NProgress.done();
                    toastr.error('Terjadi kesalahan server.');
                }
            });
        }

        // ── Render Detail Modal ───────────────────────────────────────────
        // BUG FIX #1: renderDetail sekarang menjadi function declaration
        // yang bisa dipanggil dari mana saja, bukan function expression
        // yang bermasalah dengan hoisting di dalam $(function(){})
        function renderDetail(d) {
            var baseUrl = '<?= base_url() ?>';
            var siteUrl = '<?= site_url() ?>';

            var statusLabel = {
                draft: 'Draft',
                pengajuan_baru: 'Pengajuan Baru',
                pengajuan_ulang: 'Pengajuan Ulang',
                diterima_manager: 'Diterima Manager',
                ditolak_manager: 'Ditolak Manager',
                dijadwalkan: 'Dijadwalkan Inspeksi',
                lulus_inspeksi: 'Lulus — Menunggu OHS Supt',
                tidak_lulus_inspeksi: 'Tidak Lulus — Dikembalikan',
                selesai_inspeksi: 'Selesai Inspeksi',
                diterima_admin_ohs: 'Diterima Admin OHS',
                ditolak_admin_ohs: 'Ditolak Admin OHS',
                diterima_ohs_supt: 'Diterima OHS Superintendent',
                ditolak_ohs_supt: 'Ditolak OHS Superintendent',
                acc_ktt: 'Disetujui KTT',
                ditolak_ktt: 'Ditolak KTT',
                stiker_keluar: 'Stiker Sudah Keluar',
                rejected: 'Ditolak'
            };
            var statusClass = {
                draft: 'secondary text-white',
                pengajuan_baru: 'primary text-white',
                pengajuan_ulang: 'info text-white',
                diterima_manager: 'warning text-dark',
                ditolak_manager: 'danger text-white',
                dijadwalkan: 'primary text-white',
                lulus_inspeksi: 'success text-white',
                tidak_lulus_inspeksi: 'danger text-white',
                selesai_inspeksi: 'warning text-dark',
                diterima_admin_ohs: 'info text-white',
                ditolak_admin_ohs: 'danger text-white',
                diterima_ohs_supt: 'info text-white',
                ditolak_ohs_supt: 'danger text-white',
                acc_ktt: 'success text-white',
                ditolak_ktt: 'danger text-white',
                stiker_keluar: 'success text-white',
                rejected: 'danger text-white'
            };
            var sc = statusClass[d.status] || 'secondary text-white';
            var sl = statusLabel[d.status] || d.status;

            var ditolakStatuses = ['ditolak_manager', 'ditolak_admin_ohs', 'ditolak_ohs_supt', 'ditolak_ktt', 'tidak_lulus_inspeksi'];
            var alertDitolak = '';
            if (ditolakStatuses.indexOf(d.status) >= 0) {
                var msgDitolak = d.status === 'tidak_lulus_inspeksi' ?
                    '<strong>Unit tidak lulus inspeksi.</strong> Lakukan perbaikan unit lalu ajukan kembali.' :
                    '<strong>Pengajuan ini dikembalikan.</strong> Periksa catatan di riwayat approval.';
                alertDitolak = '<div class="alert alert-danger py-2 mb-0 mt-2" style="font-size:13px;">' +
                    '<i class="bi bi-exclamation-triangle-fill me-2"></i>' + msgDitolak + '</div>';
            }

            var unitBadge = d.is_unit_baru == 1 ?
                '<span class="badge bg-warning text-dark"><i class="bi bi-star-fill me-1"></i>Unit Baru</span>' :
                '<span class="badge bg-secondary">Unit Lama</span>';

            var tipeLabel = d.tipe_pengajuan === 'new_commissioning' ? 'New Commissioning' : 'Recommissioning';

            function valOrDash(v) {
                return (v && String(v).trim()) ? v : '—';
            }

            // ── Info Kendaraan ────────────────────────────────────────────
            var kendaraanHtml =
                '<div class="card border-0 bg-light h-100"><div class="card-body">' +
                '<h6 class="fw-bold text-primary mb-3"><i class="bi bi-truck me-2"></i>Informasi Kendaraan</h6>' +
                '<div class="row g-2">' +
                '<div class="col-6"><small class="text-muted d-block">No. Polisi</small><span class="badge bg-dark font-monospace fs-6">' + valOrDash(d.no_polisi) + '</span></div>' +
                '<div class="col-6"><small class="text-muted d-block">Tipe Unit</small>' + unitBadge + '</div>' +
                '<div class="col-6"><small class="text-muted d-block">Jenis Kendaraan</small><strong class="small">' + valOrDash(d.jenis_kendaraan) + '</strong></div>' +
                '<div class="col-6"><small class="text-muted d-block">Merk</small><strong class="small">' + valOrDash(d.merk) + '</strong></div>' +
                '<div class="col-6"><small class="text-muted d-block">Tipe / Model</small><strong class="small">' + valOrDash(d.tipe) + '</strong></div>' +
                '<div class="col-6"><small class="text-muted d-block">Tahun</small><strong class="small">' + valOrDash(d.tahun) + '</strong></div>' +
                '<div class="col-6"><small class="text-muted d-block">Nomor Unit</small><strong class="small">' + valOrDash(d.nomor_unit) + '</strong></div>' +
                '<div class="col-6"><small class="text-muted d-block">Perusahaan</small><strong class="small">' + valOrDash(d.perusahaan) + '</strong></div>' +
                '<div class="col-6"><small class="text-muted d-block">Nomor Rangka</small><span class="small font-monospace">' + valOrDash(d.nomor_rangka) + '</span></div>' +
                '<div class="col-6"><small class="text-muted d-block">Nomor Mesin</small><span class="small font-monospace">' + valOrDash(d.nomor_mesin) + '</span></div>' +
                '<div class="col-12"><small class="text-muted d-block">Tipe Pengajuan</small><span class="badge bg-primary text-white">' + tipeLabel + '</span></div>' +
                '</div></div></div>';

            // ── Info Pemohon ──────────────────────────────────────────────
            var pemohonHtml =
                '<div class="card border-0 bg-light h-100"><div class="card-body">' +
                '<h6 class="fw-bold text-success mb-3"><i class="bi bi-person me-2"></i>Informasi Pemohon</h6>' +
                '<div class="row g-2">' +
                '<div class="col-12"><small class="text-muted d-block">Nama</small><strong class="small">' + valOrDash(d.nama_pemohon) + '</strong></div>' +
                '<div class="col-12"><small class="text-muted d-block">Email</small><strong class="small">' + valOrDash(d.email_pemohon || d.email_user) + '</strong></div>' +
                '<div class="col-6"><small class="text-muted d-block">Tgl Pengajuan</small><strong class="small">' + (d.tanggal_pengajuan ? d.tanggal_pengajuan.substr(0, 16) : '—') + '</strong></div>' +
                '<div class="col-6"><small class="text-muted d-block">Status</small><span class="badge bg-' + sc + '">' + sl + '</span></div>' +
                '<div class="col-12"><small class="text-muted d-block">Tujuan</small><span class="small">' + valOrDash(d.tujuan) + '</span></div>' +
                '</div></div></div>';

            // ── Lampiran ──────────────────────────────────────────────────
            var jenisLabel = {
                stnk: 'STNK',
                unit_depan: 'Depan',
                unit_belakang: 'Belakang',
                unit_kiri: 'Kiri',
                unit_kanan: 'Kanan',
                maintenance_record: 'Maintenance'
            };
            var lampiranHtml = '';
            if (d.lampiran && d.lampiran.length > 0) {
                $.each(d.lampiran, function(i, l) {
                    var ext = l.file_path.split('.').pop().toLowerCase();
                    var isImg = ['jpg', 'jpeg', 'png', 'webp'].indexOf(ext) >= 0;
                    var preview = isImg ?
                        '<a href="' + baseUrl + l.file_path + '" target="_blank"><img src="' + baseUrl + l.file_path + '" class="img-fluid rounded mb-1" style="height:80px;width:100%;object-fit:cover;"></a>' :
                        '<a href="' + baseUrl + l.file_path + '" target="_blank" class="d-flex align-items-center justify-content-center" style="height:80px;"><i class="bi bi-file-earmark-pdf text-danger fs-1"></i></a>';
                    lampiranHtml += '<div class="col-6 col-md-4 col-lg-2"><div class="border rounded text-center p-2">' + preview + '<div class="small text-muted fw-semibold mt-1">' + (jenisLabel[l.jenis_lampiran] || l.jenis_lampiran) + '</div></div></div>';
                });
            } else {
                lampiranHtml = '<div class="col-12"><p class="text-muted small mb-0"><i class="bi bi-dash-circle me-1"></i>Tidak ada lampiran.</p></div>';
            }

            // ── Jadwal ────────────────────────────────────────────────────
            var jadwalHtml = d.jadwal ?
                '<div class="row g-3">' +
                '<div class="col-md-4"><small class="text-muted d-block">Tanggal Uji</small><strong>' + valOrDash(d.jadwal.tanggal_uji) + '</strong></div>' +
                '<div class="col-md-4"><small class="text-muted d-block">Lokasi</small><strong>' + valOrDash(d.jadwal.lokasi) + '</strong></div>' +
                '<div class="col-md-4"><small class="text-muted d-block">Dibuat oleh</small><strong>' + valOrDash(d.jadwal.dibuat_oleh_nama) + '</strong></div>' +
                '<div class="col-md-6"><small class="text-muted d-block"><i class="bi bi-tools me-1 text-warning"></i>Mekanik Lapangan</small><strong>' + valOrDash(d.jadwal.nama_mekanik_master) + '</strong>' + (d.jadwal.perusahaan_mekanik ? '<br><small class="text-muted">' + d.jadwal.perusahaan_mekanik + '</small>' : '') + '</div>' +
                '<div class="col-md-6"><small class="text-muted d-block"><i class="bi bi-person-badge me-1 text-primary"></i>Inspektor</small><strong>' + valOrDash(d.jadwal.nama_inspektor_user) + '</strong></div>' +
                '</div>' :
                '<div class="text-center py-3 text-muted"><i class="bi bi-calendar-x fs-3 d-block mb-1 opacity-50"></i><small>Belum dijadwalkan.</small></div>';

            // ── Hasil Uji ─────────────────────────────────────────────────
            var ujiHtml = '';
            if (d.uji) {
                var hasilOk = (d.uji.hasil === 'lulus');
                // Tombol checklist + tombol riwayat (jika ada perbaikan)
                var ujiButtons = '<a href="' + siteUrl + '/checklist/detail/' + d.uji.id_uji + '" target="_blank" class="btn btn-sm btn-outline-info"><i class="bi bi-clipboard2-check me-1"></i>Checklist</a>';
                if (d.perbaikan && d.perbaikan.length > 0) {
                    ujiButtons += ' <a href="' + siteUrl + '/checklist/detail/' + d.uji.id_uji + '#sectionHistoryInspeksi" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-clock-history me-1"></i>Riwayat <span class="badge bg-warning text-dark ms-1">' + d.perbaikan.length + '</span></a>';
                }
                ujiHtml =
                    '<div class="row g-3 align-items-center">' +
                    '<div class="col-md-3"><small class="text-muted d-block"><i class="bi bi-person-badge me-1"></i>Inspektor</small><strong>' + valOrDash(d.uji.nama_inspektor || d.uji.nama_mekanik) + '</strong>' + (d.uji.perusahaan_inspektor ? '<br><small class="text-muted">' + d.uji.perusahaan_inspektor + '</small>' : '') + '</div>' +
                    '<div class="col-md-3"><small class="text-muted d-block">Tanggal</small><strong>' + valOrDash(d.uji.updated_at || d.uji.created_at) + '</strong></div>' +
                    '<div class="col-md-2"><small class="text-muted d-block">Hasil</small><span class="badge bg-' + (hasilOk ? 'success' : 'danger') + ' text-white fs-6 px-3">' + (hasilOk ? 'LULUS' : 'TIDAK LULUS') + '</span></div>' +
                    '<div class="col-md-2"><small class="text-muted d-block">Catatan Temuan</small><span class="small">' + valOrDash(d.uji.catatan_temuan || d.uji.catatan_umum) + '</span></div>' +
                    '<div class="col-md-2 text-md-end d-flex flex-column gap-1 align-items-end">' + ujiButtons + '</div>' +
                    '</div>';
            } else {
                ujiHtml = '<div class="text-center py-3 text-muted"><i class="bi bi-clipboard-x fs-3 d-block mb-1 opacity-50"></i><small>Belum ada hasil inspeksi.</small></div>';
            }

            // ── Riwayat Approval ──────────────────────────────────────────
            var levelLabel = {
                dept_manager: 'Dept Manager',
                admin_ohs: 'Admin OHS',
                admin_ohs_hasil: 'Admin OHS (Hasil)',
                ohs_supt: 'OHS Superintendent',
                ktt: 'KTT',
                release_stiker: 'Release Stiker',
                perbaikan_unit: 'Perbaikan Unit',
                resubmit_admin_dept: 'Resubmit Admin Dept',
                edit_admin_dept: 'Edit Admin Dept',
                manager: 'Manager',
                admin: 'Admin OHS'
            };
            var approvalHtml = '';
            if (d.approval && d.approval.length > 0) {
                $.each(d.approval, function(i, a) {
                    var ac = a.status === 'approved' ? 'success' : (a.status === 'rejected' ? 'danger' : 'secondary');
                    var al = a.status === 'approved' ? 'Disetujui' : (a.status === 'rejected' ? 'Ditolak' : 'Pending');
                    approvalHtml += '<tr><td><span class="badge bg-light text-dark border">' + (levelLabel[a.level_approval] || a.level_approval) + '</span></td><td>' + (a.nama_approver || '<em class="text-muted small">Belum ditentukan</em>') + '</td><td><span class="badge bg-' + ac + '">' + al + '</span></td><td class="text-muted small">' + (a.created_at ? a.created_at.substr(0, 16) : '—') + '</td><td class="text-muted small">' + valOrDash(a.catatan) + '</td></tr>';
                });
            } else {
                approvalHtml = '<tr><td colspan="5" class="text-center text-muted py-3">Belum ada data approval.</td></tr>';
            }

            // ── Perbaikan Unit ────────────────────────────────────────────
            // BUG FIX #2: renderPerbaikan dipanggil di DALAM renderDetail,
            // bukan di luar scope-nya seperti versi sebelumnya
            var perbaikanSection = '';
            if (d.perbaikan && d.perbaikan.length > 0) {
                perbaikanSection = '<div class="mt-3">' + renderPerbaikan(d.perbaikan, baseUrl) + '</div>';
            }

            // ── Susun HTML akhir ──────────────────────────────────────────
            // BUG FIX #3: perbaikanSection dan histBtn digabung di SINI,
            // bukan di luar function renderDetail() seperti versi sebelumnya
            var html =
                '<div class="row g-3 mb-3">' +
                '<div class="col-md-6">' + kendaraanHtml + '</div>' +
                '<div class="col-md-6">' + pemohonHtml + '</div>' +
                '</div>' +
                alertDitolak +
                '<div class="card border mb-3"><div class="card-header bg-white py-2"><i class="bi bi-images text-primary me-2"></i><strong class="small">Lampiran Dokumen</strong></div><div class="card-body py-3"><div class="row g-2">' + lampiranHtml + '</div></div></div>' +
                '<div class="card border mb-3"><div class="card-header bg-white py-2"><i class="bi bi-calendar-event text-primary me-2"></i><strong class="small">Jadwal Uji Kelayakan</strong></div><div class="card-body py-3">' + jadwalHtml + '</div></div>' +
                '<div class="card border mb-3"><div class="card-header bg-white py-2"><i class="bi bi-clipboard2-check text-primary me-2"></i><strong class="small">Hasil Uji Kelayakan</strong></div><div class="card-body py-3">' + ujiHtml + '</div></div>' +
                '<div class="card border mb-0"><div class="card-header bg-white py-2"><i class="bi bi-check2-all text-primary me-2"></i><strong class="small">Riwayat Approval</strong></div><div class="card-body p-0"><div class="table-responsive"><table class="table table-sm table-hover align-middle mb-0"><thead class="table-light"><tr><th>Level</th><th>Approver</th><th>Status</th><th>Tanggal</th><th>Catatan</th></tr></thead><tbody>' + approvalHtml + '</tbody></table></div></div></div>' +
                perbaikanSection;

            $('#modalDetailBody').html(html);
        }

        // ── Modal Resubmit ────────────────────────────────────────────────
        var resubmitId = null;
        var modalResubmit = new bootstrap.Modal(document.getElementById('modalResubmit'));

        var resubmitInfoMap = {
            'tidak_lulus_inspeksi': 'Kendaraan tidak lulus uji kelayakan. Jelaskan perbaikan yang telah dilakukan sebelum mengajukan ulang.',
            'ditolak_ktt': 'Pengajuan ditolak oleh KTT. Jelaskan tindakan perbaikan atau klarifikasi yang telah dilakukan.',
            'ditolak_ohs_supt': 'Pengajuan ditolak oleh OHS Superintendent. Jelaskan tindakan perbaikan yang telah dilakukan.',
        };
        var resubmitAlertClass = {
            'tidak_lulus_inspeksi': 'alert-danger',
            'ditolak_ktt': 'alert-warning',
            'ditolak_ohs_supt': 'alert-warning',
        };

        $(document).on('click', '.btn-resubmit', function() {
            resubmitId = $(this).data('id');
            var polisi = $(this).data('polisi');
            var status = $(this).data('status');
            var infoText = resubmitInfoMap[status] || 'Jelaskan alasan pengajuan ulang.';
            var alertClass = resubmitAlertClass[status] || 'alert-info';
            $('#resubmitPolisi').text(polisi);
            $('#resubmitAlasan').val('');
            $('#resubmitErr').text('');
            $('#resubmitCharCount').text('0');
            $('#resubmitInfoBox').removeClass('alert-danger alert-warning alert-info').addClass(alertClass).html('<i class="bi bi-exclamation-triangle-fill me-2"></i>' + infoText);
            modalResubmit.show();
        });

        $(document).on('input', '#resubmitAlasan', function() {
            $('#resubmitCharCount').text($(this).val().length);
            $('#resubmitErr').text('');
        });

        $('#btnKonfirmasiResubmit').on('click', function() {
            var alasan = $('#resubmitAlasan').val().trim();
            if (!alasan) {
                $('#resubmitErr').text('Alasan pengajuan ulang wajib diisi.');
                $('#resubmitAlasan').focus();
                return;
            }
            if (alasan.length < 10) {
                $('#resubmitErr').text('Alasan terlalu singkat (minimal 10 karakter).');
                return;
            }

            var $btn = $(this);
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Memproses...');
            var post = {};
            post['<?= $this->security->get_csrf_token_name() ?>'] = '<?= $this->security->get_csrf_hash() ?>';
            post.id_pengajuan = resubmitId;
            post.alasan_pengajuan_ulang = alasan;
            NProgress.start();
            $.ajax({
                url: '<?= site_url('pengajuan/resubmit') ?>',
                type: 'POST',
                data: post,
                dataType: 'json',
                success: function(res) {
                    NProgress.done();
                    $btn.prop('disabled', false).html('<i class="bi bi-send me-1"></i>Ajukan Ulang');
                    if (res.status === 'success') {
                        modalResubmit.hide();
                        Swal.fire({
                            title: 'Berhasil Diajukan Ulang!',
                            html: res.message,
                            icon: 'success',
                            confirmButtonColor: '#4154f1'
                        }).then(function() {
                            table.ajax.reload(null, false);
                        });
                    } else {
                        $('#resubmitErr').html(res.message);
                    }
                },
                error: function() {
                    NProgress.done();
                    $btn.prop('disabled', false).html('<i class="bi bi-send me-1"></i>Ajukan Ulang');
                    toastr.error('Terjadi kesalahan server.');
                }
            });
        });

    }); // end $(function)

    // ── renderPerbaikan — di luar $(function) agar bisa dipanggil dari renderDetail ──
    // BUG FIX #4: baseUrl diterima sebagai parameter, bukan closure capture
    function renderPerbaikan(perbaikanArr, baseUrl) {
        if (!perbaikanArr || perbaikanArr.length === 0) return '';

        // Fallback baseUrl jika tidak dikirim sebagai argumen
        if (!baseUrl) baseUrl = '<?= base_url() ?>';

        var rows = '';
        $.each(perbaikanArr, function(i, pb) {
            var statusMap = {
                menunggu: ['bg-secondary', 'Menunggu'],
                selesai: ['bg-info text-white', 'Selesai'],
                diverifikasi: ['bg-success text-white', 'Diverifikasi ✓'],
            };
            var sc = statusMap[pb.status] || ['bg-light text-dark', pb.status];
            var tgl_maks = pb.tgl_max_perbaikan || '';
            var tgl_sel = pb.tgl_selesai || '';

            var badgeTepat = '';
            if (tgl_maks && tgl_sel) {
                var maks = new Date(tgl_maks);
                var sel = new Date(tgl_sel);
                var sisa = Math.ceil((maks - sel) / 86400000);
                badgeTepat = sisa >= 0 ?
                    '<span class="badge bg-success ms-1" style="font-size:10px;"><i class="bi bi-clock me-1"></i>Tepat Waktu</span>' :
                    '<span class="badge bg-danger ms-1" style="font-size:10px;">Terlambat ' + Math.abs(sisa) + ' hari</span>';
            }

            var lampiranHtml = '';
            if (pb.lampiran && pb.lampiran.length > 0) {
                var thumbs = '';
                $.each(pb.lampiran, function(j, l) {
                    var ext = l.file_path.split('.').pop().toLowerCase();
                    var imgExts = ['jpg', 'jpeg', 'png', 'webp'];
                    if (imgExts.indexOf(ext) >= 0) {
                        thumbs += '<div class="col-4 col-md-2"><a href="' + baseUrl + l.file_path + '" target="_blank"><img src="' + baseUrl + l.file_path + '" class="img-fluid rounded w-100" style="height:70px;object-fit:cover;" onerror="this.src=\'' + baseUrl + 'assets/img/img-error.png\'"></a></div>';
                    } else {
                        var icon = ext === 'pdf' ? 'bi-file-earmark-pdf text-danger' : (ext === 'doc' || ext === 'docx' ? 'bi-file-earmark-word text-primary' : 'bi-file-earmark text-secondary');
                        thumbs += '<div class="col-4 col-md-2"><a href="' + baseUrl + l.file_path + '" target="_blank" class="d-flex flex-column align-items-center justify-content-center border rounded bg-light text-muted text-decoration-none" style="height:70px;"><i class="bi ' + icon + ' fs-3"></i><span style="font-size:9px;">File</span></a></div>';
                    }
                });
                lampiranHtml = '<div class="mt-2 small fw-semibold text-muted mb-1"><i class="bi bi-paperclip me-1"></i>Bukti Perbaikan (' + pb.lampiran.length + ' file):</div><div class="row g-1">' + thumbs + '</div>';
            } else {
                lampiranHtml = '<div class="small text-muted fst-italic mt-1"><i class="bi bi-images me-1"></i>Tidak ada bukti perbaikan.</div>';
            }

            var verif = pb.nama_verifikator ?
                '<div class="small text-muted mb-1"><i class="bi bi-person-check me-1 text-primary"></i>Verifikator: <strong>' + pb.nama_verifikator + '</strong></div>' :
                '';

            var fmtDate = function(s) {
                if (!s) return '';
                var d = new Date(s);
                return d.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            };

            rows += '<div class="p-3 ' + (i > 0 ? 'border-top' : '') + '">' +
                '<div class="d-flex align-items-start justify-content-between gap-2 mb-2 flex-wrap">' +
                '<div class="d-flex align-items-center gap-2">' +
                '<div class="rounded-circle bg-warning d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0" style="width:26px;height:26px;font-size:.75rem;">' + (i + 1) + '</div>' +
                '<div><span class="fw-semibold small">Perbaikan #' + pb.id_perbaikan + '</span>' +
                '<div class="d-flex gap-1 flex-wrap mt-1"><span class="badge ' + sc[0] + '" style="font-size:10px;">' + sc[1] + '</span>' + badgeTepat + '</div></div></div>' +
                '<div class="text-end small text-muted">' +
                (tgl_maks ? '<div><i class="bi bi-calendar-x text-danger me-1"></i>Deadline: <strong>' + fmtDate(tgl_maks) + '</strong></div>' : '') +
                (tgl_sel ? '<div><i class="bi bi-calendar-check text-success me-1"></i>Selesai: <strong>' + fmtDate(tgl_sel) + '</strong></div>' : '') +
                '</div></div>' +
                (pb.catatan_perbaikan ?
                    '<div class="alert alert-light border py-2 mb-2 small"><i class="bi bi-chat-left-text me-1 text-warning"></i><strong>Catatan:</strong> ' + pb.catatan_perbaikan + '</div>' :
                    '<p class="text-muted small mb-2 fst-italic"><i class="bi bi-dash me-1"></i>Tidak ada catatan perbaikan.</p>') +
                verif + lampiranHtml + '</div>';
        });

        return '<div class="card border-warning mb-0">' +
            '<div class="card-header bg-warning bg-opacity-10 border-warning py-2 d-flex align-items-center justify-content-between">' +
            '<span class="fw-bold text-warning small"><i class="bi bi-tools me-2"></i>Riwayat Perbaikan Unit</span>' +
            '<span class="badge bg-warning text-dark">' + perbaikanArr.length + ' entri</span>' +
            '</div><div class="card-body p-0">' + rows + '</div></div>';
    }
</script>