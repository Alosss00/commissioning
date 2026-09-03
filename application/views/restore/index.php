<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<main id="main" class="main">

    <div class="pagetitle">
        <h1>Pusat Pemulihan Data (Recycle Bin)</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Home</a></li>
                <li class="breadcrumb-item">Administrasi</li>
                <li class="breadcrumb-item active">Pusat Pemulihan Data</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <!-- ═══ REKAP STAT CARDS ═══ -->
        <div class="row g-3 mb-4">
            <div class="<?= ($isAdminDeptOnly ?? false) ? 'col-12 col-md-6' : 'col-6 col-md-4 col-lg-2' ?>">
                <div class="card border-0 shadow-sm bg-primary bg-opacity-10 text-center py-3 h-100">
                    <div class="fs-2 fw-bold text-primary"><?= (int)($counts['pengajuan'] ?? 0) ?></div>
                    <div class="text-muted small fw-semibold"><?= ($isAdminDeptOnly ?? false) ? 'Pengajuan Anda yang Dibatalkan / Dihapus' : 'Pengajuan Terhapus' ?></div>
                </div>
            </div>
            <?php if (!($isAdminDeptOnly ?? false)): ?>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 shadow-sm bg-success bg-opacity-10 text-center py-3 h-100">
                    <div class="fs-2 fw-bold text-success"><?= (int)($counts['kendaraan'] ?? 0) ?></div>
                    <div class="text-muted small fw-semibold">Kendaraan Terhapus</div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 shadow-sm bg-info bg-opacity-10 text-center py-3 h-100">
                    <div class="fs-2 fw-bold text-info"><?= (int)($counts['users'] ?? 0) ?></div>
                    <div class="text-muted small fw-semibold">User Terhapus</div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 shadow-sm bg-warning bg-opacity-10 text-center py-3 h-100">
                    <div class="fs-2 fw-bold text-warning"><?= (int)($counts['mekanik'] ?? 0) ?></div>
                    <div class="text-muted small fw-semibold">Mekanik Terhapus</div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 shadow-sm bg-secondary bg-opacity-10 text-center py-3 h-100">
                    <div class="fs-2 fw-bold text-secondary"><?= (int)($counts['tipe_kendaraan'] ?? 0) ?></div>
                    <div class="text-muted small fw-semibold">Tipe Unit Terhapus</div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 shadow-sm bg-warning bg-opacity-10 text-center py-3 h-100">
                    <div class="fs-2 fw-bold text-warning"><?= (int)($counts['checklist_template'] ?? 0) ?></div>
                    <div class="text-muted small fw-semibold">Template Checklist</div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 shadow-sm bg-dark bg-opacity-10 text-center py-3 h-100">
                    <div class="fs-2 fw-bold text-dark"><?= (int)($counts['checklist_item'] ?? 0) ?></div>
                    <div class="text-muted small fw-semibold">Item Checklist</div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- ═══ CARD UTAMA & TABS ═══ -->
        <div class="card shadow-sm border-0">
            <div class="card-body pt-4">

                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <div>
                        <h5 class="card-title p-0 m-0 text-primary fw-bold"><i class="bi bi-arrow-counterclockwise me-2"></i>Daftar Data Terhapus (Soft Delete)</h5>
                        <small class="text-muted">Pilih tab di bawah untuk melihat dan memulihkan data yang telah dihapus.</small>
                    </div>
                </div>

                <!-- Nav Tabs -->
                <ul class="nav nav-tabs nav-tabs-bordered mb-3" id="restoreTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-semibold" id="tab-pengajuan" data-bs-toggle="tab" data-bs-target="#pane-pengajuan" type="button" role="tab" data-type="pengajuan">
                            <i class="bi bi-clipboard-check me-1"></i>Pengajuan Commissioning
                        </button>
                    </li>
                    <?php if (!($isAdminDeptOnly ?? false)): ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" id="tab-kendaraan" data-bs-toggle="tab" data-bs-target="#pane-kendaraan" type="button" role="tab" data-type="kendaraan">
                            <i class="bi bi-truck me-1"></i>Data Kendaraan
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" id="tab-users" data-bs-toggle="tab" data-bs-target="#pane-users" type="button" role="tab" data-type="users">
                            <i class="bi bi-people me-1"></i>Pengguna / Akun
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" id="tab-mekanik" data-bs-toggle="tab" data-bs-target="#pane-mekanik" type="button" role="tab" data-type="mekanik">
                            <i class="bi bi-person-gear me-1"></i>Master Mekanik
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" id="tab-tipekendaraan" data-bs-toggle="tab" data-bs-target="#pane-tipekendaraan" type="button" role="tab" data-type="tipe_kendaraan">
                            <i class="bi bi-car-front-fill me-1"></i>Tipe Kendaraan
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" id="tab-template" data-bs-toggle="tab" data-bs-target="#pane-template" type="button" role="tab" data-type="checklist_template">
                            <i class="bi bi-ui-checks-grid me-1"></i>Template Checklist
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" id="tab-checklist" data-bs-toggle="tab" data-bs-target="#pane-checklist" type="button" role="tab" data-type="checklist_item">
                            <i class="bi bi-card-checklist me-1"></i>Item Checklist
                        </button>
                    </li>
                    <?php endif; ?>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content pt-2" id="restoreTabContent">
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle w-100" id="tabelRestore" style="width:100%">
                            <thead class="table-light align-middle text-nowrap">
                                <tr>
                                    <th width="40" class="text-center">No</th>
                                    <th width="110" class="text-center">Kode / ID</th>
                                    <th>Identitas Utama</th>
                                    <th>Keterangan / Detail</th>
                                    <th width="100" class="text-center">Status</th>
                                    <th width="140" class="text-center">Waktu Dihapus</th>
                                    <th width="130">Dihapus Oleh</th>
                                    <th width="110" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                </div>

            </div>
        </div>

    </section>

