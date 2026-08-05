<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Helper terpusat untuk Pengajuan, Audit Log & Status
 */

if (!function_exists('time_ago')) {
    function time_ago($datetime)
    {
        if (empty($datetime)) return '-';
        $diff = time() - strtotime($datetime);
        if ($diff < 60)        return $diff . ' dtk lalu';
        if ($diff < 3600)      return floor($diff / 60) . ' mnt lalu';
        if ($diff < 86400)     return floor($diff / 3600) . ' jam lalu';
        if ($diff < 2592000)   return floor($diff / 86400) . ' hari lalu';
        return date('d M Y', strtotime($datetime));
    }
}

if (!function_exists('aksi_color')) {
    function aksi_color($aksi)
    {
        if (strpos($aksi, 'reject') !== false || strpos($aksi, 'tolak') !== false || strpos($aksi, 'batal') !== false) return 'danger';
        if (strpos($aksi, 'approve') !== false || strpos($aksi, 'sticker') !== false || strpos($aksi, 'acc') !== false) return 'success';
        if (strpos($aksi, 'submit') !== false || strpos($aksi, 'buat') !== false || strpos($aksi, 'resubmit') !== false) return 'primary';
        return 'secondary';
    }
}

if (!function_exists('aksi_label')) {
    function aksi_label($aksi, $nama, $id_ref, $tabel = '')
    {
        $CI = &get_instance();
        $no = !empty($id_ref) ? '#PU-' . str_pad($id_ref, 4, '0', STR_PAD_LEFT) : '';
        
        // Format untuk Pengajuan Unit
        if ($tabel === 'pengajuan_uji' || strpos($aksi, 'pengajuan') !== false || strpos($aksi, 'inspeksi') !== false || strpos($aksi, 'manager') !== false || strpos($aksi, 'ohs') !== false || strpos($aksi, 'ktt') !== false || strpos($aksi, 'jadwal') !== false) {
            $pengajuan = $CI->db->select('k.nomor_unit')
                ->from('pengajuan_uji pu')
                ->join('kendaraan k', 'k.id_kendaraan = pu.id_kendaraan', 'left')
                ->where('pu.id_pengajuan', $id_ref)
                ->get()->row();
            if ($pengajuan && !empty($pengajuan->nomor_unit)) {
                $no = '#' . $pengajuan->nomor_unit;
            }
        } 
        // Format untuk Aktivasi User
        elseif ($tabel === 'users' || $aksi === 'Aktifkan Akun' || $aksi === 'Nonaktifkan Akun') {
            $target = $CI->db->select('u.nama, GROUP_CONCAT(r.nama_role SEPARATOR ", ") AS roles')
                ->from('users u')
                ->join('user_roles ur', 'ur.id_user = u.id_user', 'left')
                ->join('roles r', 'r.id_role = ur.id_role', 'left')
                ->where('u.id_user', $id_ref)
                ->group_by('u.id_user')
                ->get()->row();
            if ($target) {
                $role_str = !empty($target->roles) ? strtolower($target->roles) : 'user';
                $no = '#' . $target->nama . ' (' . $role_str . ')';
            }
        }

        $map = [
            'buat_pengajuan'          => "<strong>$nama</strong> membuat pengajuan baru <a href='" . site_url('pengajuan') . "' class='fw-bold text-dark'>$no</a>",
            'resubmit_pengajuan'      => "<strong>$nama</strong> mengajukan ulang <a href='" . site_url('pengajuan') . "' class='fw-bold text-dark'>$no</a>",
            'cancel_pengajuan'        => "<strong>$nama</strong> membatalkan pengajuan <a href='" . site_url('pengajuan') . "' class='fw-bold text-dark'>$no</a>",
            'approve_manager'         => "<strong>$nama</strong> (Manager) menyetujui <a href='" . site_url('pengajuan') . "' class='fw-bold text-dark'>$no</a>",
            'reject_manager'          => "<strong>$nama</strong> (Manager) menolak <a href='" . site_url('pengajuan') . "' class='fw-bold text-dark'>$no</a>",
            'approve_admin_ohs'       => "<strong>$nama</strong> (Admin OHS) menyetujui dokumen <a href='" . site_url('pengajuan') . "' class='fw-bold text-dark'>$no</a>",
            'buat_jadwal'             => "<strong>$nama</strong> membuat jadwal inspeksi untuk <a href='" . site_url('jadwal') . "' class='fw-bold text-dark'>$no</a>",
            'submit_inspeksi'         => "<strong>$nama</strong> mengunggah hasil inspeksi untuk <a href='" . site_url('pengajuan') . "' class='fw-bold text-dark'>$no</a>",
            'approve_admin_ohs_hasil' => "<strong>$nama</strong> (Admin OHS) menyetujui hasil inspeksi <a href='" . site_url('pengajuan') . "' class='fw-bold text-dark'>$no</a>",
            'reject_admin_ohs_hasil'  => "<strong>$nama</strong> (Admin OHS) menolak hasil inspeksi <a href='" . site_url('pengajuan') . "' class='fw-bold text-dark'>$no</a>",
            'approve_ohs_supt'        => "<strong>$nama</strong> (OHS Supt.) menyetujui <a href='" . site_url('pengajuan') . "' class='fw-bold text-dark'>$no</a>",
            'reject_ohs_supt'         => "<strong>$nama</strong> (OHS Supt.) mengembalikan ke Admin OHS <a href='" . site_url('pengajuan') . "' class='fw-bold text-dark'>$no</a>",
            'approve_ktt'             => "<strong>$nama</strong> (KTT) memberikan approval final <a href='" . site_url('pengajuan') . "' class='fw-bold text-dark'>$no</a>",
            'reject_ktt'              => "<strong>$nama</strong> (KTT) mengembalikan ke Admin OHS <a href='" . site_url('pengajuan') . "' class='fw-bold text-dark'>$no</a>",
            'login'                   => "<strong>$nama</strong> berhasil login ke sistem",
            'logout'                  => "<strong>$nama</strong> keluar dari sistem (logout)",
        ];
        
        if ($aksi === 'Aktifkan Akun' || $aksi === 'Nonaktifkan Akun') {
            return "<strong>$nama</strong> melakukan aksi $aksi <span class='fw-bold text-dark'>$no</span>";
        }
        
        return isset($map[$aksi]) ? $map[$aksi] : "<strong>$nama</strong> melakukan aksi <em>" . html_escape($aksi) . "</em> " . ($no ? "<span class='fw-bold text-dark'>$no</span>" : "");
    }
}

