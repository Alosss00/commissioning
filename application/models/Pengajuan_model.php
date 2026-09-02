<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Model Pengajuan_model
 * 
 * Pengelolaan data pengajuan uji kelayakan unit kendaraan/peralatan.
 * Menyediakan fungsionalitas pencarian datatable, agregasi status, detail pengajuan,
 * riwayat approval, jadwal uji, hasil uji, perbaikan, serta query data untuk ekspor history.
 */
class Pengajuan_model extends CI_Model
{
    /**
     * Konstruktor Pengajuan_model
     * Inisialisasi library database CodeIgniter.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Helper privat penyusun dasar klausa JOIN dan WHERE untuk filter pengajuan.
     * Menggabungkan tabel pengajuan_uji, kendaraan, tipe_kendaraan, dan users (eager loading).
     * 
     * @param array $filters Filter opsional (status, jenis, tgl_dari, tgl_sampai, id_pemohon, departemen, search)
     * @return void
     */
    private function _base_query($filters = [])
    {
        $this->db->select(
            'pu.*, '
                . 'k.no_polisi, t.nama_tipe AS jenis_kendaraan, k.merk, k.tipe, k.tahun, '
                . 'k.is_unit_baru, k.nomor_unit, k.model_unit, k.perusahaan, '
                . 'u.nama AS nama_pemohon, u.email AS email_user'
        );
        $this->db->from('pengajuan_uji pu');
        $this->db->join('kendaraan k',        'k.id_kendaraan = pu.id_kendaraan',          'left');
        $this->db->join('tipe_kendaraan t',   't.id_tipe_kendaraan = k.id_tipe_kendaraan', 'left');
        $this->db->join('users u',            'u.id_user = pu.id_pemohon',                 'left');

        // Filter status spesifik atau pembatasan tahapan approval
        if (!empty($filters['status'])) {
            if (!empty($filters['allowed_statuses']) && is_array($filters['allowed_statuses'])) {
                if (in_array($filters['status'], $filters['allowed_statuses'], true)) {
                    $this->db->where('pu.status', $filters['status']);
                } else {
                    $this->db->where('1 = 0', null, false);
                }
            } else {
                $this->db->where('pu.status', $filters['status']);
            }
        } elseif (!empty($filters['status_in']) && is_array($filters['status_in'])) {
            if (!empty($filters['allowed_statuses']) && is_array($filters['allowed_statuses'])) {
                $intersect = array_intersect($filters['status_in'], $filters['allowed_statuses']);
                if (!empty($intersect)) {
                    $this->db->where_in('pu.status', array_values($intersect));
                } else {
                    $this->db->where('1 = 0', null, false);
                }
            } else {
                $this->db->where_in('pu.status', $filters['status_in']);
            }
        } elseif (!empty($filters['allowed_statuses']) && is_array($filters['allowed_statuses'])) {
            $this->db->where_in('pu.status', $filters['allowed_statuses']);
        }
        if (!empty($filters['jenis']))       $this->db->where('t.nama_tipe', $filters['jenis']);
        if (!empty($filters['tgl_dari']))    $this->db->where('DATE(pu.tanggal_pengajuan) >=', $filters['tgl_dari']);
        if (!empty($filters['tgl_sampai'])) $this->db->where('DATE(pu.tanggal_pengajuan) <=', $filters['tgl_sampai']);

        if (!empty($filters['scope_dept_pemohon'])) {
            $id_pem = (int) ($filters['scope_dept_pemohon']['id_pemohon'] ?? 0);
            $dept   = trim((string)($filters['scope_dept_pemohon']['departemen'] ?? ''));

            $this->db->group_start();
            $has_cond = false;
            if ($id_pem > 0) {
                $this->db->where('pu.id_pemohon', $id_pem);
                $has_cond = true;
            }
            if (!empty($dept)) {
                $dept_clean = strtolower($dept);
                if ($has_cond) {
                    $this->db->or_where('LOWER(TRIM(k.perusahaan)) =', $dept_clean);
                } else {
                    $this->db->where('LOWER(TRIM(k.perusahaan)) =', $dept_clean);
                    $has_cond = true;
                }
                $this->db->or_where('LOWER(TRIM(u.departemen)) =', $dept_clean);
            }
            $this->db->group_end();
        } elseif (!empty($filters['departemen'])) {
            $dept_clean = strtolower(trim((string)$filters['departemen']));
            $this->db->group_start()
                ->where('LOWER(TRIM(k.perusahaan)) =', $dept_clean)
                ->or_where('LOWER(TRIM(u.departemen)) =', $dept_clean)
                ->group_end();
        } elseif (!empty($filters['id_pemohon'])) {
            $this->db->where('pu.id_pemohon', (int) $filters['id_pemohon']);
        }
        
        // Filter soft delete: jika filter status adalah 'trash' / 'deleted', ambil data terhapus saja
        if (!empty($filters['only_deleted']) || ($filters['status'] ?? '') === 'trash' || ($filters['status'] ?? '') === 'deleted') {
            $this->db->where('pu.deleted_at IS NOT NULL');
        } elseif (empty($filters['include_deleted'])) {
            $this->db->where('pu.deleted_at IS NULL');
        }

        // Filter kata kunci pencarian global
        if (!empty($filters['search'])) {
            $kw = $filters['search'];
            $this->db->group_start();
            $this->db->like('k.no_polisi',    $kw);
            $this->db->or_like('k.nomor_unit', $kw);
            $this->db->or_like('u.nama',       $kw);
            $this->db->or_like('t.nama_tipe',  $kw);
            $this->db->or_like('k.merk',       $kw);
            $this->db->or_like('k.tipe',       $kw);
            $this->db->group_end();
        }
    }

