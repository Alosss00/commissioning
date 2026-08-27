<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<main id="main" class="main">

    <div class="pagetitle d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1>Daftar Pengajuan Perlu Perbaikan</h1>
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('pengajuan') ?>">Pengajuan</a></li>
                    <li class="breadcrumb-item active">Perbaikan Unit</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="section">
        <!-- ═══ RINGKASAN STATUS PERBAIKAN ═══ -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm border-start border-danger border-4 h-100">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-muted fw-semibold d-block">Perlu Input Perbaikan</small>
                                <h3 class="fw-bold mb-0 text-danger" id="countTidakLulus">0</h3>
                                <small class="text-muted" style="font-size: 11px;">Status: Tidak Lulus Inspeksi</small>
                            </div>
                            <div class="rounded-circle bg-danger bg-opacity-10 p-3 text-danger">
                                <i class="bi bi-tools fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm border-start border-info border-4 h-100">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-muted fw-semibold d-block">Menunggu Verifikasi Fisik</small>
                                <h3 class="fw-bold mb-0 text-info" id="countSiapVerifikasi">0</h3>
                                <small class="text-muted" style="font-size: 11px;">Status: Siap Verifikasi Inspektor</small>
                            </div>
                            <div class="rounded-circle bg-info bg-opacity-10 p-3 text-info">
                                <i class="bi bi-patch-check fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm border-start border-warning border-4 h-100">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-muted fw-semibold d-block">Ditolak Verifikasi</small>
                                <h3 class="fw-bold mb-0 text-warning" id="countDitolakVerifikasi">0</h3>
                                <small class="text-muted" style="font-size: 11px;">Perlu Perbaikan Ulang</small>
                            </div>
                            <div class="rounded-circle bg-warning bg-opacity-10 p-3 text-warning">
                                <i class="bi bi-exclamation-triangle fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══ TABEL PERBAIKAN UNIT ═══ -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body pt-4">

                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <h5 class="card-title p-0 m-0">
                                <i class="bi bi-tools text-danger me-2"></i>Daftar Unit Memerlukan Perbaikan
                            </h5>
                        </div>

                        <!-- Filter Bar -->
                        <div class="row g-2 mb-3">
                            <div class="col-sm-6 col-md-4">
                                <label class="form-label small text-muted mb-1">Filter Status Perbaikan</label>
                                <select class="form-select form-select-sm" id="filterStatus">
                                    <option value="">-- Semua Status Perbaikan --</option>
                                    <option value="tidak_lulus_inspeksi">Tidak Lulus (Perlu Input Perbaikan)</option>
                                    <option value="siap_verifikasi">Siap Verifikasi Fisik (Inspektor)</option>
                                </select>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <label class="form-label small text-muted mb-1">Filter Perusahaan / Departemen</label>
                                <select class="form-select form-select-sm" id="filterPerusahaan" <?= (!$is_site_wide && !empty($user_dept)) ? 'disabled' : '' ?>>
                                    <?php if (!$is_site_wide && !empty($user_dept)): ?>
                                        <option value="<?= html_escape($user_dept) ?>" selected><?= html_escape($user_dept) ?></option>
                                    <?php else: ?>
                                        <option value="">-- Semua Perusahaan --</option>
                                        <?php if (!empty($perusahaan)): foreach ($perusahaan as $p): ?>
                                            <option value="<?= html_escape($p->nama_perusahaan) ?>"><?= html_escape($p->nama_perusahaan) ?></option>
                                        <?php endforeach; endif; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <!-- DataTables Table -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle w-100" id="tablePerbaikan" style="font-size:13px;">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:40px;">#</th>
                                        <th style="width:100px;">ID Pengajuan</th>
                                        <th>Nomor Unit & Polisi</th>
                                        <th>Jenis / Tipe Unit</th>
                                        <th>Pemohon & Perusahaan</th>
                                        <th>Temuan / Catatan Inspektor</th>
                                        <th style="width:140px;">Status Perbaikan</th>
                                        <th style="width:130px;">Tanggal</th>
                                        <th style="width:120px;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<!-- ═══ MODAL DETAIL PENGAJUAN ═══ -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="modalDetailLabel">
                    <i class="bi bi-file-earmark-text me-2"></i>Detail Pengajuan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalDetailBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var csrfName = '<?= $this->security->get_csrf_token_name() ?>';
        var csrfHash = '<?= $this->security->get_csrf_hash() ?>';

        function csrfField() {
            var obj = {};
            obj[csrfName] = csrfHash;
            return obj;
        }

        function badgeStatusPerbaikan(status) {
            if (status === 'tidak_lulus_inspeksi') {
                return '<span class="badge bg-danger text-white px-2 py-1"><i class="bi bi-tools me-1"></i>Perlu Perbaikan</span>';
            } else if (status === 'siap_verifikasi') {
                return '<span class="badge bg-info text-white px-2 py-1"><i class="bi bi-patch-check me-1"></i>Siap Verifikasi</span>';
            }
            return '<span class="badge bg-secondary">' + status + '</span>';
        }

        function badgeTipeAkses(akses) {
            if (akses === 'mining') return '<span class="badge bg-dark">Mining</span>';
            if (akses === 'non_mining') return '<span class="badge bg-secondary">Non-Mining</span>';
            if (akses === 'underground') return '<span class="badge bg-warning text-dark">Underground</span>';
            return '<span class="badge bg-light text-dark border">' + (akses || '-') + '</span>';
        }

        var table = $('#tablePerbaikan').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= site_url('perbaikan/get_data') ?>',
                type: 'POST',
                data: function(d) {
                    d[csrfName] = csrfHash;
                    d.filter_status = $('#filterStatus').val();
                    d.filter_departemen = $('#filterPerusahaan').val();
                },
                dataSrc: function(json) {
                    if (json.csrfHash) csrfHash = json.csrfHash;

                    // Update Ringkas Kartu Stat
                    if (json.counts) {
                        $('#countTidakLulus').text(json.counts.tidak_lulus || 0);
                        $('#countSiapVerifikasi').text(json.counts.siap_verifikasi || 0);
                        $('#countDitolakVerifikasi').text(json.counts.ditolak_verifikasi || 0);
                    }

                    return json.data;
                }
            },
            columns: [
                { data: 'no', orderable: false, searchable: false },
                { data: 'id_display' },
                {
                    data: null,
                    render: function(d) {
                        return '<strong>' + (d.no_polisi || 'N/A') + '</strong><br>' +
                               '<small class="text-muted">Unit: ' + (d.nomor_unit || '—') + '</small>';
                    }
                },
                {
                    data: null,
                    render: function(d) {
                        return '<strong>' + (d.jenis_kendaraan || '-') + '</strong><br>' +
                               '<small class="text-muted">' + (d.merk_tipe || '-') + '</small>';
                    }
                },
                {
                    data: null,
                    render: function(d) {
                        return '<strong>' + (d.pemohon || '-') + '</strong><br>' +
                               '<small class="text-muted">' + (d.perusahaan || '-') + '</small>';
                    }
                },
                {
                    data: 'catatan_temuan',
                    render: function(val) {
                        return '<div class="text-wrap text-danger fw-semibold" style="max-width:260px;">' +
                               '<i class="bi bi-exclamation-triangle-fill me-1"></i>' + val + '</div>';
                    }
                },
                { data: 'status' },
                { data: 'tgl_pengajuan' },
                { data: 'aksi', orderable: false, searchable: false, className: 'text-center' }
            ],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                zeroRecords: "Tidak ada pengajuan yang memerlukan perbaikan.",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 dari 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: "&raquo;",
                    previous: "&laquo;"
                }
            },
            order: [[1, 'desc']],
            pageLength: 10
        });

        $('#filterStatus, #filterPerusahaan').on('change', function() {
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

        function renderDetail(data) {
            var baseUrl = '<?= base_url() ?>';
            var siteUrl = '<?= site_url() ?>';
            var p            = (data && data.pengajuan) ? data.pengajuan : (data || {});
            var approvalList  = (data && data.approval)  ? data.approval  : [];
            var ujiData       = (data && data.uji)       ? data.uji       : null;

            function valOrDash(v) {
                return (v && String(v).trim() && String(v).trim() !== 'null') ? v : '—';
            }

            var kendaraanHtml =
                '<div class="card border-0 bg-light h-100"><div class="card-body">' +
                '<h6 class="fw-bold text-primary mb-3"><i class="bi bi-truck me-2"></i>Informasi Kendaraan</h6>' +
                '<div class="row g-2">' +
                '<div class="col-6"><small class="text-muted d-block">No. Polisi</small><span class="badge bg-dark font-monospace fs-6">' + valOrDash(p.no_polisi) + '</span></div>' +
                '<div class="col-6"><small class="text-muted d-block">Nomor Unit</small><strong class="small">' + valOrDash(p.nomor_unit) + '</strong></div>' +
                '<div class="col-6"><small class="text-muted d-block">Jenis Kendaraan</small><strong class="small">' + valOrDash(p.jenis_kendaraan) + '</strong></div>' +
                '<div class="col-6"><small class="text-muted d-block">Merk / Tipe</small><strong class="small">' + valOrDash(p.merk) + ' ' + valOrDash(p.tipe || p.model_unit) + '</strong></div>' +
                '<div class="col-6"><small class="text-muted d-block">Perusahaan</small><strong class="small">' + valOrDash(p.perusahaan) + '</strong></div>' +
                '<div class="col-6"><small class="text-muted d-block">Tipe Akses</small>' + badgeTipeAkses(p.tipe_akses) + '</div>' +
                '</div></div></div>';

            var lastRejectNote = '';
            if (ujiData && (ujiData.catatan_temuan || ujiData.catatan_umum)) {
                lastRejectNote = ujiData.catatan_temuan || ujiData.catatan_umum;
            }

            var alertDitolak =
                '<div class="card border-danger bg-danger-subtle mb-3 shadow-sm">' +
                '<div class="card-body p-3">' +
                '<div class="d-flex align-items-center mb-1 text-danger fw-bold fs-6">' +
                '<i class="bi bi-exclamation-triangle-fill fs-4 me-2"></i>Pengajuan Perlu Perbaikan' +
                '</div>' +
                '<div class="mt-2 pt-2 border-top border-danger border-opacity-25 fs-6 fw-semibold text-danger-emphasis">' +
                '<i class="bi bi-chat-left-quote-fill me-2 text-danger"></i>Catatan Temuan Inspektor: ' +
                '<span class="fw-normal text-dark bg-white p-2 rounded border d-block mt-1 shadow-sm">' + valOrDash(lastRejectNote) + '</span>' +
                '</div></div></div>';

            var levelLabel = {
                dept_manager: 'Dept Manager',
                admin_ohs: 'Admin OHS',
                ohs_supt: 'OHS Superintendent',
                ktt: 'KTT',
                perbaikan_unit: 'Perbaikan Unit',
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

            var html =
                '<div class="mb-3">' + kendaraanHtml + '</div>' +
                alertDitolak +
                '<div class="card border mb-0"><div class="card-header bg-white py-2"><i class="bi bi-check2-all text-primary me-2"></i><strong class="small">Riwayat Approval & Perbaikan</strong></div><div class="card-body p-0"><div class="table-responsive"><table class="table table-sm table-hover align-middle mb-0"><thead class="table-light"><tr><th>Level</th><th>Approver</th><th>Status</th><th>Tanggal</th><th>Catatan</th></tr></thead><tbody>' + approvalHtml + '</tbody></table></div></div></div>';

            $('#modalDetailBody').html(html);
        }
    });
</script>
