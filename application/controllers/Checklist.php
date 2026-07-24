<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Checklist extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model([
            'Checklist_model' => 'checklist_model',
            'Pengajuan_model' => 'pengajuan_model',
            'Kendaraan_model' => 'kendaraan_model',
        ]);
        $this->load->library(['session', 'form_validation', 'upload']);
        $this->load->helper(['url', 'form']);
        if (!$this->session->userdata('id_user')) redirect('auth/login');
    }

    // =========================================================
    // DAFTAR TEMPLATE
    // =========================================================
    public function index()
    {
        $roles = $this->_user_roles();
        if (!$this->_has_role([1, 5], $roles)) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses ke halaman ini.');
            redirect('dashboard');
        }

        $data['title']     = 'Checklist Template';
        $data['user']      = $this->session->userdata();
        $data['templates'] = $this->checklist_model->get_all_templates();

        $this->load->view('templates/header',  $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('checklist/index',   $data);
        $this->load->view('templates/footer',  $data);
    }

    // =========================================================
    // DETAIL TEMPLATE
    // =========================================================
    public function template($id_template = null)
    {
        $roles = $this->_user_roles();
        if (!$this->_has_role([1, 5], $roles)) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses ke halaman ini.');
            redirect('dashboard');
        }

        $id_template = (int) $id_template;
        $template    = $this->checklist_model->get_template($id_template);
        if (!$template) {
            $this->session->set_flashdata('error', 'Template tidak ditemukan.');
            redirect('checklist');
        }

        $items   = $this->checklist_model->get_items($id_template);
        $grouped = ['CRITICAL' => [], 'GENERAL' => []];
        foreach ($items as $item) {
            $grouped[$item->kategori][] = $item;
        }

        $data = [
            'title'    => 'Template: ' . $template->nama_tipe,
            'user'     => $this->session->userdata(),
            'template' => $template,
            'grouped'  => $grouped,
        ];

        $this->load->view('templates/header',   $data);
        $this->load->view('templates/sidebar',  $data);
        $this->load->view('checklist/template', $data);
        $this->load->view('templates/footer',   $data);
    }

    // =========================================================
    // AJAX — Tipe tersedia untuk tambah template
    // =========================================================
    public function get_tipe_tersedia()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $roles = $this->_user_roles();
        if (!$this->_has_role([1, 5], $roles)) {
            echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
            return;
        }
        $tipe = $this->checklist_model->get_tipe_tersedia();
        echo json_encode(['status' => 'success', 'data' => $tipe]);
    }

    // =========================================================
    // AJAX — Simpan Template Baru
    // =========================================================
    public function save_template()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $roles = $this->_user_roles();
        if (!$this->_has_role([1, 5], $roles)) {
            echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
            return;
        }

        $id_tipe       = (int) $this->input->post('id_tipe_kendaraan');
        $nama_template = trim($this->input->post('nama_template') ?? '');
        $kode          = strtoupper(trim($this->input->post('kode') ?? ''));

        if (!$id_tipe) {
            echo json_encode(['status' => 'error', 'message' => 'Pilih tipe kendaraan.']);
            return;
        }
        if (empty($nama_template)) {
            echo json_encode(['status' => 'error', 'message' => 'Nama template wajib diisi.']);
            return;
        }
        if (empty($kode)) {
            echo json_encode(['status' => 'error', 'message' => 'Kode template wajib diisi.']);
            return;
        }

        $existing = $this->checklist_model->get_template_by_tipe_id($id_tipe);
        if ($existing) {
            echo json_encode(['status' => 'error', 'message' => 'Tipe ini sudah memiliki template aktif.']);
            return;
        }

        if ($this->db->where('kode', $kode)->count_all_results('checklist_template') > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Kode template <strong>' . html_escape($kode) . '</strong> sudah dipakai.']);
            return;
        }

        $new_id = $this->checklist_model->insert_template([
            'kode'              => $kode,
            'id_tipe_kendaraan' => $id_tipe,
            'nama_template'     => $nama_template,
            'is_active'         => 1,
            'created_at'        => date('Y-m-d H:i:s'),
        ]);

        if (!$new_id) {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan template.']);
            return;
        }

        echo json_encode([
            'status'   => 'success',
            'message'  => 'Template berhasil dibuat. Silakan tambahkan item checklist.',
            'redirect' => site_url('checklist/template/' . $new_id),
        ]);
    }

    // =========================================================
    // FORM INSPEKSI
    // FIX: Hapus pengecekan id_mekanik/id_inspektor yang terlalu
    // ketat. Semua inspektor (role 4) dan admin (role 1) boleh
    // mengisi form selama pengajuan berstatus dijadwalkan/inspeksi_ulang.
    // =========================================================
    public function form($id_pengajuan = null)
    {
        $roles = $this->_user_roles();

        if (!$this->_has_role([1, 4], $roles)) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses ke halaman ini.');
            redirect('dashboard');
        }

        $id_pengajuan = (int) $id_pengajuan;
        $pengajuan    = $this->pengajuan_model->get_detail($id_pengajuan);

        if (!$pengajuan) {
            $this->session->set_flashdata('error', 'Data pengajuan tidak ditemukan.');
            redirect('inspeksi');
        }

        // Status valid: dijadwalkan ATAU inspeksi_ulang
        $status_valid = ['dijadwalkan', 'inspeksi_ulang'];
        if (!in_array($pengajuan->status, $status_valid)) {
            $this->session->set_flashdata(
                'error',
                'Form inspeksi hanya bisa diisi untuk pengajuan berstatus '
                    . '<strong>Dijadwalkan</strong> atau <strong>Siap Pengujian Ulang</strong>. '
                    . 'Status saat ini: <strong>' . $pengajuan->status . '</strong>'
            );
            redirect('inspeksi');
        }

        $is_inspeksi_ulang = ($pengajuan->status === 'inspeksi_ulang');

        // Cari template checklist berdasarkan tipe kendaraan
        $template = null;
        if (!empty($pengajuan->id_tipe_kendaraan)) {
            $template = $this->checklist_model->get_template_by_tipe_id($pengajuan->id_tipe_kendaraan);
        }
        if (!$template && !empty($pengajuan->nama_tipe)) {
            $template = $this->checklist_model->get_template_by_jenis($pengajuan->nama_tipe);
        }
        if (!$template) {
            $this->session->set_flashdata(
                'error',
                'Template checklist untuk tipe kendaraan ini tidak ditemukan. '
                    . 'Hubungi Admin OHS untuk menambahkan template.'
            );
            redirect('inspeksi');
        }

        // Ambil SEMUA items checklist dari template
        $all_items = $this->checklist_model->get_items($template->id_template);

        // ── Untuk inspeksi ulang: filter hanya item yang sebelumnya NO ──
        $items_no_sebelumnya = []; // id_item => true
        $uji_sebelumnya      = $this->pengajuan_model->get_uji($id_pengajuan);

        if ($is_inspeksi_ulang && $uji_sebelumnya) {
            $rows_no = $this->db
                ->select('id_item')
                ->where('id_uji', $uji_sebelumnya->id_uji)
                ->where('hasil', 'no')
                ->get('uji_checklist')->result();

            foreach ($rows_no as $r) {
                $items_no_sebelumnya[$r->id_item] = true;
            }
        }

        // Filter items yang akan ditampilkan di form
        $items_form = [];
        foreach ($all_items as $item) {
            if ($is_inspeksi_ulang) {
                // Hanya tampilkan item yang sebelumnya NO
                if (isset($items_no_sebelumnya[$item->id_item])) {
                    $items_form[] = $item;
                }
            } else {
                $items_form[] = $item;
            }
        }

        // Kelompokkan per kategori
        $grouped = ['CRITICAL' => [], 'GENERAL' => []];
        foreach ($items_form as $item) {
            $grouped[$item->kategori][] = $item;
        }

        // Jawaban yang sudah ada (draft / uji sebelumnya)
        $existing               = [];
        $existing_inspektor     = '';
        $existing_perusahaan    = '';
        $existing_mekanik       = '';
        $existing_perus_mekanik = '';

        $existing_foto_mekanik  = null;
        $uji = $this->pengajuan_model->get_uji($id_pengajuan);
        if ($uji) {
            $answers = $this->checklist_model->get_checklist_answers($uji->id_uji);
            foreach ($answers as $a) {
                $existing[$a->id_item] = [
                    'hasil'      => $a->hasil,
                    'keterangan' => $a->keterangan,
                ];
            }
            $existing_inspektor     = $uji->nama_inspektor       ?? '';
            $existing_perusahaan    = $uji->perusahaan_inspektor  ?? '';
            $existing_mekanik       = $uji->nama_mekanik          ?? '';
            $existing_perus_mekanik = $uji->perusahaan_mekanik    ?? '';

            $existing_foto_mekanik = $this->db
                ->where('id_uji', $uji->id_uji)
                ->where('jenis', 'mekanik')
                ->order_by('id_foto', 'DESC')
                ->get('uji_foto')->row();
        }

        // Auto-fill dari jadwal & data user inspektor
        $jadwal_info = $this->db
            ->select('j.id_jadwal,
                      mm.nama       AS nama_mekanik_jdl,
                      mm.perusahaan AS perus_mekanik_jdl,
                      ui.nama       AS nama_inspektor_jdl,
                      ui.departemen AS perus_inspektor_jdl')
            ->from('jadwal_uji j')
            ->join('mekanik_master mm', 'mm.id_mekanik = j.id_mekanik_master', 'left')
            ->join('users ui', 'ui.id_user = COALESCE(j.id_inspektor, j.id_mekanik)', 'left')
            ->where('j.id_pengajuan', $id_pengajuan)
            ->where_in('j.status', ['scheduled', 'done'])
            ->order_by('j.id_jadwal', 'DESC')
            ->get()->row();

        if (empty($existing_inspektor) && $jadwal_info) {
            $existing_inspektor = $jadwal_info->nama_inspektor_jdl ?? '';
        }

        if (empty($existing_perusahaan)) {
            if ($jadwal_info && !empty($jadwal_info->perus_inspektor_jdl)) {
                $existing_perusahaan = $jadwal_info->perus_inspektor_jdl;
            } else {
                // Fallback ke departemen milik user logged in jika role-nya inspektor/admin
                $id_user_login = $this->session->userdata('id_user');
                $user_login = $this->db->select('departemen')->where('id_user', $id_user_login)->get('users')->row();
                if ($user_login && !empty($user_login->departemen)) {
                    $existing_perusahaan = $user_login->departemen;
                }
            }
        }

        if (empty($existing_mekanik) && $jadwal_info) {
            $existing_mekanik       = $jadwal_info->nama_mekanik_jdl   ?? '';
            $existing_perus_mekanik = $jadwal_info->perus_mekanik_jdl  ?? '';
        }

        // Data perbaikan (konteks inspeksi ulang)
        $perbaikan_info = null;
        $lampiran_perbaikan = [];
        if ($is_inspeksi_ulang) {
            $perbaikan_info = $this->db
                ->where('id_pengajuan', $id_pengajuan)
                ->order_by('id_perbaikan', 'DESC')
                ->get('perbaikan_unit')->row();

            if ($perbaikan_info) {
                $lampiran_perbaikan = $this->db
                    ->where('id_perbaikan', $perbaikan_info->id_perbaikan)
                    ->get('perbaikan_lampiran')->result();
            }
        }

        // Summary item yang perlu diuji ulang
        $total_no_sebelumnya = count($items_no_sebelumnya);

        $daftar_perusahaan = $this->db
            ->select('nama_perusahaan')
            ->where('is_active', 1)
            ->order_by('nama_perusahaan', 'ASC')
            ->get('perusahaan')->result();

        $data = [
            'title'                  => ($is_inspeksi_ulang ? 'Pengujian Ulang' : 'Form Inspeksi')
                . ' — ' . $pengajuan->no_polisi,
            'user'                   => $this->session->userdata(),
            'pengajuan'              => $pengajuan,
            'template'               => $template,
            'grouped'                => $grouped,
            'all_items'              => $all_items,
            'items_no_sebelumnya'    => $items_no_sebelumnya,
            'total_no_sebelumnya'    => $total_no_sebelumnya,
            'existing'               => $existing,
            'existing_inspektor'     => $existing_inspektor,
            'existing_perusahaan'    => $existing_perusahaan,
            'existing_mekanik'       => $existing_mekanik,
            'existing_perus_mekanik' => $existing_perus_mekanik,
            'nama_mekanik'           => $jadwal_info->nama_mekanik_jdl  ?? '',
            'perusahaan_mekanik'     => $jadwal_info->perus_mekanik_jdl ?? '',
            'perbaikan_info'         => $perbaikan_info,
            'lampiran_perbaikan'     => $lampiran_perbaikan,
            'is_inspeksi_ulang'      => $is_inspeksi_ulang,
            'uji_sebelumnya'         => $uji_sebelumnya,
            'existing_foto_mekanik'  => $existing_foto_mekanik,
            'daftar_perusahaan'      => $daftar_perusahaan,
        ];

        $this->load->view('templates/header',  $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('checklist/form',    $data);
        $this->load->view('templates/footer',  $data);
    }


    // =========================================================
    // SUBMIT INSPEKSI — AJAX
    // =========================================================
    public function submit()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $roles = $this->_user_roles();
        if (!$this->_has_role([1, 4], $roles)) {
            echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
            return;
        }

        $id_pengajuan         = (int) $this->input->post('id_pengajuan');
        $id_template          = (int) $this->input->post('id_template');
        $catatan              = trim($this->input->post('catatan_temuan') ?? $this->input->post('catatan_umum') ?? '');
        $nama_inspektor       = trim($this->input->post('nama_inspektor') ?? '');
        $perusahaan_inspektor = trim($this->input->post('perusahaan_inspektor') ?? '');
        $nama_mekanik         = trim($this->input->post('nama_mekanik') ?? '');
        $perusahaan_mekanik   = trim($this->input->post('perusahaan_mekanik') ?? '');
        $tgl_maks_perbaikan   = trim($this->input->post('tgl_maks_perbaikan') ?? '');
        $items_post           = $this->input->post('items');
        if (!is_array($items_post)) $items_post = [];

        if (empty($nama_inspektor)) {
            echo json_encode(['status' => 'error', 'message' => 'Nama inspektor wajib diisi.']);
            return;
        }
        if (empty($perusahaan_inspektor)) {
            echo json_encode(['status' => 'error', 'message' => 'Perusahaan inspektor wajib diisi.']);
            return;
        }

        $pengajuan    = $this->pengajuan_model->get_detail($id_pengajuan);
        $status_valid = ['dijadwalkan', 'inspeksi_ulang'];
        if (!$pengajuan || !in_array($pengajuan->status, $status_valid)) {
            echo json_encode(['status' => 'error', 'message' => 'Pengajuan tidak valid atau tidak dalam status yang diizinkan.']);
            return;
        }

        $is_inspeksi_ulang = ($pengajuan->status === 'inspeksi_ulang');

        // ── Untuk inspeksi ulang: gabungkan jawaban lama (yes/na) + baru (dari form) ──
        $uji_existing   = $this->pengajuan_model->get_uji($id_pengajuan);

        // Validasi Foto Mekanik / Peserta Commissioning (Wajib)
        $has_foto_mekanik = !empty($_FILES['foto_mekanik']['tmp_name']) && $_FILES['foto_mekanik']['error'] === UPLOAD_ERR_OK;
        if (!$has_foto_mekanik && $uji_existing) {
            $check_exist = $this->db
                ->where('id_uji', $uji_existing->id_uji)
                ->where('jenis', 'mekanik')
                ->count_all_results('uji_foto');
            if ($check_exist > 0) {
                $has_foto_mekanik = true;
            }
        }
        if (!$has_foto_mekanik) {
            echo json_encode(['status' => 'error', 'message' => 'Foto Mekanik / Peserta Commissioning wajib di-upload.']);
            return;
        }
        $template_items = $this->checklist_model->get_items($id_template);

        if ($is_inspeksi_ulang && $uji_existing) {
            // Ambil jawaban lama
            $answers_lama = $this->checklist_model->get_checklist_answers($uji_existing->id_uji);
            $jawaban_lama = []; // id_item => ['hasil', 'keterangan']
            foreach ($answers_lama as $a) {
                $jawaban_lama[$a->id_item] = [
                    'hasil'      => $a->hasil,
                    'keterangan' => $a->keterangan,
                ];
            }

            // Tentukan item yang NO (harus diisi ulang)
            $items_no_ids = [];
            foreach ($jawaban_lama as $id_item => $jaw) {
                if ($jaw['hasil'] === 'no') {
                    $items_no_ids[$id_item] = true;
                }
            }

            // Validasi: semua item NO harus diisi di form
            $missing = [];
            foreach ($template_items as $item) {
                if (isset($items_no_ids[$item->id_item])) {
                    if (empty($items_post[$item->id_item]['hasil'])) {
                        $missing[] = $item->no_urut;
                    }
                }
            }
            if (!empty($missing)) {
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Item pengujian ulang no. ' . implode(', ', $missing) . ' belum dijawab.',
                ]);
                return;
            }

            // Gabungkan: item NO → pakai jawaban baru, item lain → pakai jawaban lama
            $items_gabung = [];
            foreach ($template_items as $item) {
                $id_item = $item->id_item;
                if (isset($items_no_ids[$id_item]) && isset($items_post[$id_item])) {
                    // Item yang diuji ulang → pakai jawaban baru
                    $items_gabung[$id_item] = $items_post[$id_item];
                } elseif (isset($jawaban_lama[$id_item])) {
                    // Item lain → pertahankan jawaban lama
                    $items_gabung[$id_item] = $jawaban_lama[$id_item];
                }
            }
            $items_final = $items_gabung;
        } else {
            // ── Inspeksi normal: semua item harus diisi ──
            $missing = [];
            foreach ($template_items as $item) {
                if (empty($items_post[$item->id_item]['hasil'])) {
                    $missing[] = $item->no_urut;
                }
            }
            if (!empty($missing)) {
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Item no. ' . implode(', ', $missing) . ' belum dijawab.',
                ]);
                return;
            }
            $items_final = $items_post;
        }

        $id_inspektor = (int) $this->session->userdata('id_user');

        $this->db->trans_start();

        // Upsert uji_kelayakan
        if ($uji_existing) {
            $id_uji = $uji_existing->id_uji;
            $this->db->where('id_uji', $id_uji)->update('uji_kelayakan', [
                'nama_inspektor'       => $nama_inspektor,
                'perusahaan_inspektor' => $perusahaan_inspektor,
                'nama_mekanik'         => $nama_mekanik,
                'perusahaan_mekanik'   => $perusahaan_mekanik,
                'catatan_temuan'       => $catatan,
                'updated_at'           => date('Y-m-d H:i:s'),
            ]);
        } else {
            $this->db->insert('uji_kelayakan', [
                'id_pengajuan'         => $id_pengajuan,
                'id_mekanik'           => $id_inspektor,
                'nama_inspektor'       => $nama_inspektor,
                'perusahaan_inspektor' => $perusahaan_inspektor,
                'nama_mekanik'         => $nama_mekanik,
                'perusahaan_mekanik'   => $perusahaan_mekanik,
                'id_template'          => $id_template,
                'tanggal_uji'          => date('Y-m-d'),
                'catatan_temuan'       => $catatan,
                'hasil'                => 'pending',
                'created_at'           => date('Y-m-d H:i:s'),
            ]);
            $id_uji = $this->db->insert_id();
        }

        // Upload foto
        $this->_upload_foto_inspeksi($id_uji, $id_pengajuan);

        // Simpan jawaban checklist (gabungan lama + baru untuk inspeksi_ulang)
        $this->checklist_model->save_checklist($id_uji, $items_final);

        // Hitung hasil berdasarkan SEMUA item
        $summary = $this->checklist_model->get_summary($id_uji);
        $hasil   = $summary['lulus'] ? 'lulus' : 'tidak_lulus';

        $this->db->where('id_uji', $id_uji)->update('uji_kelayakan', [
            'hasil'      => $hasil,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        if ($summary['lulus']) {
            $new_status = 'lulus_inspeksi';

            // Jika inspeksi ulang, tandai perbaikan sebagai diverifikasi
            if ($is_inspeksi_ulang) {
                $this->db
                    ->where('id_pengajuan', $id_pengajuan)
                    ->where_in('status', ['diverifikasi', 'menunggu_verifikasi', 'menunggu'])
                    ->order_by('id_perbaikan', 'DESC')
                    ->limit(1)
                    ->update('perbaikan_unit', [
                        'status'         => 'diverifikasi',
                        'tgl_selesai'    => date('Y-m-d'),
                        'id_verifikator' => $id_inspektor,
                        'updated_at'     => date('Y-m-d H:i:s'),
                    ]);
            }
        } else {
            $new_status = 'tidak_lulus_inspeksi';

            if (empty($tgl_maks_perbaikan)) {
                $this->db->trans_rollback();
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Tanggal maksimum perbaikan wajib diisi ketika kendaraan tidak lulus.',
                ]);
                return;
            }
            if (strtotime($tgl_maks_perbaikan) < strtotime('today')) {
                $this->db->trans_rollback();
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Tanggal maksimum perbaikan tidak boleh di masa lalu.',
                ]);
                return;
            }

            // Insert record perbaikan baru
            $this->db->insert('perbaikan_unit', [
                'id_pengajuan'      => $id_pengajuan,
                'id_uji'            => $id_uji,
                'tgl_max_perbaikan' => $tgl_maks_perbaikan,
                'tgl_selesai'       => null,
                'id_verifikator'    => $id_inspektor,
                'catatan_perbaikan' => $catatan ?: null,
                'status'            => 'menunggu',
                'created_at'        => date('Y-m-d H:i:s'),
            ]);
        }

        $this->db->where('id_pengajuan', $id_pengajuan)
            ->update('pengajuan_uji', ['status' => $new_status]);

        $this->db->where('id_pengajuan', $id_pengajuan)
            ->where('status', 'scheduled')
            ->update('jadwal_uji', ['status' => 'done']);

        $this->db->insert('audit_log', [
            'id_user'    => $id_inspektor,
            'aksi'       => $is_inspeksi_ulang ? 'submit_inspeksi_ulang' : 'submit_inspeksi',
            'tabel'      => 'uji_kelayakan',
            'id_ref'     => $id_uji,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->db->trans_complete();

        if (!$this->db->trans_status()) {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan hasil inspeksi.']);
            return;
        }

        // Kirim notifikasi email otomatis
        if (file_exists(APPPATH . 'libraries/Sikuk_email.php')) {
            try {
                $this->load->library('sikuk_email');
                if ($summary['lulus']) {
                    $this->sikuk_email->notif_selesai_inspeksi($id_pengajuan);
                    $this->sikuk_email->notif_progress($id_pengajuan, 'Lulus Inspeksi — Menunggu Review Admin OHS');
                } else {
                    $this->sikuk_email->notif_ditolak_admin_ohs_ke_manager($id_pengajuan, 'Hasil inspeksi kelayakan menyatakan unit TIDAK LULUS.');
                }
            } catch (Throwable $e) {
                log_message('error', '[Checklist Submit Email] Exception: ' . $e->getMessage());
            }
        }

        // ── Susun pesan response ──────────────────────────────────────
        $prefix = $is_inspeksi_ulang ? 'Pengujian ulang selesai.' : 'Inspeksi selesai.';

        if ($summary['lulus']) {
            $msg = $prefix . ' Kendaraan <strong>LULUS</strong> uji kelayakan. '
                . 'Pengajuan diteruskan ke <strong>OHS Superintendent</strong>.';
        } else {
            $detail_no = [];
            if (!empty($summary['critical_no']) && $summary['critical_no'] > 0)
                $detail_no[] = '<strong>' . $summary['critical_no'] . ' item CRITICAL</strong>';
            if (!empty($summary['general_no']) && $summary['general_no'] > 0)
                $detail_no[] = '<strong>' . $summary['general_no'] . ' item GENERAL</strong>';
            $tgl_fmt = date('d M Y', strtotime($tgl_maks_perbaikan));
            $msg = $prefix . ' Kendaraan <strong>TIDAK LULUS</strong> — '
                . implode(' dan ', $detail_no) . ' tidak memenuhi syarat. '
                . 'Deadline perbaikan: <strong>' . $tgl_fmt . '</strong>.';
        }

        echo json_encode([
            'status'   => 'success',
            'message'  => $msg,
            'hasil'    => $hasil,
            'id_uji'   => $id_uji,
            'summary'  => $summary,
            'redirect' => site_url('inspeksi'),
        ]);
    }
    // =========================================================
    // DETAIL — read-only
    // =========================================================
    public function detail($id_uji = null)
    {
        $id_uji  = (int) $id_uji;
        if (!$id_uji) show_404();

        $roles   = $this->_user_roles();
        $id_user = (int) $this->session->userdata('id_user');

        $uji = $this->db
            ->select('uk.*, u.nama AS nama_user_login,
                  k.no_polisi, pu.id_pengajuan, pu.status AS status_pengajuan,
                  k.id_tipe_kendaraan, t.nama_tipe AS jenis_kendaraan,
                  k.merk, k.tipe, k.nomor_unit, k.tahun')
            ->from('uji_kelayakan uk')
            ->join('users u',          'u.id_user = uk.id_mekanik',         'left')
            ->join('pengajuan_uji pu', 'pu.id_pengajuan = uk.id_pengajuan', 'left')
            ->join('kendaraan k',      'k.id_kendaraan = pu.id_kendaraan',  'left')
            ->join('tipe_kendaraan t', 't.id_tipe_kendaraan = k.id_tipe_kendaraan', 'left')
            ->where('uk.id_uji', $id_uji)
            ->get()->row();

        if (!$uji) show_404();

        // FIX: inspektor bisa lihat semua detail, bukan hanya miliknya
        // Cukup cek role saja, tidak perlu cek id_mekanik
        if (!$this->_has_role([1, 4, 3, 5, 2, 6], $roles)) show_404();

        $items   = $this->checklist_model->get_checklist_answers($id_uji);
        $summary = $this->checklist_model->get_summary($id_uji);
        $grouped = [];
        foreach ($items as $item) {
            $grouped[$item->kategori][] = $item;
        }

        $foto_list        = $this->db->where('id_uji', $id_uji)->get('uji_foto')->result();
        $history_versions = $this->checklist_model->get_history_versions($id_uji);
        $history_detail   = $this->checklist_model->get_checklist_history($id_uji);

        $data = [
            'title'            => 'Detail Hasil Inspeksi',
            'user'             => $this->session->userdata(),
            'uji'              => $uji,
            'items'            => $items,
            'grouped'          => $grouped,
            'summary'          => $summary,
            'foto_list'        => $foto_list,
            'history_versions' => $history_versions,
            'history_detail'   => $history_detail,
            'perbaikan_list'   => $this->_get_perbaikan_list($uji->id_pengajuan),
        ];

        $this->load->view('templates/header',  $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('checklist/detail',  $data);
        $this->load->view('templates/footer',  $data);
    }

    // =========================================================
    // PDF
    // =========================================================
    public function pdf($id_uji = null)
    {
        $id_uji  = (int) $id_uji;
        if (!$id_uji) show_404();

        $roles = $this->_user_roles();

        $uji = $this->db
            ->select('uk.*, u.nama AS nama_user_login,
                  k.no_polisi, pu.id_pengajuan, pu.status AS status_pengajuan,
                  pu.tipe_pengajuan, pu.tipe_akses,
                  t.nama_tipe AS jenis_kendaraan,
                  k.merk, k.tipe AS tipe_kendaraan, k.nomor_unit, k.tahun, k.perusahaan,
                  t.doc_no, t.title_id, t.title_en,
                  t.doc_name_id, t.doc_name_en,
                  t.tgl_terbit, t.tgl_review, t.no_revisi')
            ->from('uji_kelayakan uk')
            ->join('users u',          'u.id_user = uk.id_mekanik',         'left')
            ->join('pengajuan_uji pu', 'pu.id_pengajuan = uk.id_pengajuan', 'left')
            ->join('kendaraan k',      'k.id_kendaraan = pu.id_kendaraan',  'left')
            ->join('tipe_kendaraan t', 't.id_tipe_kendaraan = k.id_tipe_kendaraan', 'left')
            ->where('uk.id_uji', $id_uji)
            ->get()->row();

        if (!$uji) show_404();

        $jenis = $uji->jenis_kendaraan ?? '';
        $doc   = [
            'title_id'    => $uji->title_id    ?: 'DAFTAR PERIKSA UJI KELAYAKAN ' . strtoupper($jenis),
            'title_en'    => $uji->title_en    ?: ($jenis . ' Commissioning Checklist'),
            'doc_no'      => $uji->doc_no      ?: 'TT-OHS-FRO-002',
            'doc_name_id' => $uji->doc_name_id ?: ('Daftar Periksa Uji Kelayakan ' . $jenis),
            'doc_name_en' => $uji->doc_name_en ?: ($jenis . ' Commissioning Checklist'),
            'tgl_terbit'  => $uji->tgl_terbit  ? date('d M Y', strtotime($uji->tgl_terbit)) : '—',
            'tgl_review'  => $uji->tgl_review  ? date('d M Y', strtotime($uji->tgl_review)) : '—',
            'no_revisi'   => $uji->no_revisi   ?: '01',
        ];

        $items   = $this->checklist_model->get_checklist_answers($id_uji);
        $summary = $this->checklist_model->get_summary($id_uji);
        $grouped = [];
        foreach ($items as $item) {
            $grouped[$item->kategori][] = $item;
        }

        $history_versions = $this->checklist_model->get_history_versions($id_uji);
        $history_detail   = $this->checklist_model->get_checklist_history($id_uji);

        $data = [
            'title'            => 'Hasil Inspeksi — ' . $uji->no_polisi,
            'user'             => $this->session->userdata(),
            'uji'              => $uji,
            'doc'              => $doc,
            'items'            => $items,
            'grouped'          => $grouped,
            'summary'          => $summary,
            'history_versions' => $history_versions,
            'history_detail'   => $history_detail,
        ];

        $this->load->view('checklist/pdf_print', $data);
    }

    // =========================================================
    // AJAX handlers
    // =========================================================
    public function get_items()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $id_template = (int) $this->input->post('id_template');
        $items       = $this->checklist_model->get_items($id_template);
        echo json_encode(['status' => 'success', 'data' => $items]);
    }

    public function save_item()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $roles = $this->_user_roles();
        if (!$this->_has_role([1, 5], $roles)) {
            echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
            return;
        }
        $id      = (int) $this->input->post('id_item');
        $payload = [
            'id_template' => (int) $this->input->post('id_template'),
            'kategori'    => $this->input->post('kategori'),
            'no_urut'     => $this->input->post('no_urut'),
            'kriteria'    => $this->input->post('kriteria'),
        ];
        if ($id) {
            $this->checklist_model->update_item($id, $payload);
            echo json_encode(['status' => 'success', 'message' => 'Item berhasil diperbarui.']);
        } else {
            $new_id = $this->checklist_model->insert_item($payload);
            echo json_encode(['status' => 'success', 'message' => 'Item berhasil ditambahkan.', 'id' => $new_id]);
        }
    }

    public function delete_item()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $roles = $this->_user_roles();
        if (!$this->_has_role([1, 5], $roles)) {
            echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
            return;
        }
        $id = (int) $this->input->post('id_item');
        $this->checklist_model->delete_item($id);
        echo json_encode(['status' => 'success', 'message' => 'Item berhasil dihapus.']);
    }

    // =========================================================
    // PRIVATE helpers
    // =========================================================
    private function _upload_foto_inspeksi($id_uji, $id_pengajuan)
    {
        if (!isset($this->upload)) {
            $this->load->library('upload');
        }

        $path = FCPATH . 'uploads/inspeksi_foto/' . $id_uji . '/';
        if (!is_dir($path)) mkdir($path, 0755, true);

        $batch = [];

        if (
            !empty($_FILES['foto_mekanik']['tmp_name'])
            && $_FILES['foto_mekanik']['error'] === UPLOAD_ERR_OK
        ) {
            $this->upload->initialize([
                'upload_path'   => $path,
                'allowed_types' => 'jpg|jpeg|png',
                'max_size'      => 5120,
                'file_name'     => 'mekanik_' . time(),
            ]);
            if ($this->upload->do_upload('foto_mekanik')) {
                $info    = $this->upload->data();
                $ket     = trim($this->input->post('ket_foto_mekanik') ?? '');
                $batch[] = [
                    'id_uji'      => $id_uji,
                    'jenis'       => 'mekanik',
                    'file_path'   => 'uploads/inspeksi_foto/' . $id_uji . '/' . $info['file_name'],
                    'keterangan'  => $ket ?: null,
                    'uploaded_at' => date('Y-m-d H:i:s'),
                ];
            }
        }

        $keterangan_arr = $this->input->post('ket_foto_temuan') ?? [];
        if (!empty($_FILES['foto_temuan']['name']) && is_array($_FILES['foto_temuan']['name'])) {
            $count = 0;
            foreach ($_FILES['foto_temuan']['name'] as $idx => $fname) {
                if ($count >= 10) break;
                if (empty($fname) || $_FILES['foto_temuan']['error'][$idx] !== UPLOAD_ERR_OK) continue;

                $_FILES['_foto_temuan_item_'] = [
                    'name'     => $_FILES['foto_temuan']['name'][$idx],
                    'type'     => $_FILES['foto_temuan']['type'][$idx],
                    'tmp_name' => $_FILES['foto_temuan']['tmp_name'][$idx],
                    'error'    => $_FILES['foto_temuan']['error'][$idx],
                    'size'     => $_FILES['foto_temuan']['size'][$idx],
                ];

                $this->upload->initialize([
                    'upload_path'   => $path,
                    'allowed_types' => 'jpg|jpeg|png',
                    'max_size'      => 5120,
                    'file_name'     => 'temuan_' . $idx . '_' . time(),
                ]);

                if ($this->upload->do_upload('_foto_temuan_item_')) {
                    $info    = $this->upload->data();
                    $ket     = isset($keterangan_arr[$idx]) ? trim($keterangan_arr[$idx]) : '';
                    $batch[] = [
                        'id_uji'      => $id_uji,
                        'jenis'       => 'temuan',
                        'file_path'   => 'uploads/inspeksi_foto/' . $id_uji . '/' . $info['file_name'],
                        'keterangan'  => $ket ?: null,
                        'uploaded_at' => date('Y-m-d H:i:s'),
                    ];
                    $count++;
                }
            }
        }

        if (!empty($batch)) {
            $this->db->insert_batch('uji_foto', $batch);
        }
    }

    private function _get_perbaikan_list($id_pengajuan)
    {
        $rows = $this->db
            ->select('pu.*, u.nama AS nama_verifikator')
            ->from('perbaikan_unit pu')
            ->join('users u', 'u.id_user = pu.id_verifikator', 'left')
            ->where('pu.id_pengajuan', $id_pengajuan)
            ->order_by('pu.id_perbaikan', 'ASC')
            ->get()->result();

        foreach ($rows as $pb) {
            $pb->lampiran = $this->db
                ->where('id_perbaikan', $pb->id_perbaikan)
                ->get('perbaikan_lampiran')->result();
        }
        return $rows;
    }

    private function _user_roles()
    {
        $raw = $this->session->userdata('roles');
        if (is_array($raw) && !empty($raw)) return array_map('intval', $raw);
        $r = (int) $this->session->userdata('role');
        return $r > 0 ? [$r] : [];
    }

    private function _has_role(array $required, array $user_roles)
    {
        foreach ($required as $r) {
            if (in_array((int) $r, $user_roles)) return true;
        }
        return false;
    }
}