    /**
     * Menghitung total data pengajuan berdasarkan hak akses (scoping saja).
     * Digunakan oleh DataTables untuk nilai recordsTotal.
     * 
     * @param array $filters Filter scoping (id_pemohon, departemen)
     * @return int Jumlah total record yang berhak diakses user
     */
    public function count_all($filters = [])
    {
        $scope_keys = ['id_pemohon', 'departemen', 'scope_dept_pemohon', 'status', 'status_in', 'allowed_statuses', 'include_deleted'];
        $scope_only = array_intersect_key($filters, array_flip($scope_keys));
        $this->_base_query($scope_only);
        return $this->db->count_all_results();
    }

    /**
     * Menghitung jumlah data pengajuan setelah diterapkan seluruh filter pencarian.
     * Digunakan oleh DataTables untuk nilai recordsFiltered.
     * 
     * @param array $filters Filter pencarian aktif
     * @return int Jumlah record hasil filter
     */
    public function count_filtered($filters = [])
    {
        $this->_base_query($filters);
        return $this->db->count_all_results();
    }

    /**
     * Mengambil daftar data pengajuan berpaginasi untuk DataTables.
     * 
     * @param int $start Offset baris awal
     * @param int $length Jumlah limit baris
     * @param array $filters Filter pencarian aktif
     * @return array List object data pengajuan
     */
    public function get_datatable($start, $length, $filters = [])
    {
        $this->_base_query($filters);

        $pending = $filters['pending_statuses'] ?? [];

        if (!empty($pending) && is_array($pending)) {
            $escaped = array_map(function($s) {
                return "'" . $this->db->escape_str($s) . "'";
            }, $pending);
            $in_str = implode(',', $escaped);

            // Prioritas 1: Pengajuan yang belum ditindaklanjuti (pending) diurutkan paling lama dahulu (ASC)
            // Prioritas 2: Pengajuan yang sudah ditindaklanjuti / riwayat diurutkan paling baru dahulu (DESC)
            $this->db->order_by("CASE WHEN pu.status IN ({$in_str}) THEN 1 ELSE 2 END", 'ASC', false);
            $this->db->order_by("CASE WHEN pu.status IN ({$in_str}) THEN pu.tanggal_pengajuan END", 'ASC', false);
            $this->db->order_by("pu.tanggal_pengajuan", 'DESC');
            $this->db->order_by("pu.id_pengajuan", 'DESC');
        } else {
            $this->db->order_by('pu.tanggal_pengajuan', 'DESC');
            $this->db->order_by('pu.id_pengajuan', 'DESC');
        }

        $this->db->limit((int) $length, (int) $start);
        return $this->db->get()->result();
    }