if (!function_exists('badge_status')) {
    function badge_status($status)
    {
        $map = [
            'pengajuan_baru'      => ['primary',          'Pengajuan Baru'],
            'pengajuan_ulang'     => ['warning text-dark', 'Pengajuan Ulang'],
            'diterima_manager'    => ['info text-dark',    'Diterima Manager'],
            'dijadwalkan'         => ['primary',           'Terjadwal'],
            'selesai_inspeksi'    => ['info text-dark',    'Selesai Inspeksi'],
            'diterima_admin_ohs'  => ['info text-dark',    'Diterima Admin OHS'],
            'diterima_ohs_supt'   => ['info text-dark',    'Diterima OHS Supt'],
            'acc_ktt'             => ['success',           'ACC KTT'],
            'stiker_keluar'       => ['success',           'Stiker Keluar'],
            'ditolak_manager'     => ['danger',            'Ditolak Manager'],
            'ditolak_admin_ohs'   => ['danger',            'Ditolak Admin OHS'],
            'ditolak_ohs_supt'    => ['danger',            'Ditolak OHS Supt'],
            'ditolak_ktt'         => ['danger',            'Ditolak KTT'],
            'rejected'            => ['danger',            'Ditolak'],
        ];
        $c = isset($map[$status]) ? $map[$status] : ['secondary', $status];
        return '<span class="badge bg-' . $c[0] . '">' . $c[1] . '</span>';
    }
}

if (!function_exists('level_label')) {
    function level_label($level)
    {
        $map = [
            'pengajuan_baru'     => ['warning text-dark', 'Review Manager'],
            'pengajuan_ulang'    => ['warning text-dark', 'Review Manager'],
            'ditolak_admin_ohs'  => ['warning text-dark', 'Review Manager'],
            'diterima_manager'   => ['info text-dark',    'Review Admin OHS'],
            'selesai_inspeksi'   => ['info text-dark',    'Review Hasil'],
            'acc_ktt'            => ['dark',              'Release Stiker'],
            'diterima_admin_ohs' => ['info text-dark',    'OHS Superintendent'],
            'ditolak_ohs_supt'   => ['info text-dark',    'OHS Superintendent'],
            'diterima_ohs_supt'  => ['dark',              'KTT'],
            'dijadwalkan'        => ['primary',           'Mekanik'],
            'ditolak_manager'    => ['danger',            'Ditolak'],
        ];
        $c = isset($map[$level]) ? $map[$level] : ['secondary', $level];
        return '<span class="badge bg-' . $c[0] . '" style="font-size:10px;">' . $c[1] . '</span>';
    }
}

if (!function_exists('approval_route')) {
    function approval_route($status)
    {
        $map = [
            'pengajuan_baru'     => 'approval/manager',
            'pengajuan_ulang'    => 'approval/manager',
            'ditolak_admin_ohs'  => 'approval/manager',
            'diterima_manager'   => 'approval/admin_ohs',
            'selesai_inspeksi'   => 'approval/admin_hasil',
            'acc_ktt'            => 'approval/admin_ohs',
            'diterima_admin_ohs' => 'approval/ohs_supt',
            'diterima_ohs_supt'  => 'approval/ktt',
            'dijadwalkan'        => 'jadwal',
        ];
        return isset($map[$status]) ? $map[$status] : 'pengajuan';
    }
}

if (!function_exists('badge_tipe_akses')) {
    function badge_tipe_akses($tipe, $extra_style = '')
    {
        if (empty($tipe)) return '<span class="text-muted small">—</span>';
        $key = strtolower(trim($tipe));
        if ($key === 'mining' || (strpos($key, 'mining') !== false && strpos($key, 'non') === false)) {
            $bg = 'bg-danger';
            $label = 'MINING ACCESS';
        } elseif (strpos($key, 'non') !== false || $key === 'non_mining') {
            $bg = 'bg-success';
            $label = 'NON MINING';
        } elseif (strpos($key, 'underground') !== false) {
            $bg = 'bg-secondary';
            $label = 'UNDERGROUND';
        } else {
            $bg = 'bg-secondary';
            $label = strtoupper($tipe);
        }
        $style = $extra_style ? ' style="' . $extra_style . '"' : '';
        return '<span class="badge ' . $bg . ' text-white"' . $style . '>' . html_escape($label) . '</span>';
    }
}
