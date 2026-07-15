<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Form Inspeksi Kelayakan<?= isset($is_inspeksi_ulang) && $is_inspeksi_ulang ? ' <span class="badge bg-warning text-dark ms-2" style="font-size:.7em;">Inspeksi Ulang</span>' : '' ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url('inspeksi') ?>">Form Inspeksi</a></li>
                <li class="breadcrumb-item active">Form Inspeksi</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row justify-content-center">
            <div class="col-xl-10">

                <!-- HEADER KENDARAAN -->
                <div class="card mb-3 border-primary">
                    <div class="card-body py-3">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white"
                                        style="width:50px;height:50px;font-size:1.3rem;">
                                        <i class="bi bi-truck"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0 fw-bold"><?= html_escape($pengajuan->no_polisi) ?></h5>
                                        <small class="text-muted">
                                            <?= html_escape($pengajuan->jenis_kendaraan) ?> —
                                            <?= html_escape($pengajuan->merk) ?> <?= html_escape($pengajuan->tipe) ?>
                                            (<?= $pengajuan->tahun ?>)
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <small class="text-muted d-block">No. Pengajuan</small>
                                        <strong class="text-primary">#PU-<?= str_pad($pengajuan->id_pengajuan, 4, '0', STR_PAD_LEFT) ?></strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Pemohon</small>
                                        <strong><?= html_escape($pengajuan->nama_pemohon) ?></strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Template</small>
                                        <strong><?= html_escape($template->kode) ?> — <?= html_escape($template->nama_tipe) ?></strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Tanggal Inspeksi</small>
                                        <strong><?= date('d M Y') ?></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (isset($is_inspeksi_ulang) && $is_inspeksi_ulang && isset($perbaikan_info) && $perbaikan_info): ?>
                    <!-- INFO INSPEKSI ULANG — konteks dari perbaikan sebelumnya -->
                    <div class="alert alert-warning d-flex gap-3 align-items-start mb-3">
                        <i class="bi bi-arrow-repeat fs-5 flex-shrink-0 mt-1"></i>
                        <div class="small">
                            <strong>Inspeksi Ulang Setelah Perbaikan</strong><br>
                            Deadline perbaikan: <strong><?= date('d M Y', strtotime($perbaikan_info->tgl_max_perbaikan)) ?></strong>
                            <?php if ($perbaikan_info->catatan_perbaikan): ?>
                                <br>Catatan perbaikan: <em><?= html_escape($perbaikan_info->catatan_perbaikan) ?></em>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- PROGRESS BAR -->
                <div class="card mb-3">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="fw-semibold">Progress Pengisian</small>
                            <small class="text-primary fw-bold" id="progressLabel">0 / <?= array_sum(array_map('count', $grouped)) ?> item</small>
                        </div>
                        <div class="progress" style="height:8px;">
                            <div class="progress-bar bg-primary" id="progressBar" style="width:0%;transition:width .3s;"></div>
                        </div>
                    </div>
                </div>

                <!-- FORM UTAMA -->
                <div id="formInspeksi">
                    <input type="hidden" id="hid_id_pengajuan" value="<?= $pengajuan->id_pengajuan ?>">
                    <input type="hidden" id="hid_id_template" value="<?= $template->id_template ?>">

                    <!-- INFO INSPEKTOR & MEKANIK -->
                    <div class="card mb-3 border-warning">
                        <div class="card-header bg-warning text-dark py-2">
                            <h6 class="mb-0 fw-bold">
                                <i class="bi bi-person-badge me-2"></i>Informasi Inspektor &amp; Mekanik Lapangan
                            </h6>
                        </div>
                        <div class="card-body pt-3">
                            <div class="row g-3">
                                <div class="col-12">
                                    <small class="fw-bold text-primary"><i class="bi bi-person-badge me-1"></i>Inspektor Sistem</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nama Inspektor <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama_inspektor"
                                        placeholder="Nama lengkap inspektor"
                                        value="<?= html_escape($existing_inspektor ?? '') ?>" required>
                                    <div class="text-danger small mt-1" id="err_nama_inspektor"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Perusahaan Inspektor <span class="text-danger">*</span></label>
                                    <select class="form-select" id="perusahaan_inspektor" required>
                                        <option value="">-- Pilih Perusahaan --</option>
                                        <?php 
                                        $found = false;
                                        foreach ($daftar_perusahaan as $p) {
                                            if ($existing_perusahaan === $p->nama_perusahaan) {
                                                $found = true;
                                            }
                                        }
                                        ?>
                                        <?php if (!empty($existing_perusahaan) && !$found): ?>
                                            <option value="<?= html_escape($existing_perusahaan) ?>" selected>
                                                <?= html_escape($existing_perusahaan) ?>
                                            </option>
                                        <?php endif; ?>
                                        <?php foreach ($daftar_perusahaan as $p): ?>
                                            <option value="<?= html_escape($p->nama_perusahaan) ?>" <?= ($existing_perusahaan === $p->nama_perusahaan) ? 'selected' : '' ?>>
                                                <?= html_escape($p->nama_perusahaan) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="text-danger small mt-1" id="err_perusahaan_inspektor"></div>
                                </div>
                                <div class="col-12">
                                    <hr class="my-1">
                                    <small class="fw-bold text-warning"><i class="bi bi-tools me-1"></i>Mekanik Lapangan</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nama Mekanik</label>
                                    <input type="text" class="form-control" id="nama_mekanik"
                                        value="<?= html_escape($existing_mekanik ?? '') ?>"
                                        placeholder="Nama mekanik yang hadir">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Perusahaan Mekanik</label>
                                    <?php $current_perus_mekanik = $existing_perus_mekanik ?? ''; ?>
                                    <select class="form-select" id="perusahaan_mekanik">
                                        <option value="">-- Pilih Perusahaan --</option>
                                        <?php 
                                        $found_mek = false;
                                        foreach ($daftar_perusahaan as $p) {
                                            if ($current_perus_mekanik === $p->nama_perusahaan) {
                                                $found_mek = true;
                                            }
                                        }
                                        ?>
                                        <?php if (!empty($current_perus_mekanik) && !$found_mek): ?>
                                            <option value="<?= html_escape($current_perus_mekanik) ?>" selected>
                                                <?= html_escape($current_perus_mekanik) ?>
                                            </option>
                                        <?php endif; ?>
                                        <?php foreach ($daftar_perusahaan as $p): ?>
                                            <option value="<?= html_escape($p->nama_perusahaan) ?>" <?= ($current_perus_mekanik === $p->nama_perusahaan) ? 'selected' : '' ?>>
                                                <?= html_escape($p->nama_perusahaan) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CRITICAL ITEMS -->
                    <?php if (!empty($grouped['CRITICAL'])): ?>
                        <div class="card mb-3">
                            <div class="card-body pt-4">
                                <h6 class="card-title d-flex align-items-center gap-2 mb-3">
                                    <span class="badge bg-danger text-white rounded-pill px-3 py-1">
                                        <i class="bi bi-exclamation-triangle me-1"></i>CRITICAL ITEMS
                                    </span>
                                    <span class="text-muted small fw-normal">— Semua item WAJIB YES untuk lulus</span>
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover align-middle mb-0">
                                        <thead class="table-danger">
                                            <tr>
                                                <th style="width:50px;" class="text-center">No.</th>
                                                <th>Kriteria / Criteria</th>
                                                <th style="width:90px;" class="text-center">YES</th>
                                                <th style="width:90px;" class="text-center">NO</th>
                                                <th style="width:90px;" class="text-center">N/A</th>
                                                <th style="width:200px;">Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($grouped['CRITICAL'] as $item): ?>
                                                <?php $ev = $existing[$item->id_item] ?? null; ?>
                                                <tr class="checklist-row" data-id="<?= $item->id_item ?>" data-kategori="CRITICAL" id="row_<?= $item->id_item ?>">
                                                    <td class="text-center fw-bold text-danger"><?= html_escape($item->no_urut) ?></td>
                                                    <td><?= html_escape($item->kriteria) ?></td>
                                                    <td class="text-center">
                                                        <div class="form-check d-flex justify-content-center">
                                                            <input class="form-check-input check-radio" type="radio"
                                                                name="items[<?= $item->id_item ?>][hasil]" value="yes"
                                                                <?= ($ev && $ev['hasil'] === 'yes') ? 'checked' : '' ?>>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="form-check d-flex justify-content-center">
                                                            <input class="form-check-input check-radio" type="radio"
                                                                name="items[<?= $item->id_item ?>][hasil]" value="no"
                                                                <?= ($ev && $ev['hasil'] === 'no') ? 'checked' : '' ?>>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="form-check d-flex justify-content-center">
                                                            <input class="form-check-input check-radio" type="radio"
                                                                name="items[<?= $item->id_item ?>][hasil]" value="na"
                                                                <?= ($ev && $ev['hasil'] === 'na') ? 'checked' : '' ?>>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="items[<?= $item->id_item ?>][keterangan]"
                                                            value="<?= html_escape($ev ? $ev['keterangan'] : '') ?>"
                                                            placeholder="Catatan...">
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- GENERAL ITEMS -->
                    <?php if (!empty($grouped['GENERAL'])): ?>
                        <div class="card mb-3">
                            <div class="card-body pt-4">
                                <h6 class="card-title d-flex align-items-center gap-2 mb-3">
                                    <span class="badge bg-primary text-white rounded-pill px-3 py-1">
                                        <i class="bi bi-list-check me-1"></i>GENERAL REQUIREMENTS
                                    </span>
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover align-middle mb-0">
                                        <thead class="table-primary">
                                            <tr>
                                                <th style="width:50px;" class="text-center">No.</th>
                                                <th>Kriteria / Criteria</th>
                                                <th style="width:90px;" class="text-center">YES</th>
                                                <th style="width:90px;" class="text-center">NO</th>
                                                <th style="width:90px;" class="text-center">N/A</th>
                                                <th style="width:200px;">Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($grouped['GENERAL'] as $item): ?>
                                                <?php $ev = $existing[$item->id_item] ?? null; ?>
                                                <tr class="checklist-row" data-id="<?= $item->id_item ?>" data-kategori="GENERAL" id="row_<?= $item->id_item ?>">
                                                    <td class="text-center fw-bold text-primary"><?= html_escape($item->no_urut) ?></td>
                                                    <td><?= html_escape($item->kriteria) ?></td>
                                                    <td class="text-center">
                                                        <div class="form-check d-flex justify-content-center">
                                                            <input class="form-check-input check-radio" type="radio"
                                                                name="items[<?= $item->id_item ?>][hasil]" value="yes"
                                                                <?= ($ev && $ev['hasil'] === 'yes') ? 'checked' : '' ?>>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="form-check d-flex justify-content-center">
                                                            <input class="form-check-input check-radio" type="radio"
                                                                name="items[<?= $item->id_item ?>][hasil]" value="no"
                                                                <?= ($ev && $ev['hasil'] === 'no') ? 'checked' : '' ?>>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="form-check d-flex justify-content-center">
                                                            <input class="form-check-input check-radio" type="radio"
                                                                name="items[<?= $item->id_item ?>][hasil]" value="na"
                                                                <?= ($ev && $ev['hasil'] === 'na') ? 'checked' : '' ?>>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="items[<?= $item->id_item ?>][keterangan]"
                                                            value="<?= html_escape($ev ? $ev['keterangan'] : '') ?>"
                                                            placeholder="Catatan...">
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- CATATAN & ESTIMASI + TGL MAKS PERBAIKAN -->
                    <div class="card mb-3">
                        <div class="card-body pt-4">
                            <h6 class="card-title mb-3">Catatan Umum &amp; Estimasi Hasil</h6>
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">Temuan / Catatan Umum</label>
                                    <textarea class="form-control" id="catatan_temuan" rows="3"
                                        placeholder="Tuliskan temuan, catatan, atau hal yang perlu diperhatikan..."
                                        maxlength="1000"></textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Estimasi Hasil</label>
                                    <div id="estimasiHasil" class="rounded border p-3 text-center" style="min-height:90px;">
                                        <div class="text-muted small">Isi semua item untuk melihat estimasi</div>
                                    </div>
                                </div>

                                <!-- ══ FIELD TANGGAL MAKS PERBAIKAN — muncul hanya saat ada item NO ══ -->
                                <div class="col-12" id="wrapTglMaks" style="display:none;">
                                    <div class="border border-danger rounded p-3 bg-danger bg-opacity-10">
                                        <div class="d-flex align-items-start gap-3">
                                            <i class="bi bi-calendar-x-fill text-danger flex-shrink-0 fs-4 mt-1"></i>
                                            <div class="flex-grow-1">
                                                <label class="form-label fw-bold text-danger mb-1">
                                                    Tanggal Maksimum Perbaikan
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <div class="row g-2 align-items-end">
                                                    <div class="col-md-4">
                                                        <input type="date" class="form-control border-danger"
                                                            id="tgl_maks_perbaikan"
                                                            min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                                                            placeholder="yyyy-mm-dd">
                                                        <div class="text-danger small mt-1" id="err_tgl_maks"></div>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <small class="text-danger">
                                                            <i class="bi bi-info-circle me-1"></i>
                                                            Wajib diisi karena terdapat item yang <strong>TIDAK LULUS</strong>.
                                                            Inspektor menetapkan deadline perbaikan untuk Admin Departemen.
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FOTO DOKUMENTASI -->
                    <div class="card mb-3">
                        <div class="card-header bg-info text-white py-2 d-flex align-items-center gap-2">
                            <i class="bi bi-camera-fill"></i>
                            <h6 class="mb-0 fw-bold">Foto Dokumentasi Inspeksi</h6>
                        </div>
                        <div class="card-body pt-3">
                            <div class="row g-4">
                                <!-- Foto Mekanik -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-person-badge me-1 text-warning"></i>
                                        Foto Mekanik / Peserta Commissioning
                                        <span class="badge bg-secondary text-white ms-1" style="font-size:9px;">Opsional</span>
                                    </label>
                                    <div class="foto-upload-box border rounded p-3 text-center" id="box_foto_mekanik">
                                        <input type="file" class="d-none" id="inp_foto_mekanik"
                                            name="foto_mekanik" accept=".jpg,.jpeg,.png">
                                        <div class="foto-default" id="default_foto_mekanik">
                                            <i class="bi bi-person-badge-fill text-warning d-block mb-2" style="font-size:2.5rem;"></i>
                                            <div class="text-muted small mb-2">Klik untuk upload foto mekanik</div>
                                            <button type="button" class="btn btn-sm btn-outline-warning btn-trigger-foto"
                                                data-input="inp_foto_mekanik">
                                                <i class="bi bi-upload me-1"></i>Pilih Foto
                                            </button>
                                        </div>
                                        <div class="foto-preview d-none" id="preview_foto_mekanik">
                                            <div class="position-relative d-inline-block">
                                                <img id="img_foto_mekanik" src="" alt="Foto Mekanik"
                                                    class="img-fluid rounded border mb-1"
                                                    style="max-height:160px;max-width:100%;object-fit:cover;">
                                                <button type="button" class="btn btn-danger rounded-circle p-0 btn-hapus-foto"
                                                    data-input="inp_foto_mekanik"
                                                    data-default="default_foto_mekanik"
                                                    data-preview="preview_foto_mekanik"
                                                    style="width:22px;height:22px;font-size:11px;position:absolute;top:-8px;right:-8px;">
                                                    <i class="bi bi-x"></i>
                                                </button>
                                            </div>
                                            <div class="small text-success mt-1">
                                                <i class="bi bi-check-circle me-1"></i>
                                                <span id="fname_foto_mekanik"></span>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-secondary mt-1 btn-trigger-foto"
                                                data-input="inp_foto_mekanik" style="font-size:11px;">
                                                <i class="bi bi-arrow-repeat me-1"></i>Ganti Foto
                                            </button>
                                        </div>
                                    </div>
                                    <input type="text" class="form-control form-control-sm mt-2"
                                        id="ket_foto_mekanik" name="ket_foto_mekanik"
                                        placeholder="Keterangan foto mekanik (opsional)" maxlength="200">
                                </div>

                                <!-- Foto Temuan (multiple, maks 10) -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-search me-1 text-danger"></i>
                                        Foto Temuan / Hasil Inspeksi
                                        <span class="badge bg-secondary text-white ms-1" style="font-size:9px;">Opsional · Maks 10 foto</span>
                                    </label>
                                    <small class="text-muted d-block mb-2">Upload foto kondisi unit, temuan masalah, atau dokumentasi pemeriksaan.</small>
                                    <div class="mb-2">
                                        <input type="file" class="d-none" id="inp_foto_temuan" accept=".jpg,.jpeg,.png" multiple>
                                        <button type="button" class="btn btn-sm btn-outline-danger" id="btn_tambah_temuan">
                                            <i class="bi bi-camera-fill me-1"></i>Pilih Foto Temuan
                                        </button>
                                        <span class="text-muted small ms-2">(<span id="count_foto_temuan">0</span>/10 foto)</span>
                                    </div>
                                    <div class="row g-2" id="grid_foto_temuan"></div>
                                    <div id="hidden_foto_temuan"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ALUR INFO -->
                    <div class="alert alert-info d-flex gap-3 align-items-start mb-3">
                        <i class="bi bi-info-circle-fill fs-5 flex-shrink-0 mt-1 text-info"></i>
                        <div class="small">
                            <strong>Alur setelah submit:</strong><br>
                            <span class="text-success"><i class="bi bi-check-circle-fill me-1"></i><strong>LULUS</strong></span>
                            → Langsung ke <strong>OHS Superintendent</strong>.<br>
                            <span class="text-danger"><i class="bi bi-x-circle-fill me-1"></i><strong>TIDAK LULUS</strong></span>
                            → Isi tanggal deadline perbaikan → Admin Departemen input bukti perbaikan → Inspektor verifikasi langsung (tanpa jadwal ulang).
                        </div>
                    </div>

                    <!-- SUBMIT -->
                    <div class="card mb-4">
                        <div class="card-body py-3">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div class="text-muted small">
                                    <i class="bi bi-info-circle me-1 text-primary"></i>
                                    Login: <strong><?= html_escape($user['nama']) ?></strong> —
                                    Tanggal: <strong><?= date('d M Y') ?></strong>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="<?= site_url('inspeksi') ?>" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left me-1"></i>Kembali
                                    </a>
                                    <button type="button" class="btn btn-warning" id="btnSimpanDraft">
                                        <i class="bi bi-floppy me-1"></i>Simpan Draft
                                    </button>
                                    <button type="button" class="btn btn-primary" id="btnSubmit">
                                        <i class="bi bi-send-check me-1"></i>Submit Hasil Inspeksi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div><!-- end formInspeksi -->
            </div>
        </div>
    </section>