    /**
     * Mengambil detail lengkap 1 pengajuan uji berdasarkan ID.
     * 
     * @param int $id ID Pengajuan
     * @param array $filters Filter opsional hak akses (departemen, id_pemohon)
     * @return object|null Object data pengajuan detail
     */
    public function get_detail($id, $filters = [])
    {
        $this->db->select(
            'pu.*, '
                . 'k.no_polisi, k.id_tipe_kendaraan, t.nama_tipe AS jenis_kendaraan, '
                . 'k.merk, k.tipe, k.tahun, '
                . 'k.is_unit_baru, k.nomor_unit, k.model_unit, k.perusahaan, '
                . 'u.nama AS nama_pemohon, u.email AS email_user'
        );
        $this->db->from('pengajuan_uji pu');
        $this->db->join('kendaraan k',      'k.id_kendaraan = pu.id_kendaraan',          'left');
        $this->db->join('tipe_kendaraan t', 't.id_tipe_kendaraan = k.id_tipe_kendaraan', 'left');
        $this->db->join('users u',          'u.id_user = pu.id_pemohon',                 'left');
        $this->db->where('pu.id_pengajuan', (int) $id);

        if (empty($filters['include_deleted'])) {
            $this->db->where('pu.deleted_at IS NULL');
        }

        if (!empty($filters['scope_dept_pemohon'])) {
            $id_pem = (int) ($filters['scope_dept_pemohon']['id_pemohon'] ?? 0);
            $dept   = trim((string)($filters['scope_dept_pemohon']['departemen'] ?? ''));

            $this->db->group_start();
            if ($id_pem > 0) {
                $this->db->where('pu.id_pemohon', $id_pem);
            }
            if (!empty($dept)) {
                $dept_clean = strtolower($dept);
                $this->db->or_where('LOWER(TRIM(k.perusahaan)) =', $dept_clean);
                $this->db->or_where('LOWER(TRIM(u.departemen)) =', $dept_clean);
            }
            $this->db->group_end();
        } elseif (!empty($filters['departemen'])) {
            $dept_clean = strtolower(trim((string)$filters['departemen']));
            $this->db->group_start()
                ->where('LOWER(TRIM(k.perusahaan)) =', $dept_clean)
                ->or_where('LOWER(TRIM(u.departemen)) =', $dept_clean)
                ->group_end();
        } elseif (!empty($filters['id_pemohon'])) {
            $this->db->where('pu.id_pemohon', (int) $filters['id_pemohon']);
        }

        return $this->db->get()->row();
    }

    /**
     * Menyimpan data pengajuan uji baru.
     * 
     * @param array $data Field data pengajuan
     * @return int ID pengajuan yang baru dibuat
     */
    public function insert_pengajuan($data)
    {
        $this->db->insert('pengajuan_uji', $data);
        return $this->db->insert_id();
    }

    /**
     * Melakukan soft delete record pengajuan uji berdasarkan ID.
     * 
     * @param int $id ID Pengajuan
     * @param int|null $id_user User yang menghapus
     * @return bool Status keberhasilan soft delete
     */
    public function delete_pengajuan($id, $id_user = null)
    {
        $id_user = $id_user ?: (int) $this->session->userdata('id_user');
        return $this->db->where('id_pengajuan', (int) $id)->update('pengajuan_uji', [
            'deleted_at' => date('Y-m-d H:i:s'),
            'deleted_by' => $id_user ?: null,
        ]);
    }

    /**
     * Memulihkan (restore) record pengajuan uji yang sebelumnya di-soft delete.
     * 
     * @param int $id ID Pengajuan
     * @return bool Status keberhasilan pemulihan
     */
    public function restore_pengajuan($id)
    {
        return $this->db->where('id_pengajuan', (int) $id)->update('pengajuan_uji', [
            'deleted_at' => null,
            'deleted_by' => null,
        ]);
    }

    /**
     * Menyimpan data lampiran pengajuan uji.
     * 
     * @param array $data Record lampiran
     * @return int ID lampiran yang baru dibuat
     */
    public function insert_lampiran($data)
    {
        $this->db->insert('pengajuan_lampiran', $data);
        return $this->db->insert_id();
    }

    /**
     * Mengambil daftar lampiran dokumen suatu pengajuan.
     * 
     * @param int $id ID Pengajuan
     * @return array List object lampiran pengajuan
     */
    public function get_lampiran($id)
    {
        return $this->db->where('id_pengajuan', (int) $id)->get('pengajuan_lampiran')->result();
    }

    /**
     * Menyimpan catatan log approval pengajuan.
     * 
     * @param array $data Record data approval
     * @return int ID approval baru
     */
    public function insert_approval($data)
    {
        $this->db->insert('pengajuan_approval', $data);
        return $this->db->insert_id();
    }

