<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controller Pengajuan
 * 
 * Mengelola seluruh siklus pengajuan uji kelayakan unit kendaraan dan peralatan.
 * Menyediakan fungsionalitas:
 * - Halaman indeks & DataTables pengajuan
 * - Pembuatan pengajuan baru (unit baru & recommissioning unit lama)
 * - Pengubahan/Edit data pengajuan draft atau yang dikembalikan
 * - Pengajuan ulang (resubmit) unit yang telah diperbaiki
 * - Ekspor data history pengajuan ke format Excel/JSON
 * - Pengelolaan upload dokumen & foto kendaraan
 */
class Pengajuan extends CI_Controller
{
    /**
     * Konstruktor Controller Pengajuan
     * Memuat model, library, helper, dan memverifikasi status otentikasi login pengguna.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Pengajuan_model' => 'pengajuan_model']);
        $this->load->model(['Kendaraan_model' => 'kendaraan_model']);
        $this->load->library(['session', 'form_validation', 'upload']);
        $this->load->helper(['url', 'form']);

        // Proteksi Otorisasi: Alihkan ke halaman login jika sesi tidak aktif
        if (!$this->session->userdata('id_user')) {
            redirect('auth/login');
        }

        // Sinkronisasi nama departemen di database secara ter-optimasikan
        $this->_check_departments();
    }

    /**
     * Memastikan seluruh master departemen di tabel perusahaan up-to-date.
     * Menggunakan query 1-kali fetch & static flag untuk mengeliminasi query N+1 pada setiap siklus controller.
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

        // Ambil data perusahaan existing untuk verifikasi efisien tanpa query dalam loop
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
     * Halaman Indeks Daftar Pengajuan Uji Kelayakan
     * 
     * @return void Render view daftar pengajuan
     */
    public function index()
    {
        $data['title']      = 'Daftar Pengajuan';
        $data['user']       = $this->session->userdata();
        $data['perusahaan'] = $this->db->where('is_active', 1)->order_by('nama_perusahaan', 'ASC')->get('perusahaan')->result();
        $data['tipe_unit']  = $this->db->where('is_active', 1)->order_by('nama_tipe', 'ASC')->get('tipe_kendaraan')->result();
        
        $this->load->view('templates/header',  $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('pengajuan/index',   $data);
        $this->load->view('templates/footer',  $data);
    }

    /**
     * Endpoint AJAX Ekspor Data History Pengajuan ke Format Excel/JSON.
     * Menggunakan query JOIN ter-optimasikan pada model.
     * 
     * @return void Output JSON data ekspor + hash CSRF terbaru
     */
    public function get_export_history()
    {
        if (!$this->input->is_ajax_request()) show_404();

        if (!$this->session->userdata('id_user')) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'    => 'error',
                    'message'   => 'Sesi telah berakhir, silakan login kembali.',
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
        }

        try {
            $filters = [
                'status'     => trim($this->input->post('status')     ?? ''),
                'jenis'      => trim($this->input->post('jenis')      ?? ''),
                'departemen' => trim($this->input->post('departemen') ?? ''),
                'tgl_dari'   => trim($this->input->post('tgl_dari')   ?? ''),
                'tgl_sampai' => trim($this->input->post('tgl_sampai') ?? ''),
                'search'     => trim($this->input->post('search')     ?? ''),
            ];

            $roles     = $this->_user_roles();
            $user_dept = $this->session->userdata('departemen');

            if ($this->_has_role([7, 2], $roles) && !empty($user_dept)) {
                $filters['departemen'] = $user_dept;
            }

            $rows = $this->pengajuan_model->get_export_history_data($filters);

            $output = [
                'status'    => 'success',
                'data'      => $rows,
                'csrf_hash' => $this->security->get_csrf_hash(),
            ];
        } catch (Throwable $e) {
            log_message('error', 'Export History Error: ' . $e->getMessage());
            $output = [
                'status'    => 'error',
                'message'   => 'Gagal mengekspor data: ' . $e->getMessage(),
                'csrf_hash' => $this->security->get_csrf_hash(),
            ];
        } catch (Exception $e) {
            log_message('error', 'Export History Error: ' . $e->getMessage());
            $output = [
                'status'    => 'error',
                'message'   => 'Gagal mengekspor data: ' . $e->getMessage(),
                'csrf_hash' => $this->security->get_csrf_hash(),
            ];
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($output));
    }

