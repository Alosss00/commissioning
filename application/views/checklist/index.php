<main id="main" class="main">

    <div class="pagetitle">
        <h1>Checklist Template</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Home</a></li>
                <li class="breadcrumb-item active">Checklist Template</li>
            </ol>
        </nav>
    </div>

    <section class="section">

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <p class="text-muted mb-0">
                Total <strong><?= count($templates) ?></strong> template aktif terdaftar.
            </p>
            <button class="btn btn-primary btn-sm" id="btnTambahTemplate">
                <i class="bi bi-plus-circle me-1"></i>Tambah Template
            </button>
        </div>

        <div class="row">
            <?php foreach ($templates as $tmpl): ?>
                <div class="col-xl-4 col-md-6 mb-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body pt-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-secondary font-monospace"><?= html_escape($tmpl->kode) ?></span>
                                <span class="badge <?= $tmpl->is_active ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $tmpl->is_active ? 'Aktif' : 'Nonaktif' ?>
                                </span>
                            </div>
                            <h6 class="fw-bold mb-1"><?= html_escape($tmpl->nama_tipe) ?></h6>
                            <p class="text-muted small mb-3"><?= html_escape($tmpl->nama_template) ?></p>
                            <a href="<?= site_url('checklist/template/' . $tmpl->id_template) ?>"
                                class="btn btn-sm btn-outline-primary w-100">
                                <i class="bi bi-list-check me-1"></i>Lihat & Kelola Items
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($templates)): ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        Belum ada template checklist. Klik <strong>Tambah Template</strong> untuk mulai.
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </section>
</main>

<!-- Modal Tambah Template -->
<div class="modal fade" id="modalTambahTemplate" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Template Checklist</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Tipe Kendaraan <span class="text-danger">*</span></label>
                    <select class="form-select" id="tmpl_tipe">
                        <option value="">— Memuat data... —</option>
                    </select>
                    <div class="form-text text-muted">Hanya tipe yang belum punya template yang ditampilkan.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Kode Template <span class="text-danger">*</span></label>
                    <input type="text" class="form-control text-uppercase font-monospace"
                        id="tmpl_kode" maxlength="20" placeholder="contoh: 002V">
                    <div class="form-text text-muted">Unik, akan otomatis kapital.</div>
                </div>

                <div class="mb-2">
                    <label class="form-label fw-semibold">Nama Template <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="tmpl_nama" maxlength="200"
                        placeholder="contoh: Dump Truck Commissioning Checklist">
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSimpanTemplate">
                    <i class="bi bi-check-lg me-1"></i>Buat Template
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        var CSRF_NAME = '<?= $this->security->get_csrf_token_name() ?>';
        var CSRF_HASH = '<?= $this->security->get_csrf_hash() ?>';
        var modal = new bootstrap.Modal(document.getElementById('modalTambahTemplate'));

        // ── Buka modal & muat tipe tersedia ──────────────────────────────────────
        $('#btnTambahTemplate').on('click', function() {
            $('#tmpl_tipe').html('<option value="">— Memuat... —</option>');
            $('#tmpl_kode').val('');
            $('#tmpl_nama').val('');

            var d = {};
            d[CSRF_NAME] = CSRF_HASH;
            $.post('<?= site_url('checklist/get_tipe_tersedia') ?>', d, function(res) {
                if (res.status !== 'success') {
                    toastr.error('Gagal memuat tipe kendaraan.');
                    return;
                }
                if (res.data.length === 0) {
                    $('#tmpl_tipe').html('<option value="">— Semua tipe sudah punya template —</option>');
                } else {
                    var opts = '<option value="">— Pilih tipe kendaraan —</option>';
                    $.each(res.data, function(_, t) {
                        var kode = t.kode_tipe ? ' (' + t.kode_tipe + ')' : '';
                        opts += '<option value="' + t.id_tipe_kendaraan + '">' + t.nama_tipe + kode + '</option>';
                    });
                    $('#tmpl_tipe').html(opts);
                }
            }, 'json');

            modal.show();
        });

        // Auto-isi nama template saat tipe dipilih
        $('#tmpl_tipe').on('change', function() {
            var nama = $(this).find('option:selected').text().trim();
            if (nama && nama !== '— Pilih tipe kendaraan —') {
                if (!$('#tmpl_nama').val()) {
                    $('#tmpl_nama').val(nama + ' Commissioning Checklist');
                }
            }
        });

        // Auto uppercase kode
        $('#tmpl_kode').on('input', function() {
            this.value = this.value.toUpperCase();
        });

        // ── Simpan template ───────────────────────────────────────────────────────
        $('#btnSimpanTemplate').on('click', function() {
            var tipe = $('#tmpl_tipe').val();
            var kode = $.trim($('#tmpl_kode').val()).toUpperCase();
            var nama = $.trim($('#tmpl_nama').val());

            if (!tipe) {
                toastr.warning('Pilih tipe kendaraan.');
                return;
            }
            if (!kode) {
                toastr.warning('Kode template wajib diisi.');
                $('#tmpl_kode').focus();
                return;
            }
            if (!nama) {
                toastr.warning('Nama template wajib diisi.');
                $('#tmpl_nama').focus();
                return;
            }

            var $btn = $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...');

            var d = {};
            d[CSRF_NAME] = CSRF_HASH;
            d.id_tipe_kendaraan = tipe;
            d.kode = kode;
            d.nama_template = nama;

            $.post('<?= site_url('checklist/save_template') ?>', d, function(res) {
                $btn.prop('disabled', false).html('<i class="bi bi-check-lg me-1"></i>Buat Template');
                if (res.status === 'success') {
                    toastr.success(res.message);
                    // Redirect ke halaman detail template untuk tambah item
                    setTimeout(function() {
                        window.location.href = res.redirect;
                    }, 800);
                } else {
                    toastr.error(res.message);
                }
            }, 'json').fail(function() {
                $btn.prop('disabled', false).html('<i class="bi bi-check-lg me-1"></i>Buat Template');
                toastr.error('Terjadi kesalahan server.');
            });
        });
    });
</script>