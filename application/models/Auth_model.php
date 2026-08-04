<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth_model extends CI_Model
{
    /**
     * Login by email — return user row lengkap termasuk foto, jabatan, departemen
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
            $user->ldap_dn = null;
        }

        return $user;
    }

    /**
     * Login by username — return user row lengkap
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
            $user->ldap_dn = null;
        }

        return $user;
    }

    /**
     * Ambil semua role milik user dari tabel user_roles + nama role
     * Return: array of objects [{id_role, nama_role}]
     */
    public function get_user_roles($id_user)
    {
        return $this->db
            ->select('r.id_role, r.nama_role')
            ->from('user_roles ur')
            ->join('roles r', 'r.id_role = ur.id_role')
            ->where('ur.id_user', $id_user)
            ->order_by('r.id_role', 'ASC')
            ->get()
            ->result();
    }

    /**
     * Otomatis mendaftarkan user baru dari LDAP ke DB lokal (JIT Provisioning)
     * User baru diberi id_role = 0 (Menunggu penetapan Role oleh Administrator)
     */
    public function auto_provision_ldap_user($username, $ldap_attrs = [])
    {
        $email     = !empty($ldap_attrs['email']) ? $ldap_attrs['email'] : $username . '@archimining.local';
        $full_name = !empty($ldap_attrs['full_name']) ? $ldap_attrs['full_name'] : ucfirst($username);

        $data_user = [
            'id_role'     => 0, // Pending Role: Menunggu penetapan role dari Admin
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
}
