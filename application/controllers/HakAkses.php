<?php
defined('BASEPATH') or exit('No direct script access allowed');

class HakAkses extends CI_Controller
{

    // Matrix hak akses per role — sumber kebenaran tunggal
    // Format: 'nama_akses' => [role_ids_yang_punya_akses]
    private $_matrix = [
        'Pengajuan' => [
            'Buat Pengajuan'              => [1, 2],
            'Lihat Daftar Pengajuan'      => [1, 2, 3, 4, 5],
            'Edit Pengajuan (Draft)'      => [1, 2],
            'Submit Pengajuan'            => [1, 2],
        ],
        'Approval' => [
            'Approve / Tolak (Manager)'   => [1, 2],
            'Approve / Tolak (Admin OHS)' => [1, 4],
            'Review Hasil Inspeksi'       => [1, 4],
            'Approve / Tolak (OHS Supt)'  => [1, 4],
            'Approve / Tolak (KTT)'       => [1, 5],
        ],
        'Uji Kelayakan' => [
            'Buat & Kelola Jadwal'        => [1, 4],
            'Isi Form Inspeksi'           => [1, 3],
            'Lihat Detail Checklist'      => [1, 3, 4, 5],
        ],
        'Kendaraan' => [
            'Lihat Data Kendaraan'        => [1, 2, 3, 4, 5],
            'Tambah / Edit Kendaraan'     => [1, 2, 4],
        ],
        'Master Data' => [
            'Kelola Template Checklist'   => [1],
            'Kelola Tipe Kendaraan'       => [1],
            'Kelola Perusahaan'           => [1],
        ],
        'Administrasi' => [
            'Manajemen User'              => [1],
            'Atur Hak Akses'              => [1],
            'Lihat Audit Log'             => [1],
        ],
        'Akun' => [
            'Edit Profil Sendiri'         => [1, 2, 3, 4, 5],
            'Ganti Password'              => [1, 2, 3, 4, 5],
            'Upload Foto Profil'          => [1, 2, 3, 4, 5],
        ],
    ];

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
        if (!$this->session->userdata('id_user')) redirect('auth/login');
        if ((int)$this->session->userdata('role') !== 1) {
            $this->session->set_flashdata('error', 'Akses ditolak.');
            redirect('dashboard');
        }
    }

    public function index()
    {
        $roles = $this->db->order_by('id_role', 'ASC')->get('roles')->result();

        $data['title']  = 'Hak Akses';
        $data['user']   = $this->session->userdata();
        $data['roles']  = $roles;
        $data['matrix'] = $this->_matrix;

        $this->load->view('templates/header',   $data);
        $this->load->view('templates/sidebar',  $data);
        $this->load->view('hakakses/index',    $data);
        $this->load->view('templates/footer',   $data);
    }
}
