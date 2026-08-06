<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Model User_model
 * 
 * Pengelolaan data pengguna (users) dan hak akses (user_roles).
 * Menyediakan fungsi CRUD user, sinkronisasi role, serta pengecekan keunikan akun.
 */
class User_model extends CI_Model
{
    /**
     * Konstruktor User_model
     * Inisialisasi library database CodeIgniter.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Mengambil daftar seluruh pengguna beserta label dan ID role yang dimiliki.
     * 
     * @param array $filters Filter opsional ['search' => string, 'is_active' => int|string]
     * @return array List object data user
     */
    public function get_all($filters = [])
    {
        // Parameterized select dengan agregasi GROUP_CONCAT untuk menggabungkan banyak role per user (mencegah N+1 query)
        $this->db->select('u.*, 
            GROUP_CONCAT(r.nama_role ORDER BY r.id_role SEPARATOR ", ") AS roles_label,
            GROUP_CONCAT(r.id_role ORDER BY r.id_role SEPARATOR ",") AS roles_ids');
        $this->db->from('users u');
        $this->db->join('user_roles ur', 'ur.id_user = u.id_user', 'left');
        $this->db->join('roles r',       'r.id_role  = ur.id_role', 'left');
        $this->db->group_by('u.id_user');

        // Filter pencarian berdasarkan nama, username, atau email
        if (!empty($filters['search'])) {
            $kw = $filters['search'];
            $this->db->group_start();
            $this->db->like('u.nama', $kw);
            $this->db->or_like('u.username', $kw);
            $this->db->or_like('u.email', $kw);
            $this->db->group_end();
        }

        // Filter status aktif user jika diset
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $this->db->where('u.is_active', $filters['is_active']);
        }

        $this->db->order_by('u.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Mengambil data detail satu pengguna berdasarkan ID.
     * 
     * @param int $id ID User
     * @return object|null Object data user atau null jika tidak ditemukan
     */
    public function get_by_id($id)
    {
        // Menggunakan JOIN + GROUP_CONCAT agar role langsung ter-load dalam 1 kali query (eager loading)
        $this->db->select('u.*, 
            GROUP_CONCAT(r.id_role ORDER BY r.id_role SEPARATOR ",") AS roles_ids,
            GROUP_CONCAT(r.nama_role ORDER BY r.id_role SEPARATOR ", ") AS roles_label');
        $this->db->from('users u');
        $this->db->join('user_roles ur', 'ur.id_user = u.id_user', 'left');
        $this->db->join('roles r',       'r.id_role  = ur.id_role', 'left');
        $this->db->where('u.id_user', (int) $id);
        $this->db->group_by('u.id_user');
        return $this->db->get()->row();
    }

    /**
     * Mengambil relasi role pengguna dari tabel user_roles.
     * 
     * @param int $id_user ID User
     * @return array List object user_roles
     */
    public function get_roles($id_user)
    {
        return $this->db->where('id_user', (int) $id_user)
            ->get('user_roles')->result();
    }

    /**
     * Mengambil daftar seluruh role yang tersedia dalam sistem.
     * 
     * @return array List object roles
     */
    public function get_all_roles()
    {
        return $this->db->order_by('id_role', 'ASC')->get('roles')->result();
    }

    /**
     * Memeriksa apakah username sudah digunakan oleh user lain.
     * 
     * @param string $username Username yang dicek
     * @param int|null $exclude_id ID User yang dikecualikan (saat edit)
     * @return bool True jika username sudah ada
     */
    public function is_username_exists($username, $exclude_id = null)
    {
        $this->db->where('username', $username);
        if (!empty($exclude_id)) {
            $this->db->where('id_user !=', (int) $exclude_id);
        }
        return $this->db->count_all_results('users') > 0;
    }

    /**
     * Memeriksa apakah email sudah digunakan oleh user lain.
     * 
     * @param string $email Email yang dicek
     * @param int|null $exclude_id ID User yang dikecualikan (saat edit)
     * @return bool True jika email sudah ada
     */
    public function is_email_exists($email, $exclude_id = null)
    {
        $this->db->where('email', $email);
        if (!empty($exclude_id)) {
            $this->db->where('id_user !=', (int) $exclude_id);
        }
        return $this->db->count_all_results('users') > 0;
    }

    /**
     * Menambahkan data user baru beserta penetapan role (optimasi insert_batch).
     * 
     * @param array $data Data pengguna (nama, username, email, password, dll)
     * @param array $roles Array ID role yang diberikan ke user
     * @return int|bool ID user baru jika berhasil, false jika gagal
     */
    public function insert($data, $roles = [])
    {
        $this->db->trans_start(); // Memulai transaksi database

        // Langkah 1: Simpan data utama pengguna ke tabel users
        $this->db->insert('users', $data);
        $id_user = $this->db->insert_id();

        // Langkah 2: Optimasi N+1 - Menyusun data batch role lalu lakukan insert_batch sekaligus
        if (!empty($roles) && is_array($roles)) {
            $role_batch = [];
            foreach ($roles as $id_role) {
                $id_role = (int) $id_role;
                if ($id_role > 0) {
                    $role_batch[] = [
                        'id_user' => $id_user,
                        'id_role' => $id_role,
                    ];
                }
            }
            if (!empty($role_batch)) {
                $this->db->insert_batch('user_roles', $role_batch);
            }

            // Sync kolom id_role di tabel users dengan ID role pertama sebagai role utama
            $this->db->where('id_user', $id_user)
                ->update('users', ['id_role' => (int) $roles[0]]);
        }

        $this->db->trans_complete(); // Selesaikan transaksi
        return $this->db->trans_status() ? $id_user : false;
    }

    /**
     * Memperbarui data pengguna beserta pembaruan relasi role (optimasi insert_batch).
     * 
     * @param int $id ID User
     * @param array $data Data pengguna yang diperbarui
     * @param array|null $roles Array ID role baru (jika null, role tidak diubah)
     * @return bool Status keberhasilan transaksi
     */
    public function update($id, $data, $roles = null)
    {
        $this->db->trans_start(); // Memulai transaksi database

        // Langkah 1: Update data utama pengguna
        $this->db->where('id_user', (int) $id)->update('users', $data);

        // Langkah 2: Pembaruan relasi role jika parameter $roles diberikan
        if ($roles !== null && is_array($roles)) {
            // Hapus relasi role lama terlebih dahulu
            $this->db->where('id_user', (int) $id)->delete('user_roles');

            // Optimasi N+1 - Menyusun data batch role baru lalu simpan via insert_batch
            $role_batch = [];
            foreach ($roles as $id_role) {
                $id_role = (int) $id_role;
                if ($id_role > 0) {
                    $role_batch[] = [
                        'id_user' => (int) $id,
                        'id_role' => $id_role,
                    ];
                }
            }
            if (!empty($role_batch)) {
                $this->db->insert_batch('user_roles', $role_batch);
            }

            // Sync id_role utama di tabel users
            if (!empty($roles)) {
                $this->db->where('id_user', (int) $id)
                    ->update('users', ['id_role' => (int) $roles[0]]);
            }
        }

        $this->db->trans_complete(); // Selesaikan transaksi
        return $this->db->trans_status();
    }

    /**
     * Mengubah status aktif/nonaktif akun pengguna (Toggle active status).
     * 
     * @param int $id ID User
     * @return bool True jika berhasil diubah, false jika user tidak ditemukan
     */
    public function toggle_active($id)
    {
        $user = $this->db->select('is_active')->where('id_user', (int) $id)->get('users')->row();
        if (!$user) {
            return false;
        }

        $new_status = $user->is_active ? 0 : 1;
        return $this->db->where('id_user', (int) $id)
            ->update('users', ['is_active' => $new_status]);
    }

    /**
     * Menghapus data pengguna beserta relasi role yang dimilikinya.
     * 
     * @param int $id ID User
     * @return bool Status keberhasilan penghapusan data
     */
    public function delete($id)
    {
        $this->db->trans_start();
        // Hapus relasi di user_roles terlebih dahulu untuk menjamin referential integrity
        $this->db->where('id_user', (int) $id)->delete('user_roles');
        // Hapus data pengguna utama
        $this->db->where('id_user', (int) $id)->delete('users');
        $this->db->trans_complete();
        return $this->db->trans_status();
    }
}
