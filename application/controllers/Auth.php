<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controller Auth
 * 
 * Pengelolaan proses masuk (Login) dan keluar (Logout) pengguna dari sistem.
 * Fitur Keamanan:
 * - Proteksi brute-force (Maksimal 5x percobaan gagal -> Lockout 5 menit)
 * - Otentikasi dual (Lokal DB & LDAP JIT Provisioning)
 * - Pencatatan Audit Log saat Login & Logout
 */
class Auth extends CI_Controller
{
    /**
     * Konstruktor Controller Auth
     * Memuat model, library validasi form & session, serta helper URL.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model');
        $this->load->library(['form_validation', 'session']);
        $this->load->helper(['url', 'form']);
    }

    /**
     * Halaman Login Utama
     * Jika pengguna sudah dalam keadaan login, otomatis dialihkan ke Dashboard.
     * 
     * @return void Render view auth/index
     */
    public function index()
    {
        if ($this->session->userdata('logged_in')) {
            redirect('dashboard');
        }

        // Reset hitungan percobaan login jika sudah melampaui batas waktu lockout 5 menit (300 detik)
        $last_attempt_time = $this->session->userdata('last_attempt_time');
        if ($last_attempt_time && (time() - $last_attempt_time > 300)) {
            $this->session->unset_userdata('login_attempt');
            $this->session->unset_userdata('last_attempt_time');
        }

        $this->load->view('auth/index');
    }

