<main id="main" class="main">

    <div class="pagetitle">
        <h1>Template: <?= html_escape($template->nama_tipe) ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url('checklist') ?>">Checklist Template</a></li>
                <li class="breadcrumb-item active"><?= html_escape($template->kode) ?></li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-12">

                <!-- Info Template -->
                <div class="card mb-3">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <span class="badge bg-secondary me-2"><?= html_escape($template->kode) ?></span>
                                <strong><?= html_escape($template->nama_template) ?></strong>
                            </div>
                            <button class="btn btn-sm btn-primary" id="btnTambahItem">
                                <i class="bi bi-plus-circle me-1"></i>Tambah Item
                            </button>
                        </div>
                    </div>
                </div>

                <!-- CRITICAL ITEMS -->
                <?php foreach (['CRITICAL', 'GENERAL'] as $kat): ?>
                    <div class="card mb-3">
                        <div class="card-body pt-4">
                            <h6 class="card-title d-flex align-items-center gap-2 mb-3">
                                <?php if ($kat === 'CRITICAL'): ?>
                                    <span class="badge bg-danger rounded-pill px-3">CRITICAL ITEMS</span>
                                <?php else: ?>
                                    <span class="badge bg-primary rounded-pill px-3">GENERAL REQUIREMENTS</span>
                                <?php endif; ?>
                                <span class="text-muted small fw-normal">(<?= count($grouped[$kat]) ?> item)</span>
                            </h6>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="<?= $kat === 'CRITICAL' ? 'table-danger' : 'table-primary' ?>">
                                        <tr>
                                            <th style="width:60px;" class="text-center">No.</th>
                                            <th>Kriteria / Criteria</th>
                                            <th style="width:110px;" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($grouped[$kat])): ?>
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-3">Belum ada item</td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php foreach ($grouped[$kat] as $item): ?>
                                            <tr>
                                                <td class="text-center fw-bold"><?= html_escape($item->no_urut) ?></td>
                                                <td><?= html_escape($item->kriteria) ?></td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-outline-secondary py-0 btn-edit-item"
                                                        data-id="<?= $item->id_item ?>"
                                                        data-template="<?= $item->id_template ?>"
                                                        data-kategori="<?= $item->kategori ?>"
                                                        data-nourut="<?= html_escape($item->no_urut) ?>"
                                                        data-kriteria="<?= html_escape($item->kriteria) ?>"
                                                        title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger py-0 btn-delete-item"
                                                        data-id="<?= $item->id_item ?>"
                                                        data-kriteria="<?= html_escape(substr($item->kriteria, 0, 50)) ?>..."
                                                        title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
        </div>
    </section>
</main>


<!-- Modal Tambah / Edit Item -->
<div class="modal fade" id="modalItem" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalItemTitle">Tambah Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="item_id">
                <input type="hidden" id="item_id_template" value="<?= $template->id_template ?>">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                    <select class="form-select" id="item_kategori">
                        <option value="CRITICAL">CRITICAL</option>
                        <option value="GENERAL">GENERAL</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">No. Urut <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="item_nourut" placeholder="misal: 1, 2, 3">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Kriteria / Criteria <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="item_kriteria" rows="3" placeholder="Deskripsi item pemeriksaan..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSimpanItem">
                    <i class="bi bi-check-lg me-1"></i>Simpan
                </button>
            </div>
        </div>
    </div>
</div>


<script>
    $(function() {
        var csrf_name = '<?= $this->security->get_csrf_token_name() ?>';
        var csrf_hash = '<?= $this->security->get_csrf_hash() ?>';
        var modalItem = new bootstrap.Modal(document.getElementById('modalItem'));

        // Tambah item baru
        $('#btnTambahItem').on('click', function() {
            $('#modalItemTitle').text('Tambah Item');
            $('#item_id').val('');
            $('#item_kategori').val('GENERAL');
            $('#item_nourut').val('');
            $('#item_kriteria').val('');
            modalItem.show();
        });

        // Edit item
        $(document).on('click', '.btn-edit-item', function() {
            $('#modalItemTitle').text('Edit Item');
            $('#item_id').val($(this).data('id'));
            $('#item_kategori').val($(this).data('kategori'));
            $('#item_nourut').val($(this).data('nourut'));
            $('#item_kriteria').val($(this).data('kriteria'));
            modalItem.show();
        });

        // Simpan item
        $('#btnSimpanItem').on('click', function() {
            var kriteria = $.trim($('#item_kriteria').val());
            var nourut = $.trim($('#item_nourut').val());

            if (!nourut || !kriteria) {
                toastr.warning('No. urut dan kriteria wajib diisi.');
                return;
            }

            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

            var postData = {
                id_item: $('#item_id').val(),
                id_template: $('#item_id_template').val(),
                kategori: $('#item_kategori').val(),
                no_urut: nourut,
                kriteria: kriteria,
            };
            postData[csrf_name] = csrf_hash;

            $.ajax({
                url: '<?= site_url('checklist/save_item') ?>',
                type: 'POST',
                data: postData,
                dataType: 'json',
                success: function(res) {
                    $('#btnSimpanItem').prop('disabled', false).html('<i class="bi bi-check-lg me-1"></i>Simpan');
                    if (res.status === 'success') {
                        toastr.success(res.message);
                        modalItem.hide();
                        setTimeout(function() {
                            location.reload();
                        }, 800);
                    } else {
                        toastr.error(res.message || 'Gagal menyimpan.');
                    }
                },
                error: function() {
                    $('#btnSimpanItem').prop('disabled', false).html('<i class="bi bi-check-lg me-1"></i>Simpan');
                    toastr.error('Terjadi kesalahan server.');
                }
            });
        });

        // Hapus item
        $(document).on('click', '.btn-delete-item', function() {
            var id = $(this).data('id');
            var kriteria = $(this).data('kriteria');

            Swal.fire({
                title: 'Hapus Item?',
                html: '<small>' + kriteria + '</small>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
            }).then(function(r) {
                if (!r.isConfirmed) return;

                var postData = {
                    id_item: id
                };
                postData[csrf_name] = csrf_hash;

                $.ajax({
                    url: '<?= site_url('checklist/delete_item') ?>',
                    type: 'POST',
                    data: postData,
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            toastr.success(res.message);
                            setTimeout(function() {
                                location.reload();
                            }, 600);
                        }
                    }
                });
            });
        });
    });
</script>