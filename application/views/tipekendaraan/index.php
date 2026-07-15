<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<main id="main" class="main">

    <div class="pagetitle">
        <h1>Master Tipe Kendaraan</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Home</a></li>
                <li class="breadcrumb-item active">Tipe Kendaraan</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body pt-4">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <h5 class="card-title mb-0">Daftar Tipe Kendaraan</h5>
                            <button class="btn btn-primary btn-sm" id="btnTambah">
                                <i class="bi bi-plus-circle me-1"></i>Tambah Tipe
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle"
                                id="tblTipe" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:40px;">#</th>
                                        <th>Nama Tipe</th>
                                        <th style="width:80px;">Kode</th>
                                        <th style="width:145px;">No. Dokumen</th>
                                        <th style="width:80px;">Status</th>
                                        <th style="width:90px;" class="text-center">Kendaraan</th>
                                        <th style="width:90px;" class="text-center">Template</th>
                                        <th style="width:90px;" class="text-center">Mekanik</th>
                                        <th style="width:110px;" class="text-center">Aksi</th>
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


<!-- ═══════════════════════════════════════════════════════════════════
     Modal Tambah / Edit — ukuran lg agar kolom doc muat
════════════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalTipe" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTipeTitle">Tambah Tipe Kendaraan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="tipe_id">

                <!-- ── Identitas ──────────────────────────────────────────── -->
                <div class="row g-3 mb-3">
                    <div class="col-md-7">
                        <label class="form-label fw-semibold">
                            Nama Tipe <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="tipe_nama"
                            maxlength="100" placeholder="contoh: Dump Truck">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Kode Tipe</label>
                        <input type="text" class="form-control text-uppercase font-monospace"
                            id="tipe_kode" maxlength="30" placeholder="contoh: DT">
                        <div class="form-text">Opsional. Otomatis kapital.</div>
                    </div>
                </div>

                <hr class="my-2">
                <p class="text-muted small mb-2">
                    <i class="bi bi-file-earmark-pdf me-1 text-danger"></i>
                    <strong>Info Dokumen PDF</strong>
                    — digunakan otomatis di header &amp; footer saat cetak hasil inspeksi.
                </p>

                <!-- ── Dokumen PDF ────────────────────────────────────────── -->
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">No. Dokumen</label>
                        <input type="text" class="form-control font-monospace"
                            id="doc_no" maxlength="30"
                            placeholder="contoh: TT-OHS-FRO-002E">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">No. Revisi</label>
                        <input type="text" class="form-control" id="no_revisi"
                            maxlength="10" placeholder="01" value="01">
                    </div>
                    <div class="col-md-3">
                        <!-- placeholder kosong supaya layout rapi -->
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Judul Dokumen (Bahasa Indonesia)</label>
                        <input type="text" class="form-control" id="title_id"
                            maxlength="200"
                            placeholder="DAFTAR PERIKSA UJI KELAYAKAN BIS">
                        <div class="form-text">Tampil di baris judul tengah header PDF.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Judul Dokumen (English)</label>
                        <input type="text" class="form-control" id="title_en"
                            maxlength="200"
                            placeholder="Bus Commissioning Checklist">
                        <div class="form-text">Tampil di bawah judul Indonesia (italic).</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama Dokumen Footer (ID)</label>
                        <input type="text" class="form-control" id="doc_name_id"
                            maxlength="200"
                            placeholder="Daftar Periksa Uji Kelayakan Bis">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama Dokumen Footer (EN)</label>
                        <input type="text" class="form-control" id="doc_name_en"
                            maxlength="200"
                            placeholder="Bus Commissioning Checklist">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tanggal Terbit</label>
                        <input type="date" class="form-control" id="tgl_terbit">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tanggal Tinjau Ulang</label>
                        <input type="date" class="form-control" id="tgl_review">
                    </div>
                    <div class="col-md-4">
                        <!-- kosong — bisa dipakai nanti -->
                    </div>
                </div>

                <!-- Preview doc info -->
                <div id="docPreview" class="mt-3 d-none">
                    <div class="alert alert-light border py-2 small">
                        <i class="bi bi-eye me-1 text-secondary"></i>
                        <strong>Preview footer PDF:</strong>
                        <span id="prevDocNo" class="font-monospace ms-1"></span>
                        &nbsp;|&nbsp;
                        Rev. <span id="prevRevisi"></span>
                        &nbsp;|&nbsp;
                        Terbit: <span id="prevTerbit"></span>
                        &nbsp;|&nbsp;
                        Tinjau: <span id="prevReview"></span>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSimpan">
                    <i class="bi bi-check-lg me-1"></i>Simpan
                </button>
            </div>
        </div>
    </div>
