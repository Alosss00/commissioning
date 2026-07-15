<?php

/**
 * Mekanik Controller
 * Tujuan   : CRUD master mekanik/teknisi lapangan + assign tipe kendaraan
 * Caller   : Route /mekanik/*
 * Dependen : Mekanik_Model, tipe_kendaraan, session
 * Fungsi   :
 *   index()          — daftar mekanik + filter
 *   form($id)        — form tambah/edit mekanik
 *   save()           — simpan mekanik + tipe kendaraan (array id_tipe_kendaraan)
 *   toggle()         — AJAX toggle aktif/nonaktif
 *   delete()         — AJAX hapus (validasi jadwal aktif)
 *   get_available()  — AJAX mekanik tersedia untuk tipe & tanggal (form jadwal)
 * Side effect:
 *   - READ: mekanik_master, mekanik_tipe_kendaraan, tipe_kendaraan, jadwal_uji
 *   - WRITE: mekanik_master, mekanik_tipe_kendaraan
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Mekanik extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Mekanik_Model', 'mekanik_model');
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['url', 'form']);

        if (!$this->session->userdata('id_user')) redirect('auth/login');

        $roles = $this->_roles();
        if (!$this->_has([1, 5, 8], $roles)) {
            $this->session->set_flashdata('error', 'Akses ditolak.');
            redirect('dashboard');
        }
    }

    // ── INDEX ─────────────────────────────────────────────────────────────────
    public function index()
    {
        $filter = [
            'search'    => $this->input->get('search'),
            'is_active' => $this->input->get('status') ?? '',
        ];

        $data = [
            'title'   => 'Master Data Mekanik',
            'user'    => $this->session->userdata(),
            'list'    => $this->mekanik_model->get_all($filter),
            'filter'  => $filter,
        ];

        $this->load->view('templates/header',  $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('mekanik/index',     $data);
        $this->load->view('templates/footer',  $data);
    }

    // ── FORM TAMBAH / EDIT ────────────────────────────────────────────────────
    public function form($id = null)
    {
        $mekanik    = $id ? $this->mekanik_model->get_by_id($id) : null;

        // Tipe yang sudah dimiliki mekanik — array id_tipe_kendaraan (int)
        $tipe_exist_raw = $id
            ? $this->mekanik_model->get_tipe_by_mekanik($id)
            : [];
        $tipe_exist = array_column($tipe_exist_raw, 'id_tipe_kendaraan');

        // Semua tipe aktif dari master — untuk checkbox list
        $semua_tipe = $this->db
            ->select('id_tipe_kendaraan, nama_tipe, kode_tipe')
            ->where('is_active', 1)
            ->order_by('nama_tipe', 'ASC')
            ->get('tipe_kendaraan')->result();

        $semua_perusahaan = $this->db
            ->select('nama_perusahaan')
            ->where('is_active', 1)
            ->order_by('nama_perusahaan', 'ASC')
            ->get('perusahaan')->result();

        $data = [
            'title'            => $id ? 'Edit Mekanik' : 'Tambah Mekanik',
            'user'             => $this->session->userdata(),
            'mekanik'          => $mekanik,
            'tipe_exist'       => $tipe_exist,   // array of id_tipe_kendaraan
            'semua_tipe'       => $semua_tipe,   // semua tipe untuk checkbox
            'semua_perusahaan' => $semua_perusahaan,
        ];

        $this->load->view('templates/header',  $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('mekanik/form',      $data);
        $this->load->view('templates/footer',  $data);
    }

    // ── SAVE (POST dari form) ─────────────────────────────────────────────────
    // tipe_kendaraan[] dikirim sebagai array id_tipe_kendaraan (int)
    public function save()
    {
        $id       = (int) $this->input->post('id_mekanik');
        // Ambil array id_tipe_kendaraan dari form checkbox
        $tipe_arr = array_map('intval', (array) $this->input->post('tipe_kendaraan'));
        $tipe_arr = array_filter($tipe_arr); // buang 0

        $payload = [
            'nama'       => trim($this->input->post('nama')),
            'no_hp'      => trim($this->input->post('no_hp')),
            'email'      => trim($this->input->post('email')),
            'perusahaan' => trim($this->input->post('perusahaan')),
            'jabatan'    => trim($this->input->post('jabatan')),
            'is_active'  => 1,
        ];

        if (empty($payload['nama'])) {
            $this->session->set_flashdata('error', 'Nama mekanik wajib diisi.');
            redirect($id ? 'mekanik_master/form/' . $id : 'mekanik_master/form');
            return;
        }

        if ($id) {
            $ok  = $this->mekanik_model->update($id, $payload, $tipe_arr);
            $msg = 'Data mekanik berhasil diperbarui.';
        } else {
            $ok  = $this->mekanik_model->insert($payload, $tipe_arr);
            $msg = 'Mekanik baru berhasil ditambahkan.';
        }

        if ($ok) {
            $this->session->set_flashdata('success', $msg);
        } else {
            $this->session->set_flashdata('error', 'Gagal menyimpan data.');
        }
        redirect('mekanik_master');
    }

    // ── TOGGLE ACTIVE (AJAX) ──────────────────────────────────────────────────
    public function toggle()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $id = (int) $this->input->post('id');
        $ok = $this->mekanik_model->toggle_active($id);
        echo json_encode(['status' => $ok ? 'success' : 'error']);
    }

    // ── DELETE (AJAX) ─────────────────────────────────────────────────────────
    public function delete()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $id = (int) $this->input->post('id');

        $in_use = $this->db
            ->where('id_mekanik', $id)
            ->where('status', 'scheduled')
            ->count_all_results('jadwal_uji');

        if ($in_use > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Mekanik masih terjadwal — tidak bisa dihapus.']);
            return;
        }

        $this->mekanik_model->delete($id);
        echo json_encode(['status' => 'success', 'message' => 'Mekanik berhasil dihapus.']);
    }

    // ── AJAX: mekanik tersedia by tipe & tanggal (untuk form jadwal) ──────────
    public function get_available()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $nama_tipe   = $this->input->post('jenis_kendaraan'); // masih nama untuk compat
        $tanggal_uji = $this->input->post('tanggal_uji');
        $exclude_id  = (int) $this->input->post('exclude_jadwal_id');

        // Gunakan get_by_jenis — JOIN nama_tipe (compat)
        $mekaniks = $nama_tipe
            ? $this->mekanik_model->get_by_jenis($nama_tipe)
            : $this->mekanik_model->get_all(['is_active' => 1]);

        $result = [];
        foreach ($mekaniks as $m) {
            $konflik = $tanggal_uji
                ? $this->mekanik_model->cek_konflik_mekanik($m->id_mekanik, $tanggal_uji, $exclude_id ?: null)
                : false;
            $result[] = [
                'id_mekanik'  => $m->id_mekanik,
                'nama'        => $m->nama,
                'perusahaan'  => $m->perusahaan,
                'jabatan'     => $m->jabatan,
                'konflik'     => $konflik,
            ];
        }

        echo json_encode(['status' => 'success', 'data' => $result]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────
    private function _roles()
    {
        $raw = $this->session->userdata('roles');
        if (is_array($raw) && !empty($raw)) return array_map('intval', $raw);
        $r = (int) $this->session->userdata('role');
        return $r > 0 ? [$r] : [];
    }

    private function _has(array $req, array $user_roles)
    {
        foreach ($req as $r) {
            if (in_array((int) $r, $user_roles)) return true;
        }
        return false;
    }
}
