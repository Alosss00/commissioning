<?php
$_sess_foto = $this->session->userdata('foto');
$_sess_nama = $this->session->userdata('nama');
$_sess_role = $this->session->userdata('role');
$_sess_jabatan = $this->session->userdata('jabatan');
$_roles_names_raw = $this->session->userdata('roles_names');
$_roles_names     = is_array($_roles_names_raw) ? $_roles_names_raw : [];
// Fallback dari role tunggal jika session lama
if (empty($_roles_names) && $_sess_role) {
    $role_map_temp = [1 => 'Administrator', 2 => 'User / Dept', 3 => 'Mekanik', 4 => 'Admin OHS', 5 => 'KTT'];
    $_roles_names  = [isset($role_map_temp[$_sess_role]) ? $role_map_temp[$_sess_role] : 'User'];
}

// Role label
$role_labels   = [1 => 'Administrator', 2 => 'User / Dept', 3 => 'Mekanik', 4 => 'Admin OHS', 5 => 'KTT'];
$primary_label = isset($role_labels[$_sess_role]) ? $role_labels[$_sess_role] : 'User';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= isset($title) ? html_escape($title) . ' — TACTIC' : 'TACTIC' ?></title>

    <!-- Favicons -->
    <link href="<?= base_url('assets/img/favicon.png') ?>" rel="icon">
    <link href="<?= base_url('assets/img/apple-touch-icon.png') ?>" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i&display=swap" rel="stylesheet">

    <!-- Vendor CSS -->
    <link href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/bootstrap-icons/bootstrap-icons.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/boxicons/css/boxicons.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">

    <!-- Plugin CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/datatables.bootstrap5.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/sweetalert2.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/select2.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/toastr.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/flatpickr.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/nprogress.css') ?>">

    <script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>

    <!-- CSRF Token Meta Tags -->
    <meta name="csrf-token-name" content="<?= $this->security->get_csrf_token_name(); ?>">
    <meta name="csrf-token-hash" content="<?= $this->security->get_csrf_hash(); ?>">

    <script> // Setup global AJAX and Form CSRF protection
        function getCsrfCookieValue() {
            var m = document.cookie.match(/(?:^|; )csrf_cookie=([^;]*)/);
            return m ? decodeURIComponent(m[1]) : null;
        }
        $.ajaxPrefilter(function(options, originalOptions, jqXHR) {
            var csrfName = $('meta[name="csrf-token-name"]').attr('content');
            var csrfHash = getCsrfCookieValue() || $('meta[name="csrf-token-hash"]').attr('content');
            
            if (options.type.toUpperCase() === 'POST' && csrfName && csrfHash) {
                if (options.data instanceof FormData) {
                    if (!options.data.has(csrfName)) {
                        options.data.append(csrfName, csrfHash);
                    }
                } else if (typeof options.data === 'string') {
                    if (options.data.indexOf(csrfName + '=') === -1) {
                        options.data += (options.data ? '&' : '') + csrfName + '=' + encodeURIComponent(csrfHash);
                    }
                } else if (typeof options.data === 'object' && options.data !== null) {
                    options.data[csrfName] = csrfHash;
                } else if (!options.data) {
                    options.data = csrfName + '=' + encodeURIComponent(csrfHash);
                }
            }
        });

        // Automatically add CSRF token hidden inputs to all POST forms on document ready
        $(function() {
            var csrfName = $('meta[name="csrf-token-name"]').attr('content');
            var csrfHash = getCsrfCookieValue() || $('meta[name="csrf-token-hash"]').attr('content');
            if (csrfName && csrfHash) {
                // Attach to dynamic/future POST forms
                $(document).on('submit', 'form', function() {
                    var $form = $(this);
                    if ($form.attr('method') && $form.attr('method').toUpperCase() === 'POST') {
                        if ($form.find('input[name="' + csrfName + '"]').length === 0) {
                            $form.append('<input type="hidden" name="' + csrfName + '" value="' + csrfHash + '">');
                        } else {
                            $form.find('input[name="' + csrfName + '"]').val(csrfHash);
                        }
                    }
                });
                
                // Add it immediately for elements parsed on load
                $('form').each(function() {
                    var $form = $(this);
                    if ($form.attr('method') && $form.attr('method').toUpperCase() === 'POST') {
                        if ($form.find('input[name="' + csrfName + '"]').length === 0) {
                            $form.append('<input type="hidden" name="' + csrfName + '" value="' + csrfHash + '">');
                        }
                    }
                });
            }
        });
    </script>

 <!-- CSRF Token Meta Tags -->
    <meta name="csrf-token-name" content="<?= $this->security->get_csrf_token_name(); ?>">
    <meta name="csrf-token-hash" content="<?= $this->security->get_csrf_hash(); ?>">
    <script>
        // Setup global AJAX and Form CSRF protection
        $.ajaxPrefilter(function(options, originalOptions, jqXHR) {
            var csrfName = $('meta[name="csrf-token-name"]').attr('content');
            var csrfHash = getCsrfCookieValue() || $('meta[name="csrf-token-hash"]').attr('content');
            
            if (options.type.toUpperCase() === 'POST' && csrfName && csrfHash) {
                if (options.data instanceof FormData) {
                    if (!options.data.has(csrfName)) {
                        options.data.append(csrfName, csrfHash);
                    }
                } else if (typeof options.data === 'string') {
                    if (options.data.indexOf(csrfName + '=') === -1) {
                        options.data += (options.data ? '&' : '') + csrfName + '=' + encodeURIComponent(csrfHash);
                    }
                } else if (typeof options.data === 'object' && options.data !== null) {
                    options.data[csrfName] = csrfHash;
                } else if (!options.data) {
                    options.data = csrfName + '=' + encodeURIComponent(csrfHash);
                }
            }
        });
        // Automatically add CSRF token hidden inputs to all POST forms on document ready
        $(function() {
            var csrfName = $('meta[name="csrf-token-name"]').attr('content');
            var csrfHash = $('meta[name="csrf-token-hash"]').attr('content');
            if (csrfName && csrfHash) {
                // Attach to dynamic/future POST forms
                $(document).on('submit', 'form', function() {
                    var $form = $(this);
                    if ($form.attr('method') && $form.attr('method').toUpperCase() === 'POST') {
                        if ($form.find('input[name="' + csrfName + '"]').length === 0) {
                            $form.append('<input type="hidden" name="' + csrfName + '" value="' + csrfHash + '">');
                        } else {
                            $form.find('input[name="' + csrfName + '"]').val(csrfHash);
                        }
                    }
                });
                
                // Add it immediately for elements parsed on load
                $('form').each(function() {
                    var $form = $(this);
                    if ($form.attr('method') && $form.attr('method').toUpperCase() === 'POST') {
                        if ($form.find('input[name="' + csrfName + '"]').length === 0) {
                            $form.append('<input type="hidden" name="' + csrfName + '" value="' + csrfHash + '">');
                        }
                    }
                });
            }
        });
    </script>
    <!-- CSRF Token Meta Tags -->
    <meta name="csrf-token-name" content="<?= $this->security->get_csrf_token_name(); ?>">
    <meta name="csrf-token-hash" content="<?= $this->security->get_csrf_hash(); ?>">
    <script>
        // Setup global AJAX and Form CSRF protection
        $.ajaxPrefilter(function(options, originalOptions, jqXHR) {
            var csrfName = $('meta[name="csrf-token-name"]').attr('content');
            var csrfHash = getCsrfCookieValue() || $('meta[name="csrf-token-hash"]').attr('content');
            
            if (options.type.toUpperCase() === 'POST' && csrfName && csrfHash) {
                if (options.data instanceof FormData) {
                    if (!options.data.has(csrfName)) {
                        options.data.append(csrfName, csrfHash);
                    }
                } else if (typeof options.data === 'string') {
                    if (options.data.indexOf(csrfName + '=') === -1) {
                        options.data += (options.data ? '&' : '') + csrfName + '=' + encodeURIComponent(csrfHash);
                    }
                } else if (typeof options.data === 'object' && options.data !== null) {
                    options.data[csrfName] = csrfHash;
                } else if (!options.data) {
                    options.data = csrfName + '=' + encodeURIComponent(csrfHash);
                }
            }
        });
        // Automatically add CSRF token hidden inputs to all POST forms on document ready
        $(function() {
            var csrfName = $('meta[name="csrf-token-name"]').attr('content');
            var csrfHash = getCsrfCookieValue() || $('meta[name="csrf-token-hash"]').attr('content');
            if (csrfName && csrfHash) {
                // Attach to dynamic/future POST forms
                $(document).on('submit', 'form', function() {
                    var $form = $(this);
                    if ($form.attr('method') && $form.attr('method').toUpperCase() === 'POST') {
                        if ($form.find('input[name="' + csrfName + '"]').length === 0) {
                            $form.append('<input type="hidden" name="' + csrfName + '" value="' + csrfHash + '">');
                        } else {
                            $form.find('input[name="' + csrfName + '"]').val(csrfHash);
                        }
                    }
                });
                
                // Add it immediately for elements parsed on load
                $('form').each(function() {
                    var $form = $(this);
                    if ($form.attr('method') && $form.attr('method').toUpperCase() === 'POST') {
                        if ($form.find('input[name="' + csrfName + '"]').length === 0) {
                            $form.append('<input type="hidden" name="' + csrfName + '" value="' + csrfHash + '">');
                        }
                    }
                });
            }
        });
    </script>

    <script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
        <script src="<?= base_url('assets/js/dropzone.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/select2.min.js') ?>"></script>
    <!-- 2. Bootstrap Bundle (includes Popper) -->
