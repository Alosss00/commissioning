<?php
// perubahan
defined('BASEPATH') or exit('No direct script access allowed');
class Pengajuan_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private function _base_query($filters = [])
    {
        $this->db->select(
            'pu.*, '
                . 'k.no_polisi, t.nama_tipe AS jenis_kendaraan, k.merk, k.tipe, k.tahun, '
                . 'k.is_unit_baru, k.nomor_unit, k.model_unit, k.perusahaan, '
                . 'u.nama AS nama_pemohon, u.email AS email_user'
        );
        $this->db->from('pengajuan_uji pu');
        $this->db->join('kendaraan k',        'k.id_kendaraan = pu.id_kendaraan',              'left');
        $this->db->join('tipe_kendaraan t',   't.id_tipe_kendaraan = k.id_tipe_kendaraan',     'left'); // ← tambah ini
        $this->db->join('users u',            'u.id_user = pu.id_pemohon',                     'left');

        if (!empty($filters['status']))      $this->db->where('pu.status', $filters['status']);
        if (!empty($filters['jenis']))       $this->db->where('t.nama_tipe', $filters['jenis']); // ← ganti k.jenis_kendaraan
        if (!empty($filters['tgl_dari']))    $this->db->where('DATE(pu.tanggal_pengajuan) >=', $filters['tgl_dari']);
        if (!empty($filters['tgl_sampai'])) $this->db->where('DATE(pu.tanggal_pengajuan) <=', $filters['tgl_sampai']);
        if (!empty($filters['id_pemohon'])) $this->db->where('pu.id_pemohon', $filters['id_pemohon']);
        if (!empty($filters['departemen']))  $this->db->where('k.perusahaan', $filters['departemen']);

        if (!empty($filters['search'])) {
            $kw = $filters['search'];
            $this->db->group_start();
            $this->db->like('k.no_polisi',       $kw);
            $this->db->or_like('k.nomor_unit',    $kw);
            $this->db->or_like('u.nama',          $kw);
            $this->db->or_like('t.nama_tipe',     $kw);
            $this->db->or_like('k.merk',          $kw);
            $this->db->or_like('k.tipe',          $kw);
            $this->db->group_end();
        }
    }
    public function count_all($filters = [])
    {
        // Hanya scoping akses (id_pemohon/departemen) yang ikut,
        // filter UI (status/jenis/tanggal/search) TIDAK ikut di sini
        // supaya recordsTotal = total data yang boleh dilihat user,
        // bukan total data yang sudah difilter.
        $scope_only = array_intersect_key($filters, array_flip(['id_pemohon', 'departemen']));
        $this->_base_query($scope_only);
        return $this->db->count_all_results();
    }

    public function count_filtered($filters = [])
    {
        $this->_base_query($filters);
        return $this->db->count_all_results();
    }

    public function get_datatable($start, $length, $filters = [])
    {
        $this->_base_query($filters);
        $this->db->order_by('pu.tanggal_pengajuan', 'DESC');
        $this->db->limit($length, $start);
        return $this->db->get()->result();
    }

    public function get_detail($id, $filters = [])
    {
        $this->db->select(
            'pu.*, '
                . 'k.no_polisi, k.id_tipe_kendaraan, t.nama_tipe AS jenis_kendaraan, ' // ← tambah k.id_tipe_kendaraan
                . 'k.merk, k.tipe, k.tahun, '
                . 'k.is_unit_baru, k.nomor_unit, k.model_unit, k.perusahaan, '
                . 'u.nama AS nama_pemohon, u.email AS email_user'
        );
        $this->db->from('pengajuan_uji pu');
        $this->db->join('kendaraan k',      'k.id_kendaraan = pu.id_kendaraan',          'left');
        $this->db->join('tipe_kendaraan t', 't.id_tipe_kendaraan = k.id_tipe_kendaraan', 'left');
        $this->db->join('users u',          'u.id_user = pu.id_pemohon',                 'left');
        $this->db->where('pu.id_pengajuan', $id);
        if (!empty($filters['departemen'])) {
            $this->db->where('k.perusahaan', $filters['departemen']);
        }
        if (!empty($filters['id_pemohon'])) {
            $this->db->where('pu.id_pemohon', $filters['id_pemohon']);
        }
        return $this->db->get()->row();
    }

    public function insert_pengajuan($data)
    {
        $this->db->insert('pengajuan_uji', $data);
        return $this->db->insert_id();
    }

    public function delete_pengajuan($id)
    {
        return $this->db->where('id_pengajuan', $id)->delete('pengajuan_uji');
    }

    public function insert_lampiran($data)
    {
        $this->db->insert('pengajuan_lampiran', $data);
        return $this->db->insert_id();
    }

    public function get_lampiran($id)
    {
        return $this->db->where('id_pengajuan', $id)->get('pengajuan_lampiran')->result();
    }

    public function insert_approval($data)
    {
        $this->db->insert('pengajuan_approval', $data);
        return $this->db->insert_id();
    }

    public function get_approval($id)
    {
        $this->db->select('pa.*, u.nama AS nama_approver');
        $this->db->from('pengajuan_approval pa');
        $this->db->join('users u', 'u.id_user = pa.id_approver', 'left');
        $this->db->where('pa.id_pengajuan', $id);
        $this->db->order_by('pa.id_approval', 'ASC');
        return $this->db->get()->result();
    }

    public function get_jadwal($id)
    {
        $this->db->select('j.*, u_dibuat.nama AS dibuat_oleh_nama,
            u_ins.nama     AS nama_inspektor_user,
            mm.nama        AS nama_mekanik_master,
            mm.perusahaan  AS perusahaan_mekanik');
        $this->db->from('jadwal_uji j');
        $this->db->join('users u_dibuat',    'u_dibuat.id_user = j.dibuat_oleh', 'left');
        $this->db->join('users u_ins',       'u_ins.id_user = COALESCE(j.id_inspektor, j.id_mekanik)', 'left');
        $this->db->join('mekanik_master mm', 'mm.id_mekanik = j.id_mekanik_master', 'left');
        $this->db->where('j.id_pengajuan', $id);
        return $this->db->get()->row();
    }

    public function get_uji($id)
    {
        $this->db->select('uk.*, u.nama AS nama_mekanik,
            mm.nama       AS nama_mekanik_master,
            mm.perusahaan AS perusahaan_mekanik_master');
        $this->db->from('uji_kelayakan uk');
        $this->db->join('users u',           'u.id_user = uk.id_mekanik',          'left');
        $this->db->join('mekanik_master mm', 'mm.id_mekanik = uk.id_mekanik_master', 'left');
        $this->db->where('uk.id_pengajuan', $id);
        return $this->db->get()->row();
    }

    public function count_by_status($status)
    {
        return $this->db->where('status', $status)->count_all_results('pengajuan_uji');
    }

    public function get_export_history_data($filters = [])
    {
        $where_clauses = [];

        if (!empty($filters['status'])) {
            $where_clauses[] = "pu.status = " . $this->db->escape($filters['status']);
        }
        if (!empty($filters['jenis'])) {
            $where_clauses[] = "t.nama_tipe = " . $this->db->escape($filters['jenis']);
        }
        if (!empty($filters['departemen'])) {
            $where_clauses[] = "k.perusahaan = " . $this->db->escape($filters['departemen']);
        }
        if (!empty($filters['tgl_dari'])) {
            $where_clauses[] = "DATE(pu.tanggal_pengajuan) >= " . $this->db->escape($filters['tgl_dari']);
        }
        if (!empty($filters['tgl_sampai'])) {
            $where_clauses[] = "DATE(pu.tanggal_pengajuan) <= " . $this->db->escape($filters['tgl_sampai']);
        }
        if (!empty($filters['search'])) {
            $kw = $this->db->escape_like_str($filters['search']);
            $where_clauses[] = "(k.no_polisi LIKE '%$kw%' OR k.nomor_unit LIKE '%$kw%' OR u_pem.nama LIKE '%$kw%' OR t.nama_tipe LIKE '%$kw%' OR k.merk LIKE '%$kw%' OR k.perusahaan LIKE '%$kw%')";
        }

        $where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

        $sql = "
            SELECT 
                pu.id_pengajuan,
                pu.tanggal_pengajuan,
                pu.tipe_pengajuan,
                pu.tipe_akses,
                pu.tujuan,
                pu.status,
                sr.nomor_sticker AS nomor_stiker,
                sr.tanggal_release AS tanggal_rilis_stiker,
                sr.tgl_expired AS tgl_expired_stiker,
                k.no_polisi,
                k.nomor_unit,
                k.merk,
                k.model_unit,
                k.tipe,
                k.tahun,
                k.perusahaan,
                t.nama_tipe AS jenis_kendaraan,
                u_pem.nama AS nama_pemohon,
                u_pem.email AS email_pemohon,
                j.tgl_rencana AS tgl_jadwal_rencana,
                j.created_at AS tgl_jadwal_dibuat,
                COALESCE(uk.nama_inspektor, mm.nama) AS nama_mekanik,
                COALESCE(uk.perusahaan_inspektor, mm.perusahaan) AS perusahaan_mekanik,
                uk.hasil AS hasil_inspeksi,
                uk.catatan_temuan AS catatan_inspeksi,
                uk.tanggal_uji AS tgl_inspeksi,
                pa_mgr.created_at AS tgl_approve_mgr,
                pa_mgr.catatan AS catatan_mgr,
                pa_ohs.created_at AS tgl_approve_ohs,
                pa_ohs.catatan AS catatan_ohs
            FROM pengajuan_uji pu
            LEFT JOIN kendaraan k ON k.id_kendaraan = pu.id_kendaraan
            LEFT JOIN tipe_kendaraan t ON t.id_tipe_kendaraan = k.id_tipe_kendaraan
            LEFT JOIN users u_pem ON u_pem.id_user = pu.id_pemohon
            LEFT JOIN (
                SELECT id_pengajuan, MAX(id_jadwal) AS max_id_jadwal
                FROM jadwal_uji GROUP BY id_pengajuan
            ) jl ON jl.id_pengajuan = pu.id_pengajuan
            LEFT JOIN jadwal_uji j ON j.id_jadwal = jl.max_id_jadwal
            LEFT JOIN mekanik_master mm ON mm.id_mekanik = j.id_mekanik_master
            LEFT JOIN (
                SELECT id_pengajuan, MAX(id_uji) AS max_id_uji
                FROM uji_kelayakan GROUP BY id_pengajuan
            ) ul ON ul.id_pengajuan = pu.id_pengajuan
            LEFT JOIN uji_kelayakan uk ON uk.id_uji = ul.max_id_uji
            LEFT JOIN (
                SELECT id_pengajuan, MAX(id_sticker) AS max_id_sticker
                FROM sticker_release GROUP BY id_pengajuan
            ) sl ON sl.id_pengajuan = pu.id_pengajuan
            LEFT JOIN sticker_release sr ON sr.id_sticker = sl.max_id_sticker
            LEFT JOIN (
                SELECT id_pengajuan, MAX(id_approval) AS max_id_app
                FROM pengajuan_approval WHERE level_approval = 'dept_manager' GROUP BY id_pengajuan
            ) pal_mgr ON pal_mgr.id_pengajuan = pu.id_pengajuan
            LEFT JOIN pengajuan_approval pa_mgr ON pa_mgr.id_approval = pal_mgr.max_id_app
            LEFT JOIN (
                SELECT id_pengajuan, MAX(id_approval) AS max_id_app
                FROM pengajuan_approval WHERE level_approval = 'ohs_supt' GROUP BY id_pengajuan
            ) pal_ohs ON pal_ohs.id_pengajuan = pu.id_pengajuan
            LEFT JOIN pengajuan_approval pa_ohs ON pa_ohs.id_approval = pal_ohs.max_id_app
            {$where_sql}
            ORDER BY pu.tanggal_pengajuan DESC, pu.id_pengajuan DESC
        ";

        return $this->db->query($sql)->result();
    }
}
