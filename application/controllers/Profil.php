<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Profil extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model', 'user_model');
        $this->load->library(['session', 'form_validation', 'upload']);
        $this->load->helper(['url', 'form']);
        if (!$this->session->userdata('id_user')) redirect('auth/login');
    }

    public function index()
    {
        $id_user = (int) $this->session->userdata('id_user');
        $user    = $this->user_model->get_by_id($id_user);

        $data['title'] = 'Profil Saya';
        $data['user']  = $this->session->userdata();
        $data['profil'] = $user;
        $data['roles'] = $this->user_model->get_all_roles();

        $this->load->view('templates/header',  $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('profil/index',      $data);
        $this->load->view('templates/footer',  $data);
    }

    // ── AJAX: update profil ───────────────────────────────────
    public function update()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $id_user = (int) $this->session->userdata('id_user');

        $nama       = trim($this->input->post('nama'));
        $email      = trim($this->input->post('email'));
        $jabatan    = trim($this->input->post('jabatan'));
        $no_hp      = trim($this->input->post('no_hp'));
        $departemen = trim($this->input->post('departemen'));

        if (!$nama || !$email) {
            echo json_encode(['status' => 'error', 'message' => 'Nama dan email wajib diisi.']);
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'message' => 'Format email tidak valid.']);
            return;
        }
        if ($this->user_model->is_email_exists($email, $id_user)) {
            echo json_encode(['status' => 'error', 'message' => 'Email sudah digunakan akun lain.']);
            return;
        }

        $payload = [
            'nama'       => $nama,
            'email'      => $email,
            'jabatan'    => $jabatan ?: null,
            'no_hp'      => $no_hp ?: null,
            'departemen' => $departemen ?: null,
        ];

        $ok = $this->user_model->update($id_user, $payload);
        if ($ok) {
            // Refresh session
            $this->session->set_userdata('nama', $nama);
            $this->session->set_userdata('email', $email);
        }
        echo json_encode($ok
            ? ['status' => 'success', 'message' => 'Profil berhasil diperbarui.']
            : ['status' => 'error', 'message' => 'Gagal menyimpan.']);
    }

    // ── AJAX: update foto ─────────────────────────────────────
    public function update_foto()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $id_user = (int) $this->session->userdata('id_user');

        if (empty($_FILES['foto']['name'])) {
            echo json_encode(['status' => 'error', 'message' => 'File foto tidak ditemukan.']);
            return;
        }

        $path = FCPATH . 'uploads/foto_user/';
        if (!is_dir($path)) mkdir($path, 0755, true);

        $this->upload->initialize([
            'upload_path'   => $path,
            'allowed_types' => 'jpg|jpeg|png|webp',
            'max_size'      => 2048,
            'file_name'     => 'user_' . $id_user . '_' . time(),
            'overwrite'     => false,
        ]);

        if (!$this->upload->do_upload('foto')) {
            echo json_encode(['status' => 'error', 'message' => $this->upload->display_errors('', '')]);
            return;
        }

        $file_path = 'uploads/foto_user/' . $this->upload->data('file_name');
        $this->user_model->update($id_user, ['foto' => $file_path]);
        $this->session->set_userdata('foto', $file_path);

        echo json_encode([
            'status'  => 'success',
            'message' => 'Foto profil berhasil diperbarui.',
            'foto_url' => base_url($file_path),
        ]);
    }

    // ── AJAX: ganti password ──────────────────────────────────
    public function ganti_password()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $id_user  = (int) $this->session->userdata('id_user');
        $lama     = $this->input->post('password_lama');
        $baru     = $this->input->post('password_baru');
        $konfirm  = $this->input->post('password_konfirm');

        if (!$lama || !$baru || !$konfirm) {
            echo json_encode(['status' => 'error', 'message' => 'Semua field password wajib diisi.']);
            return;
        }
        if (strlen($baru) < 6) {
            echo json_encode(['status' => 'error', 'message' => 'Password baru minimal 6 karakter.']);
            return;
        }
        if ($baru !== $konfirm) {
            echo json_encode(['status' => 'error', 'message' => 'Konfirmasi password tidak cocok.']);
            return;
        }

        // Verifikasi password lama
        $user = $this->db->select('password')->where('id_user', $id_user)->get('users')->row();
        if (!$user || !password_verify($lama, $user->password)) {
            echo json_encode(['status' => 'error', 'message' => 'Password lama tidak sesuai.']);
            return;
        }

        $ok = $this->user_model->update($id_user, ['password' => password_hash($baru, PASSWORD_BCRYPT)]);
        echo json_encode($ok
            ? ['status' => 'success', 'message' => 'Password berhasil diubah. Silakan login ulang.']
            : ['status' => 'error', 'message' => 'Gagal mengubah password.']);
    }
}