</main>


<style>
    .checklist-row.answered-yes {
        background-color: #f0fff5 !important;
    }

    .checklist-row.answered-no {
        background-color: #fff5f5 !important;
    }

    .checklist-row.answered-na {
        background-color: #f8f9fa !important;
    }

    .checklist-row.unanswered {
        background-color: #fffbe6 !important;
    }

    .form-check-input[value="yes"]:checked {
        background-color: #2eca6a;
        border-color: #2eca6a;
    }

    .form-check-input[value="no"]:checked {
        background-color: #dc3545;
        border-color: #dc3545;
    }

    .form-check-input[value="na"]:checked {
        background-color: #6c757d;
        border-color: #6c757d;
    }

    #wrapTglMaks {
        transition: all .3s;
    }
</style>


<script>
    $(function() {

        $('#perusahaan_inspektor').select2({
            placeholder: '-- Pilih Perusahaan --',
            allowClear: true,
            width: '100%',
            tags: true
        });

        $('#perusahaan_mekanik').select2({
            placeholder: '-- Pilih Perusahaan --',
            allowClear: true,
            width: '100%',
            tags: true
        });

        var totalItems = <?= array_sum(array_map('count', $grouped)) ?>;
        var criticalIds = [<?= implode(',', array_map(fn($i) => $i->id_item, $grouped['CRITICAL'])) ?>];

        // ── Row highlight + progress ─────────────────────────────────────────
        $(document).on('change', '.check-radio', function() {
            var row = $(this).closest('tr');
            row.removeClass('answered-yes answered-no answered-na unanswered');
            row.addClass('answered-' + $(this).val());
            updateProgress();
            updateEstimasi();
            toggleTglMaks(); // ← tampilkan/sembunyikan field tgl maks perbaikan
        });

        // Init dari existing
        $('.checklist-row').each(function() {
            var checked = $(this).find('.check-radio:checked');
            if (checked.length) $(this).addClass('answered-' + checked.val());
        });
        updateProgress();
        updateEstimasi();
        toggleTglMaks();

        function updateProgress() {
            var answered = $('.checklist-row').filter(function() {
                return $(this).find('.check-radio:checked').length > 0;
            }).length;
            var pct = totalItems > 0 ? Math.round(answered / totalItems * 100) : 0;
            $('#progressBar').css('width', pct + '%');
            $('#progressLabel').text(answered + ' / ' + totalItems + ' item');
        }

        function countNO() {
            var criticalNo = 0,
                generalNo = 0;
            $('.checklist-row').each(function() {
                var checked = $(this).find('.check-radio:checked');
                if (!checked.length || checked.val() !== 'no') return;
                var id = parseInt($(this).data('id'));
                if (criticalIds.indexOf(id) >= 0) criticalNo++;
                else generalNo++;
            });
            return {
                criticalNo: criticalNo,
                generalNo: generalNo,
                totalNo: criticalNo + generalNo
            };
        }

        // ── Tampilkan/sembunyikan field tgl_maks_perbaikan ────────────────────
        // Muncul ketika ada setidaknya 1 item yang dijawab NO
        function toggleTglMaks() {
            var c = countNO();
            if (c.totalNo > 0) {
                $('#wrapTglMaks').slideDown(200);
                $('#tgl_maks_perbaikan').prop('required', true);
            } else {
                $('#wrapTglMaks').slideUp(200);
                $('#tgl_maks_perbaikan').prop('required', false).val('');
                $('#err_tgl_maks').text('');
            }
        }

        function updateEstimasi() {
            var answered = $('.checklist-row').filter(function() {
                return $(this).find('.check-radio:checked').length > 0;
            }).length;
            var box = $('#estimasiHasil').html('');
            if (answered < totalItems) {
                box.html('<div class="text-muted small"><i class="bi bi-hourglass me-1"></i>' + (totalItems - answered) + ' item belum diisi</div>');
                return;
            }
            var c = countNO();
            if (c.totalNo === 0) {
                box.html('<div class="text-success"><i class="bi bi-patch-check-fill fs-2 d-block mb-1"></i>' +
                    '<strong>LULUS</strong><br><small class="text-muted">→ OHS Superintendent</small></div>');
            } else {
                var detail = [];
                if (c.criticalNo > 0) detail.push('<strong>' + c.criticalNo + ' CRITICAL</strong> NO');
                if (c.generalNo > 0) detail.push('<strong>' + c.generalNo + ' GENERAL</strong> NO');
                box.html('<div class="text-danger"><i class="bi bi-x-circle-fill fs-2 d-block mb-1"></i>' +
                    '<strong>TIDAK LULUS</strong><br>' +
                    '<small class="text-muted">' + detail.join(' + ') + '<br>Isi deadline perbaikan ↓</small></div>');
            }
        }

        // ── Foto mekanik ────────────────────────────────────────────────────
        $(document).on('click', '.btn-trigger-foto', function() {
            document.getElementById($(this).data('input')).click();
        });
        $(document).on('change', '#inp_foto_mekanik', function() {
            var file = this.files[0];
            if (!file) return;
            if (file.size > 5 * 1024 * 1024) {
                toastr.warning('Foto mekanik maks 5MB.');
                return;
            }
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#img_foto_mekanik').attr('src', e.target.result);
                var fname = file.name.length > 25 ? file.name.substring(0, 23) + '…' : file.name;
                $('#fname_foto_mekanik').text(fname);
                $('#default_foto_mekanik').addClass('d-none');
                $('#preview_foto_mekanik').removeClass('d-none');
            };
            reader.readAsDataURL(file);
        });
        $(document).on('click', '.btn-hapus-foto', function() {
            var el = document.getElementById($(this).data('input'));
            var neu = el.cloneNode(true);
            el.parentNode.replaceChild(neu, el);
            $('#' + $(this).data('default')).removeClass('d-none');
            $('#' + $(this).data('preview')).addClass('d-none');
            $('#img_foto_mekanik').attr('src', '');
        });

        // ── Foto temuan (multiple maks 10) ───────────────────────────────────
        var fotoTemuan = [];
        $('#btn_tambah_temuan').on('click', function() {
            $('#inp_foto_temuan').trigger('click');
        });
        $(document).on('change', '#inp_foto_temuan', function() {
            Array.from(this.files).forEach(function(file) {
                if (fotoTemuan.length >= 10) {
                    toastr.warning('Maks 10 foto temuan.');
                    return;
                }
                if (file.size > 5 * 1024 * 1024) {
                    toastr.warning(file.name + ': maks 5MB, dilewati.');
                    return;
                }
                fotoTemuan.push(file);
            });
            renderFotoTemuan();
            var el = document.getElementById('inp_foto_temuan');
            var neu = el.cloneNode(true);
            el.parentNode.replaceChild(neu, el);
        });

        function renderFotoTemuan() {
            var $grid = $('#grid_foto_temuan').empty();
            $('#count_foto_temuan').text(fotoTemuan.length);
            fotoTemuan.forEach(function(file, idx) {
                var $col = $('<div class="col-6 col-sm-4"></div>');
                var $box = $('<div class="border rounded p-1 text-center position-relative"></div>');
                var reader = new FileReader();
                reader.onload = function(e) {
                    $box.prepend('<img src="' + e.target.result + '" class="img-fluid rounded mb-1" style="height:80px;width:100%;object-fit:cover;">');
                };
                reader.readAsDataURL(file);
                var fname = file.name.length > 14 ? file.name.substring(0, 12) + '…' : file.name;
                $box.append('<div class="text-muted" style="font-size:9px;">' + fname + '</div>');
                $box.append('<button type="button" class="btn btn-danger rounded-circle p-0 btn-hapus-temuan position-absolute" data-idx="' + idx + '" style="width:18px;height:18px;font-size:9px;top:2px;right:2px;"><i class="bi bi-x"></i></button>');
                $col.append($box);
                $grid.append($col);
            });
            var $hidden = $('#hidden_foto_temuan').empty();
            if (fotoTemuan.length > 0) {
                $hidden.append('<div class="mt-2"><small class="fw-semibold text-muted">Keterangan per foto (opsional):</small></div>');
                fotoTemuan.forEach(function(file, idx) {
                    var fname = file.name.length > 20 ? file.name.substring(0, 18) + '…' : file.name;
                    $hidden.append('<div class="d-flex align-items-center gap-2 mt-1"><small class="text-muted flex-shrink-0" style="width:90px;font-size:10px;">' + fname + '</small><input type="text" class="form-control form-control-sm inp-ket-temuan" data-idx="' + idx + '" placeholder="Keterangan..." maxlength="200"></div>');
                });
            }
        }
        $(document).on('click', '.btn-hapus-temuan', function() {
            fotoTemuan.splice(parseInt($(this).data('idx')), 1);
            renderFotoTemuan();
        });

        // ── Submit ──────────────────────────────────────────────────────────
        $('#btnSubmit').on('click', function() {
            // Validasi inspektor
            var namaIns = $('#nama_inspektor').val().trim();
            var perusIns = ($('#perusahaan_inspektor').val() || '').trim();
            var errFocus = null;
            $('#err_nama_inspektor, #err_perusahaan_inspektor, #err_tgl_maks').text('');

            if (!namaIns) {
                $('#err_nama_inspektor').text('Nama inspektor wajib diisi.');
                errFocus = errFocus || '#nama_inspektor';
            }
            if (!perusIns) {
                $('#err_perusahaan_inspektor').text('Perusahaan wajib diisi.');
                errFocus = errFocus || '#perusahaan_inspektor';
            }

            // Validasi tgl_maks_perbaikan jika ada item NO
            var c = countNO();
            if (c.totalNo > 0) {
                var tglMaks = $('#tgl_maks_perbaikan').val();
                if (!tglMaks) {
                    $('#err_tgl_maks').text('Tanggal maksimum perbaikan wajib diisi.');
                    errFocus = errFocus || '#tgl_maks_perbaikan';
                }
            }

            if (errFocus) {
                $('html,body').animate({
                    scrollTop: $(errFocus).offset().top - 120
                }, 300);
                return;
            }

            // Validasi semua item dijawab
            var unanswered = [];
            $('.checklist-row').each(function() {
                if (!$(this).find('.check-radio:checked').length) {
                    unanswered.push($(this).find('td:first').text().trim());
                    $(this).addClass('unanswered');
                } else {
                    $(this).removeClass('unanswered');
                }
            });
            if (unanswered.length) {
                Swal.fire({
                    title: 'Belum Lengkap',
                    html: 'Item <strong>no. ' + unanswered.join(', ') + '</strong> belum dijawab.',
                    icon: 'warning',
                    confirmButtonColor: '#4154f1'
                });
                $('html,body').animate({
                    scrollTop: $('.checklist-row.unanswered').first().offset().top - 120
                }, 400);
                return;
            }

            // Preview konfirmasi
            var hasilText;
            if (c.totalNo === 0) {
                hasilText = '<span class="text-success fw-bold">LULUS</span> → diteruskan ke <strong>OHS Superintendent</strong>';
            } else {
                var detail = [];
                if (c.criticalNo > 0) detail.push(c.criticalNo + ' CRITICAL NO');
                if (c.generalNo > 0) detail.push(c.generalNo + ' GENERAL NO');
                var tglFmt = $('#tgl_maks_perbaikan').val() ?
                    new Date($('#tgl_maks_perbaikan').val()).toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    }) :
                    '';
                hasilText = '<span class="text-danger fw-bold">TIDAK LULUS</span> (' + detail.join(', ') + ')' +
                    (tglFmt ? '<br>Deadline perbaikan: <strong>' + tglFmt + '</strong>' : '');
            }

            Swal.fire({
                title: 'Submit Hasil Inspeksi?',
                html: 'Estimasi: ' + hasilText + '<br><small class="text-muted">Data tidak dapat diubah setelah disubmit.</small>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4154f1',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-send-check me-1"></i>Ya, Submit',
                cancelButtonText: 'Periksa Kembali',
            }).then(function(r) {
                if (r.isConfirmed) doSubmit();
            });
        });

        function doSubmit() {
            NProgress.start();
            var $btn = $('#btnSubmit');
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');

            var fd = new FormData();
            fd.append('<?= $this->security->get_csrf_token_name() ?>', '<?= $this->security->get_csrf_hash() ?>');
            fd.append('id_pengajuan', $('#hid_id_pengajuan').val());
            fd.append('id_template', $('#hid_id_template').val());
            fd.append('catatan_temuan', $('#catatan_temuan').val());
            fd.append('nama_inspektor', $('#nama_inspektor').val().trim());
            fd.append('perusahaan_inspektor', ($('#perusahaan_inspektor').val() || '').trim());
            fd.append('nama_mekanik', $('#nama_mekanik').val().trim());
            fd.append('perusahaan_mekanik', ($('#perusahaan_mekanik').val() || '').trim());
            fd.append('tgl_maks_perbaikan', $('#tgl_maks_perbaikan').val());

            // Checklist items
            $('.check-radio:checked').each(function() {
                fd.append($(this).attr('name'), $(this).val());
            });
            $('input[name^="items["]').filter('[name$="[keterangan]"]').each(function() {
                fd.append($(this).attr('name'), $(this).val());
            });

            // Foto mekanik
            var elFotoMek = document.getElementById('inp_foto_mekanik');
            if (elFotoMek && elFotoMek.files[0]) {
                fd.append('foto_mekanik', elFotoMek.files[0]);
                fd.append('ket_foto_mekanik', $('#ket_foto_mekanik').val().trim());
            }
            // Foto temuan
            fotoTemuan.forEach(function(file, idx) {
                fd.append('foto_temuan[]', file);
                fd.append('ket_foto_temuan[]', $('.inp-ket-temuan[data-idx="' + idx + '"]').val() || '');
            });

            $.ajax({
                url: '<?= site_url('checklist/submit') ?>',
                type: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(res) {
                    NProgress.done();
                    $btn.prop('disabled', false).html('<i class="bi bi-send-check me-1"></i>Submit Hasil Inspeksi');
                    if (res.status === 'success') {
                        Swal.fire({
                            title: res.hasil === 'lulus' ? '✅ Lulus Inspeksi!' : '❌ Tidak Lulus',
                            html: res.message,
                            icon: res.hasil === 'lulus' ? 'success' : 'error',
                            confirmButtonColor: '#4154f1',
                            allowOutsideClick: false,
                        }).then(function() {
                            window.location.href = res.redirect;
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            html: res.message,
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                },
                error: function() {
                    NProgress.done();
                    $btn.prop('disabled', false).html('<i class="bi bi-send-check me-1"></i>Submit Hasil Inspeksi');
                    toastr.error('Terjadi kesalahan server.');
                }
            });
        }

        // ── Draft (localStorage) ─────────────────────────────────────────────
        $('#btnSimpanDraft').on('click', function() {
            var answers = {};
            $('.check-radio:checked').each(function() {
                answers[$(this).attr('name')] = $(this).val();
            });
            answers['_nama_inspektor'] = $('#nama_inspektor').val();
            answers['_perusahaan_inspektor'] = $('#perusahaan_inspektor').val() || '';
            answers['_nama_mekanik'] = $('#nama_mekanik').val();
            answers['_perusahaan_mekanik'] = $('#perusahaan_mekanik').val() || '';
            answers['_catatan_temuan'] = $('#catatan_temuan').val();
            answers['_tgl_maks_perbaikan'] = $('#tgl_maks_perbaikan').val();
            localStorage.setItem('draft_checklist_<?= $pengajuan->id_pengajuan ?>', JSON.stringify(answers));
            toastr.success('Draft tersimpan di browser.');
        });

        <?php if (empty($existing)): ?>
            var draft = localStorage.getItem('draft_checklist_<?= $pengajuan->id_pengajuan ?>');
            if (draft) {
                try {
                    var saved = JSON.parse(draft);
                    $.each(saved, function(name, val) {
                        if (name === '_nama_inspektor') {
                            $('#nama_inspektor').val(val);
                            return;
                        }
                        if (name === '_perusahaan_inspektor') {
                            $('#perusahaan_inspektor').val(val).trigger('change');
                            return;
                        }
                        if (name === '_nama_mekanik') {
                            $('#nama_mekanik').val(val);
                            return;
                        }
                        if (name === '_perusahaan_mekanik') {
                            $('#perusahaan_mekanik').val(val).trigger('change');
                            return;
                        }
                        if (name === '_catatan_temuan') {
                            $('#catatan_temuan').val(val);
                            return;
                        }
                        if (name === '_tgl_maks_perbaikan') {
                            $('#tgl_maks_perbaikan').val(val);
                            toggleTglMaks();
                            return;
                        }
                        if (['yes', 'no', 'na'].indexOf(val) >= 0) {
                            $('input[name="' + name + '"][value="' + val + '"]').prop('checked', true).trigger('change');
                        }
                    });
                    toastr.info('Draft sebelumnya dimuat.');
                } catch (e) {}
            }
        <?php endif; ?>

    });
</script>