<main id="main" class="main">

    <div class="pagetitle">
        <h1>Buat Pengajuan Uji Kelayakan</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url('pengajuan') ?>">Daftar Pengajuan</a></li>
                <li class="breadcrumb-item active">Buat Pengajuan</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row justify-content-center">
            <div class="col-xl-10">

                <!-- ══════════════════════════════════════════════════
             TABS — Mining Access vs Non Mining
             tipe_akses di-set otomatis dari tab yang aktif
        ══════════════════════════════════════════════════ -->
                <ul class="nav nav-tabs nav-tabs-akses mb-0" id="tabsAkses" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active px-4 py-3" id="tab-mining-btn"
                            data-bs-toggle="tab" data-bs-target="#tab-mining"
                            type="button" role="tab" data-akses="mining">
                            <i class="bi bi-minecart-loaded me-2"></i>
                            <span class="fw-bold">Mining Access</span>
                            <small class="d-block text-muted fw-normal" style="font-size:11px;">Area Tambang</small>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-4 py-3" id="tab-nonmining-btn"
                            data-bs-toggle="tab" data-bs-target="#tab-nonmining"
                            type="button" role="tab" data-akses="non_mining">
                            <i class="bi bi-building me-2"></i>
                            <span class="fw-bold">Non Mining</span>
                            <small class="d-block text-muted fw-normal" style="font-size:11px;">Area Non-Tambang</small>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-4 py-3" id="tab-underground-btn"
                            data-bs-toggle="tab" data-bs-target="#tab-underground"
                            type="button" role="tab" data-akses="underground">
                            <i class="bi bi-arrow-down-circle me-2"></i>
                            <span class="fw-bold">Underground</span>
                            <small class="d-block text-muted fw-normal" style="font-size:11px;">Area Bawah Tanah</small>
                        </button>
                    </li>
                </ul>

                <!-- ══════════════════════════════════════════════════
             TAB CONTENT
        ══════════════════════════════════════════════════ -->
                <div class="tab-content tab-content-akses">

                    <?php
                    // Render form 3x — satu per tab: mining (m), non_mining (nm), underground (ug)
                    $tabs = [
                        'mining'      => ['id' => 'tab-mining',      'akses' => 'mining',      'label' => 'Mining Access',  'active' => 'show active'],
                        'non_mining'  => ['id' => 'tab-nonmining',   'akses' => 'non_mining',  'label' => 'Non Mining',     'active' => ''],
                        'underground' => ['id' => 'tab-underground', 'akses' => 'underground', 'label' => 'Underground',    'active' => ''],
                    ];
                    $tab_suffix_map = ['mining' => 'm', 'non_mining' => 'nm', 'underground' => 'ug'];
                    foreach ($tabs as $tabKey => $tab):
                        $s = $tab_suffix_map[$tabKey]; // suffix untuk id field
                    ?>

                        <div class="tab-pane fade <?= $tab['active'] ?>" id="<?= $tab['id'] ?>" role="tabpanel">

                            <div id="formPengajuan_<?= $s ?>">
                                <!-- Hidden: tipe_akses sudah dikunci per tab -->
                                <input type="hidden" class="inp-tipe-akses" value="<?= $tab['akses'] ?>">

                                <!-- ══════════════════════════════════
                   SECTION 1 — Tipe Commissioning
              ══════════════════════════════════ -->
                                <div class="card mb-3 border-top-0 rounded-top-0">
                                    <div class="card-body pt-4">
                                        <h6 class="card-title d-flex align-items-center gap-2 mb-3">
                                            <span class="step-badge">1</span>
                                            Tipe Pengajuan / <em class="fw-normal text-muted">Commissioning Type</em>
                                            <?php
                                            $tab_badge_class = ['mining' => 'bg-danger', 'non_mining' => 'bg-secondary', 'underground' => 'bg-dark'];
                                            $tab_icon        = ['mining' => 'minecart-loaded', 'non_mining' => 'building', 'underground' => 'arrow-down-circle'];
                                            ?>
                                            <span class="badge ms-auto <?= $tab_badge_class[$tabKey] ?? 'bg-secondary' ?> px-3 py-1 text-white" style="font-size:11px;">
                                                <i class="bi bi-<?= $tab_icon[$tabKey] ?? 'circle' ?> me-1"></i>
                                                <?= $tab['label'] ?>
                                            </span>
                                        </h6>

                                        <label class="form-label fw-semibold">Tipe Commissioning <span class="text-danger">*</span></label>
                                        <div class="d-flex gap-3 mt-1">

                                            <label class="tipe-card flex-fill p-3 border rounded" for="tipeNew_<?= $s ?>" style="cursor:pointer;">
                                                <input type="radio" class="d-none tipe-pengajuan-radio" name="tipe_pengajuan_<?= $s ?>"
                                                    id="tipeNew_<?= $s ?>" value="new_commissioning">
                                                <div class="fw-bold text-primary mb-1">
                                                    <i class="bi bi-plus-circle me-1"></i>Pengajuan Kelayakan
                                                </div>
                                                <small class="text-muted d-block">New Commissioning</small>
                                                <small class="text-success d-block mt-1">
                                                    <i class="bi bi-star-fill me-1"></i>Unit / kendaraan baru
                                                </small>
                                            </label>

                                            <label class="tipe-card flex-fill p-3 border rounded" for="tipeRecomm_<?= $s ?>" style="cursor:pointer;">
                                                <input type="radio" class="d-none tipe-pengajuan-radio" name="tipe_pengajuan_<?= $s ?>"
                                                    id="tipeRecomm_<?= $s ?>" value="recommissioning">
                                                <div class="fw-bold text-primary mb-1">
                                                    <i class="bi bi-arrow-repeat me-1"></i>Pengajuan Kembali
                                                </div>
                                                <small class="text-muted d-block">Recommissioning</small>
                                                <small class="text-info d-block mt-1">
                                                    <i class="bi bi-clock-history me-1"></i>Unit sudah pernah lulus (berkala 6 bulan)
                                                </small>
                                            </label>

                                        </div>
                                        <div class="text-danger small mt-1 err-tipe-pengajuan"></div>
                                    </div>
                                </div>


                                <!-- ══════════════════════════════════
                   SECTION 2 — Detail Unit
              ══════════════════════════════════ -->
                                <div class="card mb-3 section-detail-unit" style="display:none;">
                                    <div class="card-body pt-4">
                                        <h6 class="card-title d-flex align-items-center gap-2 mb-3">
                                            <span class="step-badge">2</span>
                                            Detail Unit / <em class="fw-normal text-muted">Unit Details</em>
                                            <span class="badge-mode-unit ms-1"></span>
                                        </h6>

                                        <!-- ── Unit Baru ── -->
                                        <div class="section-unit-baru" style="display:none;">
                                            <div class="row g-3">

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Tipe Unit / <em class="fw-normal">Unit Type</em> <span class="text-danger">*</span></label>
                                                    <select class="form-select select2-jenis" name="jenis_kendaraan" id="jenis_kendaraan_<?= $s ?>">
                                                        <option value="">— Pilih Tipe Unit —</option>
                                                        <?php foreach ($tipe_kendaraan as $tk): ?>
                                                            <option value="<?= $tk->id_tipe_kendaraan ?>"><?= html_escape($tk->nama_tipe) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <div class="text-danger small mt-1 err-jenis"></div>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Nomor Unit / <em class="fw-normal">Unit No.</em></label>
                                                    <div class="d-flex gap-2 align-items-start">
                                                        <div class="flex-grow-1">
                                                            <input type="text" class="form-control inp-nomor-unit" placeholder="misal: DT-001">
                                                            <div class="text-danger small mt-1 err-nomor-unit"></div>
                                                        </div>
                                                        <div class="form-check mt-2 flex-shrink-0">
                                                            <input class="form-check-input inp-na-check" type="checkbox"
                                                                id="na_nomor_unit_<?= $s ?>" data-target="nomor_unit" data-s="<?= $s ?>">
                                                            <label class="form-check-label small text-muted" for="na_nomor_unit_<?= $s ?>">N/A</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Merk Unit / <em class="fw-normal">Unit Brand</em> <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control inp-merk" placeholder="misal: Volvo, Komatsu">
                                                    <div class="text-danger small mt-1 err-merk"></div>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Model Unit / <em class="fw-normal">Unit Model</em></label>
                                                    <div class="d-flex gap-2 align-items-start">
                                                        <div class="flex-grow-1">
                                                            <input type="text" class="form-control inp-model-unit" placeholder="misal: D375A-6">
                                                            <div class="text-danger small mt-1 err-model-unit"></div>
                                                        </div>
                                                        <div class="form-check mt-2 flex-shrink-0">
                                                            <input class="form-check-input inp-na-check" type="checkbox"
                                                                id="na_model_unit_<?= $s ?>" data-target="model_unit" data-s="<?= $s ?>">
                                                            <label class="form-check-label small text-muted" for="na_model_unit_<?= $s ?>">N/A</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Nomor Rangka / <em class="fw-normal">Chassis No.</em> <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control inp-nomor-rangka" placeholder="Nomor rangka">
                                                    <div class="text-danger small mt-1 err-nomor-rangka"></div>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Nomor Mesin / <em class="fw-normal">Machine No.</em></label>
                                                    <div class="d-flex gap-2 align-items-start">
                                                        <div class="flex-grow-1">
                                                            <input type="text" class="form-control inp-nomor-mesin" placeholder="Nomor mesin">
                                                            <div class="text-danger small mt-1 err-nomor-mesin"></div>
                                                        </div>
                                                        <div class="form-check mt-2 flex-shrink-0">
                                                            <input class="form-check-input inp-na-check" type="checkbox"
                                                                id="na_nomor_mesin_<?= $s ?>" data-target="nomor_mesin" data-s="<?= $s ?>">
                                                            <label class="form-check-label small text-muted" for="na_nomor_mesin_<?= $s ?>">N/A</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Nomor Polisi / <em class="fw-normal">Police No.</em></label>
                                                    <div class="d-flex gap-2 align-items-start">
                                                        <div class="flex-grow-1">
                                                            <input type="text" class="form-control inp-no-polisi" placeholder="misal: DB 1234 GT" style="text-transform:uppercase;">
                                                            <div class="text-danger small mt-1 err-no-polisi"></div>
                                                        </div>
                                                        <div class="form-check mt-2 flex-shrink-0">
                                                            <input class="form-check-input inp-na-check" type="checkbox"
                                                                id="na_no_polisi_<?= $s ?>" data-target="no_polisi" data-s="<?= $s ?>">
                                                            <label class="form-check-label small text-muted" for="na_no_polisi_<?= $s ?>">N/A</label>
                                                        </div>
                                                    </div>
                                                    <small class="text-muted">Centang N/A jika kendaraan tidak memiliki nomor polisi.</small>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Perusahaan / <em class="fw-normal">Company</em> <span class="text-danger">*</span></label>
                                                    <select class="form-select select2-perusahaan" id="perusahaan_<?= $s ?>">
                                                        <option value="">— Pilih Perusahaan —</option>
                                                        <?php foreach ($perusahaan as $p): ?>
                                                            <option value="<?= html_escape($p->nama_perusahaan) ?>"><?= html_escape($p->nama_perusahaan) ?><?= $p->singkatan ? ' (' . html_escape($p->singkatan) . ')' : '' ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <div class="text-danger small mt-1 err-perusahaan"></div>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Tahun / <em class="fw-normal">Year</em> <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control inp-tahun" placeholder="misal: 2022" min="1990" max="<?= date('Y') + 1 ?>">
                                                    <div class="text-danger small mt-1 err-tahun"></div>
                                                </div>

                                                <!-- STNK -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">
                                                        Foto STNK
                                                        <span class="badge bg-warning text-dark ms-1">Wajib Unit Baru</span>
                                                    </label>
                                                    <!-- N/A toggle untuk STNK -->
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input inp-na-foto" type="checkbox"
                                                            id="na_stnk_<?= $s ?>" data-ftype="stnk" data-s="<?= $s ?>">
                                                        <label class="form-check-label small text-muted" for="na_stnk_<?= $s ?>">
                                                            N/A — Kendaraan tidak memiliki STNK (misal: alat berat tanpa plat)
                                                        </label>
                                                    </div>
                                                    <div class="border rounded p-3 d-flex align-items-center gap-3 flex-wrap upload-row na-upload-wrap" id="box_stnk_<?= $s ?>">
                                                        <i class="bi bi-card-text text-primary flex-shrink-0" style="font-size:2rem;"></i>
                                                        <div class="flex-grow-1">
                                                            <input type="file" class="form-control form-control-sm inp-stnk"
                                                                id="lampiran_stnk_<?= $s ?>" accept=".jpg,.jpeg,.png,.pdf">
                                                            <small class="text-muted">JPG, PNG, atau PDF. Maks 2MB.</small>
                                                            <div class="text-danger small err-stnk"></div>
                                                        </div>
                                                        <div class="thumb-stnk d-none">
                                                            <div class="position-relative">
                                                                <img class="thumb-stnk-img rounded border" src="" alt="STNK" style="height:52px;width:72px;object-fit:cover;">
                                                                <button type="button" class="btn-clear-stnk btn btn-danger rounded-circle p-0"
                                                                    style="width:18px;height:18px;font-size:9px;position:absolute;top:-7px;right:-7px;">
                                                                    <i class="bi bi-x"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="thumb-stnk-pdf d-none d-flex align-items-center gap-1">
                                                            <span class="badge bg-danger px-2 py-2"><i class="bi bi-file-earmark-pdf me-1"></i><span class="stnk-pdf-name"></span></span>
                                                            <button type="button" class="btn-clear-stnk btn btn-sm btn-outline-danger py-0 px-1 ms-1">
                                                                <i class="bi bi-x"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Maintenance Record / Mechanical Inspection Report -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">
                                                        Maintenance Records / Mechanical Inspection Reports
                                                        <span class="badge bg-warning text-dark ms-1">Kondisional</span>
                                                    </label>

                                                    <!-- Toggle: pernah maintenance di luar? -->
                                                    <div class="mb-2">
                                                        <div class="d-flex gap-3 align-items-center">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input inp-maintenance-luar"
                                                                    type="radio"
                                                                    name="pernah_maintenance_luar_<?= $s ?>"
                                                                    id="maintenance_tidak_<?= $s ?>"
                                                                    value="0" checked>
                                                                <label class="form-check-label" for="maintenance_tidak_<?= $s ?>">
                                                                    <span class="text-success"><i class="bi bi-check-circle me-1"></i>Tidak pernah maintenance di luar perusahaan</span>
                                                                </label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input inp-maintenance-luar"
                                                                    type="radio"
                                                                    name="pernah_maintenance_luar_<?= $s ?>"
                                                                    id="maintenance_ya_<?= $s ?>"
                                                                    value="1">
                                                                <label class="form-check-label" for="maintenance_ya_<?= $s ?>">
                                                                    <span class="text-danger"><i class="bi bi-exclamation-triangle me-1"></i>Pernah maintenance di luar perusahaan</span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <small class="text-muted d-block mt-1">
                                                            Jika unit pernah dibawa ke bengkel / workshop di luar perusahaan, dokumen maintenance wajib dilampirkan.
                                                        </small>
                                                    </div>

                                                    <!-- Upload dokumen maintenance — muncul jika "ya" dipilih -->
                                                    <div class="box-maintenance-upload" id="box_maintenance_<?= $s ?>" style="display:none;">
                                                        <div class="border rounded p-3 d-flex align-items-center gap-3 flex-wrap upload-row border-warning" id="box_maintenance_file_<?= $s ?>">
                                                            <i class="bi bi-file-earmark-medical text-warning flex-shrink-0" style="font-size:2rem;"></i>
                                                            <div class="flex-grow-1">
                                                                <input type="file" class="form-control form-control-sm inp-maintenance"
                                                                    id="lampiran_maintenance_record_<?= $s ?>"
                                                                    accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx">
                                                                <small class="text-muted">
                                                                    JPG, PNG, PDF, Word, atau Excel. Maks 10MB.<br>
                                                                    Contoh: catatan servis, laporan pengecekan mekanik, work order maintenance.
                                                                </small>
                                                                <div class="text-danger small err-maintenance"></div>
                                                            </div>
                                                            <div class="thumb-maintenance d-none">
                                                                <div class="position-relative">
                                                                    <img class="thumb-maintenance-img rounded border" src="" alt="Maintenance" style="height:52px;width:72px;object-fit:cover;">
                                                                    <button type="button" class="btn-clear-maintenance btn btn-danger rounded-circle p-0"
                                                                        style="width:18px;height:18px;font-size:9px;position:absolute;top:-7px;right:-7px;">
                                                                        <i class="bi bi-x"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="thumb-maintenance-doc d-none d-flex align-items-center gap-1">
                                                                <span class="badge bg-warning text-dark px-2 py-2">
                                                                    <i class="bi bi-file-earmark-text me-1"></i>
                                                                    <span class="maintenance-doc-name"></span>
                                                                </span>
                                                                <button type="button" class="btn-clear-maintenance btn btn-sm btn-outline-danger py-0 px-1 ms-1">
                                                                    <i class="bi bi-x"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Foto 4 sisi -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">
                                                        Foto Unit 4 Sisi
                                                        <small class="text-muted fw-normal ms-1">Depan, Belakang, Kiri, Kanan</small>
                                                    </label>
                                                    <!-- N/A toggle untuk semua foto sisi -->
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input inp-na-all-foto" type="checkbox"
                                                            id="na_all_foto_<?= $s ?>" data-s="<?= $s ?>">
                                                        <label class="form-check-label small text-muted" for="na_all_foto_<?= $s ?>">
                                                            N/A — Semua foto unit tidak tersedia (tandai N/A untuk semua sisi)
                                                        </label>
                                                    </div>
                                                    <div class="row g-2">
                                                        <?php
                                                        $foto_list = [
                                                            'unit_depan'    => ['Depan',    'bi-truck'],
                                                            'unit_belakang' => ['Belakang', 'bi-truck'],
                                                            'unit_kiri'     => ['Kiri',     'bi-layout-sidebar'],
                                                            'unit_kanan'    => ['Kanan',    'bi-layout-sidebar-reverse'],
                                                        ];
                                                        foreach ($foto_list as $fkey => [$flabel, $ficon]):
                                                        ?>
                                                            <div class="col-6 col-md-3">
                                                                <div class="foto-box border rounded text-center p-2 na-foto-box" id="fbox_<?= $fkey ?>_<?= $s ?>">
                                                                    <div class="fdefault-<?= $fkey ?>">
                                                                        <i class="bi <?= $ficon ?> text-primary d-block mb-1" style="font-size:1.5rem;"></i>
                                                                        <div class="small fw-semibold mb-1"><?= $flabel ?></div>
                                                                        <input type="file" class="form-control form-control-sm inp-foto-<?= $fkey ?>"
                                                                            id="lampiran_<?= $fkey ?>_<?= $s ?>"
                                                                            accept=".jpg,.jpeg,.png"
                                                                            data-fkey="<?= $fkey ?>" data-s="<?= $s ?>">
                                                                        <!-- N/A per sisi -->
                                                                        <div class="form-check mt-1 d-flex justify-content-center">
                                                                            <input class="form-check-input inp-na-foto-single" type="checkbox"
                                                                                id="na_<?= $fkey ?>_<?= $s ?>"
                                                                                data-fkey="<?= $fkey ?>" data-s="<?= $s ?>">
                                                                            <label class="form-check-label small text-muted ms-1" for="na_<?= $fkey ?>_<?= $s ?>">N/A</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="fpreview-<?= $fkey ?> d-none">
                                                                        <div class="position-relative d-inline-block">
                                                                            <img class="fimg-<?= $fkey ?> rounded border" src="" alt="<?= $flabel ?>"
                                                                                style="width:100%;height:80px;object-fit:cover;max-width:130px;">
                                                                            <button type="button" class="btn-clear-foto btn btn-danger rounded-circle p-0"
                                                                                style="width:20px;height:20px;font-size:10px;position:absolute;top:-8px;right:-8px;"
                                                                                data-fkey="<?= $fkey ?>" data-s="<?= $s ?>">
                                                                                <i class="bi bi-x"></i>
                                                                            </button>
                                                                        </div>
                                                                        <div class="fname-<?= $fkey ?> small text-success mt-1"></div>
                                                                    </div>
                                                                    <!-- N/A badge ditampilkan saat dicentang -->
                                                                    <div class="fna-<?= $fkey ?> d-none">
                                                                        <span class="badge bg-secondary text-white px-3 py-2">N/A</span>
                                                                    </div>
                                                                    <div class="err-foto-<?= $fkey ?> text-danger mt-1" style="font-size:11px;"></div>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>

                                            </div>
                                        </div><!-- end unit baru -->

                                        <!-- ── Unit Lama ── -->
                                        <div class="section-unit-lama" style="display:none;">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">
                                                        Cari Kendaraan / <em class="fw-normal">Search Unit</em>
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="alert alert-info py-2 mb-2" style="font-size:13px;">
                                                        <i class="bi bi-info-circle me-1"></i>
                                                        Hanya kendaraan yang <strong>pernah lulus</strong> uji kelayakan dan stiker sudah
                                                        <strong>expired</strong> yang dapat diajukan kembali (Recommissioning).
                                                        Data nomor mesin dan rangka akan terisi otomatis dari pengajuan terakhir.
                                                    </div>
                                                    <select class="form-select select2-kendaraan" id="id_kendaraan_<?= $s ?>">
                                                        <option value="">— Ketik nomor polisi atau jenis kendaraan... —</option>
                                                        <?php foreach ($kendaraan as $k): ?>
                                                            <?php
                                                            // Badge status stiker untuk label option
                                                            $stiker_info = '';
                                                            if (!empty($k->tgl_expired)) {
                                                                $stiker_info = ' (expired: ' . date('d M Y', strtotime($k->tgl_expired)) . ')';
                                                            } elseif ($k->status_stiker === 'belum_ada') {
                                                                $stiker_info = ' (belum ada stiker)';
                                                            }
                                                            ?>
                                                            <option value="<?= $k->id_kendaraan ?>"
                                                                data-json='<?= json_encode([
                                                                                'no_polisi'         => $k->no_polisi,
                                                                                'jenis_kendaraan'   => $k->jenis_kendaraan,
                                                                                'merk'              => $k->merk,
                                                                                'tipe'              => $k->tipe,
                                                                                'tahun'             => $k->tahun,
                                                                                'nomor_unit'        => $k->nomor_unit ?? '',
                                                                                'model_unit'        => $k->model_unit ?? $k->tipe ?? '',
                                                                                'perusahaan'        => $k->perusahaan ?? '',
                                                                                'last_nomor_mesin'  => $k->last_nomor_mesin ?? '',
                                                                                'last_nomor_rangka' => $k->last_nomor_rangka ?? '',
                                                                                'last_tujuan'       => $k->last_tujuan ?? '',
                                                                                'status_stiker'     => $k->status_stiker ?? '',
                                                                                'tgl_expired'       => $k->tgl_expired ?? '',
                                                                            ], JSON_HEX_QUOT | JSON_HEX_APOS) ?>'>
                                                                <?= html_escape($k->no_polisi) ?> — <?= html_escape($k->jenis_kendaraan) ?>
                                                                <?= html_escape($k->merk) ?> <?= html_escape($k->tipe) ?>
                                                                <?= html_escape($stiker_info) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <div class="text-danger small mt-1 err-id-kendaraan"></div>
                                                </div>

                                                <!-- Info kendaraan lama — muncul setelah pilih -->
                                                <div class="col-12 info-kendaraan-lama" style="display:none;">

                                                    <!-- Status stiker badge -->
                                                    <div class="mb-2 badge-stiker-wrap"></div>

                                                    <table class="table table-bordered table-sm mb-3">
                                                        <thead class="table-dark">
                                                            <tr>
                                                                <th colspan="4" class="text-center py-2">
                                                                    Detail Unit / <em class="fw-light">Unit Details</em>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td class="fw-semibold bg-light" style="width:22%">Tipe Unit</td>
                                                                <td class="lama-jenis" style="width:28%">—</td>
                                                                <td class="fw-semibold bg-light" style="width:22%">No. Polisi</td>
                                                                <td class="lama-nopol">—</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fw-semibold bg-light">Merk Unit</td>
                                                                <td class="lama-merk">—</td>
                                                                <td class="fw-semibold bg-light">Model / Tipe</td>
                                                                <td class="lama-model">—</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fw-semibold bg-light">Nomor Unit</td>
                                                                <td class="lama-nomor-unit">—</td>
                                                                <td class="fw-semibold bg-light">Tahun</td>
                                                                <td class="lama-tahun">—</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fw-semibold bg-light">Perusahaan</td>
                                                                <td class="lama-perusahaan" colspan="3">—</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>

                                                    <!-- Nomor Rangka & Mesin — editable, auto-fill dari data terakhir -->
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-semibold">
                                                                Nomor Rangka / <em class="fw-normal">Chassis No.</em>
                                                                <span class="text-danger">*</span>
                                                                <small class="text-muted fw-normal ms-1">(dari pengajuan terakhir, dapat diubah)</small>
                                                            </label>
                                                            <input type="text" class="form-control inp-nomor-rangka-lama"
                                                                placeholder="Nomor rangka kendaraan">
                                                            <div class="text-danger small mt-1 err-nomor-rangka-lama"></div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-semibold">
                                                                Nomor Mesin / <em class="fw-normal">Engine No.</em>
                                                                <small class="text-muted fw-normal ms-1">(dari pengajuan terakhir, dapat diubah)</small>
                                                            </label>
                                                            <input type="text" class="form-control inp-nomor-mesin-lama"
                                                                placeholder="Nomor mesin (opsional)">
                                                        </div>
                                                    </div>

                                                </div><!-- end info-kendaraan-lama -->
                                            </div>
                                        </div><!-- end unit lama -->

                                    </div>
                                </div><!-- end section detail unit -->


                                <!-- ══════════════════════════════════
                   SECTION 3 — Tujuan Penggunaan
              ══════════════════════════════════ -->
                                <div class="card mb-3 section-tujuan" style="display:none;">
                                    <div class="card-body pt-4">
                                        <h6 class="card-title d-flex align-items-center gap-2 mb-3">
                                            <span class="step-badge">3</span>
                                            Tujuan Penggunaan / <em class="fw-normal text-muted">Purpose of Access</em>
                                        </h6>
                                        <textarea class="form-control inp-tujuan" rows="4"
                                            placeholder="Jelaskan tujuan penggunaan kendaraan dan area operasi..."
                                            maxlength="1000"></textarea>
                                        <div class="d-flex justify-content-between mt-1">
                                            <div class="text-danger small err-tujuan"></div>
                                            <small class="text-muted ms-auto"><span class="char-count">0</span>/1000</small>
                                        </div>
                                    </div>
                                </div>


                                <!-- ══════════════════════════════════
                   SECTION 4 — Informasi Pemohon
              ══════════════════════════════════ -->
                                <div class="card mb-3 section-pemohon" style="display:none;">
                                    <div class="card-body pt-4">
                                        <h6 class="card-title d-flex align-items-center gap-2 mb-3">
                                            <span class="step-badge">4</span>
                                            Informasi Pemohon / <em class="fw-normal text-muted">Requester Information</em>
                                        </h6>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Diajukan Oleh</label>
                                                <input type="text" class="form-control" value="<?= html_escape($user['nama']) ?>" readonly disabled>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Email Pemohon <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                                    <input type="email" class="form-control inp-email-pemohon"
                                                        value="<?= html_escape($user['email'] ?? '') ?>" placeholder="nama@perusahaan.com">
                                                </div>
                                                <div class="text-danger small mt-1 err-email-pemohon"></div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Tanggal Pengajuan</label>
                                                <input type="text" class="form-control" value="<?= date('d F Y') ?>" readonly disabled>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Diteruskan ke</label>
                                                <div class="form-control-plaintext">
                                                    <span class="badge bg-primary px-3">Manager Dept → Review & Approval</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!-- ══════════════════════════════════
                   SUBMIT
              ══════════════════════════════════ -->
                                <div class="card mb-4 section-submit" style="display:none;">
                                    <div class="card-body py-3">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                            <small class="text-muted">
                                                <i class="bi bi-info-circle me-1 text-primary"></i>
                                                Setelah disubmit, pengajuan diteruskan ke <strong>Manager</strong> untuk review.
                                            </small>
                                            <div class="d-flex gap-2">
                                                <a href="<?= site_url('pengajuan') ?>" class="btn btn-outline-secondary">
                                                    <i class="bi bi-arrow-left me-1"></i>Kembali
                                                </a>
                                                <button type="button" class="btn btn-primary btn-submit-pengajuan">
                                                    <i class="bi bi-send me-1"></i>Submit Pengajuan
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div><!-- end formPengajuan_s -->
                        </div><!-- end tab-pane -->

                    <?php endforeach; ?>

                </div><!-- end tab-content -->
            </div>
        </div>
    </section>
