<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // ── Ambil semua user + roles mereka ──────────────────────
    public function get_all($filters = [])
    {
        $this->db->select('u.*, GROUP_CONCAT(r.nama_role ORDER BY r.id_role SEPARATOR ", ") AS roles_label,
                           GROUP_CONCAT(r.id_role ORDER BY r.id_role SEPARATOR ",") AS roles_ids');
        $this->db->from('users u');
        $this->db->join('user_roles ur', 'ur.id_user = u.id_user', 'left');
        $this->db->join('roles r',       'r.id_role  = ur.id_role', 'left');
        $this->db->group_by('u.id_user');

        if (!empty($filters['search'])) {
            $kw = $filters['search'];
            $this->db->group_start();
            $this->db->like('u.nama', $kw);
            $this->db->or_like('u.username', $kw);
            $this->db->or_like('u.email', $kw);
            $this->db->group_end();
        }
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $this->db->where('u.is_active', $filters['is_active']);
        }
        $this->db->order_by('u.created_at', 'DESC');
        return $this->db->get()->result();
    }

    // ── Get by ID ─────────────────────────────────────────────
    public function get_by_id($id)
    {
        $this->db->select('u.*, GROUP_CONCAT(r.id_role ORDER BY r.id_role SEPARATOR ",") AS roles_ids,
                           GROUP_CONCAT(r.nama_role ORDER BY r.id_role SEPARATOR ", ") AS roles_label');
        $this->db->from('users u');
        $this->db->join('user_roles ur', 'ur.id_user = u.id_user', 'left');
        $this->db->join('roles r',       'r.id_role  = ur.id_role', 'left');
        $this->db->where('u.id_user', $id);
        $this->db->group_by('u.id_user');
        return $this->db->get()->row();
    }

    // ── Get roles user ────────────────────────────────────────
    public function get_roles($id_user)
    {
        return $this->db->where('id_user', $id_user)
            ->get('user_roles')->result();
    }

    // ── Semua roles tersedia ──────────────────────────────────
    public function get_all_roles()
    {
        return $this->db->order_by('id_role', 'ASC')->get('roles')->result();
    }

    // ── Cek username/email unik ───────────────────────────────
    public function is_username_exists($username, $exclude_id = null)
    {
        $this->db->where('username', $username);
        if ($exclude_id) $this->db->where('id_user !=', $exclude_id);
        return $this->db->count_all_results('users') > 0;
    }

    public function is_email_exists($email, $exclude_id = null)
    {
        $this->db->where('email', $email);
        if ($exclude_id) $this->db->where('id_user !=', $exclude_id);
        return $this->db->count_all_results('users') > 0;
    }

    // ── Insert user ───────────────────────────────────────────
    public function insert($data, $roles = [])
    {
        $this->db->trans_start();
        $this->db->insert('users', $data);
        $id_user = $this->db->insert_id();

        // Insert roles
        foreach ($roles as $id_role) {
            $this->db->insert('user_roles', [
                'id_user' => $id_user,
                'id_role' => (int) $id_role,
            ]);
        }
        // Sync kolom id_role dengan role pertama
        if (!empty($roles)) {
            $this->db->where('id_user', $id_user)
                ->update('users', ['id_role' => (int) $roles[0]]);
        }
        $this->db->trans_complete();
        return $this->db->trans_status() ? $id_user : false;
    }

    // ── Update user ───────────────────────────────────────────
    public function update($id, $data, $roles = null)
    {
        $this->db->trans_start();
        $this->db->where('id_user', $id)->update('users', $data);

        if ($roles !== null) {
            // Hapus roles lama, insert baru
            $this->db->where('id_user', $id)->delete('user_roles');
            foreach ($roles as $id_role) {
                $this->db->insert('user_roles', [
                    'id_user' => $id,
                    'id_role' => (int) $id_role,
                ]);
            }
            // Sync id_role
            if (!empty($roles)) {
                $this->db->where('id_user', $id)
                    ->update('users', ['id_role' => (int) $roles[0]]);
            }
        }
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    // ── Toggle aktif ──────────────────────────────────────────
    public function toggle_active($id)
    {
        $user = $this->db->select('is_active')->where('id_user', $id)->get('users')->row();
        if (!$user) return false;
        return $this->db->where('id_user', $id)
            ->update('users', ['is_active' => $user->is_active ? 0 : 1]);
    }

    // ── Delete user ───────────────────────────────────────────
    public function delete($id)
    {
        $this->db->trans_start();
        $this->db->where('id_user', $id)->delete('user_roles');
        $this->db->where('id_user', $id)->delete('users');
        $this->db->trans_complete();
        return $this->db->trans_status();
    }
}