    /**
     * Endpoint AJAX Proses Login Pengguna.
     * Melakukan validasi input, proteksi brute-force, otentikasi password/LDAP, dan inisialisasi session.
     * 
     * @return void Output JSON status otentikasi & URL redirect
     */
    public function login()
    {
        if ($this->input->method() !== 'post') {
            redirect('auth');
            return;
        }

        // Validasi input form (identity = email/username & password)
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('identity', 'Email / Username', 'required');
        $this->form_validation->set_rules('password',  'Password',         'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['status' => 'error', 'message' => validation_errors()]);
            return;
        }

        $identity   = $this->input->post('identity', TRUE);
        $password   = $this->input->post('password', TRUE);
        $ip_address = $this->input->ip_address();

        // Cleanup percobaan login berusia > 1 jam
        $this->Auth_model->clean_old_login_attempts();

        // Cek proteksi brute-force login berbasis DB (identity & ip_address)
        $attempt_row = $this->Auth_model->get_login_attempt($identity, $ip_address);
        $attempt = $attempt_row ? (int) $attempt_row->attempt : 0;
        $last_attempt_time = ($attempt_row && $attempt_row->last_attempt_time) ? strtotime($attempt_row->last_attempt_time) : null;

        if ($attempt >= 5 && $last_attempt_time && (time() - $last_attempt_time < 300)) {
            $sisa_detik = 300 - (time() - $last_attempt_time);
            $sisa_menit = ceil($sisa_detik / 60);
            echo json_encode(['status' => 'error', 'message' => "Terlalu banyak percobaan gagal. Silakan tunggu {$sisa_menit} menit lagi sebelum mencoba kembali."]);
            return;
        } elseif ($attempt >= 5) {
            // Reset batas lockout jika sudah lewat 5 menit
            $attempt = 0;
            $this->Auth_model->reset_login_attempts($identity, $ip_address);
        }

        // Cari user di database lokal berdasarkan Email atau Username
        $user = filter_var($identity, FILTER_VALIDATE_EMAIL)
            ? $this->Auth_model->check_login_by_email($identity)
            : $this->Auth_model->check_login_by_username($identity);

        $authenticated = false;

        // Jika pengguna belum terdaftar di DB lokal, uji otentikasi via LDAP (JIT Provisioning)
        if (!$user) {
            $this->load->library('ldap_auth');
            $ldap_attrs = $this->ldap_auth->authenticate($identity, $password);
            if ($ldap_attrs !== false) {
                $user = $this->Auth_model->auto_provision_ldap_user($identity, $ldap_attrs);
                if ($user) {
                    $authenticated = true;
                }
            }
        } else {
            // Pengguna ada di DB lokal — verifikasi berdasarkan auth_source (LDAP atau lokal)
            $auth_source = !empty($user->auth_source) ? $user->auth_source : 'local';

            if ($auth_source === 'ldap') {
                $this->load->library('ldap_auth');
                $ldap_attrs = $this->ldap_auth->authenticate($user->username ?? $identity, $password);
                if ($ldap_attrs !== false) {
                    $authenticated = true;
                    if (isset($ldap_attrs['dn']) && isset($user->ldap_dn) && $user->ldap_dn !== $ldap_attrs['dn']) {
                        if ($this->db->field_exists('ldap_dn', 'users')) {
                            $this->db->where('id_user', $user->id_user)->update('users', ['ldap_dn' => $ldap_attrs['dn']]);
                        }
                    }
                } else {
                    // Fallback jika password lokal telah diperbarui
                    $authenticated = password_verify($password, $user->password);
                }
            } else {
                $authenticated = password_verify($password, $user->password);
            }
        }

        // Penanganan kegagalan verifikasi
        if (!$user || !$authenticated) {
            $this->Auth_model->record_failed_attempt($identity, $ip_address);
            echo json_encode(['status' => 'error', 'message' => 'Username / Password salah!']);
            return;
        }

        // Cek status keaktifan akun
        if (!$user->is_active) {
            echo json_encode(['status' => 'error', 'message' => 'Akun Anda telah dinonaktifkan.']);
            return;
        }

        // Ambil daftar seluruh role pengguna (mencegah N+1 query)
        $roles_raw = $this->db
            ->select('r.id_role, r.nama_role')
            ->from('user_roles ur')
            ->join('roles r', 'r.id_role = ur.id_role')
            ->where('ur.id_user', $user->id_user)
            ->get()->result();

        if (empty($roles_raw)) {
            if ((int)$user->id_role === 0) {
                $roles_ids   = [];
                $roles_names = ['Menunggu Penetapan Role (LDAP)'];
                $primary_role = 0;
            } else {
                $roles_ids   = [(int) $user->id_role];
                $role_map    = [1 => 'Administrator', 2 => 'User / Dept', 3 => 'Mekanik', 4 => 'Admin OHS', 5 => 'KTT'];
                $roles_names = [isset($role_map[$user->id_role]) ? $role_map[$user->id_role] : 'User'];
                $primary_role = (int)$user->id_role;
            }
        } else {
            $roles_ids    = array_map(fn($r) => (int)$r->id_role, $roles_raw);
            $roles_names  = array_map(fn($r) => $r->nama_role,    $roles_raw);
            $primary_role = !empty($roles_ids) ? min($roles_ids) : (int)$user->id_role;
        }

        // Inisialisasi data session login pengguna & reset login_attempts di DB
        $this->Auth_model->reset_login_attempts($identity, $ip_address);
        $this->session->unset_userdata('login_attempt');
        $this->session->unset_userdata('last_attempt_time');
        $this->session->set_userdata([
            'id_user'     => (int) $user->id_user,
            'nama'        => $user->nama,
            'username'    => $user->username ?? $identity,
            'email'       => $user->email,
            'foto'        => $user->foto        ?? null,
            'jabatan'     => $user->jabatan     ?? null,
            'departemen'  => $user->departemen  ?? null,
            'role'        => $primary_role,
            'roles'       => $roles_ids,
            'roles_names' => $roles_names,
            'logged_in'   => TRUE,
        ]);

        // Catat aktivitas login di audit_log
        if ($this->db->table_exists('audit_log')) {
            $this->db->insert('audit_log', [
                'id_user'    => $user->id_user,
                'aksi'       => 'login',
                'tabel'      => 'users',
                'id_ref'     => $user->id_user,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        echo json_encode([
            'status'   => 'success',
            'message'  => 'Berhasil masuk! Mengalihkan...',
            'redirect' => base_url('dashboard'),
        ]);
    }

    /**
     * Proses Keluar (Logout) Pengguna.
     * Mencatat log logout, mengosongkan session, dan mengalihkan ke halaman login.
     * 
     * @return void
     */
    public function logout()
    {
        if ($this->db->table_exists('audit_log') && $this->session->userdata('id_user')) {
            $this->db->insert('audit_log', [
                'id_user'    => $this->session->userdata('id_user'),
                'aksi'       => 'logout',
                'tabel'      => 'users',
                'id_ref'     => $this->session->userdata('id_user'),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
        $this->session->set_flashdata('success', 'Berhasil keluar');
        $this->session->sess_destroy();
        redirect('auth');
    }
}
