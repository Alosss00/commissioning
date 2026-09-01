<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Model Jadwal_model
 * 
 * Pengelolaan agenda jadwal inspeksi uji kelayakan kendaraan.
 * Menyediakan fungsi:
 * - Mengambil seluruh agenda jadwal ter-filter (Eager JOIN kendaraan, tipe, pemohon, inspektor, mekanik master)
 * - Pengecekan konflik jadwal inspektor & mekanik (toleransi waktu ±60 menit)
 * - Pembatalan (cancel) dan pembaharuan status jadwal uji
 */
class Jadwal_model extends CI_Model
{
    /**
     * Konstruktor Jadwal_model
     * Inisialisasi library database CodeIgniter.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Mengambil daftar seluruh jadwal inspeksi beserta relasi detail kendaraan, pemohon, dan mekanik.
     * 
     * @param array $filter Filter opsional (status, bulan, tahun)
     * @return array List object jadwal uji
     */
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
        $this->db->join('pengajuan_uji pu',  'pu.id_pengajuan = j.id_pengajuan');
        $this->db->join('kendaraan k',        'k.id_kendaraan = pu.id_kendaraan');
        $this->db->join('tipe_kendaraan t',   't.id_tipe_kendaraan = k.id_tipe_kendaraan', 'left');
        $this->db->join('users u_pemohon',    'u_pemohon.id_user = pu.id_pemohon');
        $this->db->join('users u_ins',        'u_ins.id_user = COALESCE(j.id_inspektor, j.id_mekanik)', 'left');
        $this->db->join('mekanik_master mm',  'mm.id_mekanik = j.id_mekanik_master', 'left');
        $this->db->join('users u_dibuat',     'u_dibuat.id_user = j.dibuat_oleh', 'left');

        if (!empty($filter['status'])) $this->db->where('j.status',            $filter['status']);
        if (!empty($filter['bulan']))  $this->db->where('MONTH(j.tanggal_uji)', (int)$filter['bulan']);
        if (!empty($filter['tahun']))  $this->db->where('YEAR(j.tanggal_uji)',  (int)$filter['tahun']);

