<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Model Kendaraan_model
 * 
 * Pengelolaan master kendaraan, tipe unit, data stiker komisioning,
 * dan penyediaan data terpaginasi DataTables (seluruh kendaraan maupun yang lulus uji).
 */
class Kendaraan_model extends CI_Model
{
    /**
     * Konstruktor Kendaraan_model
     * Inisialisasi library database CodeIgniter.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Helper privat penyusun query dasar untuk seluruh kendaraan.
     * 
     * @param array $filter Filter pencarian
     * @return void
     */
    private function _base_query($filter = [])
    {
        $this->db
            ->select('k.*, t.nama_tipe AS jenis_kendaraan, t.kode_tipe,
                      COUNT(pu.id_pengajuan) AS total_pengajuan')
            ->from('kendaraan k')
            ->join('tipe_kendaraan t',  't.id_tipe_kendaraan = k.id_tipe_kendaraan', 'left')
            ->join('pengajuan_uji pu',  'pu.id_kendaraan = k.id_kendaraan',          'left')
            ->group_by('k.id_kendaraan');

        $this->_apply_filter($filter);
    }

    /**
     * Helper privat penyusun query dasar khusus kendaraan yang telah lulus komisioning (memiliki stiker / ACC KTT).
     * Menggunakan subquery EXISTS untuk performa optimal.
     * 
     * @param array $filter Filter pencarian
     * @return void
     */
    private function _base_query_lulus($filter = [])
    {
        $this->db
            ->select('k.*, t.nama_tipe AS jenis_kendaraan, t.kode_tipe,
                      (SELECT pu_sub.tipe_akses FROM pengajuan_uji pu_sub WHERE pu_sub.id_kendaraan = k.id_kendaraan ORDER BY pu_sub.id_pengajuan DESC LIMIT 1) AS tipe_akses,
                      COUNT(pu_all.id_pengajuan) AS total_pengajuan,
                      MAX(pu_lulus.tgl_acc_ktt)  AS tgl_lulus')
            ->from('kendaraan k')
            ->join('tipe_kendaraan t', 't.id_tipe_kendaraan = k.id_tipe_kendaraan', 'left')
            ->join('pengajuan_uji pu_all', 'pu_all.id_kendaraan = k.id_kendaraan', 'left')
            ->join(
                'pengajuan_uji pu_lulus',
                "pu_lulus.id_kendaraan = k.id_kendaraan
                 AND pu_lulus.status IN ('stiker_keluar','acc_ktt','dicabut_ktt','menunggu_ktt_cabut')",
                'left'
            )
            ->where("EXISTS (
                SELECT 1 FROM pengajuan_uji pu_filter
                WHERE pu_filter.id_kendaraan = k.id_kendaraan
                  AND pu_filter.status IN ('stiker_keluar','acc_ktt','dicabut_ktt','menunggu_ktt_cabut')
            )", null, false)
            ->group_by('k.id_kendaraan');

        $this->_apply_filter($filter);
    }

    /**
     * Helper privat penerapan filter kata kunci dan tipe kendaraan.
     * 
     * @param array $filter Parameter filter
     * @return void
     */
    private function _apply_filter($filter = [])
    {
        // Filter soft delete: jangan tampilkan yang sudah dihapus kecuali diminta
        if (empty($filter['include_deleted'])) {
            $this->db->where('k.deleted_at IS NULL');
        }

        if (!empty($filter['search'])) {
            $kw = $filter['search'];
            $this->db->group_start()
                ->like('k.no_polisi',    $kw)
                ->or_like('t.nama_tipe', $kw)
                ->or_like('k.merk',      $kw)
                ->or_like('k.tipe',      $kw)
                ->or_like('k.nomor_unit', $kw)
                ->group_end();
        }

        if (!empty($filter['jenis_kendaraan'])) {
            if (is_numeric($filter['jenis_kendaraan'])) {
                $this->db->where('k.id_tipe_kendaraan', (int) $filter['jenis_kendaraan']);
            } else {
                $this->db->where('t.nama_tipe', $filter['jenis_kendaraan']);
            }
        }

        if (!empty($filter['perusahaan'])) {
            $this->db->where('LOWER(TRIM(k.perusahaan)) =', strtolower(trim((string)$filter['perusahaan'])));
        }

        if (isset($filter['is_unit_baru']) && $filter['is_unit_baru'] !== '') {
            $this->db->where('k.is_unit_baru', (int) $filter['is_unit_baru']);
        }
    }

    public function count_all($filter = [])
    {
        $this->_base_query($filter);
        return $this->db->count_all_results();
    }

    public function count_filtered($filter = [])
    {
        $this->_base_query($filter);
        return $this->db->count_all_results();
    }

    public function count_all_lulus($filter = [])
    {
        $this->_base_query_lulus($filter);
        return $this->db->count_all_results();
    }

    public function count_filtered_lulus($filter = [])
    {
        $this->_base_query_lulus($filter);
        return $this->db->count_all_results();
    }

    public function get_datatable($start, $length, $filter = [])
    {
        $this->_base_query($filter);
        $this->db->order_by('k.created_at', 'DESC')->limit((int) $length, (int) $start);
        return $this->db->get()->result();
    }

    public function get_datatable_lulus($start, $length, $filter = [])
    {
        $this->_base_query_lulus($filter);
        $this->db->order_by('tgl_lulus', 'DESC')->limit((int) $length, (int) $start);
        return $this->db->get()->result();
    }

    public function get_all($filter = [])
    {
        $this->db
            ->select('k.*, t.nama_tipe AS jenis_kendaraan, t.kode_tipe')
            ->from('kendaraan k')
            ->join('tipe_kendaraan t', 't.id_tipe_kendaraan = k.id_tipe_kendaraan', 'left');
        
        if (empty($filter['include_deleted'])) {
            $this->db->where('k.deleted_at IS NULL');
        }

        return $this->db->order_by('k.no_polisi', 'ASC')->get()->result();
    }

    public function get_by_id($id, $include_deleted = false)
    {
        $this->db
            ->select('k.*, t.nama_tipe AS jenis_kendaraan, t.kode_tipe, t.id_tipe_kendaraan,
                      (SELECT pu_sub.tipe_akses FROM pengajuan_uji pu_sub WHERE pu_sub.id_kendaraan = k.id_kendaraan ORDER BY pu_sub.id_pengajuan DESC LIMIT 1) AS tipe_akses')
            ->from('kendaraan k')
            ->join('tipe_kendaraan t', 't.id_tipe_kendaraan = k.id_tipe_kendaraan', 'left')
            ->where('k.id_kendaraan', (int) $id);

        if (!$include_deleted) {
            $this->db->where('k.deleted_at IS NULL');
        }

        return $this->db->get()->row();
    }

    public function is_no_polisi_exists($no_polisi, $exclude_id = null)
    {
        $this->db->where('no_polisi', $no_polisi);
        $this->db->where('deleted_at IS NULL');
        if (!empty($exclude_id)) {
            $this->db->where('id_kendaraan !=', (int) $exclude_id);
        }
        return $this->db->count_all_results('kendaraan') > 0;
    }

    public function is_nomor_unit_exists($nomor_unit, $exclude_id = null)
    {
        $this->db->where('nomor_unit', $nomor_unit);
        $this->db->where('deleted_at IS NULL');
        if (!empty($exclude_id)) {
            $this->db->where('id_kendaraan !=', (int) $exclude_id);
        }
        return $this->db->count_all_results('kendaraan') > 0;
    }

    public function insert($data)
    {
        $this->db->insert('kendaraan', $data);
        return $this->db->insert_id();
    }

    public function insert_kendaraan($data)
    {
        $this->db->insert('kendaraan', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        return $this->db->where('id_kendaraan', (int) $id)->update('kendaraan', $data);
    }

    public function update_kendaraan($id, $data)
    {
        return $this->db->where('id_kendaraan', (int) $id)->update('kendaraan', $data);
    }

    public function delete($id, $id_user = null)
    {
        $id_user = $id_user ?: (int) $this->session->userdata('id_user');
        return $this->db->where('id_kendaraan', (int) $id)->update('kendaraan', [
            'deleted_at' => date('Y-m-d H:i:s'),
            'deleted_by' => $id_user ?: null,
        ]);
    }

    public function restore($id)
    {
        return $this->db->where('id_kendaraan', (int) $id)->update('kendaraan', [
            'deleted_at' => null,
            'deleted_by' => null,
        ]);
    }

    public function has_pengajuan($id)
    {
        return $this->db->where('id_kendaraan', (int) $id)
            ->where('deleted_at IS NULL')
            ->count_all_results('pengajuan_uji') > 0;
    }

    /**
     * Mengambil kendaraan yang eligible untuk recommissioning (pernah lulus dan stiker expired/belum ada).
     * 
     * @return array List object kendaraan eligible
     */
    public function get_kendaraan_lulus_eligible()
    {
        return $this->db->query("
            SELECT DISTINCT
                k.*,
                t.nama_tipe  AS jenis_kendaraan,
                t.kode_tipe,
                sr.nomor_sticker,
                sr.tgl_expired,
                DATEDIFF(sr.tgl_expired, NOW()) AS sisa_hari,
                CASE
                    WHEN sr.tgl_expired IS NULL              THEN 'belum_ada'
                    WHEN DATEDIFF(sr.tgl_expired, NOW()) < 0 THEN 'expired'
                    ELSE 'aktif'
                END AS status_stiker
            FROM kendaraan k
            INNER JOIN tipe_kendaraan t ON t.id_tipe_kendaraan = k.id_tipe_kendaraan
            INNER JOIN pengajuan_uji pu
                ON pu.id_kendaraan = k.id_kendaraan
                AND pu.status IN ('stiker_keluar','lulus_inspeksi','diterima_ohs_supt','acc_ktt','diterima_admin_ohs')
            LEFT JOIN (
                SELECT sr2.*, pu2.id_kendaraan
                FROM sticker_release sr2
                INNER JOIN pengajuan_uji pu2 ON pu2.id_pengajuan = sr2.id_pengajuan
                INNER JOIN (
                    SELECT pu3.id_kendaraan, MAX(sr3.id_sticker) AS max_id
                    FROM sticker_release sr3
                    INNER JOIN pengajuan_uji pu3 ON pu3.id_pengajuan = sr3.id_pengajuan
                    GROUP BY pu3.id_kendaraan
                ) latest ON sr2.id_sticker = latest.max_id AND pu2.id_kendaraan = latest.id_kendaraan
            ) sr ON sr.id_kendaraan = k.id_kendaraan
            WHERE sr.tgl_expired IS NULL OR DATEDIFF(sr.tgl_expired, NOW()) < 0
            ORDER BY k.no_polisi ASC
        ")->result();
    }

    /**
     * Mengambil informasi stiker untuk batch ID kendaraan dalam 1 kali query (Bebas N+1).
     * 
     * @param array $id_list Array ID Kendaraan
     * @return array Map [id_kendaraan => object stiker info]
     */
    public function get_stiker_info_batch(array $id_list)
    {
        if (empty($id_list)) return [];

        $ids = implode(',', array_map('intval', $id_list));

        $rows = $this->db->query("
            SELECT
                pu.id_kendaraan,
                sr.nomor_sticker,
                sr.tanggal_release,
                sr.tgl_expired,
                sr.is_expired,
                sr.dicabut,
                sr.tgl_dicabut,
                DATEDIFF(sr.tgl_expired, NOW()) AS sisa_hari
            FROM sticker_release sr
            INNER JOIN pengajuan_uji pu ON pu.id_pengajuan = sr.id_pengajuan
            INNER JOIN (
                SELECT pu2.id_kendaraan, MAX(sr2.id_sticker) AS max_id
                FROM sticker_release sr2
                INNER JOIN pengajuan_uji pu2 ON pu2.id_pengajuan = sr2.id_pengajuan
                WHERE pu2.id_kendaraan IN ({$ids})
                GROUP BY pu2.id_kendaraan
            ) latest ON sr.id_sticker = latest.max_id AND pu.id_kendaraan = latest.id_kendaraan
            WHERE pu.id_kendaraan IN ({$ids})
        ")->result();

        $map = [];
        foreach ($rows as $r) {
            $map[$r->id_kendaraan] = $r;
        }
        return $map;
    }

    public function get_jenis_list()
    {
        return $this->db
            ->select('t.id_tipe_kendaraan, t.nama_tipe AS jenis_kendaraan')
            ->from('tipe_kendaraan t')
            ->join('kendaraan k',      'k.id_tipe_kendaraan = t.id_tipe_kendaraan', 'inner')
            ->join('pengajuan_uji pu', 'pu.id_kendaraan = k.id_kendaraan',          'inner')
            ->where_in('pu.status', ['stiker_keluar', 'acc_ktt', 'dicabut_ktt', 'menunggu_ktt_cabut'])
            ->group_by('t.id_tipe_kendaraan')
            ->order_by('t.nama_tipe', 'ASC')
            ->get()->result();
    }
}
