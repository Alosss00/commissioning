<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Helper Terpusat Pengajuan, Audit Log & Formatting Status
 * 
 * Menyediakan fungsi-fungsi pembantu global untuk:
 * 1. Format durasi waktu relatif (time_ago)
 * 2. Format warna badge & label aksi audit log (aksi_color, aksi_label)
 * 3. Render badge HTML status pengajuan, role, dan tipe akses kendaraan.
 */

if (!function_exists('time_ago')) {
    /**
     * Mengubah tanggal string/datetime menjadi format waktu relatif (misal: "5 mnt lalu").
     * 
     * @param string|null $datetime String tanggal (Y-m-d H:i:s)
     * @return string Label waktu relatif
     */
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
    /**
     * Menentukan kelas warna Bootstrap berdasarkan nama aksi log audit.
     * 
     * @param string $aksi Kode/Nama aksi audit
     * @return string Nama kelas status Bootstrap (danger, success, primary, secondary)
     */
    function aksi_color($aksi)
    {
        if (strpos($aksi, 'reject') !== false || strpos($aksi, 'tolak') !== false || strpos($aksi, 'batal') !== false) return 'danger';
        if (strpos($aksi, 'approve') !== false || strpos($aksi, 'sticker') !== false || strpos($aksi, 'acc') !== false) return 'success';
        if (strpos($aksi, 'submit') !== false || strpos($aksi, 'buat') !== false || strpos($aksi, 'resubmit') !== false) return 'primary';
        return 'secondary';
    }
}

if (!function_exists('aksi_label')) {
    /**
     * Menyusun teks deskriptif log audit beserta link referensi.
     * Menggunakan static cache internal untuk mengeliminasi query N+1 saat merender daftar log dalam jumlah banyak.
     * 
     * @param string $aksi Kode aksi audit
     * @param string $nama Nama pelaku aksi
     * @param int|string $id_ref ID referensi entitas (id_pengajuan atau id_user)
     * @param string $tabel Nama tabel entitas (opsional)
     * @return string Format HTML teks deskripsi log audit
     */
    function aksi_label($aksi, $nama, $id_ref, $tabel = '')
    {
        $CI = &get_instance();
        $no = !empty($id_ref) ? '#PU-' . str_pad($id_ref, 4, '0', STR_PAD_LEFT) : '';

        // Internal static cache untuk mencegah N+1 query berulang saat rendering daftar log audit
        static $unit_cache = [];
        static $user_cache = [];
        
        // Format untuk Pengajuan Unit
        if ($tabel === 'pengajuan_uji' || strpos($aksi, 'pengajuan') !== false || strpos($aksi, 'inspeksi') !== false || strpos($aksi, 'manager') !== false || strpos($aksi, 'ohs') !== false || strpos($aksi, 'ktt') !== false || strpos($aksi, 'jadwal') !== false) {
            if ($id_ref > 0) {
                if (!array_key_exists($id_ref, $unit_cache)) {
                    $pengajuan = $CI->db->select('k.nomor_unit')
                        ->from('pengajuan_uji pu')
                        ->join('kendaraan k', 'k.id_kendaraan = pu.id_kendaraan', 'left')
                        ->where('pu.id_pengajuan', $id_ref)
                        ->get()->row();
                    $unit_cache[$id_ref] = ($pengajuan && !empty($pengajuan->nomor_unit)) ? '#' . $pengajuan->nomor_unit : '';
                }
                if (!empty($unit_cache[$id_ref])) {
                    $no = $unit_cache[$id_ref];
                }
            }
        } 
        // Format untuk Aktivasi User
        elseif ($tabel === 'users' || $aksi === 'Aktifkan Akun' || $aksi === 'Nonaktifkan Akun') {
            if ($id_ref > 0) {
                if (!array_key_exists($id_ref, $user_cache)) {
                    $target = $CI->db->select('u.nama, GROUP_CONCAT(r.nama_role SEPARATOR ", ") AS roles')
                        ->from('users u')
                        ->join('user_roles ur', 'ur.id_user = u.id_user', 'left')
                        ->join('roles r', 'r.id_role = ur.id_role', 'left')
                        ->where('u.id_user', $id_ref)
                        ->group_by('u.id_user')
                        ->get()->row();
                    if ($target) {
                        $role_str = !empty($target->roles) ? strtolower($target->roles) : 'user';
                        $user_cache[$id_ref] = '#' . $target->nama . ' (' . $role_str . ')';
                    } else {
                        $user_cache[$id_ref] = '';
                    }
                }
                if (!empty($user_cache[$id_ref])) {
                    $no = $user_cache[$id_ref];
                }
            }
        }

        // Pemetaan template teks deskripsi berdasarkan jenis aksi
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
    /**
     * Menghasilkan elemen HTML badge Bootstrap untuk status pengajuan uji.
     * 
     * @param string $status Kode status pengajuan
     * @return string HTML Badge
     */
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
        $c = isset($map[$status]) ? $map[$status] : ['secondary', html_escape($status)];
        return '<span class="badge bg-' . $c[0] . '">' . $c[1] . '</span>';
    }
}

if (!function_exists('level_label')) {
    /**
     * Menghasilkan badge level persetujuan posisi alur berkas.
     * 
     * @param string $level Kode level status
     * @return string HTML Badge
     */
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
        $c = isset($map[$level]) ? $map[$level] : ['secondary', html_escape($level)];
        return '<span class="badge bg-' . $c[0] . '" style="font-size:10px;">' . $c[1] . '</span>';
    }
}

if (!function_exists('approval_route')) {
    /**
     * Mendapatkan URL segmen route approval berdasarkan status pengajuan.
     * 
     * @param string $status Kode status pengajuan
     * @return string Path URL tujuan
     */
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
    /**
     * Menghasilkan badge HTML tipe akses kendaraan (MINING / NON MINING / UNDERGROUND).
     * 
     * @param string|null $tipe Nama/Kode tipe akses
     * @param string $extra_style Style CSS inline tambahan
     * @return string HTML Badge Tipe Akses
     */
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