</div>


<script>
    $(function() {
        var CSRF_NAME = '<?= $this->security->get_csrf_token_name() ?>';
        var CSRF_HASH = '<?= $this->security->get_csrf_hash() ?>';
        var modal = new bootstrap.Modal(document.getElementById('modalTipe'));

        /* ── DataTable ──────────────────────────────────────────────── */
        var dt = $('#tblTipe').DataTable({
            ajax: {
                url: '<?= site_url('tipekendaraan/get_data') ?>',
                type: 'POST',
                data: function(d) {
                    d[CSRF_NAME] = CSRF_HASH;
                },
            },
            columns: [{
                    data: 'id',
                    orderable: false,
                    className: 'text-center text-muted small'
                },
                {
                    data: 'nama_tipe'
                },
                {
                    data: 'kode_tipe'
                },
                {
                    data: 'doc_no'
                },
                {
                    data: 'status',
                    className: 'text-center'
                },
                {
                    data: 'total_kendaraan',
                    className: 'text-center'
                },
                {
                    data: 'total_template',
                    className: 'text-center'
                },
                {
                    data: 'total_mekanik',
                    className: 'text-center'
                },
                {
                    data: 'aksi',
                    orderable: false,
                    className: 'text-center'
                },
            ],
            order: [
                [0, 'asc']
            ],
            pageLength: 25,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            },
        });

        function refreshDt() {
            CSRF_HASH = '<?= $this->security->get_csrf_hash() ?>';
            dt.ajax.reload(null, false);
        }

        /* ── Helper: reset form ─────────────────────────────────────── */
        function resetForm() {
            $('#tipe_id, #tipe_nama, #tipe_kode, #doc_no, #title_id, #title_en, #doc_name_id, #doc_name_en, #tgl_terbit, #tgl_review').val('');
            $('#no_revisi').val('01');
            $('#docPreview').addClass('d-none');
        }

        /* ── Helper: auto-fill judul dari nama tipe ─────────────────── */
        function autoFillDoc() {
            var nama = $.trim($('#tipe_nama').val());
            if (!nama) return;

            // Hanya auto-fill jika field masih kosong
            if (!$('#title_id').val()) {
                $('#title_id').val('DAFTAR PERIKSA UJI KELAYAKAN ' + nama.toUpperCase());
            }
            if (!$('#title_en').val()) {
                $('#title_en').val(nama + ' Commissioning Checklist');
            }
            if (!$('#doc_name_id').val()) {
                $('#doc_name_id').val('Daftar Periksa Uji Kelayakan ' + nama);
            }
            if (!$('#doc_name_en').val()) {
                $('#doc_name_en').val(nama + ' Commissioning Checklist');
            }
        }

        /* ── Helper: update preview ─────────────────────────────────── */
        function updatePreview() {
            var docNo = $.trim($('#doc_no').val());
            var revisi = $.trim($('#no_revisi').val()) || '01';
            var terbit = $('#tgl_terbit').val();
            var review = $('#tgl_review').val();

            if (docNo || terbit) {
                $('#prevDocNo').text(docNo || '—');
                $('#prevRevisi').text(revisi || '01');
                $('#prevTerbit').text(terbit ? new Date(terbit).toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                }) : '—');
                $('#prevReview').text(review ? new Date(review).toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                }) : '—');
                $('#docPreview').removeClass('d-none');
            } else {
                $('#docPreview').addClass('d-none');
            }
        }

        /* ── Buka modal tambah ──────────────────────────────────────── */
        $('#btnTambah').on('click', function() {
            $('#modalTipeTitle').text('Tambah Tipe Kendaraan');
            resetForm();
            modal.show();
            setTimeout(function() {
                $('#tipe_nama').focus();
            }, 300);
        });

        /* ── Buka modal edit ────────────────────────────────────────── */
        $(document).on('click', '.btn-edit', function() {
            $('#modalTipeTitle').text('Edit Tipe Kendaraan');
            var $b = $(this);
            $('#tipe_id').val($b.data('id'));
            $('#tipe_nama').val($b.data('nama'));
            $('#tipe_kode').val($b.data('kode'));
            $('#doc_no').val($b.data('docno'));
            $('#title_id').val($b.data('titleid'));
            $('#title_en').val($b.data('titleen'));
            $('#doc_name_id').val($b.data('docnameid'));
            $('#doc_name_en').val($b.data('docnameen'));
            $('#tgl_terbit').val($b.data('tglterbit'));
            $('#tgl_review').val($b.data('tglreview'));
            $('#no_revisi').val($b.data('norevisi') || '01');
            updatePreview();
            modal.show();
            setTimeout(function() {
                $('#tipe_nama').focus();
            }, 300);
        });

        /* ── Auto uppercase & auto-fill ─────────────────────────────── */
        $('#tipe_kode').on('input', function() {
            this.value = this.value.toUpperCase();
        });
        $('#doc_no').on('input', function() {
            this.value = this.value.toUpperCase();
            updatePreview();
        });
        $('#tipe_nama').on('blur', autoFillDoc);
        $('#no_revisi, #tgl_terbit, #tgl_review').on('change input', updatePreview);

        /* ── Simpan ─────────────────────────────────────────────────── */
        $('#btnSimpan').on('click', function() {
            var nama = $.trim($('#tipe_nama').val());
            if (!nama) {
                toastr.warning('Nama tipe wajib diisi.');
                $('#tipe_nama').focus();
                return;
            }

            var $btn = $(this).prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...');

            var d = {};
            d[CSRF_NAME] = CSRF_HASH;
            d.id_tipe_kendaraan = $('#tipe_id').val();
            d.nama_tipe = nama;
            d.kode_tipe = $.trim($('#tipe_kode').val()).toUpperCase();
            d.doc_no = $.trim($('#doc_no').val()).toUpperCase();
            d.title_id = $.trim($('#title_id').val());
            d.title_en = $.trim($('#title_en').val());
            d.doc_name_id = $.trim($('#doc_name_id').val());
            d.doc_name_en = $.trim($('#doc_name_en').val());
            d.tgl_terbit = $('#tgl_terbit').val();
            d.tgl_review = $('#tgl_review').val();
            d.no_revisi = $.trim($('#no_revisi').val()) || '01';

            $.post('<?= site_url('tipekendaraan/save') ?>', d, function(res) {
                $btn.prop('disabled', false)
                    .html('<i class="bi bi-check-lg me-1"></i>Simpan');
                if (res.status === 'success') {
                    toastr.success(res.message);
                    modal.hide();
                    refreshDt();
                } else {
                    toastr.error(res.message);
                }
            }, 'json').fail(function() {
                $btn.prop('disabled', false)
                    .html('<i class="bi bi-check-lg me-1"></i>Simpan');
                toastr.error('Terjadi kesalahan server.');
            });
        });

        /* Enter di input simpan */
        $('#modalTipe').on('keydown', 'input', function(e) {
            if (e.key === 'Enter') $('#btnSimpan').trigger('click');
        });

        /* ── Toggle aktif ───────────────────────────────────────────── */
        $(document).on('click', '.btn-toggle', function() {
            var d = {};
            d[CSRF_NAME] = CSRF_HASH;
            d.id = $(this).data('id');
            $.post('<?= site_url('tipekendaraan/toggle') ?>', d, function(res) {
                if (res.status === 'success') {
                    toastr.success(res.message);
                    refreshDt();
                } else toastr.error(res.message);
            }, 'json');
        });

        /* ── Hapus ──────────────────────────────────────────────────── */
        $(document).on('click', '.btn-delete', function() {
            var id = $(this).data('id');
            var nama = $(this).data('nama');
            Swal.fire({
                title: 'Hapus Tipe?',
                html: '<strong>' + nama + '</strong><br>' +
                    '<small class="text-muted">Tidak dapat dihapus jika masih dipakai.</small>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
            }).then(function(r) {
                if (!r.isConfirmed) return;
                var d = {};
                d[CSRF_NAME] = CSRF_HASH;
                d.id = id;
                $.post('<?= site_url('tipekendaraan/delete') ?>', d, function(res) {
                    if (res.status === 'success') {
                        toastr.success(res.message);
                        refreshDt();
                    } else toastr.error(res.message);
                }, 'json');
            });
        });
    });
</script>