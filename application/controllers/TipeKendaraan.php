<?php

/**
 * Controller TipeKendaraan
 * 
 * Pengelolaan master data tipe kendaraan/unit (Light Vehicle, Dump Truck, Excavator, dll).
 * Menangani:
 * - Menampilkan daftar tipe kendaraan dan metadata dokumen teknis
 * - Menambah dan mengedit tipe kendaraan, penandaan status alat berat, dan nomor revisi dokumen
 * - Mengubah status aktif/non-aktif tipe kendaraan
 * - Menghapus tipe kendaraan yang tidak terikat dengan data kendaraan aktif
 */
defined('BASEPATH') or exit('No direct script access allowed');

class TipeKendaraan extends CI_Controller
{
    /**
     * Konstruktor Controller TipeKendaraan
     * Memuat library, helper, dan memverifikasi hak akses pengguna (Role 1 & 5).
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['url', 'form']);

        if (!$this->session->userdata('id_user')) {
            redirect('auth/login');
        }

        $roles = $this->_roles();
        if (!$this->_has([1, 5], $roles)) {
            $this->session->set_flashdata('error', 'Akses ditolak.');
            redirect('dashboard');
        }

        $this->_check_schema_alat_berat();
    }

    /**
     * Helper privat memastikan keberadaan kolom is_alat_berat pada tabel tipe_kendaraan.
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
     * Halaman Utama Master Tipe Kendaraan
     * 
     * @return void Render view tipekendaraan/index
     */
    public function index()
    {
        $data = [
            'title' => 'Master Tipe Kendaraan',
            'user'  => $this->session->userdata(),
        ];
        $this->load->view('templates/header',    $data);
        $this->load->view('templates/sidebar',   $data);
        $this->load->view('tipekendaraan/index', $data);
        $this->load->view('templates/footer',    $data);
    }

    /**
     * Endpoint AJAX DataTables untuk mengambil daftar tipe kendaraan beserta jumlah unit terikat.
     * 
     * @return void Response JSON DataTables + CSRF token
     */
    public function get_data()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $rows = $this->db
            ->select('t.*,
                (SELECT COUNT(*) FROM kendaraan k
                    WHERE k.id_tipe_kendaraan = t.id_tipe_kendaraan AND k.deleted_at IS NULL) AS total_kendaraan,
                (SELECT COUNT(*) FROM checklist_template ct
                    WHERE ct.id_tipe_kendaraan = t.id_tipe_kendaraan) AS total_template,
                (SELECT COUNT(*) FROM mekanik_tipe_kendaraan mtk
                    WHERE mtk.id_tipe_kendaraan = t.id_tipe_kendaraan) AS total_mekanik')
            ->from('tipe_kendaraan t')
            ->where('t.deleted_at IS NULL')
            ->order_by('t.id_tipe_kendaraan', 'ASC')
            ->get()->result();

        $data = [];
        foreach ($rows as $r) {
            $badge_status = $r->is_active
                ? '<span class="badge bg-success">Aktif</span>'
                : '<span class="badge bg-secondary">Nonaktif</span>';

            $badge_doc = !empty($r->doc_no)
                ? '<span class="badge bg-info text-dark font-monospace">' . html_escape($r->doc_no) . '</span>'
                : '<span class="text-muted small">—</span>';

            $badge_alat_berat = !empty($r->is_alat_berat)
                ? ' <span class="badge bg-warning text-dark ms-1" title="Alat Berat — Wajib Upload Sertifikasi"><i class="bi bi-truck-flatbed me-1"></i>Alat Berat</span>'
                : '';

            $btn_edit = '<button class="btn btn-sm btn-outline-primary py-0 btn-edit"
                data-id="'          . $r->id_tipe_kendaraan . '"
                data-nama="'        . html_escape($r->nama_tipe)     . '"
                data-kode="'        . html_escape($r->kode_tipe ?? '') . '"
                data-isalatberat="' . ($r->is_alat_berat ?? 0) . '"
                data-docno="'       . html_escape($r->doc_no      ?? '') . '"
                data-titleid="'     . html_escape($r->title_id    ?? '') . '"
                data-titleen="'     . html_escape($r->title_en    ?? '') . '"
                data-docnameid="'   . html_escape($r->doc_name_id ?? '') . '"
                data-docnameen="'   . html_escape($r->doc_name_en ?? '') . '"
                data-tglterbit="'   . ($r->tgl_terbit ?? '') . '"
                data-tglreview="'   . ($r->tgl_review ?? '') . '"
                data-norevisi="'    . html_escape($r->no_revisi   ?? '01') . '"
                title="Edit"><i class="bi bi-pencil"></i></button>';

            $btn_toggle = $r->is_active
                ? '<button class="btn btn-sm btn-outline-warning py-0 btn-toggle"
                    data-id="'     . $r->id_tipe_kendaraan . '"
                    data-active="1"
                    title="Nonaktifkan"><i class="bi bi-eye-slash"></i></button>'
                : '<button class="btn btn-sm btn-outline-success py-0 btn-toggle"
                    data-id="'     . $r->id_tipe_kendaraan . '"
                    data-active="0"
                    title="Aktifkan"><i class="bi bi-eye"></i></button>';

            $btn_del = ($r->total_kendaraan == 0 && $r->total_template == 0)
                ? '<button class="btn btn-sm btn-outline-danger py-0 btn-delete"
                    data-id="'   . $r->id_tipe_kendaraan . '"
                    data-nama="' . html_escape($r->nama_tipe) . '"
                    title="Hapus"><i class="bi bi-trash"></i></button>'
                : '';

            $data[] = [
                'id'                => $r->id_tipe_kendaraan,
                'id_tipe_kendaraan' => $r->id_tipe_kendaraan,
                'nama_tipe'         => '<strong>' . html_escape($r->nama_tipe) . '</strong>' . $badge_alat_berat,
                'kode_tipe'         => '<span class="badge bg-light text-dark font-monospace border">' . html_escape($r->kode_tipe ?? '-') . '</span>',
                'doc_no'            => $badge_doc,
                'no_revisi'         => html_escape($r->no_revisi ?? '01'),
                'status'            => $badge_status,
                'is_active'         => $badge_status,
                'total_kendaraan'   => '<span class="badge bg-light text-dark border">' . $r->total_kendaraan . ' unit</span>',
                'total_template'    => '<span class="badge bg-light text-dark border">' . $r->total_template . ' template</span>',
                'total_mekanik'     => '<span class="badge bg-light text-dark border">' . $r->total_mekanik . ' orang</span>',
                'aksi'              => '<div class="d-flex gap-1 justify-content-center text-nowrap">' . $btn_edit . $btn_toggle . $btn_del . '</div>',
            ];
        }

        echo json_encode(['data' => $data, 'csrf_hash' => $this->security->get_csrf_hash()]);
    }