    /**
     * Mengambil riwayat persetujuan/approval suatu pengajuan.
     * 
     * @param int $id ID Pengajuan
     * @return array List object approval beserta nama approver
     */
    public function get_approval($id)
    {
        $this->db->select('pa.*, u.nama AS nama_approver');
        $this->db->from('pengajuan_approval pa');
        $this->db->join('users u', 'u.id_user = pa.id_approver', 'left');
        $this->db->where('pa.id_pengajuan', (int) $id);
        $this->db->order_by('pa.id_approval', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Mengambil data jadwal inspeksi pengajuan uji.
     * 
     * @param int $id ID Pengajuan
     * @return object|null Object data jadwal uji
     */
    public function get_jadwal($id)
    {
        $this->db->select('j.*, u_dibuat.nama AS dibuat_oleh_nama,
            u_ins.nama     AS nama_inspektor_user,
            mm.nama        AS nama_mekanik_master,
            mm.perusahaan  AS perusahaan_mekanik');
        $this->db->from('jadwal_uji j');
        $this->db->join('users u_dibuat',    'u_dibuat.id_user = j.dibuat_oleh', 'left');
        $this->db->join('users u_ins',       'u_ins.id_user = COALESCE(j.id_inspektor, j.id_mekanik)', 'left');
        $this->db->join('mekanik_master mm', 'mm.id_mekanik = j.id_mekanik_master', 'left');
        $this->db->where('j.id_pengajuan', (int) $id);
        return $this->db->get()->row();
    }

    /**
     * Mengambil hasil pelaksanaan uji kelayakan (inspeksi).
     * 
     * @param int $id ID Pengajuan
     * @return object|null Object data uji kelayakan
     */
    public function get_uji($id)
    {
        $this->db->select('uk.*, u.nama AS nama_mekanik,
            mm.nama       AS nama_mekanik_master,
            mm.perusahaan AS perusahaan_mekanik_master');
        $this->db->from('uji_kelayakan uk');
        $this->db->join('users u',           'u.id_user = uk.id_mekanik',          'left');
        $this->db->join('mekanik_master mm', 'mm.id_mekanik = uk.id_mekanik_master', 'left');
        $this->db->where('uk.id_pengajuan', (int) $id);
        return $this->db->get()->row();
    }

    /**
     * Menhitung total pengajuan berdasarkan status tertentu.
     * 
     * @param string $status Kode status pengajuan
     * @return int Jumlah total record status
     */
    public function count_by_status($status)
    {
        return $this->db->where('status', $status)->count_all_results('pengajuan_uji');
    }

    /**
     * Mengambil data lengkap history pengajuan untuk ekspor laporan Excel.
     * Menggunakan SQL optimized JOIN subquery untuk menghindari N+1 query.
     * 
     * @param array $filters Filter pencarian ekspor (status, jenis, departemen, tgl_dari, tgl_sampai, search)
     * @return array List object hasil ekspor
     */
    public function get_export_history_data($filters = [])
    {
        $where_clauses = [];

        if (!empty($filters['status'])) {
            if (!empty($filters['allowed_statuses']) && is_array($filters['allowed_statuses'])) {
                if (in_array($filters['status'], $filters['allowed_statuses'], true)) {
                    $where_clauses[] = "pu.status = " . $this->db->escape($filters['status']);
                } else {
                    $where_clauses[] = "1 = 0";
                }
            } else {
                $where_clauses[] = "pu.status = " . $this->db->escape($filters['status']);
            }
        } elseif (!empty($filters['allowed_statuses']) && is_array($filters['allowed_statuses'])) {
            $esc_allowed = array_map([$this->db, 'escape'], $filters['allowed_statuses']);
            $where_clauses[] = "pu.status IN (" . implode(',', $esc_allowed) . ")";
        }
        if (!empty($filters['jenis'])) {
            $where_clauses[] = "t.nama_tipe = " . $this->db->escape($filters['jenis']);
        }
        if (!empty($filters['scope_dept_pemohon'])) {
            $id_pem   = (int) ($filters['scope_dept_pemohon']['id_pemohon'] ?? 0);
            $dept_val = trim((string)($filters['scope_dept_pemohon']['departemen'] ?? ''));

            $sub_conds = [];
            if ($id_pem > 0) {
                $sub_conds[] = "pu.id_pemohon = {$id_pem}";
            }
            if (!empty($dept_val)) {
                $dept_esc = $this->db->escape(strtolower($dept_val));
                $sub_conds[] = "LOWER(TRIM(k.perusahaan)) = {$dept_esc}";
                $sub_conds[] = "LOWER(TRIM(u_pem.departemen)) = {$dept_esc}";
            }
            if (!empty($sub_conds)) {
                $where_clauses[] = "(" . implode(" OR ", $sub_conds) . ")";
            }
        } elseif (!empty($filters['departemen'])) {
            $dept_esc = $this->db->escape(strtolower(trim((string)$filters['departemen'])));
            $where_clauses[] = "(LOWER(TRIM(k.perusahaan)) = {$dept_esc} OR LOWER(TRIM(u_pem.departemen)) = {$dept_esc})";
        } elseif (!empty($filters['id_pemohon'])) {
            $where_clauses[] = "pu.id_pemohon = " . (int)$filters['id_pemohon'];
        }
        if (!empty($filters['tgl_dari'])) {
            $where_clauses[] = "DATE(pu.tanggal_pengajuan) >= " . $this->db->escape($filters['tgl_dari']);
        }
        if (!empty($filters['tgl_sampai'])) {
            $where_clauses[] = "DATE(pu.tanggal_pengajuan) <= " . $this->db->escape($filters['tgl_sampai']);
        }
        if (!empty($filters['search'])) {
            $kw = $this->db->escape_like_str($filters['search']);
            $where_clauses[] = "(k.no_polisi LIKE '%$kw%' OR k.nomor_unit LIKE '%$kw%' OR u_pem.nama LIKE '%$kw%' OR t.nama_tipe LIKE '%$kw%' OR k.merk LIKE '%$kw%' OR k.perusahaan LIKE '%$kw%')";
        }

        $where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

        // Query kompleks dengan subquery agregasi MAX() untuk mengambil record terbaru dari masing-masing relasi
        $sql = "
            SELECT 
                pu.id_pengajuan,
                pu.tanggal_pengajuan,
                pu.tipe_pengajuan,
                pu.tipe_akses,
                pu.tujuan,
                pu.status,
                sr.nomor_sticker AS nomor_stiker,
                sr.tanggal_release AS tanggal_rilis_stiker,
                sr.tgl_expired AS tgl_expired_stiker,
                k.no_polisi,
                k.nomor_unit,
                k.merk,
                k.model_unit,
                k.tipe,
                k.tahun,
                k.perusahaan,
                t.nama_tipe AS jenis_kendaraan,
                u_pem.nama AS nama_pemohon,
                u_pem.email AS email_pemohon,
                j.tanggal_uji AS tgl_jadwal_rencana,
                j.created_at AS tgl_jadwal_dibuat,
                COALESCE(uk.nama_inspektor, mm.nama) AS nama_mekanik,
                COALESCE(uk.perusahaan_inspektor, mm.perusahaan) AS perusahaan_mekanik,
                uk.hasil AS hasil_inspeksi,
                uk.catatan_temuan AS catatan_inspeksi,
                uk.tanggal_uji AS tgl_inspeksi,
                COALESCE(pa_mgr.created_at, CASE WHEN pu.status NOT IN ('draft', 'pengajuan_baru', 'ditolak_manager') THEN pu.tanggal_pengajuan ELSE NULL END) AS tgl_approve_mgr,
                pa_mgr.catatan AS catatan_mgr,
                COALESCE(pa_ohs.created_at, CASE WHEN pu.status IN ('diterima_ohs_supt', 'acc_ktt', 'stiker_keluar') THEN sr.tanggal_release ELSE NULL END) AS tgl_approve_ohs,
                pa_ohs.catatan AS catatan_ohs
            FROM pengajuan_uji pu
            LEFT JOIN kendaraan k ON k.id_kendaraan = pu.id_kendaraan
            LEFT JOIN tipe_kendaraan t ON t.id_tipe_kendaraan = k.id_tipe_kendaraan
            LEFT JOIN users u_pem ON u_pem.id_user = pu.id_pemohon
            LEFT JOIN (
                SELECT id_pengajuan, MAX(id_jadwal) AS max_id_jadwal
                FROM jadwal_uji GROUP BY id_pengajuan
            ) jl ON jl.id_pengajuan = pu.id_pengajuan
            LEFT JOIN jadwal_uji j ON j.id_jadwal = jl.max_id_jadwal
            LEFT JOIN mekanik_master mm ON mm.id_mekanik = j.id_mekanik_master
            LEFT JOIN (
                SELECT id_pengajuan, MAX(id_uji) AS max_id_uji
                FROM uji_kelayakan GROUP BY id_pengajuan
            ) ul ON ul.id_pengajuan = pu.id_pengajuan
            LEFT JOIN uji_kelayakan uk ON uk.id_uji = ul.max_id_uji
            LEFT JOIN (
                SELECT id_pengajuan, MAX(id_sticker) AS max_id_sticker
                FROM sticker_release GROUP BY id_pengajuan
            ) sl ON sl.id_pengajuan = pu.id_pengajuan
            LEFT JOIN sticker_release sr ON sr.id_sticker = sl.max_id_sticker
            LEFT JOIN (
                SELECT id_pengajuan, MAX(id_approval) AS max_id_app
                FROM pengajuan_approval WHERE level_approval IN ('dept_manager', 'manager', 'diterima_manager') GROUP BY id_pengajuan
            ) pal_mgr ON pal_mgr.id_pengajuan = pu.id_pengajuan
            LEFT JOIN pengajuan_approval pa_mgr ON pa_mgr.id_approval = pal_mgr.max_id_app
            LEFT JOIN (
                SELECT id_pengajuan, MAX(id_approval) AS max_id_app
                FROM pengajuan_approval WHERE level_approval IN ('ohs_supt', 'ohs', 'admin_ohs') GROUP BY id_pengajuan
            ) pal_ohs ON pal_ohs.id_pengajuan = pu.id_pengajuan
            LEFT JOIN pengajuan_approval pa_ohs ON pa_ohs.id_approval = pal_ohs.max_id_app
            {$where_sql}
            ORDER BY " . (!empty($filters['pending_statuses']) && is_array($filters['pending_statuses']) ? "
                CASE WHEN pu.status IN ('" . implode("','", array_map([$this->db, 'escape_str'], $filters['pending_statuses'])) . "') THEN 1 ELSE 2 END ASC,
                CASE WHEN pu.status IN ('" . implode("','", array_map([$this->db, 'escape_str'], $filters['pending_statuses'])) . "') THEN pu.tanggal_pengajuan END ASC,
                pu.tanggal_pengajuan DESC, pu.id_pengajuan DESC
            " : "pu.tanggal_pengajuan DESC, pu.id_pengajuan DESC") . "
        ";

        return $this->db->query($sql)->result();
    }

    /**
     * Mengambil daftar data perbaikan unit beserta seluruh lampirannya (Optimasi Eager Loading Batch).
     * Menggunakan WHERE IN untuk mengambil seluruh lampiran perbaikan dalam 1 query (bebas N+1).
     * 
     * @param int $id_pengajuan ID Pengajuan
     * @return array List object perbaikan unit yang dilengkapi properti ->lampiran
     */
    public function get_perbaikan_with_lampiran($id_pengajuan)
    {
        // Langkah 1: Ambil seluruh baris perbaikan untuk pengajuan ini
        $rows = $this->db
            ->select('pu.*, u.nama AS nama_verifikator')
            ->from('perbaikan_unit pu')
            ->join('users u', 'u.id_user = pu.id_verifikator', 'left')
            ->where('pu.id_pengajuan', (int) $id_pengajuan)
            ->order_by('pu.id_perbaikan', 'ASC')
            ->get()->result();

        if (empty($rows)) {
            return $rows;
        }

        // Langkah 2: Kumpulkan seluruh ID perbaikan
        $ids = array_map(function ($r) { return $r->id_perbaikan; }, $rows);

        // Langkah 3: Ambil seluruh lampiran sekaligus dalam 1 query batch (Eager loading N+1 fix)
        $lampiran_rows = $this->db
            ->select('pl.*, ci.kriteria AS nama_item, ci.kategori AS kategori_item, ci.no_urut AS no_urut_item')
            ->from('perbaikan_lampiran pl')
            ->join('checklist_item ci', 'ci.id_item = pl.id_item', 'left')
            ->where_in('pl.id_perbaikan', $ids)
            ->order_by('pl.id_lampiran', 'ASC')
            ->get()
            ->result();

        // Langkah 4: Grouping lampiran berdasarkan id_perbaikan
        $grouped = [];
        foreach ($lampiran_rows as $l) {
            $grouped[$l->id_perbaikan][] = $l;
        }

        // Langkah 5: Petakan lampiran kembali ke masing-masing objek perbaikan
        foreach ($rows as $pb) {
            $pb->lampiran = $grouped[$pb->id_perbaikan] ?? [];
        }

        return $rows;
    }
}
