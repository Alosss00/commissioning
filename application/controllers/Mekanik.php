<?php

/**
 * Controller Mekanik
 * 
 * Pengelolaan data master mekanik/teknisi lapangan dan pemetaan tipe unit yang dikuasai.
 * Fitur:
 * - Menampilkan daftar mekanik + filter status
 * - Form tambah/edit data mekanik beserta penetapan kompetensi tipe kendaraan
 * - Menyiapkan daftar mekanik yang tersedia untuk jadwal pengujian (bebas bentrok ±60 menit)
 * - Pembaruan status aktif/non-aktif dan penghapusan mekanik
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Mekanik extends CI_Controller
{
    /**
     * Konstruktor Controller Mekanik
     * Memuat model Mekanik_Model, library, helper, dan memverifikasi hak akses pengguna (Role 1, 5, 8).
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Mekanik_Model', 'mekanik_model');
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['url', 'form']);

        if (!$this->session->userdata('id_user')) {
            redirect('auth/login');
        }

        $roles = $this->_roles();
        if (!$this->_has([1, 5, 8], $roles)) {
            $this->session->set_flashdata('error', 'Akses ditolak.');
            redirect('dashboard');
        }

        $this->_check_departments();
    }

    /**
     * Memastikan data master departemen di tabel perusahaan up-to-date.
     * Menggunakan single query & static flag untuk mencegah overhead N+1 query.
     * 
     * @return void
     */
    private function _check_departments()
    {
        static $checked = false;
        if ($checked) return;
        $checked = true;

        $old_new_map = [
            'BUSINESS DEVELOPMENT' => 'Departemen Business Development',
            'COMMERCIAL' => 'Departemen Commercial',
            'COMMUNITY DEVELOPMENT' => 'Departemen Community Development',
            'COMPLIANCE' => 'Departemen Compliance',
            'COMREL & LAND ACQ (Community Relations & Land Acquisition)' => 'Departemen Comrel & Land Acq (Community Relations & Land Acquisition)',
            'CORPORATE LEGAL' => 'Departemen Corporate Legal',
            'ENVIRONMENTAL' => 'Departemen Environmental',
            'EXPLORATION' => 'Departemen Exploration',
            'EXTERNAL RELATIONS' => 'Departemen External Relations',
            'FIN & ACC OPERATIONAL (Finance & Accounting Operational)' => 'Departemen Fin & Acc Operational (Finance & Accounting Operational)',
            'HCCS (Human Capital & Corporate Services)' => 'Departemen HCCS (Human Capital & Corporate Services)',
            'HSE & FORMALITIES / HSE (Health, Safety, Environment)' => 'Departemen HSE & Formalities / HSE (Health, Safety, Environment)',
            'IT (Information Technology)' => 'Departemen IT (Information Technology)',
            'MAINTENANCE' => 'Departemen Maintenance',
            'MANAGEMENT' => 'Departemen Management',
            'METALLURGY' => 'Departemen Metallurgy',
            'MINING' => 'Departemen Mining',
            'MINING TECH SERVICE / MINING TECHNICAL SERVICE' => 'Departemen Mining Tech Service / Mining Technical Service',
            'OHS (Occupational Health & Safety)' => 'Departemen OHS (Occupational Health & Safety)',
            'PRINCIPAL MINING' => 'Departemen Principal Mining',
            'PROCESS PLANT' => 'Departemen Process Plant',
            'PROJECT' => 'Departemen Project',
            'RESOURCES & RESERVE' => 'Departemen Resources & Reserve',
            'SECURITY' => 'Departemen Security',
            'SUSTAINABILITY & EXTERNAL AFFAIRS' => 'Departemen Sustainability & External Affairs',
            'SUPPLY CHAIN' => 'Departemen Supply Chain',
            'UNDERGROUND' => 'Departemen Underground'
        ];

        $existing = $this->db->select('LOWER(nama_perusahaan) as nama_lower')->get('perusahaan')->result_array();
        $existing_map = array_column($existing, 'nama_lower');

        $to_insert = [];
        $has_created_at = $this->db->field_exists('created_at', 'perusahaan');

        foreach ($old_new_map as $old => $new) {
            $old_lower = strtolower($old);
            $new_trim  = trim($new);
            $new_lower = strtolower($new_trim);

            if (in_array($old_lower, $existing_map, true)) {
                $this->db->where('LOWER(nama_perusahaan)', $old_lower)->update('perusahaan', ['nama_perusahaan' => $new_trim]);
            }

            if (!in_array($old_lower, $existing_map, true) && !in_array($new_lower, $existing_map, true)) {
                $payload = [
                    'nama_perusahaan' => $new_trim,
                    'is_active'       => 1
                ];
                if ($has_created_at) {
                    $payload['created_at'] = date('Y-m-d H:i:s');
                }
                $to_insert[] = $payload;
                $existing_map[] = $new_lower;
            }
        }

        if (!empty($to_insert)) {
            $this->db->insert_batch('perusahaan', $to_insert);
        }
    }

    /**
     * Halaman Indeks Daftar Mekanik Master
     * 
     * @return void Render view mekanik/index
     */
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

    /**
     * Form Tambah / Edit Data Mekanik
     * 
     * @param int|null $id ID Mekanik
     * @return void Render view mekanik/form
     */
    public function form($id = null)
    {
        $id         = (int) $id;
        $mekanik    = $id ? $this->mekanik_model->get_by_id($id) : null;

        $tipe_exist_raw = $id
            ? $this->mekanik_model->get_tipe_by_mekanik($id)
            : [];
        $tipe_exist = array_column($tipe_exist_raw, 'id_tipe_kendaraan');

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
            'tipe_exist'       => $tipe_exist,
            'semua_tipe'       => $semua_tipe,
            'semua_perusahaan' => $semua_perusahaan,
        ];

        $this->load->view('templates/header',  $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('mekanik/form',      $data);
        $this->load->view('templates/footer',  $data);
    }

    /**
     * Proses Simpan (Insert/Update) Data Mekanik beserta pemetaan tipe kendaraan.
     * Menggunakan batch insert pada model.
     * 
     * @return void Redirect ke halaman mekanik_master
     */
    public function save()
    {
        $id       = (int) $this->input->post('id_mekanik');
        $tipe_arr = array_map('intval', (array) $this->input->post('tipe_kendaraan'));
        $tipe_arr = array_filter($tipe_arr);

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

    /**
     * Endpoint AJAX Toggle Status Aktif/Non-aktif Mekanik.
     * 
     * @return void JSON Response status
     */
    public function toggle()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $id = (int) $this->input->post('id');
        $ok = $this->mekanik_model->toggle_active($id);
        echo json_encode(['status' => $ok ? 'success' : 'error']);
    }

    /**
     * Endpoint AJAX Hapus Data Mekanik.
     * Memeriksa keterlibatan jadwal aktif sebelum menghapus data.
     * 
     * @return void JSON Response status hapus
     */
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

    /**
     * Endpoint AJAX untuk mendapatkan daftar mekanik yang tersedia (bebas konflik jadwal).
     * 
     * @return void JSON Response daftar mekanik tersedia
     */
    public function get_available()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $nama_tipe   = $this->input->post('jenis_kendaraan') ?: $this->input->get('jenis_kendaraan');
        $tanggal_uji = $this->input->post('tanggal_uji') ?: $this->input->get('tanggal_uji');
        $exclude_id  = (int) ($this->input->post('exclude_jadwal_id') ?: $this->input->get('exclude_jadwal_id'));

        $mekaniks = $nama_tipe
            ? $this->mekanik_model->get_by_jenis($nama_tipe)
            : $this->mekanik_model->get_all(['is_active' => 1]);

        $result = [];
        foreach ($mekaniks as $m) {
            $konflik = $tanggal_uji
                ? $this->mekanik_model->cek_konflik_mekanik($m->id_mekanik, $tanggal_uji, $exclude_id ?: null)
                : false;

            $result[] = [
                'id_mekanik' => $m->id_mekanik,
                'nama'       => $m->nama,
                'perusahaan' => $m->perusahaan,
                'jabatan'    => $m->jabatan,
                'konflik'    => $konflik,
            ];
        }

        echo json_encode(['status' => 'success', 'data' => $result]);
    }

    /**
     * Helper privat mengambil array ID role pengguna.
     * 
     * @return array Array integer ID role
     */
    private function _roles()
    {
        $raw = $this->session->userdata('roles');
        if (is_array($raw) && !empty($raw)) return array_map('intval', $raw);
        $r = (int) $this->session->userdata('role');
        return $r > 0 ? [$r] : [];
    }

    /**
     * Helper privat memeriksa apakah pengguna memiliki salah satu role yang disyaratkan.
     * 
     * @param array $req Role yang disyaratkan
     * @param array $user_roles Role yang dimiliki user
     * @return bool True jika diizinkan
     */
    private function _has(array $req, array $user_roles)
    {
        foreach ($req as $r) {
            if (in_array((int) $r, $user_roles, true)) return true;
        }
        return false;
    }
}