    /**
     * Endpoint AJAX Simpan (Insert / Update) Master Tipe Kendaraan.
     * 
     * @return void Response JSON status simpan
     */
    public function save()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id            = (int) $this->input->post('id_tipe_kendaraan');
        $nama          = trim($this->input->post('nama_tipe'));
        $kode          = strtoupper(trim($this->input->post('kode_tipe')));
        $is_alat_berat = (int) $this->input->post('is_alat_berat');

        $doc_no      = trim($this->input->post('doc_no'));
        $title_id    = trim($this->input->post('title_id'));
        $title_en    = trim($this->input->post('title_en'));
        $doc_name_id = trim($this->input->post('doc_name_id'));
        $doc_name_en = trim($this->input->post('doc_name_en'));
        $tgl_terbit  = trim($this->input->post('tgl_terbit'));
        $tgl_review  = trim($this->input->post('tgl_review'));
        $no_revisi   = trim($this->input->post('no_revisi')) ?: '01';

        if (empty($nama)) {
            echo json_encode(['status' => 'error', 'message' => 'Nama tipe kendaraan wajib diisi.', 'csrf_hash' => $this->security->get_csrf_hash()]);
            return;
        }

        // Cek keunikan nama tipe
        $this->db->where('nama_tipe', $nama);
        if ($id) $this->db->where('id_tipe_kendaraan !=', $id);
        if ($this->db->count_all_results('tipe_kendaraan') > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Nama tipe kendaraan sudah digunakan.', 'csrf_hash' => $this->security->get_csrf_hash()]);
            return;
        }

        $payload = [
            'nama_tipe'     => $nama,
            'kode_tipe'     => $kode ?: null,
            'is_alat_berat' => $is_alat_berat,
            'doc_no'        => $doc_no      ?: null,
            'title_id'      => $title_id    ?: null,
            'title_en'      => $title_en    ?: null,
            'doc_name_id'   => $doc_name_id ?: null,
            'doc_name_en'   => $doc_name_en ?: null,
            'tgl_terbit'    => $tgl_terbit  ?: null,
            'tgl_review'    => $tgl_review  ?: null,
            'no_revisi'     => $no_revisi   ?: '01',
        ];

