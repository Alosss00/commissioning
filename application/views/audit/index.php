<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<main id="main" class="main">

    <div class="pagetitle d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h1>Log Aktivitas Sistem</h1>
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Log Aktivitas Sistem</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= site_url('dashboard') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke Dashboard
            </a>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-12">

                <!-- CARD FILTER INTERAKTIF -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h6 class="m-0 fw-bold text-primary">
                            <i class="bi bi-funnel-fill me-2"></i>Filter Log Aktivitas
                        </h6>
                        <span class="badge bg-primary px-3 py-2" id="badgeTotalLogs">
                            Total: <?= number_format($total_logs) ?> Aktivitas
                        </span>
                    </div>
                    <div class="card-body pt-4">
                        <form id="formFilterAudit" method="GET" action="<?= site_url('audit') ?>">
                            <div class="row g-3">

                                <!-- Filter Mode -->
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold small">Mode Filter Waktu</label>
                                    <select class="form-select form-select-sm" id="filterMode">
                                        <option value="semua">Semua Waktu</option>
                                        <option value="hari" <?= !empty($filter['tanggal']) ? 'selected' : '' ?>>Hari / Tanggal Spesifik</option>
                                        <option value="bulan" <?= (!empty($filter['bulan']) && !empty($filter['tahun'])) ? 'selected' : '' ?>>Bulan & Tahun</option>
                                        <option value="tahun" <?= (empty($filter['bulan']) && !empty($filter['tahun'])) ? 'selected' : '' ?>>Tahun Saja</option>
                                    </select>
                                </div>

                                <!-- Input Tanggal (Hari) -->
                                <div class="col-md-3 box-filter-item" id="boxFilterHari" style="<?= empty($filter['tanggal']) ? 'display:none;' : '' ?>">
                                    <label class="form-label fw-semibold small">Pilih Tanggal (Hari)</label>
                                    <input type="date" class="form-control form-control-sm" name="tanggal" id="inpTanggal" value="<?= html_escape($filter['tanggal'] ?? '') ?>">
                                </div>

                                <!-- Input Bulan -->
                                <div class="col-md-2 box-filter-item" id="boxFilterBulan" style="<?= (empty($filter['bulan']) && empty($filter['tahun'])) ? 'display:none;' : '' ?>">
                                    <label class="form-label fw-semibold small">Pilih Bulan</label>
                                    <select class="form-select form-select-sm" name="bulan" id="inpBulan">
                                        <option value="">— Semua Bulan —</option>
                                        <?php
                                        $nama_bulan = [
                                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                        ];
                                        foreach ($nama_bulan as $num => $nama):
                                        ?>
                                            <option value="<?= sprintf('%02d', $num) ?>" <?= ($filter['bulan'] == sprintf('%02d', $num) || $filter['bulan'] == $num) ? 'selected' : '' ?>><?= $nama ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Input Tahun -->
                                <div class="col-md-2 box-filter-item" id="boxFilterTahun" style="<?= (empty($filter['bulan']) && empty($filter['tahun'])) ? 'display:none;' : '' ?>">
                                    <label class="form-label fw-semibold small">Pilih Tahun</label>
                                    <select class="form-select form-select-sm" name="tahun" id="inpTahun">
                                        <option value="">— Semua Tahun —</option>
                                        <?php foreach ($list_years as $yr): ?>
                                            <option value="<?= $yr ?>" <?= ($filter['tahun'] == $yr) ? 'selected' : '' ?>><?= $yr ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Filter Pengguna -->
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold small">Pengguna / User</label>
                                    <select class="form-select form-select-sm" name="id_user" id="inpUser">
                                        <option value="">— Semua Pengguna —</option>
                                        <?php foreach ($list_users as $u): ?>
                                            <option value="<?= $u->id_user ?>" <?= ($filter['id_user'] == $u->id_user) ? 'selected' : '' ?>><?= html_escape($u->nama) ?> (<?= html_escape($u->username) ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Filter Jenis Aksi -->
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold small">Jenis Aksi</label>
                                    <select class="form-select form-select-sm" name="aksi" id="inpAksi">
                                        <option value="">— Semua Aksi —</option>
                                        <?php foreach ($list_actions as $act): ?>
                                            <option value="<?= html_escape($act) ?>" <?= ($filter['aksi'] == $act) ? 'selected' : '' ?>><?= html_escape($act) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Search Keywords -->
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Pencarian Kata Kunci</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                                        <input type="text" class="form-control" name="search" id="inpSearch" value="<?= html_escape($filter['search'] ?? '') ?>" placeholder="Cari nama, aksi, ref #PU-001...">
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="col-md-5 d-flex align-items-end gap-2">
                                    <button type="button" id="btnApplyFilter" class="btn btn-primary btn-sm px-3">
                                        <i class="bi bi-filter me-1"></i>Terapkan Filter
                                    </button>
                                    <a href="<?= site_url('audit') ?>" class="btn btn-outline-secondary btn-sm px-3">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Filter
                                    </a>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>

                <!-- TABEL DATA AUDIT LOG -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body pt-4">

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="tableAuditLog">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 5%" class="text-center">#</th>
                                        <th style="width: 22%">Waktu / Tanggal</th>
                                        <th style="width: 20%">Pengguna (Aktor)</th>
                                        <th style="width: 18%">Jenis Aksi</th>
                                        <th style="width: 35%">Detail Aktivitas & Referensi</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyAuditLog">
                                    <?php if (empty($logs)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">
                                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada data log aktivitas ditemukan untuk filter ini.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($logs as $idx => $a): ?>
                                            <tr>
                                                <td class="text-center small text-muted"><?= $idx + 1 ?></td>
                                                <td>
                                                    <span class="fw-semibold text-dark d-block"><?= date('d M Y — H:i:s', strtotime($a->created_at)) ?></span>
                                                    <small class="text-muted"><?= time_ago($a->created_at) ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark border px-2 py-1">
                                                        <i class="bi bi-person me-1"></i><?= html_escape($a->nama_user ?? 'System / Anonymous') ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= aksi_color($a->aksi) ?> text-white me-1 px-2 py-1">
                                                        <?= html_escape($a->aksi) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?= aksi_label($a->aksi, html_escape($a->nama_user ?? 'System'), $a->id_ref) ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

</main>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        // Handler Toggle Tampilan Input Filter berdasarkan Mode
        function toggleFilterInputs() {
            var mode = $('#filterMode').val();
            if (mode === 'hari') {
                $('#boxFilterHari').slideDown(200);
                $('#boxFilterBulan, #boxFilterTahun').slideUp(200);
            } else if (mode === 'bulan') {
                $('#boxFilterBulan, #boxFilterTahun').slideDown(200);
                $('#boxFilterHari').slideUp(200);
            } else if (mode === 'tahun') {
                $('#boxFilterTahun').slideDown(200);
                $('#boxFilterHari, #boxFilterBulan').slideUp(200);
            } else {
                $('#boxFilterHari, #boxFilterBulan, #boxFilterTahun').slideUp(200);
            }
        }

        $('#filterMode').on('change', function() {
            toggleFilterInputs();
        });

        // AJAX Filter Request
        $('#btnApplyFilter').on('click', function() {
            var mode = $('#filterMode').val();
            var postData = {
                id_user: $('#inpUser').val(),
                aksi: $('#inpAksi').val(),
                search: $('#inpSearch').val()
            };

            if (mode === 'hari') {
                postData.tanggal = $('#inpTanggal').val();
            } else if (mode === 'bulan') {
                postData.bulan = $('#inpBulan').val();
                postData.tahun = $('#inpTahun').val();
            } else if (mode === 'tahun') {
                postData.tahun = $('#inpTahun').val();
            }

            $('#tbodyAuditLog').html('<tr><td colspan="5" class="text-center py-4"><span class="spinner-border spinner-border-sm text-primary me-2"></span>Memuat log aktivitas...</td></tr>');

            $.ajax({
                url: '<?= site_url('audit/fetch_ajax') ?>',
                type: 'POST',
                data: postData,
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        $('#tbodyAuditLog').html(res.html);
                        $('#badgeTotalLogs').text('Total: ' + new Intl.NumberFormat().format(res.total) + ' Aktivitas');
                    } else {
                        toastr.error('Gagal mengambil data log.');
                    }
                },
                error: function() {
                    toastr.error('Terjadi kesalahan server saat memuat log.');
                }
            });
        });

        // Allow pressing Enter in search field
        $('#inpSearch').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                $('#btnApplyFilter').click();
            }
        });

    });
</script>
