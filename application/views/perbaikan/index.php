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
                                        <th style="min-width:130px;">Hasil Inspeksi</th>
                                        <th>Temuan / Catatan Inspektor</th>
                                        <th style="width:130px;">Status Perbaikan</th>
                                        <th style="width:120px;">Tanggal</th>
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
            } else if (status === 'ditolak_verifikasi') {
                return '<span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-exclamation-triangle me-1"></i>Ditolak Verifikasi</span>';
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
                { data: 'hasil_inspeksi' },
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
            var p             = (data && data.pengajuan) ? data.pengajuan : (data || {});
            var approvalList   = (data && data.approval)  ? data.approval  : [];
            var ujiData        = (data && data.uji)       ? data.uji       : null;
            var perbaikanList  = (data && data.perbaikan) ? data.perbaikan : [];

            function valOrDash(v) {
                return (v && String(v).trim() && String(v).trim() !== 'null') ? v : '—';
            }

            // ── Banner Status Pengajuan ──────────────────────────────────
            var statusBadge = badgeStatusPerbaikan(p.status);
            var statusBanner =
                '<div class="alert alert-danger d-flex align-items-center justify-content-between p-3 mb-3 border-danger shadow-sm rounded-3">' +
                '  <div class="d-flex align-items-center gap-3">' +
                '    <div class="rounded-circle bg-danger bg-opacity-10 p-2 text-danger flex-shrink-0">' +
                '      <i class="bi bi-tools fs-4"></i>' +
                '    </div>' +
                '    <div>' +
                '      <div class="fw-bold text-danger fs-6 mb-0">Pengajuan Memerlukan Tindakan Perbaikan Unit</div>' +
                '      <small class="text-muted">Unit kendaraan tidak lulus inspeksi kelayakan dan harus diperbaiki sebelum dilakukan pengujian ulang.</small>' +
                '    </div>' +
                '  </div>' +
                '  <div class="ms-2 flex-shrink-0">' + statusBadge + '</div>' +
                '</div>';

            // ── Informasi Kendaraan & Pengaju ─────────────────────────────
            var kendaraanHtml =
                '<div class="card border mb-3 shadow-sm rounded-3">' +
                '  <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">' +
                '    <span class="fw-bold text-primary small"><i class="bi bi-truck me-2"></i>Informasi Kendaraan & Pengaju</span>' +
                '    <span class="badge bg-dark font-monospace fs-6 px-2 py-1">' + valOrDash(p.no_polisi) + '</span>' +
                '  </div>' +
                '  <div class="card-body py-3">' +
                '    <div class="row g-3">' +
                '      <div class="col-md-3 col-6"><small class="text-muted d-block">Nomor Unit</small><strong class="small text-dark">' + valOrDash(p.nomor_unit) + '</strong></div>' +
                '      <div class="col-md-3 col-6"><small class="text-muted d-block">Jenis Kendaraan</small><strong class="small text-dark">' + valOrDash(p.jenis_kendaraan) + '</strong></div>' +
                '      <div class="col-md-3 col-6"><small class="text-muted d-block">Merk / Tipe</small><strong class="small text-dark">' + valOrDash(p.merk) + ' ' + valOrDash(p.tipe || p.model_unit) + '</strong></div>' +
                '      <div class="col-md-3 col-6"><small class="text-muted d-block">Tahun</small><strong class="small text-dark">' + valOrDash(p.tahun) + '</strong></div>' +
                '      <div class="col-md-4 col-6"><small class="text-muted d-block">Perusahaan / Dept</small><strong class="small text-dark">' + valOrDash(p.perusahaan) + '</strong></div>' +
                '      <div class="col-md-4 col-6"><small class="text-muted d-block">Pemohon</small><strong class="small text-dark">' + valOrDash(p.nama_pemohon) + '</strong></div>' +
                '      <div class="col-md-4 col-12"><small class="text-muted d-block">Tipe Akses</small>' + badgeTipeAkses(p.tipe_akses) + '</div>' +
                '    </div>' +
                '  </div>' +
                '</div>';

            // ── Hasil Uji Kelayakan / Inspeksi ──────────────────────────────
            var ujiHtml = '';
            if (ujiData) {
                var hasilOk = (ujiData.hasil === 'lulus');
                var tglUjiFormatted = (ujiData.tanggal_uji || ujiData.updated_at || ujiData.created_at || '—');
                if (tglUjiFormatted && tglUjiFormatted !== '—') {
                    var dt = new Date(tglUjiFormatted.replace(/-/g, '/'));
                    if (!isNaN(dt.getTime())) {
                        var dd = ('0' + dt.getDate()).slice(-2);
                        var mm = ('0' + (dt.getMonth() + 1)).slice(-2);
                        var yyyy = dt.getFullYear();
                        var hh = ('0' + dt.getHours()).slice(-2);
                        var ii = ('0' + dt.getMinutes()).slice(-2);
                        tglUjiFormatted = dd + '/' + mm + '/' + yyyy + ' ' + hh + ':' + ii;
                    }
                }

                var actionBtns = '<div class="d-flex flex-wrap gap-2 justify-content-md-end">';
                actionBtns += '<a href="' + siteUrl + '/checklist/detail/' + ujiData.id_uji + '" target="_blank" class="btn btn-sm btn-outline-primary shadow-sm"><i class="bi bi-clipboard2-check me-1"></i>Buka Lembar Checklist</a>';
                if (perbaikanList && perbaikanList.length > 0) {
                    actionBtns += '<a href="' + siteUrl + '/checklist/detail/' + ujiData.id_uji + '#sectionHistoryInspeksi" target="_blank" class="btn btn-sm btn-outline-secondary shadow-sm"><i class="bi bi-clock-history me-1"></i>Riwayat <span class="badge bg-warning text-dark ms-1">' + perbaikanList.length + '</span></a>';
                }
                actionBtns += '</div>';

                var catatanRaw = valOrDash(ujiData.catatan_temuan || ujiData.catatan_umum);
                var catatanBox = '';
                if (catatanRaw && catatanRaw !== '—') {
                    catatanBox =
                        '<div class="col-12 mt-3">' +
                        '  <div class="p-3 rounded-3 border border-danger-subtle bg-danger bg-opacity-10">' +
                        '    <div class="d-flex align-items-center text-danger fw-bold mb-2 small">' +
                        '      <i class="bi bi-exclamation-octagon-fill me-2 fs-5"></i>Catatan Temuan Inspektor (Item Temuan Perlu Perbaikan):' +
                        '    </div>' +
                        '    <div class="p-2 px-3 bg-white rounded border border-danger-subtle text-dark fw-semibold small shadow-sm">' +
                        '      ' + catatanRaw +
                        '    </div>' +
                        '  </div>' +
                        '</div>';
                }

                ujiHtml =
                    '<div class="card border mb-3 shadow-sm rounded-3">' +
                    '  <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">' +
                    '    <span class="fw-bold text-danger small"><i class="bi bi-clipboard2-check text-danger me-2"></i>Hasil Uji Kelayakan / Inspeksi</span>' +
                    '  </div>' +
                    '  <div class="card-body py-3">' +
                    '    <div class="row g-3 align-items-center">' +
                    '      <div class="col-md-3 col-6">' +
                    '        <small class="text-muted d-block"><i class="bi bi-person-badge text-primary me-1"></i>Inspektor</small>' +
                    '        <strong class="text-dark small">' + valOrDash(ujiData.nama_inspektor || ujiData.nama_mekanik) + '</strong>' +
                    '        ' + (ujiData.perusahaan_inspektor ? '<div class="text-muted" style="font-size:11px;">' + ujiData.perusahaan_inspektor + '</div>' : '') +
                    '      </div>' +
                    '      <div class="col-md-3 col-6">' +
                    '        <small class="text-muted d-block"><i class="bi bi-calendar-event text-muted me-1"></i>Tanggal Inspeksi</small>' +
                    '        <strong class="text-dark small">' + tglUjiFormatted + '</strong>' +
                    '      </div>' +
                    '      <div class="col-md-2 col-6">' +
                    '        <small class="text-muted d-block mb-1">Hasil Uji</small>' +
                    '        <span class="badge bg-' + (hasilOk ? 'success' : 'danger') + ' text-white px-3 py-2 fw-bold fs-6">' +
                    '          <i class="bi bi-' + (hasilOk ? 'check-circle' : 'x-circle') + ' me-1"></i>' + (hasilOk ? 'LULUS' : 'TIDAK LULUS') +
                    '        </span>' +
                    '      </div>' +
                    '      <div class="col-md-4 col-12">' +
                    '        ' + actionBtns +
                    '      </div>' +
                    '      ' + catatanBox +
                    '    </div>' +
                    '  </div>' +
                    '</div>';
            } else {
                ujiHtml =
                    '<div class="card border mb-3 shadow-sm rounded-3">' +
                    '  <div class="card-header bg-white py-2">' +
                    '    <span class="fw-bold text-muted small"><i class="bi bi-clipboard2-check me-2"></i>Hasil Uji Kelayakan / Inspeksi</span>' +
                    '  </div>' +
                    '  <div class="card-body py-4 text-center text-muted">' +
                    '    <i class="bi bi-clipboard-x fs-3 d-block mb-1 opacity-50"></i><small>Belum ada data hasil inspeksi.</small>' +
                    '  </div>' +
                    '</div>';
            }

            // ── Riwayat Approval ──────────────────────────────────────────
            var levelLabel = {
                draft: 'Pengajuan Baru',
                dept_manager: 'Dept Manager',
                dept_manage: 'Dept Manager',
                admin_ohs: 'Admin OHS',
                admin_ohs_hasil: 'Admin OHS (Review Hasil)',
                admin_ohs_h: 'Admin OHS (Review Hasil)',
                ohs_supt: 'OHS Superintendent',
                ohs: 'OHS Superintendent',
                ktt: 'KTT',
                release_stiker: 'Release Stiker',
                release_sti: 'Release Stiker',
                perbaikan_unit: 'Perbaikan Unit',
                perbaikan_u: 'Perbaikan Unit',
                verif_perbaikan: 'Verifikasi Perbaikan',
                verif_perba: 'Verifikasi Perbaikan',
                inspeksi_verif: 'Verifikasi Perbaikan',
                inspeksi_ve: 'Verifikasi Perbaikan',
                cabut_stiker: 'Admin OHS (Pencabutan Stiker)',
                pencabutan_stiker: 'Admin OHS (Pencabutan Stiker)',
                resubmit_admin_dept: 'Resubmit Admin Dept',
                resubmit_ad: 'Resubmit Admin Dept',
                edit_admin_dept: 'Edit Admin Dept',
                edit_admin_: 'Edit Admin Dept',
                manager: 'Dept Manager',
                admin: 'Admin OHS'
            };
            var approvalHtml = '';
            if (approvalList && approvalList.length > 0) {
                $.each(approvalList, function(i, a) {
                    var isPencabutan = (a.status === 'revoked' || a.status === 'dicabut' || a.level_approval === 'cabut_stiker' || a.level_approval === 'pencabutan_stiker' || (a.catatan && a.catatan.indexOf('[EKSEKUSI PENCABUTAN STIKER]') !== -1));
                    var isDraft = (a.status === 'draft');
                    var isSubmitted = (a.status === 'submitted' || (a.level_approval === 'draft' && a.status !== 'draft'));
                    
                    var hasNextAction = false;
                    for (var k = i + 1; k < approvalList.length; k++) {
                        var ns = approvalList[k].status;
                        if (ns === 'approved' || ns === 'setuju' || ns === 'submitted' || ns === 'revoked' || ns === 'rejected' || ns === 'tolak') {
                            hasNextAction = true;
                            break;
                        }
                    }

                    var ac = 'secondary';
                    var al = 'Pending';
                    var lvlName = levelLabel[a.level_approval] || a.level_approval;

                    if (isPencabutan) {
                        ac = 'danger';
                        al = 'Stiker Dicabut';
                        lvlName = 'Admin OHS (Pencabutan Stiker)';
                    } else if (isDraft) {
                        ac = hasNextAction ? 'success' : 'secondary';
                        al = hasNextAction ? 'Diajukan' : 'Draft';
                        lvlName = 'Draft Pengajuan';
                    } else if (isSubmitted) {
                        ac = 'primary';
                        al = 'Diajukan';
                        lvlName = 'Pengajuan Baru';
                    } else if (a.status === 'approved' || a.status === 'setuju') {
                        ac = 'success';
                        al = 'Disetujui';
                    } else if (a.status === 'rejected' || a.status === 'tolak') {
                        ac = 'danger';
                        al = 'Ditolak';
                    } else if (a.status === 'pending') {
                        if (hasNextAction) {
                            ac = 'success';
                            al = 'Selesai';
                        } else {
                            ac = 'warning text-dark';
                            al = 'Menunggu Approval';
                        }
                    }

                    approvalHtml += '<tr><td><span class="badge bg-light text-dark border">' + lvlName + '</span></td><td>' + (a.nama_approver || '<em class="text-muted small">Belum ditentukan</em>') + '</td><td><span class="badge bg-' + ac + '">' + al + '</span></td><td class="text-muted small">' + (a.created_at ? a.created_at.substr(0, 16) : '—') + '</td><td class="text-muted small">' + valOrDash(a.catatan) + '</td></tr>';
                });
            } else {
                approvalHtml = '<tr><td colspan="5" class="text-center text-muted py-3">Belum ada data approval.</td></tr>';
            }

            var approvalCard =
                '<div class="card border mb-0 shadow-sm rounded-3">' +
                '  <div class="card-header bg-white py-2">' +
                '    <i class="bi bi-check2-all text-primary me-2"></i><strong class="small">Riwayat Approval & Perbaikan</strong>' +
                '  </div>' +
                '  <div class="card-body p-0">' +
                '    <div class="table-responsive">' +
                '      <table class="table table-sm table-hover align-middle mb-0">' +
                '        <thead class="table-light"><tr><th>Level</th><th>Approver</th><th>Status</th><th>Tanggal</th><th>Catatan</th></tr></thead>' +
                '        <tbody>' + approvalHtml + '</tbody>' +
                '      </table>' +
                '    </div>' +
                '  </div>' +
                '</div>';

            // ── Perbaikan Unit (jika ada data perbaikan) ───────────────────
            var perbaikanSection = '';
            if (perbaikanList && perbaikanList.length > 0) {
                perbaikanSection = '<div class="mt-3">' + renderPerbaikan(perbaikanList, baseUrl) + '</div>';
            }

            var html =
                statusBanner +
                kendaraanHtml +
                ujiHtml +
                approvalCard +
                perbaikanSection;

            $('#modalDetailBody').html(html);
        }

        // ── Helper Render Riwayat Perbaikan & Bukti Foto ──────────────────
        function renderPerbaikan(perbaikanArr, baseUrl) {
            if (!perbaikanArr || perbaikanArr.length === 0) return '';
            if (!baseUrl) baseUrl = '<?= base_url() ?>';

            var rows = '';
            $.each(perbaikanArr, function(i, pb) {
                var statusMap = {
                    menunggu: ['bg-secondary text-white', 'Menunggu'],
                    siap_verifikasi: ['bg-info text-white', 'Siap Verifikasi'],
                    selesai: ['bg-primary text-white', 'Selesai'],
                    diverifikasi: ['bg-success text-white', 'Diverifikasi ✓'],
                    ditolak_verifikasi: ['bg-warning text-dark', 'Ditolak Verifikasi'],
                };
                var sc = statusMap[pb.status] || ['bg-light text-dark border', pb.status];

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
                                      '      <img src="' + baseUrl + l.file_path + '" class="img-fluid w-100" style="height:85px;object-fit:cover;">' +
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
                                      '    <a href="' + baseUrl + l.file_path + '" target="_blank" class="d-flex flex-column align-items-center justify-content-center bg-light text-muted text-decoration-none" style="height:85px;">' +
                                      '      <i class="bi ' + icon + ' fs-3"></i>' +
                                      '    </a>' +
                                      '    <div class="p-1 bg-light text-truncate small" style="font-size:10px;" title="' + (l.nama_item || l.keterangan || '') + '">' +
                                      '      <i class="bi bi-tag-fill me-1 text-primary"></i>' + itemLabel +
                                      '    </div>' +
                                      '  </div>' +
                                      '</div>';
                        }
                    });
                    lampiranHtml = '<div class="mt-3 small fw-semibold text-muted mb-2"><i class="bi bi-camera-fill me-1 text-primary"></i>Bukti Foto/Dokumen Perbaikan (' + pb.lampiran.length + ' file):</div><div class="row g-2">' + thumbs + '</div>';
                }

                var catatanDeskripsi = pb.catatan_perbaikan || pb.tindakan || pb.keterangan || '—';
                var formattedCatatan = catatanDeskripsi.replace(/\n/g, '<br>');

                rows +=
                    '<div class="card border mb-2 shadow-sm rounded-3">' +
                    '  <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">' +
                    '    <span class="small fw-bold text-dark"><i class="bi bi-wrench me-1 text-primary"></i>Tindakan Perbaikan #' + (i + 1) + '</span>' +
                    '    <span class="badge ' + sc[0] + '">' + sc[1] + '</span>' +
                    '  </div>' +
                    '  <div class="card-body py-2">' +
                    '    <div class="small mb-2"><strong>Rincian Tindakan:</strong><br><div class="mt-1 p-2 bg-light rounded border">' + formattedCatatan + '</div></div>' +
                    '    <div class="small text-muted mb-2"><i class="bi bi-clock me-1"></i>Tanggal Input: ' + (pb.created_at || '—') + '</div>' +
                    '    ' + lampiranHtml +
                    '  </div>' +
                    '</div>';
            });

            return '<div class="card border mb-0 shadow-sm rounded-3"><div class="card-header bg-white py-2"><strong class="small text-primary"><i class="bi bi-tools me-2"></i>Data & Bukti Perbaikan Unit</strong></div><div class="card-body py-2">' + rows + '</div></div>';
        }
    });
</script>