    /**
     * Form Pembuatan Pengajuan Uji Kelayakan Baru.
     * Khusus diakses oleh Admin Departemen (Role 7) dan Super Admin (Role 1).
     * 
     * @return void Render view pengajuan/create
     */
    public function create()
    {
        $roles = $this->_user_roles();
        if (!$this->_has_role([1, 7], $roles)) {
            $this->session->set_flashdata('error', 'Hanya Admin Departemen yang dapat membuat pengajuan.');
            redirect('pengajuan');
        }

        $this->_check_schema_alat_berat();

        $data = [
            'title'          => 'Buat Pengajuan Uji Kelayakan',
            'user'           => $this->session->userdata(),
            'kendaraan'      => $this->kendaraan_model->get_kendaraan_lulus_eligible(),
            'tipe_kendaraan' => $this->db->where('is_active', 1)->order_by('is_alat_berat', 'DESC')->order_by('nama_tipe', 'ASC')->get('tipe_kendaraan')->result(),
            'perusahaan'     => $this->db->where('is_active', 1)->order_by('nama_perusahaan', 'ASC')->get('perusahaan')->result(),
        ];

        $this->load->view('templates/header',  $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('pengajuan/create',  $data);
        $this->load->view('templates/footer',  $data);
    }

    /**
     * Endpoint Simpan (Store) Pengajuan Baru.
     * Menangani pendaftaran unit kendaraan baru maupun pemilih unit lama (recommissioning),
     * pengunggahan berkas/lampiran foto, dan inisialisasi status pengajuan.
     * 
     * @return void Response JSON status simpan
     */
    public function store()
    {
        if (!$this->input->is_ajax_request()) show_404();
        
        $roles   = $this->_user_roles();
        $id_user = (int) $this->session->userdata('id_user');

        // Closure penyiapan struktur response JSON terstandar + token CSRF
        $response = function($status, $message, $data = []) {
            $output = [
                'status'    => $status,
                'message'   => $message,
                'csrfHash'  => $this->security->get_csrf_hash(),
            ];
            return array_merge($output, $data);
        };

        if (!$this->_has_role([1, 7], $roles)) {
            echo json_encode($response('error', 'Akses ditolak.'));
            return;
        }

        $mode_unit = $this->input->post('mode_unit');

        if ($mode_unit === 'lama') {
            // Mode Unit Lama (Recommissioning)
            $id_kendaraan   = (int) $this->input->post('id_kendaraan');
            $tipe_akses     = trim($this->input->post('tipe_akses_lama'));
            $tujuan         = trim($this->input->post('tujuan_lama'));
            $tipe_pengajuan = trim($this->input->post('tipe_pengajuan_lama') ?: 'recommissioning');

            if (!$id_kendaraan) {
                echo json_encode($response('error', 'Pilih kendaraan yang akan diajukan ulang.'));
                return;
            }

            $k = $this->kendaraan_model->get_by_id($id_kendaraan);
            if (!$k) {
                echo json_encode($response('error', 'Data kendaraan tidak ditemukan.'));
                return;
            }

            $this->db->trans_start();

            // Insert record pengajuan_uji baru
            $id_pengajuan = $this->pengajuan_model->insert_pengajuan([
                'id_kendaraan'      => $id_kendaraan,
                'id_pemohon'        => $id_user,
                'tipe_pengajuan'    => $tipe_pengajuan,
                'tipe_akses'        => $tipe_akses ?: null,
                'tujuan'            => $tujuan ?: null,
                'status'            => 'pengajuan_baru',
                'tanggal_pengajuan' => date('Y-m-d H:i:s'),
            ]);

            // Catat log approval awal
            $this->pengajuan_model->insert_approval([
                'id_pengajuan'   => $id_pengajuan,
                'id_approver'    => $id_user,
                'level_approval' => 'draft',
                'status'         => 'submitted',
                'catatan'        => 'Pengajuan uji kelayakan unit lama (' . $k->no_polisi . ') disubmit oleh Admin Dept',
                'created_at'     => date('Y-m-d H:i:s'),
            ]);

            // Tangani upload lampiran dokumen opsional (STNK / Maintenance Record)
            $upload_errs = [];
            if (!empty($_FILES['lampiran_stnk_lama']['name'])) {
                $err = $this->_upload_single_lampiran($id_pengajuan, 'stnk', 'lampiran_stnk_lama');
                if ($err) $upload_errs[] = 'STNK: ' . $err;
            }
            if (!empty($_FILES['lampiran_maintenance_lama']['name'])) {
                $err = $this->_upload_single_lampiran($id_pengajuan, 'maintenance_record', 'lampiran_maintenance_lama');
                if ($err) $upload_errs[] = 'Maintenance Record: ' . $err;
            }

            $this->_audit('buat_pengajuan', 'pengajuan_uji', $id_pengajuan);
            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                $msg = 'Pengajuan uji kelayakan unit lama berhasil dibuat (No. Pengajuan #' . str_pad($id_pengajuan, 4, '0', STR_PAD_LEFT) . ').';
                if (!empty($upload_errs)) {
                    $msg .= ' Namun ada catatan upload: ' . implode(', ', $upload_errs);
                }
                echo json_encode($response('success', $msg, ['id_pengajuan' => $id_pengajuan]));
            } else {
                echo json_encode($response('error', 'Gagal menyimpan pengajuan unit lama. Silakan coba lagi.'));
            }
            return;
        }

        // Mode Unit Baru
        $no_polisi     = strtoupper(trim((string) $this->input->post('no_polisi')));
        $nomor_unit    = strtoupper(trim((string) $this->input->post('nomor_unit')));
        $model_unit    = trim((string) $this->input->post('model_unit'));
        $id_tipe       = (int) $this->input->post('id_tipe_kendaraan');
        $merk          = trim((string) $this->input->post('merk'));
        $tipe          = trim((string) $this->input->post('tipe'));
        $tahun         = (int) $this->input->post('tahun');
        $perusahaan    = trim((string) $this->input->post('perusahaan'));
        $tipe_akses    = trim((string) $this->input->post('tipe_akses'));
        $tujuan        = trim((string) $this->input->post('tujuan'));
        $is_unit_baru  = (int) $this->input->post('is_unit_baru');

        if (!$nomor_unit || !$id_tipe || !$merk || !$perusahaan) {
            echo json_encode($response('error', 'Nomor Unit, Tipe Kendaraan, Merk, dan Perusahaan wajib diisi.'));
            return;
        }

        // Cek duplikasi nomor unit
        if ($this->kendaraan_model->is_nomor_unit_exists($nomor_unit)) {
            echo json_encode($response('error', 'Nomor Unit <strong>' . html_escape($nomor_unit) . '</strong> sudah terdaftar dalam sistem. Gunakan opsi Unit Lama jika ingin mengajukan ulang.'));
            return;
        }

        $this->db->trans_start();

        // Register master kendaraan baru
        $id_kendaraan = $this->kendaraan_model->insert_kendaraan([
            'no_polisi'         => $no_polisi ?: $nomor_unit,
            'nomor_unit'        => $nomor_unit,
            'model_unit'        => $model_unit ?: null,
            'id_tipe_kendaraan' => $id_tipe,
            'merk'              => $merk,
            'tipe'              => $tipe ?: null,
            'tahun'             => $tahun ?: null,
            'perusahaan'        => $perusahaan,
            'is_unit_baru'      => $is_unit_baru,
            'status_uji'        => 'belum_uji',
            'created_at'        => date('Y-m-d H:i:s'),
        ]);

        // Insert record pengajuan_uji
        $id_pengajuan = $this->pengajuan_model->insert_pengajuan([
            'id_kendaraan'      => $id_kendaraan,
            'id_pemohon'        => $id_user,
            'tipe_pengajuan'    => 'baru',
            'tipe_akses'        => $tipe_akses ?: null,
            'tujuan'            => $tujuan ?: null,
            'status'            => 'pengajuan_baru',
            'tanggal_pengajuan' => date('Y-m-d H:i:s'),
        ]);

        // Insert log approval awal
        $this->pengajuan_model->insert_approval([
            'id_pengajuan'   => $id_pengajuan,
            'id_approver'    => $id_user,
            'level_approval' => 'draft',
            'status'         => 'submitted',
            'catatan'        => 'Pengajuan baru disubmit oleh Admin Dept',
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        // Upload lampiran dokumen & foto
        $upload_errors = $this->_upload_lampiran($id_pengajuan);

        $this->_audit('buat_pengajuan', 'pengajuan_uji', $id_pengajuan);
        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            $msg = 'Pengajuan uji kelayakan unit baru berhasil dibuat (No. Pengajuan #' . str_pad($id_pengajuan, 4, '0', STR_PAD_LEFT) . ').';
            if (!empty($upload_errors)) {
                $msg .= ' Catatan upload: ' . implode(', ', $upload_errors);
            }
            echo json_encode($response('success', $msg, ['id_pengajuan' => $id_pengajuan]));
        } else {
            echo json_encode($response('error', 'Gagal menyimpan data pengajuan. Silakan coba lagi.'));
        }
    }

    /**
     * Endpoint AJAX DataTables untuk menyajikan daftar pengajuan uji kelayakan.
     * Menggunakan query terpaginasi dan penyaringan berhak akses.
     * 
     * @return void Output JSON DataTables + Hash CSRF
     */
    public function get_data()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $start   = max(0, (int) $this->input->post('start'));
        $length  = (int) $this->input->post('length');
        $draw    = (int) $this->input->post('draw');
        $roles   = $this->_user_roles();
        $id_user = (int) $this->session->userdata('id_user');

        // Proteksi beban server: Batasi jumlah data per query (default 10, max 500)
        if ($length <= 0) {
            $length = 10;
        }
        $length = min($length, 500);

        $filters = [
            'status'     => trim($this->input->post('status')     ?? ''),
            'jenis'      => trim($this->input->post('jenis')      ?? ''),
            'tgl_dari'   => trim($this->input->post('tgl_dari')   ?? ''),
            'tgl_sampai' => trim($this->input->post('tgl_sampai') ?? ''),
            'search'     => trim($this->input->post('search[value]') ?? ''),
        ];

        // Filtering scoping hak akses pengguna
        $user_dept = $this->session->userdata('departemen');
        if (in_array(7, $roles) && !in_array(1, $roles)) {
            $filters['id_pemohon'] = $id_user;
        } elseif (in_array(2, $roles) && !in_array(1, $roles) && !empty($user_dept)) {
            $filters['departemen'] = $user_dept;
        }

        $total_records    = $this->pengajuan_model->count_all($filters);
        $filtered_records = $this->pengajuan_model->count_filtered($filters);
        $data_rows        = $this->pengajuan_model->get_datatable($start, $length, $filters);

        $data = [];
        $no   = $start + 1;

        foreach ($data_rows as $r) {
            $nomor_unit_html = !empty($r->nomor_unit) 
                ? html_escape($r->nomor_unit) 
                : '<span class="text-muted small">—</span>';
            
            $id_display_html = '<span class="badge bg-light text-dark font-monospace border">#PU-' . str_pad($r->id_pengajuan, 4, '0', STR_PAD_LEFT) . '</span>';
            
            $raw_tipe_pengajuan  = $r->tipe_pengajuan ?: 'baru';
            $clean_tipe_pengajuan = ucwords(str_replace('_', ' ', $raw_tipe_pengajuan));
            $tipe_pengajuan_html  = '<span class="badge bg-light text-dark border text-nowrap">' . html_escape($clean_tipe_pengajuan) . '</span>';
            $tgl_pengajuan_html   = '<span class="text-nowrap">' . date('d/m/Y H:i', strtotime($r->tanggal_pengajuan)) . '</span>';

            $data[] = [
                'no'                => $no++,
                'id_display'        => $id_display_html,
                'pemohon'           => html_escape($r->nama_pemohon ?: '-'),
                'nomor_unit'        => $nomor_unit_html,
                'no_polisi'         => html_escape($r->no_polisi ?: 'N/A'),
                'jenis_kendaraan'   => html_escape($r->jenis_kendaraan ?: '-'),
                'tipe_pengajuan'    => $tipe_pengajuan_html,
                'tipe_akses'        => badge_tipe_akses($r->tipe_akses),
                'status'            => $this->_badge_status($r->status),
                'tgl_pengajuan'     => $tgl_pengajuan_html,
                'tanggal_pengajuan' => date('d/m/Y H:i', strtotime($r->tanggal_pengajuan)),
                'nama_pemohon'      => html_escape($r->nama_pemohon ?: '-'),
                'merk_tipe'         => html_escape($r->merk) . ' ' . html_escape($r->tipe),
                'perusahaan'        => html_escape($r->perusahaan),
                'aksi'              => $this->_tombol_aksi($r),
            ];
        }

        echo json_encode([
            'draw'            => $draw,
            'recordsTotal'    => $total_records,
            'recordsFiltered' => $filtered_records,
            'data'            => $data,
            'csrfHash'        => $this->security->get_csrf_hash(),
        ]);
    }

