<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Audit_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Ambil data audit log terfilter
     */
    public function get_filtered_logs($filter = [])
    {
        $this->db->select('al.*, u.nama AS nama_user, u.username, u.email AS user_email')
            ->from('audit_log al')
            ->join('users u', 'u.id_user = al.id_user', 'left');

        // Filter berdasarkan tanggal spesifik (Hari)
        if (!empty($filter['tanggal'])) {
            $this->db->where('DATE(al.created_at)', $filter['tanggal']);
        }

        // Filter berdasarkan Bulan & Tahun
        if (!empty($filter['bulan']) && !empty($filter['tahun'])) {
            $this->db->where('MONTH(al.created_at)', (int)$filter['bulan']);
            $this->db->where('YEAR(al.created_at)', (int)$filter['tahun']);
        } elseif (!empty($filter['tahun'])) {
            // Filter Tahun saja
            $this->db->where('YEAR(al.created_at)', (int)$filter['tahun']);
        }

        // Filter Pengguna / User
        if (!empty($filter['id_user'])) {
            $this->db->where('al.id_user', (int)$filter['id_user']);
        }

        // Filter Jenis Aksi
        if (!empty($filter['aksi'])) {
            $this->db->where('al.aksi', $filter['aksi']);
        }

        // Search kata kunci (pencarian serbaguna)
        if (!empty($filter['search'])) {
            $s = $filter['search'];
            $this->db->group_start()
                ->like('u.nama', $s)
                ->or_like('al.aksi', $s)
                ->or_like('al.tabel', $s)
                ->or_like('al.id_ref', $s)
                ->group_end();
        }

        $this->db->order_by('al.created_at', 'DESC');

        if (!empty($filter['limit'])) {
            $offset = !empty($filter['offset']) ? (int)$filter['offset'] : 0;
            $this->db->limit((int)$filter['limit'], $offset);
        }

        return $this->db->get()->result();
    }

    /**
     * Hitung total log terfilter (untuk statistik/pagination)
     */
    public function count_filtered_logs($filter = [])
    {
        $this->db->from('audit_log al')
            ->join('users u', 'u.id_user = al.id_user', 'left');

        if (!empty($filter['tanggal'])) {
            $this->db->where('DATE(al.created_at)', $filter['tanggal']);
        }
        if (!empty($filter['bulan']) && !empty($filter['tahun'])) {
            $this->db->where('MONTH(al.created_at)', (int)$filter['bulan']);
            $this->db->where('YEAR(al.created_at)', (int)$filter['tahun']);
        } elseif (!empty($filter['tahun'])) {
            $this->db->where('YEAR(al.created_at)', (int)$filter['tahun']);
        }
        if (!empty($filter['id_user'])) {
            $this->db->where('al.id_user', (int)$filter['id_user']);
        }
        if (!empty($filter['aksi'])) {
            $this->db->where('al.aksi', $filter['aksi']);
        }
        if (!empty($filter['search'])) {
            $s = $filter['search'];
            $this->db->group_start()
                ->like('u.nama', $s)
                ->or_like('al.aksi', $s)
                ->or_like('al.tabel', $s)
                ->or_like('al.id_ref', $s)
                ->group_end();
        }

        return $this->db->count_all_results();
    }

    /**
     * Ambil daftar semua user untuk dropdown filter
     */
    public function get_all_users()
    {
        return $this->db->select('id_user, nama, username')
            ->from('users')
            ->order_by('nama', 'ASC')
            ->get()->result();
    }

    /**
     * Ambil daftar jenis aksi unik dari audit_log
     */
    public function get_distinct_actions()
    {
        $rows = $this->db->select('DISTINCT(aksi) as aksi')
            ->from('audit_log')
            ->order_by('aksi', 'ASC')
            ->get()->result();

        return array_column($rows, 'aksi');
    }

    /**
     * Ambil daftar tahun unik dari audit_log
     */
    public function get_distinct_years()
    {
        $rows = $this->db->select('DISTINCT(YEAR(created_at)) as tahun')
            ->from('audit_log')
            ->order_by('tahun', 'DESC')
            ->get()->result();

        $years = array_filter(array_column($rows, 'tahun'));
        if (empty($years)) {
            $years = [date('Y')];
        }
        return $years;
    }
}
