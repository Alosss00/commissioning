<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controller UserManagement
 * 
 * Pengelolaan pengguna (User Management) khusus untuk Super Admin (Role ID = 1).
 * Menyediakan fitur:
 * - Menampilkan daftar user dan role (Index & DataTables AJAX)
 * - Detail pengguna untuk modal edit
 * - Menambah dan mengedit akun user beserta hak akses/role (Save AJAX)
 * - Mengubah status aktif/non-aktif user (Toggle Active AJAX)
 * - Menghapus akun user non-sistem (Delete AJAX)
 */
class UserManagement extends CI_Controller
{
    /**
     * Konstruktor UserManagement
     * Memuat model, library, helper, serta melakukan pemeriksaan otentikasi & otorisasi Super Admin.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model', 'user_model');
        $this->load->library(['session', 'form_validation', 'upload']);
        $this->load->helper(['url', 'form']);

        // Proteksi Otorisasi: Pastikan pengguna sudah login
        if (!$this->session->userdata('id_user')) {
            redirect('auth/login');
        }

        // Proteksi Hak Akses: Hanya Super Admin (Role 1) yang boleh mengakses controller ini
        if ((int)$this->session->userdata('role') !== 1) {
            $this->session->set_flashdata('error', 'Akses ditolak.');
            redirect('dashboard');
        }

        // Jalankan pengecekan kelengkapan data departemen secara ter-optimasikan
        $this->_check_departments();
    }

    /**
     * Memastikan data master departemen di tabel perusahaan selalu up-to-date.
     * Menggunakan query tunggal dan caching internal untuk menghindari N+1 query loop.
     * 
     * @return void
     */
    private function _check_departments()
    {
        static $checked = false;
        if ($checked) return;
        $checked = true;

        $old_new_map = [
            'BUSINESS DEVELOPMENT' => 'Departemen Business Development',
            'COMMERCIAL' => 'Departemen Commercial',
            'COMMUNITY DEVELOPMENT' => 'Departemen Community Development',
            'COMPLIANCE' => 'Departemen Compliance',
            'COMREL & LAND ACQ (Community Relations & Land Acquisition)' => 'Departemen Comrel & Land Acq (Community Relations & Land Acquisition)',
            'CORPORATE LEGAL' => 'Departemen Corporate Legal',
            'ENVIRONMENTAL' => 'Departemen Environmental',
            'EXPLORATION' => 'Departemen Exploration',
            'EXTERNAL RELATIONS' => 'Departemen External Relations',
            'FIN & ACC OPERATIONAL (Finance & Accounting Operational)' => 'Departemen Fin & Acc Operational (Finance & Accounting Operational)',
            'HCCS (Human Capital & Corporate Services)' => 'Departemen HCCS (Human Capital & Corporate Services)',
            'HSE & FORMALITIES / HSE (Health, Safety, Environment)' => 'Departemen HSE & Formalities / HSE (Health, Safety, Environment)',
            'IT (Information Technology)' => 'Departemen IT (Information Technology)',
            'MAINTENANCE' => 'Departemen Maintenance',
            'MANAGEMENT' => 'Departemen Management',
            'METALLURGY' => 'Departemen Metallurgy',
            'MINING' => 'Departemen Mining',
            'MINING TECH SERVICE / MINING TECHNICAL SERVICE' => 'Departemen Mining Tech Service / Mining Technical Service',
            'OHS (Occupational Health & Safety)' => 'Departemen OHS (Occupational Health & Safety)',
            'PRINCIPAL MINING' => 'Departemen Principal Mining',
            'PROCESS PLANT' => 'Departemen Process Plant',
            'PROJECT' => 'Departemen Project',
            'RESOURCES & RESERVE' => 'Departemen Resources & Reserve',
            'SECURITY' => 'Departemen Security',
            'SUSTAINABILITY & EXTERNAL AFFAIRS' => 'Departemen Sustainability & External Affairs',
            'SUPPLY CHAIN' => 'Departemen Supply Chain',
            'UNDERGROUND' => 'Departemen Underground'
        ];

        // Ambil daftar seluruh departemen yang ada di DB dalam 1 kali query
        $existing = $this->db->select('LOWER(nama_perusahaan) as nama_lower')->get('perusahaan')->result_array();
        $existing_map = array_column($existing, 'nama_lower');

        $to_insert = [];
        $has_created_at = $this->db->field_exists('created_at', 'perusahaan');

        // Iterasi peta departemen secara efisien tanpa query dalam loop yang tidak perlu
        foreach ($old_new_map as $old => $new) {
            $old_lower = strtolower($old);
            $new_trim  = trim($new);
            $new_lower = strtolower($new_trim);

            // Update nama lama ke baru jika masih ditemukan nama lama di DB
            if (in_array($old_lower, $existing_map, true)) {
                $this->db->where('LOWER(nama_perusahaan)', $old_lower)->update('perusahaan', ['nama_perusahaan' => $new_trim]);
            }

            // Kumpulkan departemen baru yang belum ada untuk di-insert sekaligus via insert_batch
            if (!in_array($old_lower, $existing_map, true) && !in_array($new_lower, $existing_map, true)) {
                $payload = [
                    'nama_perusahaan' => $new_trim,
                    'is_active'       => 1
                ];
                if ($has_created_at) {
                    $payload['created_at'] = date('Y-m-d H:i:s');
                }
                $to_insert[] = $payload;
                $existing_map[] = $new_lower;
            }
        }

        // Lakukan batch insert jika ada departemen baru
        if (!empty($to_insert)) {
            $this->db->insert_batch('perusahaan', $to_insert);
        }
    }