</main>

<script>
    $(function() {
        var currentType = 'pengajuan';
        var restoreEndpoints = {
            'pengajuan':          { url: '<?= site_url('pengajuan/restore') ?>',          field: 'id_pengajuan', label: 'pengajuan' },
            'kendaraan':          { url: '<?= site_url('kendaraan/restore') ?>',          field: 'id_kendaraan', label: 'kendaraan' },
            'users':              { url: '<?= site_url('usermanagement/restore') ?>',     field: 'id_user',      label: 'user' },
            'mekanik':            { url: '<?= site_url('mekanik/restore') ?>',            field: 'id',           label: 'mekanik' },
            'tipe_kendaraan':     { url: '<?= site_url('tipekendaraan/restore') ?>',      field: 'id',           label: 'tipe kendaraan' },
            'checklist_template': { url: '<?= site_url('checklist/restore_template') ?>', field: 'id_template', label: 'template checklist' },
            'checklist_item':     { url: '<?= site_url('checklist/restore_item') ?>',     field: 'id_item',      label: 'item checklist' },
        };

        var table = $('#tabelRestore').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: '<?= site_url('restore/get_data/') ?>' + currentType,
                type: 'POST',
                data: function(d) {
                    if (window.csrfTokenName && window.csrfTokenHash) {
                        d[window.csrfTokenName] = window.csrfTokenHash;
                    }
                },
                dataSrc: function(json) {
                    if (json && json.csrf_hash) {
                        window.updateCsrfToken(json.csrf_hash);
                    }
                    return json.data || [];
                }
            },
            columns: [
                { data: 'no', className: 'text-center' },
                { 
                    data: 'kode', 
                    className: 'text-center font-monospace small',
                    render: function(d) {
                        return '<span class="badge bg-light text-dark border font-monospace">' + d + '</span>';
                    }
                },
                { data: 'identitas' },
                { data: 'keterangan' },
                { data: 'status', className: 'text-center' },
                { 
                    data: 'deleted_at', 
                    className: 'text-center text-nowrap text-danger small font-monospace',
                    render: function(d) {
                        return '<i class="bi bi-clock-history me-1"></i>' + d;
                    }
                },
                { 
                    data: 'deleted_by',
                    render: function(d) {
                        return '<span class="small fw-semibold text-muted"><i class="bi bi-person me-1"></i>' + d + '</span>';
                    }
                },
                {
                    data: null,
                    className: 'text-center text-nowrap',
                    orderable: false,
                    render: function(data, type, row) {
                        return '<button class="btn btn-sm btn-success py-1 px-2 btn-restore-item fw-semibold" ' +
                               'data-id="' + row.id + '" data-type="' + row.type + '" data-kode="' + (row.kode || '') + '" title="Pulihkan Data">' +
                               '<i class="bi bi-arrow-counterclockwise me-1"></i>Pulihkan</button>';
                    }
                }
            ],
            language: {
                emptyTable: '<div class="py-4 text-center text-muted"><i class="bi bi-check-circle fs-2 d-block mb-1 text-success opacity-75"></i>Tidak ada data yang terhapus pada kategori ini.</div>',
                info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ data terhapus",
                infoEmpty: "Tidak ada data",
                search: "Cari data terhapus:",
                lengthMenu: "Tampilkan _MENU_ data",
                paginate: { first: "«", last: "»", next: "›", previous: "‹" }
            }
        });

        // Ganti Tab
        $('#restoreTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            currentType = $(e.target).data('type');
            table.ajax.url('<?= site_url('restore/get_data/') ?>' + currentType).load();
        });

        // Klik Tombol Restore
        $(document).on('click', '.btn-restore-item', function() {
            var id    = $(this).data('id');
            var type  = $(this).data('type');
            var kode  = $(this).data('kode');
            var cfg   = restoreEndpoints[type];

            if (!cfg) {
                toastr.error('Tipe entitas tidak valid.');
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Pemulihan',
                html: 'Pulihkan kembali data <strong>' + cfg.label + ' (' + kode + ')</strong> ke status aktif?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-arrow-counterclockwise me-1"></i>Ya, Pulihkan',
                cancelButtonText: 'Batal'
            }).then(function(r) {
                if (r.isConfirmed) {
                    NProgress.start();
                    var postData = {};
                    postData[cfg.field] = id;
                    if (window.csrfTokenName && window.csrfTokenHash) {
                        postData[window.csrfTokenName] = window.csrfTokenHash;
                    }

                    $.ajax({
                        url: cfg.url,
                        type: 'POST',
                        data: postData,
                        dataType: 'json',
                        success: function(res) {
                            NProgress.done();
                            if (res && res.csrf_hash) {
                                window.updateCsrfToken(res.csrf_hash);
                            }
                            if (res && res.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil Dipulihkan!',
                                    text: res.message || 'Data telah berhasil dikembalikan ke status aktif.',
                                    timer: 1800,
                                    showConfirmButton: false
                                });
                                table.ajax.reload(null, false);
                            } else {
                                Swal.fire({
                                    title: 'Gagal',
                                    html: (res && res.message) ? res.message : 'Gagal memulihkan data.',
                                    icon: 'error'
                                });
                            }
                        },
                        error: function() {
                            NProgress.done();
                            toastr.error('Terjadi kesalahan server saat memulihkan data.');
                        }
                    });
                }
            });
        });
    });
</script>
