<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth_model extends CI_Model
{
    /**
     * Login by email — return user row lengkap termasuk foto, jabatan, departemen
     */
    public function check_login_by_email($email)
    {
        return $this->db
            ->select('id_user, id_role, nama, username, email, foto, jabatan, no_hp, departemen, password, is_active')
            ->where('email', $email)
            ->where('is_active', 1)
            ->get('users')
            ->row();
    }

    /**
     * Login by username — return user row lengkap
     */
    public function check_login_by_username($username)
    {
        return $this->db
            ->select('id_user, id_role, nama, username, email, foto, jabatan, no_hp, departemen, password, is_active')
            ->where('username', $username)
            ->where('is_active', 1)
            ->get('users')
            ->row();
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
}
