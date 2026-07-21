<?php

/**
 * Kendaraan_model
 * Tujuan   : Akses data kendaraan + info stiker untuk daftar & dropdown
 * Caller   : Kendaraan.php, Pengajuan.php controller
 * Dependen : kendaraan, tipe_kendaraan (JOIN), sticker_release, pengajuan_uji
 * Fungsi   :
 *   get_all()                      — dropdown sederhana + nama_tipe
 *   get_by_id($id)                 — 1 row + nama_tipe JOIN
 *   get_datatable(...)             — DataTable server-side (semua kendaraan)
 *   count_all/count_filtered       — paginasi semua kendaraan
 *   count_all_lulus(...)           — paginasi hanya kendaraan lulus commissioning
 *   count_filtered_lulus(...)      — idem filtered
 *   get_datatable_lulus(...)       — DataTable hanya kendaraan lulus commissioning
 *   is_no_polisi_exists(...)       — validasi duplikat
 *   insert/update/delete           — CRUD
 *   has_pengajuan($id)             — cek sebelum hapus
 *   get_kendaraan_lulus_eligible() — dropdown recommissioning
 *   get_stiker_info_batch(...)     — batch stiker info per kendaraan
 *   get_jenis_list()               — list tipe unik (untuk filter)
 * Side effect:
 *   - READ: kendaraan, tipe_kendaraan, sticker_release, pengajuan_uji
 *   - WRITE: kendaraan (insert/update/delete)
 * Perubahan v2:
 *   - Tambah _base_query_lulus() — filter hanya kendaraan yang pernah lulus (stiker_keluar/acc_ktt)
 *   - Tambah count_all_lulus, count_filtered_lulus, get_datatable_lulus
 *   - get_datatable_lulus SELECT tambahkan tgl_lulus (tanggal acc KTT terakhir)
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Kendaraan_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Base Query — JOIN tipe_kendaraan, SEMUA kendaraan
    // ─────────────────────────────────────────────────────────────────────────
    private function _base_query($filter = [])
    {
        $this->db
            ->select('k.*, t.nama_tipe AS jenis_kendaraan, t.kode_tipe,
                      COUNT(pu.id_pengajuan) AS total_pengajuan')
            ->from('kendaraan k')
            ->join('tipe_kendaraan t',  't.id_tipe_kendaraan = k.id_tipe_kendaraan', 'left')
            ->join('pengajuan_uji pu',  'pu.id_kendaraan = k.id_kendaraan',          'left')
            ->group_by('k.id_kendaraan');

        $this->_apply_filter($filter);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Base Query — HANYA kendaraan yang lulus commissioning (stiker_keluar/acc_ktt)
    // Menggunakan EXISTS subquery — lebih efisien dari JOIN lalu GROUP BY
    // INDEX yang dipakai: pengajuan_uji(id_kendaraan, status) — idx_status_tgl sudah ada
    // ─────────────────────────────────────────────────────────────────────────
    private function _base_query_lulus($filter = [])
    {
        $this->db
            ->select('k.*, t.nama_tipe AS jenis_kendaraan, t.kode_tipe,
                      COUNT(pu_all.id_pengajuan) AS total_pengajuan,
                      MAX(pu_lulus.tgl_acc_ktt)  AS tgl_lulus')
            ->from('kendaraan k')
            ->join('tipe_kendaraan t', 't.id_tipe_kendaraan = k.id_tipe_kendaraan', 'left')
            // Count semua pengajuan kendaraan ini
            ->join('pengajuan_uji pu_all', 'pu_all.id_kendaraan = k.id_kendaraan', 'left')
            // Ambil tgl_lulus dari pengajuan yang sudah acc
            ->join(
                'pengajuan_uji pu_lulus',
                "pu_lulus.id_kendaraan = k.id_kendaraan
                 AND pu_lulus.status IN ('stiker_keluar','acc_ktt')",
                'left'
            )
            // Batasi hanya kendaraan yang PERNAH lulus — EXISTS lebih efisien untuk filter
            ->where("EXISTS (
                SELECT 1 FROM pengajuan_uji pu_filter
                WHERE pu_filter.id_kendaraan = k.id_kendaraan
                  AND pu_filter.status IN ('stiker_keluar','acc_ktt')
            )", null, false)
            ->group_by('k.id_kendaraan');

        $this->_apply_filter($filter);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Filter terpusat — dipakai oleh kedua base query
    // ─────────────────────────────────────────────────────────────────────────
    private function _apply_filter($filter = [])
    {
        if (!empty($filter['search'])) {
            $kw = $filter['search'];
            $this->db->group_start()
                ->like('k.no_polisi',  $kw)
                ->or_like('t.nama_tipe', $kw)
                ->or_like('k.merk',    $kw)
                ->or_like('k.tipe',    $kw)
                ->or_like('k.nomor_unit', $kw)
                ->group_end();
        }

        if (!empty($filter['jenis_kendaraan'])) {
            if (is_numeric($filter['jenis_kendaraan'])) {
                $this->db->where('k.id_tipe_kendaraan', (int) $filter['jenis_kendaraan']);
            } else {
                $this->db->where('t.nama_tipe', $filter['jenis_kendaraan']);
            }
        }

        if (isset($filter['is_unit_baru']) && $filter['is_unit_baru'] !== '') {
            $this->db->where('k.is_unit_baru', $filter['is_unit_baru']);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Count — SEMUA kendaraan
    // ─────────────────────────────────────────────────────────────────────────
    public function count_all($filter = [])
    {
        $this->_base_query($filter);
        return $this->db->count_all_results();
    }

    public function count_filtered($filter = [])
    {
        $this->_base_query($filter);
        return $this->db->count_all_results();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Count — HANYA kendaraan lulus commissioning
    // ─────────────────────────────────────────────────────────────────────────
    public function count_all_lulus($filter = [])
    {
        $this->_base_query_lulus($filter);
        return $this->db->count_all_results();
    }

    public function count_filtered_lulus($filter = [])
    {
        $this->_base_query_lulus($filter);
        return $this->db->count_all_results();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DataTable — SEMUA kendaraan (dipakai Pengajuan, dll.)
    // ─────────────────────────────────────────────────────────────────────────
    public function get_datatable($start, $length, $filter = [])
    {
        $this->_base_query($filter);
        $this->db->order_by('k.created_at', 'DESC')->limit($length, $start);
        return $this->db->get()->result();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DataTable — HANYA kendaraan lulus commissioning
    // Kolom tambahan: tgl_lulus (MAX tgl_acc_ktt dari pengajuan lulus)
    // ─────────────────────────────────────────────────────────────────────────
    public function get_datatable_lulus($start, $length, $filter = [])
    {
        $this->_base_query_lulus($filter);
        $this->db->order_by('tgl_lulus', 'DESC')->limit($length, $start);
        return $this->db->get()->result();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Get all — dropdown
    // ─────────────────────────────────────────────────────────────────────────
    public function get_all()
    {
        return $this->db
            ->select('k.*, t.nama_tipe AS jenis_kendaraan, t.kode_tipe')
            ->from('kendaraan k')
            ->join('tipe_kendaraan t', 't.id_tipe_kendaraan = k.id_tipe_kendaraan', 'left')
            ->order_by('k.no_polisi', 'ASC')
            ->get()->result();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Get by ID — JOIN tipe
    // ─────────────────────────────────────────────────────────────────────────
    public function get_by_id($id)
    {
        return $this->db
            ->select('k.*, t.nama_tipe AS jenis_kendaraan, t.kode_tipe, t.id_tipe_kendaraan')
            ->from('kendaraan k')
            ->join('tipe_kendaraan t', 't.id_tipe_kendaraan = k.id_tipe_kendaraan', 'left')
            ->where('k.id_kendaraan', (int) $id)
            ->get()->row();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Validasi no polisi unik
    // ─────────────────────────────────────────────────────────────────────────
    public function is_no_polisi_exists($no_polisi, $exclude_id = null)
    {
        $this->db->where('no_polisi', $no_polisi);
        if ($exclude_id) $this->db->where('id_kendaraan !=', $exclude_id);
        return $this->db->count_all_results('kendaraan') > 0;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CRUD
    // ─────────────────────────────────────────────────────────────────────────
    public function insert($data)
    {
        $this->db->insert('kendaraan', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        return $this->db->where('id_kendaraan', $id)->update('kendaraan', $data);
    }

    public function delete($id)
    {
        return $this->db->where('id_kendaraan', $id)->delete('kendaraan');
    }

    public function has_pengajuan($id)
    {
        return $this->db->where('id_kendaraan', $id)
            ->count_all_results('pengajuan_uji') > 0;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Kendaraan eligible recommissioning (lulus + stiker expired/belum ada)
    // 1 query subquery — no N+1
    // ─────────────────────────────────────────────────────────────────────────
    public function get_kendaraan_lulus_eligible()
    {
        return $this->db->query("
            SELECT DISTINCT
                k.*,
                t.nama_tipe  AS jenis_kendaraan,
                t.kode_tipe,
                sr.nomor_sticker,
                sr.tgl_expired,
                DATEDIFF(sr.tgl_expired, NOW()) AS sisa_hari,
                CASE
                    WHEN sr.tgl_expired IS NULL              THEN 'belum_ada'
                    WHEN DATEDIFF(sr.tgl_expired, NOW()) < 0 THEN 'expired'
                    ELSE 'aktif'
                END AS status_stiker
            FROM kendaraan k
            INNER JOIN tipe_kendaraan t ON t.id_tipe_kendaraan = k.id_tipe_kendaraan
            INNER JOIN pengajuan_uji pu
                ON pu.id_kendaraan = k.id_kendaraan
                AND pu.status IN ('stiker_keluar','lulus_inspeksi','diterima_ohs_supt','acc_ktt','diterima_admin_ohs')
            LEFT JOIN (
                SELECT sr2.*, pu2.id_kendaraan
                FROM sticker_release sr2
                INNER JOIN pengajuan_uji pu2 ON pu2.id_pengajuan = sr2.id_pengajuan
                INNER JOIN (
                    SELECT pu3.id_kendaraan, MAX(sr3.id_sticker) AS max_id
                    FROM sticker_release sr3
                    INNER JOIN pengajuan_uji pu3 ON pu3.id_pengajuan = sr3.id_pengajuan
                    GROUP BY pu3.id_kendaraan
                ) latest ON sr2.id_sticker = latest.max_id AND pu2.id_kendaraan = latest.id_kendaraan
            ) sr ON sr.id_kendaraan = k.id_kendaraan
            WHERE sr.tgl_expired IS NULL OR DATEDIFF(sr.tgl_expired, NOW()) < 0
            ORDER BY k.no_polisi ASC
        ")->result();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Batch stiker info — 1 query, keyed by id_kendaraan
    // ─────────────────────────────────────────────────────────────────────────
    public function get_stiker_info_batch(array $id_list)
    {
        if (empty($id_list)) return [];

        $ids = implode(',', array_map('intval', $id_list));

        $rows = $this->db->query("
            SELECT
                pu.id_kendaraan,
                sr.nomor_sticker,
                sr.tanggal_release,
                sr.tgl_expired,
                sr.is_expired,
                DATEDIFF(sr.tgl_expired, NOW()) AS sisa_hari
            FROM sticker_release sr
            INNER JOIN pengajuan_uji pu ON pu.id_pengajuan = sr.id_pengajuan
            INNER JOIN (
                SELECT pu2.id_kendaraan, MAX(sr2.id_sticker) AS max_id
                FROM sticker_release sr2
                INNER JOIN pengajuan_uji pu2 ON pu2.id_pengajuan = sr2.id_pengajuan
                WHERE pu2.id_kendaraan IN ({$ids})
                GROUP BY pu2.id_kendaraan
            ) latest ON sr.id_sticker = latest.max_id AND pu.id_kendaraan = latest.id_kendaraan
            WHERE pu.id_kendaraan IN ({$ids})
        ")->result();

        $map = [];
        foreach ($rows as $r) {
            $map[$r->id_kendaraan] = $r;
        }
        return $map;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // List tipe unik hanya dari kendaraan lulus — untuk filter dropdown
    // ─────────────────────────────────────────────────────────────────────────
    public function get_jenis_list()
    {
        return $this->db
            ->select('t.id_tipe_kendaraan, t.nama_tipe AS jenis_kendaraan')
            ->from('tipe_kendaraan t')
            ->join('kendaraan k',      'k.id_tipe_kendaraan = t.id_tipe_kendaraan', 'inner')
            ->join('pengajuan_uji pu', 'pu.id_kendaraan = k.id_kendaraan',          'inner')
            ->where_in('pu.status', ['stiker_keluar', 'acc_ktt'])
            ->group_by('t.id_tipe_kendaraan')
            ->order_by('t.nama_tipe', 'ASC')
            ->get()->result();
    }
}
