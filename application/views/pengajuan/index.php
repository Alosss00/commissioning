<script src="<?= base_url('assets/vendor/xlsx/xlsx.full.min.js') ?>"></script>

<main id="main" class="main">

    <div class="pagetitle">
        <h1>Daftar Pengajuan Uji Kelayakan</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Home</a></li>
                <li class="breadcrumb-item active">Daftar Pengajuan</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body pt-4">

                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <h5 class="card-title p-0 m-0">Pengajuan Uji Kelayakan (Commissioning)</h5>
                            <?php
                            $roles_sess = $this->session->userdata('roles');
                            $role_int   = (int)$this->session->userdata('role');
                            $_r = is_array($roles_sess) ? array_map('intval', $roles_sess) : ($role_int > 0 ? [$role_int] : []);
                            $canCreate       = in_array(1, $_r) || in_array(7, $_r);
                            $isAdminDeptOnly = in_array(7, $_r) && !in_array(1, $_r);
                            ?>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-success btn-sm fw-semibold" id="btnExportExcelHistory">
                                    <i class="bi bi-file-earmark-excel me-1"></i>Export History Excel
                                </button>
                                <?php if ($canCreate): ?>
                                    <a href="<?= site_url('pengajuan/create') ?>" class="btn btn-primary btn-sm fw-semibold">
                                        <i class="bi bi-plus-circle me-1"></i>Buat Pengajuan
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Filter -->
                        <div class="row g-2 mb-3">
                            <div class="col-sm-6 col-md-3">
                                <select class="form-select form-select-sm" id="filterStatus">
                                    <option value="">— <?= !empty($allowed_statuses) ? 'Semua Status Tahap Ini' : 'Semua Status Pengajuan' ?> —</option>
                                    <?php
                                    $status_list = [
                                        'draft'                => 'Draft',
                                        'pengajuan_baru'       => 'Pengajuan Baru',
                                        'pengajuan_ulang'      => 'Pengajuan Ulang',
                                        'diterima_manager'     => 'Diterima Manager',
                                        'ditolak_manager'      => 'Ditolak Manager',
                                        'dijadwalkan'          => 'Dijadwalkan Inspeksi',
                                        'inspeksi_ulang'       => 'Siap Inspeksi Ulang',
                                        'selesai_inspeksi'     => 'Selesai Inspeksi',
                                        'lulus_inspeksi'       => 'Lulus — Menunggu OHS Supt',
                                        'tidak_lulus_inspeksi' => 'Tidak Lulus — Dikembalikan',
                                        'diterima_admin_ohs'   => 'Diterima Admin OHS',
                                        'ditolak_admin_ohs'    => 'Ditolak Admin OHS',
                                        'diterima_ohs_supt'    => 'Diterima OHS Superintendent',
                                        'ditolak_ohs_supt'     => 'Ditolak OHS Superintendent',
                                        'acc_ktt'              => 'Disetujui KTT',
                                        'ditolak_ktt'          => 'Ditolak KTT',
                                        'stiker_keluar'        => 'Stiker Sudah Keluar',
                                        'rejected'             => 'Ditolak',
                                    ];
                                    foreach ($status_list as $k => $lbl):
                                        if (!empty($allowed_statuses) && !in_array($k, $allowed_statuses, true)) continue;
                                    ?>
                                        <option value="<?= $k ?>"><?= $lbl ?></option>
                                    <?php endforeach; ?>
                                    <?php if (empty($allowed_statuses)): ?>
                                        <option value="trash" class="text-danger fw-bold">🗑 Data Terhapus (Sampah / Soft Delete)</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <?php if (!empty($user_dept) && !($is_site_wide ?? false)): ?>
                                    <select class="form-select form-select-sm border-primary text-primary fw-bold" id="filterPerusahaan" disabled title="Terkunci berdasarkan departemen akun Anda">
                                        <option value="<?= html_escape($user_dept) ?>" selected>Dept: <?= html_escape($user_dept) ?></option>
                                    </select>
                                <?php else: ?>
                                    <select class="form-select form-select-sm" id="filterPerusahaan">
                                        <option value="">— Semua Perusahaan / Dept —</option>
                                        <?php if (!empty($perusahaan)): ?>
                                            <?php foreach ($perusahaan as $p): ?>
                                                <option value="<?= html_escape($p->nama_perusahaan) ?>"><?= html_escape($p->nama_perusahaan) ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                <?php endif; ?>
                            </div>
                            <div class="col-sm-6 col-md-2">
                                <select class="form-select form-select-sm" id="filterJenis">
                                    <option value="">— Semua Tipe Unit —</option>
                                    <?php if (!empty($tipe_unit)): ?>
                                        <?php foreach ($tipe_unit as $tu): ?>
                                            <option value="<?= html_escape($tu->nama_tipe) ?>"><?= html_escape($tu->nama_tipe) ?></option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option>Light Vehicle</option>
                                        <option>Light Truck</option>
                                        <option>Bus</option>
                                        <option>Dump Truck</option>
                                        <option>Excavator</option>
                                        <option>Bulldozer</option>
                                        <option>Motor Grader</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-sm-6 col-md-2">
                                <input type="text" class="form-control form-control-sm flatpickr-date" id="filterTglDari" placeholder="Dari Tanggal">
                            </div>
                            <div class="col-sm-6 col-md-2">
                                <input type="text" class="form-control form-control-sm flatpickr-date" id="filterTglSampai" placeholder="Sampai Tanggal">
                            </div>
                            <div class="col-sm-12 col-md-12 d-flex justify-content-end gap-2 mt-2">
                                <button class="btn btn-primary btn-sm px-3" id="btnFilter">
                                    <i class="bi bi-search me-1"></i>Filter
                                </button>
                                <button class="btn btn-outline-secondary btn-sm px-3" id="btnReset" title="Reset">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                                </button>
                            </div>
                        </div>

                        <!-- Tabel -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle w-100" id="tabelPengajuan" style="width:100%">
                                <thead class="table-light align-middle text-nowrap">
                                    <tr>
                                        <th width="35" class="text-center">No</th>
                                        <th width="85" class="text-center">ID</th>
                                        <th>Pemohon</th>
                                        <th width="90" class="text-center">No. Unit</th>
                                        <th width="100" class="text-center">No. Polisi</th>
                                        <th>Kendaraan</th>
                                        <th width="135" class="text-center">Tipe Pengajuan</th>
                                        <th width="120" class="text-center">Tipe Akses</th>
                                        <th width="130" class="text-center">Status</th>
                                        <th width="125" class="text-center">Tgl Pengajuan</th>
                                        <th width="90" class="text-center">Aksi</th>
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
    function csrfField() {
        var f = {};
        f[window.csrfTokenName] = window.csrfTokenHash;
        return f;
    }

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
            destroy: true,
            autoWidth: false,
            ajax: {
                url: '<?= site_url('pengajuan/get_data') ?>',
                type: 'POST',
                data: function(d) {
                    var deptVal         = '<?= html_escape($user_dept ?? '') ?>' || $('#filterPerusahaan').val() || '';
                    d.status            = $('#filterStatus').val();
                    d.jenis             = $('#filterJenis').val();
                    d.departemen        = deptVal;
                    d.tgl_dari          = $('#filterTglDari').val();
                    d.tgl_sampai        = $('#filterTglSampai').val();
                    d.filter_status     = $('#filterStatus').val();
                    d.filter_jenis      = $('#filterJenis').val();
                    d.filter_departemen = deptVal;
                    d.filter_tgl_dari   = $('#filterTglDari').val();
                    d.filter_tgl_sampai = $('#filterTglSampai').val();
                    d[window.csrfTokenName] = window.csrfTokenHash;
                },
                dataSrc: function(json) {
                    return json.data;
                },
                error: function(xhr) {
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
                    orderable: false,
                    className: 'text-center text-nowrap'
                },
                {
                    data: 'pemohon',
                    className: 'text-nowrap'
                },
                {
                    data: 'nomor_unit',
                    className: 'text-center text-nowrap'
                },
                {
                    data: 'no_polisi',
                    className: 'text-center text-nowrap'
                },
                {
                    data: 'jenis_kendaraan',
                    className: 'text-nowrap'
                },
                {
                    data: 'tipe_pengajuan',
                    className: 'text-center text-nowrap',
                    orderable: false
                },
                {
                    data: 'tipe_akses',
                    className: 'text-center text-nowrap',
                    orderable: false
                },
                {
                    data: 'status',
                    className: 'text-center text-nowrap',
                    orderable: false
                },
                {
                    data: 'tgl_pengajuan',
                    className: 'text-center text-nowrap'
                },
                {
                    data: 'aksi',
                    orderable: false,
                    className: 'text-center text-nowrap'
                },
            ],
            order: [],
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100, 200],
            language: {
                url: '<?= base_url("assets/vendor/datatables/id.json") ?>'
            },
        });

        $('#btnFilter').on('click', function() {
            table.ajax.reload();
        });
        $('#btnReset').on('click', function() {
            $('#filterStatus, #filterJenis, #filterPerusahaan').val('');
            $('#filterTglDari, #filterTglSampai').val('');
            table.ajax.reload();
        });

        // ── Export Excel History — format Workflow Lifecycle ─────────────────────
        $('#btnExportExcelHistory').on('click', function() {
            var $btn = $(this);
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Mengambil Data...');

            var postData = {
                status:     $('#filterStatus').val(),
                jenis:      $('#filterJenis').val(),
                departemen: $('#filterPerusahaan').val(),
                tgl_dari:   $('#filterTglDari').val(),
                tgl_sampai: $('#filterTglSampai').val(),
                search:     $('#tabelPengajuan_filter input').val() || ''
            };
            postData[window.csrfTokenName] = window.csrfTokenHash;

            $.ajax({
                url: '<?= site_url('pengajuan/get_export_history') ?>',
                type: 'POST',
                data: postData,
                dataType: 'json',
                success: function(res) {
                    $btn.prop('disabled', false).html('<i class="bi bi-file-earmark-excel me-1"></i>Export History Excel');
                    if (res && res.csrf_hash) {
                        window.csrfTokenHash = res.csrf_hash;
                    }
                    if (!res || res.status !== 'success') {
                        toastr.error(res && res.message ? res.message : 'Gagal mengambil data history.');
                        return;
                    }
                    generateHistoryPengajuanExcel(res.data);
                },
                error: function() {
                    $btn.prop('disabled', false).html('<i class="bi bi-file-earmark-excel me-1"></i>Export History Excel');
                    toastr.error('Terjadi kesalahan server saat mengekspor data.');
                }
            });
        });

        function generateHistoryPengajuanExcel(rows) {
            if (!rows || !rows.length) {
                toastr.warning('Tidak ada data history pengajuan untuk diekspor.');
                return;
            }

            var headers = [
                'No.',
                'ID Pengajuan',
                'Tanggal Pengajuan',
                'Pemohon',
                'Perusahaan / Departemen',
                'Tipe Commissioning',
                'Tipe Akses',
                'Jenis / Tipe Unit',
                'Merk Unit',
                'Model Unit',
                'Nomor Unit',
                'Nomor Polisi',
                'Status Pengajuan',
                'Approve Dept Manager',
                'Jadwal Rencana Inspeksi',
                'Inspektor / Mekanik',
                'Hasil Inspeksi Mekanik',
                'Catatan Inspeksi',
                'Approve OHS Supt',
                'Catatan OHS',
                'Nomor Stiker',
                'Tanggal Stiker Rilis',
                'Masa Berlaku Expired'
            ];

            var data = [headers];

            rows.forEach(function(r, idx) {
                var statusLabel = r.status ? r.status.replace(/_/g, ' ').toUpperCase() : 'DRAFT';
                var tipeCommissioning = (r.tipe_pengajuan === 'recommissioning') ? 'Re-Commissioning' : 'New Commissioning';

                data.push([
                    idx + 1,
                    'PGJ-' + String(r.id_pengajuan).padStart(4, '0'),
                    r.tanggal_pengajuan || '-',
                    r.nama_pemohon || '-',
                    r.perusahaan || '-',
                    tipeCommissioning,
                    (r.tipe_akses || '-').toUpperCase(),
                    r.jenis_kendaraan || '-',
                    r.merk || '-',
                    r.model_unit || '-',
                    r.nomor_unit || '-',
                    r.no_polisi || '-',
                    statusLabel,
                    r.tgl_approve_mgr || '-',
                    r.tgl_jadwal_rencana || '-',
                    r.nama_mekanik || '-',
                    r.hasil_inspeksi ? r.hasil_inspeksi.toUpperCase() : '-',
                    r.catatan_inspeksi || '-',
                    r.tgl_approve_ohs || '-',
                    r.catatan_ohs || '-',
                    r.nomor_stiker || '-',
                    r.tanggal_rilis_stiker || '-',
                    r.tgl_expired_stiker || '-'
                ]);
            });

            var ws = XLSX.utils.aoa_to_sheet(data);

            ws['!cols'] = [
                { wch: 5 },  // No
                { wch: 15 }, // ID Pengajuan
                { wch: 18 }, // Tanggal Pengajuan
                { wch: 22 }, // Pemohon
                { wch: 30 }, // Perusahaan / Dept
                { wch: 20 }, // Tipe Commissioning
                { wch: 15 }, // Tipe Akses
                { wch: 20 }, // Jenis Unit
                { wch: 18 }, // Merk
                { wch: 18 }, // Model
                { wch: 15 }, // Nomor Unit
                { wch: 15 }, // Nomor Polisi
                { wch: 25 }, // Status
                { wch: 20 }, // Approve Mgr
                { wch: 20 }, // Jadwal Rencana
                { wch: 22 }, // Mekanik
                { wch: 20 }, // Hasil Inspeksi
                { wch: 25 }, // Catatan Inspeksi
                { wch: 20 }, // Approve OHS
                { wch: 25 }, // Catatan OHS
                { wch: 18 }, // Nomor Stiker
                { wch: 20 }, // Tgl Rilis
                { wch: 20 }  // Tgl Expired
            ];

            var wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'History Pengajuan');

            var dateToday = new Date().toISOString().slice(0, 10);
            var fname = 'History_Pengajuan_Commissioning_' + dateToday + '.xlsx';
            XLSX.writeFile(wb, fname);
        }

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
                '</table></div></div>' +
                '<div class="col-md-6"><div class="bg-light rounded p-3 mb-2">' +
                '<h6 class="fw-bold text-success mb-2"><i class="bi bi-person me-2"></i>Pemohon</h6>' +
                '<strong>' + (d.nama_pemohon || '—') + '</strong><br>' +
                '<span class="text-muted small">' + (d.email_pemohon || '—') + '</span>' +
                '<div class="mt-2"><span class="badge bg-info text-white me-1">' + tipeLabel + '</span>' +
                renderBadgeTipeAkses(d.tipe_akses) + '</div>' +
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
            var post = csrfField();
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
                data: $.extend(csrfField(), {
                    id_pengajuan: id,
                    level: level,
                    aksi: aksi,
                    catatan: catatan,
                    nomor_stiker: nomor_stiker,
                }),
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
        // ── Soft Delete Pengajuan ─────────────────────────────────────────
        $(document).on('click', '.btn-delete-pengajuan', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Hapus pengajuan #PU-' + String(id).padStart(4, '0') + '? Data akan dipindahkan ke Sampah (Soft Delete).',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-trash me-1"></i>Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then(function(r) {
                if (r.isConfirmed) {
                    NProgress.start();
                    $.ajax({
                        url: '<?= site_url('pengajuan/delete') ?>',
                        type: 'POST',
                        data: $.extend(csrfField(), { id_pengajuan: id }),
                        dataType: 'json',
                        success: function(res) {
                            NProgress.done();
                            if (res.status === 'success') {
                                toastr.success(res.message || 'Pengajuan berhasil dihapus.');
                                table.ajax.reload(null, false);
                            } else {
                                Swal.fire({ title: 'Gagal', html: res.message, icon: 'error' });
                            }
                        },
                        error: function() {
                            NProgress.done();
                            toastr.error('Terjadi kesalahan server saat menghapus pengajuan.');
                        }
                    });
                }
            });
        });

        // ── Restore Pengajuan ──────────────────────────────────────────────
        $(document).on('click', '.btn-restore-pengajuan', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Konfirmasi Pemulihan',
                text: 'Pulihkan kembali pengajuan #PU-' + String(id).padStart(4, '0') + ' ke daftar aktif?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-arrow-counterclockwise me-1"></i>Ya, Pulihkan',
                cancelButtonText: 'Batal'
            }).then(function(r) {
                if (r.isConfirmed) {
                    NProgress.start();
                    $.ajax({
                        url: '<?= site_url('pengajuan/restore') ?>',
                        type: 'POST',
                        data: $.extend(csrfField(), { id_pengajuan: id }),
                        dataType: 'json',
                        success: function(res) {
                            NProgress.done();
                            if (res.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil Dipulihkan!',
                                    text: res.message || 'Pengajuan telah kembali aktif.',
                                    timer: 1800,
                                    showConfirmButton: false
                                });
                                table.ajax.reload(null, false);
                            } else {
                                Swal.fire({ title: 'Gagal', html: res.message, icon: 'error' });
                            }
                        },
                        error: function() {
                            NProgress.done();
                            toastr.error('Terjadi kesalahan server saat memulihkan pengajuan.');
                        }
                    });
                }
            });
        });

        // ── Render Detail Modal ───────────────────────────────────────────
        function renderDetail(data) {
            var baseUrl = '<?= base_url() ?>';
            var siteUrl = '<?= site_url() ?>';

            var p            = (data && data.pengajuan) ? data.pengajuan : (data || {});
            var lampiranList  = (data && data.lampiran)  ? data.lampiran  : [];
            var approvalList  = (data && data.approval)  ? data.approval  : [];
            var jadwalData    = (data && data.jadwal)    ? data.jadwal    : null;
            var ujiData       = (data && data.uji)       ? data.uji       : null;
            var perbaikanList = (data && data.perbaikan) ? data.perbaikan : [];

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
            var sc = statusClass[p.status] || 'secondary text-white';
            var sl = statusLabel[p.status] || p.status || '—';

            var ditolakStatuses = ['ditolak_manager', 'ditolak_admin_ohs', 'ditolak_ohs_supt', 'ditolak_ktt', 'tidak_lulus_inspeksi'];
            var alertDitolak = '';
            if (p.status && ditolakStatuses.indexOf(p.status) >= 0) {
                var lastRejectNote = '';
                if (p.status === 'tidak_lulus_inspeksi' && ujiData && (ujiData.catatan_temuan || ujiData.catatan_umum)) {
                    lastRejectNote = ujiData.catatan_temuan || ujiData.catatan_umum;
                }
                
                if (!lastRejectNote && approvalList && approvalList.length > 0) {
                    for (var i = approvalList.length - 1; i >= 0; i--) {
                        var appItem = approvalList[i];
                        if (appItem && appItem.catatan && String(appItem.catatan).trim() !== '' && String(appItem.catatan).trim() !== '—') {
                            if (appItem.status === 'rejected' || (appItem.level_approval && appItem.level_approval.indexOf('ditolak') >= 0)) {
                                lastRejectNote = appItem.catatan;
                                break;
                            }
                        }
                    }
                    if (!lastRejectNote) {
                        for (var j = approvalList.length - 1; j >= 0; j--) {
                            if (approvalList[j] && approvalList[j].catatan && String(approvalList[j].catatan).trim() !== '') {
                                lastRejectNote = approvalList[j].catatan;
                                break;
                            }
                        }
                    }
                }

                var titleText = p.status === 'tidak_lulus_inspeksi' ?
                    'Pengajuan Tidak Lulus Inspeksi' :
                    'Pengajuan Dikembalikan / Ditolak (' + sl + ')';

                var noteBlock = lastRejectNote ?
                    '<div class="mt-2 pt-2 border-top border-danger border-opacity-25 fs-6 fw-semibold text-danger-emphasis">' +
                    '<i class="bi bi-chat-left-quote-fill me-2 text-danger"></i>Catatan Penolakan: ' +
                    '<span class="fw-normal text-dark bg-white p-2 rounded border d-block mt-1 shadow-sm">' + valOrDash(lastRejectNote) + '</span>' +
                    '</div>' :
                    '<div class="small text-muted mt-1"><i class="bi bi-info-circle me-1"></i>Periksa tabel Riwayat Approval di bawah untuk rincian catatan.</div>';

                alertDitolak =
                    '<div class="card border-danger bg-danger-subtle mb-3 shadow-sm">' +
                    '<div class="card-body p-3">' +
                    '<div class="d-flex align-items-center mb-1 text-danger fw-bold fs-6">' +
                    '<i class="bi bi-exclamation-triangle-fill fs-4 me-2"></i>' + titleText +
                    '</div>' +
                    noteBlock +
                    '</div></div>';
            }

            var unitBadge = (p.is_unit_baru == 1) ?
                '<span class="badge bg-warning text-dark"><i class="bi bi-star-fill me-1"></i>Unit Baru</span>' :
                '<span class="badge bg-secondary">Unit Lama</span>';

            var tipeLabel = (p.tipe_pengajuan === 'new_commissioning' || p.tipe_pengajuan === 'baru') ? 'New Commissioning' : 'Recommissioning';

            function valOrDash(v) {
                return (v && String(v).trim() && String(v).trim() !== 'null') ? v : '—';
            }

            // ── Info Kendaraan ────────────────────────────────────────────
            var kendaraanHtml =
                '<div class="card border-0 bg-light h-100"><div class="card-body">' +
                '<h6 class="fw-bold text-primary mb-3"><i class="bi bi-truck me-2"></i>Informasi Kendaraan</h6>' +
                '<div class="row g-2">' +
                '<div class="col-6"><small class="text-muted d-block">No. Polisi</small><span class="badge bg-dark font-monospace fs-6">' + valOrDash(p.no_polisi) + '</span></div>' +
                '<div class="col-6"><small class="text-muted d-block">Tipe Unit</small>' + unitBadge + '</div>' +
                '<div class="col-6"><small class="text-muted d-block">Jenis Kendaraan</small><strong class="small">' + valOrDash(p.jenis_kendaraan) + '</strong></div>' +
                '<div class="col-6"><small class="text-muted d-block">Merk</small><strong class="small">' + valOrDash(p.merk) + '</strong></div>' +
                '<div class="col-6"><small class="text-muted d-block">Tipe / Model</small><strong class="small">' + valOrDash(p.tipe || p.model_unit) + '</strong></div>' +
                '<div class="col-6"><small class="text-muted d-block">Tahun</small><strong class="small">' + valOrDash(p.tahun) + '</strong></div>' +
                '<div class="col-6"><small class="text-muted d-block">Nomor Unit</small><strong class="small">' + valOrDash(p.nomor_unit) + '</strong></div>' +
                '<div class="col-6"><small class="text-muted d-block">Perusahaan</small><strong class="small">' + valOrDash(p.perusahaan) + '</strong></div>' +
                '<div class="col-6"><small class="text-muted d-block">Tipe Pengajuan</small><span class="badge bg-primary text-white">' + tipeLabel + '</span></div>' +
                '<div class="col-6"><small class="text-muted d-block">Tipe Akses</small>' + renderBadgeTipeAkses(p.tipe_akses) + '</div>' +
                '</div></div></div>';

            // ── Info Pemohon ──────────────────────────────────────────────
            var pemohonHtml =
                '<div class="card border-0 bg-light h-100"><div class="card-body">' +
                '<h6 class="fw-bold text-success mb-3"><i class="bi bi-person me-2"></i>Informasi Pemohon</h6>' +
                '<div class="row g-2">' +
                '<div class="col-12"><small class="text-muted d-block">Nama</small><strong class="small">' + valOrDash(p.nama_pemohon) + '</strong></div>' +
                '<div class="col-12"><small class="text-muted d-block">Email</small><strong class="small">' + valOrDash(p.email_pemohon || p.email_user) + '</strong></div>' +
                '<div class="col-6"><small class="text-muted d-block">Tgl Pengajuan</small><strong class="small">' + (p.tanggal_pengajuan ? p.tanggal_pengajuan.substr(0, 16) : '—') + '</strong></div>' +
                '<div class="col-6"><small class="text-muted d-block">Status</small><span class="badge bg-' + sc + '">' + sl + '</span></div>' +
                '<div class="col-12"><small class="text-muted d-block">Tujuan</small><span class="small">' + valOrDash(p.tujuan) + '</span></div>' +
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
            if (lampiranList && lampiranList.length > 0) {
                $.each(lampiranList, function(i, l) {
                    var ext = l.file_path ? l.file_path.split('.').pop().toLowerCase() : '';
                    var isImg = ['jpg', 'jpeg', 'png', 'webp'].indexOf(ext) >= 0;
                    var preview = isImg ?
                        '<a href="' + baseUrl + l.file_path + '" target="_blank"><img src="' + baseUrl + l.file_path + '" class="img-fluid rounded mb-1" style="height:80px;width:100%;object-fit:cover;" onerror="this.onerror=null;this.src=\'' + baseUrl + 'assets/img/img-error.png\';"></a>' :
                        '<a href="' + baseUrl + l.file_path + '" target="_blank" class="d-flex align-items-center justify-content-center" style="height:80px;"><i class="bi bi-file-earmark-pdf text-danger fs-1"></i></a>';
                    lampiranHtml += '<div class="col-6 col-md-4 col-lg-2"><div class="border rounded text-center p-2">' + preview + '<div class="small text-muted fw-semibold mt-1">' + (jenisLabel[l.jenis_lampiran] || l.jenis_lampiran) + '</div></div></div>';
                });
            } else {
                lampiranHtml = '<div class="col-12"><p class="text-muted small mb-0"><i class="bi bi-dash-circle me-1"></i>Tidak ada lampiran.</p></div>';
            }

            // ── Jadwal ────────────────────────────────────────────────────
            var jadwalHtml = jadwalData ?
                '<div class="row g-3">' +
                '<div class="col-md-4"><small class="text-muted d-block">Tanggal Uji</small><strong>' + valOrDash(jadwalData.tanggal_uji) + '</strong></div>' +
                '<div class="col-md-4"><small class="text-muted d-block">Lokasi</small><strong>' + valOrDash(jadwalData.lokasi) + '</strong></div>' +
                '<div class="col-md-4"><small class="text-muted d-block">Dibuat oleh</small><strong>' + valOrDash(jadwalData.dibuat_oleh_nama) + '</strong></div>' +
                '<div class="col-md-6"><small class="text-muted d-block"><i class="bi bi-tools me-1 text-warning"></i>Mekanik Lapangan</small><strong>' + valOrDash(jadwalData.nama_mekanik_master) + '</strong>' + (jadwalData.perusahaan_mekanik ? '<br><small class="text-muted">' + jadwalData.perusahaan_mekanik + '</small>' : '') + '</div>' +
                '<div class="col-md-6"><small class="text-muted d-block"><i class="bi bi-person-badge me-1 text-primary"></i>Inspektor</small><strong>' + valOrDash(jadwalData.nama_inspektor_user) + '</strong></div>' +
                '</div>' :
                '<div class="text-center py-3 text-muted"><i class="bi bi-calendar-x fs-3 d-block mb-1 opacity-50"></i><small>Belum dijadwalkan.</small></div>';

            // ── Hasil Uji ─────────────────────────────────────────────────
            var ujiHtml = '';
            if (ujiData) {
                var hasilOk = (ujiData.hasil === 'lulus');
                var ujiButtons = '<a href="' + siteUrl + '/checklist/detail/' + ujiData.id_uji + '" target="_blank" class="btn btn-sm btn-outline-info"><i class="bi bi-clipboard2-check me-1"></i>Checklist</a>';
                if (perbaikanList && perbaikanList.length > 0) {
                    ujiButtons += ' <a href="' + siteUrl + '/checklist/detail/' + ujiData.id_uji + '#sectionHistoryInspeksi" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-clock-history me-1"></i>Riwayat <span class="badge bg-warning text-dark ms-1">' + perbaikanList.length + '</span></a>';
                }
                ujiHtml =
                    '<div class="row g-3 align-items-center">' +
                    '<div class="col-md-3"><small class="text-muted d-block"><i class="bi bi-person-badge me-1"></i>Inspektor</small><strong>' + valOrDash(ujiData.nama_inspektor || ujiData.nama_mekanik) + '</strong>' + (ujiData.perusahaan_inspektor ? '<br><small class="text-muted">' + ujiData.perusahaan_inspektor + '</small>' : '') + '</div>' +
                    '<div class="col-md-3"><small class="text-muted d-block">Tanggal</small><strong>' + valOrDash(ujiData.updated_at || ujiData.created_at) + '</strong></div>' +
                    '<div class="col-md-2"><small class="text-muted d-block">Hasil</small><span class="badge bg-' + (hasilOk ? 'success' : 'danger') + ' text-white fs-6 px-3">' + (hasilOk ? 'LULUS' : 'TIDAK LULUS') + '</span></div>' +
                    '<div class="col-md-2"><small class="text-muted d-block">Catatan Temuan</small><span class="small">' + valOrDash(ujiData.catatan_temuan || ujiData.catatan_umum) + '</span></div>' +
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
            if (approvalList && approvalList.length > 0) {
                $.each(approvalList, function(i, a) {
                    var ac = a.status === 'approved' ? 'success' : (a.status === 'rejected' ? 'danger' : 'secondary');
                    var al = a.status === 'approved' ? 'Disetujui' : (a.status === 'rejected' ? 'Ditolak' : 'Pending');
                    approvalHtml += '<tr><td><span class="badge bg-light text-dark border">' + (levelLabel[a.level_approval] || a.level_approval) + '</span></td><td>' + (a.nama_approver || '<em class="text-muted small">Belum ditentukan</em>') + '</td><td><span class="badge bg-' + ac + '">' + al + '</span></td><td class="text-muted small">' + (a.created_at ? a.created_at.substr(0, 16) : '—') + '</td><td class="text-muted small">' + valOrDash(a.catatan) + '</td></tr>';
                });
            } else {
                approvalHtml = '<tr><td colspan="5" class="text-center text-muted py-3">Belum ada data approval.</td></tr>';
            }

            // ── Perbaikan Unit ────────────────────────────────────────────
            var perbaikanSection = '';
            if (perbaikanList && perbaikanList.length > 0) {
                perbaikanSection = '<div class="mt-3">' + renderPerbaikan(perbaikanList, baseUrl) + '</div>';
            }

            // ── Susun HTML akhir ──────────────────────────────────────────
            var isAdminDeptOnly = <?= json_encode($isAdminDeptOnly ?? false) ?>;
            var html = '';

            if (isAdminDeptOnly) {
                html =
                    '<div class="mb-3">' + kendaraanHtml + '</div>' +
                    alertDitolak +
                    '<div class="card border mb-0"><div class="card-header bg-white py-2"><i class="bi bi-check2-all text-primary me-2"></i><strong class="small">Riwayat Approval</strong></div><div class="card-body p-0"><div class="table-responsive"><table class="table table-sm table-hover align-middle mb-0"><thead class="table-light"><tr><th>Level</th><th>Approver</th><th>Status</th><th>Tanggal</th><th>Catatan</th></tr></thead><tbody>' + approvalHtml + '</tbody></table></div></div></div>';
            } else {
                html =
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
            }

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
            var catatan = $(this).data('catatan') || '';
            var infoText = resubmitInfoMap[status] || 'Jelaskan alasan pengajuan ulang.';
            var alertClass = resubmitAlertClass[status] || 'alert-info';
            $('#resubmitPolisi').text(polisi);
            $('#resubmitAlasan').val('');
            $('#resubmitErr').text('');
            $('#resubmitCharCount').text('0');

            var htmlBox = '<div class="fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>' + infoText + '</div>';
            if (catatan) {
                htmlBox += '<div class="mt-2 pt-2 border-top border-secondary border-opacity-25 small"><strong>Catatan Penolakan Verifikator:</strong> <span class="fst-italic text-dark">"' + catatan + '"</span></div>';
            }

            $('#resubmitInfoBox').removeClass('alert-danger alert-warning alert-info').addClass(alertClass).html(htmlBox);
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
            var post = csrfField();
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
                    var itemLabel = l.nama_item ? ('#' + (l.no_urut_item || '') + ' ' + l.nama_item) : (l.keterangan || 'Bukti #' + (j + 1));
                    if (itemLabel.length > 25) itemLabel = itemLabel.substring(0, 23) + '…';

                    if (imgExts.indexOf(ext) >= 0) {
                        thumbs += '<div class="col-6 col-md-3">' +
                                  '  <div class="border rounded overflow-hidden shadow-sm bg-white">' +
                                  '    <a href="' + baseUrl + l.file_path + '" target="_blank">' +
                                  '      <img src="' + baseUrl + l.file_path + '" class="img-fluid w-100" style="height:80px;object-fit:cover;" onerror="this.src=\'' + baseUrl + 'assets/img/img-error.png\'">' +
                                  '    </a>' +
                                  '    <div class="p-1 bg-light text-truncate small" style="font-size:10px;" title="' + (l.nama_item || l.keterangan || '') + '">' +
                                  '      <i class="bi bi-tag-fill me-1 text-primary"></i>' + itemLabel +
                                  '    </div>' +
                                  '  </div>' +
                                  '</div>';
                    } else {
                        var icon = ext === 'pdf' ? 'bi-file-earmark-pdf text-danger' : (ext === 'doc' || ext === 'docx' ? 'bi-file-earmark-word text-primary' : 'bi-file-earmark text-secondary');
                        thumbs += '<div class="col-6 col-md-3">' +
                                  '  <div class="border rounded overflow-hidden shadow-sm bg-white">' +
                                  '    <a href="' + baseUrl + l.file_path + '" target="_blank" class="d-flex flex-column align-items-center justify-content-center bg-light text-muted text-decoration-none" style="height:80px;">' +
                                  '      <i class="bi ' + icon + ' fs-3"></i>' +
                                  '    </a>' +
                                  '    <div class="p-1 bg-light text-truncate small" style="font-size:10px;" title="' + (l.nama_item || l.keterangan || '') + '">' +
                                  '      <i class="bi bi-tag-fill me-1 text-primary"></i>' + itemLabel +
                                  '    </div>' +
                                  '  </div>' +
                                  '</div>';
                    }
                });
                lampiranHtml = '<div class="mt-2 small fw-semibold text-muted mb-2"><i class="bi bi-camera-fill me-1 text-primary"></i>Bukti Foto/Dokumen Perbaikan (' + pb.lampiran.length + ' file):</div><div class="row g-2">' + thumbs + '</div>';
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

            var catatanDeskripsi = pb.catatan_perbaikan || pb.tindakan || pb.keterangan || '';
            var formattedCatatan = catatanDeskripsi ? catatanDeskripsi.replace(/\n/g, '<br>') : '';

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
                (formattedCatatan ?
                    '<div class="alert alert-light border py-2 mb-2 small"><i class="bi bi-chat-left-text me-1 text-warning"></i><strong>Catatan & Tindakan:</strong><br><div class="mt-1">' + formattedCatatan + '</div></div>' :
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