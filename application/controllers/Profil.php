<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controller Profil
 * 
 * Pengelolaan profil mandiri pengguna (Self-Service Profile Management).
 * Menyediakan fitur:
 * - Menampilkan informasi profil dan role pengguna yang login
 * - Pembaruan data pribadi (Nama, Email, Jabatan, Nomor HP, Departemen)
 * - Pengunggahan foto profil mandiri
 * - Perubahan password akun (dengan verifikasi password lama & otentikasi dual LDAP/Lokal)
 */
class Profil extends CI_Controller
{
    /**
     * Konstruktor Controller Profil
     * Memuat model User_model, library session & upload, helper, serta memverifikasi login pengguna.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model', 'user_model');
        $this->load->library(['session', 'form_validation', 'upload']);
        $this->load->helper(['url', 'form']);

        if (!$this->session->userdata('id_user')) {
            redirect('auth/login');
        }
    }

    /**
     * Halaman Profil Saya
     * 
     * @return void Render view profil/index
     */
    public function index()
    {
        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");

        $id_user = (int) $this->session->userdata('id_user');
        $user    = $this->user_model->get_by_id($id_user);

        $data['title']  = 'Profil Saya';
        $data['user']   = $this->session->userdata();
        $data['profil'] = $user;
        $data['roles']  = $this->user_model->get_all_roles();

        $this->load->view('templates/header',  $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('profil/index',      $data);
        $this->load->view('templates/footer',  $data);
    }

    /**
     * Endpoint AJAX Pembaruan Data Profil Pribadi.
     * 
     * @return void Response JSON status update
     */
    public function update()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id_user    = (int) $this->session->userdata('id_user');
        $nama       = trim($this->input->post('nama'));
        $email      = trim($this->input->post('email'));
        $jabatan    = trim($this->input->post('jabatan'));
        $no_hp      = trim($this->input->post('no_hp'));
        $departemen = trim($this->input->post('departemen'));

        if (!$nama || !$email) {
            echo json_encode(['status' => 'error', 'message' => 'Nama dan email wajib diisi.', 'csrf_hash' => $this->security->get_csrf_hash()]);
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'message' => 'Format email tidak valid.', 'csrf_hash' => $this->security->get_csrf_hash()]);
            return;
        }
        if ($this->user_model->is_email_exists($email, $id_user)) {
            echo json_encode(['status' => 'error', 'message' => 'Email sudah digunakan akun lain.', 'csrf_hash' => $this->security->get_csrf_hash()]);
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
            // Refresh data session lokal
            $this->session->set_userdata('nama', $nama);
            $this->session->set_userdata('email', $email);
        }

        echo json_encode([
            'status'    => $ok ? 'success' : 'error',
            'message'   => $ok ? 'Profil berhasil diperbarui.' : 'Gagal menyimpan profil.',
            'csrf_hash' => $this->security->get_csrf_hash()
        ]);
    }

    /**
     * Endpoint AJAX Pembaruan Foto Profil.
     * 
     * @return void Response JSON status & URL foto baru
     */
    public function update_foto()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id_user = (int) $this->session->userdata('id_user');

        if (empty($_FILES['foto']['name'])) {
            echo json_encode(['status' => 'error', 'message' => 'File foto tidak ditemukan.', 'csrf_hash' => $this->security->get_csrf_hash()]);
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
            echo json_encode(['status' => 'error', 'message' => $this->upload->display_errors('', ''), 'csrf_hash' => $this->security->get_csrf_hash()]);
            return;
        }

        $upload_data = $this->upload->data();
        if (function_exists('strip_image_exif')) {
            strip_image_exif($upload_data['full_path']);
        }

        $file_path = 'uploads/foto_user/' . $upload_data['file_name'];
        $this->user_model->update($id_user, ['foto' => $file_path]);
        $this->session->set_userdata('foto', $file_path);

        echo json_encode([
            'status'    => 'success',
            'message'   => 'Foto profil berhasil diperbarui.',
            'foto_url'  => base_url($file_path),
            'csrf_hash' => $this->security->get_csrf_hash(),
        ]);
    }

    /**
     * Endpoint AJAX Ganti Password Akun Pengguna.
     * Memverifikasi keberadaan password lama via DB lokal atau server LDAP.
     * 
     * @return void Response JSON status ganti password
     */
    public function ganti_password()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id_user = (int) $this->session->userdata('id_user');
        $lama    = $this->input->post('password_lama');
        $baru    = $this->input->post('password_baru');
        $konfirm = $this->input->post('password_konfirm');

        if (!$lama || !$baru || !$konfirm) {
            echo json_encode(['status' => 'error', 'message' => 'Semua field password wajib diisi.', 'csrf_hash' => $this->security->get_csrf_hash()]);
            return;
        }
        if (strlen($baru) < 8) {
            echo json_encode(['status' => 'error', 'message' => 'Password baru minimal 8 karakter.', 'csrf_hash' => $this->security->get_csrf_hash()]);
            return;
        }
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $baru)) {
            echo json_encode(['status' => 'error', 'message' => 'Password baru harus mengandung minimal 1 huruf besar, 1 huruf kecil, dan 1 angka.', 'csrf_hash' => $this->security->get_csrf_hash()]);
            return;
        }
        if ($baru !== $konfirm) {
            echo json_encode(['status' => 'error', 'message' => 'Konfirmasi password tidak cocok.', 'csrf_hash' => $this->security->get_csrf_hash()]);
            return;
        }

        // Verifikasi kesesuaian password lama
        $user = $this->db->select('username, password, auth_source')->where('id_user', $id_user)->get('users')->row();
        $is_old_valid = false;

        if ($user) {
            if (password_verify($lama, $user->password)) {
                $is_old_valid = true;
            } elseif (!empty($user->auth_source) && $user->auth_source === 'ldap') {
                $this->load->library('ldap_auth');
                $ldap_attrs = $this->ldap_auth->authenticate($user->username, $lama);
                if ($ldap_attrs !== false) {
                    $is_old_valid = true;
                }
            }
        }

        if (!$is_old_valid) {
            echo json_encode(['status' => 'error', 'message' => 'Password lama tidak sesuai.', 'csrf_hash' => $this->security->get_csrf_hash()]);
            return;
        }

        // Simpan password baru terenkripsi (BCRYPT)
        $new_hash = password_hash($baru, PASSWORD_BCRYPT);
        $ok = $this->user_model->update($id_user, [
            'password' => $new_hash
        ]);

        echo json_encode([
            'status'    => $ok ? 'success' : 'error',
            'message'   => $ok ? 'Password berhasil diperbarui dan disimpan ke database sistem. Silakan login ulang.' : 'Gagal menyimpan password baru.',
            'csrf_hash' => $this->security->get_csrf_hash()
        ]);
    }
}
