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
        $this->db->select('pu.*, k.no_polisi, k.nomor_unit, t.nama_tipe AS jenis_kendaraan, k.merk, k.tipe, k.tahun, k.is_unit_baru, u.nama AS nama_pemohon, u.email AS email_pemohon');
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
            $this->db->or_like('k.nomor_unit', $kw);
            $this->db->or_like('u.nama',   $kw);
            $this->db->group_end();
        }
        $this->db->order_by('pu.tanggal_pengajuan', 'ASC');
        return $this->db->get()->result();
    }

    public function get_detail($id, $filters = [])
    {
        $this->db->select('pu.*, k.no_polisi, k.nomor_unit, t.nama_tipe AS jenis_kendaraan, k.merk, k.tipe, k.tahun, k.is_unit_baru, u.nama AS nama_pemohon, u.email AS email_pemohon');
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
        $this->db->join('tipe_kendaraan t', 't.id_tipe_kendaraan = k.id_tipe_kendaraan', 'left');
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

    // =========================================================
    // PENCABUTAN STIKER WORKFLOW METHODS
    // =========================================================

    public function check_pencabutan_schema()
    {
        if (!$this->db->table_exists('pencabutan_stiker')) return;
        
        $fields = [
            'id_pemohon'       => "INT NULL AFTER id_pengajuan",
            'role_pemohon'     => "INT NULL AFTER id_pemohon",
            'status_request'   => "VARCHAR(50) NOT NULL DEFAULT 'menunggu_ohs_supt' AFTER alasan",
            'ohs_supt_by'      => "INT NULL AFTER status_request",
            'ohs_supt_at'      => "DATETIME NULL AFTER ohs_supt_by",
            'ktt_1_by'         => "INT NULL AFTER ohs_supt_at",
            'ktt_1_at'         => "DATETIME NULL AFTER ktt_1_by",
            'ktt_2_by'         => "INT NULL AFTER ktt_1_at",
            'ktt_2_at'         => "DATETIME NULL AFTER ktt_2_by",
            'catatan_penolakan' => "TEXT NULL AFTER ktt_2_at",
        ];

        foreach ($fields as $field => $def) {
            if (!$this->db->field_exists($field, 'pencabutan_stiker')) {
                @$this->db->query("ALTER TABLE `pencabutan_stiker` ADD COLUMN `$field` $def");
            }
        }
    }

    public function create_request_cabut($id_sticker, $id_pengajuan, $id_pemohon, $role_pemohon, $alasan)
    {
        $this->check_pencabutan_schema();

        // Tentukan initial status_request berdasarkan role pemohon:
        // Kondisi 1: Inspektor (role 4) -> 'menunggu_ohs_supt'
        // Kondisi 2: OHS Supt (role 3)  -> 'menunggu_ktt_1'
        // Kondisi 3: KTT (role 2) / Admin (role 1) -> 'siap_dicabut'
        if ((int)$role_pemohon === 4) {
            $status_req = 'menunggu_ohs_supt';
        } elseif ((int)$role_pemohon === 3) {
            $status_req = 'menunggu_ktt_1';
        } else {
            $status_req = 'siap_dicabut';
        }

        $data = [
            'id_sticker'     => (int)$id_sticker,
            'id_pengajuan'   => (int)$id_pengajuan,
            'id_pemohon'     => (int)$id_pemohon,
            'role_pemohon'   => (int)$role_pemohon,
            'id_ktt'         => ((int)$role_pemohon === 2) ? (int)$id_pemohon : NULL,
            'alasan'         => $alasan,
            'status_request' => $status_req,
            'tgl_perintah'   => date('Y-m-d H:i:s'),
            'status'         => 'diperintahkan',
        ];

        $this->db->insert('pencabutan_stiker', $data);
        return $this->db->insert_id();
    }

    public function get_pencabutan_list($filters = [])
    {
        $this->check_pencabutan_schema();

        $this->db->select('ps.*, sr.nomor_sticker, sr.tanggal_release AS tgl_terbit, sr.tgl_expired AS berlaku_sampai,
                           pu.id_pengajuan, pu.id_pemohon AS id_pemohon_pengajuan,
                           k.no_polisi, k.nomor_unit, k.merk, k.tipe, k.perusahaan, t.nama_tipe AS jenis_kendaraan,
                           u_pem.nama AS nama_pemungut_cabut, u_pem.email AS email_pemungut_cabut,
                           u_ohs.nama AS nama_ohs_supt, u_ktt1.nama AS nama_ktt_1, u_ktt2.nama AS nama_ktt_2,
                           u_eks.nama AS nama_eksekutor');
        $this->db->from('pencabutan_stiker ps');
        $this->db->join('sticker_release sr', 'sr.id_sticker = ps.id_sticker',     'left');
        $this->db->join('pengajuan_uji pu',   'pu.id_pengajuan = ps.id_pengajuan', 'left');
        $this->db->join('kendaraan k',        'k.id_kendaraan = pu.id_kendaraan',   'left');
        $this->db->join('tipe_kendaraan t',   't.id_tipe_kendaraan = k.id_tipe_kendaraan', 'left');
        $this->db->join('users u_pem',        'u_pem.id_user = ps.id_pemohon',     'left');
        $this->db->join('users u_ohs',        'u_ohs.id_user = ps.ohs_supt_by',    'left');
        $this->db->join('users u_ktt1',       'u_ktt1.id_user = ps.ktt_1_by',      'left');
        $this->db->join('users u_ktt2',       'u_ktt2.id_user = ps.ktt_2_by',      'left');
        $this->db->join('users u_eks',        'u_eks.id_user = ps.dilaksanakan_oleh', 'left');

        if (!empty($filters['status_request'])) {
            if (is_array($filters['status_request'])) {
                $this->db->where_in('ps.status_request', $filters['status_request']);
            } else {
                $this->db->where('ps.status_request', $filters['status_request']);
            }
        }

        if (!empty($filters['search'])) {
            $kw = $filters['search'];
            $this->db->group_start();
            $this->db->like('sr.nomor_sticker', $kw);
            $this->db->or_like('k.no_polisi',   $kw);
            $this->db->or_like('k.nomor_unit',  $kw);
            $this->db->or_like('u_pem.nama',    $kw);
            $this->db->group_end();
        }

        $this->db->order_by('ps.id_cabut', 'DESC');
        return $this->db->get()->result();
    }

    public function get_pencabutan_detail($id_cabut)
    {
        $this->check_pencabutan_schema();

        $this->db->select('ps.*, sr.nomor_sticker, sr.tanggal_release AS tgl_terbit, sr.tgl_expired AS berlaku_sampai,
                           pu.id_pengajuan, pu.id_pemohon AS id_pemohon_pengajuan, pu.email_pemohon,
                           k.no_polisi, k.nomor_unit, k.merk, k.tipe, k.perusahaan, t.nama_tipe AS jenis_kendaraan,
                           u_pem.nama AS nama_pemungut_cabut, u_pem.email AS email_pemungut_cabut');
        $this->db->from('pencabutan_stiker ps');
        $this->db->join('sticker_release sr', 'sr.id_sticker = ps.id_sticker',     'left');
        $this->db->join('pengajuan_uji pu',   'pu.id_pengajuan = ps.id_pengajuan', 'left');
        $this->db->join('kendaraan k',        'k.id_kendaraan = pu.id_kendaraan',   'left');
        $this->db->join('tipe_kendaraan t',   't.id_tipe_kendaraan = k.id_tipe_kendaraan', 'left');
        $this->db->join('users u_pem',        'u_pem.id_user = ps.id_pemohon',     'left');
        $this->db->where('ps.id_cabut', (int)$id_cabut);
        return $this->db->get()->row();
    }
}