</main>


<!-- ═══════════════════════════════════════════════════════
     STYLES
═══════════════════════════════════════════════════════ -->
<style>
    /* Step badge */
    .step-badge {
        width: 26px;
        height: 26px;
        font-size: 13px;
        border-radius: 50%;
        background: #4154f1;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Tab styling */
    .nav-tabs-akses {
        border-bottom: none;
    }

    .nav-tabs-akses .nav-link {
        border: 1px solid #dee2e6;
        border-bottom: none;
        border-radius: 8px 8px 0 0;
        color: #6c757d;
        background: #f8f9fa;
        margin-right: 4px;
        min-width: 160px;
        text-align: center;
        transition: all .2s;
    }

    .nav-tabs-akses .nav-link:hover {
        color: #4154f1;
        background: #f0f2ff;
    }

    .nav-tabs-akses .nav-link.active {
        color: #fff;
        background: #4154f1;
        border-color: #4154f1;
        font-weight: 600;
    }

    .nav-tabs-akses .nav-link.active small {
        color: rgba(255, 255, 255, .75) !important;
    }

    #tab-nonmining-btn.active {
        background: #495057;
        border-color: #495057;
    }

    #tab-nonmining-btn:hover:not(.active) {
        color: #495057;
    }

    #tab-underground-btn.active {
        background: #212529;
        border-color: #212529;
    }

    #tab-underground-btn:hover:not(.active) {
        color: #212529;
    }

    .tab-content-akses {
        border: 1px solid #dee2e6;
        border-radius: 0 8px 8px 8px;
        background: #fff;
    }

    .tab-content-akses>.tab-pane>div.card:first-child {
        border-left: none;
        border-right: none;
        border-top: none;
    }

    /* Tipe commissioning card */
    .tipe-card {
        transition: border-color .18s, background .18s;
        user-select: none;
    }

    .tipe-card:has(input:checked) {
        border-color: #4154f1 !important;
        background: #f0f2ff;
    }

    .tipe-card:hover {
        border-color: #adb5bd;
    }

    /* Foto box */
    .foto-box {
        transition: border-color .18s, background .18s;
        min-height: 130px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .foto-box.has-file {
        border-color: #2eca6a !important;
        background: #f0fff5;
    }

    .foto-box.has-error {
        border-color: #dc3545 !important;
    }

    /* Upload row (STNK & maintenance) */
    .upload-row.has-file {
        background: #f0fff5;
    }

    .upload-row.border-warning.has-file {
        background: #fffde7;
    }
</style>


<!-- ═══════════════════════════════════════════════════════
     SCRIPT
═══════════════════════════════════════════════════════ -->
<script>
    $(function() {

        var csrfName = '<?= $this->security->get_csrf_token_name() ?>';
        var csrfHash = '<?= $this->security->get_csrf_hash() ?>';

        // ── Suffix aktif dan modeUnit per tab ──────────────────────────────────
        // Disimpan per tab agar tidak saling terpengaruh
        // m=mining, nm=non_mining, ug=underground
        var tabState = {
            m: null,
            nm: null,
            ug: null
        }; // 'baru' | 'lama' | null

        // Helper: ambil container tab yang aktif
        function activeTab() {
            return $('.tab-pane.active.show');
        }

        function suffixOfTab($tab) {
            var akses = $tab.find('.inp-tipe-akses').val();
            return akses === 'mining' ? 'm' : (akses === 'non_mining' ? 'nm' : 'ug');
        }

        // ── Init Select2 untuk semua tab ──────────────────────────────────────
        ['m', 'nm', 'ug'].forEach(function(s) {
            $('#jenis_kendaraan_' + s).select2({
                placeholder: '— Pilih Tipe Unit —',
                allowClear: true,
                width: '100%'
            });
            $('#perusahaan_' + s).select2({
                placeholder: '— Pilih atau Ketik Perusahaan —',
                allowClear: true,
                width: '100%',
                tags: true
            });
            $('#id_kendaraan_' + s).select2({
                placeholder: 'Ketik nomor polisi atau jenis kendaraan...',
                allowClear: true,
                width: '100%'
            });
        });

        // ── SECTION 1: Pilih tipe commissioning ──────────────────────────────
        $(document).on('change', '.tipe-pengajuan-radio', function() {
            var $tab = $(this).closest('.tab-pane');
            var s = suffixOfTab($tab);
            var tipe = $(this).val();
            var modeUnit = (tipe === 'new_commissioning') ? 'baru' : 'lama';
            tabState[s] = modeUnit;

            // Badge mode unit
            var $badge = $tab.find('.badge-mode-unit');
            if (modeUnit === 'baru') {
                $badge.removeClass('bg-info text-white').addClass('bg-warning text-dark')
                    .html('<i class="bi bi-star me-1"></i>Unit Baru');
                $tab.find('.section-unit-baru').slideDown(200);
                $tab.find('.section-unit-lama').slideUp(200);
            } else {
                $badge.removeClass('bg-warning text-dark').addClass('bg-info text-white')
                    .html('<i class="bi bi-clock-history me-1"></i>Recommissioning');
                $tab.find('.section-unit-lama').slideDown(200);
                $tab.find('.section-unit-baru').slideUp(200);
            }

            // Tampilkan section 2–4 + submit
            $tab.find('.section-detail-unit, .section-tujuan, .section-pemohon, .section-submit')
                .slideDown(250);
            $tab.find('.err-tipe-pengajuan').text('');
        });

        // ── Auto-fill kendaraan lama ──────────────────────────────────────────

        $(document).off('change', '.select2-kendaraan');
        $(document).on('change', '.select2-kendaraan', function() {
            var $tab = $(this).closest('.tab-pane');
            var opt = $(this).find('option:selected');

            if (!opt.val()) {
                $tab.find('.info-kendaraan-lama').slideUp(200);
                return;
            }

            var d = opt.data('json');
            if (typeof d === 'string') {
                try {
                    d = JSON.parse(d);
                } catch (e) {
                    return;
                }
            }

            // Tabel info
            $tab.find('.lama-jenis').text(d.jenis_kendaraan || '—');
            $tab.find('.lama-nomor-unit').text(d.nomor_unit || '—');
            $tab.find('.lama-merk').text(d.merk || '—');
            $tab.find('.lama-model').text(d.model_unit || d.tipe || '—');
            $tab.find('.lama-nopol').text(d.no_polisi || '—');
            $tab.find('.lama-perusahaan').text(d.perusahaan || '—');
            $tab.find('.lama-tahun').text(d.tahun || '—');

            // Auto-fill nomor rangka & mesin dari data terakhir
            $tab.find('.inp-nomor-rangka-lama').val(d.last_nomor_rangka || '');
            $tab.find('.inp-nomor-mesin-lama').val(d.last_nomor_mesin || '');

            // Auto-fill tujuan penggunaan
            if (d.last_tujuan) {
                $tab.find('.inp-tujuan').val(d.last_tujuan);
                $tab.find('.char-count').text(d.last_tujuan.length);
            }

            // Badge status stiker
            var badgeHtml = '';
            if (d.status_stiker === 'expired' && d.tgl_expired) {
                badgeHtml = '<span class="badge bg-danger text-white me-2">' +
                    '<i class="bi bi-x-circle-fill me-1"></i>Stiker Expired: ' +
                    d.tgl_expired.substring(0, 10) + '</span>' +
                    '<small class="text-muted">Unit sudah dapat diajukan kembali.</small>';
            } else if (d.status_stiker === 'belum_ada') {
                badgeHtml = '<span class="badge bg-secondary text-white me-2">' +
                    '<i class="bi bi-dash-circle me-1"></i>Belum Pernah Ada Stiker</span>';
            }
            $tab.find('.badge-stiker-wrap').html(badgeHtml);

            $tab.find('.info-kendaraan-lama').slideDown(200);
            $tab.find('.err-id-kendaraan').text('');
        });
        // ── Char counter tujuan ───────────────────────────────────────────────
        $(document).on('input', '.inp-tujuan', function() {
            $(this).closest('.card').find('.char-count').text($(this).val().length);
        });

        // ── Upload foto 4 sisi ────────────────────────────────────────────────
        $(document).on('change', 'input[type="file"][data-fkey]', function() {
            var fkey = $(this).data('fkey');
            var s = $(this).data('s');
            var file = this.files ? this.files[0] : null;
            if (!file) return;

            var reader = new FileReader();
            reader.onload = function(e) {
                var $box = $('#fbox_' + fkey + '_' + s);
                $box.find('.fimg-' + fkey).attr('src', e.target.result);
                var name = file.name.length > 14 ? file.name.substring(0, 12) + '…' : file.name;
                $box.find('.fname-' + fkey).text(name);
                $box.find('.fdefault-' + fkey).addClass('d-none');
                $box.find('.fpreview-' + fkey).removeClass('d-none');
                $box.addClass('has-file').removeClass('has-error');
                $box.find('.err-foto-' + fkey).text('');
            };
            reader.readAsDataURL(file);
        });

        // Hapus foto
        $(document).on('click', '.btn-clear-foto', function() {
            var fkey = $(this).data('fkey');
            var s = $(this).data('s');
            var $box = $('#fbox_' + fkey + '_' + s);
            var old = document.getElementById('lampiran_' + fkey + '_' + s);
            var neu = old.cloneNode(true);
            old.parentNode.replaceChild(neu, old);

            $box.find('.fpreview-' + fkey).addClass('d-none');
            $box.find('.fdefault-' + fkey).removeClass('d-none');
            $box.removeClass('has-file');
            $box.find('.fimg-' + fkey).attr('src', '');
        });

        // ── Upload STNK ──────────────────────────────────────────────────────
        $(document).on('change', '.inp-stnk', function() {
            var file = this.files[0];
            if (!file) return;
            var $wrap = $(this).closest('.upload-row');
            $wrap.addClass('has-file');
            $wrap.find('.err-stnk').text('');

            if (file.type === 'application/pdf') {
                var name = file.name.length > 20 ? file.name.substring(0, 18) + '…' : file.name;
                $wrap.find('.stnk-pdf-name').text(name);
                $wrap.find('.thumb-stnk').addClass('d-none');
                $wrap.find('.thumb-stnk-pdf').removeClass('d-none');
            } else {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $wrap.find('.thumb-stnk-img').attr('src', e.target.result);
                    $wrap.find('.thumb-stnk').removeClass('d-none');
                    $wrap.find('.thumb-stnk-pdf').addClass('d-none');
                };
                reader.readAsDataURL(file);
            }
        });

        $(document).on('click', '.btn-clear-stnk', function() {
            var $wrap = $(this).closest('.upload-row');
            var $inp = $wrap.find('.inp-stnk');
            var neu = $inp[0].cloneNode(true);
            $inp[0].parentNode.replaceChild(neu, $inp[0]);
            $wrap.removeClass('has-file');
            $wrap.find('.thumb-stnk, .thumb-stnk-pdf').addClass('d-none');
            $wrap.find('.thumb-stnk-img').attr('src', '');
        });

        // ── Toggle maintenance upload: tampil/sembunyikan kotak upload ────────
        $(document).on('change', '.inp-maintenance-luar', function() {
            var $tab = $(this).closest('.tab-pane');
            var s = suffixOfTab($tab);
            if ($(this).val() === '1') {
                $('#box_maintenance_' + s).slideDown(200);
            } else {
                $('#box_maintenance_' + s).slideUp(200);
                // Reset file jika disembunyikan
                var $inp = $('#lampiran_maintenance_record_' + s);
                var neu = $inp[0].cloneNode(true);
                $inp[0].parentNode.replaceChild(neu, $inp[0]);
                var $wrap = $('#box_maintenance_file_' + s);
                $wrap.removeClass('has-file');
                $wrap.find('.thumb-maintenance, .thumb-maintenance-doc').addClass('d-none');
                $wrap.find('.err-maintenance').text('');
            }
        });

        // ── Upload preview maintenance record ────────────────────────────────
        $(document).on('change', '.inp-maintenance', function() {
            var file = this.files[0];
            if (!file) return;
            var $wrap = $(this).closest('.upload-row');
            $wrap.addClass('has-file');
            $wrap.find('.err-maintenance').text('');

            var imgTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (imgTypes.indexOf(file.type) >= 0) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $wrap.find('.thumb-maintenance-img').attr('src', e.target.result);
                    $wrap.find('.thumb-maintenance').removeClass('d-none');
                    $wrap.find('.thumb-maintenance-doc').addClass('d-none');
                };
                reader.readAsDataURL(file);
            } else {
                var name = file.name.length > 22 ? file.name.substring(0, 20) + '…' : file.name;
                $wrap.find('.maintenance-doc-name').text(name);
                $wrap.find('.thumb-maintenance-doc').removeClass('d-none');
                $wrap.find('.thumb-maintenance').addClass('d-none');
            }
        });

        $(document).on('click', '.btn-clear-maintenance', function() {
            var $wrap = $(this).closest('.upload-row');
            var $inp = $wrap.find('.inp-maintenance');
            var neu = $inp[0].cloneNode(true);
            $inp[0].parentNode.replaceChild(neu, $inp[0]);
            $wrap.removeClass('has-file');
            $wrap.find('.thumb-maintenance, .thumb-maintenance-doc').addClass('d-none');
            $wrap.find('.thumb-maintenance-img').attr('src', '');
        });

        // ── VALIDASI & SUBMIT ─────────────────────────────────────────────────
        // ── N/A checkbox handler — text fields (nomor unit, model, mesin, polisi) ─
        $(document).on('change', '.inp-na-check', function() {
            var target = $(this).data('target');
            var $tab = $(this).closest('.tab-pane');
            var isNa = $(this).prop('checked');
            var $inp = $tab.find('.inp-' + target.replace(/_/g, '-'));
            if (isNa) {
                $inp.val('N/A').prop('disabled', true).addClass('bg-secondary text-white');
            } else {
                $inp.val('').prop('disabled', false).removeClass('bg-secondary text-white');
            }
        });

        // ── N/A checkbox — STNK upload ─────────────────────────────────────
        $(document).on('change', '.inp-na-foto', function() {
            var $tab = $(this).closest('.tab-pane');
            var s = suffixOfTab($tab);
            var isNa = $(this).prop('checked');
            var $wrap = $(this).closest('.col-12').find('.na-upload-wrap');
            if (isNa) {
                $wrap.css('opacity', '.4').find('input[type="file"]').prop('disabled', true);
                $wrap.find('.err-stnk').text('');
            } else {
                $wrap.css('opacity', '1').find('input[type="file"]').prop('disabled', false);
            }
        });

        // ── N/A checkbox — semua foto sisi sekaligus ───────────────────────
        $(document).on('change', '.inp-na-all-foto', function() {
            var $tab = $(this).closest('.tab-pane');
            var s = suffixOfTab($tab);
            var isNa = $(this).prop('checked');
            ['unit_depan', 'unit_belakang', 'unit_kiri', 'unit_kanan'].forEach(function(fkey) {
                var $box = $('#fbox_' + fkey + '_' + s);
                var $cb = $('#na_' + fkey + '_' + s);
                $cb.prop('checked', isNa).trigger('change');
            });
        });

        // ── N/A checkbox — foto per sisi ───────────────────────────────────
        $(document).on('change', '.inp-na-foto-single', function() {
            var fkey = $(this).data('fkey');
            var s = $(this).data('s');
            var isNa = $(this).prop('checked');
            var $box = $('#fbox_' + fkey + '_' + s);
            if (isNa) {
                $box.find('.fdefault-' + fkey).addClass('d-none');
                $box.find('.fpreview-' + fkey).addClass('d-none');
                $box.find('.fna-' + fkey).removeClass('d-none');
                $box.find('input[type="file"]').prop('disabled', true);
                $box.addClass('bg-secondary bg-opacity-10').removeClass('has-error');
            } else {
                $box.find('.fdefault-' + fkey).removeClass('d-none');
                $box.find('.fna-' + fkey).addClass('d-none');
                $box.find('input[type="file"]').prop('disabled', false);
                $box.removeClass('bg-secondary bg-opacity-10');
            }
        });

        // ── VALIDASI & SUBMIT ─────────────────────────────────────────────────
        $(document).on('click', '.btn-submit-pengajuan', function() {
            var $tab = $(this).closest('.tab-pane');
            var s = suffixOfTab($tab);
            var modeUnit = tabState[s];
            var tipaAkses = $tab.find('.inp-tipe-akses').val();
            var errors = false;

            $tab.find('[class*="err-"]').text('');
            $tab.find('.foto-box').removeClass('has-error');

            if (!$tab.find('.tipe-pengajuan-radio:checked').val()) {
                $tab.find('.err-tipe-pengajuan').text('Pilih tipe commissioning terlebih dahulu.');
                errors = true;
            }

            if (modeUnit === 'baru') {
                if (!$('#jenis_kendaraan_' + s).val()) {
                    $tab.find('.err-jenis').text('Tipe unit wajib dipilih.');
                    errors = true;
                }
                if (!$('#perusahaan_' + s).val()) {
                    $tab.find('.err-perusahaan').text('Perusahaan wajib dipilih.');
                    errors = true;
                }
                if (!$tab.find('.inp-merk').val().trim()) {
                    $tab.find('.err-merk').text('Merk unit wajib diisi.');
                    errors = true;
                }
                if (!$tab.find('.inp-nomor-rangka').val().trim()) {
                    $tab.find('.err-nomor-rangka').text('Nomor rangka wajib diisi.');
                    errors = true;
                }
                if (!$tab.find('.inp-tahun').val().trim()) {
                    $tab.find('.err-tahun').text('Tahun wajib diisi.');
                    errors = true;
                }

                // Nomor Polisi — wajib KECUALI jika N/A dicentang
                var naPol = $('#na_no_polisi_' + s).prop('checked');
                if (!naPol && !$tab.find('.inp-no-polisi').val().trim()) {
                    $tab.find('.err-no-polisi').text('Nomor polisi wajib diisi atau centang N/A.');
                    errors = true;
                }
                // Nomor Unit — opsional (N/A atau diisi)
                // Nomor Mesin — wajib KECUALI N/A
                var naMesin = $('#na_nomor_mesin_' + s).prop('checked');
                if (!naMesin && !$tab.find('.inp-nomor-mesin').val().trim()) {
                    $tab.find('.err-nomor-mesin').text('Nomor mesin wajib diisi atau centang N/A.');
                    errors = true;
                }

                // STNK — wajib KECUALI N/A
                var naStnk = $('#na_stnk_' + s).prop('checked');
                if (!naStnk && !document.getElementById('lampiran_stnk_' + s).files.length) {
                    $tab.find('.err-stnk').text('Foto STNK wajib diupload atau centang N/A.');
                    errors = true;
                }

                // Foto 4 sisi — wajib KECUALI masing-masing N/A
                ['unit_depan', 'unit_belakang', 'unit_kiri', 'unit_kanan'].forEach(function(fkey) {
                    var naFoto = $('#na_' + fkey + '_' + s).prop('checked');
                    if (!naFoto && !document.getElementById('lampiran_' + fkey + '_' + s).files.length) {
                        $('#fbox_' + fkey + '_' + s).find('.err-foto-' + fkey)
                            .text('Upload foto atau centang N/A.')
                            .closest('.foto-box').addClass('has-error');
                        errors = true;
                    }
                });

                // Validasi maintenance record
                var maintenanceLuar = $('input[name="pernah_maintenance_luar_' + s + '"]:checked').val();
                if (maintenanceLuar === '1' && !document.getElementById('lampiran_maintenance_record_' + s).files.length) {
                    $tab.find('.err-maintenance').text('Dokumen Maintenance Record wajib diupload.');
                    errors = true;
                }

            } else if (modeUnit === 'lama') {
                if (!$('#id_kendaraan_' + s).val()) {
                    $tab.find('.err-id-kendaraan').text('Pilih kendaraan terlebih dahulu.');
                    errors = true;
                }
            } else {
                $tab.find('.err-tipe-pengajuan').text('Pilih tipe commissioning terlebih dahulu.');
                errors = true;
            }

            if (!$tab.find('.inp-tujuan').val().trim()) {
                $tab.find('.err-tujuan').text('Tujuan penggunaan wajib diisi.');
                errors = true;
            }
            if (!$tab.find('.inp-email-pemohon').val().trim()) {
                $tab.find('.err-email-pemohon').text('Email pemohon wajib diisi.');
                errors = true;
            }

            if (errors) {
                toastr.warning('Lengkapi semua field yang wajib diisi.');
                var firstErr = $tab.find('[class*="err-"]').filter(function() {
                    return $(this).text().trim() !== '';
                }).first();
                if (firstErr.length) $('html,body').animate({
                    scrollTop: firstErr.offset().top - 130
                }, 300);
                return;
            }

            var aksesLabelMap = {
                'mining': 'Mining Access (Area Tambang)',
                'non_mining': 'Non Mining (Area Non-Tambang)',
                'underground': 'Underground (Area Bawah Tanah)',
            };
            var aksesLabel = aksesLabelMap[tipaAkses] || tipaAkses;

            Swal.fire({
                title: 'Submit Pengajuan?',
                html: 'Tipe Akses: <strong>' + aksesLabel + '</strong><br>Pengajuan akan diteruskan ke <strong>Manager Dept</strong> untuk review.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4154f1',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-send me-1"></i>Ya, Submit',
                cancelButtonText: 'Batal',
            }).then(function(r) {
                if (!r.isConfirmed) return;

                NProgress.start();
                var $btn = $tab.find('.btn-submit-pengajuan');
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');

                var maintenanceLuar = $('input[name="pernah_maintenance_luar_' + s + '"]:checked').val() || '0';

                var fd = new FormData();
                fd.append(csrfName, csrfHash);
                fd.append('mode_unit', modeUnit);
                fd.append('tipe_pengajuan', $tab.find('.tipe-pengajuan-radio:checked').val());
                fd.append('tipe_akses', tipaAkses);
                fd.append('tujuan', $tab.find('.inp-tujuan').val());
                fd.append('email_pemohon', $tab.find('.inp-email-pemohon').val());
                fd.append('pernah_maintenance_luar', maintenanceLuar);

                if (modeUnit === 'baru') {
                    // Flag N/A untuk field opsional
                    fd.append('is_na_no_polisi', $('#na_no_polisi_' + s).prop('checked') ? '1' : '0');
                    fd.append('is_na_nomor_mesin', $('#na_nomor_mesin_' + s).prop('checked') ? '1' : '0');
                    fd.append('is_na_nomor_unit', $('#na_nomor_unit_' + s).prop('checked') ? '1' : '0');
                    fd.append('is_na_model_unit', $('#na_model_unit_' + s).prop('checked') ? '1' : '0');
                    fd.append('is_na_stnk', $('#na_stnk_' + s).prop('checked') ? '1' : '0');
                    fd.append('is_na_foto', $('#na_all_foto_' + s).prop('checked') ? '1' : '0');
                    // Flag N/A per sisi
                    ['unit_depan', 'unit_belakang', 'unit_kiri', 'unit_kanan'].forEach(function(fk) {
                        fd.append('is_na_foto_' + fk, $('#na_' + fk + '_' + s).prop('checked') ? '1' : '0');
                    });

                    fd.append('jenis_kendaraan', $('#jenis_kendaraan_' + s).val());
                    fd.append('nomor_unit', $('#na_nomor_unit_' + s).prop('checked') ? 'N/A' : $tab.find('.inp-nomor-unit').val());
                    fd.append('merk', $tab.find('.inp-merk').val());
                    fd.append('model_unit', $('#na_model_unit_' + s).prop('checked') ? 'N/A' : $tab.find('.inp-model-unit').val());
                    fd.append('nomor_rangka', $tab.find('.inp-nomor-rangka').val());
                    fd.append('nomor_mesin', $('#na_nomor_mesin_' + s).prop('checked') ? 'N/A' : $tab.find('.inp-nomor-mesin').val());
                    fd.append('no_polisi', $('#na_no_polisi_' + s).prop('checked') ? 'N/A' : $tab.find('.inp-no-polisi').val().toUpperCase());
                    fd.append('perusahaan', $('#perusahaan_' + s).val());
                    fd.append('tahun', $tab.find('.inp-tahun').val());

                    // File upload — skip jika N/A
                    if (!$('#na_stnk_' + s).prop('checked')) {
                        var elStnk = document.getElementById('lampiran_stnk_' + s);
                        if (elStnk && elStnk.files[0]) fd.append('lampiran_stnk', elStnk.files[0]);
                    }
                    ['unit_depan', 'unit_belakang', 'unit_kiri', 'unit_kanan'].forEach(function(fbase) {
                        if (!$('#na_' + fbase + '_' + s).prop('checked')) {
                            var el = document.getElementById('lampiran_' + fbase + '_' + s);
                            if (el && el.files[0]) fd.append('lampiran_' + fbase, el.files[0]);
                        }
                    });

                    var elMaint = document.getElementById('lampiran_maintenance_record_' + s);
                    if (elMaint && elMaint.files[0]) fd.append('lampiran_maintenance_record', elMaint.files[0]);
                } else {
                    fd.append('id_kendaraan', $('#id_kendaraan_' + s).val());
                    fd.append('nomor_rangka', $tab.find('.inp-nomor-rangka-lama').val());
                    fd.append('nomor_mesin', $tab.find('.inp-nomor-mesin-lama').val());
                }

                $.ajax({
                    url: '<?= site_url('pengajuan/store') ?>',
                    type: 'POST',
                    data: fd,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(res) {
                        NProgress.done();
                        $btn.prop('disabled', false).html('<i class="bi bi-send me-1"></i>Submit Pengajuan');
                        if (res.status === 'success') {
                            Swal.fire({
                                    title: 'Berhasil!',
                                    html: res.message,
                                    icon: 'success',
                                    confirmButtonColor: '#4154f1'
                                })
                                .then(function() {
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
                        $btn.prop('disabled', false).html('<i class="bi bi-send me-1"></i>Submit Pengajuan');
                        toastr.error('Terjadi kesalahan server.');
                    }
                });
            });
        });


    });
</script>