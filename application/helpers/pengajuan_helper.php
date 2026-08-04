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
    function aksi_label($aksi, $nama, $id_ref)
    {
        $no = !empty($id_ref) ? '#PU-' . str_pad($id_ref, 4, '0', STR_PAD_LEFT) : '';
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
        return isset($map[$aksi]) ? $map[$aksi] : "<strong>$nama</strong> melakukan aksi <em>" . html_escape($aksi) . "</em> " . ($no ? "<a href='" . site_url('pengajuan') . "' class='fw-bold text-dark'>$no</a>" : "");
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
