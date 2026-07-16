<?php

/**
 * Mekanik_model
 * Tujuan   : Akses data master mekanik/teknisi lapangan & tipe kendaraan kompetensi
 * Caller   : Mekanik.php, Jadwal.php, Jadwal_model.php
 * Dependen : mekanik_master, mekanik_tipe_kendaraan, tipe_kendaraan, jadwal_uji
 * Fungsi   :
 *   get_all($filter)            — daftar mekanik + concat nama tipe
 *   get_by_id($id)              — 1 mekanik
 *   get_tipe_by_mekanik($id)    — tipe kendaraan yang dikuasai mekanik
 *   get_by_jenis($nama_tipe)    — mekanik capable untuk tipe tertentu (by nama)
 *   get_by_tipe_id($id)         — mekanik capable by id_tipe_kendaraan (lebih efisien)
 *   cek_konflik_mekanik(...)    — cek jadwal konflik ±60 menit
 *   insert/update/toggle/delete — CRUD
 *   jenis_list()                — static list nama tipe (compat)
 * Side effect:
 *   - READ: mekanik_master, mekanik_tipe_kendaraan, tipe_kendaraan, jadwal_uji
 *   - WRITE: mekanik_master, mekanik_tipe_kendaraan
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Mekanik_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Get all mekanik + concat nama tipe kendaraan
    // JOIN tipe_kendaraan untuk nama yang konsisten
    // ─────────────────────────────────────────────────────────────────────────
    public function get_all($filter = [])
    {
        $this->db
            ->select('m.*, GROUP_CONCAT(t.nama_tipe ORDER BY t.nama_tipe SEPARATOR ", ") AS tipe_list')
            ->from('mekanik_master m')
            ->join('mekanik_tipe_kendaraan mt', 'mt.id_mekanik = m.id_mekanik', 'left')
            ->join('tipe_kendaraan t',           't.id_tipe_kendaraan = mt.id_tipe_kendaraan', 'left');

        if (!empty($filter['search'])) {
            $this->db->group_start()
                ->like('m.nama',       $filter['search'])
                ->or_like('m.perusahaan', $filter['search'])
                ->group_end();
        }
        if (isset($filter['is_active']) && $filter['is_active'] !== '') {
            $this->db->where('m.is_active', $filter['is_active']);
        }

        $this->db->group_by('m.id_mekanik')->order_by('m.nama', 'ASC');
        return $this->db->get()->result();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Get by ID
    // ─────────────────────────────────────────────────────────────────────────
    public function get_by_id($id)
    {
        return $this->db->where('id_mekanik', $id)->get('mekanik_master')->row();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Tipe kendaraan yang dikuasai mekanik tertentu
    // Return: array [{id_tipe_kendaraan, nama_tipe, kode_tipe}]
    // ─────────────────────────────────────────────────────────────────────────
    public function get_tipe_by_mekanik($id_mekanik)
    {
        return $this->db
            ->select('t.id_tipe_kendaraan, t.nama_tipe, t.kode_tipe')
            ->from('mekanik_tipe_kendaraan mt')
            ->join('tipe_kendaraan t', 't.id_tipe_kendaraan = mt.id_tipe_kendaraan')
            ->where('mt.id_mekanik', (int) $id_mekanik)
            ->order_by('t.nama_tipe', 'ASC')
            ->get()->result();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Mekanik capable untuk tipe kendaraan — by nama (compat untuk kode lama)
    // ─────────────────────────────────────────────────────────────────────────
    public function get_by_jenis($nama_tipe, $only_active = true)
    {
        $this->db
            ->select('m.*')
            ->from('mekanik_master m')
            ->join('mekanik_tipe_kendaraan mt', 'mt.id_mekanik = m.id_mekanik')
            ->join('tipe_kendaraan t',           't.id_tipe_kendaraan = mt.id_tipe_kendaraan')
            ->where('t.nama_tipe', $nama_tipe);
        if ($only_active) $this->db->where('m.is_active', 1);
        $this->db->order_by('m.nama', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Mekanik capable by id_tipe_kendaraan — lebih efisien (FK langsung, no JOIN extra).
     * Gunakan ini untuk kode baru.
     */
    public function get_by_tipe_id($id_tipe_kendaraan, $only_active = true)
    {
        $this->db
            ->select('m.*')
            ->from('mekanik_master m')
            ->join('mekanik_tipe_kendaraan mt', 'mt.id_mekanik = m.id_mekanik')
            ->where('mt.id_tipe_kendaraan', (int) $id_tipe_kendaraan);
        if ($only_active) $this->db->where('m.is_active', 1);
        $this->db->order_by('m.nama', 'ASC');
        return $this->db->get()->result();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Cek konflik jadwal mekanik — selisih minimal 60 menit
    // ─────────────────────────────────────────────────────────────────────────
    public function cek_konflik_mekanik($id_mekanik, $tanggal_uji, $exclude_id = null)
    {
        $dt = $this->db->escape_str(date('Y-m-d H:i:s', strtotime($tanggal_uji)));

        $this->db->from('jadwal_uji j')
            ->where('j.id_mekanik_master', (int) $id_mekanik)
            ->where('j.status', 'scheduled')
            ->where("ABS(TIMESTAMPDIFF(MINUTE, j.tanggal_uji, '{$dt}')) < 60", null, false);

        if (!empty($exclude_id) && (int) $exclude_id > 0) {
            $this->db->where('j.id_jadwal !=', (int) $exclude_id);
        }
        return $this->db->count_all_results() > 0;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CRUD
    // ─────────────────────────────────────────────────────────────────────────
    public function insert($data, $tipe_arr = [])
    {
        $this->db->trans_start();
        $this->db->insert('mekanik_master', $data);
        $id = $this->db->insert_id();
        if ($id && !empty($tipe_arr)) $this->_save_tipe($id, $tipe_arr);
        $this->db->trans_complete();
        return $this->db->trans_status() ? $id : false;
    }

    public function update($id, $data, $tipe_arr = [])
    {
        $this->db->trans_start();
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id_mekanik', $id)->update('mekanik_master', $data);
        $this->_save_tipe($id, $tipe_arr); // _save_tipe sudah handle DELETE + INSERT sendiri
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function toggle_active($id)
    {
        $row = $this->get_by_id($id);
        if (!$row) return false;
        return $this->db->where('id_mekanik', $id)
            ->update('mekanik_master', ['is_active' => $row->is_active ? 0 : 1]);
    }

    public function delete($id)
    {
        return $this->db->where('id_mekanik', $id)->delete('mekanik_master');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private: simpan tipe batch — terima array id_tipe_kendaraan (int)
    // ─────────────────────────────────────────────────────────────────────────
    private function _save_tipe($id_mekanik, array $tipe_arr)
    {
        // Hapus semua tipe lama dulu
        $this->db->where('id_mekanik', $id_mekanik)->delete('mekanik_tipe_kendaraan');

        $batch = [];
        foreach (array_unique($tipe_arr) as $id_tipe) {
            $id_tipe = (int) $id_tipe;
            if ($id_tipe > 0) {
                $batch[] = [
                    'id_mekanik'        => $id_mekanik,
                    'id_tipe_kendaraan' => $id_tipe,
                ];
            }
        }
        if (!empty($batch)) {
            $this->db->insert_batch('mekanik_tipe_kendaraan', $batch);
        }
        // Jika $batch kosong, berarti tidak ada tipe dipilih — tidak apa-apa
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Static list nama tipe — compat untuk form mekanik yang masih pakai nama
    // Setelah migrasi, form mekanik idealnya pakai dropdown dari tipe_kendaraan DB
    // ─────────────────────────────────────────────────────────────────────────
    public static function jenis_list()
    {
        // Deprecated: gunakan tipe_kendaraan DB via TipeKendaraan::get_dropdown()
        return [
            'Light Vehicle',
            'Light Truck',
            'Bus',
            'Bus Manhaul',
            'Fuel Truck',
            'Dump Truck',
            'Crane Truck',
            'ADT',
            'Haul Truck',
            'Forklift',
            'Excavator',
            'Compactor',
            'Motor Grader',
            'Wheel Loader',
            'Bulldozer',
            'Crawler',
            'Drill Rig',
            'Jumbo',
            'Equipment Support',
            'Water Truck',
        ];
    }
}
