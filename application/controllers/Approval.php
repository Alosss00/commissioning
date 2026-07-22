<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Approval extends CI_Controller
{
    // =========================================================
    // STATUS LABEL — dipakai juga oleh view dan Pengajuan.php
    // =========================================================
    public static $STATUS_LABEL = [
        'draft'                  => ['bg-secondary text-white',  'Draft'],
        'pengajuan_baru'         => ['bg-primary text-white',    'Pengajuan Baru'],
        'pengajuan_ulang'        => ['bg-info text-white',       'Pengajuan Ulang'],
        'diterima_manager'       => ['bg-warning text-dark',     'Diterima Manager'],
        'ditolak_manager'        => ['bg-danger text-white',     'Ditolak Manager'],
        'dijadwalkan'            => ['bg-primary text-white',    'Dijadwalkan Inspeksi'],
        'ditolak_admin_ohs'      => ['bg-danger text-white',     'Ditolak Admin OHS'],
        'selesai_inspeksi'       => ['bg-warning text-dark',     'Selesai Inspeksi'],
        'lulus_inspeksi'         => ['bg-success text-white',    'Lulus — Menunggu OHS Supt'],
        'tidak_lulus_inspeksi'   => ['bg-danger text-white',     'Tidak Lulus — Dikembalikan'],
        'siap_verifikasi'        => ['bg-warning text-dark',     'Siap Verifikasi Fisik'],
        'inspeksi_ulang'         => ['bg-info text-white',       'Siap Pengujian Ulang'],
        'diterima_admin_ohs'     => ['bg-info text-white',       'Diterima Admin OHS'],
        'diterima_ohs_supt'      => ['bg-info text-white',       'Diterima OHS Superintendent'],
        'ditolak_ohs_supt'       => ['bg-danger text-white',     'Ditolak OHS Superintendent'],
        'acc_ktt'                => ['bg-success text-white',    'Disetujui KTT'],
        'ditolak_ktt'            => ['bg-danger text-white',     'Ditolak KTT'],
        'stiker_keluar'          => ['bg-success text-white',    'Stiker Sudah Keluar'],
        'rejected'               => ['bg-danger text-white',     'Ditolak'],
        'menunggu_ktt_2'         => ['bg-warning text-dark',     'Menunggu KTT Kedua'],
        'dicabut_ktt'            => ['bg-dark text-white',       'Dicabut oleh KTT'],
    ];

    // =========================================================
    // KONFIGURASI LEVEL
    // Roles: 1=Super Admin, 2=KTT, 3=OHS Supt, 4=Inspektor,
    //        5=Admin OHS, 6=Dept Manager, 7=Admin Departemen
    // =========================================================
    private $_levels = [
        'dept_manager' => [
            'role_required'  => [1, 6],
            'status_masuk'   => ['pengajuan_baru', 'pengajuan_ulang', 'ditolak_admin_ohs'],
            'status_approve' => 'diterima_manager',
            'status_reject'  => 'ditolak_manager',
            'label'          => 'Review Dept Manager',
            'desc'           => 'Review pengajuan dari Admin Departemen.',
            'reject_label'   => 'Tolak Pengajuan',
        ],
        'admin_ohs' => [
            'role_required'  => [1, 5],
            'status_masuk'   => ['diterima_manager'],
            'status_approve' => 'dijadwalkan',
            'status_reject'  => 'ditolak_admin_ohs',
            'label'          => 'Review Admin OHS',
            'desc'           => 'Verifikasi dokumen. Setuju = jadwalkan inspeksi + notifikasi inspektor.',
            'reject_label'   => 'Tolak (Kembalikan ke Dept Manager)',
            'approve_action' => 'jadwal',
        ],
        // ── VERIFIKASI PERBAIKAN ────────────────────────────────────────────
        // Inspektor memeriksa secara fisik apakah perbaikan yang dilakukan
        // Admin Departemen sudah benar.
        //   ACC  → inspeksi_ulang  (inspektor isi ulang form checklist)
        //   Tolak → tidak_lulus_inspeksi (Admin Dept harus perbaiki ulang)
        // ────────────────────────────────────────────────────────────────────
        'verif_perbaikan' => [
            'role_required'  => [1, 4],
            'status_masuk'   => ['siap_verifikasi'],
            'status_approve' => 'inspeksi_ulang',
            'status_reject'  => 'tidak_lulus_inspeksi',
            'label'          => 'Verifikasi Perbaikan',
            'desc'           => 'Inspektor memeriksa fisik apakah perbaikan sudah sesuai. ACC → Siap Pengujian Ulang (inspektor isi checklist ulang). Tolak → Admin Departemen harus perbaiki ulang.',
            'reject_label'   => 'Tolak — Perbaikan Belum Sesuai',
        ],
        'ohs_supt' => [
            'role_required'  => [1, 3],
            'status_masuk'   => ['lulus_inspeksi', 'diterima_admin_ohs'],
            'status_approve' => 'diterima_ohs_supt',
            'status_reject'  => 'ditolak_ohs_supt',
            'label'          => 'OHS Superintendent',
            'desc'           => 'Review hasil inspeksi yang LULUS.',
            'reject_label'   => 'Tolak (Kembalikan ke Admin OHS)',
        ],
        'ktt' => [
            'role_required'  => [1, 2],
            'status_masuk'   => ['diterima_ohs_supt', 'menunggu_ktt_2'],
            'status_approve' => 'acc_ktt',
            'status_reject'  => 'ditolak_ktt',
            'label'          => 'Approval KTT',
            'desc'           => 'Wajib 2 KTT berbeda. KTT pertama → Menunggu KTT Kedua. KTT kedua → ACC KTT.',
            'reject_label'   => 'Tolak (Kembalikan ke Admin OHS)',
        ],
        'release_stiker' => [
            'role_required'  => [1, 5],
            'status_masuk'   => ['acc_ktt'],
            'status_approve' => 'stiker_keluar',
            'status_reject'  => null,
            'label'          => 'Penerbitan Stiker',
            'desc'           => 'Terbitkan stiker kelayakan. Email otomatis dikirim ke Admin Departemen.',
            'approve_action' => 'stiker',
        ],
    ];

    public function __construct()
    {
        parent::__construct();
        $this->load->model([
            'Approval_model'  => 'approval_model',
            'Pengajuan_model' => 'pengajuan_model',
            'Checklist_model' => 'checklist_model',
        ]);
        $this->load->library('session');
        $this->load->helper('url');
        if (!$this->session->userdata('id_user')) redirect('auth/login');
    }

    // =========================================================
    // AJAX — Get detail pengajuan untuk modal release stiker
    // =========================================================
    public function get_detail_stiker()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $id = (int) $this->input->post('id_pengajuan');

        $departemen = $this->session->userdata('departemen');
        $data = $this->db
            ->select('pu.id_pengajuan, pu.tipe_pengajuan, pu.tipe_akses, pu.tgl_acc_ktt,
                k.no_polisi, t.nama_tipe AS jenis_kendaraan, k.merk, k.tipe AS tipe_kendaraan,
                k.tahun, k.nomor_unit, k.perusahaan,
                pu.nomor_mesin, pu.nomor_rangka,
                u.nama AS nama_pemohon, u.email AS email_pemohon')
            ->from('pengajuan_uji pu')
            ->join('kendaraan k',      'k.id_kendaraan = pu.id_kendaraan',          'left')
            ->join('tipe_kendaraan t', 't.id_tipe_kendaraan = k.id_tipe_kendaraan', 'left')
            ->join('users u',          'u.id_user = pu.id_pemohon',                 'left')
            ->where('pu.id_pengajuan', $id);
        if (!in_array(1, $this->_user_roles()) && !empty($departemen)) {
            $data->where('k.perusahaan', $departemen);
        }
        $data = $data->get()->row();

        if (!$data) {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
            return;
        }

        $tgl_expired = null;
        if (!empty($data->tgl_acc_ktt)) {
            $tgl_expired = date('d M Y', strtotime($data->tgl_acc_ktt . ' + 6 months'));
        }

        echo json_encode([
            'status'      => 'success',
            'data'        => $data,
            'tgl_expired' => $tgl_expired,
        ]);
    }

    // =========================================================
    // ROUTES
    // =========================================================
    public function manager()
    {
        $this->_index('dept_manager');
    }
    public function admin_ohs()
    {
        $this->_index('admin_ohs');
    }
    public function ohs_supt()
    {
        $this->_index('ohs_supt');
    }
    public function ktt()
    {
        $this->_index('ktt');
    }
    public function stiker()
    {
        $this->_index('release_stiker');
    }
    // Inspektor verifikasi fisik perbaikan — status masuk: siap_verifikasi
    public function verif_perbaikan()
    {
        $this->_index('verif_perbaikan');
    }

    // =========================================================
    // INDEX
    // =========================================================
    private function _index($level)
    {
        $cfg   = $this->_cfg($level);
        $roles = $this->_user_roles();

        if (!$this->_has_access($cfg['role_required'], $roles)) {
            $this->session->set_flashdata('error', 'Akses ditolak.');
            redirect('dashboard');
        }

        $filters = [];
        $departemen = $this->session->userdata('departemen');
        if (!in_array(1, $roles) && !empty($departemen)) {
            $filters['departemen'] = $departemen;
            if (in_array(7, $roles)) {
                $filters['id_pemohon'] = (int) $this->session->userdata('id_user');
            }
        }

        $status_show = array_unique(array_merge(
            $cfg['status_masuk'],
            [$cfg['status_approve']],
            !empty($cfg['status_reject']) ? [$cfg['status_reject']] : []
        ));

        $list    = $this->approval_model->get_list($status_show, array_merge($filters, ['search' => $this->input->get('search')]) );
        $pending = $this->approval_model->get_list($cfg['status_masuk'], $filters);

        $id_user = (int) $this->session->userdata('id_user');
        $my_approvals = [];
        if ($level === 'ktt') {
            $my_approvals_raw = $this->db->select('id_pengajuan')
                ->where('id_ktt', $id_user)
                ->where('aksi', 'approve')
                ->get('ktt_approval')
                ->result_array();
            $my_approvals = array_column($my_approvals_raw, 'id_pengajuan');
        }

        $data = [
            'title'         => $cfg['label'],
            'user'          => $this->session->userdata(),
            'level'         => $level,
            'cfg'           => $cfg,
            'list'          => $list,
            'pending_count' => count($pending),
            'status_masuk'  => $cfg['status_masuk'],
            'status_labels' => self::$STATUS_LABEL,
            'my_approvals'  => $my_approvals,
        ];

        $this->load->view('templates/header',  $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('approval/index',    $data);
        $this->load->view('templates/footer',  $data);
    }

    // =========================================================
    // DETAIL
    // =========================================================
    public function detail($level, $id_pengajuan = null)
    {
        $cfg   = $this->_cfg($level);
        $roles = $this->_user_roles();

        if (!$this->_has_access($cfg['role_required'], $roles)) redirect('dashboard');

        $filters = [];
        $departemen = $this->session->userdata('departemen');
        if (!in_array(1, $roles) && !empty($departemen)) {
            $filters['departemen'] = $departemen;
            if (in_array(7, $roles)) {
                $filters['id_pemohon'] = (int) $this->session->userdata('id_user');
            }
        }

        $pengajuan = $this->approval_model->get_detail($id_pengajuan, $filters);
        if (!$pengajuan) show_404();

        $id_user = (int) $this->session->userdata('id_user');
        $has_approved = false;
        if ($level === 'ktt') {
            $check = $this->db->where('id_pengajuan', $id_pengajuan)
                ->where('id_ktt', $id_user)
                ->where('aksi', 'approve')
                ->count_all_results('ktt_approval');
            $has_approved = ($check > 0);
        }

        $data = [
            'title'         => 'Detail — ' . $pengajuan->no_polisi,
            'user'          => $this->session->userdata(),
            'level'         => $level,
            'cfg'           => $cfg,
            'pengajuan'     => $pengajuan,
            'lampiran'      => $this->approval_model->get_lampiran($id_pengajuan),
            'riwayat'       => $this->approval_model->get_riwayat($id_pengajuan),
            'status_labels' => self::$STATUS_LABEL,
            'uji'           => null,
            'summary'       => null,
            'has_approved'  => $has_approved,
        ];

        $uji = $this->db
            ->select('uk.*, u.nama AS nama_mekanik')
            ->from('uji_kelayakan uk')
            ->join('users u', 'u.id_user = uk.id_mekanik', 'left')
            ->where('uk.id_pengajuan', $id_pengajuan)
            ->get()->row();

        $data['uji']     = $uji;
        $data['summary'] = $uji ? $this->checklist_model->get_summary($uji->id_uji) : null;

        $perbaikan_list = [];
        if ($uji) {
            $perb_rows = $this->db
                ->select('pu.*, u.nama AS nama_verifikator')
                ->from('perbaikan_unit pu')
                ->join('users u', 'u.id_user = pu.id_verifikator', 'left')
                ->where('pu.id_pengajuan', $id_pengajuan)
                ->order_by('pu.id_perbaikan', 'ASC')
                ->get()->result();
            foreach ($perb_rows as $pb) {
                $pb->lampiran = $this->db
                    ->where('id_perbaikan', $pb->id_perbaikan)
                    ->get('perbaikan_lampiran')->result();
            }
            $perbaikan_list = $perb_rows;
        }

        $history_versions = $uji ? $this->checklist_model->get_history_versions($uji->id_uji) : [];
        $history_detail   = $uji ? $this->checklist_model->get_checklist_history($uji->id_uji) : [];

        $data['perbaikan_list']   = $perbaikan_list;
        $data['history_versions'] = $history_versions;
        $data['history_detail']   = $history_detail;

        $this->load->view('templates/header',  $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('approval/detail',   $data);
        $this->load->view('templates/footer',  $data);
    }

    // =========================================================
    // PROSES AJAX
    // =========================================================
    public function proses()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $level        = $this->input->post('level');
        $id_pengajuan = (int) $this->input->post('id_pengajuan');
        $aksi         = $this->input->post('aksi');
        $catatan      = trim((string) $this->input->post('catatan'));
        $nomor_stiker = trim((string) $this->input->post('nomor_stiker'));

        $cfg   = $this->_cfg($level);
        $roles = $this->_user_roles();

        if (!$this->_has_access($cfg['role_required'], $roles)) {
            echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
            return;
        }
        if ($aksi === 'reject') {
            if (is_null($cfg['status_reject'])) {
                echo json_encode(['status' => 'error', 'message' => 'Level ini tidak memiliki opsi penolakan.']);
                return;
            }
            if (empty($catatan)) {
                echo json_encode(['status' => 'error', 'message' => 'Catatan alasan penolakan wajib diisi.']);
                return;
            }
        }

        $filters = [];
        $departemen = $this->session->userdata('departemen');
        if (!in_array(1, $roles) && !empty($departemen)) {
            $filters['departemen'] = $departemen;
            if (in_array(7, $roles)) {
                $filters['id_pemohon'] = (int) $this->session->userdata('id_user');
            }
        }

        $pengajuan = $this->approval_model->get_detail($id_pengajuan, $filters);
        if (!$pengajuan || !in_array($pengajuan->status, $cfg['status_masuk'])) {
            echo json_encode(['status' => 'error', 'message' => 'Status pengajuan tidak sesuai atau sudah diproses.']);
            return;
        }

        // ── Admin OHS approve → jadwalkan inspeksi ──
        if ($level === 'admin_ohs' && $aksi === 'approve') {
            $this->db->where('id_pengajuan', $id_pengajuan)->update('pengajuan_uji', ['status' => 'dijadwalkan']);
            $this->db->insert('pengajuan_approval', [
                'id_pengajuan'   => $id_pengajuan,
                'id_approver'    => $this->session->userdata('id_user'),
                'level_approval' => 'admin_ohs',
                'status'         => 'approved',
                'catatan'        => $catatan,
                'created_at'     => date('Y-m-d H:i:s'),
            ]);
            $this->_audit('approve_admin_ohs', $id_pengajuan);
            echo json_encode([
                'status'          => 'success',
                'aksi'            => 'approve',
                'message'         => 'Pengajuan disetujui. Silakan buat jadwal inspeksi untuk mekanik.',
                'redirect_jadwal' => site_url('jadwal/create/' . $id_pengajuan),
            ]);
            return;
        }

        // ── Release stiker ──
        if ($level === 'release_stiker' && $aksi === 'approve') {
            if (empty($nomor_stiker)) {
                echo json_encode(['status' => 'error', 'message' => 'Nomor stiker wajib diisi.']);
                return;
            }

            $p_ktt         = $this->db->select('tgl_acc_ktt')->where('id_pengajuan', $id_pengajuan)->get('pengajuan_uji')->row();
            $basis_expired = (!empty($p_ktt->tgl_acc_ktt)) ? $p_ktt->tgl_acc_ktt : date('Y-m-d H:i:s');
            $tgl_expired   = date('Y-m-d H:i:s', strtotime($basis_expired . ' + 6 months'));
            $tgl_release   = date('Y-m-d H:i:s');

            $this->db->trans_start();
            $this->db->where('id_pengajuan', $id_pengajuan)->update('pengajuan_uji', ['status' => 'stiker_keluar']);
            $this->db->insert('sticker_release', [
                'id_pengajuan'    => $id_pengajuan,
                'nomor_sticker'   => $nomor_stiker,
                'tanggal_release' => $tgl_release,
                'tgl_expired'     => $tgl_expired,
                'is_expired'      => 0,
                'released_by'     => $this->session->userdata('id_user'),
            ]);
            $this->db->insert('pengajuan_approval', [
                'id_pengajuan'   => $id_pengajuan,
                'id_approver'    => $this->session->userdata('id_user'),
                'level_approval' => 'release_stiker',
                'status'         => 'approved',
                'catatan'        => 'Nomor stiker: ' . $nomor_stiker . ' | Expired: ' . date('d M Y', strtotime($tgl_expired)),
                'created_at'     => $tgl_release,
            ]);
            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                $this->_audit('release_stiker', $id_pengajuan);
                echo json_encode([
                    'status'   => 'success',
                    'aksi'     => 'approve',
                    'message'  => 'Stiker berhasil diterbitkan. Expired: <strong>' . date('d M Y', strtotime($tgl_expired)) . '</strong>. Email dikirim ke Admin Departemen.',
                    'redirect' => site_url('approval/stiker'),
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal menerbitkan stiker. Silakan coba lagi.']);
            }
            return;
        }

        // ── KTT: approve wajib 2 orang berbeda, reject langsung ──
        if ($level === 'ktt') {
            $id_ktt = (int) $this->session->userdata('id_user');

            if ($aksi === 'reject') {
                $this->db->trans_start();
                $this->db->insert('ktt_approval', [
                    'id_pengajuan' => $id_pengajuan,
                    'id_ktt'       => $id_ktt,
                    'aksi'         => 'reject',
                    'catatan'      => $catatan,
                    'created_at'   => date('Y-m-d H:i:s'),
                ]);
                $this->db->where('id_pengajuan', $id_pengajuan)
                    ->update('pengajuan_uji', ['status' => 'ditolak_ktt']);
                $this->db->insert('pengajuan_approval', [
                    'id_pengajuan'   => $id_pengajuan,
                    'id_approver'    => $id_ktt,
                    'level_approval' => 'ktt',
                    'status'         => 'rejected',
                    'catatan'        => $catatan,
                    'created_at'     => date('Y-m-d H:i:s'),
                ]);
                $this->db->trans_complete();
                if (!$this->db->trans_status()) {
                    echo json_encode(['status' => 'error', 'message' => 'Gagal memproses.']);
                    return;
                }
                $this->_audit('reject_ktt', $id_pengajuan);
                echo json_encode([
                    'status'   => 'success',
                    'aksi'     => 'reject',
                    'message'  => 'Pengajuan <strong>#PU-' . str_pad($id_pengajuan, 4, '0', STR_PAD_LEFT) . '</strong> ditolak KTT.',
                    'redirect' => site_url('approval/ktt'),
                ]);
                return;
            }

            // Approve: cek apakah KTT ini sudah approve sebelumnya
            $existing_ktt = $this->db
                ->where('id_pengajuan', $id_pengajuan)
                ->where('id_ktt', $id_ktt)
                ->where('aksi', 'approve')
                ->count_all_results('ktt_approval');

            if ($existing_ktt > 0) {
                echo json_encode(['status' => 'error', 'message' => 'Anda sudah memberikan persetujuan untuk pengajuan ini.']);
                return;
            }

            $approved_count = $this->db
                ->where('id_pengajuan', $id_pengajuan)
                ->where('aksi', 'approve')
                ->count_all_results('ktt_approval');

            $this->db->trans_start();
            $this->db->insert('ktt_approval', [
                'id_pengajuan' => $id_pengajuan,
                'id_ktt'       => $id_ktt,
                'aksi'         => 'approve',
                'catatan'      => $catatan,
                'created_at'   => date('Y-m-d H:i:s'),
            ]);

            if ($approved_count + 1 >= 2) {
                $new_status = 'acc_ktt';
                $tgl_acc    = date('Y-m-d H:i:s');
                $this->db->where('id_pengajuan', $id_pengajuan)
                    ->update('pengajuan_uji', [
                        'status'            => 'acc_ktt',
                        'tgl_acc_ktt'       => $tgl_acc,
                        'ktt_approve_count' => 2,
                    ]);
                $msg = 'Pengajuan <strong>#PU-' . str_pad($id_pengajuan, 4, '0', STR_PAD_LEFT)
                    . '</strong> disetujui 2 KTT. Admin OHS dapat menerbitkan stiker.';
            } else {
                $new_status = 'menunggu_ktt_2';
                $this->db->where('id_pengajuan', $id_pengajuan)
                    ->update('pengajuan_uji', [
                        'status'            => 'menunggu_ktt_2',
                        'ktt_approve_count' => 1,
                    ]);
                $msg = 'Persetujuan KTT pertama berhasil dicatat. Menunggu persetujuan KTT kedua.';
            }

            $this->db->insert('pengajuan_approval', [
                'id_pengajuan'   => $id_pengajuan,
                'id_approver'    => $id_ktt,
                'level_approval' => 'ktt',
                'status'         => 'approved',
                'catatan'        => $catatan
                    . ($new_status === 'acc_ktt'
                        ? ' [KTT ke-2 — ACC FINAL]'
                        : ' [KTT ke-1 — Menunggu KTT ke-2]'),
                'created_at'     => date('Y-m-d H:i:s'),
            ]);
            $this->db->trans_complete();

            if (!$this->db->trans_status()) {
                echo json_encode(['status' => 'error', 'message' => 'Gagal memproses.']);
                return;
            }

            $this->_audit('approve_ktt', $id_pengajuan);

            $redirect_url = site_url('approval/ktt');
            if ($new_status === 'acc_ktt' && $this->_has_access([1, 5], $roles)) {
                $redirect_url = site_url('approval/stiker');
            }

            echo json_encode([
                'status'   => 'success',
                'aksi'     => 'approve',
                'message'  => $msg,
                'redirect' => $redirect_url,
            ]);
            return;
        }

        // ── verif_perbaikan: ACC → inspeksi_ulang, Tolak → tidak_lulus_inspeksi ──
        if ($level === 'verif_perbaikan') {
            $id_inspektor = (int) $this->session->userdata('id_user');

            $perbaikan = $this->db
                ->where('id_pengajuan', $id_pengajuan)
                ->order_by('id_perbaikan', 'DESC')
                ->get('perbaikan_unit')->row();

            $this->db->trans_start();

            if ($aksi === 'approve') {
                // ACC: perbaikan OK → siap pengujian ulang checklist
                $new_status       = 'inspeksi_ulang';
                $perbaikan_status = 'diverifikasi';
                $catatan_log      = 'Verifikasi fisik DITERIMA. Unit siap pengujian ulang.'
                    . ($catatan ? ' Catatan: ' . $catatan : '');
            } else {
                // Tolak: perbaikan belum sesuai → kembali ke Admin Dept
                $new_status       = 'tidak_lulus_inspeksi';
                $perbaikan_status = 'ditolak_verifikasi';
                $catatan_log      = 'Verifikasi fisik DITOLAK. Perbaikan belum sesuai. ' . $catatan;
            }

            // Update perbaikan_unit
            if ($perbaikan) {
                $this->db->where('id_perbaikan', $perbaikan->id_perbaikan)
                    ->update('perbaikan_unit', [
                        'status'         => $perbaikan_status,
                        'id_verifikator' => $id_inspektor,
                        'updated_at'     => date('Y-m-d H:i:s'),
                    ]);
            }

            // Update status pengajuan
            $this->db->where('id_pengajuan', $id_pengajuan)
                ->update('pengajuan_uji', ['status' => $new_status]);

            // Log approval
            $this->db->insert('pengajuan_approval', [
                'id_pengajuan'   => $id_pengajuan,
                'id_approver'    => $id_inspektor,
                'level_approval' => 'verif_perbaikan',
                'status'         => $aksi === 'approve' ? 'approved' : 'rejected',
                'catatan'        => $catatan_log,
                'created_at'     => date('Y-m-d H:i:s'),
            ]);

            $this->db->trans_complete();

            if (!$this->db->trans_status()) {
                echo json_encode(['status' => 'error', 'message' => 'Gagal memproses verifikasi.']);
                return;
            }

            $this->_audit(
                $aksi === 'approve' ? 'approve_verif_perbaikan' : 'reject_verif_perbaikan',
                $id_pengajuan
            );

            $no = '#PU-' . str_pad($id_pengajuan, 4, '0', STR_PAD_LEFT);

            if ($aksi === 'approve') {
                echo json_encode([
                    'status'   => 'success',
                    'aksi'     => 'approve',
                    'message'  => 'Verifikasi fisik <strong>' . $no . '</strong> diterima. '
                        . 'Unit sekarang berstatus <strong>Siap Pengujian Ulang</strong>. '
                        . 'Silakan lakukan pengujian checklist ulang.',
                    'redirect' => site_url('checklist/form/' . $id_pengajuan),
                ]);
            } else {
                echo json_encode([
                    'status'   => 'success',
                    'aksi'     => 'reject',
                    'message'  => 'Verifikasi fisik <strong>' . $no . '</strong> ditolak. '
                        . 'Admin Departemen akan diminta melakukan perbaikan ulang.',
                    'redirect' => site_url('approval/verif_perbaikan'),
                ]);
            }
            return;
        }

        // ── Proses normal (dept_manager, ohs_supt) ──
        $status_tujuan = $aksi === 'approve' ? $cfg['status_approve'] : $cfg['status_reject'];

        $ok = $this->approval_model->proses([
            'id_pengajuan' => $id_pengajuan,
            'aksi'         => $aksi,
            'level'        => $level,
            'status_next'  => $status_tujuan,
            'id_approver'  => $this->session->userdata('id_user'),
            'catatan'      => $catatan,
        ]);

        if (!$ok) {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memproses. Silakan coba lagi.']);
            return;
        }

        $this->_audit(($aksi === 'approve' ? 'approve_' : 'reject_') . $level, $id_pengajuan);

        $no  = '#PU-' . str_pad($id_pengajuan, 4, '0', STR_PAD_LEFT);
        $msg = $aksi === 'approve'
            ? 'Pengajuan <strong>' . $no . '</strong> berhasil disetujui.'
            : 'Pengajuan <strong>' . $no . '</strong> ditolak. Catatan telah dicatat.';

        $redirect_map = [
            'dept_manager'    => 'approval/manager',
            'admin_ohs'       => 'approval/admin_ohs',
            'ohs_supt'        => 'approval/ohs_supt',
            'ktt'             => 'approval/ktt',
            'release_stiker'  => 'approval/stiker',
            'verif_perbaikan' => 'approval/verif_perbaikan',
        ];

        echo json_encode([
            'status'   => 'success',
            'message'  => $msg,
            'aksi'     => $aksi,
            'redirect' => site_url($redirect_map[$level] ?? 'dashboard'),
        ]);
    }

    // =========================================================
    // PENCABUTAN STIKER — MULTI-ROLE WORKFLOW & ADMIN OHS EKSEKUSI
    // =========================================================

    // Halaman daftar pengajuan pencabutan stiker
    public function pencabutan()
    {
        $roles = $this->_user_roles();
        if (!$this->_has_access([1, 2, 3, 4, 5], $roles)) {
            $this->session->set_flashdata('error', 'Akses ditolak.');
            redirect('dashboard');
        }

        $filters = [
            'search' => $this->input->get('search'),
            'status_request' => $this->input->get('status'),
        ];

        $data['title']     = 'Pencabutan Stiker Kelayakan';
        $data['user']      = $this->session->userdata();
        $data['roles']     = $roles;
        $data['requests']  = $this->approval_model->get_pencabutan_list($filters);

        $this->load->view('templates/header',  $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('pencabutan/index',  $data);
        $this->load->view('templates/footer',  $data);
    }

    // Form modal request pencabutan (Inspektor=4, OHS Supt=3, KTT=2, Admin=1)
    public function request_cabut()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $roles = $this->_user_roles();
        if (!$this->_has_access([1, 2, 3, 4], $roles)) {
            echo json_encode(['status' => 'error', 'message' => 'Anda tidak memiliki hak untuk mengajukan pencabutan stiker.']);
            return;
        }

        $id_pengajuan = (int) $this->input->post('id_pengajuan');
        $alasan       = trim((string) $this->input->post('alasan'));

        if (empty($alasan)) {
            echo json_encode(['status' => 'error', 'message' => 'Alasan pencabutan wajib diisi.']);
            return;
        }

        $stiker = $this->db
            ->select('sr.id_sticker, sr.nomor_sticker, sr.dicabut')
            ->from('sticker_release sr')
            ->where('sr.id_pengajuan', $id_pengajuan)
            ->where('sr.dicabut', 0)
            ->order_by('sr.id_sticker', 'DESC')
            ->get()->row();

        if (!$stiker) {
            echo json_encode(['status' => 'error', 'message' => 'Tidak ada stiker aktif untuk pengajuan ini.']);
            return;
        }

        $id_user = (int) $this->session->userdata('id_user');
        
        // Tentukan primary role pengaju
        $role_pemohon = 4;
        if (in_array(1, $roles) || in_array(2, $roles)) {
            $role_pemohon = 2; // KTT / Super Admin
        } elseif (in_array(3, $roles)) {
            $role_pemohon = 3; // OHS Supt
        }

        $id_cabut = $this->approval_model->create_request_cabut($stiker->id_sticker, $id_pengajuan, $id_user, $role_pemohon, $alasan);

        if (!$id_cabut) {
            echo json_encode(['status' => 'error', 'message' => 'Gagal membuat permohonan pencabutan stiker.']);
            return;
        }

        $this->_audit('request_cabut_stiker', $id_pengajuan);

        $msg_map = [
            4 => 'Permohonan pencabutan stiker berhasil dikirim. Menunggu verifikasi dari OHS Superintendent.',
            3 => 'Permohonan pencabutan stiker berhasil dikirim. Menunggu approval dari KTT.',
            2 => 'Permohonan pencabutan stiker berhasil dibuat. Siap dieksekusi oleh Admin OHS.',
        ];

        echo json_encode([
            'status'   => 'success',
            'message'  => $msg_map[$role_pemohon] ?? 'Permohonan pencabutan stiker berhasil dikirim.',
            'redirect' => site_url('approval/pencabutan'),
        ]);
    }

    // Legacy fallback alias untuk request_cabut
    public function cabut_stiker()
    {
        return $this->request_cabut();
    }

    // Approve permohonan pencabutan (OHS Supt & KTT)
    public function approve_cabut()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $roles   = $this->_user_roles();
        $id_user = (int) $this->session->userdata('id_user');
        $id_cabut = (int) $this->input->post('id_cabut');

        $c = $this->approval_model->get_pencabutan_detail($id_cabut);
        if (!$c) {
            echo json_encode(['status' => 'error', 'message' => 'Data permohonan pencabutan tidak ditemukan.']);
            return;
        }

        $status_req = $c->status_request;

        if ($status_req === 'menunggu_ohs_supt') {
            if (!$this->_has_access([1, 3], $roles)) {
                echo json_encode(['status' => 'error', 'message' => 'Hanya OHS Superintendent yang dapat memverifikasi permohonan ini.']);
                return;
            }
            $this->db->where('id_cabut', $id_cabut)->update('pencabutan_stiker', [
                'status_request' => 'menunggu_ktt_1',
                'ohs_supt_by'    => $id_user,
                'ohs_supt_at'    => date('Y-m-d H:i:s'),
            ]);
            $msg = 'Permohonan disetujui oleh OHS Superintendent dan diteruskan ke KTT.';

        } elseif ($status_req === 'menunggu_ktt_1') {
            if (!$this->_has_access([1, 2], $roles)) {
                echo json_encode(['status' => 'error', 'message' => 'Hanya KTT yang dapat melakukan approval permohonan ini.']);
                return;
            }
            $this->db->where('id_cabut', $id_cabut)->update('pencabutan_stiker', [
                'status_request' => 'menunggu_ktt_2',
                'ktt_1_by'       => $id_user,
                'ktt_1_at'       => date('Y-m-d H:i:s'),
            ]);
            $msg = 'Approval KTT Pertama berhasil. Menunggu approval dari KTT Kedua.';

        } elseif ($status_req === 'menunggu_ktt_2') {
            if (!$this->_has_access([1, 2], $roles)) {
                echo json_encode(['status' => 'error', 'message' => 'Hanya KTT yang dapat melakukan approval permohonan ini.']);
                return;
            }
            if (!in_array(1, $roles) && (int)$c->ktt_1_by === $id_user) {
                echo json_encode(['status' => 'error', 'message' => 'KTT kedua harus merupakan KTT yang berbeda dari KTT pertama.']);
                return;
            }
            $this->db->where('id_cabut', $id_cabut)->update('pencabutan_stiker', [
                'status_request' => 'siap_dicabut',
                'ktt_2_by'       => $id_user,
                'ktt_2_at'       => date('Y-m-d H:i:s'),
            ]);
            $msg = 'Approval Dual KTT Selesai. Permohonan pencabutan stiker SIAP DIEKSEKUSI oleh Admin OHS.';
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Status permohonan ini tidak membutuhkan approval lagi.']);
            return;
        }

        $this->_audit('approve_cabut_stiker', $c->id_pengajuan);

        echo json_encode([
            'status'  => 'success',
            'message' => $msg,
        ]);
    }

    // Reject permohonan pencabutan (OHS Supt & KTT)
    public function reject_cabut()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $roles   = $this->_user_roles();
        if (!$this->_has_access([1, 2, 3], $roles)) {
            echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
            return;
        }

        $id_cabut = (int) $this->input->post('id_cabut');
        $catatan  = trim((string) $this->input->post('catatan'));

        $c = $this->approval_model->get_pencabutan_detail($id_cabut);
        if (!$c) {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
            return;
        }

        $this->db->where('id_cabut', $id_cabut)->update('pencabutan_stiker', [
            'status_request'    => 'ditolak',
            'catatan_penolakan' => $catatan,
        ]);

        $this->_audit('reject_cabut_stiker', $c->id_pengajuan);

        echo json_encode([
            'status'  => 'success',
            'message' => 'Permohonan pencabutan stiker telah ditolak.',
        ]);
    }

    // Eksekusi Pencabutan Stiker — Khusus Admin OHS (Role 5) atau Super Admin (Role 1)
    public function eksekusi_cabut()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $roles = $this->_user_roles();
        if (!$this->_has_access([1, 5], $roles)) {
            echo json_encode(['status' => 'error', 'message' => 'Hanya Admin OHS yang dapat melakukan eksekusi pencabutan stiker.']);
            return;
        }

        $id_cabut = (int) $this->input->post('id_cabut');
        $c = $this->approval_model->get_pencabutan_detail($id_cabut);

        if (!$c) {
            echo json_encode(['status' => 'error', 'message' => 'Data permohonan pencabutan tidak ditemukan.']);
            return;
        }

        if ($c->status_request !== 'siap_dicabut' && $c->status !== 'diperintahkan') {
            echo json_encode(['status' => 'error', 'message' => 'Permohonan pencabutan stiker belum memenuhi syarat approval untuk dieksekusi.']);
            return;
        }

        $id_admin_ohs = (int) $this->session->userdata('id_user');

        $this->db->trans_start();

        // Update status pencabutan
        $this->db->where('id_cabut', $id_cabut)->update('pencabutan_stiker', [
            'status_request'    => 'dilaksanakan',
            'status'            => 'dilaksanakan',
            'tgl_dilaksanakan'  => date('Y-m-d H:i:s'),
            'dilaksanakan_oleh' => $id_admin_ohs,
        ]);

        // Nonaktifkan stiker
        $this->db->where('id_sticker', $c->id_sticker)->update('sticker_release', [
            'dicabut'     => 1,
            'tgl_dicabut' => date('Y-m-d H:i:s'),
        ]);

        // Update status pengajuan uji
        $this->db->where('id_pengajuan', $c->id_pengajuan)->update('pengajuan_uji', [
            'status' => 'dicabut_ktt',
        ]);

        // Catat riwayat approval
        $this->db->insert('pengajuan_approval', [
            'id_pengajuan'   => $c->id_pengajuan,
            'id_approver'    => $id_admin_ohs,
            'level_approval' => 'admin_ohs',
            'status'         => 'rejected',
            'catatan'        => '[EKSEKUSI PENCABUTAN STIKER] ' . $c->alasan,
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        $this->_audit('eksekusi_cabut_stiker', $c->id_pengajuan);

        $this->db->trans_complete();

        if (!$this->db->trans_status()) {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengeksekusi pencabutan stiker.']);
            return;
        }

        // Kirim Notifikasi Email Otomatis sesuai kondisi pengaju
        if (file_exists(APPPATH . 'libraries/Sikuk_email.php')) {
            $this->load->library('sikuk_email');
            $this->sikuk_email->notif_stiker_dicabut($id_cabut);
        }

        echo json_encode([
            'status'  => 'success',
            'message' => 'Stiker kelayakan <strong>' . html_escape($c->nomor_sticker) . '</strong> berhasil <strong>DICABUT</strong>. Notifikasi email telah dikirimkan ke pihak terkait.',
        ]);
    }

    // =========================================================
    // HELPERS PRIVATE
    // =========================================================
    private function _cfg($level)
    {
        if (!isset($this->_levels[$level])) show_404();
        return $this->_levels[$level];
    }

    private function _user_roles()
    {
        $raw = $this->session->userdata('roles');
        if (is_array($raw) && !empty($raw)) return array_map('intval', $raw);
        $r = (int) $this->session->userdata('role');
        return $r > 0 ? [$r] : [];
    }

    private function _has_access(array $required, array $user_roles)
    {
        foreach ($required as $r) {
            if (in_array((int) $r, $user_roles)) return true;
        }
        return false;
    }

    private function _audit($aksi, $id_pengajuan)
    {
        $this->db->insert('audit_log', [
            'id_user'    => $this->session->userdata('id_user'),
            'aksi'       => $aksi,
            'tabel'      => 'pengajuan_uji',
            'id_ref'     => $id_pengajuan,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    // =========================================================
    // NOTIF EMAIL
    // =========================================================
    private function _notif_pemohon($id_pengajuan, $level, $aksi, $catatan = '')
    {
        if (file_exists(APPPATH . 'libraries/Sikuk_email.php')) {
            $this->load->library('sikuk_email');
            if ($aksi === 'reject') {
                switch ($level) {
                    case 'dept_manager':
                        $this->sikuk_email->notif_ditolak_manager($id_pengajuan, $catatan);
                        break;
                    case 'admin_ohs':
                        $this->sikuk_email->notif_ditolak_admin_ohs_ke_manager($id_pengajuan, $catatan);
                        break;
                    case 'ohs_supt':
                        $this->sikuk_email->notif_dikembalikan_ke_admin_ohs($id_pengajuan, 'OHS Superintendent', $catatan);
                        break;
                    case 'ktt':
                        $this->sikuk_email->notif_dikembalikan_ke_admin_ohs($id_pengajuan, 'KTT', $catatan);
                        break;
                }
            } elseif ($aksi === 'approve') {
                $nama_tahapan_map = [
                    'dept_manager'    => 'Verifikasi Dokumen oleh Admin OHS',
                    'ohs_supt'        => 'Final Approval oleh KTT',
                    'ktt'             => 'ACC KTT — Menunggu Penerbitan Stiker',
                    'verif_perbaikan' => 'Verifikasi Perbaikan Diterima — Siap Pengujian Ulang',
                ];
                if (isset($nama_tahapan_map[$level])) {
                    $this->sikuk_email->notif_progress($id_pengajuan, $nama_tahapan_map[$level]);
                }
            }
            return;
        }

        // Fallback email sederhana
        $p = $this->db
            ->select('pu.id_pengajuan, u.email AS email_pemohon, u.nama AS nama_pemohon, k.no_polisi')
            ->from('pengajuan_uji pu')
            ->join('users u',     'u.id_user = pu.id_pemohon',        'left')
            ->join('kendaraan k', 'k.id_kendaraan = pu.id_kendaraan', 'left')
            ->where('pu.id_pengajuan', $id_pengajuan)
            ->get()->row();

        if (!$p || empty($p->email_pemohon)) return;

        $level_label = [
            'dept_manager'    => 'Dept Manager',
            'admin_ohs'       => 'Admin OHS',
            'ohs_supt'        => 'OHS Superintendent',
            'ktt'             => 'KTT',
            'verif_perbaikan' => 'Inspektor',
        ];
        $penolak = $level_label[$level] ?? $level;
        $no      = '#PU-' . str_pad($id_pengajuan, 4, '0', STR_PAD_LEFT);

        $this->load->library('email');

        if ($aksi === 'reject') {
            $subject = '[TACTIC] Pengajuan Dikembalikan — Unit ' . $p->no_polisi;
            $body    = 'Yth. ' . htmlspecialchars($p->nama_pemohon) . ',<br><br>'
                . 'Pengajuan commissioning <strong>' . $no . '</strong> untuk unit <strong>'
                . htmlspecialchars($p->no_polisi) . '</strong> <strong>dikembalikan</strong>'
                . ' oleh <strong>' . $penolak . '</strong>.<br><br>'
                . ($catatan ? '<b>Catatan:</b> <em>"' . htmlspecialchars($catatan) . '"</em><br><br>' : '')
                . 'Mohon periksa kembali pengajuan Anda di sistem TACTIC.<br><br>'
                . 'Terima kasih,<br><b>TACTIC System</b>';
        } else {
            $tahapan_map = [
                'dept_manager'    => 'Verifikasi Dokumen oleh Admin OHS',
                'ohs_supt'        => 'Final Approval oleh KTT',
                'ktt'             => 'ACC KTT — Menunggu Penerbitan Stiker',
                'verif_perbaikan' => 'Verifikasi Perbaikan Diterima — Siap Pengujian Ulang',
            ];
            if (!isset($tahapan_map[$level])) return;
            $subject = '[TACTIC] Update Status Pengajuan — Unit ' . $p->no_polisi;
            $body    = 'Yth. ' . htmlspecialchars($p->nama_pemohon) . ',<br><br>'
                . 'Pengajuan commissioning <strong>' . $no . '</strong> untuk unit <strong>'
                . htmlspecialchars($p->no_polisi) . '</strong> telah disetujui dan berlanjut ke tahap:<br><br>'
                . '<strong>' . $tahapan_map[$level] . '</strong><br><br>'
                . 'Silakan pantau status pengajuan Anda di sistem TACTIC.<br><br>'
                . 'Terima kasih,<br><b>TACTIC System</b>';
        }

        $this->email->from(
            $this->config->item('sikuk_email_from') ?: 'noreply@sikuk.app',
            $this->config->item('sikuk_email_name') ?: 'TACTIC System'
        );
        $this->email->to($p->email_pemohon);
        $this->email->subject($subject);
        $this->email->message($body);
        @$this->email->send();
    }

    private function _notif_stiker($id_pengajuan)
    {
        $data = $this->db
            ->select('pu.*, u.email AS email_pemohon, u.nama AS nama_pemohon, k.no_polisi, t.nama_tipe AS jenis_kendaraan, sr.nomor_sticker')
            ->from('pengajuan_uji pu')
            ->join('users u',            'u.id_user = pu.id_pemohon',                 'left')
            ->join('kendaraan k',        'k.id_kendaraan = pu.id_kendaraan',          'left')
            ->join('tipe_kendaraan t',   't.id_tipe_kendaraan = k.id_tipe_kendaraan', 'left')
            ->join('sticker_release sr', 'sr.id_pengajuan = pu.id_pengajuan',         'left')
            ->where('pu.id_pengajuan', $id_pengajuan)
            ->get()->row();

        if (!$data || empty($data->email_pemohon)) return;

        $this->load->library('email');
        $this->email->from('noreply@sikuk.app', 'TACTIC System');
        $this->email->to($data->email_pemohon);
        $this->email->subject('[TACTIC] Stiker Kelayakan Telah Diterbitkan — ' . $data->no_polisi);
        $this->email->message(
            'Yth. ' . htmlspecialchars($data->nama_pemohon) . ',<br><br>'
                . 'Stiker kelayakan kendaraan Anda telah resmi diterbitkan:<br><br>'
                . '<b>No. Polisi</b>&nbsp;: ' . $data->no_polisi . '<br>'
                . '<b>Jenis</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ' . $data->jenis_kendaraan . '<br>'
                . '<b>No. Stiker</b> : ' . ($data->nomor_sticker ?? '-') . '<br>'
                . '<b>Tanggal</b>&nbsp;&nbsp;&nbsp;&nbsp;: ' . date('d M Y H:i') . '<br><br>'
                . 'Silakan menghubungi bagian Admin OHS untuk pengambilan stiker fisik.<br><br>'
                . 'Terima kasih,<br><b>TACTIC System</b>'
        );
        @$this->email->send();
    }
}
