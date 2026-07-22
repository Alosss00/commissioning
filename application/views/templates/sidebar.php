<?php
// ================================================================
// Roles: 1=Super Admin, 2=KTT, 3=OHS Supt, 4=Inspektor,
//        5=Admin OHS, 6=Dept Manager, 7=Admin Departemen
// ================================================================

$_role_int  = (int) $this->session->userdata('role');
$_roles_raw = $this->session->userdata('roles');

if (is_array($_roles_raw) && !empty($_roles_raw)) {
    $_roles = array_map('intval', $_roles_raw);
} elseif ($_role_int > 0) {
    $_roles = [$_role_int];
} else {
    $_roles = [];
}

function has_role($id, $roles)
{
    return !empty($roles) && in_array((int)$id, $roles, true);
}

$isAdmin     = has_role(1, $_roles);
$isKTT       = has_role(2, $_roles);
$isOHSSupt   = has_role(3, $_roles);
$isInspektor = has_role(4, $_roles);
$isAdminOHS  = has_role(5, $_roles);
$isDeptMgr   = has_role(6, $_roles);
$isAdminDept = has_role(7, $_roles);
$isPlanner   = has_role(8, $_roles);

// ================================================================
// BADGE — hitung pengajuan pending per status
// ================================================================
function pending_badge($status_arr)
{
    $CI = &get_instance();
    $db = &$CI->db;
    
    $roles_raw = $CI->session->userdata('roles');
    $role_int  = (int) $CI->session->userdata('role');
    $roles = is_array($roles_raw) ? array_map('intval', $roles_raw) : ($role_int > 0 ? [$role_int] : []);
    $departemen = $CI->session->userdata('departemen');

    $db->from('pengajuan_uji pu');
    $db->where_in('pu.status', (array)$status_arr);

    if (!in_array(1, $roles, true) && !empty($departemen)) {
        $db->join('kendaraan k', 'k.id_kendaraan = pu.id_kendaraan', 'left');
        $db->where('k.perusahaan', $departemen);
        if (in_array(7, $roles, true)) {
            $db->where('pu.id_pemohon', (int)$CI->session->userdata('id_user'));
        }
    }

    $cnt = $db->count_all_results();

    return $cnt > 0
        ? '<span class="badge bg-danger rounded-pill ms-auto" style="font-size:10px;">' . $cnt . '</span>'
        : '';
}

// ================================================================
// ACTIVE MENU
// ================================================================
$current_uri = trim(uri_string(), '/');

function is_active($menu)
{
    $CI = &get_instance();
    return $CI->uri->segment(1) === $menu ? 'active' : '';
}

function is_active_exact($uri)
{
    $CI = &get_instance();
    return trim(uri_string(), '/') === trim($uri, '/') ? 'active' : '';
}

function is_active_custom($menu)
{
    $uri = trim(uri_string(), '/');

    if (
        strpos($uri, 'inspeksi') === 0 ||
        strpos($uri, 'checklist/form') === 0
    ) {
        return $menu === 'inspeksi' ? 'active' : '';
    }

    if (strpos($uri, 'checklist') === 0) {
        return $menu === 'checklist' ? 'active' : '';
    }

    if (strpos($uri, $menu) === 0) {
        return 'active';
    }

    return '';
}

// Collapse approval terbuka jika sedang di halaman approval
$approval_open = strpos($current_uri, 'approval') === 0 ? '' : 'collapsed';

?>

