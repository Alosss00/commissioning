<main id="main" class="main">

    <div class="pagetitle">
        <h1><?= $mekanik ? 'Edit' : 'Tambah' ?> Mekanik</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url('mekanik_master') ?>">Master Mekanik</a></li>
                <li class="breadcrumb-item active"><?= $mekanik ? 'Edit' : 'Tambah' ?></li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row justify-content-center">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-body pt-4">
                        <h5 class="card-title mb-4">
                            <i class="bi bi-person-gear me-2 text-primary"></i>
                            <?= $mekanik ? 'Edit Data Mekanik' : 'Tambah Mekanik Baru' ?>
                        </h5>

                        <form action="<?= site_url('mekanik_master/save') ?>" method="POST">
                            <?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()) ?>
                            <input type="hidden" name="id_mekanik" value="<?= $mekanik ? $mekanik->id_mekanik : '' ?>">

                            <div class="row g-3">

                                <!-- Nama -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nama Mekanik <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="nama" required
                                        value="<?= html_escape($mekanik->nama ?? '') ?>"
                                        placeholder="Nama lengkap mekanik/teknisi">
                                </div>

                                <!-- Jabatan -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Jabatan</label>
                                    <input type="text" class="form-control" name="jabatan"
                                        value="<?= html_escape($mekanik->jabatan ?? '') ?>"
                                        placeholder="misal: Senior Mechanic, Teknisi Heavy Equipment">
                                </div>

                                <!-- Perusahaan -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Perusahaan / Instansi</label>
                                    <?php $current_perusahaan = $mekanik ? $mekanik->perusahaan : ''; ?>
                                    <select class="form-select" name="perusahaan" id="perusahaan_select">
                                        <option value="">-- Pilih Perusahaan --</option>
                                        <?php 
                                        $found = false;
                                        foreach ($semua_perusahaan as $p) {
                                            if ($current_perusahaan === $p->nama_perusahaan) {
                                                $found = true;
                                            }
                                        }
                                        ?>
                                        <?php if (!empty($current_perusahaan) && !$found): ?>
                                            <option value="<?= html_escape($current_perusahaan) ?>" selected>
                                                <?= html_escape($current_perusahaan) ?>
                                            </option>
                                        <?php endif; ?>
                                        <?php foreach ($semua_perusahaan as $p): ?>
                                            <option value="<?= html_escape($p->nama_perusahaan) ?>" <?= ($current_perusahaan === $p->nama_perusahaan) ? 'selected' : '' ?>>
                                                <?= html_escape($p->nama_perusahaan) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- No HP -->
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">No. HP</label>
                                    <input type="text" class="form-control" name="no_hp"
                                        value="<?= html_escape($mekanik->no_hp ?? '') ?>"
                                        placeholder="08xx-xxxx-xxxx">
                                </div>

                                <!-- Email -->
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Email</label>
                                    <input type="email" class="form-control" name="email"
                                        value="<?= html_escape($mekanik->email ?? '') ?>"
                                        placeholder="email@domain.com">
                                </div>

                                <!-- Tipe Kendaraan -->
                                <!-- Tipe Kendaraan -->
                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        Tipe Kendaraan yang Dapat Diinspeksi
                                        <span class="text-muted fw-normal ms-1 small">(centang semua yang berlaku)</span>
                                    </label>
                                    <div class="border rounded p-3">
                                        <div class="d-flex gap-2 mb-3">
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnPilihSemua">
                                                <i class="bi bi-check-all me-1"></i>Pilih Semua
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnHapusSemua">
                                                <i class="bi bi-x-lg me-1"></i>Hapus Semua
                                            </button>
                                        </div>
                                        <div class="row g-2">
                                            <?php foreach ($semua_tipe as $t): ?>
                                                <div class="col-md-4 col-lg-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input tipe-check" type="checkbox"
                                                            name="tipe_kendaraan[]"
                                                            value="<?= $t->id_tipe_kendaraan ?>"
                                                            id="tipe_<?= $t->id_tipe_kendaraan ?>"
                                                            <?= in_array($t->id_tipe_kendaraan, $tipe_exist) ? 'checked' : '' ?>>
                                                        <label class="form-check-label small" for="tipe_<?= $t->id_tipe_kendaraan ?>">
                                                            <?= html_escape($t->nama_tipe) ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <span id="tipeCount">0</span> tipe dipilih.
                                                Jika tidak ada yang dipilih, mekanik akan muncul untuk semua tipe.
                                            </small>
                                        </div>
                                    </div>
                                </div>

                            </div><!-- end row -->

                            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                <a href="<?= site_url('mekanik_master') ?>" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Kembali
                                </a>
                                <button type="submit" class="btn btn-primary text-white">
                                    <i class="bi bi-check-circle me-1"></i>
                                    <?= $mekanik ? 'Update Data' : 'Simpan Mekanik' ?>
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>


<script>
    $(function() {
        $('#perusahaan_select').select2({
            placeholder: '-- Pilih Perusahaan --',
            allowClear: true,
            width: '100%',
            tags: true
        });

        function updateCount() {
            var n = $('.tipe-check:checked').length;
            $('#tipeCount').text(n);
        }
        updateCount();
        $(document).on('change', '.tipe-check', updateCount);

        $('#btnPilihSemua').on('click', function() {
            $('.tipe-check').prop('checked', true);
            updateCount();
        });
        $('#btnHapusSemua').on('click', function() {
            $('.tipe-check').prop('checked', false);
            updateCount();
        });
    });
</script>