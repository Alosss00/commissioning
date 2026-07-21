<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pengajuan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Pengajuan_model' => 'pengajuan_model']);
        $this->load->model(['Kendaraan_model' => 'kendaraan_model']);
        $this->load->library(['session', 'form_validation', 'upload']);
        $this->load->helper(['url', 'form']);
        if (!$this->session->userdata('id_user')) redirect('auth/login');
    }

    public function index()
    {
        $data['title'] = 'Daftar Pengajuan';
        $data['user']  = $this->session->userdata();
        $this->load->view('templates/header',  $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('pengajuan/index',   $data);
        $this->load->view('templates/footer',  $data);
    }

    // Hanya Admin Departemen (7) & Super Admin (1)
    public function create()
    {
        $roles = $this->_user_roles();
        if (!$this->_has_role([1, 7], $roles)) {
            $this->session->set_flashdata('error', 'Hanya Admin Departemen yang dapat membuat pengajuan.');
            redirect('pengajuan');
        }
        $data = [
            'title'              => 'Buat Pengajuan Uji Kelayakan',
            'user'               => $this->session->userdata(),
            // Untuk recommissioning: hanya kendaraan yang lulus + stiker expired
            'kendaraan'          => $this->kendaraan_model->get_kendaraan_lulus_eligible(),
            'tipe_kendaraan'     => $this->db->where('is_active', 1)->order_by('nama_tipe', 'ASC')->get('tipe_kendaraan')->result(),
            'perusahaan'         => $this->db->where('is_active', 1)->order_by('nama_perusahaan', 'ASC')->get('perusahaan')->result(),
        ];
        $this->load->view('templates/header',  $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('pengajuan/create',  $data);
        $this->load->view('templates/footer',  $data);
    }

    // STORE — mode_unit: baru=pengajuan_baru, lama=pengajuan_ulang
    public function store()
    {
        if (!$this->input->is_ajax_request()) show_404();
        
        $roles   = $this->_user_roles();
        $id_user = (int) $this->session->userdata('id_user');

        // Helper function untuk return JSON + csrfHash
        $response = function($status, $message, $data = []) {
            $output = [
                'status'    => $status,
                'message'   => $message,
                'csrfHash'  => $this->security->get_csrf_hash(),  // ✅ WAJIB: setiap response
            ];
            return array_merge($output, $data);
        };

        if (!$this->_has_role([1, 7], $roles)) {
            echo json_encode($response('error', 'Akses ditolak.'));
            return;
        }

        $mode_unit = $this->input->post('mode_unit');
        
        // ════════════════════════════════════════════════════════
        // SERVER-SIDE VALIDATION
        // ════════════════════════════════════════════════════════
        $this->form_validation->set_rules('tipe_pengajuan', 'Tipe Pengajuan', 
            'required|in_list[new_commissioning,recommissioning]');
        $this->form_validation->set_rules('tipe_akses', 'Tipe Akses', 
            'required|in_list[mining,non_mining,underground]');
        $this->form_validation->set_rules('tujuan', 'Tujuan Penggunaan', 
            'required|max_length[1000]');
        $this->form_validation->set_rules('email_pemohon', 'Email Pemohon', 
            'required|valid_email|max_length[100]');

        if (!$this->form_validation->run()) {
            $errors = str_replace(['<p>', '</p>'], ['<li>', '</li>'], validation_errors());
            echo json_encode($response('error', '<strong>Validasi Gagal:</strong><ul>' . $errors . '</ul>'));
            return;
        }

        $id_kendaraan = 0;
        $is_unit_baru = false;

        // ════════════════════════════════════════════════════════
        // MODE: UNIT BARU (New Commissioning)
        // ════════════════════════════════════════════════════════
        if ($mode_unit === 'baru') {
            // Ambil flag N/A dari form
            $is_na_no_polisi    = $this->input->post('is_na_no_polisi') == '1';
            $is_na_nomor_mesin  = $this->input->post('is_na_nomor_mesin') == '1';
            $is_na_nomor_unit   = $this->input->post('is_na_nomor_unit') == '1';
            $is_na_model_unit   = $this->input->post('is_na_model_unit') == '1';
            $is_na_stnk         = $this->input->post('is_na_stnk') == '1';

            // Validasi field WAJIB (tidak bisa N/A)
            $jenis_kendaraan = $this->input->post('jenis_kendaraan');
            $merk            = trim($this->input->post('merk') ?? '');
            $nomor_rangka    = trim($this->input->post('nomor_rangka') ?? '');
            $perusahaan      = $this->input->post('perusahaan');
            $tahun           = $this->input->post('tahun');

            if (empty($jenis_kendaraan)) {
                echo json_encode($response('error', 'Tipe Unit <strong>wajib dipilih</strong>.'));
                return;
            }
            if (empty($merk)) {
                echo json_encode($response('error', 'Merk Unit <strong>wajib diisi</strong>.'));
                return;
            }
            if (empty($nomor_rangka)) {
                echo json_encode($response('error', 'Nomor Rangka <strong>wajib diisi</strong>.'));
                return;
            }
            if (empty($perusahaan)) {
                echo json_encode($response('error', 'Perusahaan <strong>wajib dipilih</strong>.'));
                return;
            }
            if (empty($tahun)) {
                echo json_encode($response('error', 'Tahun <strong>wajib diisi</strong>.'));
                return;
            }

            // Nomor Polisi — wajib KECUALI N/A
            $no_polisi = $is_na_no_polisi ? 'N/A' : strtoupper(trim($this->input->post('no_polisi') ?? ''));
            if (!$is_na_no_polisi && empty($no_polisi)) {
                echo json_encode($response('error', 'Nomor Polisi <strong>wajib diisi</strong> atau centang N/A.'));
                return;
            }

            // Cek duplikat no_polisi (hanya jika bukan N/A)
            if (!$is_na_no_polisi) {
                $existing = $this->db->where('no_polisi', $no_polisi)
                                      ->where('is_na_no_polisi', 0)
                                      ->get('kendaraan')->row();
                if ($existing) {
                    echo json_encode($response('error', 
                        'Nomor Polisi <strong>' . html_escape($no_polisi) . '</strong> sudah terdaftar. '
                        . 'Gunakan mode <strong>Pengajuan Kembali (Recommissioning)</strong> untuk unit yang sudah ada.'));
                    return;
                }
            }

            // Nomor Mesin — wajib KECUALI N/A
            $nomor_mesin = $is_na_nomor_mesin ? 'N/A' : trim($this->input->post('nomor_mesin') ?? '');
            if (!$is_na_nomor_mesin && empty($nomor_mesin)) {
                echo json_encode($response('error', 'Nomor Mesin <strong>wajib diisi</strong> atau centang N/A.'));
                return;
            }

            // ── Validasi lampiran: STNK + Foto 4 sisi ──
            $foto_required = [
                'stnk'          => ['field' => 'lampiran_stnk', 'label' => 'Foto STNK', 'na_key' => 'is_na_stnk'],
                'unit_depan'    => ['field' => 'lampiran_unit_depan', 'label' => 'Foto Depan', 'na_key' => 'is_na_foto_unit_depan'],
                'unit_belakang' => ['field' => 'lampiran_unit_belakang', 'label' => 'Foto Belakang', 'na_key' => 'is_na_foto_unit_belakang'],
                'unit_kiri'     => ['field' => 'lampiran_unit_kiri', 'label' => 'Foto Kiri', 'na_key' => 'is_na_foto_unit_kiri'],
                'unit_kanan'    => ['field' => 'lampiran_unit_kanan', 'label' => 'Foto Kanan', 'na_key' => 'is_na_foto_unit_kanan'],
            ];

            foreach ($foto_required as $key => $config) {
                $is_na = $this->input->post($config['na_key']) == '1';
                if (!$is_na && empty($_FILES[$config['field']]['name'])) {
                    echo json_encode($response('error', 
                        '<strong>' . $config['label'] . '</strong> wajib diupload atau centang N/A.'));
                    return;
                }
            }

            // ── Insert kendaraan baru ──
            $kendaraan_data = [
                'no_polisi'         => $no_polisi,
                'is_na_no_polisi'   => $is_na_no_polisi ? 1 : 0,
                'id_tipe_kendaraan' => (int) $jenis_kendaraan,
                'nomor_unit'        => $is_na_nomor_unit ? 'N/A' : trim($this->input->post('nomor_unit') ?? ''),
                'is_na_nomor_unit'  => $is_na_nomor_unit ? 1 : 0,
                'merk'              => $merk,
                'tipe'              => $is_na_model_unit ? 'N/A' : trim($this->input->post('model_unit') ?? ''),
                'model_unit'        => $is_na_model_unit ? 'N/A' : trim($this->input->post('model_unit') ?? ''),
                'is_na_model_unit'  => $is_na_model_unit ? 1 : 0,
                'perusahaan'        => $perusahaan,
                'tahun'             => (int) $tahun,
                'is_unit_baru'      => 1,
                'created_at'        => date('Y-m-d H:i:s'),
            ];

            $id_kendaraan = $this->kendaraan_model->insert($kendaraan_data);
            if (!$id_kendaraan) {
                echo json_encode($response('error', 'Gagal mendaftarkan kendaraan baru.'));
                return;
            }
            $is_unit_baru = true;

        // ════════════════════════════════════════════════════════
        // MODE: UNIT LAMA (Recommissioning)
        // ════════════════════════════════════════════════════════
        } elseif ($mode_unit === 'lama') {
            $id_kendaraan = (int) $this->input->post('id_kendaraan');
            if (!$id_kendaraan || !$this->kendaraan_model->get_by_id($id_kendaraan)) {
                echo json_encode($response('error', 'Kendaraan tidak ditemukan atau tidak valid.'));
                return;
            }

            // Catatan: nomor_rangka & nomor_mesin disimpan di tabel pengajuan_uji, bukan kendaraan
        } else {
            echo json_encode($response('error', 'Mode unit tidak valid.'));
            return;
        }

        // ════════════════════════════════════════════════════════
        // VALIDASI: Maintenance Record
        // ════════════════════════════════════════════════════════
        $pernah_maintenance_luar = ($this->input->post('pernah_maintenance_luar') == '1') ? 1 : 0;
        if ($pernah_maintenance_luar && empty($_FILES['lampiran_maintenance_record']['name'])) {
            if ($is_unit_baru) {
                $this->db->where('id_kendaraan', $id_kendaraan)->delete('kendaraan');
            }
            echo json_encode($response('error', 
                '<strong>Maintenance Record</strong> wajib diupload karena unit pernah '
                . 'maintenance di luar perusahaan.'));
            return;
        }

        // ════════════════════════════════════════════════════════
        // INSERT PENGAJUAN
        // ════════════════════════════════════════════════════════
        $id_pengajuan = $this->pengajuan_model->insert_pengajuan([
            'id_kendaraan'            => $id_kendaraan,
            'id_pemohon'              => $id_user,
            'email_pemohon'           => $this->input->post('email_pemohon'),
            'tipe_pengajuan'          => $this->input->post('tipe_pengajuan'),
            'tipe_akses'              => $this->input->post('tipe_akses'),
            'tujuan'                  => $this->input->post('tujuan'),
            'pernah_maintenance_luar' => $pernah_maintenance_luar,
            'nomor_rangka'            => $mode_unit === 'baru' ? trim($this->input->post('nomor_rangka') ?? '') : trim($this->input->post('nomor_rangka') ?? ''),
            'nomor_mesin'             => $mode_unit === 'baru' ? ($nomor_mesin ?? '') : trim($this->input->post('nomor_mesin') ?? ''),
            'status'                  => ($mode_unit === 'baru') ? 'pengajuan_baru' : 'pengajuan_ulang',
            'tanggal_pengajuan'       => date('Y-m-d H:i:s'),
        ]);

        if (!$id_pengajuan) {
            if ($is_unit_baru) {
                $this->db->where('id_kendaraan', $id_kendaraan)->delete('kendaraan');
            }
            echo json_encode($response('error', 'Gagal menyimpan pengajuan ke database.'));
            return;
        }

        // ════════════════════════════════════════════════════════
        // UPLOAD LAMPIRAN — untuk Unit Baru
        // ════════════════════════════════════════════════════════
        if ($is_unit_baru) {
            $upload_errors = $this->_upload_lampiran($id_pengajuan);
            if (!empty($upload_errors)) {
                // Rollback: hapus pengajuan & kendaraan
                $this->pengajuan_model->delete_pengajuan($id_pengajuan);
                $this->db->where('id_kendaraan', $id_kendaraan)->delete('kendaraan');
                
                $error_html = '<ul><li>' . implode('</li><li>', $upload_errors) . '</li></ul>';
                echo json_encode($response('error', 'Gagal upload lampiran:<br>' . $error_html));
                return;
            }
        }

        // ════════════════════════════════════════════════════════
        // UPLOAD MAINTENANCE RECORD (jika ada)
        // ════════════════════════════════════════════════════════
        if (!empty($_FILES['lampiran_maintenance_record']['name'])) {
            $maintenance_error = $this->_upload_single_lampiran(
                $id_pengajuan, 
                'maintenance_record', 
                'lampiran_maintenance_record'
            );
            if ($maintenance_error) {
                // Warning saja, tidak rollback — pengajuan tetap tersimpan
                log_message('error', 'Upload Maintenance Record failed for pengajuan #' . $id_pengajuan . ': ' . $maintenance_error);
            }
        }

        // ════════════════════════════════════════════════════════
        // CREATE APPROVAL WORKFLOW
        // ════════════════════════════════════════════════════════
        $this->pengajuan_model->insert_approval([
            'id_pengajuan'   => $id_pengajuan,
            'id_approver'    => 0,
            'level_approval' => 'dept_manager',
            'status'         => 'pending',
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        // ════════════════════════════════════════════════════════
        // AUDIT LOG
        // ════════════════════════════════════════════════════════
        $this->_audit('buat_pengajuan', 'pengajuan_uji', $id_pengajuan);

        // ════════════════════════════════════════════════════════
        // SUCCESS RESPONSE ✅ dengan csrfHash
        // ════════════════════════════════════════════════════════
        $no_pengajuan = '#PU-' . str_pad($id_pengajuan, 4, '0', STR_PAD_LEFT);
        $message = 'Pengajuan <strong>' . $no_pengajuan . '</strong> berhasil disubmit. '
                 . 'Menunggu review dari <strong>Manager Departemen</strong>.';

        echo json_encode($response('success', $message, [
            'redirect' => site_url('pengajuan'),
            'id_pengajuan' => $id_pengajuan,
        ]));
    }

    public function get_data()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $filters = [
            'status'      => $this->input->post('filter_status'),
            'jenis'       => $this->input->post('filter_jenis'),
            'tgl_dari'    => $this->input->post('filter_tgl_dari'),
            'tgl_sampai'  => $this->input->post('filter_tgl_sampai'),
            'search'      => $this->input->post('search')['value'] ?? '',
        ];

        // Admin Dept hanya lihat pengajuan miliknya
        $roles = $this->_user_roles();
        if (in_array(7, $roles) && !in_array(1, $roles)) {
            $filters['id_pemohon'] = (int) $this->session->userdata('id_user');
        }

        $draw   = $this->input->post('draw');
        $start  = $this->input->post('start');
        $length = $this->input->post('length');

        $total    = $this->pengajuan_model->count_all($filters);
        $filtered = $this->pengajuan_model->count_filtered($filters);
        $rows     = $this->pengajuan_model->get_datatable($start, $length, $filters);

        $data_rows = [];
        $no = $start + 1;
        foreach ($rows as $row) {
            $data_rows[] = [
                'no'              => $no++,
                'id_display'      => '<span class="fw-bold text-primary">#PU-' . str_pad($row->id_pengajuan, 4, '0', STR_PAD_LEFT) . '</span>',
                'pemohon'         => html_escape($row->nama_pemohon),
                'no_polisi'       => '<span class="badge bg-secondary font-monospace">' . html_escape($row->no_polisi) . '</span>',
                'jenis_kendaraan' => html_escape($row->jenis_kendaraan) . '<br><small class="text-muted">' . html_escape($row->merk) . ' ' . html_escape($row->tipe) . '</small>',
                'unit_baru'       => $row->is_unit_baru ? '<span class="badge bg-warning text-dark">Unit Baru</span>' : '<span class="badge bg-secondary">Unit Lama</span>',
                'status'          => $this->_badge_status($row->status),
                'tgl_pengajuan'   => date('d M Y', strtotime($row->tanggal_pengajuan)) . '<br><small class="text-muted">' . date('H:i', strtotime($row->tanggal_pengajuan)) . '</small>',
                'aksi'            => $this->_tombol_aksi($row),
            ];
        }

        echo json_encode([
            'draw'            => (int)$draw,
            'recordsTotal'    => $total,
            'recordsFiltered' => $filtered,
            'data'            => $data_rows,
            'csrfHash'        => $this->security->get_csrf_hash(),
            'csrf_hash'       => $this->security->get_csrf_hash(),
        ]);
    }

    public function detail($id = null)
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id   = (int) $id;
        $roles = $this->_user_roles();
        $filters = [];
        $departemen = $this->session->userdata('departemen');
        if (!in_array(1, $roles) && !empty($departemen)) {
            $filters['departemen'] = $departemen;
            if (in_array(7, $roles)) {
                $filters['id_pemohon'] = (int) $this->session->userdata('id_user');
            }
        }

        $data = $this->pengajuan_model->get_detail($id, $filters);

        if (!$data) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Data tidak ditemukan.'
            ]);
            return;
        }

        $data->lampiran = $this->pengajuan_model->get_lampiran($id);
        $data->approval = $this->pengajuan_model->get_approval($id);
        $data->jadwal   = $this->pengajuan_model->get_jadwal($id);
        $data->uji      = $this->pengajuan_model->get_uji($id);


        $perbaikan_rows = $this->db
            ->select('pu.*, u.nama AS nama_verifikator')
            ->from('perbaikan_unit pu')
            ->join('users u', 'u.id_user = pu.id_verifikator', 'left')
            ->where('pu.id_pengajuan', $id)
            ->order_by('pu.id_perbaikan', 'ASC')
            ->get()
            ->result();

        foreach ($perbaikan_rows as $pb) {
            $pb->lampiran = $this->db
                ->where('id_perbaikan', $pb->id_perbaikan)
                ->get('perbaikan_lampiran')
                ->result();
        }

        $data->perbaikan = $perbaikan_rows;

        echo json_encode([
            'status' => 'success',
            'data'   => $data
        ]);
    }

    public function get_kendaraan_info()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $id  = (int) $this->input->post('id_kendaraan');
        $row = $this->kendaraan_model->get_by_id($id);
        echo $row ? json_encode(['status' => 'success', 'data' => $row]) : json_encode(['status' => 'error']);
    }

    // ============================================================
    // EDIT — tampilkan form edit pengajuan yang ditolak_manager
    // ============================================================
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
            $this->session->set_flashdata('error', 'Pengajuan tidak ditemukan.');
            redirect('pengajuan');
        }

        // Hanya bisa edit jika ditolak_manager atau draft, dan milik pemohon sendiri
        if (!in_array($pengajuan->status, ['ditolak_manager', 'draft'])) {
            $this->session->set_flashdata('error', 'Pengajuan hanya dapat diedit dalam status Draft atau Ditolak Manager.');
            redirect('pengajuan');
        }
        if (!in_array(1, $roles) && $pengajuan->id_pemohon != $id_user) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses ke pengajuan ini.');
            redirect('pengajuan');
        }

        $lampiran   = $this->pengajuan_model->get_lampiran($id_pengajuan);
        $kendaraan  = $this->kendaraan_model->get_by_id($pengajuan->id_kendaraan);
        $riwayat    = $this->pengajuan_model->get_approval($id_pengajuan);

        // Ambil catatan penolakan terakhir dari manager
        $catatan_tolak = '';
        foreach (array_reverse($riwayat) as $r) {
            if ($r->level_approval === 'dept_manager' && $r->status === 'rejected') {
                $catatan_tolak = $r->catatan ?? '';
                break;
            }
        }

        $data = [
            'title'        => 'Edit Pengajuan #PU-' . str_pad($id_pengajuan, 4, '0', STR_PAD_LEFT),
            'user'         => $this->session->userdata(),
            'pengajuan'    => $pengajuan,
            'kendaraan'    => $kendaraan,
            'lampiran'     => $lampiran,
            'catatan_tolak' => $catatan_tolak,
            'tipe_kendaraan' => $this->db->where('is_active', 1)->order_by('nama_tipe', 'ASC')->get('tipe_kendaraan')->result(),
            'perusahaan'   => $this->db->where('is_active', 1)->order_by('nama_perusahaan', 'ASC')->get('perusahaan')->result(),
        ];

        $this->load->view('templates/header',  $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('pengajuan/edit',    $data);
        $this->load->view('templates/footer',  $data);
    }

    // ============================================================
    // UPDATE — simpan perubahan pengajuan + ubah status ke pengajuan_ulang
    // ============================================================
    public function update()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $roles   = $this->_user_roles();
        $id_user = (int) $this->session->userdata('id_user');

        if (!$this->_has_role([1, 7], $roles)) {
            echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
            return;
        }

        $id_pengajuan = (int) $this->input->post('id_pengajuan');
        $pengajuan    = $this->pengajuan_model->get_detail($id_pengajuan);

        if (!$pengajuan || !in_array($pengajuan->status, ['ditolak_manager', 'draft'])) {
            echo json_encode(['status' => 'error', 'message' => 'Pengajuan tidak dapat diedit saat ini.']);
            return;
        }
        if (!in_array(1, $roles) && $pengajuan->id_pemohon != $id_user) {
            echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
            return;
        }

        $tujuan      = trim($this->input->post('tujuan') ?? '');
        $email       = trim($this->input->post('email_pemohon') ?? '');
        $alasan_edit = trim($this->input->post('alasan_edit') ?? '');

        if (empty($tujuan) || empty($email)) {
            echo json_encode(['status' => 'error', 'message' => 'Tujuan dan email pemohon wajib diisi.']);
            return;
        }
        if (empty($alasan_edit)) {
            echo json_encode(['status' => 'error', 'message' => 'Jelaskan tindakan perbaikan / alasan pengajuan ulang.']);
            return;
        }

        $this->db->trans_start();

        // Update data pengajuan
        $this->db->where('id_pengajuan', $id_pengajuan)->update('pengajuan_uji', [
            'tujuan'                  => $tujuan,
            'email_pemohon'           => $email,
            'tipe_akses'              => $this->input->post('tipe_akses') ?: $pengajuan->tipe_akses,
            'status'                  => 'pengajuan_ulang',
            'tanggal_pengajuan'       => date('Y-m-d H:i:s'),
            'alasan_pengajuan_ulang'  => $alasan_edit,
        ]);

        // ── Replace lampiran per jenis ──────────────────────────────────────
        // Hanya jenis yang dikirim user (ada di $_FILES) yang diupdate
        $jenis_list = ['stnk', 'unit_depan', 'unit_belakang', 'unit_kiri', 'unit_kanan', 'maintenance_record'];
        $upload_errors = [];

        foreach ($jenis_list as $jenis) {
            $field = 'lampiran_' . $jenis;
            if (empty($_FILES[$field]['name'])) continue; // tidak dikirim, skip

            $err = $this->_upload_replace_lampiran($id_pengajuan, $jenis, $field);
            if ($err) {
                $upload_errors[] = $jenis . ': ' . $err;
            }
        }

        // Catat di approval log
        $this->db->insert('pengajuan_approval', [
            'id_pengajuan'   => $id_pengajuan,
            'id_approver'    => $id_user,
            'level_approval' => 'edit_admin_dept',
            'status'         => 'approved',
            'catatan'        => 'Diedit dan diajukan ulang: ' . $alasan_edit,
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        $this->_audit('edit_pengajuan', 'pengajuan_uji', $id_pengajuan);
        $this->db->trans_complete();

        if (!$this->db->trans_status()) {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan perubahan.']);
            return;
        }

        $no  = '#PU-' . str_pad($id_pengajuan, 4, '0', STR_PAD_LEFT);
        $msg = 'Pengajuan <strong>' . $no . '</strong> telah diperbaiki dan dikirim ulang ke '
            . '<strong>Dept Manager</strong> untuk review.';

        if (!empty($upload_errors)) {
            $msg .= '<br><small class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>'
                . 'Beberapa lampiran gagal diupload: ' . implode(', ', $upload_errors) . '</small>';
        }

        echo json_encode([
            'status'   => 'success',
            'message'  => $msg,
            'redirect' => site_url('pengajuan'),
        ]);
    }
    //   - tidak_lulus_inspeksi (setelah perbaikan unit)
    //   - ditolak_ktt
    //   - ditolak_ohs_supt
    // Langsung masuk ke antrian Dept Manager (status = diterima_manager)
    // ============================================================
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

        // Status yang diizinkan untuk diajukan ulang
        $status_boleh = ['tidak_lulus_inspeksi', 'ditolak_ktt', 'ditolak_ohs_supt'];
        if (!in_array($pengajuan->status, $status_boleh)) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Pengajuan dengan status <strong>' . $pengajuan->status . '</strong> tidak dapat diajukan ulang.',
            ]);
            return;
        }

        // Admin Dept hanya bisa resubmit pengajuannya sendiri
        if (in_array(7, $roles) && !in_array(1, $roles) && $pengajuan->id_pemohon != $id_user) {
            echo json_encode(['status' => 'error', 'message' => 'Anda hanya dapat mengajukan ulang pengajuan milik Anda sendiri.']);
            return;
        }

        $this->db->trans_start();

        // Ubah status: langsung masuk antrian Dept Manager untuk di-review ulang
        $this->db->where('id_pengajuan', $id_pengajuan)->update('pengajuan_uji', [
            'status'                  => 'pengajuan_ulang',
            'alasan_pengajuan_ulang'  => $alasan,
            'tanggal_pengajuan'       => date('Y-m-d H:i:s'), // update tanggal
        ]);

        // Catat di approval log
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

    private function _upload_lampiran($id_pengajuan)
    {
        $errors     = [];
        $jenis_list = ['stnk', 'unit_depan', 'unit_belakang', 'unit_kiri', 'unit_kanan'];
        $path       = FCPATH . 'uploads/lampiran/' . $id_pengajuan . '/';
        if (!is_dir($path)) mkdir($path, 0755, true);
        foreach ($jenis_list as $jenis) {
            $field = 'lampiran_' . $jenis;
            if (empty($_FILES[$field]['name'])) continue;
            $this->upload->initialize(['upload_path' => $path, 'allowed_types' => 'jpg|jpeg|png|pdf', 'max_size' => 5120, 'file_name' => $jenis . '_' . time(), 'overwrite' => true]);
            if (!$this->upload->do_upload($field)) {
                $errors[] = $this->upload->display_errors('', '');
            } else {
                $info = $this->upload->data();
                $this->pengajuan_model->insert_lampiran(['id_pengajuan' => $id_pengajuan, 'jenis_lampiran' => $jenis, 'file_path' => 'uploads/lampiran/' . $id_pengajuan . '/' . $info['file_name'], 'uploaded_at' => date('Y-m-d H:i:s')]);
            }
        }
        return $errors;
    }

    // Upload satu file lampiran — untuk maintenance_record (opsional)
    private function _upload_single_lampiran($id_pengajuan, $jenis, $field_name)
    {
        $path = FCPATH . 'uploads/lampiran/' . $id_pengajuan . '/';
        if (!is_dir($path)) mkdir($path, 0755, true);
        $this->upload->initialize([
            'upload_path'   => $path,
            'allowed_types' => 'jpg|jpeg|png|pdf|doc|docx|xls|xlsx',
            'max_size'      => 10240,   // 10MB — dokumen bisa lebih besar
            'file_name'     => $jenis . '_' . time(),
            'overwrite'     => true,
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
        return null; // null = sukses
    }

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
        $cfg = $map[$status] ?? ['bg-secondary text-white', $status];
        return '<span class="badge ' . $cfg[0] . '">' . $cfg[1] . '</span>';
    }

    private function _tombol_aksi($row)
    {
        $id    = $row->id_pengajuan;
        $roles = $this->_user_roles();
        $uid   = (int) $this->session->userdata('id_user');

        $btn  = '<div class="d-flex gap-1 flex-wrap">';
        $btn .= '<button class="btn btn-sm btn-outline-primary py-0 btn-detail" data-id="' . $id . '" title="Lihat Detail"><i class="bi bi-eye"></i></button>';

        // Admin Dept edit kalau draft / ditolak manager
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

        // Dept Manager
        if ($this->_has_role([1, 6], $roles) && in_array($row->status, ['pengajuan_baru', 'pengajuan_ulang', 'ditolak_admin_ohs'])) {
            $btn .= '<button class="btn btn-sm btn-success py-0 btn-approve" data-id="' . $id . '" data-level="dept_manager" title="Setujui"><i class="bi bi-check-lg"></i></button>';
            $btn .= '<button class="btn btn-sm btn-danger  py-0 btn-reject"  data-id="' . $id . '" data-level="dept_manager" title="Tolak"><i class="bi bi-x-lg"></i></button>';
        }

        // Admin OHS — review dokumen
        if ($this->_has_role([1, 5], $roles) && $row->status === 'diterima_manager') {
            $btn .= '<button class="btn btn-sm btn-success py-0 btn-approve" data-id="' . $id . '" data-level="admin_ohs" title="Setujui & Jadwalkan"><i class="bi bi-calendar-check"></i></button>';
            $btn .= '<button class="btn btn-sm btn-danger  py-0 btn-reject"  data-id="' . $id . '" data-level="admin_ohs" title="Tolak"><i class="bi bi-x-lg"></i></button>';
        }

        // Admin OHS — review hasil inspeksi sudah dihapus (langsung ke OHS Supt jika lulus)
        // Status lulus_inspeksi → OHS Supt yang handle
        // Status tidak_lulus_inspeksi → Dept Manager yang handle (sudah di bagian atas)

        // Admin OHS — release stiker setelah acc KTT
        if ($this->_has_role([1, 5], $roles) && $row->status === 'acc_ktt') {
            $btn .= '<button class="btn btn-sm btn-success py-0 btn-release-stiker" data-id="' . $id . '" title="Terbitkan Stiker"><i class="bi bi-patch-check"></i></button>';
        }

        // OHS Superintendent
        if ($this->_has_role([1, 3], $roles) && $row->status === 'diterima_admin_ohs') {
            $btn .= '<button class="btn btn-sm btn-success py-0 btn-approve" data-id="' . $id . '" data-level="ohs_supt" title="Setujui OHS Supt"><i class="bi bi-check-lg"></i></button>';
            $btn .= '<button class="btn btn-sm btn-danger  py-0 btn-reject"  data-id="' . $id . '" data-level="ohs_supt" title="Tolak"><i class="bi bi-x-lg"></i></button>';
        }

        // KTT
        if ($this->_has_role([1, 2], $roles) && $row->status === 'diterima_ohs_supt') {
            $btn .= '<button class="btn btn-sm btn-success py-0 btn-approve" data-id="' . $id . '" data-level="ktt" title="ACC KTT"><i class="bi bi-check-lg"></i></button>';
            $btn .= '<button class="btn btn-sm btn-danger  py-0 btn-reject"  data-id="' . $id . '" data-level="ktt" title="Tolak"><i class="bi bi-x-lg"></i></button>';
        }

        // Mekanik / Inspektor — form inspeksi (dijadwalkan) atau verifikasi perbaikan (inspeksi_ulang)
        if ($this->_has_role([1, 4], $roles) && $row->status === 'dijadwalkan') {
            $btn .= '<a href="' . site_url('checklist/form/' . $id) . '" class="btn btn-sm btn-warning py-0" title="Isi Form Inspeksi"><i class="bi bi-tools"></i></a>';
        }
        if ($this->_has_role([1, 4], $roles) && $row->status === 'inspeksi_ulang') {
            $btn .= '<a href="' . site_url('checklist/form/' . $id) . '" class="btn btn-sm btn-info py-0 text-white" title="Verifikasi Hasil Perbaikan"><i class="bi bi-patch-check"></i></a>';
        }

        // Admin Dept — tombol tindakan untuk status yang dikembalikan
        $status_boleh_ulang = ['ditolak_ktt', 'ditolak_ohs_supt'];
        if (
            $this->_has_role([1, 7], $roles)
            && $row->status === 'tidak_lulus_inspeksi'
            && ($uid == $row->id_pemohon || in_array(1, $roles))
        ) {
            // → form perbaikan (input bukti perbaikan, langsung ke inspektor)
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
            // ditolak_ktt / ditolak_ohs_supt → modal resubmit biasa
            $info_btn = 'Pengajuan dikembalikan — ajukan ulang ke Dept Manager';
            $btn .= '<button class="btn btn-sm btn-warning py-0 btn-resubmit fw-semibold"'
                . ' data-id="' . $id . '"'
                . ' data-polisi="' . html_escape($row->no_polisi) . '"'
                . ' data-status="' . $row->status . '"'
                . ' data-info="' . html_escape($info_btn) . '"'
                . ' title="Ajukan Ulang">'
                . '<i class="bi bi-arrow-repeat me-1"></i>Ajukan Ulang</button>';
        }

        $btn .= '</div>';
        return $btn;
    }
    private function _upload_replace_lampiran($id_pengajuan, $jenis, $field_name)
    {
        $path = FCPATH . 'uploads/lampiran/' . $id_pengajuan . '/';
        if (!is_dir($path)) mkdir($path, 0755, true);

        // Tentukan allowed types berdasarkan jenis
        $doc_types = 'jpg|jpeg|png|pdf|doc|docx|xls|xlsx';
        $img_types = 'jpg|jpeg|png';
        $allowed   = ($jenis === 'stnk' || $jenis === 'maintenance_record')
            ? $doc_types
            : $img_types;

        $this->upload->initialize([
            'upload_path'   => $path,
            'allowed_types' => $allowed,
            'max_size'      => ($jenis === 'maintenance_record') ? 10240 : 5120,
            'file_name'     => $jenis . '_' . time(),
            'overwrite'     => true,
        ]);

        if (!$this->upload->do_upload($field_name)) {
            return $this->upload->display_errors('', '');
        }

        $info       = $this->upload->data();
        $new_path   = 'uploads/lampiran/' . $id_pengajuan . '/' . $info['file_name'];

        // Cek apakah record lama ada
        $existing = $this->db
            ->where('id_pengajuan', $id_pengajuan)
            ->where('jenis_lampiran', $jenis)
            ->get('pengajuan_lampiran')
            ->row();

        if ($existing) {
            // Hapus file fisik lama jika ada dan berbeda
            $old_file = FCPATH . $existing->file_path;
            if (file_exists($old_file) && $existing->file_path !== $new_path) {
                @unlink($old_file);
            }
            // Update record
            $this->db
                ->where('id_lampiran', $existing->id_lampiran)
                ->update('pengajuan_lampiran', [
                    'file_path'   => $new_path,
                    'uploaded_at' => date('Y-m-d H:i:s'),
                ]);
        } else {
            // Insert baru
            $this->pengajuan_model->insert_lampiran([
                'id_pengajuan'   => $id_pengajuan,
                'jenis_lampiran' => $jenis,
                'file_path'      => $new_path,
                'uploaded_at'    => date('Y-m-d H:i:s'),
            ]);
        }

        return null; // sukses
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
            if (in_array((int)$r, $user_roles)) return true;
        }
        return false;
    }

    private function _audit($aksi, $tabel, $id_ref)
    {
        $this->db->insert('audit_log', ['id_user' => $this->session->userdata('id_user'), 'aksi' => $aksi, 'tabel' => $tabel, 'id_ref' => $id_ref, 'created_at' => date('Y-m-d H:i:s')]);
    }
}
