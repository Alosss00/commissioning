<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Data Kendaraan Commissioning</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Home</a></li>
                <li class="breadcrumb-item active">Data Kendaraan</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-12">

                <!-- ═══ REKAP CARDS ═══ -->
                <div class="row g-3 mb-3" id="rekapCards">
                    <div class="col-6 col-md-3">
                        <div class="card border-primary h-100">
                            <div class="card-body py-2 text-center">
                                <div class="fs-3 fw-bold text-primary" id="rekap_total">—</div>
                                <div class="small text-muted">Total Lulus Commissioning</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border-warning h-100">
                            <div class="card-body py-2 text-center">
                                <div class="fs-3 fw-bold text-warning" id="rekap_baru">—</div>
                                <div class="small text-muted">Unit Baru</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border-success h-100">
                            <div class="card-body py-2 text-center">
                                <div class="fs-3 fw-bold text-success" id="rekap_stiker_aktif">—</div>
                                <div class="small text-muted">Stiker Aktif</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border-danger h-100">
                            <div class="card-body py-2 text-center">
                                <div class="fs-3 fw-bold text-danger" id="rekap_stiker_expired">—</div>
                                <div class="small text-muted">Stiker Expired</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center pt-3 mb-3 flex-wrap gap-2">
                            <div>
                                <h5 class="card-title mb-0">Daftar Kendaraan Lulus Commissioning</h5>
                                <small class="text-muted">Menampilkan kendaraan yang sudah mendapatkan stiker kelayakan / ACC KTT</small>
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                <button class="btn btn-outline-success btn-sm" id="btnExportExcel">
                                    <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
                                </button>
                                <button class="btn btn-outline-secondary btn-sm" id="btnCetak">
                                    <i class="bi bi-printer me-1"></i>Cetak Rekap
                                </button>
                            </div>
                        </div>

                        <!-- Filter -->
                        <div class="row g-2 mb-3">
                            <div class="col-sm-6 col-md-3">
                                <select class="form-select form-select-sm" id="filterJenis">
                                    <option value="">— Semua Jenis —</option>
                                </select>
                            </div>
                            <div class="col-sm-6 col-md-2">
                                <select class="form-select form-select-sm" id="filterUnit">
                                    <option value="">— Semua Unit —</option>
                                    <option value="1">Unit Baru</option>
                                    <option value="0">Unit Lama</option>
                                </select>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <select class="form-select form-select-sm" id="filterStiker">
                                    <option value="">— Semua Status Stiker —</option>
                                    <option value="expired">Expired</option>
                                    <option value="hampir">Akan Kadaluarsa (≤30 hari)</option>
                                    <option value="aktif">Aktif</option>
                                    <option value="belum">Belum Ada Stiker</option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4 d-flex gap-2">
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
                            <table class="table table-bordered table-hover align-middle"
                                id="tabelKendaraan" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40" class="text-center">No</th>
                                        <th>No. Polisi</th>
                                        <th>Nomor Unit</th>
                                        <th>Jenis</th>
                                        <th>Merk / Tipe</th>
                                        <th width="60" class="text-center">Tahun</th>
                                        <th width="90" class="text-center">Tipe Unit</th>
                                        <th width="140" class="text-center">Stiker / Expired</th>
                                        <th width="80" class="text-center">Pengajuan</th>
                                        <th width="110" class="text-center">Tgl Lulus</th>
                                        <th width="80" class="text-center">Aksi</th>
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


<!-- ═══ MODAL DETAIL ═══ -->
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-truck me-2"></i>Detail Kendaraan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalDetailBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- ═══ MODAL REKAP CETAK ═══ -->
<div class="modal fade" id="modalRekap" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-bar-chart-fill me-2"></i>Rekap Kendaraan Commissioning</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalRekapBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i>Print
                </button>
            </div>
        </div>
    </div>
</div>


