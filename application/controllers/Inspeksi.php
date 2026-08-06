<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controller Inspeksi
 * 
 * Pengelolaan antarmuka khusus Inspektor (Role ID = 4) & Super Admin (Role ID = 1)
 * untuk melihat daftar antrean pengajuan unit yang siap diinspeksi atau diuji ulang.
 */
class Inspeksi extends CI_Controller
{
    /**
     * Konstruktor Controller Inspeksi
     * Memuat model, library, helper, dan memverifikasi otorisasi Inspektor (4) & Super Admin (1).
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model([
            'Pengajuan_model' => 'pengajuan_model',
            'Checklist_model' => 'checklist_model',
        ]);
        $this->load->library('session');
        $this->load->helper('url');

        if (!$this->session->userdata('id_user')) {
            redirect('auth/login');
        }

        $roles = $this->_user_roles();
        if (!$this->_has_role([1, 4], $roles)) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses ke halaman ini.');
            redirect('dashboard');
        }
    }

    /**
     * Halaman Indeks Daftar Unit Siap Diinspeksi.
     * Mengambil daftar pengajuan berstatus 'dijadwalkan' atau 'inspeksi_ulang' (Eager JOIN).
     * 
     * @return void Render view inspeksi/index
     */
    public function index()
    {
        $id_user  = (int) $this->session->userdata('id_user');
        $roles    = $this->_user_roles();
        $is_admin = in_array(1, $roles, true);

        // Query ter-optimasikan (JOIN 1 kali query)
        $this->db->select('
            pu.id_pengajuan, pu.status, pu.tipe_pengajuan, pu.tipe_akses, pu.tujuan,
            k.no_polisi, t.nama_tipe AS jenis_kendaraan, k.merk, k.tipe, k.tahun,
            u.nama AS nama_pemohon,
            j.id_jadwal, j.tanggal_uji, j.lokasi, j.id_mekanik, j.id_mekanik_master, j.id_inspektor,
            um.nama AS nama_mekanik,
            mm.nama AS nama_mekanik_master, mm.perusahaan AS perusahaan_mekanik_master,
            ui.nama AS nama_inspektor_user,
            uk.id_uji, uk.hasil AS hasil_uji, uk.updated_at AS tgl_uji
        ');
        $this->db->from('pengajuan_uji pu');
        $this->db->join('kendaraan k',       'k.id_kendaraan = pu.id_kendaraan',          'inner');
        $this->db->join('tipe_kendaraan t',  't.id_tipe_kendaraan = k.id_tipe_kendaraan', 'left');
        $this->db->join('users u',           'u.id_user = pu.id_pemohon',                 'left');
        $this->db->join('jadwal_uji j',      'j.id_pengajuan = pu.id_pengajuan',          'left');
        $this->db->join('users um',          'um.id_user = j.id_mekanik',                 'left');
        $this->db->join('mekanik_master mm', 'mm.id_mekanik = j.id_mekanik_master',       'left');
        $this->db->join('users ui',          'ui.id_user = j.id_inspektor',               'left');
        $this->db->join('uji_kelayakan uk',  'uk.id_pengajuan = pu.id_pengajuan',         'left');

        // Pengajuan dalam status dijadwalkan atau inspeksi_ulang
        $this->db->where_in('pu.status', ['dijadwalkan', 'inspeksi_ulang']);

        // Jadwal aktif atau belum terbit jadwal
        $this->db->group_start();
        $this->db->where('j.status', 'scheduled');
        $this->db->or_where('j.id_jadwal IS NULL', null, false);
        $this->db->group_end();

        // Mengurutkan jadwal penugasan pribadi di bagian paling atas
        $this->db->order_by("CASE WHEN j.id_inspektor = {$id_user} THEN 0 ELSE 1 END", 'ASC', false);
        $this->db->order_by("CASE WHEN pu.status = 'inspeksi_ulang' THEN 0 ELSE 1 END", 'ASC', false);
        $this->db->order_by('j.tanggal_uji', 'ASC');

        $data['list_inspeksi'] = $this->db->get()->result();
        $data['title']         = 'Form Inspeksi';
        $data['user']          = $this->session->userdata();

        $this->load->view('templates/header',  $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('inspeksi/index',    $data);
        $this->load->view('templates/footer',  $data);
    }

    /**
     * Helper privat mengambil array ID role pengguna.
     * 
     * @return array Array integer ID role
     */
    private function _user_roles()
    {
        $raw = $this->session->userdata('roles');
        if (is_array($raw) && !empty($raw)) return array_map('intval', $raw);
        $r = (int) $this->session->userdata('role');
        return $r > 0 ? [$r] : [];
    }

    /**
     * Helper privat verifikasi hak akses pengguna.
     * 
     * @param array $required List ID role yang disyaratkan
     * @param array $user_roles List ID role yang dimiliki user
     * @return bool True jika diizinkan
     */
    private function _has_role(array $required, array $user_roles)
    {
        foreach ($required as $r) {
            if (in_array((int) $r, $user_roles, true)) return true;
        }
        return false;
    }
}
