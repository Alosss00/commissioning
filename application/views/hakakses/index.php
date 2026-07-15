<main id="main" class="main">
    <div class="pagetitle">
        <h1>Hak Akses</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Home</a></li>
                <li class="breadcrumb-item active">Hak Akses</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-12">

                <div class="card mb-3">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-success px-2 py-1"><i class="bi bi-check-lg"></i></span>
                                <span class="small text-muted">Punya akses</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-light text-muted border px-2 py-1"><i class="bi bi-dash"></i></span>
                                <span class="small text-muted">Tidak punya akses</span>
                            </div>
                            <div class="ms-auto text-muted small">
                                <i class="bi bi-info-circle me-1"></i>
                                Matrix ini menunjukkan hak akses default per role. Perubahan akses dikelola melalui Manajemen User.
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                $role_colors = [1 => 'danger', 2 => 'primary', 3 => 'warning', 4 => 'success', 5 => 'dark'];
                $role_icons  = [1 => 'bi-shield-fill-check', 2 => 'bi-person-fill', 3 => 'bi-tools', 4 => 'bi-heart-pulse-fill', 5 => 'bi-star-fill'];
                ?>

                <?php foreach ($matrix as $grup => $items): ?>
                    <div class="card mb-3">
                        <div class="card-header bg-white d-flex align-items-center gap-2 py-2">
                            <i class="bi bi-folder2-open text-primary"></i>
                            <strong><?= html_escape($grup) ?></strong>
                            <span class="badge bg-secondary ms-1" style="font-size:11px;"><?= count($items) ?> fitur</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="min-width:220px;">Fitur / Aksi</th>
                                            <?php foreach ($roles as $r): ?>
                                                <th class="text-center" style="width:110px;">
                                                    <div class="d-flex flex-column align-items-center gap-1">
                                                        <span class="badge bg-<?= $role_colors[$r->id_role] ?? 'secondary' ?> px-2">
                                                            <i class="bi <?= $role_icons[$r->id_role] ?? 'bi-person' ?> me-1"></i>
                                                            <?= html_escape($r->nama_role) ?>
                                                        </span>
                                                    </div>
                                                </th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items as $fitur => $allowed_roles): ?>
                                            <tr>
                                                <td class="ps-3">
                                                    <span class="small"><?= html_escape($fitur) ?></span>
                                                </td>
                                                <?php foreach ($roles as $r): ?>
                                                    <td class="text-center">
                                                        <?php if (in_array($r->id_role, $allowed_roles)): ?>
                                                            <span class="badge bg-success px-2 py-1">
                                                                <i class="bi bi-check-lg"></i>
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="text-muted small"><i class="bi bi-dash"></i></span>
                                                        <?php endif; ?>
                                                    </td>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Info Multi-Role -->
                <div class="card border-info">
                    <div class="card-body py-3">
                        <h6 class="text-info mb-2"><i class="bi bi-layers me-2"></i>Multi-Role</h6>
                        <p class="text-muted small mb-2">
                            User dapat memiliki lebih dari satu role. Hak akses yang berlaku adalah <strong>gabungan (union)</strong> dari semua role yang dimiliki.
                        </p>
                        <div class="bg-light rounded p-2">
                            <code class="small">
                                Contoh: User memiliki role <strong>User/Dept</strong> + <strong>Admin OHS</strong><br>
                                → Bisa buat pengajuan (dari User) + bisa approve OHS + isi jadwal (dari Admin OHS)
                            </code>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</main>