</head>

<body>

    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top d-flex align-items-center">

        <div class="d-flex align-items-center justify-content-between">
            <a href="<?= site_url('dashboard') ?>" class="logo d-flex align-items-center gap-2">
                <div class="d-flex align-items-center justify-content-center rounded"
                    style="width:36px;height:36px;background:linear-gradient(135deg,#0d6efd,#0dcaf0);">
                    <i class="bi bi-shield-check text-white" style="font-size:1.1rem;"></i>
                </div>
                <span class="d-none d-lg-block fw-bold text-dark" style="font-size:1.05rem;letter-spacing:.3px;">
                    TACTIC
                    <div class="small text-muted" style="font-size:10px;line-height:1;">TOKA APPROVAL &amp; COMMISSIONING TECHNICAL INSPECTION CENTER</div>
                </span>
            </a>
            <i class="bi bi-list toggle-sidebar-btn ms-3"></i>
        </div>

        <nav class="header-nav ms-auto">
            <ul class="d-flex align-items-center">

                <!-- Notifikasi pending approval -->
                <?php
                $notif_count = 0;
                $notif_items = [];
                $_roles_raw_h = $this->session->userdata('roles');
                $roles_arr    = (is_array($_roles_raw_h) && !empty($_roles_raw_h))
                    ? array_map('intval', $_roles_raw_h)
                    : [(int)$_sess_role];

                if (in_array(1, $roles_arr) || in_array(2, $roles_arr)) {
                    $c = $this->db->where('status', 'submitted')->count_all_results('pengajuan_uji');
                    if ($c) {
                        $notif_count += $c;
                        $notif_items[] = ['label' => 'Menunggu Review Manager', 'count' => $c, 'url' => 'approval/manager', 'color' => 'warning'];
                    }
                }
                if (in_array(1, $roles_arr) || in_array(4, $roles_arr)) {
                    $c = $this->db->where('status', 'approved_manager')->count_all_results('pengajuan_uji');
                    if ($c) {
                        $notif_count += $c;
                        $notif_items[] = ['label' => 'Review Admin OHS', 'count' => $c, 'url' => 'approval/admin_ohs', 'color' => 'info'];
                    }
                    $c = $this->db->where('status', 'review_ohs')->count_all_results('pengajuan_uji');
                    if ($c) {
                        $notif_count += $c;
                        $notif_items[] = ['label' => 'Hasil Inspeksi Perlu Review', 'count' => $c, 'url' => 'approval/admin_hasil', 'color' => 'warning'];
                    }
                    $c = $this->db->where('status', 'approved_ohs')->count_all_results('pengajuan_uji');
                    if ($c) {
                        $notif_count += $c;
                        $notif_items[] = ['label' => 'Review OHS Superintendent', 'count' => $c, 'url' => 'approval/ohs_supt', 'color' => 'info'];
                    }
                }
                if (in_array(1, $roles_arr) || in_array(5, $roles_arr)) {
                    $c = $this->db->where('status', 'approved_ktt')->count_all_results('pengajuan_uji');
                    if ($c) {
                        $notif_count += $c;
                        $notif_items[] = ['label' => 'Approval KTT', 'count' => $c, 'url' => 'approval/ktt', 'color' => 'dark'];
                    }
                }
                ?>
                <li class="nav-item dropdown">
                    <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-bell"></i>
                        <?php if ($notif_count > 0): ?>
                    <script src="<?= base_url('assets/js/dropzone.min.js') ?>"></script>
                        <?php endif; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
                        <li class="dropdown-header">
                            <?= $notif_count > 0 ? $notif_count . ' item memerlukan tindakan Anda' : 'Tidak ada notifikasi baru' ?>
                        </li>
                        <?php if (empty($notif_items)): ?>
                            <li>
                                <div class="text-center text-muted py-3 small"><i class="bi bi-check2-all d-block fs-4 mb-1 text-success"></i>Semua sudah diproses</div>
                            </li>
                        <?php else: ?>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <?php foreach ($notif_items as $n): ?>
                                <li class="notification-item">
                                    <i class="bi bi-clock text-<?= $n['color'] ?>"></i>
                                    <div>
                                        <h4><?= $n['label'] ?></h4>
                                        <p><?= $n['count'] ?> pengajuan menunggu</p>
                                    </div>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                            <?php endforeach; ?>
                            <li class="dropdown-footer"><a href="<?= site_url('pengajuan') ?>">Lihat semua pengajuan</a></li>
                        <?php endif; ?>
                    </ul>
                </li>

                <!-- Profile -->
                <li class="nav-item dropdown pe-3">
                    <a class="nav-link nav-profile d-flex align-items-center pe-0 gap-2" href="#" data-bs-toggle="dropdown">
                        <?php if ($_sess_foto): ?>
                            <img src="<?= base_url($_sess_foto) ?>?v=<?= time() ?>" alt="Foto"
                                class="rounded-circle" style="width:36px;height:36px;object-fit:cover;">
                        <?php else: ?>
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold"
                                style="width:36px;height:36px;font-size:14px;">
                                <?= strtoupper(substr($_sess_nama ?? 'U', 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                        <div class="d-none d-md-block">
                            <div class="fw-semibold lh-1" style="font-size:13px;"><?= html_escape($_sess_nama) ?></div>
                            <div class="text-muted lh-1 mt-1" style="font-size:11px;"><?= $primary_label ?></div>
                        </div>
                        <i class="bi bi-chevron-down d-none d-md-block ms-1" style="font-size:10px;"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                        <li class="dropdown-header pb-2">
                            <div class="fw-bold"><?= html_escape($_sess_nama) ?></div>
                            <div class="mt-1 d-flex flex-wrap gap-1">
                                <?php foreach ($_roles_names as $rn): ?>
                                    <span class="badge bg-light text-dark border" style="font-size:10px;"><?= html_escape($rn) ?></span>
                                <?php endforeach; ?>
                            </div>
                            <?php if ($_sess_jabatan): ?>
                                <div class="text-muted small mt-1"><?= html_escape($_sess_jabatan) ?></div>
                            <?php endif; ?>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="<?= site_url('profil') ?>">
                                <i class="bi bi-person-circle text-primary"></i><span>Profil Saya</span>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="<?= site_url('auth/logout') ?>">
                                <i class="bi bi-box-arrow-right text-danger"></i><span>Keluar</span>
                            </a>
                        </li>
                    </ul>
                </li>

            </ul>
        </nav>
    </header>