    /**
     * Endpoint AJAX untuk mengambil detail pengajuan, lampiran, jadwal, uji, dan riwayat approval.
     * 
     * @param int|null $id ID Pengajuan
     * @return void Output JSON detail pengajuan
     */
    public function detail($id = null)
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id_pengajuan = (int) ($id ?: $this->input->post('id_pengajuan'));
        $roles        = $this->_user_roles();
        $id_user      = (int) $this->session->userdata('id_user');
        $filters      = [];

        $user_dept = $this->session->userdata('departemen');
        if (in_array(7, $roles) && !in_array(1, $roles)) {
            $filters['id_pemohon'] = $id_user;
        } elseif (in_array(2, $roles) && !in_array(1, $roles) && !empty($user_dept)) {
            $filters['departemen'] = $user_dept;
        }

        $pengajuan = $this->pengajuan_model->get_detail($id_pengajuan, $filters);
        if (!$pengajuan) {
            echo json_encode([
                'status'    => 'error', 
                'message'   => 'Data pengajuan tidak ditemukan atau Anda tidak memiliki akses.',
                'csrfHash'  => $this->security->get_csrf_hash()
            ]);
            return;
        }

        // Ambil relasi dokumen, approval, jadwal, dan uji
        $lampiran  = $this->pengajuan_model->get_lampiran($id_pengajuan);
        $approval  = $this->pengajuan_model->get_approval($id_pengajuan);
        $jadwal    = $this->pengajuan_model->get_jadwal($id_pengajuan);
        $uji       = $this->pengajuan_model->get_uji($id_pengajuan);
        $perbaikan = $this->pengajuan_model->get_perbaikan_with_lampiran($id_pengajuan);