        if ($id) {
            $this->db->where('id_tipe_kendaraan', $id)->update('tipe_kendaraan', $payload);
            $msg = 'Tipe kendaraan berhasil diperbarui.';
        } else {
            $payload['is_active'] = 1;
            $this->db->insert('tipe_kendaraan', $payload);
            $msg = 'Tipe kendaraan baru berhasil ditambahkan.';
        }

        echo json_encode(['status' => 'success', 'message' => $msg, 'csrf_hash' => $this->security->get_csrf_hash()]);
    }

    /**
     * Endpoint AJAX Toggle Status Aktif/Non-aktif Tipe Kendaraan.
     * 
     * @return void Response JSON status toggle
     */
    public function toggle()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id  = (int) $this->input->post('id');
        $row = $this->db->where('id_tipe_kendaraan', $id)->get('tipe_kendaraan')->row();

        if (!$row) {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan.', 'csrf_hash' => $this->security->get_csrf_hash()]);
            return;
        }

        $new_val = $row->is_active ? 0 : 1;
        $this->db->where('id_tipe_kendaraan', $id)->update('tipe_kendaraan', ['is_active' => $new_val]);

        echo json_encode(['status' => 'success', 'csrf_hash' => $this->security->get_csrf_hash()]);
    }

    /**
     * Endpoint AJAX Hapus Tipe Kendaraan.
     * Memeriksa keterikatan data kendaraan dan template sebelum menghapus.
     * 
     * @return void Response JSON status hapus
     */
    public function delete()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id   = (int) $this->input->post('id');
        $cek1 = $this->db->where('id_tipe_kendaraan', $id)->where('deleted_at IS NULL')->count_all_results('kendaraan');
        $cek2 = $this->db->where('id_tipe_kendaraan', $id)->count_all_results('checklist_template');

        if ($cek1 > 0 || $cek2 > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Tipe kendaraan masih terikat dengan data kendaraan aktif atau template checklist.', 'csrf_hash' => $this->security->get_csrf_hash()]);
            return;
        }

        $this->db->where('id_tipe_kendaraan', $id)->update('tipe_kendaraan', [
            'deleted_at' => date('Y-m-d H:i:s'),
            'deleted_by' => (int) $this->session->userdata('id_user'),
            'is_active'  => 0,
        ]);
        echo json_encode(['status' => 'success', 'message' => 'Tipe kendaraan berhasil dihapus (soft delete).', 'csrf_hash' => $this->security->get_csrf_hash()]);
    }

    /**
     * Endpoint AJAX Pemulihan (Restore) Tipe Kendaraan.
     * 
     * @return void Response JSON status restore
     */
    public function restore()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id = (int) $this->input->post('id');
        $this->db->where('id_tipe_kendaraan', $id)->update('tipe_kendaraan', [
            'deleted_at' => null,
            'deleted_by' => null,
            'is_active'  => 1,
        ]);
        echo json_encode(['status' => 'success', 'message' => 'Tipe kendaraan berhasil dipulihkan.', 'csrf_hash' => $this->security->get_csrf_hash()]);
    }

    /**
     * Endpoint AJAX Ambil Informasi Dokumen Teknis Tipe Kendaraan.
     * 
     * @return void Response JSON info dokumen
     */
    public function get_doc_info()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id  = (int) $this->input->post('id_tipe_kendaraan');
        $row = $this->db
            ->select('id_tipe_kendaraan, nama_tipe, kode_tipe, is_alat_berat,
                  doc_no, title_id, title_en, doc_name_id, doc_name_en,
                  tgl_terbit, tgl_review, no_revisi')
            ->where('id_tipe_kendaraan', $id)
            ->get('tipe_kendaraan')
            ->row();

        if (!$row) {
            echo json_encode(['status' => 'error', 'message' => 'Tipe kendaraan tidak ditemukan.', 'csrf_hash' => $this->security->get_csrf_hash()]);
            return;
        }

        echo json_encode(['status' => 'success', 'data' => $row, 'csrf_hash' => $this->security->get_csrf_hash()]);
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
     * Helper privat memeriksa hak akses pengguna.
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
