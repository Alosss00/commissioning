<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Sikuk_email Library
 * ============================================================
 * Library terpusat untuk semua notifikasi email di sistem TACTIC.
 * Setiap method mewakili satu kejadian dalam alur approval.
 *
 * CARA PAKAI di controller:
 *   $this->load->library('sikuk_email');
 *   $this->sikuk_email->notif_pengajuan_dibuat($id_pengajuan);
 *
 * KONFIGURASI SMTP:
 *   Set di application/config/config.php — lihat bagian bawah file ini.
 * ============================================================
 *
 * PETA NOTIFIKASI (sesuai dokumen template):
 *
 * Trigger                    | Method                        | Penerima
 * ---------------------------|-------------------------------|---------------------------
 * Pengajuan dibuat           | notif_pengajuan_dibuat()      | Admin Dept (konfirmasi) + Dept Manager (tugas)
 * Dept Manager approve       | notif_diterima_manager()      | Admin OHS (tugas verifikasi)
 * Dept Manager reject        | notif_ditolak_manager()       | Admin Dept (revisi)
 * Admin OHS approve → jadwal | notif_dijadwalkan()           | Admin Dept (progress)
 * Admin OHS reject           | notif_ditolak_admin_ohs()     | Dept Manager (dikembalikan)
 * Jadwal dibuat              | notif_jadwal_mekanik()        | Mekanik (order inspeksi)
 * Mekanik submit inspeksi    | notif_selesai_inspeksi()      | Admin OHS (review hasil)
 * Admin OHS hasil approve    | notif_diterima_admin_ohs()    | OHS Supt (pengecekan akhir)
 * Admin OHS hasil reject     | notif_ditolak_admin_ohs()     | Dept Manager (dikembalikan)
 * OHS Supt approve           | notif_diterima_ohs_supt()     | KTT (final approval)
 * OHS Supt reject            | notif_ditolak_ohs_supt()      | Admin OHS (revisi)
 * KTT approve                | notif_acc_ktt()               | Admin OHS (terbitkan stiker)
 * KTT reject                 | notif_ditolak_ktt()           | Admin OHS (revisi)
 * Stiker keluar              | notif_stiker_keluar()         | Admin Dept (ambil stiker)
 * ============================================================
 */
class Sikuk_email
{
    /** @var CI_Controller */
    protected $CI;

    /** @var string Email pengirim */
    protected $from_email;

    /** @var string Nama pengirim */
    protected $from_name;

    /** @var string Base URL sistem */
    protected $base_url;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->library('email');
        $this->CI->load->database();

        $this->from_email = $this->CI->config->item('sikuk_email_from');
        $this->from_name  = $this->CI->config->item('sikuk_email_name');
        $this->base_url   = rtrim($this->CI->config->item('base_url'), '/');