        echo json_encode([
            'status'    => 'success',
            'data'      => [
                'pengajuan' => $pengajuan,
                'lampiran'  => $lampiran,
                'approval'  => $approval,
                'jadwal'    => $jadwal,
                'uji'       => $uji,
                'perbaikan' => $perbaikan,
            ],
            'csrfHash'  => $this->security->get_csrf_hash(),
        ]);
    }

    /**
     * Endpoint AJAX untuk mendapatkan informasi kendaraan saat memilih dropdown pada form pengajuan.
     * 
     * @return void JSON data kendaraan
     */
    public function get_kendaraan_info()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id = (int) $this->input->post('id_kendaraan');
        $k  = $this->kendaraan_model->get_by_id($id);

        if (!$k) {
            echo json_encode(['status' => 'error', 'message' => 'Kendaraan tidak ditemukan.', 'csrfHash' => $this->security->get_csrf_hash()]);
            return;
        }

        echo json_encode(['status' => 'success', 'data' => $k, 'csrfHash' => $this->security->get_csrf_hash()]);
    }

    /**
     * Halaman Edit Pengajuan (Untuk status Draft atau Ditolak Manager).
     * 
     * @param int|null $id_pengajuan ID Pengajuan
     * @return void Render view edit
     */
    public function edit($id_pengajuan = null)
    {
        $id_pengajuan = (int) $id_pengajuan;
        $roles        = $this->_user_roles();
        $id_user      = (int) $this->session->userdata('id_user');

        if (!$this->_has_role([1, 7], $roles)) {
            $this->session->set_flashdata('error', 'Akses ditolak.');
            redirect('pengajuan');
        }

        $pengajuan = $this->pengajuan_model->get_detail($id_pengajuan);
        if (!$pengajuan) {
            $this->session->set_flashdata('error', 'Data pengajuan tidak ditemukan.');
            redirect('pengajuan');
        }

        // Hanya boleh diedit jika status draft atau ditolak_manager
        if (!in_array($pengajuan->status, ['draft', 'ditolak_manager'])) {
            $this->session->set_flashdata('error', 'Pengajuan dengan status ini tidak dapat diedit.');
            redirect('pengajuan');
        }

        // Admin Dept hanya bisa edit pengajuannya sendiri (kecuali Super Admin)
        if (in_array(7, $roles) && !in_array(1, $roles) && $pengajuan->id_pemohon != $id_user) {
            $this->session->set_flashdata('error', 'Anda hanya dapat mengedit pengajuan milik Anda sendiri.');
            redirect('pengajuan');
        }

        $this->_check_schema_alat_berat();

        $lampiran_raw = $this->pengajuan_model->get_lampiran($id_pengajuan);
        $lampiran     = [];
        foreach ($lampiran_raw as $l) {
            $lampiran[$l->jenis_lampiran] = $l;
        }

        $data = [
            'title'          => 'Edit Pengajuan Uji Kelayakan #' . str_pad($id_pengajuan, 4, '0', STR_PAD_LEFT),
            'user'           => $this->session->userdata(),
            'pengajuan'      => $pengajuan,
            'lampiran'       => $lampiran,
            'tipe_kendaraan' => $this->db->where('is_active', 1)->order_by('is_alat_berat', 'DESC')->order_by('nama_tipe', 'ASC')->get('tipe_kendaraan')->result(),
            'perusahaan'     => $this->db->where('is_active', 1)->order_by('nama_perusahaan', 'ASC')->get('perusahaan')->result(),
        ];

        $this->load->view('templates/header',  $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('pengajuan/edit',    $data);
        $this->load->view('templates/footer',  $data);
    }

    /**
     * Endpoint AJAX Update Data Pengajuan.
     * Mengakomodasi pengubahan data kendaraan, tipe akses, serta penggantian berkas lampiran.
     * 
     * @return void Response JSON status update
     */
    public function update()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $roles        = $this->_user_roles();
        $id_user      = (int) $this->session->userdata('id_user');
        $id_pengajuan = (int) $this->input->post('id_pengajuan');

        $response = function($status, $message, $data = []) {
            $output = [
                'status'    => $status,
                'message'   => $message,
                'csrfHash'  => $this->security->get_csrf_hash(),
            ];
            return array_merge($output, $data);
        };

        if (!$this->_has_role([1, 7], $roles)) {
            echo json_encode($response('error', 'Akses ditolak.'));
            return;
        }

        $pengajuan = $this->pengajuan_model->get_detail($id_pengajuan);
        if (!$pengajuan) {
            echo json_encode($response('error', 'Data pengajuan tidak ditemukan.'));
            return;
        }

        if (!in_array($pengajuan->status, ['draft', 'ditolak_manager'])) {
            echo json_encode($response('error', 'Pengajuan dengan status ini tidak dapat diperbarui.'));
            return;
        }

        if (in_array(7, $roles) && !in_array(1, $roles) && $pengajuan->id_pemohon != $id_user) {
            echo json_encode($response('error', 'Anda hanya dapat memperbarui pengajuan milik Anda sendiri.'));
            return;
        }

        // Ambil input form
        $no_polisi     = strtoupper(trim($this->input->post('no_polisi')));
        $nomor_unit    = strtoupper(trim($this->input->post('nomor_unit')));
        $model_unit    = trim($this->input->post('model_unit'));
        $id_tipe       = (int) $this->input->post('id_tipe_kendaraan');
        $merk          = trim($this->input->post('merk'));
        $tipe          = trim($this->input->post('tipe'));
        $tahun         = (int) $this->input->post('tahun');
        $perusahaan    = trim($this->input->post('perusahaan'));
        $tipe_akses    = trim($this->input->post('tipe_akses'));
        $tujuan        = trim($this->input->post('tujuan'));
        $is_unit_baru  = (int) $this->input->post('is_unit_baru');

        if (!$nomor_unit || !$id_tipe || !$merk || !$perusahaan) {
            echo json_encode($response('error', 'Nomor Unit, Tipe Kendaraan, Merk, dan Perusahaan wajib diisi.'));
            return;
        }

        // Cek duplikasi nomor unit (abaikan ID kendaraan sendiri)
        if ($this->kendaraan_model->is_nomor_unit_exists($nomor_unit, $pengajuan->id_kendaraan)) {
            echo json_encode($response('error', 'Nomor Unit <strong>' . html_escape($nomor_unit) . '</strong> sudah terdaftar pada kendaraan lain.'));
            return;
        }

        $this->db->trans_start();

        // 1. Update data master kendaraan
        $this->kendaraan_model->update_kendaraan($pengajuan->id_kendaraan, [
            'no_polisi'         => $no_polisi ?: $nomor_unit,
            'nomor_unit'        => $nomor_unit,
            'model_unit'        => $model_unit ?: null,
            'id_tipe_kendaraan' => $id_tipe,
            'merk'              => $merk,
            'tipe'              => $tipe ?: null,
            'tahun'             => $tahun ?: null,
            'perusahaan'        => $perusahaan,
            'is_unit_baru'      => $is_unit_baru,
            'updated_at'        => date('Y-m-d H:i:s'),
        ]);

        // 2. Update data pengajuan_uji & ubah status ke pengajuan_baru (agar masuk ke queue manager lagi)
        $this->db->where('id_pengajuan', $id_pengajuan)->update('pengajuan_uji', [
            'tipe_akses'        => $tipe_akses ?: null,
            'tujuan'            => $tujuan ?: null,
            'status'            => 'pengajuan_baru',
            'tanggal_pengajuan' => date('Y-m-d H:i:s'),
        ]);

        // 3. Catat log approval resubmit
        $this->pengajuan_model->insert_approval([
            'id_pengajuan'   => $id_pengajuan,
            'id_approver'    => $id_user,
            'level_approval' => 'edit_resubmit',
            'status'         => 'submitted',
            'catatan'        => 'Pengajuan diperbarui & dikirim ulang oleh Admin Dept',
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        // 4. Update file lampiran jika diunggah file baru
        $jenis_list   = ['sertifikasi', 'stnk', 'unit_depan', 'unit_belakang', 'unit_kiri', 'unit_kanan', 'maintenance_record'];
        $upload_errs  = [];

        foreach ($jenis_list as $jenis) {
            $field = 'lampiran_' . $jenis;
            if (!empty($_FILES[$field]['name'])) {
                $err = $this->_upload_replace_lampiran($id_pengajuan, $jenis, $field);
                if ($err) $upload_errs[] = strtoupper($jenis) . ': ' . $err;
            }
        }

        $this->_audit('update_pengajuan', 'pengajuan_uji', $id_pengajuan);
        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            $no  = '#PU-' . str_pad($id_pengajuan, 4, '0', STR_PAD_LEFT);
            $msg = 'Pengajuan <strong>' . $no . '</strong> berhasil diperbarui dan dikirim ulang ke <strong>Dept Manager</strong>.';
            if (!empty($upload_errs)) {
                $msg .= ' Catatan upload: ' . implode(', ', $upload_errs);
            }
            echo json_encode($response('success', $msg));
        } else {
            echo json_encode($response('error', 'Gagal memperbarui pengajuan. Silakan coba lagi.'));
        }
    }

    /**
     * Endpoint AJAX Pengajuan Ulang (Resubmit) Unit pasca perbaikan atau pasca penolakan.
     * Mengembalikan status berkas ke antrean Dept Manager.
     * 
     * @return void Response JSON status resubmit
     */
    public function resubmit()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $roles   = $this->_user_roles();
        $id_user = (int) $this->session->userdata('id_user');

        if (!$this->_has_role([1, 7], $roles)) {
            echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
            return;
        }

        $id_pengajuan = (int) $this->input->post('id_pengajuan');
        $alasan       = trim((string) $this->input->post('alasan_pengajuan_ulang'));

        if (empty($alasan)) {
            echo json_encode(['status' => 'error', 'message' => 'Alasan pengajuan ulang wajib diisi.']);
            return;
        }

        $pengajuan = $this->pengajuan_model->get_detail($id_pengajuan);
        if (!$pengajuan) {
            echo json_encode(['status' => 'error', 'message' => 'Data pengajuan tidak ditemukan.']);
            return;
        }

        $status_boleh = ['tidak_lulus_inspeksi', 'ditolak_ktt', 'ditolak_ohs_supt'];
        if (!in_array($pengajuan->status, $status_boleh)) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Pengajuan dengan status <strong>' . html_escape($pengajuan->status) . '</strong> tidak dapat diajukan ulang.',
            ]);
            return;
        }

        if (in_array(7, $roles) && !in_array(1, $roles) && $pengajuan->id_pemohon != $id_user) {
            echo json_encode(['status' => 'error', 'message' => 'Anda hanya dapat mengajukan ulang pengajuan milik Anda sendiri.']);
            return;
        }

        $this->db->trans_start();

        // Update status pengajuan ulang
        $this->db->where('id_pengajuan', $id_pengajuan)->update('pengajuan_uji', [
            'status'                 => 'pengajuan_ulang',
            'alasan_pengajuan_ulang' => $alasan,
            'tanggal_pengajuan'      => date('Y-m-d H:i:s'),
        ]);

        // Catat log approval
        $this->db->insert('pengajuan_approval', [
            'id_pengajuan'   => $id_pengajuan,
            'id_approver'    => $id_user,
            'level_approval' => 'resubmit_admin_dept',
            'status'         => 'approved',
            'catatan'        => 'Pengajuan ulang: ' . $alasan,
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        $this->_audit('resubmit_pengajuan', 'pengajuan_uji', $id_pengajuan);
        $this->db->trans_complete();

        if (!$this->db->trans_status()) {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memproses pengajuan ulang.']);
            return;
        }

        $no = '#PU-' . str_pad($id_pengajuan, 4, '0', STR_PAD_LEFT);
        echo json_encode([
            'status'  => 'success',
            'message' => 'Pengajuan <strong>' . $no . '</strong> berhasil diajukan ulang. Kini dalam antrian <strong>Dept Manager</strong> untuk di-review kembali.',
        ]);
    }

    /**
     * Helper privat verifikasi skema kolom is_alat_berat pada tipe_kendaraan.
     * 
     * @return void
     */
    private function _check_schema_alat_berat()
    {
        static $checked = false;
        if ($checked) return;
        $checked = true;

        if (!$this->db->field_exists('is_alat_berat', 'tipe_kendaraan')) {
            $this->db->query("ALTER TABLE `tipe_kendaraan` ADD COLUMN `is_alat_berat` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_active`");
            $this->db->query("UPDATE `tipe_kendaraan` SET `is_alat_berat` = 1 WHERE LOWER(nama_tipe) REGEXP 'excavator|dump truck|hd|dozer|bulldozer|grader|loader|crane|forklift|scraper|compactor|backhoe|heavy|alat berat' OR LOWER(kode_tipe) REGEXP 'ex|dt|hd|dz|bd|gr|ld|cr|fl|he'");
        }
    }

    /**
     * Helper privat upload multiple lampiran dokumen dan foto unit baru.
     * 
     * @param int $id_pengajuan ID Pengajuan
     * @return array List pesan error upload jika ada
     */
    private function _upload_lampiran($id_pengajuan)
    {
        $errors     = [];
        $jenis_list = ['sertifikasi', 'stnk', 'unit_depan', 'unit_belakang', 'unit_kiri', 'unit_kanan'];
        $path       = FCPATH . 'uploads/lampiran/' . $id_pengajuan . '/';
        if (!is_dir($path)) mkdir($path, 0755, true);

        foreach ($jenis_list as $jenis) {
            $field = 'lampiran_' . $jenis;
            if (empty($_FILES[$field]['name'])) continue;

            $unique_name = $jenis . '_' . time() . '_' . substr(md5(uniqid(mt_rand(), true)), 0, 6);
            $this->upload->initialize([
                'upload_path'   => $path, 
                'allowed_types' => 'jpg|jpeg|png|pdf|doc|docx|webp|JPG|JPEG|PNG|PDF|DOC|DOCX|WEBP', 
                'max_size'      => 10240, 
                'file_name'     => $unique_name, 
                'overwrite'     => false
            ]);

            if (!$this->upload->do_upload($field)) {
                $errors[] = $this->upload->display_errors('', '');
            } else {
                $info = $this->upload->data();
                $this->pengajuan_model->insert_lampiran([
                    'id_pengajuan'   => $id_pengajuan, 
                    'jenis_lampiran' => $jenis, 
                    'file_path'      => 'uploads/lampiran/' . $id_pengajuan . '/' . $info['file_name'], 
                    'uploaded_at'    => date('Y-m-d H:i:s')
                ]);
            }
        }
        return $errors;
    }

    /**
     * Helper privat upload 1 lampiran dokumen tunggal.
     * 
     * @param int $id_pengajuan ID Pengajuan
     * @param string $jenis Jenis lampiran
     * @param string $field_name Nama field form input file
     * @return string|null String pesan error jika gagal, null jika sukses
     */
    private function _upload_single_lampiran($id_pengajuan, $jenis, $field_name)
    {
        $path = FCPATH . 'uploads/lampiran/' . $id_pengajuan . '/';
        if (!is_dir($path)) mkdir($path, 0755, true);

        $unique_name = $jenis . '_' . time() . '_' . substr(md5(uniqid(mt_rand(), true)), 0, 6);
        $this->upload->initialize([
            'upload_path'   => $path,
            'allowed_types' => 'jpg|jpeg|png|pdf|doc|docx|xls|xlsx|webp|JPG|JPEG|PNG|PDF|DOC|DOCX|XLS|XLSX|WEBP',
            'max_size'      => 10240,
            'file_name'     => $unique_name,
            'overwrite'     => false,
        ]);

        if (!$this->upload->do_upload($field_name)) {
            return $this->upload->display_errors('', '');
        }

        $info = $this->upload->data();
        $this->pengajuan_model->insert_lampiran([
            'id_pengajuan'   => $id_pengajuan,
            'jenis_lampiran' => $jenis,
            'file_path'      => 'uploads/lampiran/' . $id_pengajuan . '/' . $info['file_name'],
            'uploaded_at'    => date('Y-m-d H:i:s'),
        ]);

        return null;
    }

    /**
     * Helper privat pembentuk elemen HTML badge status pengajuan.
     * 
     * @param string $status Kode status pengajuan
     * @return string HTML Badge Status
     */
    private function _badge_status($status)
    {
        $map = [
            'draft'                  => ['bg-secondary text-white',  'Draft'],
            'pengajuan_baru'         => ['bg-primary text-white',    'Pengajuan Baru'],
            'pengajuan_ulang'        => ['bg-info text-white',       'Pengajuan Ulang'],
            'diterima_manager'       => ['bg-warning text-dark',     'Diterima Manager'],
            'ditolak_manager'        => ['bg-danger text-white',     'Ditolak Manager'],
            'dijadwalkan'            => ['bg-primary text-white',    'Dijadwalkan Inspeksi'],
            'lulus_inspeksi'         => ['bg-success text-white',    'Lulus — Menunggu OHS Supt'],
            'tidak_lulus_inspeksi'   => ['bg-danger text-white',     'Tidak Lulus — Dikembalikan'],
            'inspeksi_ulang'         => ['bg-info text-white',       'Siap Inspeksi Ulang'],
            'selesai_inspeksi'       => ['bg-warning text-dark',     'Selesai Inspeksi'],
            'diterima_admin_ohs'     => ['bg-info text-white',       'Diterima Admin OHS'],
            'ditolak_admin_ohs'      => ['bg-danger text-white',     'Ditolak Admin OHS'],
            'diterima_ohs_supt'      => ['bg-info text-white',       'Diterima OHS Superintendent'],
            'ditolak_ohs_supt'       => ['bg-danger text-white',     'Ditolak OHS Superintendent'],
            'acc_ktt'                => ['bg-success text-white',    'Disetujui KTT'],
            'ditolak_ktt'            => ['bg-danger text-white',     'Ditolak KTT'],
            'stiker_keluar'          => ['bg-success text-white',    'Stiker Sudah Keluar'],
            'rejected'               => ['bg-danger text-white',     'Ditolak'],
        ];
        $cfg = $map[$status] ?? ['bg-secondary text-white', html_escape($status)];
        return '<span class="badge ' . $cfg[0] . ' text-nowrap">' . $cfg[1] . '</span>';
    }

    /**
     * Helper privat penyusun kumpulan tombol aksi pada baris tabel DataTables berdasarkan role & status.
     * 
     * @param object $row Object data baris pengajuan
     * @return string HTML konsol tombol aksi
     */
    private function _tombol_aksi($row)
    {
        $id    = $row->id_pengajuan;
        $roles = $this->_user_roles();
        $uid   = (int) $this->session->userdata('id_user');

        $btn  = '<div class="d-flex gap-1 justify-content-center text-nowrap flex-nowrap">';
        $btn .= '<button class="btn btn-sm btn-outline-primary py-0 btn-detail" data-id="' . $id . '" title="Lihat Detail"><i class="bi bi-eye"></i></button>';

        if (
            $this->_has_role([1, 7], $roles)
            && in_array($row->status, ['draft', 'ditolak_manager'])
            && ($uid == $row->id_pemohon || in_array(1, $roles))
        ) {
            $btn .= '<a href="' . site_url('pengajuan/edit/' . $id) . '"'
                . ' class="btn btn-sm btn-outline-warning py-0 text-dark fw-semibold"'
                . ' title="Edit & Kirim Ulang ke Manager">'
                . '<i class="bi bi-pencil me-1"></i>Edit</a>';
        }

        if ($this->_has_role([1, 6], $roles) && in_array($row->status, ['pengajuan_baru', 'pengajuan_ulang', 'ditolak_admin_ohs'])) {
            $btn .= '<button class="btn btn-sm btn-success py-0 btn-approve" data-id="' . $id . '" data-level="dept_manager" title="Setujui"><i class="bi bi-check-lg"></i></button>';
            $btn .= '<button class="btn btn-sm btn-danger  py-0 btn-reject"  data-id="' . $id . '" data-level="dept_manager" title="Tolak"><i class="bi bi-x-lg"></i></button>';
        }

        if ($this->_has_role([1, 5], $roles) && $row->status === 'diterima_manager') {
            $btn .= '<button class="btn btn-sm btn-success py-0 btn-approve" data-id="' . $id . '" data-level="admin_ohs" title="Setujui & Jadwalkan"><i class="bi bi-calendar-check"></i></button>';
            $btn .= '<button class="btn btn-sm btn-danger  py-0 btn-reject"  data-id="' . $id . '" data-level="admin_ohs" title="Tolak"><i class="bi bi-x-lg"></i></button>';
        }

        if ($this->_has_role([1, 5], $roles) && $row->status === 'acc_ktt') {
            $btn .= '<button class="btn btn-sm btn-success py-0 btn-release-stiker" data-id="' . $id . '" title="Terbitkan Stiker"><i class="bi bi-patch-check"></i></button>';
        }

        if ($this->_has_role([1, 3], $roles) && $row->status === 'diterima_admin_ohs') {
            $btn .= '<button class="btn btn-sm btn-success py-0 btn-approve" data-id="' . $id . '" data-level="ohs_supt" title="Setujui OHS Supt"><i class="bi bi-check-lg"></i></button>';
            $btn .= '<button class="btn btn-sm btn-danger  py-0 btn-reject"  data-id="' . $id . '" data-level="ohs_supt" title="Tolak"><i class="bi bi-x-lg"></i></button>';
        }

        if ($this->_has_role([1, 2], $roles) && $row->status === 'diterima_ohs_supt') {
            $btn .= '<button class="btn btn-sm btn-success py-0 btn-approve" data-id="' . $id . '" data-level="ktt" title="ACC KTT"><i class="bi bi-check-lg"></i></button>';
            $btn .= '<button class="btn btn-sm btn-danger  py-0 btn-reject"  data-id="' . $id . '" data-level="ktt" title="Tolak"><i class="bi bi-x-lg"></i></button>';
        }

        if ($this->_has_role([1, 4], $roles) && $row->status === 'dijadwalkan') {
            $btn .= '<a href="' . site_url('checklist/form/' . $id) . '" class="btn btn-sm btn-warning py-0" title="Isi Form Inspeksi"><i class="bi bi-tools"></i></a>';
        }
        if ($this->_has_role([1, 4], $roles) && $row->status === 'inspeksi_ulang') {
            $btn .= '<a href="' . site_url('checklist/form/' . $id) . '" class="btn btn-sm btn-info py-0 text-white" title="Verifikasi Hasil Perbaikan"><i class="bi bi-patch-check"></i></a>';
        }

        $status_boleh_ulang = ['ditolak_ktt', 'ditolak_ohs_supt'];
        if (
            $this->_has_role([1, 7], $roles)
            && $row->status === 'tidak_lulus_inspeksi'
            && ($uid == $row->id_pemohon || in_array(1, $roles))
        ) {
            $btn .= '<a href="' . site_url('perbaikan/form/' . $id) . '"'
                . ' class="btn btn-sm btn-danger py-0 fw-semibold text-white"'
                . ' title="Input Data Perbaikan Unit">'
                . '<i class="bi bi-tools me-1"></i>Input Perbaikan</a>';
        }

        if (
            $this->_has_role([1, 7], $roles)
            && in_array($row->status, $status_boleh_ulang)
            && ($uid == $row->id_pemohon || in_array(1, $roles))
        ) {
            $info_btn = 'Pengajuan dikembalikan — ajukan ulang ke Dept Manager';
            $btn .= '<button class="btn btn-sm btn-warning py-0 btn-resubmit fw-semibold"'
                . ' data-id="' . $id . '"'
                . ' data-polisi="' . html_escape($row->no_polisi) . '"'
                . ' data-status="' . html_escape($row->status) . '"'
                . ' data-info="' . html_escape($info_btn) . '"'
                . ' title="Ajukan Ulang">'
                . '<i class="bi bi-arrow-repeat me-1"></i>Ajukan Ulang</button>';
        }

        $btn .= '</div>';
        return $btn;
    }

    /**
     * Helper privat penggantian file lampiran dokumen/foto.
     * 
     * @param int $id_pengajuan ID Pengajuan
     * @param string $jenis Jenis lampiran
     * @param string $field_name Field file form
     * @return string|null String error jika ada, null jika sukses
     */
    private function _upload_replace_lampiran($id_pengajuan, $jenis, $field_name)
    {
        $path = FCPATH . 'uploads/lampiran/' . $id_pengajuan . '/';
        if (!is_dir($path)) mkdir($path, 0755, true);

        $doc_types = 'jpg|jpeg|png|pdf|doc|docx|xls|xlsx';
        $img_types = 'jpg|jpeg|png';
        $allowed   = ($jenis === 'stnk' || $jenis === 'maintenance_record') ? $doc_types : $img_types;

        $unique_name = $jenis . '_' . time() . '_' . substr(md5(uniqid(mt_rand(), true)), 0, 6);

        $this->upload->initialize([
            'upload_path'   => $path,
            'allowed_types' => $allowed,
            'max_size'      => ($jenis === 'maintenance_record') ? 10240 : 5120,
            'file_name'     => $unique_name,
            'overwrite'     => false,
        ]);

        if (!$this->upload->do_upload($field_name)) {
            return $this->upload->display_errors('', '');
        }

        $info     = $this->upload->data();
        $new_path = 'uploads/lampiran/' . $id_pengajuan . '/' . $info['file_name'];

        $existing = $this->db
            ->where('id_pengajuan', $id_pengajuan)
            ->where('jenis_lampiran', $jenis)
            ->get('pengajuan_lampiran')
            ->row();

        if ($existing) {
            $this->db
                ->where('id_lampiran', $existing->id_lampiran)
                ->update('pengajuan_lampiran', [
                    'file_path'   => $new_path,
                    'uploaded_at' => date('Y-m-d H:i:s'),
                ]);
        } else {
            $this->pengajuan_model->insert_lampiran([
                'id_pengajuan'   => $id_pengajuan,
                'jenis_lampiran' => $jenis,
                'file_path'      => $new_path,
                'uploaded_at'    => date('Y-m-d H:i:s'),
            ]);
        }

        return null;
    }

    /**
     * Helper privat mengambil daftar ID role pengguna yang sedang login.
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
     * Helper privat pengecekan apakah pengguna memiliki salah satu role yang disyaratkan.
     * 
     * @param array $required List ID role yang disyaratkan
     * @param array $user_roles List ID role yang dimiliki user
     * @return bool True jika memenuhi hak akses
     */
    private function _has_role(array $required, array $user_roles)
    {
        foreach ($required as $r) {
            if (in_array((int)$r, $user_roles)) return true;
        }
        return false;
    }

    /**
     * Helper privat mencatat aktivitas pengguna ke tabel audit_log.
     * 
     * @param string $aksi Jenis aksi aktivitas
     * @param string $tabel Nama tabel sasaran
     * @param int|string $id_ref ID referensi record
     * @return void
     */
    private function _audit($aksi, $tabel, $id_ref)
    {
        $this->db->insert('audit_log', [
            'id_user'    => $this->session->userdata('id_user'), 
            'aksi'       => $aksi, 
            'tabel'      => $tabel, 
            'id_ref'     => $id_ref, 
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
