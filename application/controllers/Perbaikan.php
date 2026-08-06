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
        $this->load->library(['session', 'upload']);
        $this->load->helper(['url', 'form']);

        if (!$this->session->userdata('id_user')) {
            redirect('auth/login');
        }
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

        $pengajuan = $this->pengajuan_model->get_detail($id_pengajuan);
        if (!$pengajuan || $pengajuan->status !== 'tidak_lulus_inspeksi') {
            $this->session->set_flashdata('error', 'Pengajuan tidak ditemukan atau tidak dalam status yang tepat.');
            redirect('pengajuan');
        }

        if (!in_array(1, $roles, true) && $pengajuan->id_pemohon != $this->session->userdata('id_user')) {
            $this->session->set_flashdata('error', 'Anda hanya dapat menginput perbaikan untuk pengajuan milik Anda.');
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
                ->select('uc.hasil, uc.keterangan, ci.kriteria, ci.kategori, ci.no_urut')
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
     * Mengunggah file bukti perbaikan dan mengubah status pengajuan ke 'siap_verifikasi'.
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

        $id_pengajuan = (int) $this->input->post('id_pengajuan');
        $id_uji       = (int) $this->input->post('id_uji');
        $catatan      = trim($this->input->post('catatan_perbaikan') ?? '');

        $pengajuan = $this->pengajuan_model->get_detail($id_pengajuan);
        if (!$pengajuan || $pengajuan->status !== 'tidak_lulus_inspeksi') {
            $this->session->set_flashdata('error', 'Status pengajuan tidak valid.');
            redirect('pengajuan');
        }

        if (!$id_pengajuan || !$id_uji) {
            $this->session->set_flashdata('error', 'Data tidak lengkap.');
            redirect('perbaikan/form/' . $id_pengajuan);
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
                'catatan_perbaikan' => $catatan ?: null,
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
                'catatan_perbaikan' => $catatan ?: null,
                'status'            => 'menunggu_verifikasi',
                'created_at'        => date('Y-m-d H:i:s'),
            ]);
            $id_perbaikan = $this->db->insert_id();
        }

        // Upload berkas bukti perbaikan
        $upload_errors = [];
        if (!empty($_FILES['bukti_perbaikan']['name'][0])) {
            $path = FCPATH . 'uploads/perbaikan/' . $id_pengajuan . '/';
            if (!is_dir($path)) mkdir($path, 0755, true);

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
                    'file_name'     => 'bukti_' . $idx . '_' . time(),
                ]);

                if ($this->upload->do_upload('upload_tmp')) {
                    $info = $this->upload->data();
                    $this->db->insert('perbaikan_lampiran', [
                        'id_perbaikan' => $id_perbaikan,
                        'file_path'    => 'uploads/perbaikan/' . $id_pengajuan . '/' . $info['file_name'],
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
                . ($catatan ? ' Catatan: ' . $catatan : ''),
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
                ->where('id_perbaikan', $perbaikan->id_perbaikan)
                ->get('perbaikan_lampiran')->result();
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
