<main id="main" class="main">

    <div class="pagetitle">
        <h1><?= isset($existing) && $existing->id_jadwal ? 'Edit' : 'Buat' ?> Jadwal Inspeksi</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('jadwal') ?>">Jadwal Uji</a></li>
                <li class="breadcrumb-item active"><?= isset($existing) && $existing->id_jadwal ? 'Edit' : 'Buat' ?> Jadwal</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row justify-content-center">
            <div class="col-xl-9">

                <!-- INFO PENGAJUAN -->
                <div class="card mb-3 border-primary">
                    <div class="card-body py-3">
                        <div class="row g-2 align-items-center">
                            <div class="col-auto">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white"
                                    style="width:48px;height:48px;font-size:1.3rem;">
                                    <i class="bi bi-truck"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h6 class="mb-0 fw-bold text-primary"><?= html_escape($pengajuan->no_polisi) ?></h6>
                                <small class="text-muted">
                                    <?= html_escape($pengajuan->jenis_kendaraan) ?> —
                                    <?= html_escape($pengajuan->merk) ?> <?= html_escape($pengajuan->tipe) ?>
                                    (<?= $pengajuan->tahun ?>)
                                </small>
                            </div>
                            <div class="col-auto text-end">
                                <small class="text-muted d-block">No. Pengajuan</small>
                                <strong class="text-primary">#PU-<?= str_pad($pengajuan->id_pengajuan, 4, '0', STR_PAD_LEFT) ?></strong>
                            </div>
                            <div class="col-auto text-end">
                                <small class="text-muted d-block">Tipe Unit</small>
                                <span class="badge bg-info text-white"><?= html_escape($pengajuan->jenis_kendaraan) ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FORM -->
                <div class="card">
                    <div class="card-body pt-4">
                        <h5 class="card-title mb-4">
                            <i class="bi bi-calendar-plus me-2 text-primary"></i>
                            <?= isset($existing) && $existing->id_jadwal ? 'Edit' : 'Isi' ?> Jadwal Inspeksi
                        </h5>

                        <div id="formJadwal">
                            <input type="hidden" id="hid_id_pengajuan" value="<?= $pengajuan->id_pengajuan ?>">
                            <input type="hidden" id="hid_id_jadwal" value="<?= isset($existing) ? $existing->id_jadwal : '' ?>">
                            <input type="hidden" id="hid_jenis_kendaraan" value="<?= html_escape($pengajuan->jenis_kendaraan) ?>">

                            <div class="row g-3">

                                <!-- Tanggal & Jam -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Tanggal & Jam Inspeksi <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="tanggal_uji"
                                        placeholder="Pilih tanggal & jam..."
                                        value="<?= isset($existing) && $existing->tanggal_uji ? date('Y-m-d H:i', strtotime($existing->tanggal_uji)) : '' ?>"
                                        autocomplete="off" required>
                                    <small class="text-muted">
                                        Minimal hari ini. Tiap mekanik/inspektor bisa multi-inspeksi dengan selisih <strong>min. 1 jam</strong>.
                                    </small>
                                </div>

                                <!-- Lokasi -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Lokasi Inspeksi <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="lokasi"
                                        placeholder="misal: Workshop OHS, Pit Area B..."
                                        value="<?= html_escape(isset($existing) ? $existing->lokasi : '') ?>"
                                        required>
                                </div>

                                <!-- ── MEKANIK LAPANGAN (dari mekanik_master) ── -->
                                <div class="col-12">
                                    <div class="card border-warning mb-0">
                                        <div class="card-header bg-warning text-dark py-2 d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 fw-bold">
                                                <i class="bi bi-tools me-2"></i>Mekanik Lapangan
                                                <span class="badge bg-dark text-white ms-2" style="font-size:10px;">
                                                    Dari Master Mekanik — filter tipe: <?= html_escape($pengajuan->jenis_kendaraan) ?>
                                                </span>
                                            </h6>
                                            <small class="text-dark opacity-75">Teknisi fisik yang melakukan pemeriksaan</small>
                                        </div>
                                        <div class="card-body pt-3">

                                            <?php if (empty($mekaniks)): ?>
                                                <div class="alert alert-warning py-2 mb-0">
                                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                                    Belum ada mekanik terdaftar untuk tipe <strong><?= html_escape($pengajuan->jenis_kendaraan) ?></strong>.
                                                    <a href="<?= site_url('mekanik_master/form') ?>" class="alert-link" target="_blank">Tambah Mekanik</a>
                                                </div>
                                            <?php else: ?>
                                                <div id="listMekanik" class="row g-2">
                                                    <?php foreach ($mekaniks as $m):
                                                        $checked_m = isset($existing) && $existing->id_mekanik_master == $m->id_mekanik;
                                                    ?>
                                                        <div class="col-md-4 col-lg-3">
                                                            <label class="person-card d-block border rounded p-2 h-100 <?= $checked_m ? 'selected' : '' ?>"
                                                                style="cursor:pointer;" for="mekanik_<?= $m->id_mekanik ?>">
                                                                <input type="radio" class="d-none mekanik-radio" name="id_mekanik_master"
                                                                    id="mekanik_<?= $m->id_mekanik ?>"
                                                                    value="<?= $m->id_mekanik ?>"
                                                                    data-nama="<?= html_escape($m->nama) ?>"
                                                                    <?= $checked_m ? 'checked' : '' ?>>
                                                                <div class="d-flex align-items-start gap-2">
                                                                    <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center flex-shrink-0 text-dark fw-bold"
                                                                        style="width:32px;height:32px;font-size:12px;">
                                                                        <?= strtoupper(substr($m->nama, 0, 2)) ?>
                                                                    </div>
                                                                    <div class="flex-grow-1 min-w-0">
                                                                        <div class="fw-semibold small text-truncate"><?= html_escape($m->nama) ?></div>
                                                                        <?php if ($m->perusahaan): ?>
                                                                            <div class="text-muted" style="font-size:10px;"><?= html_escape($m->perusahaan) ?></div>
                                                                        <?php endif; ?>
                                                                        <?php if ($m->jabatan): ?>
                                                                            <div class="text-muted" style="font-size:10px;"><?= html_escape($m->jabatan) ?></div>
                                                                        <?php endif; ?>
                                                                        <!-- Status ketersediaan (diisi via JS setelah tanggal dipilih) -->
                                                                        <div class="avail-status-m mt-1" data-id="<?= $m->id_mekanik ?>"></div>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                                <div class="text-danger small mt-2" id="err_mekanik"></div>
                                            <?php endif; ?>

                                        </div>
                                    </div>
                                </div>

                                <!-- ── INSPEKTOR (dari users role 4) ── -->
                                <div class="col-12">
                                    <div class="card border-primary mb-0">
                                        <div class="card-header bg-primary text-white py-2 d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 fw-bold text-white">
                                                <i class="bi bi-person-badge me-2"></i>Inspektor Sistem
                                                <span class="badge bg-light text-primary ms-2" style="font-size:10px;">
                                                    User Login — Role Inspektor
                                                </span>
                                            </h6>
                                            <small class="text-white opacity-75">Yang mengisi form inspeksi di sistem</small>
                                        </div>
                                        <div class="card-body pt-3">

                                            <?php if (empty($inspektors)): ?>
                                                <div class="alert alert-warning py-2 mb-0">
                                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                                    Belum ada user dengan role Inspektor.
                                                    <a href="<?= site_url('usermanagement') ?>" class="alert-link">Kelola User</a>
                                                </div>
                                            <?php else: ?>
                                                <div id="listInspektor" class="row g-2">
                                                    <?php foreach ($inspektors as $ins):
                                                        $id_ins_existing = isset($existing) ? ($existing->id_inspektor ?? $existing->id_mekanik) : null;
                                                        $checked_i = $id_ins_existing && $id_ins_existing == $ins->id_user;
                                                    ?>
                                                        <div class="col-md-4 col-lg-3">
                                                            <label class="person-card d-block border rounded p-2 h-100 <?= $checked_i ? 'selected' : '' ?>"
                                                                style="cursor:pointer;" for="ins_<?= $ins->id_user ?>">
                                                                <input type="radio" class="d-none inspektor-radio" name="id_inspektor"
                                                                    id="ins_<?= $ins->id_user ?>"
                                                                    value="<?= $ins->id_user ?>"
                                                                    data-nama="<?= html_escape($ins->nama) ?>"
                                                                    <?= $checked_i ? 'checked' : '' ?>>
                                                                <div class="d-flex align-items-start gap-2">
                                                                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center flex-shrink-0 text-white fw-bold"
                                                                        style="width:32px;height:32px;font-size:12px;">
                                                                        <?= strtoupper(substr($ins->nama, 0, 2)) ?>
                                                                    </div>
                                                                    <div class="flex-grow-1 min-w-0">
                                                                        <div class="fw-semibold small text-truncate"><?= html_escape($ins->nama) ?></div>
                                                                        <div class="text-muted" style="font-size:10px;"><?= html_escape($ins->email) ?></div>
                                                                        <?php if ($ins->jabatan): ?>
                                                                            <div class="text-muted" style="font-size:10px;"><?= html_escape($ins->jabatan) ?></div>
                                                                        <?php endif; ?>
                                                                        <!-- Status ketersediaan -->
                                                                        <div class="avail-status-i mt-1" data-id="<?= $ins->id_user ?>"></div>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                                <div class="text-danger small mt-2" id="err_inspektor"></div>
                                            <?php endif; ?>

                                        </div>
                                    </div>
                                </div>

                                <!-- ── JADWAL HARI INI (slot yang sudah terisi) ── -->
                                <div class="col-12" id="boxJadwalHari" style="display:none;">
                                    <div class="card border-secondary mb-0">
                                        <div class="card-header bg-secondary text-white py-2">
                                            <h6 class="mb-0 fw-bold text-white">
                                                <i class="bi bi-clock-history me-2"></i>Jadwal Lain di Hari yang Sama
                                            </h6>
                                        </div>
                                        <div class="card-body p-0">
                                            <div id="jadwalHariContent" class="p-3 text-muted small">
                                                Pilih tanggal untuk melihat jadwal yang sudah ada.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Keterangan -->
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Keterangan / Catatan</label>
                                    <textarea class="form-control" id="keterangan" rows="2"
                                        placeholder="Catatan tambahan untuk jadwal ini (opsional)..."
                                        maxlength="500"><?= html_escape(isset($existing) ? $existing->keterangan : '') ?></textarea>
                                </div>

                            </div><!-- end row -->

                            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                <a href="<?= base_url('jadwal') ?>" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Kembali
                                </a>
                                <button type="button" class="btn btn-primary text-white" id="btnSimpanJadwal">
                                    <i class="bi bi-calendar-check me-1"></i>
                                    <?= isset($existing) && $existing->id_jadwal ? 'Update Jadwal' : 'Simpan Jadwal' ?>
                                </button>
                            </div>

                        </div><!-- end formJadwal -->
                    </div>
                </div>

            </div>
        </div>
    </section>
