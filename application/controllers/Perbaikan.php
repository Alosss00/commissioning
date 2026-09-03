<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controller Perbaikan
 * 
 * Pengelolaan alur tindakan perbaikan unit kendaraan pasca inspeksi yang tidak lulus (temuan NO).
 * Alur Kerja:
 * 1. Admin Departemen menginput tindakan perbaikan & mengunggah foto bukti perbaikan (form / store).
 * 2. Status pengajuan berubah menjadi 'siap_verifikasi'.
 * 3. Inspektor melakukan verifikasi fisik perbaikan (verifikasi / acc_verifikasi).
 * 4. Jika verifikasi fisik disetujui (ACC) -> Status berubah ke 'inspeksi_ulang' (siap diuji ulang checklist).
 * 5. Jika verifikasi fisik ditolak -> Status kembali ke 'tidak_lulus_inspeksi' untuk perbaikan ulang.
 */
class Perbaikan extends CI_Controller
{
    /**
     * Konstruktor Controller Perbaikan
     * Memuat model, library, helper, dan memverifikasi otentikasi login pengguna.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Pengajuan_model' => 'pengajuan_model']);
        $this->load->library('upload');
        $this->load->helper(['url', 'form']);

        if (!$this->session->userdata('id_user')) {
            redirect('auth/login');
        }
    }

    /**
     * Halaman Utama Daftar Pengajuan Perlu Perbaikan Unit.
     * 
     * @return void Render view perbaikan/index
     */
    public function index()
    {
        $roles = $this->_roles();
        if (!$this->_has([1, 7], $roles)) {
            $this->session->set_flashdata('error', 'Akses ditolak. Halaman Daftar Perbaikan Unit hanya untuk Admin Departemen.');
            redirect('pengajuan');
            return;
        }

        $user_dept = trim((string) $this->session->userdata('departemen'));
        $id_user   = (int) $this->session->userdata('id_user');

        if (empty($user_dept) && $id_user > 0) {
            $u = $this->db->select('departemen')->where('id_user', $id_user)->get('users')->row();
            if ($u && !empty($u->departemen)) {
                $user_dept = trim((string) $u->departemen);
                $this->session->set_userdata('departemen', $user_dept);
            }
        }

        $is_site_wide = $this->_has([1, 3, 4, 5, 8], $roles);

        $data = [
            'title'        => 'Daftar Perbaikan Unit',
            'user'         => $this->session->userdata(),
            'user_dept'    => $user_dept,
            'is_site_wide' => $is_site_wide,
            'roles'        => $roles,
            'perusahaan'   => $this->db->where('is_active', 1)->order_by('nama_perusahaan', 'ASC')->get('perusahaan')->result(),
        ];

        $this->load->view('templates/header',  $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('perbaikan/index',   $data);
        $this->load->view('templates/footer',  $data);
    }

    /**
     * Endpoint AJAX DataTables Server-Side untuk Daftar Pengajuan Memerlukan Perbaikan.
     * 
     * @return void Output JSON DataTables
     */
    public function get_data()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $draw   = $this->input->post('draw');
        $start  = max(0, (int) $this->input->post('start'));
        $length = (int) $this->input->post('length');
        if ($length <= 0) $length = 10;
        $length = min($length, 500);

        $status     = trim((string) ($this->input->post('filter_status')     ?? $this->input->post('status')     ?? ''));
        $departemen = trim((string) ($this->input->post('filter_departemen') ?? $this->input->post('departemen') ?? ''));
        $search_post= $this->input->post('search');
        $search_val = is_array($search_post) ? ($search_post['value'] ?? '') : ($search_post ?? '');

        $roles     = $this->_roles();
        if (!$this->_has([1, 7], $roles)) {
            echo json_encode([
                'draw'            => $draw,
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [],
                'csrf_hash'       => $this->security->get_csrf_hash(),
            ]);
            return;
        }

        $id_user   = (int) $this->session->userdata('id_user');
        $user_dept = trim((string) $this->session->userdata('departemen'));

        if (empty($user_dept) && $id_user > 0) {
            $u = $this->db->select('departemen')->where('id_user', $id_user)->get('users')->row();
            if ($u && !empty($u->departemen)) {
                $user_dept = trim((string) $u->departemen);
                $this->session->set_userdata('departemen', $user_dept);
            }
        }

        $filters = [
            'status'     => $status,
            'departemen' => $departemen,
            'search'     => trim((string) $search_val),
            'status_in'  => !empty($status) ? [$status] : ['tidak_lulus_inspeksi', 'siap_verifikasi', 'ditolak_verifikasi'],
        ];

        $is_site_wide = $this->_has([1, 3, 4, 5, 8], $roles);
        if (!$is_site_wide) {
            $filters['scope_dept_pemohon'] = [
                'id_pemohon' => $id_user,
                'departemen' => $user_dept,
            ];
        }

        // Agregasi jumlah count perbaikan untuk ringkasan kartu stat
        $count_filters = $filters;
        unset($count_filters['status'], $count_filters['status_in']);

        $cf_tidak_lulus = $count_filters;
        $cf_tidak_lulus['status'] = 'tidak_lulus_inspeksi';
        $cnt_tidak_lulus = $this->pengajuan_model->count_all($cf_tidak_lulus);

        $cf_siap = $count_filters;
        $cf_siap['status'] = 'siap_verifikasi';
        $cnt_siap = $this->pengajuan_model->count_all($cf_siap);

        $cf_ditolak = $count_filters;
        $cf_ditolak['status'] = 'ditolak_verifikasi';
        $cnt_ditolak = $this->pengajuan_model->count_all($cf_ditolak);

        // Fetch data
        $total_records    = $this->pengajuan_model->count_all($filters);
        $filtered_records = $this->pengajuan_model->count_filtered($filters);
        $data_rows        = $this->pengajuan_model->get_datatable($start, $length, $filters);

        $data = [];
        $no   = $start + 1;

        foreach ($data_rows as $r) {
            $id = $r->id_pengajuan;
            $id_display_html = '<span class="badge bg-light text-dark font-monospace border">#PU-' . str_pad($id, 4, '0', STR_PAD_LEFT) . '</span>';
            $nomor_unit_html = !empty($r->nomor_unit) ? html_escape($r->nomor_unit) : '<span class="text-muted small">—</span>';
            $tgl_html        = '<span class="text-nowrap">' . date('d/m/Y H:i', strtotime($r->tanggal_pengajuan)) . '</span>';

            // Ambil data hasil inspeksi uji_kelayakan
            $uji = $this->db
                ->select('uk.*, u.nama AS nama_mekanik_user')
                ->from('uji_kelayakan uk')
                ->join('users u', 'u.id_user = uk.id_mekanik', 'left')
                ->where('uk.id_pengajuan', $id)
                ->order_by('uk.id_uji', 'DESC')
                ->get()->row();

            $catatan_temuan = '';
            $hasil_inspeksi_badge = '<span class="badge bg-light text-muted border">Belum Diuji</span>';
            $hasil_inspeksi_detail = '';

            if ($uji) {
                if ($uji->hasil === 'lulus') {
                    $hasil_inspeksi_badge = '<span class="badge bg-success text-white px-2 py-1"><i class="bi bi-check-circle me-1"></i>LULUS</span>';
                } elseif ($uji->hasil === 'tidak_lulus') {
                    $hasil_inspeksi_badge = '<span class="badge bg-danger text-white px-2 py-1"><i class="bi bi-x-circle me-1"></i>TIDAK LULUS</span>';
                } else {
                    $hasil_inspeksi_badge = '<span class="badge bg-secondary text-white px-2 py-1">' . html_escape(strtoupper($uji->hasil)) . '</span>';
                }

                $nama_inspektor = $uji->nama_inspektor ?: ($uji->nama_mekanik_user ?: '-');
                $tgl_uji = $uji->tanggal_uji ?: $uji->created_at;

                $count_no = (int) $this->db
                    ->where('id_uji', $uji->id_uji)
                    ->where('hasil', 'no')
                    ->count_all_results('uji_checklist');

                $hasil_inspeksi_detail = '<div>' . $hasil_inspeksi_badge . '</div>';
                $hasil_inspeksi_detail .= '<div class="small text-muted mt-1 text-nowrap"><i class="bi bi-person-badge me-1"></i>' . html_escape($nama_inspektor) . '</div>';
                if ($tgl_uji) {
                    $hasil_inspeksi_detail .= '<div class="small text-muted text-nowrap" style="font-size:11px;"><i class="bi bi-calendar3 me-1"></i>' . date('d/m/Y', strtotime($tgl_uji)) . '</div>';
                }
                if ($count_no > 0) {
                    $hasil_inspeksi_detail .= '<div class="mt-1"><span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle" style="font-size:11px;"><i class="bi bi-exclamation-triangle me-1"></i>' . $count_no . ' Item Temuan NO</span></div>';
                }
                $hasil_inspeksi_detail .= '<div class="mt-1"><a href="' . site_url('checklist/detail/' . $uji->id_uji) . '" target="_blank" class="btn btn-xs btn-outline-info py-0 px-2" style="font-size:11px;" title="Lihat Lembar Checklist"><i class="bi bi-clipboard2-check me-1"></i>Checklist</a></div>';

                $catatan_temuan = $uji->catatan_temuan ?: $uji->catatan_umum;
            } else {
                $hasil_inspeksi_detail = $hasil_inspeksi_badge;
            }

            if (empty($catatan_temuan)) {
                $app = $this->db->select('catatan')
                    ->where('id_pengajuan', $id)
                    ->where("catatan IS NOT NULL AND TRIM(catatan) != ''")
                    ->order_by('id_approval', 'DESC')
                    ->get('pengajuan_approval')->row();
                if ($app) $catatan_temuan = $app->catatan;
            }

            $btn = '<div class="d-flex gap-1 justify-content-center text-nowrap flex-nowrap">';
            $btn .= '<button class="btn btn-sm btn-outline-primary py-0 btn-detail" data-id="' . $id . '" title="Lihat Detail"><i class="bi bi-eye"></i></button>';

            if ($r->status === 'tidak_lulus_inspeksi' && $this->_has([1, 7], $roles)) {
                $btn .= '<a href="' . site_url('perbaikan/form/' . $id) . '" class="btn btn-sm btn-danger py-0 fw-semibold text-white" title="Input Data Perbaikan Unit"><i class="bi bi-tools me-1"></i>Input Perbaikan</a>';
            }

            if ($r->status === 'siap_verifikasi' && $this->_has([1, 4], $roles)) {
                $btn .= '<a href="' . site_url('perbaikan/verifikasi/' . $id) . '" class="btn btn-sm btn-info py-0 text-white fw-semibold" title="Verifikasi Fisik Perbaikan"><i class="bi bi-patch-check me-1"></i>Verifikasi Fisik</a>';
            }

            $btn .= '</div>';

            $data[] = [
                'no'              => $no++,
                'id_display'      => $id_display_html,
                'nomor_unit'      => $nomor_unit_html,
                'no_polisi'       => html_escape($r->no_polisi ?: 'N/A'),
                'jenis_kendaraan' => html_escape($r->jenis_kendaraan ?: '-'),
                'merk_tipe'       => html_escape($r->merk) . ' ' . html_escape($r->tipe),
                'pemohon'         => html_escape($r->nama_pemohon ?: '-'),
                'perusahaan'      => html_escape($r->perusahaan),
                'hasil_inspeksi'  => $hasil_inspeksi_detail,
                'catatan_temuan'  => !empty($catatan_temuan) ? html_escape($catatan_temuan) : '<em class="text-muted small">Tidak ada catatan</em>',
                'status'          => $this->_badge_status_perbaikan($r->status),
                'tgl_pengajuan'   => $tgl_html,
                'aksi'            => $btn,
            ];
        }

        echo json_encode([
            'draw'            => $draw,
            'recordsTotal'    => $total_records,
            'recordsFiltered' => $filtered_records,
            'data'            => $data,
            'counts'          => [
                'tidak_lulus'        => $cnt_tidak_lulus,
                'siap_verifikasi'    => $cnt_siap,
                'ditolak_verifikasi' => $cnt_ditolak,
            ],
            'csrfHash'        => $this->security->get_csrf_hash(),
        ]);
    }

