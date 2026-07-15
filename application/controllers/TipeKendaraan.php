<?php

/**
 * TipeKendaraan Controller
 * Perubahan v2:
 *   - save() menerima kolom doc_no, title_id, title_en,
 *     doc_name_id, doc_name_en, tgl_terbit, tgl_review, no_revisi
 *   - get_data() expose kolom baru ke DataTable
 *   - get_doc_info() AJAX baru — untuk preview di modal
 */
defined('BASEPATH') or exit('No direct script access allowed');

class TipeKendaraan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['url', 'form']);

        if (!$this->session->userdata('id_user')) redirect('auth/login');

        $roles = $this->_roles();
        if (!$this->_has([1, 5], $roles)) {
            $this->session->set_flashdata('error', 'Akses ditolak.');
            redirect('dashboard');
        }
    }

    // ── INDEX ──────────────────────────────────────────────────────────────────
    public function index()
    {
        $data = [
            'title' => 'Master Tipe Kendaraan',
            'user'  => $this->session->userdata(),
        ];
        $this->load->view('templates/header',     $data);
        $this->load->view('templates/sidebar',    $data);
        $this->load->view('tipekendaraan/index',  $data);
        $this->load->view('templates/footer',     $data);
    }

    // ── AJAX: DataTable ────────────────────────────────────────────────────────
    public function get_data()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $rows = $this->db
            ->select('t.*,
                (SELECT COUNT(*) FROM kendaraan k
                    WHERE k.id_tipe_kendaraan = t.id_tipe_kendaraan) AS total_kendaraan,
                (SELECT COUNT(*) FROM checklist_template ct
                    WHERE ct.id_tipe_kendaraan = t.id_tipe_kendaraan) AS total_template,
                (SELECT COUNT(*) FROM mekanik_tipe_kendaraan mtk
                    WHERE mtk.id_tipe_kendaraan = t.id_tipe_kendaraan) AS total_mekanik')
            ->from('tipe_kendaraan t')
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

            $btn_edit = '<button class="btn btn-sm btn-outline-primary py-0 btn-edit"
                data-id="'    . $r->id_tipe_kendaraan . '"
                data-nama="'  . html_escape($r->nama_tipe)     . '"
                data-kode="'  . html_escape($r->kode_tipe ?? '') . '"
                data-docno="' . html_escape($r->doc_no      ?? '') . '"
                data-titleid="'   . html_escape($r->title_id    ?? '') . '"
                data-titleen="'   . html_escape($r->title_en    ?? '') . '"
                data-docnameid="' . html_escape($r->doc_name_id ?? '') . '"
                data-docnameen="' . html_escape($r->doc_name_en ?? '') . '"
                data-tglterbit="' . ($r->tgl_terbit ?? '') . '"
                data-tglreview="' . ($r->tgl_review ?? '') . '"
                data-norevisi="'  . html_escape($r->no_revisi   ?? '01') . '"
                title="Edit"><i class="bi bi-pencil"></i></button>';

            $btn_toggle = $r->is_active
                ? '<button class="btn btn-sm btn-outline-warning py-0 btn-toggle"
                    data-id="' . $r->id_tipe_kendaraan . '" title="Nonaktifkan">
                    <i class="bi bi-eye-slash"></i></button>'
                : '<button class="btn btn-sm btn-outline-success py-0 btn-toggle"
                    data-id="' . $r->id_tipe_kendaraan . '" title="Aktifkan">
                    <i class="bi bi-eye"></i></button>';

            $btn_delete = '<button class="btn btn-sm btn-outline-danger py-0 btn-delete"
                data-id="' . $r->id_tipe_kendaraan . '"
                data-nama="' . html_escape($r->nama_tipe) . '" title="Hapus">
                <i class="bi bi-trash"></i></button>';

            $data[] = [
                'id'              => $r->id_tipe_kendaraan,
                'nama_tipe'       => html_escape($r->nama_tipe),
                'kode_tipe'       => $r->kode_tipe
                    ? '<span class="badge bg-secondary font-monospace">' . html_escape($r->kode_tipe) . '</span>'
                    : '<span class="text-muted">—</span>',
                'doc_no'          => $badge_doc,
                'status'          => $badge_status,
                'total_kendaraan' => '<span class="badge bg-primary rounded-pill">'   . $r->total_kendaraan . '</span>',
                'total_template'  => '<span class="badge bg-info rounded-pill">'      . $r->total_template  . '</span>',
                'total_mekanik'   => '<span class="badge bg-secondary rounded-pill">' . $r->total_mekanik   . '</span>',
                'aksi'            => '<div class="d-flex gap-1 justify-content-center">'
                    . $btn_edit . $btn_toggle . $btn_delete
                    . '</div>',
            ];
        }

        echo json_encode(['data' => $data]);
    }

    // ── AJAX: simpan (insert / update) ─────────────────────────────────────────
    public function save()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id         = (int)   $this->input->post('id_tipe_kendaraan');
        $nama_tipe  = trim($this->input->post('nama_tipe')    ?? '');
        $kode_tipe  = strtoupper(trim($this->input->post('kode_tipe') ?? '')) ?: null;

        // ── Kolom dokumen PDF ──────────────────────────────────────────────────
        $doc_no      = trim($this->input->post('doc_no')      ?? '') ?: null;
        $title_id    = trim($this->input->post('title_id')    ?? '') ?: null;
        $title_en    = trim($this->input->post('title_en')    ?? '') ?: null;
        $doc_name_id = trim($this->input->post('doc_name_id') ?? '') ?: null;
        $doc_name_en = trim($this->input->post('doc_name_en') ?? '') ?: null;
        $tgl_terbit  = trim($this->input->post('tgl_terbit')  ?? '') ?: null;
        $tgl_review  = trim($this->input->post('tgl_review')  ?? '') ?: null;
        $no_revisi   = trim($this->input->post('no_revisi')   ?? '') ?: '01';

        // ── Validasi wajib ─────────────────────────────────────────────────────
        if (empty($nama_tipe)) {
            echo json_encode(['status' => 'error', 'message' => 'Nama tipe wajib diisi.']);
            return;
        }
        if (strlen($nama_tipe) > 100) {
            echo json_encode(['status' => 'error', 'message' => 'Nama tipe maksimal 100 karakter.']);
            return;
        }

        // Cek duplikat nama
        $this->db->where('LOWER(nama_tipe) = LOWER(' . $this->db->escape($nama_tipe) . ')', null, false);
        if ($id) $this->db->where('id_tipe_kendaraan !=', $id);
        if ($this->db->count_all_results('tipe_kendaraan') > 0) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Nama tipe <strong>' . html_escape($nama_tipe) . '</strong> sudah terdaftar.'
            ]);
            return;
        }

        // Cek duplikat kode
        if ($kode_tipe) {
            $this->db->where('kode_tipe', $kode_tipe);
            if ($id) $this->db->where('id_tipe_kendaraan !=', $id);
            if ($this->db->count_all_results('tipe_kendaraan') > 0) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Kode tipe <strong>' . html_escape($kode_tipe) . '</strong> sudah dipakai.'
                ]);
                return;
            }
        }

        // Cek duplikat doc_no
        if ($doc_no) {
            $this->db->where('doc_no', $doc_no);
            if ($id) $this->db->where('id_tipe_kendaraan !=', $id);
            if ($this->db->count_all_results('tipe_kendaraan') > 0) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'No. Dokumen <strong>' . html_escape($doc_no) . '</strong> sudah dipakai tipe lain.'
                ]);
                return;
            }
        }

        $payload = [
            'nama_tipe'   => $nama_tipe,
            'kode_tipe'   => $kode_tipe,
            'doc_no'      => $doc_no,
            'title_id'    => $title_id,
            'title_en'    => $title_en,
            'doc_name_id' => $doc_name_id,
            'doc_name_en' => $doc_name_en,
            'tgl_terbit'  => $tgl_terbit,
            'tgl_review'  => $tgl_review,
            'no_revisi'   => $no_revisi,
        ];

        if ($id) {
            $this->db->where('id_tipe_kendaraan', $id)->update('tipe_kendaraan', $payload);
            echo json_encode([
                'status' => 'success',
                'message' => 'Tipe <strong>' . html_escape($nama_tipe) . '</strong> berhasil diperbarui.'
            ]);
        } else {
            $payload['is_active'] = 1;
            $this->db->insert('tipe_kendaraan', $payload);
            echo json_encode([
                'status' => 'success',
                'message' => 'Tipe <strong>' . html_escape($nama_tipe) . '</strong> berhasil ditambahkan.'
            ]);
        }
    }

    // ── AJAX: toggle aktif/nonaktif ───────────────────────────────────────────
    public function toggle()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id  = (int) $this->input->post('id');
        $row = $this->db->select('is_active, nama_tipe')
            ->where('id_tipe_kendaraan', $id)->get('tipe_kendaraan')->row();
        if (!$row) {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
            return;
        }

        $this->db->where('id_tipe_kendaraan', $id)
            ->update('tipe_kendaraan', ['is_active' => $row->is_active ? 0 : 1]);
        $label = $row->is_active ? 'dinonaktifkan' : 'diaktifkan';
        echo json_encode([
            'status' => 'success',
            'message' => 'Tipe <strong>' . html_escape($row->nama_tipe) . '</strong> berhasil ' . $label . '.'
        ]);
    }

    // ── AJAX: delete ───────────────────────────────────────────────────────────
    public function delete()
    {
        if (!$this->input->is_ajax_request()) show_404();

        if (!$this->_has([1], $this->_roles())) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Hanya Super Admin yang dapat menghapus tipe kendaraan.'
            ]);
            return;
        }

        $id = (int) $this->input->post('id');

        $in_use = $this->db->query("
            SELECT 1 FROM kendaraan WHERE id_tipe_kendaraan = ?
            UNION ALL
            SELECT 1 FROM checklist_template WHERE id_tipe_kendaraan = ?
            UNION ALL
            SELECT 1 FROM mekanik_tipe_kendaraan WHERE id_tipe_kendaraan = ?
            LIMIT 1
        ", [$id, $id, $id])->num_rows() > 0;

        if ($in_use) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Tipe tidak dapat dihapus karena masih dipakai oleh kendaraan, template checklist, atau mekanik.'
            ]);
            return;
        }

        $this->db->where('id_tipe_kendaraan', $id)->delete('tipe_kendaraan');
        echo json_encode(['status' => 'success', 'message' => 'Tipe kendaraan berhasil dihapus.']);
    }

    // ── AJAX: dropdown ────────────────────────────────────────────────────────
    public function get_dropdown()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $rows = $this->db->select('id_tipe_kendaraan, nama_tipe, kode_tipe')
            ->where('is_active', 1)->order_by('nama_tipe', 'ASC')
            ->get('tipe_kendaraan')->result();
        echo json_encode(['status' => 'success', 'data' => $rows]);
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
