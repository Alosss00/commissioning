<?php
defined('BASEPATH') or exit('No direct script access allowed');

class UserManagement extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model', 'user_model');
        $this->load->library(['session', 'form_validation', 'upload']);
        $this->load->helper(['url', 'form']);
        if (!$this->session->userdata('id_user')) redirect('auth/login');
        // Hanya Admin
        if ((int)$this->session->userdata('role') !== 1) {
            $this->session->set_flashdata('error', 'Akses ditolak.');
            redirect('dashboard');
        }
    }

    // ── INDEX: daftar semua user ─────────────────────────────
    public function index()
    {
        $data['title']  = 'Manajemen User';
        $data['user']   = $this->session->userdata();
        $data['users']  = $this->user_model->get_all();
        $data['roles']  = $this->user_model->get_all_roles();
        $data['perusahaan'] = $this->db
            ->select('nama_perusahaan')
            ->where('is_active', 1)
            ->order_by('nama_perusahaan', 'ASC')
            ->get('perusahaan')->result();

        $this->load->view('templates/header',        $data);
        $this->load->view('templates/sidebar',       $data);
        $this->load->view('users/index',   $data);
        $this->load->view('templates/footer',        $data);
    }

    // ── AJAX: DataTable get_data ──────────────────────────────
    public function get_data()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $filters = [
            'search'    => $this->input->post('search'),
            'is_active' => $this->input->post('is_active'),
        ];
        $users = $this->user_model->get_all($filters);
        $rows  = [];
        foreach ($users as $u) {
            $foto = $u->foto
                ? '<img src="' . base_url($u->foto) . '" class="rounded-circle" style="width:36px;height:36px;object-fit:cover;">'
                : '<div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center text-white" style="width:36px;height:36px;font-size:14px;">' . strtoupper(substr($u->nama, 0, 1)) . '</div>';

            $roles_html = '';
            if ($u->roles_label) {
                foreach (explode(', ', $u->roles_label) as $r) {
                    $roles_html .= '<span class="badge bg-light text-dark border me-1" style="font-size:11px;">' . $r . '</span>';
                }
            }
            $status = $u->is_active
                ? '<span class="badge bg-success">Aktif</span>'
                : '<span class="badge bg-secondary">Nonaktif</span>';

            $aksi = '
              <div class="d-flex gap-1">
                <button class="btn btn-sm btn-outline-primary py-0 btn-edit-user" data-id="' . $u->id_user . '" title="Edit"><i class="bi bi-pencil"></i></button>
                <button class="btn btn-sm btn-outline-' . ($u->is_active ? 'warning' : 'success') . ' py-0 btn-toggle-user" data-id="' . $u->id_user . '" data-active="' . $u->is_active . '" title="' . ($u->is_active ? 'Nonaktifkan' : 'Aktifkan') . '">
                  <i class="bi bi-' . ($u->is_active ? 'person-dash' : 'person-check') . '"></i>
                </button>
                ' . ($u->id_user != 1 ? '<button class="btn btn-sm btn-outline-danger py-0 btn-delete-user" data-id="' . $u->id_user . '" title="Hapus"><i class="bi bi-trash"></i></button>' : '') . '
              </div>';

            $rows[] = [
                'foto'     => $foto,
                'nama'     => '<strong>' . html_escape($u->nama) . '</strong><br><small class="text-muted">@' . html_escape($u->username) . '</small>',
                'email'    => html_escape($u->email),
                'jabatan'  => html_escape($u->jabatan ?? '-'),
                'roles'    => $roles_html ?: '<span class="text-muted small">—</span>',
                'status'   => $status,
                'aksi'     => $aksi,
            ];
        }
        echo json_encode(['data' => $rows]);
    }

    // ── AJAX: get detail user (untuk modal edit) ──────────────
    public function get_detail()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $id   = (int) $this->input->post('id_user');
        $user = $this->user_model->get_by_id($id);
        if (!$user) {
            echo json_encode(['status' => 'error', 'message' => 'User tidak ditemukan.']);
            return;
        }
        echo json_encode(['status' => 'success', 'data' => $user]);
    }

    // ── AJAX: simpan user (insert/update) ────────────────────
    public function save()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id       = (int) $this->input->post('id_user');
        $nama     = trim($this->input->post('nama'));
        $username = trim($this->input->post('username'));
        $email    = trim($this->input->post('email'));
        $jabatan  = trim($this->input->post('jabatan'));
        $no_hp    = trim($this->input->post('no_hp'));
        $departemen = trim($this->input->post('departemen'));
        $password = trim($this->input->post('password'));
        $roles    = $this->input->post('roles') ?: [];

        // Validasi wajib
        if (!$nama || !$username || !$email) {
            echo json_encode(['status' => 'error', 'message' => 'Nama, username, dan email wajib diisi.']);
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'message' => 'Format email tidak valid.']);
            return;
        }
        if (empty($roles)) {
            echo json_encode(['status' => 'error', 'message' => 'Pilih minimal satu role.']);
            return;
        }
        if (!$id && empty($password)) {
            echo json_encode(['status' => 'error', 'message' => 'Password wajib diisi untuk user baru.']);
            return;
        }

        // Cek duplikat
        if ($this->user_model->is_username_exists($username, $id ?: null)) {
            echo json_encode(['status' => 'error', 'message' => 'Username sudah digunakan.']);
            return;
        }
        if ($this->user_model->is_email_exists($email, $id ?: null)) {
            echo json_encode(['status' => 'error', 'message' => 'Email sudah digunakan.']);
            return;
        }

        // Handle upload foto
        $foto = null;
        if (!empty($_FILES['foto']['name'])) {
            $path = FCPATH . 'uploads/foto_user/';
            if (!is_dir($path)) mkdir($path, 0755, true);
            $this->upload->initialize([
                'upload_path'   => $path,
                'allowed_types' => 'jpg|jpeg|png|webp',
                'max_size'      => 2048,
                'file_name'     => 'user_' . ($id ?: 'new') . '_' . time(),
                'overwrite'     => true,
            ]);
            if (!$this->upload->do_upload('foto')) {
                echo json_encode(['status' => 'error', 'message' => $this->upload->display_errors('', '')]);
                return;
            }
            $foto = 'uploads/foto_user/' . $this->upload->data('file_name');
        }

        $payload = [
            'nama'       => $nama,
            'username'   => $username,
            'email'      => $email,
            'jabatan'    => $jabatan ?: null,
            'no_hp'      => $no_hp ?: null,
            'departemen' => $departemen ?: null,
        ];
        if ($foto)     $payload['foto']     = $foto;
        if ($password) $payload['password'] = password_hash($password, PASSWORD_BCRYPT);

        if ($id) {
            $ok = $this->user_model->update($id, $payload, $roles);
            $msg = 'Data user berhasil diperbarui.';
        } else {
            $payload['is_active'] = 1;
            $ok = $this->user_model->insert($payload, $roles);
            $msg = 'User baru berhasil ditambahkan.';
        }

        echo json_encode($ok
            ? ['status' => 'success', 'message' => $msg]
            : ['status' => 'error', 'message' => 'Gagal menyimpan data.']);
    }

    // ── AJAX: toggle aktif ────────────────────────────────────
    public function toggle_active()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $id = (int) $this->input->post('id_user');
        if ($id === 1) {
            echo json_encode(['status' => 'error', 'message' => 'User admin utama tidak dapat dinonaktifkan.']);
            return;
        }
        $ok = $this->user_model->toggle_active($id);
        echo json_encode($ok ? ['status' => 'success'] : ['status' => 'error', 'message' => 'Gagal.']);
    }

    // ── AJAX: delete ──────────────────────────────────────────
    public function delete()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $id = (int) $this->input->post('id_user');
        if ($id === 1) {
            echo json_encode(['status' => 'error', 'message' => 'User admin utama tidak dapat dihapus.']);
            return;
        }
        // Cek apakah punya pengajuan aktif
        $cek = $this->db->where('id_pemohon', $id)->where_in('status', ['submitted', 'approved_manager', 'approved_admin', 'scheduled', 'review_ohs', 'approved_ohs', 'approved_ktt'])->count_all_results('pengajuan_uji');
        if ($cek > 0) {
            echo json_encode(['status' => 'error', 'message' => 'User memiliki pengajuan aktif, tidak dapat dihapus.']);
            return;
        }
        $ok = $this->user_model->delete($id);
        echo json_encode($ok ? ['status' => 'success', 'message' => 'User berhasil dihapus.'] : ['status' => 'error', 'message' => 'Gagal menghapus.']);
    }
}
