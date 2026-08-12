<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Model Api_laporan_model
 *
 * Model READ-ONLY khusus untuk endpoint API laporan uji kelayakan.
 * Dibuat terpisah dari Pengajuan_model untuk memastikan tidak ada fungsi
 * insert / update / delete yang bisa di-reuse secara tidak sengaja.
 *
 * Kolom sensitif (password, token, session data) TIDAK pernah di-SELECT.
 */
class Api_laporan_model extends CI_Model
{
    /**
     * Konstruktor – hanya inisialisasi database.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // -------------------------------------------------------------------------
    // PUBLIC — READ-ONLY QUERIES
    // -------------------------------------------------------------------------

    /**
     * Mengambil data laporan pengajuan uji kelayakan.
     *
     * JOIN tabel: pengajuan_uji, kendaraan, tipe_kendaraan, users (pemohon),
     *             uji_kelayakan (hasil inspeksi), jadwal_uji, sticker_release.
     *
     * Filter opsional yang dapat dikirim via GET parameter:
     *   - status       : filter berdasarkan status pengajuan
     *   - tgl_dari     : tanggal awal pengajuan (Y-m-d)
     *   - tgl_sampai   : tanggal akhir pengajuan (Y-m-d)
     *   - departemen   : filter berdasarkan perusahaan/departemen kendaraan
     *   - jenis        : filter berdasarkan jenis/tipe kendaraan
     *   - limit        : batas jumlah baris (default 500, max 2000)
     *   - offset       : offset pagination (default 0)
     *
     * @param  array $filters  Associative array filter (sudah disanitasi controller)
     * @return array           Array of stdClass objects
     */
    public function get_laporan($filters = [])
    {
        // Tentukan LIMIT & OFFSET (dibatasi agar tidak dump seluruh tabel tanpa batas)
        $limit  = min((int) ($filters['limit']  ?? 500), 2000);
        $offset = max((int) ($filters['offset'] ?? 0),    0);

        // Bangun WHERE clauses secara manual agar aman dari injeksi
        $where_clauses = [];

        if (!empty($filters['status'])) {
            $where_clauses[] = 'pu.status = ' . $this->db->escape($filters['status']);
        }
        if (!empty($filters['tgl_dari'])) {
            $where_clauses[] = 'DATE(pu.tanggal_pengajuan) >= ' . $this->db->escape($filters['tgl_dari']);
        }
        if (!empty($filters['tgl_sampai'])) {
            $where_clauses[] = 'DATE(pu.tanggal_pengajuan) <= ' . $this->db->escape($filters['tgl_sampai']);
        }
        if (!empty($filters['departemen'])) {
            $where_clauses[] = 'k.perusahaan = ' . $this->db->escape($filters['departemen']);
        }
        if (!empty($filters['jenis'])) {
            $where_clauses[] = 't.nama_tipe = ' . $this->db->escape($filters['jenis']);
        }

        $where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

        // ---- Query utama ----
        // Menggunakan subquery MAX() agar tidak menghasilkan baris duplikat
        // ketika 1 pengajuan punya > 1 jadwal / > 1 uji_kelayakan.
        // Kolom sensitif (password, remember_token, dsb) TIDAK di-SELECT.
        $sql = "
            SELECT
                pu.id_pengajuan,
                pu.tanggal_pengajuan,
                pu.tipe_pengajuan,
                pu.tipe_akses,
                pu.tujuan,
                pu.status,

                k.no_polisi,
                k.nomor_unit,
                k.merk,
                k.model_unit,
                k.tipe         AS tipe_unit,
                k.tahun,
                k.perusahaan   AS departemen,
                k.is_unit_baru,

                t.nama_tipe    AS jenis_kendaraan,

                u_pem.nama     AS nama_pemohon,
                u_pem.jabatan  AS jabatan_pemohon,
                u_pem.departemen AS departemen_pemohon,

                j.tanggal_uji  AS tgl_jadwal_rencana,

                COALESCE(uk.nama_inspektor, mm.nama) AS nama_inspektor,
                COALESCE(uk.perusahaan_inspektor, mm.perusahaan) AS perusahaan_inspektor,
                uk.hasil       AS hasil_inspeksi,
                uk.catatan_temuan AS catatan_inspeksi,
                uk.tanggal_uji AS tgl_inspeksi_aktual,

                sr.nomor_sticker    AS nomor_stiker,
                sr.tanggal_release  AS tgl_rilis_stiker,
                sr.tgl_expired      AS tgl_expired_stiker,

                pu.created_at,
                pu.updated_at

            FROM pengajuan_uji pu

            LEFT JOIN kendaraan      k   ON k.id_kendaraan        = pu.id_kendaraan
            LEFT JOIN tipe_kendaraan t   ON t.id_tipe_kendaraan   = k.id_tipe_kendaraan
            LEFT JOIN users          u_pem ON u_pem.id_user       = pu.id_pemohon

            -- Jadwal terbaru per pengajuan
            LEFT JOIN (
                SELECT id_pengajuan, MAX(id_jadwal) AS max_id_jadwal
                FROM jadwal_uji
                GROUP BY id_pengajuan
            ) jl ON jl.id_pengajuan = pu.id_pengajuan
            LEFT JOIN jadwal_uji     j   ON j.id_jadwal           = jl.max_id_jadwal
            LEFT JOIN mekanik_master mm  ON mm.id_mekanik          = j.id_mekanik_master

            -- Hasil uji terbaru per pengajuan
            LEFT JOIN (
                SELECT id_pengajuan, MAX(id_uji) AS max_id_uji
                FROM uji_kelayakan
                GROUP BY id_pengajuan
            ) ul ON ul.id_pengajuan = pu.id_pengajuan
            LEFT JOIN uji_kelayakan  uk  ON uk.id_uji              = ul.max_id_uji

            -- Stiker terbaru per pengajuan
            LEFT JOIN (
                SELECT id_pengajuan, MAX(id_sticker) AS max_id_sticker
                FROM sticker_release
                GROUP BY id_pengajuan
            ) sl ON sl.id_pengajuan = pu.id_pengajuan
            LEFT JOIN sticker_release sr ON sr.id_sticker          = sl.max_id_sticker

            {$where_sql}

            ORDER BY pu.tanggal_pengajuan DESC, pu.id_pengajuan DESC
            LIMIT {$limit} OFFSET {$offset}
        ";

        return $this->db->query($sql)->result();
    }

