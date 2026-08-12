<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controller Jadwal
 * 
 * Pengelolaan antarmuka agenda jadwal inspeksi uji kelayakan kendaraan.
 * Menangani:
 * - Tampilan kalender FullCalendar & daftar jadwal inspeksi
 * - Pembuatan agenda jadwal inspeksi baru untuk pengajuan berstatus 'dijadwalkan'
 * - Pengecekan ketersediaan mekanik & inspektor
 * - Pembatalan / reschedule jadwal uji
 */
class Jadwal extends CI_Controller
{
    /**
     * Konstruktor Controller Jadwal
     * Memuat model, library, helper, dan memverifikasi otorisasi Admin OHS (5) / Super Admin (1).
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model([
            'Jadwal_model'    => 'jadwal_model', 
            'Pengajuan_model' => 'pengajuan_model', 
            'Mekanik_Model'   => 'mekanik_model'
        ]);
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['url', 'form']);

        if (!$this->session->userdata('id_user')) {
            redirect('auth/login');
        }

        $roles = $this->_user_roles();
        if (!$this->_has_role([1, 5, 8], $roles)) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses ke halaman ini.');
            redirect('dashboard');
        }
    }

    /**
     * Halaman Indeks Daftar & Kalender Jadwal Inspeksi
     * 
     * @return void Render view jadwal/index
     */
    public function index()
    {
        $filter = [
            'status' => $this->input->get('status'),
            'bulan'  => $this->input->get('bulan'),
            'tahun'  => $this->input->get('tahun') ?: date('Y'),
        ];

        $jadwals = $this->jadwal_model->get_all($filter);

        // Ambil daftar pengajuan berstatus 'dijadwalkan' yang BELUM memiliki jadwal aktif
        $menunggu_jadwal = $this->db
            ->select('pu.id_pengajuan, pu.tanggal_pengajuan, pu.tipe_akses, pu.tipe_pengajuan, k.no_polisi, t.nama_tipe AS jenis_kendaraan, k.merk, k.tipe, u.nama AS nama_pemohon')
            ->from('pengajuan_uji pu')
            ->join('kendaraan k',      'k.id_kendaraan = pu.id_kendaraan')
            ->join('tipe_kendaraan t', 't.id_tipe_kendaraan = k.id_tipe_kendaraan', 'left')
            ->join('users u',          'u.id_user = pu.id_pemohon')
            ->where('pu.status', 'dijadwalkan')
            ->where('NOT EXISTS (SELECT 1 FROM jadwal_uji j WHERE j.id_pengajuan = pu.id_pengajuan AND j.status = "scheduled")', null, false)
            ->order_by('pu.tanggal_pengajuan', 'ASC')
            ->get()->result();

        // Menyusun event data JSON untuk plugin FullCalendar
        $events = [];
        foreach ($jadwals as $j) {
            $color = $j->status === 'scheduled' ? '#4154f1'
                : ($j->status === 'done'      ? '#2eca6a' : '#dc3545');

            $events[] = [
                'id'    => $j->id_jadwal,
                'title' => $j->no_polisi . ' — ' . $j->jenis_kendaraan,
                'start' => date('Y-m-d\TH:i:s', strtotime($j->tanggal_uji)),
                'color' => $color,
                'extendedProps' => [
                    'id_jadwal'  => $j->id_jadwal,
                    'no_polisi'  => $j->no_polisi,
                    'jenis'      => $j->jenis_kendaraan,
                    'pemohon'    => $j->nama_pemohon,
                    'lokasi'     => $j->lokasi,
                    'status'     => $j->status,
                    'keterangan' => $j->keterangan,
                ],
            ];
        }

        $data = [
            'title'           => 'Jadwal Inspeksi',
            'user'            => $this->session->userdata(),
            'jadwals'         => $jadwals,
            'filter'          => $filter,
            'menunggu_jadwal' => $menunggu_jadwal,
            'events_json'     => json_encode($events),
        ];

        $this->load->view('templates/header',  $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('jadwal/index',      $data);
        $this->load->view('templates/footer',  $data);
    }

    /**
     * Form Pembuatan Jadwal Inspeksi Baru untuk Suatu Pengajuan.
     * 
     * @param int|null $id_pengajuan ID Pengajuan
     * @return void Render view jadwal/form
     */
    public function create($id_pengajuan = null)
    {
        $id_pengajuan = (int) $id_pengajuan;
        $pengajuan    = $this->pengajuan_model->get_detail($id_pengajuan);

        if (!$pengajuan) {
            $this->session->set_flashdata('error', 'Data pengajuan tidak ditemukan.');
            redirect('jadwal');
            return;
        }

        // Ambil daftar mekanik yang kompeten untuk jenis kendaraan ini
        $mekanik_list   = $this->jadwal_model->get_mekanik_by_jenis($pengajuan->jenis_kendaraan);
        $inspektor_list = $this->jadwal_model->get_inspektor();

        $data = [
            'title'          => 'Buat Jadwal Inspeksi #' . str_pad($id_pengajuan, 4, '0', STR_PAD_LEFT),
            'user'           => $this->session->userdata(),
            'pengajuan'      => $pengajuan,
            'mekaniks'       => $mekanik_list,
            'inspektors'     => $inspektor_list,
            'mekanik_list'   => $mekanik_list,
            'inspektor_list' => $inspektor_list,
        ];

        $this->load->view('templates/header',  $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('jadwal/form',       $data);
        $this->load->view('templates/footer',  $data);
    }

    /**
     * Form Edit Jadwal Inspeksi.
     * 
     * @param int|null $id_jadwal ID Jadwal
     * @return void Render view jadwal/form
     */
    public function edit($id_jadwal = null)
    {
        $id_jadwal = (int) $id_jadwal;
        $existing  = $this->jadwal_model->get_by_id($id_jadwal);

        if (!$existing) {
            $this->session->set_flashdata('error', 'Data jadwal tidak ditemukan.');
            redirect('jadwal');
            return;
        }

        $pengajuan = $this->pengajuan_model->get_detail($existing->id_pengajuan);
        if (!$pengajuan) {
            $this->session->set_flashdata('error', 'Data pengajuan tidak ditemukan.');
            redirect('jadwal');
            return;
        }

        $mekanik_list   = $this->jadwal_model->get_mekanik_by_jenis($pengajuan->jenis_kendaraan);
        $inspektor_list = $this->jadwal_model->get_inspektor();

        $data = [
            'title'          => 'Edit Jadwal Inspeksi #' . str_pad($existing->id_pengajuan, 4, '0', STR_PAD_LEFT),
            'user'           => $this->session->userdata(),
            'pengajuan'      => $pengajuan,
            'existing'       => $existing,
            'mekaniks'       => $mekanik_list,
            'inspektors'     => $inspektor_list,
            'mekanik_list'   => $mekanik_list,
            'inspektor_list' => $inspektor_list,
        ];

        $this->load->view('templates/header',  $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('jadwal/form',       $data);
        $this->load->view('templates/footer',  $data);
    }

    /**
     * Endpoint Simpan (Store / Update) Jadwal Uji.
     * Melakukan pengecekan bentrok jadwal mekanik/inspektor.
     * 
     * @return void Redirect atau JSON response
     */
    public function store()
    {
        $is_ajax            = $this->input->is_ajax_request() || isset($_SERVER['HTTP_X_REQUESTED_WITH']);
        $id_pengajuan       = (int) $this->input->post('id_pengajuan');
        $id_jadwal          = (int) $this->input->post('id_jadwal');
        $tanggal_uji        = trim($this->input->post('tanggal_uji'));
        $id_inspektor       = (int) $this->input->post('id_inspektor');
        $id_mekanik_master  = (int) $this->input->post('id_mekanik_master');
        $lokasi             = trim($this->input->post('lokasi'));
        $catatan            = trim($this->input->post('keterangan') ?: $this->input->post('catatan'));
        $id_user            = (int) $this->session->userdata('id_user');

        if (!$id_pengajuan || !$tanggal_uji || !$id_inspektor) {
            if ($is_ajax) {
                echo json_encode(['status' => 'error', 'message' => 'Tanggal Uji dan Inspektor wajib diisi.']);
                return;
            }
            $this->session->set_flashdata('error', 'Tanggal Uji dan Inspektor wajib diisi.');
            redirect('jadwal/create/' . $id_pengajuan);
            return;
        }

        // Cek konflik bentrok jadwal
        $konflik_ins = $this->jadwal_model->cek_konflik_inspektor($tanggal_uji, $id_inspektor, $id_jadwal);
        if ($konflik_ins) {
            if ($is_ajax) {
                echo json_encode(['status' => 'error', 'message' => 'Inspektor yang dipilih memiliki jadwal lain dalam kurun waktu 60 menit.']);
                return;
            }
            $this->session->set_flashdata('error', 'Inspektor yang dipilih memiliki jadwal lain dalam kurun waktu 60 menit.');
            redirect('jadwal/create/' . $id_pengajuan);
            return;
        }

        if ($id_mekanik_master > 0) {
            $konflik_mek = $this->jadwal_model->cek_konflik_mekanik($tanggal_uji, $id_mekanik_master, $id_jadwal);
            if ($konflik_mek) {
                if ($is_ajax) {
                    echo json_encode(['status' => 'error', 'message' => 'Mekanik yang dipilih memiliki jadwal lain dalam kurun waktu 60 menit.']);
                    return;
                }
                $this->session->set_flashdata('error', 'Mekanik yang dipilih memiliki jadwal lain dalam kurun waktu 60 menit.');
                redirect('jadwal/create/' . $id_pengajuan);
                return;
            }
        }

        $this->db->trans_start();

        if ($id_jadwal > 0) {
            // Update record jadwal_uji
            $this->jadwal_model->update($id_jadwal, [
                'tanggal_uji'       => date('Y-m-d H:i:s', strtotime($tanggal_uji)),
                'id_inspektor'      => $id_inspektor,
                'id_mekanik_master' => $id_mekanik_master ?: null,
                'lokasi'            => $lokasi ?: 'Workshop Main',
                'keterangan'        => $catatan ?: null,
            ]);

            $this->db->insert('audit_log', [
                'id_user'    => $id_user,
                'aksi'       => 'update_jadwal',
                'tabel'      => 'jadwal_uji',
                'id_ref'     => $id_jadwal,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } else {
            // Insert record jadwal_uji baru
            $id_jadwal = $this->jadwal_model->insert([
                'id_pengajuan'      => $id_pengajuan,
                'tanggal_uji'       => date('Y-m-d H:i:s', strtotime($tanggal_uji)),
                'id_inspektor'      => $id_inspektor,
                'id_mekanik_master' => $id_mekanik_master ?: null,
                'lokasi'            => $lokasi ?: 'Workshop Main',
                'keterangan'        => $catatan ?: null,
                'status'            => 'scheduled',
                'dibuat_oleh'       => $id_user,
                'created_at'        => date('Y-m-d H:i:s'),
            ]);

            $this->db->insert('audit_log', [
                'id_user'    => $id_user,
                'aksi'       => 'buat_jadwal',
                'tabel'      => 'jadwal_uji',
                'id_ref'     => $id_jadwal,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            if ($is_ajax) {
                echo json_encode([
                    'status'   => 'success',
                    'message'  => 'Jadwal inspeksi berhasil disimpan.',
                    'redirect' => site_url('jadwal')
                ]);
                return;
            }
            $this->session->set_flashdata('success', 'Jadwal inspeksi berhasil disimpan.');
            redirect('jadwal');
        } else {
            if ($is_ajax) {
                echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan jadwal inspeksi.']);
                return;
            }
            $this->session->set_flashdata('error', 'Gagal menyimpan jadwal inspeksi.');
            redirect('jadwal/create/' . $id_pengajuan);
        }
    }

    /**
     * Endpoint AJAX Cek Konflik Jadwal Inspektor.
     * 
     * @return void Response JSON
     */
    public function cek_konflik_inspektor()
    {
        $id_inspektor      = (int) ($this->input->post('id_inspektor') ?: $this->input->get('id_inspektor'));
        $tanggal_uji       = trim($this->input->post('tanggal_uji') ?: $this->input->get('tanggal_uji'));
        $exclude_jadwal_id = (int) ($this->input->post('exclude_jadwal_id') ?: $this->input->get('exclude_jadwal_id'));

        if (!$id_inspektor || !$tanggal_uji) {
            echo json_encode(['status' => 'error', 'konflik' => false]);
            return;
        }

        $konflik = $this->jadwal_model->cek_konflik_inspektor($tanggal_uji, $id_inspektor, $exclude_jadwal_id);
        echo json_encode(['status' => 'success', 'konflik' => $konflik]);
    }

    /**
     * Endpoint AJAX Daftar Jadwal pada Tanggal Tertentu.
     * 
     * @return void Response JSON
     */
    public function get_by_date()
    {
        $tanggal = trim($this->input->post('tanggal') ?: $this->input->get('tanggal'));
        if (!$tanggal) {
            echo json_encode(['status' => 'error', 'data' => []]);
            return;
        }

        $jadwal_list = $this->jadwal_model->get_jadwal_on_date($tanggal);
        $result = [];
        foreach ($jadwal_list as $j) {
            $result[] = [
                'id_jadwal'       => $j->id_jadwal,
                'waktu'           => date('H:i', strtotime($j->tanggal_uji)),
                'no_polisi'       => $j->no_polisi,
                'jenis_kendaraan' => $j->jenis_kendaraan,
                'nama_inspektor'  => $j->nama_inspektor,
                'nama_mekanik'    => $j->nama_mekanik,
            ];
        }

        echo json_encode(['status' => 'success', 'data' => $result]);
    }

    /**
     * Endpoint AJAX Detail Jadwal.
     * 
     * @return void Response JSON
     */
    public function detail()
    {
        $id = (int) ($this->input->get('id') ?: $this->input->post('id'));
        $jadwal = $this->jadwal_model->get_by_id($id);
        if ($jadwal) {
            echo json_encode(['status' => 'success', 'data' => $jadwal]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Jadwal tidak ditemukan.']);
        }
    }

    /**
     * Endpoint Pembatalan Jadwal Inspeksi (Cancel).
     * 
     * @param int|null $id ID Jadwal
     * @return void Redirect ke halaman jadwal
     */
    public function cancel($id = null)
    {
        $id = (int) $id;
        $ok = $this->jadwal_model->cancel($id);

        if ($ok) {
            $this->session->set_flashdata('success', 'Jadwal inspeksi berhasil dibatalkan.');
        } else {
            $this->session->set_flashdata('error', 'Gagal membatalkan jadwal.');
        }
        redirect('jadwal');
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
     * @param array $required Role yang disyaratkan
     * @param array $user_roles Role yang dimiliki user
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