</main>


<style>
    /* Person card — mekanik / inspektor selector */
    .person-card {
        transition: border-color .15s, background .15s;
        user-select: none;
    }

    .person-card:hover {
        border-color: #adb5bd !important;
    }

    .person-card.selected {
        border-color: #4154f1 !important;
        background: #f0f2ff;
    }

    .person-card.konflik {
        border-color: #dc3545 !important;
        background: #fff5f5;
        opacity: .8;
    }

    .person-card.available {
        border-color: #2eca6a !important;
    }

    /* Availability badges */
    .badge-avail-ok {
        background: #d4edda;
        color: #155724;
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 3px;
    }

    .badge-avail-warn {
        background: #fff3cd;
        color: #856404;
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 3px;
    }

    .badge-avail-busy {
        background: #f8d7da;
        color: #721c24;
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 3px;
    }
</style>


<script>
    $(function() {
        var csrfName = '<?= $this->security->get_csrf_token_name() ?>';
        var csrfHash = '<?= $this->security->get_csrf_hash() ?>';
        var jenisKendaraan = '<?= html_escape($pengajuan->jenis_kendaraan) ?>';
        var excludeJadwalId = '<?= isset($existing) ? $existing->id_jadwal : '' ?>';
        var checkTimer = null;

        // ── Flatpickr ──────────────────────────────────────────────────────
        flatpickr('#tanggal_uji', {
            enableTime: true,
            dateFormat: 'Y-m-d H:i',
            minDate: 'today',
            time_24hr: true,
            minuteIncrement: 30,
            locale: {
                firstDayOfWeek: 1
            },
            onChange: function(dates, str) {
                clearTimeout(checkTimer);
                if (str) checkTimer = setTimeout(function() {
                    cekKetersediaan(str);
                }, 500);
            },
        });

        // ── Highlight person card saat dipilih ─────────────────────────────
        $(document).on('change', '.mekanik-radio, .inspektor-radio', function() {
            var group = $(this).hasClass('mekanik-radio') ? '.mekanik-radio' : '.inspektor-radio';
            $(group).closest('.person-card').removeClass('selected');
            $(this).closest('.person-card').addClass('selected');
        });

        // ── Cek ketersediaan semua mekanik & inspektor ─────────────────────
        function cekKetersediaan(tanggal) {
            if (!tanggal) return;

            // Reset semua status dulu
            $('.avail-status-m, .avail-status-i').html(
                '<span class="badge-avail-warn"><i class="bi bi-hourglass-split me-1"></i>Memeriksa...</span>'
            );

            var post = {};
            post[csrfName] = csrfHash;
            post.jenis_kendaraan = jenisKendaraan;
            post.tanggal_uji = tanggal;
            post.exclude_jadwal_id = excludeJadwalId;

            $.post('<?= site_url('mekanik_master/get_available') ?>', post, function(res) {
                if (!res || res.status !== 'success') return;

                // Update mekanik master cards
                $.each(res.data, function(i, m) {
                    var $status = $('.avail-status-m[data-id="' + m.id_mekanik + '"]');
                    var $card = $status.closest('.person-card');
                    if (m.konflik) {
                        $status.html('<span class="badge-avail-busy"><i class="bi bi-x-circle me-1"></i>Konflik (&lt;1 jam)</span>');
                        $card.addClass('konflik').removeClass('available');
                    } else {
                        $status.html('<span class="badge-avail-ok"><i class="bi bi-check-circle me-1"></i>Tersedia</span>');
                        $card.addClass('available').removeClass('konflik');
                    }
                });

                // Tampilkan jadwal lain di hari yang sama
                tampilJadwalHari(tanggal);
            }, 'json');

            // Cek inspektor (users role 4) satu per satu via AJAX
            $('.inspektor-radio').each(function() {
                var id_ins = $(this).val();
                var $status = $('.avail-status-i[data-id="' + id_ins + '"]');
                var $card = $(this).closest('.person-card');

                var postIns = {};
                postIns[csrfName] = csrfHash;
                postIns.id_inspektor = id_ins;
                postIns.tanggal_uji = tanggal;
                postIns.exclude_jadwal_id = excludeJadwalId;

                $.post('<?= site_url('jadwal/cek_konflik_inspektor') ?>', postIns, function(res) {
                    if (!res) return;
                    if (res.konflik) {
                        $status.html('<span class="badge-avail-busy"><i class="bi bi-x-circle me-1"></i>Konflik (&lt;1 jam)</span>');
                        $card.addClass('konflik').removeClass('available');
                    } else {
                        $status.html('<span class="badge-avail-ok"><i class="bi bi-check-circle me-1"></i>Tersedia</span>');
                        $card.addClass('available').removeClass('konflik');
                    }
                }, 'json');
            });
        }

        // ── Tampilkan jadwal lain di hari yang sama ────────────────────────
        function tampilJadwalHari(tanggal) {
            var post = {};
            post[csrfName] = csrfHash;
            post.tanggal = tanggal;

            $.post('<?= site_url('jadwal/get_by_date') ?>', post, function(res) {
                if (!res || res.status !== 'success') return;

                var $box = $('#boxJadwalHari');
                var $cont = $('#jadwalHariContent');

                if (!res.data || res.data.length === 0) {
                    $box.hide();
                    return;
                }

                var html = '<table class="table table-sm table-hover mb-0">' +
                    '<thead class="table-light"><tr><th>Waktu</th><th>Kendaraan</th>' +
                    '<th>Inspektor</th><th>Mekanik</th></tr></thead><tbody>';

                $.each(res.data, function(i, j) {
                    html += '<tr>' +
                        '<td class="fw-semibold">' + j.waktu + '</td>' +
                        '<td>' + (j.no_polisi || '-') + '<br><small class="text-muted">' + (j.jenis_kendaraan || '') + '</small></td>' +
                        '<td><small>' + (j.nama_inspektor || '-') + '</small></td>' +
                        '<td><small>' + (j.nama_mekanik || '-') + '</small></td>' +
                        '</tr>';
                });
                html += '</tbody></table>';

                $cont.html(html);
                $box.show();
            }, 'json');
        }

        // Trigger cek saat halaman load jika ada tanggal (edit mode)
        var initTanggal = $('#tanggal_uji').val();
        if (initTanggal) {
            cekKetersediaan(initTanggal);
        }

        // ── Submit ──────────────────────────────────────────────────────────
        $('#btnSimpanJadwal').on('click', function() {
            var tanggal = $('#tanggal_uji').val().trim();
            var lokasi = $('#lokasi').val().trim();
            var id_mekanik_m = $('input[name="id_mekanik_master"]:checked').val();
            var id_inspektor = $('input[name="id_inspektor"]:checked').val();

            $('#err_mekanik, #err_inspektor').text('');
            var err = false;

            if (!tanggal) {
                toastr.warning('Tanggal inspeksi wajib diisi.');
                err = true;
            }
            if (!lokasi) {
                toastr.warning('Lokasi inspeksi wajib diisi.');
                err = true;
            }
            if (!id_mekanik_m) {
                $('#err_mekanik').text('Pilih mekanik lapangan.');
                err = true;
            }
            if (!id_inspektor) {
                $('#err_inspektor').text('Pilih inspektor sistem.');
                err = true;
            }
            if (err) return;

            // Warn jika ada yang konflik tapi tetap bisa lanjut
            var adaKonflik = $('.person-card.konflik input:checked').length > 0;
            if (adaKonflik) {
                Swal.fire({
                    title: 'Perhatian: Ada Konflik Jadwal',
                    html: 'Salah satu atau kedua petugas memiliki jadwal lain dalam rentang 1 jam. Tetap simpan?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Tetap Simpan',
                    cancelButtonText: 'Batal',
                }).then(function(r) {
                    if (r.isConfirmed) doSave();
                });
            } else {
                var namaMekanik = $('input[name="id_mekanik_master"]:checked').data('nama');
                var namaInspektor = $('input[name="id_inspektor"]:checked').data('nama');
                Swal.fire({
                    title: 'Simpan Jadwal?',
                    html: '<strong>' + tanggal + '</strong><br>' +
                        'Lokasi: ' + lokasi + '<br>' +
                        'Mekanik: <strong>' + namaMekanik + '</strong><br>' +
                        'Inspektor: <strong>' + namaInspektor + '</strong>',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#4154f1',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bi bi-calendar-check me-1"></i>Ya, Simpan',
                    cancelButtonText: 'Batal',
                }).then(function(r) {
                    if (r.isConfirmed) doSave();
                });
            }
        });

        function doSave() {
            NProgress.start();
            $('#btnSimpanJadwal').prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');

            var post = {
                id_pengajuan: $('#hid_id_pengajuan').val(),
                id_jadwal: $('#hid_id_jadwal').val(),
                tanggal_uji: $('#tanggal_uji').val(),
                lokasi: $('#lokasi').val(),
                id_mekanik_master: $('input[name="id_mekanik_master"]:checked').val(),
                id_inspektor: $('input[name="id_inspektor"]:checked').val(),
                keterangan: $('#keterangan').val(),
            };
            post[csrfName] = csrfHash;

            $.ajax({
                url: '<?= site_url('jadwal/store') ?>',
                type: 'POST',
                data: post,
                dataType: 'json',
                success: function(res) {
                    NProgress.done();
                    $('#btnSimpanJadwal').prop('disabled', false)
                        .html('<i class="bi bi-calendar-check me-1"></i>Simpan Jadwal');

                    if (res.status === 'success') {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: res.message,
                            icon: 'success',
                            confirmButtonColor: '#4154f1',
                        }).then(function() {
                            window.location.href = res.redirect;
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: res.message,
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                },
                error: function() {
                    NProgress.done();
                    $('#btnSimpanJadwal').prop('disabled', false)
                        .html('<i class="bi bi-calendar-check me-1"></i>Simpan Jadwal');
                    toastr.error('Terjadi kesalahan server.');
                }
            });
        }

    });
</script>