    /**
     * Helper privat penyusun badge status perbaikan.
     * 
     * @param string $status Status pengajuan
     * @return string HTML badge status
     */
    private function _badge_status_perbaikan($status)
    {
        if ($status === 'tidak_lulus_inspeksi') {
            return '<span class="badge bg-danger text-white px-2 py-1"><i class="bi bi-tools me-1"></i>Perlu Perbaikan</span>';
        } elseif ($status === 'siap_verifikasi') {
            return '<span class="badge bg-info text-white px-2 py-1"><i class="bi bi-patch-check me-1"></i>Siap Verifikasi</span>';
        } elseif ($status === 'ditolak_verifikasi') {
            return '<span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-exclamation-triangle me-1"></i>Ditolak Verifikasi</span>';
        }
        return '<span class="badge bg-secondary">' . html_escape($status) . '</span>';
    }

    /**
     * Form Input Data Perbaikan Unit oleh Admin Departemen.
     * 
     * @param int|null $id_pengajuan ID Pengajuan
     * @return void Render view perbaikan/form
     */
    public function form($id_pengajuan = null)
    {
        $id_pengajuan = (int) $id_pengajuan;
        $roles        = $this->_roles();

        if (!$this->_has([1, 7], $roles)) {
            $this->session->set_flashdata('error', 'Akses ditolak.');
            redirect('pengajuan');
        }

        $user_dept = trim((string) $this->session->userdata('departemen'));
        $id_user   = (int) $this->session->userdata('id_user');

        if (empty($user_dept) && $id_user > 0) {
            $u = $this->db->select('departemen')->where('id_user', $id_user)->get('users')->row();
            if ($u && !empty($u->departemen)) {
                $user_dept = trim((string) $u->departemen);
                $this->session->set_userdata('departemen', $user_dept);
            }
        }

        $filters = [];
        if (!in_array(1, $roles, true)) {
            $filters['scope_dept_pemohon'] = [
                'id_pemohon' => $id_user,
                'departemen' => $user_dept,
            ];
        }

        $pengajuan = $this->pengajuan_model->get_detail($id_pengajuan, $filters);
        if (!$pengajuan || $pengajuan->status !== 'tidak_lulus_inspeksi') {
            $this->session->set_flashdata('error', 'Pengajuan tidak ditemukan atau tidak dalam status yang tepat untuk perbaikan.');
            redirect('pengajuan');
        }

        // Ambil hasil inspeksi terakhir
        $uji = $this->db
            ->select('uk.*, u.nama AS nama_inspektor_user')
            ->from('uji_kelayakan uk')
            ->join('users u', 'u.id_user = uk.id_mekanik', 'left')
            ->where('uk.id_pengajuan', $id_pengajuan)
            ->order_by('uk.id_uji', 'DESC')
            ->get()->row();

        // Temuan kriteria yang bernilai NO (tidak lulus)
        $checklist_no = [];
        if ($uji) {
            $checklist_no = $this->db
                ->select('uc.id_item, uc.hasil, uc.keterangan, ci.kriteria, ci.kategori, ci.no_urut')
                ->from('uji_checklist uc')
                ->join('checklist_item ci', 'ci.id_item = uc.id_item')
                ->where('uc.id_uji', $uji->id_uji)
                ->where('uc.hasil', 'no')
                ->order_by('ci.kategori DESC')
                ->order_by('CAST(ci.no_urut AS UNSIGNED)', 'ASC', false)
                ->get()->result();
        }

        // Foto temuan dari inspeksi sebelumnya
        $foto_temuan  = [];
        $foto_mekanik = [];
        if ($uji) {
            $foto_temuan = $this->db
                ->where('id_uji', $uji->id_uji)
                ->where('jenis', 'temuan')
                ->order_by('id_foto', 'ASC')
                ->get('uji_foto')->result();

            $foto_mekanik = $this->db
                ->where('id_uji', $uji->id_uji)
                ->where('jenis', 'mekanik')
                ->order_by('id_foto', 'ASC')
                ->get('uji_foto')->result();
        }

        // Ambil record perbaikan_unit yang ada jika re-entry
        $perbaikan_existing = $this->db
            ->where('id_pengajuan', $id_pengajuan)
            ->order_by('id_perbaikan', 'DESC')
            ->get('perbaikan_unit')->row();

        $tgl_maks    = $perbaikan_existing ? $perbaikan_existing->tgl_max_perbaikan : null;
        $verifikator = null;

        if ($perbaikan_existing && $perbaikan_existing->id_verifikator) {
            $verifikator = $this->db
                ->select('id_user, nama, email')
                ->where('id_user', $perbaikan_existing->id_verifikator)
                ->get('users')->row();
        }

        $data = [
            'title'              => 'Input Data Perbaikan Unit',
            'user'               => $this->session->userdata(),
            'pengajuan'          => $pengajuan,
            'uji'                => $uji,
            'checklist_no'       => $checklist_no,
            'foto_temuan'        => $foto_temuan,
            'foto_mekanik'       => $foto_mekanik,
            'perbaikan_existing' => $perbaikan_existing,
            'tgl_maks'           => $tgl_maks,
            'verifikator'        => $verifikator,
        ];

        $this->load->view('templates/header',  $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('perbaikan/form',    $data);
        $this->load->view('templates/footer',  $data);
    }

    /**
     * Endpoint Simpan (Store) Perbaikan Unit oleh Admin Departemen.
     * Mengunggah file bukti perbaikan per-temuan dan mengubah status pengajuan ke 'siap_verifikasi'.
     * 
     * @return void Redirect ke halaman pengajuan
     */
    public function store()
    {
        $roles = $this->_roles();
        if (!$this->_has([1, 7], $roles)) {
            $this->session->set_flashdata('error', 'Akses ditolak.');
            redirect('pengajuan');
        }

        $id_pengajuan   = (int) $this->input->post('id_pengajuan');
        $id_uji         = (int) $this->input->post('id_uji');
        $catatan_umum   = trim((string)($this->input->post('catatan_perbaikan') ?? ''));
        $tindakan_items = $this->input->post('tindakan_item'); // array [id_item => text]

        $user_dept = trim((string) $this->session->userdata('departemen'));
        $id_user   = (int) $this->session->userdata('id_user');

        if (empty($user_dept) && $id_user > 0) {
            $u = $this->db->select('departemen')->where('id_user', $id_user)->get('users')->row();
            if ($u && !empty($u->departemen)) {
                $user_dept = trim((string) $u->departemen);
                $this->session->set_userdata('departemen', $user_dept);
            }
        }

        $filters = [];
        if (!in_array(1, $roles, true)) {
            $filters['scope_dept_pemohon'] = [
                'id_pemohon' => $id_user,
                'departemen' => $user_dept,
            ];
        }

        $pengajuan = $this->pengajuan_model->get_detail($id_pengajuan, $filters);
        if (!$pengajuan || $pengajuan->status !== 'tidak_lulus_inspeksi') {
            $this->session->set_flashdata('error', 'Status pengajuan tidak valid atau Anda tidak memiliki akses.');
            redirect('pengajuan');
        }

        if (!$id_pengajuan || !$id_uji) {
            $this->session->set_flashdata('error', 'Data tidak lengkap.');
            redirect('perbaikan/form/' . $id_pengajuan);
        }

        // Susun catatan perbaikan dari rincian per-item
        $formatted_notes = [];
        if (is_array($tindakan_items)) {
            foreach ($tindakan_items as $item_id => $tindakan_txt) {
                $item_id = (int) $item_id;
                $tindakan_txt = trim((string)$tindakan_txt);
                if (!empty($tindakan_txt)) {
                    $item_db = $this->db->select('kriteria, no_urut')->where('id_item', $item_id)->get('checklist_item')->row();
                    $label = $item_db ? ($item_db->no_urut ? '#' . $item_db->no_urut . ' ' : '') . $item_db->kriteria : 'Item #' . $item_id;
                    $formatted_notes[] = "• {$label}: {$tindakan_txt}";
                }
            }
        }

        $catatan_final = '';
        if (!empty($formatted_notes)) {
            $catatan_final = implode("\n", $formatted_notes);
            if (!empty($catatan_umum)) {
                $catatan_final .= "\n\nCatatan Tambahan: " . $catatan_umum;
            }
        } else {
            $catatan_final = $catatan_umum;
        }

        $perbaikan_existing = $this->db
            ->where('id_pengajuan', $id_pengajuan)
            ->where('id_uji', $id_uji)
            ->order_by('id_perbaikan', 'DESC')
            ->get('perbaikan_unit')->row();

        $this->db->trans_start();

        if ($perbaikan_existing) {
            $id_perbaikan = $perbaikan_existing->id_perbaikan;
            $this->db->where('id_perbaikan', $id_perbaikan)->update('perbaikan_unit', [
                'catatan_perbaikan' => $catatan_final ?: null,
                'status'            => 'menunggu_verifikasi',
                'tgl_selesai'       => date('Y-m-d'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ]);
        } else {
            $this->db->insert('perbaikan_unit', [
                'id_pengajuan'      => $id_pengajuan,
                'id_uji'            => $id_uji,
                'tgl_max_perbaikan' => date('Y-m-d', strtotime('+7 days')),
                'tgl_selesai'       => date('Y-m-d'),
                'id_verifikator'    => null,
                'catatan_perbaikan' => $catatan_final ?: null,
                'status'            => 'menunggu_verifikasi',
                'created_at'        => date('Y-m-d H:i:s'),
            ]);
            $id_perbaikan = $this->db->insert_id();
        }

        // Siapkan direktori upload
        $path = FCPATH . 'uploads/perbaikan/' . $id_pengajuan . '/';
        if (!is_dir($path)) mkdir($path, 0755, true);

        $upload_errors = [];

        // 1. Upload berkas bukti per-temuan (bukti_item_{id_item})
        if (is_array($tindakan_items)) {
            foreach ($tindakan_items as $item_id => $tindakan_txt) {
                $item_id = (int) $item_id;
                $input_key = 'bukti_item_' . $item_id;

                if (!empty($_FILES[$input_key]['name'])) {
                    $file_names = is_array($_FILES[$input_key]['name']) ? $_FILES[$input_key]['name'] : [$_FILES[$input_key]['name']];
                    foreach ($file_names as $fidx => $fname) {
                        if (empty($fname)) continue;

                        $_FILES['upload_tmp'] = [
                            'name'     => is_array($_FILES[$input_key]['name']) ? $_FILES[$input_key]['name'][$fidx] : $_FILES[$input_key]['name'],
                            'type'     => is_array($_FILES[$input_key]['type']) ? $_FILES[$input_key]['type'][$fidx] : $_FILES[$input_key]['type'],
                            'tmp_name' => is_array($_FILES[$input_key]['tmp_name']) ? $_FILES[$input_key]['tmp_name'][$fidx] : $_FILES[$input_key]['tmp_name'],
                            'error'    => is_array($_FILES[$input_key]['error']) ? $_FILES[$input_key]['error'][$fidx] : $_FILES[$input_key]['error'],
                            'size'     => is_array($_FILES[$input_key]['size']) ? $_FILES[$input_key]['size'][$fidx] : $_FILES[$input_key]['size'],
                        ];

                        $this->upload->initialize([
                            'upload_path'   => $path,
                            'allowed_types' => 'jpg|jpeg|png|pdf|doc|docx',
                            'max_size'      => 10240,
                            'file_name'     => 'bukti_item_' . $item_id . '_' . $fidx . '_' . time(),
                        ]);

                        if ($this->upload->do_upload('upload_tmp')) {
                            $info = $this->upload->data();
                            if (function_exists('strip_image_exif')) {
                                strip_image_exif($info['full_path']);
                            }
                            $this->db->insert('perbaikan_lampiran', [
                                'id_perbaikan' => $id_perbaikan,
                                'id_item'      => $item_id,
                                'file_path'    => 'uploads/perbaikan/' . $id_pengajuan . '/' . $info['file_name'],
                                'keterangan'   => trim((string)$tindakan_txt) ?: null,
                                'jenis'        => 'bukti_perbaikan',
                                'uploaded_at'  => date('Y-m-d H:i:s'),
                            ]);
                        } else {
                            $upload_errors[] = $this->upload->display_errors('', '');
                        }
                    }
                }
            }
        }

        // 2. Upload berkas bukti umum tambahan jika ada
        if (!empty($_FILES['bukti_perbaikan']['name'][0])) {
            $count = 0;
            foreach ($_FILES['bukti_perbaikan']['name'] as $idx => $fname) {
                if ($count >= 10 || empty($fname)) continue;

                $_FILES['upload_tmp'] = [
                    'name'     => $_FILES['bukti_perbaikan']['name'][$idx],
                    'type'     => $_FILES['bukti_perbaikan']['type'][$idx],
                    'tmp_name' => $_FILES['bukti_perbaikan']['tmp_name'][$idx],
                    'error'    => $_FILES['bukti_perbaikan']['error'][$idx],
                    'size'     => $_FILES['bukti_perbaikan']['size'][$idx],
                ];

                $this->upload->initialize([
                    'upload_path'   => $path,
                    'allowed_types' => 'jpg|jpeg|png|pdf|doc|docx',
                    'max_size'      => 10240,
                    'file_name'     => 'bukti_umum_' . $idx . '_' . time(),
                ]);

                if ($this->upload->do_upload('upload_tmp')) {
                    $info = $this->upload->data();
                    if (function_exists('strip_image_exif')) {
                        strip_image_exif($info['full_path']);
                    }
                    $this->db->insert('perbaikan_lampiran', [
                        'id_perbaikan' => $id_perbaikan,
                        'id_item'      => null,
                        'file_path'    => 'uploads/perbaikan/' . $id_pengajuan . '/' . $info['file_name'],
                        'keterangan'   => 'Lampiran Umum',
                        'jenis'        => 'bukti_perbaikan',
                        'uploaded_at'  => date('Y-m-d H:i:s'),
                    ]);
                    $count++;
                } else {
                    $upload_errors[] = $this->upload->display_errors('', '');
                }
            }
        }

        // Ubah status pengajuan ke siap_verifikasi
        $this->db->where('id_pengajuan', $id_pengajuan)
            ->update('pengajuan_uji', ['status' => 'siap_verifikasi']);

        // Log approval & audit
        $this->db->insert('pengajuan_approval', [
            'id_pengajuan'   => $id_pengajuan,
            'id_approver'    => $this->session->userdata('id_user'),
            'level_approval' => 'perbaikan_unit',
            'status'         => 'approved',
            'catatan'        => 'Perbaikan selesai dilakukan. Menunggu verifikasi fisik oleh inspektor.'
                . ($catatan_final ? "\n\n" . $catatan_final : ''),
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        $this->db->insert('audit_log', [
            'id_user'    => $this->session->userdata('id_user'),
            'aksi'       => 'input_perbaikan',
            'tabel'      => 'perbaikan_unit',
            'id_ref'     => $id_perbaikan,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->db->trans_complete();

        if (!$this->db->trans_status()) {
            $this->session->set_flashdata('error', 'Gagal menyimpan data perbaikan. Silakan coba lagi.');
            redirect('perbaikan/form/' . $id_pengajuan);
        }

        $no  = '#PU-' . str_pad($id_pengajuan, 4, '0', STR_PAD_LEFT);
        $msg = 'Data perbaikan <strong>' . $no . '</strong> berhasil disimpan. '
            . 'Pengajuan menunggu <strong>verifikasi fisik oleh Inspektor</strong>.';

        if (!empty($upload_errors)) {
            $msg .= '<br><small class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>'
                . 'Beberapa file gagal diupload.</small>';
        }

        $this->session->set_flashdata('success', $msg);
        redirect('pengajuan');
    }

    /**
     * Halaman Verifikasi Fisik Hasil Perbaikan oleh Inspektor (Role ID = 4).
     * 
     * @param int|null $id_pengajuan ID Pengajuan
     * @return void Render view perbaikan/verifikasi
     */
    public function verifikasi($id_pengajuan = null)
    {
        $id_pengajuan = (int) $id_pengajuan;
        $roles        = $this->_roles();

        if (!$this->_has([1, 4], $roles)) {
            $this->session->set_flashdata('error', 'Hanya Inspektor yang dapat melakukan verifikasi perbaikan.');
            redirect('inspeksi');
        }

        $pengajuan = $this->pengajuan_model->get_detail($id_pengajuan);
        if (!$pengajuan || $pengajuan->status !== 'siap_verifikasi') {
            $this->session->set_flashdata('error', 'Pengajuan tidak ditemukan atau tidak dalam status Siap Verifikasi.');
            redirect('inspeksi');
        }

        $uji = $this->db
            ->select('uk.*, u.nama AS nama_inspektor_user')
            ->from('uji_kelayakan uk')
            ->join('users u', 'u.id_user = uk.id_mekanik', 'left')
            ->where('uk.id_pengajuan', $id_pengajuan)
            ->order_by('uk.id_uji', 'DESC')
            ->get()->row();

        $checklist_no = [];
        if ($uji) {
            $checklist_no = $this->db
                ->select('uc.id_item, uc.hasil, uc.keterangan, ci.kriteria, ci.kategori, ci.no_urut')
                ->from('uji_checklist uc')
                ->join('checklist_item ci', 'ci.id_item = uc.id_item')
                ->where('uc.id_uji', $uji->id_uji)
                ->where('uc.hasil', 'no')
                ->order_by('ci.kategori DESC')
                ->order_by('CAST(ci.no_urut AS UNSIGNED)', 'ASC', false)
                ->get()->result();
        }

        $perbaikan = $this->db
            ->select('pu.*, u.nama AS nama_verifikator')
            ->from('perbaikan_unit pu')
            ->join('users u', 'u.id_user = pu.id_verifikator', 'left')
            ->where('pu.id_pengajuan', $id_pengajuan)
            ->order_by('pu.id_perbaikan', 'DESC')
            ->get()->row();

        $lampiran_perbaikan = [];
        if ($perbaikan) {
            $lampiran_perbaikan = $this->db
                ->select('pl.*, ci.kriteria AS nama_item, ci.kategori AS kategori_item, ci.no_urut AS no_urut_item')
                ->from('perbaikan_lampiran pl')
                ->join('checklist_item ci', 'ci.id_item = pl.id_item', 'left')
                ->where('pl.id_perbaikan', $perbaikan->id_perbaikan)
                ->get()->result();
        }

        $data = [
            'title'              => 'Verifikasi Fisik Perbaikan — ' . $pengajuan->no_polisi,
            'user'               => $this->session->userdata(),
            'pengajuan'          => $pengajuan,
            'uji'                => $uji,
            'checklist_no'       => $checklist_no,
            'perbaikan'          => $perbaikan,
            'lampiran_perbaikan' => $lampiran_perbaikan,
        ];

        $this->load->view('templates/header',     $data);
        $this->load->view('templates/sidebar',    $data);
        $this->load->view('perbaikan/verifikasi', $data);
        $this->load->view('templates/footer',     $data);
    }

    /**
     * Endpoint AJAX ACC / Tolak Verifikasi Fisik oleh Inspektor.
     * ACC   -> status berubah ke 'inspeksi_ulang' (siap diisi checklist ulang).
     * Tolak -> status berubah ke 'tidak_lulus_inspeksi' (kembali ke perbaikan).
     * 
     * @return void Output JSON status verifikasi & URL redirect
     */
    public function acc_verifikasi()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $roles = $this->_roles();
        if (!$this->_has([1, 4], $roles)) {
            echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
            return;
        }

        $id_pengajuan = (int) $this->input->post('id_pengajuan');
        $aksi         = $this->input->post('aksi');
        $catatan      = trim($this->input->post('catatan') ?? '');
        $id_inspektor = (int) $this->session->userdata('id_user');

        if (!in_array($aksi, ['acc', 'tolak'], true)) {
            echo json_encode(['status' => 'error', 'message' => 'Aksi tidak valid.']);
            return;
        }
        if ($aksi === 'tolak' && empty($catatan)) {
            echo json_encode(['status' => 'error', 'message' => 'Catatan alasan penolakan verifikasi wajib diisi.']);
            return;
        }

        $pengajuan = $this->pengajuan_model->get_detail($id_pengajuan);
        if (!$pengajuan || $pengajuan->status !== 'siap_verifikasi') {
            echo json_encode(['status' => 'error', 'message' => 'Status pengajuan tidak sesuai.']);
            return;
        }

        $perbaikan = $this->db
            ->where('id_pengajuan', $id_pengajuan)
            ->order_by('id_perbaikan', 'DESC')
            ->get('perbaikan_unit')->row();

        $this->db->trans_start();

        if ($aksi === 'acc') {
            $new_status       = 'inspeksi_ulang';
            $perbaikan_status = 'diverifikasi';
            $catatan_log      = 'Verifikasi fisik DITERIMA oleh inspektor. Unit siap diuji ulang.'
                . ($catatan ? ' Catatan: ' . $catatan : '');
            $level_log        = 'verifikasi_perbaikan_acc';
        } else {
            $new_status       = 'tidak_lulus_inspeksi';
            $perbaikan_status = 'ditolak_verifikasi';
            $catatan_log      = 'Verifikasi fisik DITOLAK. Perbaikan belum sesuai. ' . $catatan;
            $level_log        = 'verifikasi_perbaikan_tolak';
        }

        if ($perbaikan) {
            $this->db->where('id_perbaikan', $perbaikan->id_perbaikan)->update('perbaikan_unit', [
                'status'         => $perbaikan_status,
                'id_verifikator' => $id_inspektor,
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);
        }

        $this->db->where('id_pengajuan', $id_pengajuan)
            ->update('pengajuan_uji', ['status' => $new_status]);

        $this->db->insert('pengajuan_approval', [
            'id_pengajuan'   => $id_pengajuan,
            'id_approver'    => $id_inspektor,
            'level_approval' => $level_log,
            'status'         => $aksi === 'acc' ? 'approved' : 'rejected',
            'catatan'        => $catatan_log,
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        $this->db->insert('audit_log', [
            'id_user'    => $id_inspektor,
            'aksi'       => $aksi === 'acc' ? 'verif_perbaikan_acc' : 'verif_perbaikan_tolak',
            'tabel'      => 'perbaikan_unit',
            'id_ref'     => $perbaikan ? $perbaikan->id_perbaikan : $id_pengajuan,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->db->trans_complete();

        if (!$this->db->trans_status()) {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memproses verifikasi.']);
            return;
        }

        // Kirim notifikasi email ke Pemohon
        if (file_exists(APPPATH . 'libraries/Sikuk_email.php')) {
            try {
                $this->load->library('sikuk_email');
                if ($aksi === 'acc') {
                    $this->sikuk_email->notif_verifikasi_perbaikan_acc($id_pengajuan, $catatan);
                } else {
                    $this->sikuk_email->notif_verifikasi_perbaikan_tolak($id_pengajuan, $catatan);
                }
            } catch (Throwable $e) {
                log_message('error', '[Perbaikan acc_verifikasi Email] Exception: ' . $e->getMessage());
            }
        }

        $no = '#PU-' . str_pad($id_pengajuan, 4, '0', STR_PAD_LEFT);

        if ($aksi === 'acc') {
            echo json_encode([
                'status'   => 'success',
                'message'  => 'Verifikasi fisik <strong>' . $no . '</strong> diterima. Unit berstatus <strong>Siap Pengujian Ulang</strong>.',
                'redirect' => site_url('checklist/form/' . $id_pengajuan),
            ]);
        } else {
            echo json_encode([
                'status'   => 'success',
                'message'  => 'Verifikasi fisik <strong>' . $no . '</strong> ditolak. Admin Departemen diminta melakukan perbaikan ulang.',
                'redirect' => site_url('inspeksi'),
            ]);
        }
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
     * Helper privat mengecek kecocokan hak akses role.
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
