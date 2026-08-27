<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Model Auth_model
 * 
 * Pengelolaan otentikasi akun pengguna (Login via email/username),
 * penarikan daftar role user, serta Just-In-Time (JIT) provisioning pengguna dari LDAP.
 */
class Auth_model extends CI_Model
{
    /**
     * Memeriksa otentikasi login berdasarkan alamat email pengguna.
     * 
     * @param string $email Alamat email yang diinput
     * @return object|null Object data user jika aktif dan terdaftar, null jika tidak ada
     */
    public function check_login_by_email($email)
    {
        $select = 'id_user, id_role, nama, username, email, foto, jabatan, no_hp, departemen, password, is_active';
        if ($this->db->field_exists('auth_source', 'users')) {
            $select .= ', auth_source, ldap_dn';
        }

        $user = $this->db
            ->select($select)
            ->where('email', $email)
            ->where('is_active', 1)
            ->get('users')
            ->row();

        if ($user && !isset($user->auth_source)) {
            $user->auth_source = 'local';
            $user->ldap_dn     = null;
        }

        return $user;
    }

    /**
     * Memeriksa otentikasi login berdasarkan username pengguna.
     * 
     * @param string $username Username yang diinput
     * @return object|null Object data user jika aktif dan terdaftar, null jika tidak ada
     */
    public function check_login_by_username($username)
    {
        $select = 'id_user, id_role, nama, username, email, foto, jabatan, no_hp, departemen, password, is_active';
        if ($this->db->field_exists('auth_source', 'users')) {
            $select .= ', auth_source, ldap_dn';
        }

        $user = $this->db
            ->select($select)
            ->where('username', $username)
            ->where('is_active', 1)
            ->get('users')
            ->row();

        if ($user && !isset($user->auth_source)) {
            $user->auth_source = 'local';
            $user->ldap_dn     = null;
        }

        return $user;
    }

    /**
     * Mengambil daftar seluruh role yang dimiliki oleh pengguna dari tabel user_roles.
     * 
     * @param int $id_user ID User
     * @return array List object [{id_role, nama_role}]
     */
    public function get_user_roles($id_user)
    {
        return $this->db
            ->select('r.id_role, r.nama_role')
            ->from('user_roles ur')
            ->join('roles r', 'r.id_role = ur.id_role')
            ->where('ur.id_user', (int) $id_user)
            ->order_by('r.id_role', 'ASC')
            ->get()
            ->result();
    }

    /**
     * Mendaftarkan pengguna baru secara otomatis saat berhasil login via LDAP (JIT Provisioning).
     * 
     * @param string $username Username pengguna
     * @param array $ldap_attrs Atribut tambahan dari LDAP (email, full_name, dn)
     * @return object|bool Object data user baru jika sukses, false jika gagal
     */
    public function auto_provision_ldap_user($username, $ldap_attrs = [])
    {
        $email     = !empty($ldap_attrs['email']) ? $ldap_attrs['email'] : $username . '@archimining.com';
        $full_name = !empty($ldap_attrs['full_name']) ? $ldap_attrs['full_name'] : ucfirst($username);

        $data_user = [
            'id_role'     => 0, // Role default: Pending menetapkan role dari Administrator
            'nama'        => $full_name,
            'username'    => $username,
            'email'       => $email,
            'password'    => password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT),
            'auth_source' => 'ldap',
            'ldap_dn'     => $ldap_attrs['dn'] ?? null,
            'is_active'   => 1,
            'created_at'  => date('Y-m-d H:i:s')
        ];

        $this->db->insert('users', $data_user);
        $id_user = $this->db->insert_id();

        if ($id_user) {
            return $this->check_login_by_username($username);
        }

        return false;
    }

    /**
     * Membersihkan record percobaan login yang sudah di atas 1 jam.
     */
    public function clean_old_login_attempts()
    {
        if ($this->db->table_exists('login_attempts')) {
            $one_hour_ago = date('Y-m-d H:i:s', time() - 3600);
            $this->db->where('last_attempt_time <', $one_hour_ago)->delete('login_attempts');
        }
    }

    /**
     * Mengambil data percobaan gagal login berdasarkan identity dan ip_address.
     * 
     * @param string $identity
     * @param string $ip_address
     * @return object|null
     */
    public function get_login_attempt($identity, $ip_address)
    {
        if (!$this->db->table_exists('login_attempts')) {
            return null;
        }

        return $this->db
            ->where('identity', $identity)
            ->where('ip_address', $ip_address)
            ->get('login_attempts')
            ->row();
    }

    /**
     * Mencatat / menambah jumlah percobaan login yang gagal ke database.
     * 
     * @param string $identity
     * @param string $ip_address
     * @return void
     */
    public function record_failed_attempt($identity, $ip_address)
    {
        if (!$this->db->table_exists('login_attempts')) {
            return;
        }

        $row = $this->get_login_attempt($identity, $ip_address);
        $now = date('Y-m-d H:i:s');

        if ($row) {
            $this->db->where('id', $row->id)->update('login_attempts', [
                'attempt'           => $row->attempt + 1,
                'last_attempt_time' => $now,
            ]);
        } else {
            $this->db->insert('login_attempts', [
                'identity'          => $identity,
                'ip_address'        => $ip_address,
                'attempt'           => 1,
                'last_attempt_time' => $now,
            ]);
        }
    }

    /**
     * Mereset percobaan gagal login ke 0 / menghapus record saat login berhasil.
     * 
     * @param string $identity
     * @param string $ip_address
     * @return void
     */
    public function reset_login_attempts($identity, $ip_address)
    {
        if (!$this->db->table_exists('login_attempts')) {
            return;
        }

        $this->db
            ->where('identity', $identity)
            ->where('ip_address', $ip_address)
            ->delete('login_attempts');
    }
}

