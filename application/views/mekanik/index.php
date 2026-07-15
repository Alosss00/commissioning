<main id="main" class="main">

    <div class="pagetitle">
        <h1>Master Data Mekanik</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Home</a></li>
                <li class="breadcrumb-item active">Master Mekanik</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-12">

                <?php if ($this->session->flashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show py-2">
                        <i class="bi bi-check-circle me-2"></i><?= $this->session->flashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show py-2">
                        <i class="bi bi-x-circle me-2"></i><?= $this->session->flashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body pt-4">

                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <h5 class="card-title mb-0">
                                Daftar Mekanik / Teknisi Lapangan
                                <span class="badge bg-primary text-white ms-2"><?= count($list) ?></span>
                            </h5>
                            <div class="d-flex gap-2 flex-wrap">
                                <form method="get" class="d-flex gap-2">
                                    <input type="text" name="search" class="form-control form-control-sm"
                                        placeholder="Cari nama / perusahaan..."
                                        value="<?= html_escape($filter['search'] ?? '') ?>"
                                        style="width:200px;">
                                    <select name="status" class="form-select form-select-sm" style="width:130px;">
                                        <option value="">Semua Status</option>
                                        <option value="1" <?= ($filter['is_active'] === '1') ? 'selected' : '' ?>>Aktif</option>
                                        <option value="0" <?= ($filter['is_active'] === '0') ? 'selected' : '' ?>>Nonaktif</option>
                                    </select>
                                    <button class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i></button>
                                </form>
                                <a href="<?= site_url('mekanik_master/form') ?>" class="btn btn-sm btn-primary text-white">
                                    <i class="bi bi-plus-circle me-1"></i>Tambah Mekanik
                                </a>
                            </div>
                        </div>

                        <?php if (empty($list)): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-person-gear fs-1 d-block mb-2 opacity-40"></i>
                                <p class="mb-1">Belum ada data mekanik.</p>
                                <a href="<?= site_url('mekanik_master/form') ?>" class="btn btn-sm btn-primary text-white mt-2">
                                    <i class="bi bi-plus-circle me-1"></i>Tambah Mekanik Pertama
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Nama Mekanik</th>
                                            <th>Perusahaan</th>
                                            <th>Kontak</th>
                                            <th>Tipe Kendaraan</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($list as $i => $m): ?>
                                            <tr class="<?= !$m->is_active ? 'opacity-60' : '' ?>">
                                                <td><?= $i + 1 ?></td>
                                                <td>
                                                    <strong><?= html_escape($m->nama) ?></strong>
                                                    <?php if ($m->jabatan): ?>
                                                        <br><small class="text-muted"><?= html_escape($m->jabatan) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= html_escape($m->perusahaan ?: '—') ?></td>
                                                <td>
                                                    <?php if ($m->no_hp): ?>
                                                        <small><i class="bi bi-telephone me-1 text-muted"></i><?= html_escape($m->no_hp) ?></small><br>
                                                    <?php endif; ?>
                                                    <?php if ($m->email): ?>
                                                        <small><i class="bi bi-envelope me-1 text-muted"></i><?= html_escape($m->email) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td style="max-width:260px;">
                                                    <?php if ($m->tipe_list): ?>
                                                        <?php foreach (explode(', ', $m->tipe_list) as $t): ?>
                                                            <span class="badge bg-info text-white me-1 mb-1" style="font-size:10px;"><?= html_escape($t) ?></span>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <span class="text-muted small">Semua tipe</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-toggle-active py-0 px-2
                        <?= $m->is_active ? 'btn-success text-white' : 'btn-outline-secondary' ?>"
                                                        data-id="<?= $m->id_mekanik ?>"
                                                        data-nama="<?= html_escape($m->nama) ?>"
                                                        title="<?= $m->is_active ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                                        <?= $m->is_active ? 'Aktif' : 'Nonaktif' ?>
                                                    </button>
                                                </td>
                                                <td class="text-center">
                                                    <a href="<?= site_url('mekanik_master/form/' . $m->id_mekanik) ?>"
                                                        class="btn btn-sm btn-outline-primary py-0 px-2">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-outline-danger py-0 px-2 btn-delete"
                                                        data-id="<?= $m->id_mekanik ?>"
                                                        data-nama="<?= html_escape($m->nama) ?>">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </section>
</main>


<script>
    $(function() {
        var csrf = '<?= $this->security->get_csrf_token_name() ?>';
        var hash = '<?= $this->security->get_csrf_hash() ?>';

        // Toggle active
        $(document).on('click', '.btn-toggle-active', function() {
            var id = $(this).data('id');
            var nama = $(this).data('nama');
            var $btn = $(this);
            Swal.fire({
                title: 'Ubah Status?',
                html: 'Mekanik <strong>' + nama + '</strong> akan diubah statusnya.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4154f1',
            }).then(function(r) {
                if (!r.isConfirmed) return;
                var post = {};
                post[csrf] = hash;
                post.id = id;
                $.post('<?= site_url('mekanik_master/toggle') ?>', post, function(res) {
                    if (res.status === 'success') location.reload();
                    else toastr.error('Gagal mengubah status.');
                }, 'json');
            });
        });

        // Delete
        $(document).on('click', '.btn-delete', function() {
            var id = $(this).data('id');
            var nama = $(this).data('nama');
            Swal.fire({
                title: 'Hapus Mekanik?',
                html: '<strong>' + nama + '</strong> akan dihapus permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Ya, Hapus',
            }).then(function(r) {
                if (!r.isConfirmed) return;
                var post = {};
                post[csrf] = hash;
                post.id = id;
                $.post('<?= site_url('mekanik_master/delete') ?>', post, function(res) {
                    if (res.status === 'success') {
                        toastr.success(res.message);
                        setTimeout(function() {
                            location.reload();
                        }, 700);
                    } else Swal.fire({
                        title: 'Tidak bisa dihapus',
                        html: res.message,
                        icon: 'warning'
                    });
                }, 'json');
            });
        });
    });
</script>