        $this->db->order_by('j.tanggal_uji', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Mengambil detail lengkap 1 record jadwal uji berdasarkan ID.
     * 
     * @param int $id ID Jadwal Uji
     * @return object|null Object detail jadwal uji
     */
    public function get_by_id($id)
    {
        $this->db->select('
            j.*,
            pu.tipe_pengajuan, pu.tipe_akses, pu.status AS status_pengajuan,
            pu.tujuan,
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
        $this->db->where('j.id_jadwal', (int) $id);
        return $this->db->get()->row();
    }

    /**
     * Mengambil jadwal aktif (status 'scheduled') untuk suatu pengajuan.
     * 
     * @param int $id_pengajuan ID Pengajuan
     * @return object|null Object jadwal aktif
     */
    public function get_by_pengajuan_aktif($id_pengajuan)
    {
        return $this->db
            ->where('id_pengajuan', (int) $id_pengajuan)
            ->where('status', 'scheduled')
            ->get('jadwal_uji')->row();
    }

    /**
     * Mengambil seluruh riwayat jadwal uji suatu pengajuan.
     * 
     * @param int $id_pengajuan ID Pengajuan
     * @return array List object riwayat jadwal
     */
    public function get_by_pengajuan($id_pengajuan)
    {
        return $this->db
            ->where('id_pengajuan', (int) $id_pengajuan)
            ->order_by('created_at', 'DESC')
            ->get('jadwal_uji')->result();
    }

    /**
     * Menyimpan data jadwal uji baru.
     * 
     * @param array $data Record data jadwal
     * @return int ID jadwal baru
     */
    public function insert($data)
    {
        $this->db->insert('jadwal_uji', $data);
        return $this->db->insert_id();
    }

    /**
     * Memperbarui data jadwal uji berdasarkan ID.
     * 
     * @param int $id ID Jadwal
     * @param array $data Data pembaharuan
     * @return bool Status keberhasilan update
     */
    public function update($id, $data)
    {
        return $this->db->where('id_jadwal', (int) $id)->update('jadwal_uji', $data);
    }

    /**
     * Memperbarui status jadwal uji.
     * 
     * @param int $id ID Jadwal
     * @param string $status Kode status baru
     * @return bool Status keberhasilan update
     */
    public function update_status($id, $status)
    {
        return $this->db->where('id_jadwal', (int) $id)->update('jadwal_uji', ['status' => $status]);
    }

    /**
     * Membatalkan (cancel) jadwal uji yang terjadwal.
     * Melakukan dua operasi atomik (transaksi): update status jadwal ke 'cancelled'
     * dan mereset status pengajuan jika perlu.
     * 
     * @param int $id ID Jadwal
     * @return bool Status keberhasilan transaksi
     */
    public function cancel($id)
    {
        $jadwal = $this->get_by_id($id);
        if (!$jadwal || $jadwal->status !== 'scheduled') {
            return false;
        }

        $this->db->trans_start();
        $this->db->where('id_jadwal', (int) $id)->update('jadwal_uji', ['status' => 'cancelled']);
        $this->db->where('id_pengajuan', (int) $jadwal->id_pengajuan)
            ->where('status', 'dijadwalkan')
            ->update('pengajuan_uji', ['status' => 'dijadwalkan']);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * Mengambil daftar pengguna yang bertindak sebagai Inspektor (Role ID = 4).
     * 
     * @return array List object user inspektor
     */
    public function get_inspektor()
    {
        $this->db->select('u.id_user, u.nama, u.email, u.jabatan, u.departemen');
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

    /**
     * Mengambil daftar mekanik master yang kompeten untuk jenis/nama tipe kendaraan tertentu.
     * 
     * @param string|null $nama_tipe Nama tipe kendaraan
     * @return array List object mekanik master
     */
    public function get_mekanik_by_jenis($nama_tipe = null)
    {
        $this->db->select('mm.*');
        $this->db->from('mekanik_master mm');

        if (!empty($nama_tipe)) {
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
     * Mengambil daftar mekanik master kompeten berdasarkan ID Tipe Kendaraan (lebih efisien).
     * 
     * @param int|null $id_tipe_kendaraan ID Tipe Kendaraan
     * @return array List object mekanik master
     */
    public function get_mekanik_by_tipe_id($id_tipe_kendaraan = null)
    {
        $this->db->select('mm.*');
        $this->db->from('mekanik_master mm');

        if (!empty($id_tipe_kendaraan)) {
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

    /**
     * Memeriksa bentrok/konflik jadwal inspektor (selisih minimal 60 menit dari jadwal lain yang aktif).
     * 
     * @param string $tanggal_uji Tanggal & waktu uji (Y-m-d H:i:s)
     * @param int $id_inspektor ID User Inspektor
     * @param int|null $exclude_id ID Jadwal yang dikecualikan (saat edit)
     * @return bool True jika terdapat konflik jadwal
     */
    public function cek_konflik_inspektor($tanggal_uji, $id_inspektor, $exclude_id = null)
    {
        $dt = $this->db->escape_str(date('Y-m-d H:i:s', strtotime($tanggal_uji)));
        $id = (int) $id_inspektor;

        $this->db->from('jadwal_uji j')
            ->where("COALESCE(j.id_inspektor, j.id_mekanik) = {$id}", null, false)
            ->where('j.status', 'scheduled')
            ->where("ABS(TIMESTAMPDIFF(MINUTE, j.tanggal_uji, '{$dt}')) < 60", null, false);

        if (!empty($exclude_id) && (int) $exclude_id > 0) {
            $this->db->where('j.id_jadwal !=', (int) $exclude_id);
        }
        return $this->db->count_all_results() > 0;
    }

    /**
     * Memeriksa bentrok/konflik jadwal mekanik master (selisih minimal 60 menit).
     * 
     * @param string $tanggal_uji Tanggal & waktu uji (Y-m-d H:i:s)
     * @param int $id_mekanik_master ID Mekanik Master
     * @param int|null $exclude_id ID Jadwal yang dikecualikan (saat edit)
     * @return bool True jika terdapat konflik jadwal
     */
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

    /**
     * Mengambil daftar agenda jadwal inspeksi pada tanggal tertentu untuk referensi form penjadwalan.
     * 
     * @param string $tanggal Tanggal uji (Y-m-d)
     * @param int|null $id_inspektor ID User Inspektor
     * @param int|null $id_mekanik_master ID Mekanik Master
     * @return array List object jadwal pada tanggal tersebut
     */
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

        if ($id_inspektor)      $this->db->where("COALESCE(j.id_inspektor, j.id_mekanik)", (int) $id_inspektor);
        if ($id_mekanik_master) $this->db->where('j.id_mekanik_master', (int) $id_mekanik_master);

        $this->db->order_by('j.tanggal_uji', 'ASC');
        return $this->db->get()->result();
    }
}