<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">

        <!-- Dashboard -->
        <li class="nav-item">
            <a class="nav-link <?= is_active_exact('dashboard') ?>" href="<?= site_url('dashboard') ?>">
                <i class="bi bi-grid"></i><span>Dashboard</span>
            </a>
        </li>

        <!-- ================= PENGAJUAN ================= -->
        <li class="nav-heading">Pengajuan</li>

        <?php if ($isAdmin || $isAdminDept): ?>
            <li class="nav-item">
                <a class="nav-link <?= is_active_exact('pengajuan/create') ?>" href="<?= site_url('pengajuan/create') ?>">
                    <i class="bi bi-file-earmark-plus"></i><span>Buat Pengajuan</span>
                </a>
            </li>
        <?php endif; ?>

        <li class="nav-item">
            <a class="nav-link <?= is_active_exact('pengajuan') ?>" href="<?= site_url('pengajuan') ?>">
                <i class="bi bi-clipboard-check"></i><span>Daftar Pengajuan</span>
            </a>
        </li>

        <!-- ================= APPROVAL ================= -->
        <?php if ($isAdmin || $isKTT || $isOHSSupt || $isAdminOHS || $isDeptMgr): ?>

            <li class="nav-heading">Approval</li>
            <li class="nav-item">
                <a class="nav-link <?= $approval_open ?>"
                    data-bs-target="#approval-nav"
                    data-bs-toggle="collapse"
                    href="#">
                    <i class="bi bi-check2-circle"></i>
                    <span>Approval</span>
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>

                <ul id="approval-nav"
                    class="nav-content collapse <?= $approval_open === '' ? 'show' : '' ?>"
                    data-bs-parent="#sidebar-nav">

                    <?php if ($isAdmin || $isDeptMgr): ?>
                        <li>
                            <a href="<?= site_url('approval/manager') ?>" class="<?= is_active_exact('approval/manager') ?>">
                                <i class="bi bi-circle"></i>
                                <span>Review Dept Manager</span>
                                <?= pending_badge(['pengajuan_baru', 'pengajuan_ulang', 'ditolak_admin_ohs']) ?>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if ($isAdmin || $isAdminOHS): ?>
                        <li>
                            <a href="<?= site_url('approval/admin_ohs') ?>" class="<?= is_active_exact('approval/admin_ohs') ?>">
                                <i class="bi bi-circle"></i>
                                <span>Review Admin OHS</span>
                                <?= pending_badge(['diterima_manager']) ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?= site_url('approval/stiker') ?>" class="<?= is_active_exact('approval/stiker') ?>">
                                <i class="bi bi-circle"></i>
                                <span>Penerbitan Stiker</span>
                                <?= pending_badge(['acc_ktt']) ?>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if ($isAdmin || $isOHSSupt): ?>
                        <li>
                            <a href="<?= site_url('approval/ohs_supt') ?>" class="<?= is_active_exact('approval/ohs_supt') ?>">
                                <i class="bi bi-circle"></i>
                                <span>OHS Superintendent</span>
                                <?= pending_badge(['lulus_inspeksi', 'diterima_admin_ohs']) ?>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if ($isAdmin || $isKTT): ?>
                        <li>
                            <a href="<?= site_url('approval/ktt') ?>" class="<?= is_active_exact('approval/ktt') ?>">
                                <i class="bi bi-circle"></i>
                                <span>Approval KTT</span>
                                <?= pending_badge(['diterima_ohs_supt', 'menunggu_ktt_2']) ?>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if ($isAdmin || $isKTT || $isOHSSupt || $isInspektor || $isAdminOHS): ?>
                        <li>
                            <a href="<?= site_url('approval/pencabutan') ?>" class="<?= is_active_exact('approval/pencabutan') ?>">
                                <i class="bi bi-circle"></i>
                                <span>Pencabutan Stiker</span>
                            </a>
                        </li>
                    <?php endif; ?>

                </ul>
            </li>
        <?php endif; ?>

        <!-- ================= UJI KELAYAKAN ================= -->
        <?php if ($isAdmin || $isAdminOHS || $isInspektor || $isPlanner): ?>

            <li class="nav-heading">Uji Kelayakan</li>

            <?php if ($isAdmin || $isAdminOHS || $isPlanner): ?>
                <li class="nav-item">
                    <a class="nav-link <?= is_active('jadwal') ?>" href="<?= site_url('jadwal') ?>">
                        <i class="bi bi-calendar-check"></i>
                        <span>Jadwal Inspeksi</span>
                        <?= pending_badge(['dijadwalkan']) ?>
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($isAdmin || $isInspektor): ?>
                <li class="nav-item">
                    <a class="nav-link <?= is_active_custom('inspeksi') ?>" href="<?= site_url('inspeksi') ?>">
                        <i class="bi bi-tools"></i>
                        <span>Form Inspeksi</span>
                        <?= pending_badge(['dijadwalkan', 'inspeksi_ulang']) ?>
                    </a>
                </li>

                <!-- Verifikasi Fisik Perbaikan — status masuk: siap_verifikasi -->
                <li class="nav-item">
                    <a class="nav-link <?= is_active_exact('approval/verif_perbaikan') ?>" href="<?= site_url('approval/verif_perbaikan') ?>">
                        <i class="bi bi-patch-check"></i>
                        <span>Verifikasi Perbaikan</span>
                        <?= pending_badge(['siap_verifikasi']) ?>
                    </a>
                </li>
            <?php endif; ?>

        <?php endif; ?>

        <!-- ================= MASTER DATA ================= -->
        <?php if ($isAdmin || $isAdminOHS || $isPlanner): ?>

            <li class="nav-heading">Master Data</li>

            <li class="nav-item">
                <a class="nav-link <?= is_active('kendaraan') ?>" href="<?= site_url('kendaraan') ?>">
                    <i class="bi bi-truck"></i><span>Data Kendaraan</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= is_active_exact('mekanik_master') ?>" href="<?= site_url('mekanik_master') ?>">
                    <i class="bi bi-person-gear"></i><span>Master Mekanik</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= is_active('TipeKendaraan') ?>" href="<?= site_url('tipekendaraan') ?>">
                    <i class="bi bi-car-front-fill"></i><span>Tipe Kendaraan</span>
                </a>
            </li>

            <?php if ($isAdmin): ?>
                <li class="nav-item">
                    <a class="nav-link <?= is_active_custom('checklist') ?>" href="<?= site_url('checklist/template') ?>">
                        <i class="bi bi-card-checklist"></i><span>Template Checklist</span>
                    </a>
                </li>
            <?php endif; ?>

        <?php endif; ?>

        <!-- ================= ADMIN ================= -->
        <?php if ($isAdmin): ?>

            <li class="nav-heading">Administrasi</li>

            <li class="nav-item">
                <a class="nav-link <?= is_active('usermanagement') ?>" href="<?= site_url('usermanagement') ?>">
                    <i class="bi bi-people"></i><span>Manajemen User</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= is_active('hakakses') ?>" href="<?= site_url('hakakses') ?>">
                    <i class="bi bi-shield-lock"></i><span>Hak Akses</span>
                </a>
            </li>

        <?php endif; ?>

    </ul>
</aside>