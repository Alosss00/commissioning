<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Auth_model');
		$this->load->library(['form_validation', 'session']);
		$this->load->helper(['url', 'form']);
	}

	public function index()
	{
		if ($this->session->userdata('logged_in')) redirect('dashboard');

		// Reset login_attempt jika sudah lewat dari 5 menit (300 detik)
		$last_attempt_time = $this->session->userdata('last_attempt_time');
		if ($last_attempt_time && (time() - $last_attempt_time > 300)) {
			$this->session->unset_userdata('login_attempt');
			$this->session->unset_userdata('last_attempt_time');
		}

		$this->load->view('auth/index');
	}

	public function login()
	{
		if ($this->input->method() !== 'post') {
			redirect('auth');
			return;
		}

		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('identity', 'Email / Username', 'required');
		$this->form_validation->set_rules('password',  'Password',         'required');

		if ($this->form_validation->run() == FALSE) {
			echo json_encode(['status' => 'error', 'message' => validation_errors()]);
			return;
		}

		// Cek brute-force perlindungan percobaan login
		$attempt = (int) ($this->session->userdata('login_attempt') ?? 0);
		$last_attempt_time = $this->session->userdata('last_attempt_time');

		if ($attempt >= 5 && $last_attempt_time && (time() - $last_attempt_time < 300)) {
			$sisa_detik = 300 - (time() - $last_attempt_time);
			$sisa_menit = ceil($sisa_detik / 60);
			echo json_encode(['status' => 'error', 'message' => "Terlalu banyak percobaan gagal. Silakan tunggu {$sisa_menit} menit lagi sebelum mencoba kembali."]);
			return;
		} elseif ($attempt >= 5) {
			// Reset percobaan jika sudah melewati 5 menit
			$attempt = 0;
			$this->session->unset_userdata('login_attempt');
			$this->session->unset_userdata('last_attempt_time');
		}

		// Cek user di DB lokal
		$identity = $this->input->post('identity', TRUE);
		$password = $this->input->post('password', TRUE);

		$user = filter_var($identity, FILTER_VALIDATE_EMAIL)
			? $this->Auth_model->check_login_by_email($identity)
			: $this->Auth_model->check_login_by_username($identity);

		$authenticated = false;

		// Jika user belum ada di DB lokal, coba autentikasi ke LDAP dulu (JIT Provisioning)
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
			// User ada di DB lokal — cek auth_source
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
					// Fallback: jika pengguna telah memperbarui password personal melalui menu Profil
					$authenticated = password_verify($password, $user->password);
				}
			} else {
				$authenticated = password_verify($password, $user->password);
			}
		}

		if (!$user || !$authenticated) {
			$this->session->set_userdata('login_attempt', $attempt + 1);
			$this->session->set_userdata('last_attempt_time', time());
			echo json_encode(['status' => 'error', 'message' => 'Username / Password salah!']);
			return;
		}

		if (!$user->is_active) {
			echo json_encode(['status' => 'error', 'message' => 'Akun Anda telah dinonaktifkan.']);
			return;
		}

		// ── Ambil semua roles dari user_roles ──────────────────
		$roles_raw = $this->db
			->select('r.id_role, r.nama_role')
			->from('user_roles ur')
			->join('roles r', 'r.id_role = ur.id_role')
			->where('ur.id_user', $user->id_user)
			->get()->result();

		// Jika belum ada di user_roles, fallback ke kolom id_role
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
			$roles_ids   = array_map(fn($r) => (int)$r->id_role, $roles_raw);
			$roles_names = array_map(fn($r) => $r->nama_role,    $roles_raw);
			$primary_role = !empty($roles_ids) ? min($roles_ids) : (int)$user->id_role;
		}

		// ── Set session ────────────────────────────────────────
		$this->session->unset_userdata('login_attempt');
		$this->session->set_userdata([
			'id_user'     => (int) $user->id_user,
			'nama'        => $user->nama,
			'username'    => $user->username ?? $identity,
			'email'       => $user->email,
			'foto'        => $user->foto        ?? null,
			'jabatan'     => $user->jabatan     ?? null,
			'departemen'  => $user->departemen  ?? null,
			'role'        => $primary_role,        // int — dipakai cek akses
			'roles'       => $roles_ids,           // array semua role
			'roles_names' => $roles_names,
			'logged_in'   => TRUE,
		]);

		// Audit log (opsional — skip jika tabel belum ada)
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