    /**
     * Halaman Utama Manajemen User
     * Menyiapkan data awal dan memuat view daftar pengguna.
     * 
     * @return void
     */
    public function index()
    {
        $data['title']      = 'Manajemen User';
        $data['user']       = $this->session->userdata();
        $data['users']      = $this->user_model->get_all();
        $data['roles']      = $this->user_model->get_all_roles();
        $data['perusahaan'] = $this->db
            ->select('nama_perusahaan')
            ->where('is_active', 1)
            ->order_by('nama_perusahaan', 'ASC')
            ->get('perusahaan')->result();

        $this->load->view('templates/header',  $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('users/index',       $data);
        $this->load->view('templates/footer',  $data);
    }

    /**
     * Endpoint AJAX DataTables untuk mengambil data daftar user.
     * 
     * @return void Menampilkan JSON data pengguna + CSRF hash terbaru
     */
    public function get_data()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $filters = [
            'search'    => $this->input->post('search'),
            'is_active' => $this->input->post('is_active'),
        ];
        $users = $this->user_model->get_all($filters);
        $rows  = [];

        // Formatting tiap baris data pengguna untuk tampilan tabel
        foreach ($users as $u) {
            $foto = $u->foto
                ? '<img src="' . base_url($u->foto) . '" class="rounded-circle" style="width:36px;height:36px;object-fit:cover;">'
                : '<div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center text-white" style="width:36px;height:36px;font-size:14px;">' . strtoupper(substr($u->nama, 0, 1)) . '</div>';

            $roles_html = '';
            if ($u->roles_label) {
                foreach (explode(', ', $u->roles_label) as $r) {
                    $roles_html .= '<span class="badge bg-light text-dark border me-1" style="font-size:11px;">' . html_escape($r) . '</span>';
                }
            } else {
                $roles_html = '<span class="badge bg-warning text-dark"><i class="bi bi-clock-history me-1"></i>Menunggu Role (LDAP)</span>';
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

        $output = ['data' => $rows];
        $output['csrf_hash'] = $this->security->get_csrf_hash();
        echo json_encode($output);
    }

    /**
     * Endpoint AJAX untuk mengambil detail data satu user (populasi form modal edit).
     * 
     * @return void Response JSON status & data user
     */
    public function get_detail()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id   = (int) $this->input->post('id_user');
        $user = $this->user_model->get_by_id($id);

        if (!$user) {
            echo json_encode([
                'status'    => 'error', 
                'message'   => 'User tidak ditemukan.',
                'csrf_hash' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        echo json_encode([
            'status'    => 'success', 
            'data'      => $user,
            'csrf_hash' => $this->security->get_csrf_hash()
        ]);
    }

    /**
     * Endpoint AJAX untuk menyimpan (Insert / Update) data pengguna.
     * Melakukan sanitasi input, validasi email/username unik, penanganan upload foto, dan hash password.
     * 
     * @return void Response JSON status simpan
     */
    public function save()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id         = (int) $this->input->post('id_user');
        $nama       = trim($this->input->post('nama'));
        $username   = trim($this->input->post('username'));
        $email      = trim($this->input->post('email'));
        $jabatan    = trim($this->input->post('jabatan'));
        $no_hp      = trim($this->input->post('no_hp'));
        $departemen = trim($this->input->post('departemen'));
        $password   = trim($this->input->post('password'));
        $roles      = $this->input->post('roles') ?: [];

        // Validasi parameter wajib diisi
        if (!$nama || !$username || !$email) {
            echo json_encode(['status' => 'error', 'message' => 'Nama, username, dan email wajib diisi.', 'csrf_hash' => $this->security->get_csrf_hash()]);
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'message' => 'Format email tidak valid.', 'csrf_hash' => $this->security->get_csrf_hash()]);
            return;
        }
        if (empty($roles)) {
            echo json_encode(['status' => 'error', 'message' => 'Pilih minimal satu role.', 'csrf_hash' => $this->security->get_csrf_hash()]);
            return;
        }
        if (!$id && empty($password)) {
            echo json_encode(['status' => 'error', 'message' => 'Password wajib diisi untuk user baru.', 'csrf_hash' => $this->security->get_csrf_hash()]);
            return;
        }

        // Cek duplikasi username dan email
        if ($this->user_model->is_username_exists($username, $id ?: null)) {
            echo json_encode(['status' => 'error', 'message' => 'Username sudah digunakan.', 'csrf_hash' => $this->security->get_csrf_hash()]);
            return;
        }
        if ($this->user_model->is_email_exists($email, $id ?: null)) {
            echo json_encode(['status' => 'error', 'message' => 'Email sudah digunakan.', 'csrf_hash' => $this->security->get_csrf_hash()]);
            return;
        }

        // Penanganan upload foto profil pengguna
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
                echo json_encode(['status' => 'error', 'message' => $this->upload->display_errors('', ''), 'csrf_hash' => $this->security->get_csrf_hash()]);
                return;
            }
            $foto = 'uploads/foto_user/' . $this->upload->data('file_name');
        }