<!-- SheetJS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<style>
    @media print {
        body * {
            visibility: hidden;
        }

        #modalRekapBody,
        #modalRekapBody * {
            visibility: visible;
        }

        #modalRekapBody {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
        }

        .modal-footer,
        .btn-close {
            display: none !important;
        }
    }
</style>

<script>
    $(function() {

        var currentDetailId = null;

        // ── Init DataTable ─────────────────────────────────────────────────
        var table = $('#tabelKendaraan').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= site_url('kendaraan/get_data') ?>',
                type: 'POST',
                data: function(d) {
                    d.filter_jenis = $('#filterJenis').val();
                    d.filter_unit = $('#filterUnit').val();
                    d.filter_stiker = $('#filterStiker').val();
                    d['<?= $this->security->get_csrf_token_name() ?>'] = '<?= $this->security->get_csrf_hash() ?>';
                },
                error: function() {
                    toastr.error('Gagal memuat data kendaraan.');
                }
            },
            columns: [{
                    data: 'no',
                    orderable: false,
                    className: 'text-center'
                },
                {
                    data: 'no_polisi'
                },
                {
                    data: 'nomor_unit',
                    orderable: false
                },
                {
                    data: 'jenis_kendaraan'
                },
                {
                    data: 'merk_tipe'
                },
                {
                    data: 'tahun',
                    className: 'text-center'
                },
                {
                    data: 'unit',
                    className: 'text-center',
                    orderable: false
                },
                {
                    data: 'sisa_stiker',
                    className: 'text-center',
                    orderable: false
                },
                {
                    data: 'total_pengajuan',
                    className: 'text-center',
                    orderable: false
                },
                {
                    data: 'tgl_lulus',
                    className: 'text-center'
                },
                {
                    data: 'aksi',
                    orderable: false,
                    className: 'text-center'
                },
            ],
            order: [
                [9, 'desc']
            ],
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            }
        });

        // ── Load tipe kendaraan filter ──────────────────────────────────────
        $.getJSON('<?= site_url('kendaraan/get_tipe_list') ?>', function(res) {
            if (!res || res.status !== 'success') return;
            $.each(res.data, function(i, t) {
                $('#filterJenis').append($('<option>', {
                    value: t.jenis_kendaraan,
                    text: t.jenis_kendaraan
                }));
            });
        });

        // ── Rekap cards ────────────────────────────────────────────────────
        function loadRekapCards() {
            $.getJSON('<?= site_url('kendaraan/get_rekap') ?>', function(res) {
                if (!res || res.status !== 'success') return;
                var d = res.data;
                $('#rekap_total').text(d.total || 0);
                $('#rekap_baru').text(d.unit_baru || 0);
                $('#rekap_stiker_aktif').text(d.stiker_aktif || 0);
                $('#rekap_stiker_expired').text(d.stiker_expired || 0);
            });
        }
        loadRekapCards();

        // ── Filter / Reset ─────────────────────────────────────────────────
        $('#btnFilter').on('click', function() {
            table.ajax.reload();
        });
        $('#btnReset').on('click', function() {
            $('#filterJenis, #filterUnit, #filterStiker').val('');
            table.ajax.reload();
        });

        // ── Detail ─────────────────────────────────────────────────────────
        $(document).on('click', '.btn-detail', function() {
            currentDetailId = $(this).data('id');
            loadDetail(currentDetailId);
        });

        // ── Hapus ──────────────────────────────────────────────────────────
        $(document).on('click', '.btn-delete', function() {
            var id = $(this).data('id');
            var nopol = $(this).data('nopol');
            Swal.fire({
                title: 'Hapus Kendaraan?',
                html: 'Kendaraan <strong>' + nopol + '</strong> akan dihapus.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-trash me-1"></i>Ya, Hapus',
                cancelButtonText: 'Batal',
            }).then(function(r) {
                if (r.isConfirmed) doDelete(id);
            });
        });

        // ── Cetak Rekap ────────────────────────────────────────────────────
        $('#btnCetak').on('click', function() {
            $('#modalRekapBody').html('<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>');
            $('#modalRekap').modal('show');
            $.getJSON('<?= site_url('kendaraan/get_rekap') ?>?detail=1', function(res) {
                if (!res || res.status !== 'success') {
                    $('#modalRekapBody').html('<div class="alert alert-danger">Gagal memuat rekap.</div>');
                    return;
                }
                renderRekap(res.data);
            });
        });

        function renderRekap(d) {
            var tgl = new Date().toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            var jenis_rows = '';
            if (d.per_jenis && d.per_jenis.length) {
                d.per_jenis.forEach(function(r) {
                    jenis_rows += '<tr><td>' + r.jenis_kendaraan + '</td>' +
                        '<td class="text-center">' + r.total + '</td>' +
                        '<td class="text-center">' + r.stiker_aktif + '</td>' +
                        '<td class="text-center">' + r.stiker_expired + '</td>' +
                        '<td class="text-center">' + r.belum_ada_stiker + '</td></tr>';
                });
            }

            var stiker_rows = '';
            if (d.akan_expired && d.akan_expired.length) {
                d.akan_expired.forEach(function(r) {
                    stiker_rows += '<tr>' +
                        '<td><span class="badge bg-dark font-monospace">' + r.no_polisi + '</span></td>' +
                        '<td>' + r.jenis_kendaraan + ' — ' + r.merk + ' ' + r.tipe + '</td>' +
                        '<td>' + (r.nomor_unit || '—') + '</td>' +
                        '<td>' + (r.nomor_sticker || '—') + '</td>' +
                        '<td class="text-danger fw-bold">' + r.tgl_expired_fmt + '</td>' +
                        '<td class="text-center"><span class="badge bg-warning text-dark">' + r.sisa_hari + ' hari</span></td>' +
                        '</tr>';
                });
            }

            $('#modalRekapBody').html(
                '<div class="text-end mb-2"><small class="text-muted">Dicetak: ' + tgl + '</small></div>' +
                '<h5 class="fw-bold mb-3"><i class="bi bi-bar-chart me-2"></i>Rekap Kendaraan Commissioning</h5>' +
                '<div class="row g-3 mb-4">' +
                '<div class="col-6 col-md-3"><div class="border rounded p-3 text-center"><div class="fs-3 fw-bold text-primary">' + d.total + '</div><div class="small text-muted">Total Lulus</div></div></div>' +
                '<div class="col-6 col-md-3"><div class="border rounded p-3 text-center"><div class="fs-3 fw-bold text-warning">' + d.unit_baru + '</div><div class="small text-muted">Unit Baru</div></div></div>' +
                '<div class="col-6 col-md-3"><div class="border rounded p-3 text-center"><div class="fs-3 fw-bold text-success">' + d.stiker_aktif + '</div><div class="small text-muted">Stiker Aktif</div></div></div>' +
                '<div class="col-6 col-md-3"><div class="border rounded p-3 text-center"><div class="fs-3 fw-bold text-danger">' + d.stiker_expired + '</div><div class="small text-muted">Stiker Expired</div></div></div>' +
                '</div>' +
                '<h6 class="fw-bold mb-2"><i class="bi bi-list-ul me-2"></i>Rekap per Jenis Kendaraan</h6>' +
                '<div class="table-responsive mb-4"><table class="table table-sm table-bordered align-middle">' +
                '<thead class="table-dark"><tr><th>Jenis</th><th class="text-center">Total</th><th class="text-center">Stiker Aktif</th><th class="text-center">Stiker Expired</th><th class="text-center">Belum Stiker</th></tr></thead>' +
                '<tbody>' + (jenis_rows || '<tr><td colspan="5" class="text-center text-muted">Tidak ada data</td></tr>') + '</tbody></table></div>' +
                '<h6 class="fw-bold mb-2 text-warning"><i class="bi bi-exclamation-triangle me-2"></i>Akan Expired ≤30 Hari</h6>' +
                '<div class="table-responsive"><table class="table table-sm table-bordered align-middle">' +
                '<thead class="table-warning"><tr><th>No. Polisi</th><th>Kendaraan</th><th>Nomor Unit</th><th>No. Stiker</th><th>Expired</th><th class="text-center">Sisa</th></tr></thead>' +
                '<tbody>' + (stiker_rows || '<tr><td colspan="6" class="text-center text-muted py-3"><i class="bi bi-check-circle text-success me-1"></i>Tidak ada yang akan expired dalam 30 hari</td></tr>') + '</tbody>' +
                '</table></div>'
            );
        }

        // ── Export Excel — format Commissioning ───────────────────────────
        $('#btnExportExcel').on('click', function() {
            var $btn = $(this);
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Mengambil data...');

            $.ajax({
                url: '<?= site_url('kendaraan/get_all_for_export') ?>',
                type: 'POST',
                data: {
                    '<?= $this->security->get_csrf_token_name() ?>': getCsrf()
                },
                dataType: 'json',
                success: function(res) {
                    $btn.prop('disabled', false).html('<i class="bi bi-file-earmark-excel me-1"></i>Export Excel');
                    if (!res || res.status !== 'success') {
                        toastr.error('Gagal mengambil data.');
                        return;
                    }
                    generateCommissioningExcel(res.data);
                },
                error: function() {
                    $btn.prop('disabled', false).html('<i class="bi bi-file-earmark-excel me-1"></i>Export Excel');
                    toastr.error('Terjadi kesalahan server.');
                }
            });
        });

        // Format Excel mengikuti template Commissioning_2025-2026.xlsx
        // Kolom: Unit No. | Date Schedule | Date Conducted | Mechanic Inspector |
        //        OHS Inspector | Finding | Finding Status | Status | Due Date |
        //        Followed Up By | Complete Date | Verified By | Remark |
        //        Request Type | Access Type | Unit type | Unit Brand |
        //        Unit Model | Department User | Company Owner | Date Expired
        function generateCommissioningExcel(rows) {
            if (!rows || !rows.length) {
                toastr.warning('Tidak ada data untuk diekspor.');
                return;
            }

            var headers = [
                'Unit No.',
                'Date Schedule',
                'Date Conducted',
                'Mechanic Inspector',
                'OHS Inspector',
                'Finding',
                'Finding Status',
                'Status',
                'Due Date',
                'Followed Up By',
                'Complete Date',
                'Verified By',
                'Remark',
                'Request Type',
                'Access Type',
                'Unit type',
                'Unit Brand',
                'Unit Model',
                'Department User',
                'Company Owner',
                'Date Expired'
            ];

            var data = [headers];
            rows.forEach(function(r) {
                data.push([
                    r.unit_no || '',
                    r.date_schedule || '',
                    r.date_conducted || '',
                    r.mechanic_inspector || '',
                    r.ohs_inspector || '',
                    r.finding || '',
                    r.finding_status || '',
                    r.status || '',
                    r.due_date || '',
                    r.followed_up_by || '',
                    r.complete_date || '',
                    r.verified_by || '',
                    r.remark || '',
                    r.request_type || '',
                    r.access_type || '',
                    r.unit_type || '',
                    r.unit_brand || '',
                    r.unit_model || '',
                    r.department_user || '',
                    r.company_owner || '',
                    r.date_expired || '',
                ]);
            });

            var ws = XLSX.utils.aoa_to_sheet(data);

            // Style header row — lebar kolom otomatis
            ws['!cols'] = headers.map(function(h, i) {
                var maxLen = h.length;
                data.slice(1).forEach(function(row) {
                    var val = String(row[i] || '');
                    if (val.length > maxLen) maxLen = val.length;
                });
                return {
                    wch: Math.min(maxLen + 2, 35)
                };
            });

            // Freeze baris pertama (header)
            ws['!freeze'] = {
                xSplit: 0,
                ySplit: 1,
                topLeftCell: 'A2',
                activeCell: 'A2',
                sqref: 'A2'
            };

            var wb = XLSX.utils.book_new();

            // Nama sheet ikut tahun berjalan
            var yearNow = new Date().getFullYear();
            XLSX.utils.book_append_sheet(wb, ws, 'Commissioning ' + yearNow);

            var fname = 'Commissioning_' + yearNow + '_' + new Date().toISOString().slice(0, 10) + '.xlsx';
            XLSX.writeFile(wb, fname);
            toastr.success('File <strong>' + fname + '</strong> berhasil diunduh.', '', {
                escapeHtml: false
            });
        }

        // ── Helper: Detail ─────────────────────────────────────────────────
        function loadDetail(id) {
            $('#modalDetailBody').html('<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>');
            $('#modalDetail').modal('show');
            $.ajax({
                url: '<?= site_url('kendaraan/get_by_id') ?>',
                type: 'POST',
                data: {
                    '<?= $this->security->get_csrf_token_name() ?>': getCsrf(),
                    id: id
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status !== 'success') {
                        $('#modalDetailBody').html('<div class="alert alert-danger">' + res.message + '</div>');
                        return;
                    }
                    var d = res.data;
                    var badgeUnit = d.is_unit_baru == 1 ?
                        '<span class="badge bg-warning text-dark"><i class="bi bi-star-fill me-1"></i>Unit Baru</span>' :
                        '<span class="badge bg-secondary">Unit Lama</span>';
                    $('#modalDetailBody').html(
                        '<table class="table table-sm table-borderless mb-0">' +
                        '<tr><td class="text-muted fw-semibold" width="130">No. Polisi</td><td><span class="badge bg-dark font-monospace fs-6">' + d.no_polisi + '</span></td></tr>' +
                        '<tr><td class="text-muted fw-semibold">Nomor Unit</td><td><strong>' + (d.nomor_unit || '—') + '</strong></td></tr>' +
                        '<tr><td class="text-muted fw-semibold">Jenis</td><td>' + (d.jenis_kendaraan || '—') + '</td></tr>' +
                        '<tr><td class="text-muted fw-semibold">Merk</td><td>' + (d.merk || '—') + '</td></tr>' +
                        '<tr><td class="text-muted fw-semibold">Tipe / Model</td><td>' + (d.tipe || '—') + '</td></tr>' +
                        '<tr><td class="text-muted fw-semibold">Tahun</td><td>' + (d.tahun || '—') + '</td></tr>' +
                        '<tr><td class="text-muted fw-semibold">Perusahaan</td><td>' + (d.perusahaan || '—') + '</td></tr>' +
                        '<tr><td class="text-muted fw-semibold">Tipe Unit</td><td>' + badgeUnit + '</td></tr>' +
                        '</table>'
                    );
                },
                error: function() {
                    $('#modalDetailBody').html('<div class="alert alert-danger">Gagal memuat.</div>');
                }
            });
        }

        function doDelete(id) {
            NProgress.start();
            $.ajax({
                url: '<?= site_url('kendaraan/delete') ?>',
                type: 'POST',
                data: {
                    '<?= $this->security->get_csrf_token_name() ?>': getCsrf(),
                    id: id
                },
                dataType: 'json',
                success: function(res) {
                    NProgress.done();
                    if (res.status === 'success') {
                        toastr.success(res.message);
                        table.ajax.reload(null, false);
                        loadRekapCards();
                    } else {
                        toastr.error(res.message);
                    }
                },
                error: function() {
                    NProgress.done();
                    toastr.error('Terjadi kesalahan.');
                }
            });
        }

        function getCsrf() {
            return $('meta[name="csrf-token"]').attr('content') || '<?= $this->security->get_csrf_hash() ?>';
        }
    });
</script>