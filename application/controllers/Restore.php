<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controller Restore
 * 
 * Pusat Pemulihan Data (Recycle Bin / Restore Center) untuk memulihkan
 * data yang telah di-soft-delete (Pengajuan, Kendaraan, Users, Mekanik, Tipe Kendaraan, Checklist Items).
 * Khusus untuk Administrator / Super Admin (Role 1) dan Admin OHS (Role 5).
 */
class Restore extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['url', 'form']);
        $this->load->model([
            'pengajuan_model',
            'kendaraan_model',
            'user_model',
            'mekanik_model',
            'checklist_model',
        ]);

        if (!$this->session->userdata('id_user')) {
            redirect('auth/login');
        }

        $roles = $this->_user_roles();
        if (!$this->_has_role([1, 5, 7], $roles)) {
            $this->session->set_flashdata('error', 'Akses ditolak. Halaman pemulihan data hanya untuk pengguna berwenang.');
            redirect('dashboard');
        }
    }

    /**
     * Halaman Utama Pusat Pemulihan Data
     */
    public function index()
    {
        $roles   = $this->_user_roles();
        $id_user = (int) $this->session->userdata('id_user');
        $isAdminDeptOnly = $this->_has_role([7], $roles) && !$this->_has_role([1, 5], $roles);

        // Hitung total data terhapus per kategori
        $pengajuan_query = $this->db->where('deleted_at IS NOT NULL');
        if ($isAdminDeptOnly) {
            $pengajuan_query->where('id_pemohon', $id_user);
        }
        $cnt_pengajuan = $pengajuan_query->count_all_results('pengajuan_uji');

        $counts = [
            'pengajuan'      => $cnt_pengajuan,
            'kendaraan'      => $isAdminDeptOnly ? 0 : $this->db->where('deleted_at IS NOT NULL')->count_all_results('kendaraan'),
            'users'          => $isAdminDeptOnly ? 0 : $this->db->where('deleted_at IS NOT NULL')->count_all_results('users'),
            'mekanik'        => $isAdminDeptOnly ? 0 : $this->db->where('deleted_at IS NOT NULL')->count_all_results('mekanik_master'),
            'tipe_kendaraan' => $isAdminDeptOnly ? 0 : $this->db->where('deleted_at IS NOT NULL')->count_all_results('tipe_kendaraan'),
            'checklist_item' => $isAdminDeptOnly ? 0 : $this->db->where('deleted_at IS NOT NULL')->count_all_results('checklist_item'),
        ];

        $data = [
            'title'           => 'Pusat Pemulihan Data (Recycle Bin)',
            'user'            => $this->session->userdata(),
            'counts'          => $counts,
            'isAdminDeptOnly' => $isAdminDeptOnly,
            'roles'           => $roles,
        ];

        $this->load->view('templates/header',  $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('restore/index',     $data);
        $this->load->view('templates/footer',  $data);
    }

    /**
     * Endpoint AJAX DataTables untuk masing-masing tab data terhapus
     */
    public function get_data($type = 'pengajuan')
    {
        if (!$this->input->is_ajax_request()) show_404();

        $roles   = $this->_user_roles();
        $id_user = (int) $this->session->userdata('id_user');
        $isAdminDeptOnly = $this->_has_role([7], $roles) && !$this->_has_role([1, 5], $roles);

        // Jika Admin Dept hanya boleh melihat pengajuan
        if ($isAdminDeptOnly && $type !== 'pengajuan') {
            echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.', 'data' => []]);
            return;
        }

        $data = [];

        switch ($type) {
            case 'pengajuan':
                $this->db
                    ->select('pu.*, k.no_polisi, k.nomor_unit, k.merk, k.tipe, t.nama_tipe, u.nama AS nama_pemohon, u_del.nama AS deleted_by_nama')
                    ->from('pengajuan_uji pu')
                    ->join('kendaraan k', 'k.id_kendaraan = pu.id_kendaraan', 'left')
                    ->join('tipe_kendaraan t', 't.id_tipe_kendaraan = k.id_tipe_kendaraan', 'left')
                    ->join('users u', 'u.id_user = pu.id_pemohon', 'left')
                    ->join('users u_del', 'u_del.id_user = pu.deleted_by', 'left')
                    ->where('pu.deleted_at IS NOT NULL');

                if ($isAdminDeptOnly) {
                    $this->db->where('pu.id_pemohon', $id_user);
                }

                $rows = $this->db->order_by('pu.deleted_at', 'DESC')->get()->result();

                foreach ($rows as $i => $r) {
                    $data[] = [
                        'no'          => $i + 1,
                        'id'          => $r->id_pengajuan,
                        'kode'        => '#PU-' . str_pad($r->id_pengajuan, 4, '0', STR_PAD_LEFT),
                        'identitas'   => '<strong>' . html_escape($r->no_polisi ?: 'N/A') . '</strong>' . ($r->nomor_unit ? ' (' . html_escape($r->nomor_unit) . ')' : ''),
                        'keterangan'  => html_escape($r->nama_tipe ?: '—') . ' — ' . html_escape($r->merk . ' ' . $r->tipe) . '<br><small class="text-muted">Pemohon: ' . html_escape($r->nama_pemohon ?: '—') . '</small>',
                        'status'      => '<span class="badge bg-secondary">' . html_escape($r->status) . '</span>',
                        'deleted_at'  => date('d/m/Y H:i', strtotime($r->deleted_at)),
                        'deleted_by'  => html_escape($r->deleted_by_nama ?: 'System / Admin'),
                        'type'        => 'pengajuan',
                    ];
                }
                break;

            case 'kendaraan':
                $rows = $this->db
                    ->select('k.*, t.nama_tipe, u_del.nama AS deleted_by_nama')
                    ->from('kendaraan k')
                    ->join('tipe_kendaraan t', 't.id_tipe_kendaraan = k.id_tipe_kendaraan', 'left')
                    ->join('users u_del', 'u_del.id_user = k.deleted_by', 'left')
                    ->where('k.deleted_at IS NOT NULL')
                    ->order_by('k.deleted_at', 'DESC')
                    ->get()->result();

                foreach ($rows as $i => $r) {
                    $data[] = [
                        'no'          => $i + 1,
                        'id'          => $r->id_kendaraan,
                        'kode'        => html_escape($r->no_polisi),
                        'identitas'   => '<strong>' . html_escape($r->no_polisi) . '</strong>' . ($r->nomor_unit ? ' (' . html_escape($r->nomor_unit) . ')' : ''),
                        'keterangan'  => html_escape($r->nama_tipe ?: '—') . ' — ' . html_escape($r->merk . ' ' . $r->tipe) . '<br><small class="text-muted">Perusahaan: ' . html_escape($r->perusahaan ?: '—') . '</small>',
                        'status'      => '<span class="badge bg-danger">Terhapus</span>',
                        'deleted_at'  => date('d/m/Y H:i', strtotime($r->deleted_at)),
                        'deleted_by'  => html_escape($r->deleted_by_nama ?: 'System / Admin'),
                        'type'        => 'kendaraan',
                    ];
                }
                break;

            case 'users':
                $rows = $this->db
                    ->select('u.*, u_del.nama AS deleted_by_nama')
                    ->from('users u')
                    ->join('users u_del', 'u_del.id_user = u.deleted_by', 'left')
                    ->where('u.deleted_at IS NOT NULL')
                    ->order_by('u.deleted_at', 'DESC')
                    ->get()->result();

                foreach ($rows as $i => $r) {
                    $data[] = [
                        'no'          => $i + 1,
                        'id'          => $r->id_user,
                        'kode'        => html_escape($r->username),
                        'identitas'   => '<strong>' . html_escape($r->nama) . '</strong> (' . html_escape($r->username) . ')',
                        'keterangan'  => html_escape($r->email) . '<br><small class="text-muted">Dept: ' . html_escape($r->departemen ?: '—') . '</small>',
                        'status'      => '<span class="badge bg-danger">Nonaktif</span>',
                        'deleted_at'  => date('d/m/Y H:i', strtotime($r->deleted_at)),
                        'deleted_by'  => html_escape($r->deleted_by_nama ?: 'System / Admin'),
                        'type'        => 'users',
                    ];
                }
                break;

            case 'mekanik':
                $rows = $this->db
                    ->select('m.*, u_del.nama AS deleted_by_nama')
                    ->from('mekanik_master m')
                    ->join('users u_del', 'u_del.id_user = m.deleted_by', 'left')
                    ->where('m.deleted_at IS NOT NULL')
                    ->order_by('m.deleted_at', 'DESC')
                    ->get()->result();

                foreach ($rows as $i => $r) {
                    $data[] = [
                        'no'          => $i + 1,
                        'id'          => $r->id_mekanik,
                        'kode'        => 'MEK-' . $r->id_mekanik,
                        'identitas'   => '<strong>' . html_escape($r->nama) . '</strong>',
                        'keterangan'  => 'Perusahaan: ' . html_escape($r->perusahaan ?: '—') . '<br><small class="text-muted">Kontak: ' . html_escape($r->no_hp ?: '—') . '</small>',
                        'status'      => '<span class="badge bg-danger">Nonaktif</span>',
                        'deleted_at'  => date('d/m/Y H:i', strtotime($r->deleted_at)),
                        'deleted_by'  => html_escape($r->deleted_by_nama ?: 'System / Admin'),
                        'type'        => 'mekanik',
                    ];
                }
                break;

            case 'tipe_kendaraan':
                $rows = $this->db
                    ->select('t.*, u_del.nama AS deleted_by_nama')
                    ->from('tipe_kendaraan t')
                    ->join('users u_del', 'u_del.id_user = t.deleted_by', 'left')
                    ->where('t.deleted_at IS NOT NULL')
                    ->order_by('t.deleted_at', 'DESC')
                    ->get()->result();

                foreach ($rows as $i => $r) {
                    $data[] = [
                        'no'          => $i + 1,
                        'id'          => $r->id_tipe_kendaraan,
                        'kode'        => html_escape($r->kode_tipe ?: '—'),
                        'identitas'   => '<strong>' . html_escape($r->nama_tipe) . '</strong>',
                        'keterangan'  => 'Kode: ' . html_escape($r->kode_tipe ?: '—') . ' | Doc No: ' . html_escape($r->doc_no ?: '—'),
                        'status'      => '<span class="badge bg-danger">Nonaktif</span>',
                        'deleted_at'  => date('d/m/Y H:i', strtotime($r->deleted_at)),
                        'deleted_by'  => html_escape($r->deleted_by_nama ?: 'System / Admin'),
                        'type'        => 'tipe_kendaraan',
                    ];
                }
                break;

            case 'checklist_item':
                $rows = $this->db
                    ->select('ci.*, ct.nama_template, u_del.nama AS deleted_by_nama')
                    ->from('checklist_item ci')
                    ->join('checklist_template ct', 'ct.id_template = ci.id_template', 'left')
                    ->join('users u_del', 'u_del.id_user = ci.deleted_by', 'left')
                    ->where('ci.deleted_at IS NOT NULL')
                    ->order_by('ci.deleted_at', 'DESC')
                    ->get()->result();

                foreach ($rows as $i => $r) {
                    $data[] = [
                        'no'          => $i + 1,
                        'id'          => $r->id_item,
                        'kode'        => 'ITEM #' . $r->no_urut,
                        'identitas'   => '<strong>[' . html_escape($r->kategori) . ' #' . $r->no_urut . ']</strong> ' . html_escape(substr($r->kriteria, 0, 50)) . '...',
                        'keterangan'  => 'Template: ' . html_escape($r->nama_template ?: '—'),
                        'status'      => '<span class="badge bg-danger">' . html_escape($r->kategori) . '</span>',
                        'deleted_at'  => date('d/m/Y H:i', strtotime($r->deleted_at)),
                        'deleted_by'  => html_escape($r->deleted_by_nama ?: 'System / Admin'),
                        'type'        => 'checklist_item',
                    ];
                }
                break;
        }

        echo json_encode([
            'status' => 'success',
            'data'   => $data,
            'csrf_hash' => $this->security->get_csrf_hash(),
        ]);
    }

    /**
     * Helper privat mengambil ID role pengguna
     */
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
            if (in_array((int)$r, $user_roles, true)) return true;
        }
        return false;
    }
}