        // Inisialisasi konfigurasi SMTP setiap kali library dimuat
        $this->_init_smtp();
    }

    // ============================================================
    // NOTIFIKASI — ADMIN DEPARTEMEN (Pemohon)
    // ============================================================

    /**
     * [A] Pengajuan berhasil dibuat
     * Penerima: Admin Dept (konfirmasi) + Dept Manager (tugas baru)
     */
    public function notif_pengajuan_dibuat($id_pengajuan)
    {
        $p = $this->_get_pengajuan($id_pengajuan);
        if (!$p) return false;

        // 1. Konfirmasi ke Admin Dept / Pemohon
        $this->_send(
            $p->email_pemohon,
            '[Submitted] Pengajuan Commissioning Baru — Unit ' . $p->no_polisi,
            $this->_wrap(
                'Halo ' . $p->nama_pemohon . ',',
                'Pengajuan commissioning untuk unit <strong>' . $p->no_polisi . '</strong> berhasil didaftarkan.',
                'Saat ini data sedang dalam tahap <strong>Pengecekan Berkas Awal</strong> oleh Departemen Manager.'
                    . ' Anda akan menerima notifikasi jika terdapat pembaruan status pengajuan ini.',
                $this->_info_unit($p),
                $this->_btn_link('Buka Pengajuan', $this->base_url . '/pengajuan')
            )
        );

        // 2. Tugas ke Dept Manager
        $managers = $this->_get_users_by_role(6);
        foreach ($managers as $mgr) {
            $this->_send(
                $mgr->email,
                '[Menunggu Approval] Pengecekan Berkas Awal Commissioning — Unit ' . $p->no_polisi,
                $this->_wrap(
                    'Yth. ' . $mgr->nama . ',',
                    'Terdapat <strong>pengajuan commissioning baru</strong> dari Admin Departemen/Kontraktor untuk unit <strong>' . $p->no_polisi . '</strong>.',
                    'Mohon kesediaannya untuk melakukan pengecekan berkas awal. Jika data tidak sesuai, Anda dapat mengembalikan pengajuan ini ke pemohon.',
                    $this->_info_unit($p),
                    $this->_btn_link('Lihat & Proses Pengajuan', $this->base_url . '/approval/manager')
                )
            );
        }
        return true;
    }

    /**
     * [C] Pengajuan ditolak Dept Manager → Admin Dept harus revisi
     */
    public function notif_ditolak_manager($id_pengajuan, $catatan = '')
    {
        $p = $this->_get_pengajuan($id_pengajuan);
        if (!$p) return false;

        return $this->_send(
            $p->email_pemohon,
            '[Revisi Diperlukan] Pengajuan Commissioning Dikembalikan — Unit ' . $p->no_polisi,
            $this->_wrap(
                'Halo ' . $p->nama_pemohon . ',',
                'Terdapat kekurangan atau kesalahan pada pengajuan commissioning unit <strong>' . $p->no_polisi . '</strong>.',
                'Pengajuan Anda <strong>dikembalikan</strong> oleh <strong>Departemen Manager</strong>.'
                    . $this->_catatan_box($catatan),
                'Mohon segera lakukan perbaikan data atau lengkapi persyaratan fisik unit, lalu ajukan kembali melalui sistem.',
                $this->_info_unit($p),
                $this->_btn_link('Perbaiki & Ajukan Ulang', $this->base_url . '/pengajuan')
            )
        );
    }

    /**
     * [B] Update progress — pengajuan bergerak ke tahapan berikutnya
     * Digunakan untuk notif progress ke Admin Dept
     */
    public function notif_progress($id_pengajuan, $nama_tahapan)
    {
        $p = $this->_get_pengajuan($id_pengajuan);
        if (!$p) return false;

        return $this->_send(
            $p->email_pemohon,
            '[Progress] Update Pengajuan Commissioning — Unit ' . $p->no_polisi,
            $this->_wrap(
                'Halo ' . $p->nama_pemohon . ',',
                'Pengajuan commissioning untuk unit <strong>' . $p->no_polisi . '</strong> telah disetujui pada tahap sebelumnya'
                    . ' dan kini berlanjut ke tahap:',
                '<div style="text-align:center;margin:18px 0;">
                    <span style="background:#1a73e8;color:#fff;padding:8px 22px;border-radius:20px;font-size:15px;font-weight:bold;">'
                    . htmlspecialchars($nama_tahapan) . '</span>
                 </div>',
                'Silakan pantau sistem untuk melihat detail progres selengkapnya.',
                $this->_info_unit($p),
                $this->_btn_link('Pantau Status Pengajuan', $this->base_url . '/pengajuan')
            )
        );
    }

    // ============================================================
    // NOTIFIKASI — DEPT MANAGER
    // ============================================================

    /**
     * [B] Pengajuan dikembalikan dari Admin OHS ke Dept Manager
     */
    public function notif_ditolak_admin_ohs_ke_manager($id_pengajuan, $catatan = '')
    {
        $p       = $this->_get_pengajuan($id_pengajuan);
        $managers = $this->_get_users_by_role(6);
        if (!$p) return false;

        foreach ($managers as $mgr) {
            $this->_send(
                $mgr->email,
                '[Dikembalikan] Review Ulang Data Commissioning — Unit ' . $p->no_polisi,
                $this->_wrap(
                    'Yth. ' . $mgr->nama . ',',
                    'Pengajuan unit <strong>' . $p->no_polisi . '</strong> <strong>dikembalikan</strong> oleh Admin OHS karena terdapat'
                        . ' kekurangan dokumen atau ketidaksesuaian standar fisik (hasil inspeksi).',
                    $this->_catatan_box($catatan, 'Catatan Admin OHS'),
                    'Mohon tinjau kembali data ini dan koordinasikan dengan Admin Departemen/Kontraktor untuk perbaikan.',
                    $this->_info_unit($p),
                    $this->_btn_link('Tinjau Pengajuan', $this->base_url . '/approval/manager')
                )
            );
        }
        return true;
    }

    // ============================================================
    // NOTIFIKASI — ADMIN OHS
    // ============================================================

    /**
     * [A] Dept Manager approve → Admin OHS diminta verifikasi dokumen
     */
    public function notif_diterima_manager($id_pengajuan)
    {
        $p        = $this->_get_pengajuan($id_pengajuan);
        $admins   = $this->_get_users_by_role(5);
        if (!$p) return false;

        foreach ($admins as $adm) {
            $this->_send(
                $adm->email,
                '[Tugas Baru] Verifikasi Dokumen Commissioning — Unit ' . $p->no_polisi,
                $this->_wrap(
                    'Halo ' . $adm->nama . ',',
                    'Pengajuan unit <strong>' . $p->no_polisi . '</strong> telah disetujui oleh Dept Manager.',
                    'Mohon lakukan <strong>pengecekan dokumen kelengkapan</strong>. Jika disetujui, silakan <strong>buat jadwal inspeksi</strong> untuk tim Mekanik di dalam sistem.',
                    $this->_info_unit($p),
                    $this->_btn_link('Verifikasi Sekarang', $this->base_url . '/approval/admin_ohs')
                )
            );
        }
        return true;
    }

    /**
     * [B] Mekanik selesai inspeksi → Admin OHS diminta review hasil
     */
    public function notif_selesai_inspeksi($id_pengajuan)
    {
        $p      = $this->_get_pengajuan($id_pengajuan);
        $admins = $this->_get_users_by_role(5);
        if (!$p) return false;

        foreach ($admins as $adm) {
            $this->_send(
                $adm->email,
                '[Review Hasil] Inspeksi Mekanik Selesai — Unit ' . $p->no_polisi,
                $this->_wrap(
                    'Halo ' . $adm->nama . ',',
                    'Tim Mekanik telah selesai melakukan <strong>inspeksi kelayakan</strong> pada unit <strong>' . $p->no_polisi . '</strong>.',
                    'Mohon tinjau ulang hasil pengujian tersebut. Jika ada kekurangan perlengkapan standar, silakan kembalikan data ke Dept Manager.'
                        . ' Jika sesuai, teruskan ke OHS Manager.',
                    $this->_info_unit($p),
                    $this->_btn_link('Tinjau Hasil Inspeksi', $this->base_url . '/approval/admin_hasil')
                )
            );
        }
        return true;
    }

    /**
     * [C] Data dikembalikan dari OHS Supt atau KTT ke Admin OHS
     */
    public function notif_dikembalikan_ke_admin_ohs($id_pengajuan, $dari, $catatan = '')
    {
        $p      = $this->_get_pengajuan($id_pengajuan);
        $admins = $this->_get_users_by_role(5);
        if (!$p) return false;

        foreach ($admins as $adm) {
            $this->_send(
                $adm->email,
                '[Revisi Approval] Data Commissioning Dikembalikan — Unit ' . $p->no_polisi,
                $this->_wrap(
                    'Halo ' . $adm->nama . ',',
                    'Data hasil inspeksi untuk unit <strong>' . $p->no_polisi . '</strong> <strong>dikembalikan</strong>'
                        . ' oleh <strong>' . htmlspecialchars($dari) . '</strong> karena terdapat kesalahan/kekurangan data persetujuan.',
                    $this->_catatan_box($catatan),
                    'Mohon periksa kembali kelengkapan administrasi hasil inspeksi ini sebelum diajukan ulang.',
                    $this->_info_unit($p),
                    $this->_btn_link('Periksa & Ajukan Ulang', $this->base_url . '/approval/admin_hasil')
                )
            );
        }
        return true;
    }

    /**
     * [D] KTT final approve → Admin OHS diminta terbitkan stiker
     */
    public function notif_acc_ktt($id_pengajuan)
    {
        $p      = $this->_get_pengajuan($id_pengajuan);
        $admins = $this->_get_users_by_role(5);
        if (!$p) return false;

        foreach ($admins as $adm) {
            $this->_send(
                $adm->email,
                '[Final Approved] Segera Terbitkan Stiker Akses — Unit ' . $p->no_polisi,
                $this->_wrap(
                    'Halo ' . $adm->nama . ',',
                    'KTT telah memberikan <strong>Final Approval</strong> untuk unit <strong>' . $p->no_polisi . '</strong>.',
                    '<div style="text-align:center;margin:18px 0;">
                        <span style="background:#34a853;color:#fff;padding:10px 28px;border-radius:20px;font-size:16px;font-weight:bold;">
                            ✅ KENDARAAN LAYAK OPERASI
                        </span>
                     </div>',
                    'Silakan proses <strong>penerbitan dan penyerahan Stiker Akses Commissioning</strong> untuk unit tersebut.',
                    $this->_info_unit($p),
                    $this->_btn_link('Terbitkan Stiker Sekarang', $this->base_url . '/approval/stiker')
                )
            );
        }
        return true;
    }

    // ============================================================
    // NOTIFIKASI — MEKANIK
    // ============================================================

    /**
     * [A] Jadwal inspeksi dibuat → Mekanik mendapat order inspeksi
     */
    public function notif_jadwal_mekanik($id_pengajuan, $id_mekanik, $tanggal_uji, $lokasi)
    {
        $p       = $this->_get_pengajuan($id_pengajuan);
        $mekanik = $this->CI->db->where('id_user', $id_mekanik)->get('users')->row();
        if (!$p || !$mekanik || empty($mekanik->email)) return false;

        return $this->_send(
            $mekanik->email,
            '[Order Inspeksi] Pengujian Kelayakan Kendaraan — Unit ' . $p->no_polisi,
            $this->_wrap(
                'Halo ' . $mekanik->nama . ',',
                'Admin OHS telah menjadwalkan <strong>inspeksi kelayakan commissioning</strong> untuk unit <strong>' . $p->no_polisi . '</strong>.',
                '<table style="width:100%;border-collapse:collapse;margin:12px 0;">
                    <tr>
                        <td style="padding:6px 12px;background:#f8f9fa;font-weight:bold;width:140px;border:1px solid #e0e0e0;">Kendaraan</td>
                        <td style="padding:6px 12px;border:1px solid #e0e0e0;">' . htmlspecialchars($p->no_polisi) . ' — ' . htmlspecialchars($p->jenis_kendaraan) . '</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 12px;background:#f8f9fa;font-weight:bold;border:1px solid #e0e0e0;">Jadwal</td>
                        <td style="padding:6px 12px;border:1px solid #e0e0e0;"><strong>' . date('d M Y H:i', strtotime($tanggal_uji)) . ' WIB</strong></td>
                    </tr>
                    <tr>
                        <td style="padding:6px 12px;background:#f8f9fa;font-weight:bold;border:1px solid #e0e0e0;">Lokasi</td>
                        <td style="padding:6px 12px;border:1px solid #e0e0e0;">' . htmlspecialchars($lokasi) . '</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 12px;background:#f8f9fa;font-weight:bold;border:1px solid #e0e0e0;">No. Pengajuan</td>
                        <td style="padding:6px 12px;border:1px solid #e0e0e0;">#PU-' . str_pad($id_pengajuan, 4, '0', STR_PAD_LEFT) . '</td>
                    </tr>
                 </table>',
                'Silakan lakukan pengujian fisik pada kendaraan sesuai dengan format dan standar yang berlaku,'
                    . ' lalu <strong>submit</strong> hasil pengujian Anda melalui sistem.',
                $this->_btn_link('Buka Form Inspeksi', $this->base_url . '/inspeksi')
            )
        );
    }

    // ============================================================
    // NOTIFIKASI — OHS SUPERINTENDENT
    // ============================================================

    /**
     * [A] Admin OHS approve hasil → OHS Supt diminta pengecekan akhir
     */
    public function notif_diterima_admin_ohs($id_pengajuan)
    {
        $p     = $this->_get_pengajuan($id_pengajuan);
        $supte = $this->_get_users_by_role(3);
        if (!$p) return false;

        foreach ($supte as $s) {
            $this->_send(
                $s->email,
                '[Menunggu Approval] Pengecekan Akhir Commissioning — Unit ' . $p->no_polisi,
                $this->_wrap(
                    'Yth. ' . $s->nama . ',',
                    'Data inspeksi kendaraan dan kelengkapan dokumen untuk unit <strong>' . $p->no_polisi . '</strong>'
                        . ' telah diverifikasi oleh Admin OHS.',
                    'Mohon lakukan <strong>pengecekan akhir</strong>. Jika sesuai, silakan berikan persetujuan agar data dapat diteruskan ke KTT.'
                        . ' Jika ada kekurangan, Anda dapat mengembalikannya ke Admin OHS.',
                    $this->_info_unit($p),
                    $this->_btn_link('Lakukan Pengecekan Akhir', $this->base_url . '/approval/ohs_supt')
                )
            );
        }
        return true;
    }

    // ============================================================
    // NOTIFIKASI — KTT
    // ============================================================

    /**
     * [A] OHS Supt approve → KTT diminta final approval
     */
    public function notif_diterima_ohs_supt($id_pengajuan)
    {
        $p    = $this->_get_pengajuan($id_pengajuan);
        $ktts = $this->_get_users_by_role(2);
        if (!$p) return false;

        foreach ($ktts as $ktt) {
            $this->_send(
                $ktt->email,
                '[Menunggu Final Approval] Otorisasi Commissioning — Unit ' . $p->no_polisi,
                $this->_wrap(
                    'Yth. Bapak/Ibu ' . $ktt->nama . ',',
                    'Terlampir data pengajuan commissioning untuk unit <strong>' . $p->no_polisi . '</strong>'
                        . ' yang telah melewati tahapan <strong>inspeksi mekanik</strong> dan disetujui oleh <strong>OHS Manager</strong>.',
                    'Mohon kesediaannya untuk melakukan tinjauan dan memberikan <strong>Final Approval</strong>.'
                        . ' Jika terdapat ketidaksesuaian data, Anda dapat mengembalikan pengajuan ini ke Admin OHS.',
                    $this->_info_unit($p),
                    $this->_btn_link('Berikan Final Approval', $this->base_url . '/approval/ktt')
                )
            );
        }
        return true;
    }

    // ============================================================
    // NOTIFIKASI — STIKER KELUAR (Admin Dept / Pemohon)
    // ============================================================

    /**
     * [D] Stiker berhasil diterbitkan → Admin Dept diberitahu
     */
    public function notif_stiker_keluar($id_pengajuan)
    {
        $p = $this->CI->db
            ->select('pu.id_pengajuan, pu.tipe_pengajuan,
                      u.nama AS nama_pemohon, u.email AS email_pemohon,
                      k.no_polisi, k.jenis_kendaraan, k.merk, k.tipe,
                      sr.nomor_sticker, sr.tanggal_release')
            ->from('pengajuan_uji pu')
            ->join('users u',            'u.id_user = pu.id_pemohon',         'left')
            ->join('kendaraan k',        'k.id_kendaraan = pu.id_kendaraan',  'left')
            ->join('sticker_release sr', 'sr.id_pengajuan = pu.id_pengajuan', 'left')
            ->where('pu.id_pengajuan', $id_pengajuan)
            ->get()->row();

        if (!$p || empty($p->email_pemohon)) return false;

        return $this->_send(
            $p->email_pemohon,
            '[Final Approved] Stiker Akses Commissioning Telah Diterbitkan — Unit ' . $p->no_polisi,
            $this->_wrap(
                'Yth. ' . $p->nama_pemohon . ',',
                'Kami informasikan bahwa proses commissioning untuk kendaraan Anda telah <strong>selesai</strong> dan stiker akses resmi diterbitkan.',
                '<div style="text-align:center;margin:18px 0;">
                    <span style="background:#34a853;color:#fff;padding:10px 28px;border-radius:20px;font-size:16px;font-weight:bold;">
                        ✅ KENDARAAN DINYATAKAN LAYAK OPERASI
                    </span>
                 </div>',
                '<table style="width:100%;border-collapse:collapse;margin:12px 0;">
                    <tr>
                        <td style="padding:6px 12px;background:#f8f9fa;font-weight:bold;width:140px;border:1px solid #e0e0e0;">No. Polisi</td>
                        <td style="padding:6px 12px;border:1px solid #e0e0e0;">' . htmlspecialchars($p->no_polisi) . '</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 12px;background:#f8f9fa;font-weight:bold;border:1px solid #e0e0e0;">Jenis</td>
                        <td style="padding:6px 12px;border:1px solid #e0e0e0;">' . htmlspecialchars($p->jenis_kendaraan) . ' — ' . htmlspecialchars($p->merk) . ' ' . htmlspecialchars($p->tipe) . '</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 12px;background:#f8f9fa;font-weight:bold;border:1px solid #e0e0e0;">No. Stiker</td>
                        <td style="padding:6px 12px;border:1px solid #e0e0e0;font-size:18px;font-weight:bold;color:#1a73e8;">' . htmlspecialchars($p->nomor_sticker ?? '-') . '</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 12px;background:#f8f9fa;font-weight:bold;border:1px solid #e0e0e0;">Tanggal</td>
                        <td style="padding:6px 12px;border:1px solid #e0e0e0;">' . date('d M Y H:i', strtotime($p->tanggal_release ?? 'now')) . ' WIB</td>
                    </tr>
                 </table>',
                'Silakan menghubungi bagian <strong>Admin OHS</strong> untuk pengambilan stiker fisik.',
                $this->_btn_link('Lihat Detail Pengajuan', $this->base_url . '/pengajuan')
            )
        );
    }

    /**
     * Notifikasi Pencabutan Stiker Terkini
     * Mengirim email sesuai kondisi pengaju (Inspektor, OHS Supt, KTT)
     */
    public function notif_stiker_dicabut($id_cabut)
    {
        $c = $this->CI->db
            ->select('ps.*, sr.nomor_sticker, pu.id_pengajuan, pu.email_pemohon, pu.id_pemohon AS id_admin_dept,
                      k.no_polisi, k.jenis_kendaraan, k.merk, k.tipe, k.perusahaan,
                      u_pem.nama AS nama_pengaju')
            ->from('pencabutan_stiker ps')
            ->join('sticker_release sr', 'sr.id_sticker = ps.id_sticker',     'left')
            ->join('pengajuan_uji pu',   'pu.id_pengajuan = ps.id_pengajuan', 'left')
            ->join('kendaraan k',        'k.id_kendaraan = pu.id_kendaraan',   'left')
            ->join('users u_pem',        'u_pem.id_user = ps.id_pemohon',     'left')
            ->where('ps.id_cabut', (int)$id_cabut)
            ->get()->row();

        if (!$c) return false;

        $recipients = [];

        // 1. Selalu kirim ke Admin Dept (Pemohon Pengajuan)
        if (!empty($c->email_pemohon)) {
            $recipients[] = $c->email_pemohon;
        }

        // 2. Selalu kirim ke Dept Manager (Role 6)
        $managers = $this->_get_users_by_role(6);
        foreach ($managers as $mgr) {
            if (!empty($mgr->email)) $recipients[] = $mgr->email;
        }

        // Kondisi 2: Jika diajukan oleh OHS Supt (role 3) -> Tambahkan Inspektor (Role 4)
        if ((int)$c->role_pemohon === 3) {
            $inspektors = $this->_get_users_by_role(4);
            foreach ($inspektors as $ins) {
                if (!empty($ins->email)) $recipients[] = $ins->email;
            }
        }

        // Kondisi 3: Jika diajukan oleh KTT (role 2) -> Tambahkan OHS Supt (Role 3) & Inspektor (Role 4)
        if ((int)$c->role_pemohon === 2) {
            $ohs_supts = $this->_get_users_by_role(3);
            foreach ($ohs_supts as $supt) {
                if (!empty($supt->email)) $recipients[] = $supt->email;
            }
            $inspektors = $this->_get_users_by_role(4);
            foreach ($inspektors as $ins) {
                if (!empty($ins->email)) $recipients[] = $ins->email;
            }
        }

        $recipients = array_unique(array_filter($recipients));

        $role_label_map = [2 => 'KTT', 3 => 'OHS Superintendent', 4 => 'Inspektor'];
        $pengaju_label = $role_label_map[$c->role_pemohon] ?? 'Petugas';
        $nama_pengaju  = $c->nama_pengaju ? $c->nama_pengaju . ' (' . $pengaju_label . ')' : $pengaju_label;

        $subject = '[Pencabutan Stiker] Stiker Kelayakan Unit ' . $c->no_polisi . ' Resmi Dicabut';
        $body = $this->_wrap(
            'Pemberitahuan Pencabutan Stiker Kelayakan Operasional',
            'Stiker kelayakan operasional untuk unit <strong>' . htmlspecialchars($c->no_polisi) . '</strong> telah <strong>RESMI DICABUT</strong> oleh Admin OHS.',
            '<div style="background:#f8d7da;color:#721c24;padding:12px 16px;border-radius:4px;border:1px solid #f5c6cb;">'
            . '<strong>Nomor Stiker:</strong> ' . htmlspecialchars($c->nomor_sticker ?? '-') . '<br>'
            . '<strong>Pengaju Pencabutan:</strong> ' . htmlspecialchars($nama_pengaju) . '<br>'
            . '<strong>Alasan Pencabutan:</strong> ' . htmlspecialchars($c->alasan)
            . '</div>',
            'Dengan dicabutnya stiker ini, unit tersebut <strong>TIDAK DIIZINKAN BEROPERASI</strong> di area kerja sampai dilakukan pengajuan commissioning ulang.',
            $this->_btn_link('Lihat Detail Pengajuan', $this->base_url . '/pengajuan')
        );

        foreach ($recipients as $to) {
            $this->_send($to, $subject, $body);
        }

        return true;
    }

    // ============================================================
    // PRIVATE — Core send + template helpers
    // ============================================================

    /**
     * Kirim email HTML — return true/false
     */
    private function _send($to, $subject, $body)
    {
        if (empty($to)) return false;

        try {
            $this->CI->email->initialize($this->_smtp_config());
            $this->CI->email->clear();
            $this->CI->email->from($this->from_email, $this->from_name);
            $this->CI->email->to($to);
            $this->CI->email->subject($subject);
            $this->CI->email->message($body);
            $this->CI->email->set_mailtype('html');
            $result = $this->CI->email->send(false); // false = jangan throw exception
            if (!$result) {
                log_message('error', '[Sikuk_email] Gagal kirim ke ' . $to . ' | ' . $this->CI->email->print_debugger());
            }
            return $result;
        } catch (Exception $e) {
            log_message('error', '[Sikuk_email] Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Bungkus konten ke dalam HTML email yang konsisten
     * Parameter variadic: setiap string jadi satu paragraf/block
     */
    private function _wrap($greeting, ...$blocks)
    {
        $content = '';
        foreach ($blocks as $block) {
            $content .= '<div style="margin-bottom:14px;">' . $block . '</div>';
        }

        return '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f4f6f9;font-family:Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9;padding:30px 0;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.08);">

      <!-- HEADER -->
      <tr>
        <td style="background:#1a73e8;padding:24px 32px;">
                    <h1 style="margin:0;color:#ffffff;font-size:20px;font-weight:bold;letter-spacing:.5px;">
                        🔧 TACTIC — TOKA APPROVAL &amp; COMMISSIONING TECHNICAL INSPECTION CENTER
                    </h1>
                    <p style="margin:4px 0 0;color:#cce0ff;font-size:12px;">TOKA Approval & Commissioning — Sistem Manajemen Inspeksi</p>
        </td>
      </tr>

      <!-- BODY -->
      <tr>
        <td style="padding:28px 32px;color:#333333;font-size:14px;line-height:1.7;">
          <p style="margin:0 0 18px;font-size:15px;">' . $greeting . '</p>
          ' . $content . '
        </td>
      </tr>

      <!-- FOOTER -->
      <tr>
        <td style="background:#f8f9fa;padding:16px 32px;border-top:1px solid #e8eaed;">
                        <p style="margin:0;color:#888;font-size:12px;line-height:1.6;">
                        Email ini dikirim otomatis oleh <strong>TACTIC System</strong>. Mohon tidak membalas email ini.<br>
                        Jika Anda merasa tidak berkepentingan, abaikan email ini.
                    </p>
        </td>
      </tr>

    </table>
  </td></tr>
</table>
</body>
</html>';
    }

    /**
     * Tabel ringkasan info unit kendaraan
     */
    private function _info_unit($p)
    {
        $no = '#PU-' . str_pad($p->id_pengajuan, 4, '0', STR_PAD_LEFT);
        return '<table style="width:100%;border-collapse:collapse;margin:12px 0;font-size:13px;">
            <tr style="background:#e8f0fe;">
                <td colspan="2" style="padding:8px 12px;font-weight:bold;color:#1a73e8;border:1px solid #c5d8fd;">
                    📋 Informasi Unit — ' . $no . '
                </td>
            </tr>
            <tr>
                <td style="padding:6px 12px;background:#f8f9fa;font-weight:bold;width:140px;border:1px solid #e0e0e0;">No. Polisi</td>
                <td style="padding:6px 12px;border:1px solid #e0e0e0;">' . htmlspecialchars($p->no_polisi) . '</td>
            </tr>
            <tr>
                <td style="padding:6px 12px;background:#f8f9fa;font-weight:bold;border:1px solid #e0e0e0;">Jenis Kendaraan</td>
                <td style="padding:6px 12px;border:1px solid #e0e0e0;">' . htmlspecialchars($p->jenis_kendaraan) . '</td>
            </tr>
            <tr>
                <td style="padding:6px 12px;background:#f8f9fa;font-weight:bold;border:1px solid #e0e0e0;">Merk / Tipe</td>
                <td style="padding:6px 12px;border:1px solid #e0e0e0;">' . htmlspecialchars($p->merk) . ' ' . htmlspecialchars($p->tipe) . '</td>
            </tr>
            <tr>
                <td style="padding:6px 12px;background:#f8f9fa;font-weight:bold;border:1px solid #e0e0e0;">Pemohon</td>
                <td style="padding:6px 12px;border:1px solid #e0e0e0;">' . htmlspecialchars($p->nama_pemohon) . '</td>
            </tr>
           
        </table>';
    }

    /**
     * Box catatan penolakan
     */
    private function _catatan_box($catatan, $label = 'Catatan')
    {
        if (empty($catatan)) return '';
        return '<div style="background:#fff3cd;border-left:4px solid #ffc107;padding:12px 16px;margin:12px 0;border-radius:4px;">
            <strong>' . htmlspecialchars($label) . ':</strong><br>
            <em>"' . htmlspecialchars($catatan) . '"</em>
        </div>';
    }

    /**
     * Tombol link CTA
     */
    private function _btn_link($label, $url)
    {
        return '<div style="text-align:center;margin:20px 0;">
            <a href="' . $url . '" style="display:inline-block;background:#1a73e8;color:#ffffff;padding:11px 28px;
               border-radius:6px;text-decoration:none;font-weight:bold;font-size:14px;letter-spacing:.3px;">
                ' . $label . ' →
            </a>
        </div>';
    }

    /**
     * Ambil data pengajuan lengkap untuk email
     */
    private function _get_pengajuan($id_pengajuan)
    {
        return $this->CI->db
            ->select('pu.id_pengajuan, pu.tipe_pengajuan, pu.tipe_akses, pu.status,
                      u.nama AS nama_pemohon, pu.email_pemohon AS email_pemohon,
                      k.no_polisi, k.jenis_kendaraan, k.merk, k.tipe, k.tahun')
            ->from('pengajuan_uji pu')
            ->join('users u',     'u.id_user = pu.id_pemohon',        'left')
            ->join('kendaraan k', 'k.id_kendaraan = pu.id_kendaraan', 'left')
            ->where('pu.id_pengajuan', $id_pengajuan)
            ->get()->row();
    }

    /**
     * Ambil semua user aktif berdasarkan role
     */
    private function _get_users_by_role($id_role)
    {
        return $this->CI->db
            ->select('u.id_user, u.nama, u.email')
            ->from('users u')
            ->join('user_roles ur', 'ur.id_user = u.id_user', 'left')
            ->group_start()
            ->where('ur.id_role', $id_role)
            ->or_where('u.id_role', $id_role)
            ->group_end()
            ->where('u.is_active', 1)
            ->where('u.email IS NOT NULL', null, false)
            ->where("u.email != ''", null, false)
            ->group_by('u.id_user')
            ->get()->result();
    }

    /**
     * Inisialisasi config SMTP dari config.php
     */
    private function _init_smtp()
    {
        $this->CI->email->initialize($this->_smtp_config());
    }

    /**
     * Kembalikan array konfigurasi SMTP
     */
    private function _smtp_config()
    {
        return [
            'protocol'   => 'smtp',
            'smtp_host'  => $this->CI->config->item('sikuk_smtp_host'),
            'smtp_port'  => $this->CI->config->item('sikuk_smtp_port'),
            'smtp_user'  => $this->CI->config->item('sikuk_smtp_user'),
            'smtp_pass'  => $this->CI->config->item('sikuk_smtp_pass'),
            'smtp_crypto' => $this->CI->config->item('sikuk_smtp_crypto'),
            'mailtype'   => 'html',
            'charset'    => 'utf-8',
            'newline'    => "\r\n",
            'crlf'       => "\r\n",
            'wordwrap'   => false,
        ];
    }
}
