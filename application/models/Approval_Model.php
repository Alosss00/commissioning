<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Approval_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private function _apply_user_filters($filters = [])
    {
        if (!empty($filters['id_pemohon'])) {
            $this->db->where('pu.id_pemohon', $filters['id_pemohon']);
        }
        if (!empty($filters['departemen'])) {
            $this->db->where('k.perusahaan', $filters['departemen']);
        }
    }

    public function get_list($status_arr, $filters = [])
    {
        $this->db->select('pu.*, k.no_polisi, t.nama_tipe AS jenis_kendaraan, k.merk, k.tipe, k.tahun, k.is_unit_baru, u.nama AS nama_pemohon, u.email AS email_pemohon');
        $this->db->from('pengajuan_uji pu');
        $this->db->join('kendaraan k',      'k.id_kendaraan = pu.id_kendaraan',          'left');
        $this->db->join('tipe_kendaraan t', 't.id_tipe_kendaraan = k.id_tipe_kendaraan', 'left'); // ← tambah
        $this->db->join('users u',          'u.id_user = pu.id_pemohon',                 'left');

        if (is_array($status_arr) && !empty($status_arr)) {
            $this->db->where_in('pu.status', $status_arr);
        } elseif (!empty($status_arr)) {
            $this->db->where('pu.status', $status_arr);
        }
        $this->_apply_user_filters($filters);
        if (!empty($filters['search'])) {
            $kw = $filters['search'];
            $this->db->group_start();
            $this->db->like('k.no_polisi', $kw);
            $this->db->or_like('u.nama',   $kw);
            $this->db->group_end();
        }
        $this->db->order_by('pu.tanggal_pengajuan', 'ASC');
        return $this->db->get()->result();
    }

    public function get_detail($id, $filters = [])
    {
        $this->db->select('pu.*, k.no_polisi, t.nama_tipe AS jenis_kendaraan, k.merk, k.tipe, k.tahun, k.is_unit_baru, u.nama AS nama_pemohon, u.email AS email_pemohon');
        $this->db->from('pengajuan_uji pu');
        $this->db->join('kendaraan k',      'k.id_kendaraan = pu.id_kendaraan',          'left');
        $this->db->join('tipe_kendaraan t', 't.id_tipe_kendaraan = k.id_tipe_kendaraan', 'left'); // ← tambah
        $this->db->join('users u',          'u.id_user = pu.id_pemohon',                 'left');
        $this->db->where('pu.id_pengajuan', $id);
        $this->_apply_user_filters($filters);
        return $this->db->get()->row();
    }

    public function get_riwayat($id_pengajuan)
    {
        $this->db->select('pa.*, u.nama AS nama_approver');
        $this->db->from('pengajuan_approval pa');
        $this->db->join('users u', 'u.id_user = pa.id_approver', 'left');
        $this->db->where('pa.id_pengajuan', $id_pengajuan);
        $this->db->order_by('pa.id_approval', 'ASC');
        return $this->db->get()->result();
    }

    public function get_lampiran($id_pengajuan)
    {
        return $this->db->where('id_pengajuan', $id_pengajuan)->get('pengajuan_lampiran')->result();
    }

    /**
     * Proses approve / reject.
     * Alur bolak-balik dihandle murni lewat status:
     *  - ditolak_admin_ohs  → kembali ke queue dept_manager
     *  - ditolak_ohs_supt   → kembali ke queue Admin OHS  
     *  - ditolak_ktt        → kembali ke queue Admin OHS
     * Tidak ada FK ke record approval lama — setiap aksi insert baru.
     */
    public function proses($params)
    {
        extract($params);

        $this->db->trans_start();

        $this->db->insert('pengajuan_approval', [
            'id_pengajuan'   => $id_pengajuan,
            'id_approver'    => $id_approver,
            'level_approval' => $level,
            'status'         => ($aksi === 'approve') ? 'approved' : 'rejected',
            'catatan'        => $catatan,
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        $this->db->where('id_pengajuan', $id_pengajuan)
            ->update('pengajuan_uji', ['status' => $status_next]);

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * get_list untuk admin_ohs_hasil — tambahkan count item NO per pengajuan
     * agar view bisa sembunyikan tombol approve jika ada item NO
     */
    public function get_list_hasil($status_arr, $filters = [])
    {
        $this->db->select('pu.*, k.no_polisi, t.nama_tipe AS jenis_kendaraan, k.merk, k.tipe, k.tahun, k.is_unit_baru,
        u.nama AS nama_pemohon, u.email AS email_pemohon,
        uk.id_uji, uk.hasil AS hasil_inspeksi,
        COALESCE((SELECT COUNT(*) FROM uji_checklist uc WHERE uc.id_uji = uk.id_uji AND uc.hasil = "no"), 0) AS count_no');
        $this->db->from('pengajuan_uji pu');
        $this->db->join('kendaraan k',      'k.id_kendaraan = pu.id_kendaraan',          'left');
        $this->db->join('tipe_kendaraan t', 't.id_tipe_kendaraan = k.id_tipe_kendaraan', 'left'); // ← tambah
        $this->db->join('users u',          'u.id_user = pu.id_pemohon',                 'left');
        $this->db->join('uji_kelayakan uk', 'uk.id_pengajuan = pu.id_pengajuan',         'left');

        if (is_array($status_arr) && !empty($status_arr)) {
            $this->db->where_in('pu.status', $status_arr);
        }
        $this->_apply_user_filters($filters);
        if (!empty($filters['search'])) {
            $kw = $filters['search'];
            $this->db->group_start();
            $this->db->like('k.no_polisi', $kw);
            $this->db->or_like('u.nama',   $kw);
            $this->db->group_end();
        }
        $this->db->order_by('pu.tanggal_pengajuan', 'ASC');
        return $this->db->get()->result();
    }
}