    /**
     * Menghitung total baris yang cocok dengan filter (tanpa LIMIT/OFFSET).
     * Digunakan untuk field meta.total pada response JSON.
     *
     * @param  array $filters  Associative array filter (sudah disanitasi controller)
     * @return int
     */
    public function count_laporan($filters = [])
    {
        $where_clauses = [];

        if (!empty($filters['status'])) {
            $where_clauses[] = 'pu.status = ' . $this->db->escape($filters['status']);
        }
        if (!empty($filters['tgl_dari'])) {
            $where_clauses[] = 'DATE(pu.tanggal_pengajuan) >= ' . $this->db->escape($filters['tgl_dari']);
        }
        if (!empty($filters['tgl_sampai'])) {
            $where_clauses[] = 'DATE(pu.tanggal_pengajuan) <= ' . $this->db->escape($filters['tgl_sampai']);
        }
        if (!empty($filters['departemen'])) {
            $where_clauses[] = 'k.perusahaan = ' . $this->db->escape($filters['departemen']);
        }
        if (!empty($filters['jenis'])) {
            $where_clauses[] = 't.nama_tipe = ' . $this->db->escape($filters['jenis']);
        }

        $where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

        $sql = "
            SELECT COUNT(*) AS total
            FROM pengajuan_uji pu
            LEFT JOIN kendaraan      k   ON k.id_kendaraan      = pu.id_kendaraan
            LEFT JOIN tipe_kendaraan t   ON t.id_tipe_kendaraan = k.id_tipe_kendaraan
            {$where_sql}
        ";

        $row = $this->db->query($sql)->row();
        return $row ? (int) $row->total : 0;
    }
}
