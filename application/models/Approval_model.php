<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Model Approval_model
 * 
 * Pengelolaan antrean dan alur persetujuan (approval) pengajuan uji kelayakan
 * dari berbagai tingkatan (Dept Manager, Admin OHS, OHS Supt, KTT)
 * serta alur perintah dan verifikasi pencabutan stiker.
 */
class Approval_model extends CI_Model
{
    /**
     * Konstruktor Approval_model
     * Inisialisasi library database CodeIgniter.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Helper privat penerapan filter pengguna (pemohon & departemen) pada query builder.
     * 
     * @param array $filters Array filter (id_pemohon, departemen)
     * @return void
     */
    private function _apply_user_filters($filters = [])
    {
        if (!empty($filters['id_pemohon'])) {
            $this->db->where('pu.id_pemohon', (int) $filters['id_pemohon']);
        }
        if (!empty($filters['departemen'])) {
            $this->db->where('k.perusahaan', $filters['departemen']);
        }
    }

    /**
     * Mengambil daftar pengajuan yang memerlukan persetujuan berdasarkan status tertentu.
     * 
     * @param array|string $status_arr Status pengajuan yang difilter
     * @param array $filters Filter tambahan (search, departemen, id_pemohon)
     * @return array List object pengajuan uji
     */
    public function get_list($status_arr, $filters = [])
    {
        $this->db->select('pu.*, k.no_polisi, k.nomor_unit, t.nama_tipe AS jenis_kendaraan, k.merk, k.tipe, k.tahun, k.is_unit_baru, u.nama AS nama_pemohon, u.email AS email_pemohon');
        $this->db->from('pengajuan_uji pu');
        $this->db->join('kendaraan k',      'k.id_kendaraan = pu.id_kendaraan',          'left');
        $this->db->join('tipe_kendaraan t', 't.id_tipe_kendaraan = k.id_tipe_kendaraan', 'left');
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

        $this->db->order_by('pu.tanggal_pengajuan', 'DESC');
        $this->db->order_by('pu.id_pengajuan', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Mengambil data detail pengajuan untuk halaman approval.
     * 
     * @param int $id ID Pengajuan
     * @param array $filters Filter hak akses pengguna
     * @return object|null Object detail pengajuan
     */
    public function get_detail($id, $filters = [])
    {
        $this->db->select('pu.*, k.no_polisi, k.nomor_unit, t.nama_tipe AS jenis_kendaraan, k.merk, k.tipe, k.tahun, k.is_unit_baru, u.nama AS nama_pemohon, u.email AS email_pemohon');
        $this->db->from('pengajuan_uji pu');
        $this->db->join('kendaraan k',      'k.id_kendaraan = pu.id_kendaraan',          'left');
        $this->db->join('tipe_kendaraan t', 't.id_tipe_kendaraan = k.id_tipe_kendaraan', 'left');
        $this->db->join('users u',          'u.id_user = pu.id_pemohon',                 'left');
        $this->db->where('pu.id_pengajuan', (int) $id);
        $this->_apply_user_filters($filters);
        return $this->db->get()->row();
    }

    /**
     * Mengambil riwayat catatan approval suatu pengajuan.
     * 
     * @param int $id_pengajuan ID Pengajuan
     * @return array List object riwayat approval
     */
    public function get_riwayat($id_pengajuan)
    {
        $this->db->select('pa.*, u.nama AS nama_approver');
        $this->db->from('pengajuan_approval pa');
        $this->db->join('users u', 'u.id_user = pa.id_approver', 'left');
        $this->db->where('pa.id_pengajuan', (int) $id_pengajuan);
        $this->db->order_by('pa.id_approval', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Mengambil lampiran dokumen pendukung pengajuan.
     * 
     * @param int $id_pengajuan ID Pengajuan
     * @return array List object lampiran
     */
    public function get_lampiran($id_pengajuan)
    {
        return $this->db->where('id_pengajuan', (int) $id_pengajuan)->get('pengajuan_lampiran')->result();
    }

    /**
     * Memproses tindakan persetujuan (approve) atau penolakan (reject).
     * Melakukan dua operasi atomik (transaksi):
     * 1. Menambahkan catatan riwayat di pengajuan_approval
     * 2. Perbarui status utama pengajuan di pengajuan_uji
     * 
     * @param array $params Parameter ['id_pengajuan', 'id_approver', 'level', 'aksi', 'catatan', 'status_next']
     * @return bool Status keberhasilan transaksi
     */
    public function proses($params)
    {
        extract($params);

        $this->db->trans_start(); // Memulai transaksi database

        // Insert log approval baru
        $this->db->insert('pengajuan_approval', [
            'id_pengajuan'   => (int) $id_pengajuan,
            'id_approver'    => (int) $id_approver,
            'level_approval' => $level,
            'status'         => ($aksi === 'approve') ? 'approved' : 'rejected',
            'catatan'        => $catatan,
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        // Perbarui status pengajuan ke tahap berikutnya
        $this->db->where('id_pengajuan', (int) $id_pengajuan)
            ->update('pengajuan_uji', ['status' => $status_next]);

        $this->db->trans_complete(); // Selesaikan transaksi
        return $this->db->trans_status();
    }

    /**
     * Mengambil daftar hasil inspeksi untuk Admin OHS.
     * Termasuk perhitungan agregat jumlah item 'NO' pada uji_checklist untuk menentukan validasi tombol approve.
     * 
     * @param array|string $status_arr Filter status pengajuan
     * @param array $filters Filter tambahan pencarian
     * @return array List object hasil inspeksi beserta jumlah item NO (count_no)
     */
    public function get_list_hasil($status_arr, $filters = [])
    {
        $this->db->select('pu.*, k.no_polisi, t.nama_tipe AS jenis_kendaraan, k.merk, k.tipe, k.tahun, k.is_unit_baru,
        u.nama AS nama_pemohon, u.email AS email_pemohon,
        uk.id_uji, uk.hasil AS hasil_inspeksi,
        COALESCE(COUNT(CASE WHEN uc.hasil = "no" THEN 1 END), 0) AS count_no');
        $this->db->from('pengajuan_uji pu');
        $this->db->join('kendaraan k',      'k.id_kendaraan = pu.id_kendaraan',          'left');
        $this->db->join('tipe_kendaraan t', 't.id_tipe_kendaraan = k.id_tipe_kendaraan', 'left');
        $this->db->join('users u',          'u.id_user = pu.id_pemohon',                 'left');
        $this->db->join('uji_kelayakan uk', 'uk.id_pengajuan = pu.id_pengajuan',         'left');
        $this->db->join('uji_checklist uc', 'uc.id_uji = uk.id_uji',                    'left');

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
        $this->db->group_by('pu.id_pengajuan');
        $this->db->order_by('pu.tanggal_pengajuan', 'DESC');
        $this->db->order_by('pu.id_pengajuan', 'DESC');
        return $this->db->get()->result();
    }

    // =========================================================
    // PENCABUTAN STIKER WORKFLOW METHODS
    // =========================================================

    /**
     * Membuat pengajuan permintaan pencabutan stiker.
     * Mengatur status awal secara dinamis berdasarkan role pengaju.
     * 
     * @param int $id_sticker ID Stiker
     * @param int $id_pengajuan ID Pengajuan
     * @param int $id_pemohon ID User pemohon pencabutan
     * @param int $role_pemohon ID Role pemohon
     * @param string $alasan Alasan pencabutan stiker
     * @return int ID pencabutan stiker baru
     */
    public function create_request_cabut($id_sticker, $id_pengajuan, $id_pemohon, $role_pemohon, $alasan)
    {
        // Penentuan status_request berdasarkan hierarki role:
        // Role 4 (Inspektor) -> 'menunggu_ohs_supt'
        // Role 3 (OHS Supt)  -> 'menunggu_ktt_1'
        // Role 2 (KTT) / 1 (Admin) -> 'siap_dicabut'
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

    /**
     * Mengambil daftar pencabutan stiker dengan JOIN data pengguna & unit kendaraan.
     * 
     * @param array $filters Filter status_request dan pencarian kata kunci
     * @return array List object pencabutan stiker
     */
    public function get_pencabutan_list($filters = [])
    {
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

    /**
     * Mengambil detail lengkap permintaan pencabutan stiker berdasarkan ID.
     * 
     * @param int $id_cabut ID Pencabutan Stiker
     * @return object|null Object detail pencabutan stiker
     */
    public function get_pencabutan_detail($id_cabut)
    {
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
        $this->db->where('ps.id_cabut', (int) $id_cabut);
        return $this->db->get()->row();
    }
}