        // Menyusun payload data user
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

        // Eksekusi Simpan/Update via Model (Optimized Batch Roles)
        if ($id) {
            $ok  = $this->user_model->update($id, $payload, $roles);
            $msg = 'Data user berhasil diperbarui.';
        } else {
            $payload['is_active'] = 1;
            $ok  = $this->user_model->insert($payload, $roles);
            $msg = 'User baru berhasil ditambahkan.';
        }

        echo json_encode([
            'status'    => $ok ? 'success' : 'error',
            'message'   => $ok ? $msg : 'Gagal menyimpan data.',
            'csrf_hash' => $this->security->get_csrf_hash()
        ]);
    }

    /**
     * Endpoint AJAX untuk toggle status aktif/non-aktif akun pengguna.
     * Memasukkan catatan ke audit_log saat terjadi perubahan status.
     * 
     * @return void Response JSON status toggle
     */
    public function toggle_active()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id = (int) $this->input->post('id_user');
        if ($id === 1) {
            echo json_encode(['status' => 'error', 'message' => 'User admin utama tidak dapat dinonaktifkan.', 'csrf_hash' => $this->security->get_csrf_hash()]);
            return;
        }

        $ok = $this->user_model->toggle_active($id);
        
        if ($ok) {
            $user = $this->user_model->get_by_id($id);
            $aksi = ($user && $user->is_active) ? 'Aktifkan Akun' : 'Nonaktifkan Akun';
            $this->db->insert('audit_log', [
                'id_user'    => $this->session->userdata('id_user'),
                'aksi'       => $aksi,
                'tabel'      => 'users',
                'id_ref'     => $id,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        echo json_encode([
            'status'    => $ok ? 'success' : 'error',
            'message'   => $ok ? 'Status pengguna berhasil diperbarui.' : 'Gagal memperbarui status.',
            'csrf_hash' => $this->security->get_csrf_hash()
        ]);
    }

    /**
     * Endpoint AJAX untuk menghapus akun pengguna (selain Super Admin Utama ID 1).
     * Memeriksa keterkaitan pengajuan uji aktif sebelum menghapus data.
     * 
     * @return void Response JSON status hapus
     */
    public function delete()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id = (int) $this->input->post('id_user');
        if ($id === 1) {
            echo json_encode(['status' => 'error', 'message' => 'User admin utama tidak dapat dihapus.', 'csrf_hash' => $this->security->get_csrf_hash()]);
            return;
        }

        // Cek apakah pengguna memiliki pengajuan aktif dalam sistem
        $cek = $this->db->where('id_pemohon', $id)
            ->where_in('status', ['submitted', 'approved_manager', 'approved_admin', 'scheduled', 'review_ohs', 'approved_ohs', 'approved_ktt'])
            ->count_all_results('pengajuan_uji');

        if ($cek > 0) {
            echo json_encode(['status' => 'error', 'message' => 'User memiliki pengajuan aktif, tidak dapat dihapus.', 'csrf_hash' => $this->security->get_csrf_hash()]);
            return;
        }

        $ok = $this->user_model->delete($id);
        echo json_encode([
            'status'    => $ok ? 'success' : 'error',
            'message'   => $ok ? 'User berhasil dihapus.' : 'Gagal menghapus user.',
            'csrf_hash' => $this->security->get_csrf_hash()
        ]);
    }
}
