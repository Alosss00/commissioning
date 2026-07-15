<?php

/**
 * Jadwal_model
 * Tujuan   : Akses & mutasi data jadwal inspeksi kendaraan
 * Caller   : Jadwal.php controller
 * Dependen : jadwal_uji, pengajuan_uji, kendaraan, tipe_kendaraan,
 *             mekanik_master, mekanik_tipe_kendaraan, users
 * Fungsi   :
 *   get_all($filter)                  — semua jadwal + JOIN lengkap
 *   get_by_id($id)                    — 1 jadwal detail
 *   get_by_pengajuan_aktif($id)       — jadwal scheduled by pengajuan
 *   get_by_pengajuan($id)             — semua jadwal by pengajuan
 *   get_mekanik_by_jenis($nama_tipe)  — mekanik capable untuk tipe (JOIN FK)
 *   get_inspektor()                   — user role 4 untuk dropdown
 *   cek_konflik_inspektor(...)        — konflik jadwal inspektor ±60 menit
 *   cek_konflik_mekanik(...)          — konflik jadwal mekanik ±60 menit
 *   get_jadwal_on_date(...)           — jadwal di tanggal tertentu
 *   insert/update/update_status/cancel — CRUD + cancel flow
 * Side effect:
 *   - READ: jadwal_uji, pengajuan_uji, kendaraan, tipe_kendaraan, mekanik_master, users
 *   - WRITE: jadwal_uji, pengajuan_uji (cancel reset status)
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Jadwal_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Semua jadwal + JOIN lengkap
    // ─────────────────────────────────────────────────────────────────────────
    public function get_all($filter = [])
    {
        $this->db->select('
            j.*,
            pu.tipe_pengajuan, pu.tipe_akses, pu.status AS status_pengajuan,
            k.no_polisi, t.nama_tipe AS jenis_kendaraan, k.merk, k.tipe AS tipe_kendaraan, k.tahun,
            u_pemohon.nama AS nama_pemohon,
            u_ins.nama     AS nama_inspektor_user,
            mm.nama        AS nama_mekanik_master,
            mm.perusahaan  AS perusahaan_mekanik,
            u_dibuat.nama  AS dibuat_oleh_nama
        ');
        $this->db->from('jadwal_uji j');
        $this->db->join('pengajuan_uji pu', 'pu.id_pengajuan = j.id_pengajuan');
        $this->db->join('kendaraan k',        'k.id_kendaraan = pu.id_kendaraan');
        $this->db->join('tipe_kendaraan t',   't.id_tipe_kendaraan = k.id_tipe_kendaraan', 'left');
        $this->db->join('users u_pemohon',    'u_pemohon.id_user = pu.id_pemohon');
        $this->db->join('users u_ins',        'u_ins.id_user = COALESCE(j.id_inspektor, j.id_mekanik)', 'left');
        $this->db->join('mekanik_master mm',  'mm.id_mekanik = j.id_mekanik_master', 'left');
        $this->db->join('users u_dibuat',     'u_dibuat.id_user = j.dibuat_oleh', 'left');

        if (!empty($filter['status'])) $this->db->where('j.status',              $filter['status']);
        if (!empty($filter['bulan']))  $this->db->where('MONTH(j.tanggal_uji)',   $filter['bulan']);
        if (!empty($filter['tahun']))  $this->db->where('YEAR(j.tanggal_uji)',    $filter['tahun']);

        $this->db->order_by('j.tanggal_uji', 'ASC');
        return $this->db->get()->result();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Get by ID — detail lengkap
    // ─────────────────────────────────────────────────────────────────────────
    public function get_by_id($id)
    {
        $this->db->select('
            j.*,
            pu.tipe_pengajuan, pu.tipe_akses, pu.status AS status_pengajuan,
            pu.nomor_mesin, pu.nomor_rangka, pu.tujuan,
            k.no_polisi, t.nama_tipe AS jenis_kendaraan, k.merk, k.tipe AS tipe_kendaraan, k.tahun,
            u_pemohon.nama  AS nama_pemohon, u_pemohon.email AS email_pemohon,
            u_ins.nama      AS nama_inspektor_user,
            mm.nama         AS nama_mekanik_master,
            mm.perusahaan   AS perusahaan_mekanik,
            mm.no_hp        AS hp_mekanik,
            u_dibuat.nama   AS dibuat_oleh_nama
        ');
        $this->db->from('jadwal_uji j');
        $this->db->join('pengajuan_uji pu',  'pu.id_pengajuan = j.id_pengajuan');
        $this->db->join('kendaraan k',        'k.id_kendaraan = pu.id_kendaraan');
        $this->db->join('tipe_kendaraan t',   't.id_tipe_kendaraan = k.id_tipe_kendaraan', 'left');
        $this->db->join('users u_pemohon',    'u_pemohon.id_user = pu.id_pemohon');
        $this->db->join('users u_ins',        'u_ins.id_user = COALESCE(j.id_inspektor, j.id_mekanik)', 'left');
        $this->db->join('mekanik_master mm',  'mm.id_mekanik = j.id_mekanik_master', 'left');
        $this->db->join('users u_dibuat',     'u_dibuat.id_user = j.dibuat_oleh', 'left');
        $this->db->where('j.id_jadwal', $id);
        return $this->db->get()->row();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Jadwal aktif (scheduled) by pengajuan
    // ─────────────────────────────────────────────────────────────────────────
    public function get_by_pengajuan_aktif($id_pengajuan)
    {
        return $this->db
            ->where('id_pengajuan', $id_pengajuan)
            ->where('status', 'scheduled')
            ->get('jadwal_uji')->row();
    }

    public function get_by_pengajuan($id_pengajuan)
    {
        return $this->db
            ->where('id_pengajuan', $id_pengajuan)
            ->order_by('created_at', 'DESC')
            ->get('jadwal_uji')->result();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CRUD
    // ─────────────────────────────────────────────────────────────────────────
    public function insert($data)
    {
        $this->db->insert('jadwal_uji', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        return $this->db->where('id_jadwal', $id)->update('jadwal_uji', $data);
    }

    public function update_status($id, $status)
    {
        return $this->db->where('id_jadwal', $id)->update('jadwal_uji', ['status' => $status]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Cancel jadwal → pengajuan kembali ke 'dijadwalkan'
    // ─────────────────────────────────────────────────────────────────────────
    public function cancel($id)
    {
        $jadwal = $this->get_by_id($id);
        if (!$jadwal || $jadwal->status !== 'scheduled') return false;

        $this->db->trans_start();
        $this->db->where('id_jadwal', $id)->update('jadwal_uji', ['status' => 'cancelled']);
        $this->db->where('id_pengajuan', $jadwal->id_pengajuan)
            ->where('status', 'dijadwalkan')
            ->update('pengajuan_uji', ['status' => 'dijadwalkan']);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Inspektor (user role 4) — dropdown
    // ─────────────────────────────────────────────────────────────────────────
    public function get_inspektor()
    {
        $this->db->select('u.id_user, u.nama, u.email, u.jabatan');
        $this->db->from('users u');
        $this->db->join('user_roles ur', 'ur.id_user = u.id_user', 'left');
        $this->db->group_start()
            ->where('ur.id_role', 4)
            ->or_where('u.id_role', 4)
            ->group_end();
        $this->db->where('u.is_active', 1);
        $this->db->group_by('u.id_user');
        $this->db->order_by('u.nama', 'ASC');
        return $this->db->get()->result();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Mekanik master capable untuk nama_tipe kendaraan
    // JOIN melalui tipe_kendaraan.nama_tipe — konsisten setelah migrasi
    // Mekanik tanpa tipe terdaftar = berlaku untuk semua tipe
    // ─────────────────────────────────────────────────────────────────────────
    public function get_mekanik_by_jenis($nama_tipe = null)
    {
        $this->db->select('mm.*');
        $this->db->from('mekanik_master mm');

        if ($nama_tipe) {
            // Mekanik yang punya kompetensi tipe ini
            // ATAU mekanik yang tidak terdaftar ke tipe apapun (berlaku semua)
            $nama_escaped = $this->db->escape_str($nama_tipe);
            $this->db->where("(
                EXISTS (
                    SELECT 1
                    FROM mekanik_tipe_kendaraan mt
                    INNER JOIN tipe_kendaraan t ON t.id_tipe_kendaraan = mt.id_tipe_kendaraan
                    WHERE mt.id_mekanik = mm.id_mekanik
                      AND t.nama_tipe   = '{$nama_escaped}'
                )
                OR NOT EXISTS (
                    SELECT 1
                    FROM mekanik_tipe_kendaraan mt2
                    WHERE mt2.id_mekanik = mm.id_mekanik
                )
            )", null, false);
        }

        $this->db->where('mm.is_active', 1);
        $this->db->order_by('mm.nama', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Versi lebih efisien: pakai id_tipe_kendaraan langsung (no string JOIN).
     * Gunakan untuk kode baru setelah pengajuan + kendaraan sudah punya FK.
     */
    public function get_mekanik_by_tipe_id($id_tipe_kendaraan = null)
    {
        $this->db->select('mm.*');
        $this->db->from('mekanik_master mm');

        if ($id_tipe_kendaraan) {
            $id = (int) $id_tipe_kendaraan;
            $this->db->where("(
                EXISTS (
                    SELECT 1 FROM mekanik_tipe_kendaraan mt
                    WHERE mt.id_mekanik = mm.id_mekanik
                      AND mt.id_tipe_kendaraan = {$id}
                )
                OR NOT EXISTS (
                    SELECT 1 FROM mekanik_tipe_kendaraan mt2
                    WHERE mt2.id_mekanik = mm.id_mekanik
                )
            )", null, false);
        }

        $this->db->where('mm.is_active', 1)->order_by('mm.nama', 'ASC');
        return $this->db->get()->result();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Cek konflik inspektor (user) — selisih minimal 60 menit
    // ─────────────────────────────────────────────────────────────────────────
    public function cek_konflik_inspektor($tanggal_uji, $id_inspektor, $exclude_id = null)
    {
        $dt  = $this->db->escape_str(date('Y-m-d H:i:s', strtotime($tanggal_uji)));
        $id  = (int) $id_inspektor;

        $this->db->from('jadwal_uji j')
            ->where("COALESCE(j.id_inspektor, j.id_mekanik) = {$id}", null, false)
            ->where('j.status', 'scheduled')
            ->where("ABS(TIMESTAMPDIFF(MINUTE, j.tanggal_uji, '{$dt}')) < 60", null, false);

        if (!empty($exclude_id) && (int) $exclude_id > 0) {
            $this->db->where('j.id_jadwal !=', (int) $exclude_id);
        }
        return $this->db->count_all_results() > 0;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Cek konflik mekanik master — selisih minimal 60 menit
    // ─────────────────────────────────────────────────────────────────────────
    public function cek_konflik_mekanik($tanggal_uji, $id_mekanik_master, $exclude_id = null)
    {
        $dt = $this->db->escape_str(date('Y-m-d H:i:s', strtotime($tanggal_uji)));

        $this->db->from('jadwal_uji j')
            ->where('j.id_mekanik_master', (int) $id_mekanik_master)
            ->where('j.status', 'scheduled')
            ->where("ABS(TIMESTAMPDIFF(MINUTE, j.tanggal_uji, '{$dt}')) < 60", null, false);

        if (!empty($exclude_id) && (int) $exclude_id > 0) {
            $this->db->where('j.id_jadwal !=', (int) $exclude_id);
        }
        return $this->db->count_all_results() > 0;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Jadwal di tanggal tertentu — referensi form penjadwalan
    // ─────────────────────────────────────────────────────────────────────────
    public function get_jadwal_on_date($tanggal, $id_inspektor = null, $id_mekanik_master = null)
    {
        $tgl = date('Y-m-d', strtotime($tanggal));
        $this->db->select('j.id_jadwal, j.tanggal_uji, j.status,
            k.no_polisi, t.nama_tipe AS jenis_kendaraan,
            u_ins.nama AS nama_inspektor,
            mm.nama    AS nama_mekanik');
        $this->db->from('jadwal_uji j');
        $this->db->join('pengajuan_uji pu', 'pu.id_pengajuan = j.id_pengajuan', 'left');
        $this->db->join('kendaraan k',       'k.id_kendaraan = pu.id_kendaraan',   'left');
        $this->db->join('tipe_kendaraan t',  't.id_tipe_kendaraan = k.id_tipe_kendaraan', 'left');
        $this->db->join('users u_ins',       'u_ins.id_user = COALESCE(j.id_inspektor, j.id_mekanik)', 'left');
        $this->db->join('mekanik_master mm', 'mm.id_mekanik = j.id_mekanik_master', 'left');
        $this->db->where("DATE(j.tanggal_uji)", $tgl);
        $this->db->where('j.status', 'scheduled');

        if ($id_inspektor)      $this->db->where("COALESCE(j.id_inspektor, j.id_mekanik)", $id_inspektor);
        if ($id_mekanik_master) $this->db->where('j.id_mekanik_master', $id_mekanik_master);

        $this->db->order_by('j.tanggal_uji', 'ASC');
        return $this->db->get()->result();
    }
}
