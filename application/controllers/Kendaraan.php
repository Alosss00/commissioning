<?php

/**
 * Controller Kendaraan
 * 
 * Pengelolaan data master unit kendaraan yang telah lulus komisioning (stiker keluar / ACC KTT).
 * Fitur:
 * - Halaman daftar unit kendaraan komisioning
 * - DataTables AJAX server-side (filtering tipe unit, status stiker aktif/expired, unit baru/lama)
 * - Detail informasi kendaraan & riwayat stiker (batch-fetched)
 * - Ekspor data komisioning ke Excel
 * - Penghapusan unit kendaraan aman (validasi pengajuan aktif)
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Kendaraan extends CI_Controller
{
    /**
     * Konstruktor Controller Kendaraan
     * Memuat model, library session & upload, helper, dan memverifikasi otentikasi login.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Kendaraan_model', 'kendaraan_model');
        $this->load->library(['session', 'form_validation', 'upload']);
        $this->load->helper(['url', 'form']);

        if (!$this->session->userdata('id_user')) {
            redirect('auth/login');
        }
    }

    /**
     * Halaman Utama Data Kendaraan Commissioning
     * 
     * @return void Render view kendaraan/index
     */
    public function index()
    {
        $data['title'] = 'Data Kendaraan Commissioning';
        $data['user']  = $this->session->userdata();

        $this->load->view('templates/header',  $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('kendaraan/index',   $data);
        $this->load->view('templates/footer',  $data);
    }

    /**
     * Endpoint AJAX DataTables Server-Side Kendaraan Lulus Komisioning.
     * Menggunakan eager batch fetching `get_stiker_info_batch` untuk mencegah N+1 query.
     * 
     * @return void Output JSON DataTables + CSRF token
     */
    public function get_data()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $draw   = $this->input->post('draw');
        $start  = max(0, (int) $this->input->post('start'));
        $length = (int) $this->input->post('length');
        if ($length <= 0) {
            $length = 25;
        }
        $length = min($length, 500);

        $filters = [
            'search'          => $this->input->post('search')['value'],
            'jenis_kendaraan' => $this->input->post('filter_jenis'),
            'is_unit_baru'    => $this->input->post('filter_unit'),
        ];
        $filter_stiker = $this->input->post('filter_stiker');

        // Ambil data kendaraan yang pernah lulus komisioning
        $total    = $this->kendaraan_model->count_all_lulus($filters);
        $filtered = $this->kendaraan_model->count_filtered_lulus($filters);
        $rows     = $this->kendaraan_model->get_datatable_lulus($start, $length, $filters);

        // Optimasi N+1 Query: Batch fetching data stiker dalam 1 kali query
        $stiker_map = $this->kendaraan_model->get_stiker_info_batch(
            array_column($rows, 'id_kendaraan')
        );

        // Post-filter status stiker jika diset pada UI
        if (!empty($filter_stiker)) {
            $rows = array_filter($rows, function ($row) use ($stiker_map, $filter_stiker) {
                $stiker = $stiker_map[$row->id_kendaraan] ?? null;
                $sisa   = $stiker ? (int) $stiker->sisa_hari : null;
                switch ($filter_stiker) {
                    case 'expired':
                        return $stiker && $sisa < 0;
                    case 'hampir':
                        return $stiker && $sisa >= 0 && $sisa <= 30;
                    case 'aktif':
                        return $stiker && $sisa > 30;
                    case 'tanpa_stiker':
                        return !$stiker;
                    default:
                        return true;
                }
            });
            $filtered = count($rows);
            $rows     = array_values($rows);
        }

        // Susun baris response tabel
        $data = [];
        $no   = $start + 1;

        foreach ($rows as $r) {
            $stiker = $stiker_map[$r->id_kendaraan] ?? null;
            $badge_stiker = $this->_badge_stiker($stiker);

            $badge_unit = $r->is_unit_baru
                ? '<span class="badge bg-primary me-1">Unit Baru</span>'
                : '<span class="badge bg-secondary me-1">Unit Lama</span>';

            $tipe_akses_badge = badge_tipe_akses($r->tipe_akses ?? '');

            $aksi = '
              <div class="d-flex gap-1">
                <button class="btn btn-sm btn-outline-info py-0 btn-detail" data-id="' . $r->id_kendaraan . '" title="Detail"><i class="bi bi-eye"></i></button>
                ' . ($this->session->userdata('role') == 1 ? '<button class="btn btn-sm btn-outline-danger py-0 btn-delete" data-id="' . $r->id_kendaraan . '" data-nopol="' . html_escape($r->no_polisi) . '" title="Hapus"><i class="bi bi-trash"></i></button>' : '') . '
              </div>';

            $data[] = [
                'no'              => $no++,
                'no_polisi'       => '<strong>' . html_escape($r->no_polisi) . '</strong>',
                'nomor_unit'      => '<strong>' . html_escape($r->nomor_unit ?: '-') . '</strong>',
                'jenis_kendaraan' => html_escape($r->jenis_kendaraan ?? '-'),
                'merk_tipe'       => html_escape($r->merk) . ' ' . html_escape($r->tipe),
                'tahun'           => html_escape($r->tahun ?: '-'),
                'unit'            => $badge_unit,
                'is_unit_baru'    => $badge_unit,
                'sisa_stiker'     => $badge_stiker,
                'status_stiker'   => $badge_stiker,
                'perusahaan'      => html_escape($r->perusahaan),
                'tipe_akses'      => $tipe_akses_badge,
                'total_pengajuan' => '<span class="badge bg-light text-dark border">' . $r->total_pengajuan . ' kali</span>',
                'tgl_lulus'       => $r->tgl_lulus ? date('d/m/Y', strtotime($r->tgl_lulus)) : '<span class="text-muted small">—</span>',
                'aksi'            => $aksi,
            ];
        }

        echo json_encode([
            'draw'            => $draw,
            'recordsTotal'    => $total,
            'recordsFiltered' => $filtered,
            'data'            => $data,
            'csrf_hash'       => $this->security->get_csrf_hash(),
        ]);
    }

    /**
     * Endpoint AJAX untuk mengambil detail kendaraan & riwayat stiker.
     * 
     * @return void Response JSON status & data detail
     */
    public function get_by_id()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id        = (int) $this->input->post('id_kendaraan');
        $kendaraan = $this->kendaraan_model->get_by_id($id);

        if (!$kendaraan) {
            echo json_encode(['status' => 'error', 'message' => 'Kendaraan tidak ditemukan.']);
            return;
        }

        // Ambil data stiker aktif
        $stiker_map = $this->kendaraan_model->get_stiker_info_batch([$id]);
        $stiker     = $stiker_map[$id] ?? null;

        // Ambil riwayat pengajuan kendaraan
        $pengajuan_history = $this->db
            ->select('pu.*, sr.nomor_sticker, sr.tanggal_release, sr.tgl_expired')
            ->from('pengajuan_uji pu')
            ->join('sticker_release sr', 'sr.id_pengajuan = pu.id_pengajuan', 'left')
            ->where('pu.id_kendaraan', $id)
            ->order_by('pu.tanggal_pengajuan', 'DESC')
            ->get()->result();

        echo json_encode([
            'status' => 'success',
            'data'   => [
                'kendaraan' => $kendaraan,
                'stiker'    => $stiker,
                'history'   => $pengajuan_history,
            ],
            'csrf_hash' => $this->security->get_csrf_hash(),
        ]);
    }

    /**
     * Endpoint AJAX untuk menyajikan dropdown daftar kendaraan pada form pengajuan.
     * 
     * @return void Response JSON dropdown list
     */
    public function get_dropdown()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $rows = $this->kendaraan_model->get_all();
        echo json_encode(['status' => 'success', 'data' => $rows, 'csrf_hash' => $this->security->get_csrf_hash()]);
    }

    /**
     * Endpoint AJAX untuk menyajikan daftar tipe unik kendaraan untuk filter.
     * 
     * @return void Response JSON list jenis kendaraan
     */
    public function get_tipe_list()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $rows = $this->kendaraan_model->get_jenis_list();
        echo json_encode(['status' => 'success', 'data' => $rows, 'csrf_hash' => $this->security->get_csrf_hash()]);
    }

    /**
     * Endpoint AJAX Rekapitulasi Summary Stiker Kendaraan Komisioning.
     * 
     * @return void Response JSON summary statistik
     */
    public function get_rekap()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $rows       = $this->kendaraan_model->get_datatable_lulus(0, 99999);
        $stiker_map = $this->kendaraan_model->get_stiker_info_batch(
            array_column($rows, 'id_kendaraan')
        );

        $total        = count($rows);
        $stiker_aktif = 0;
        $stiker_hampir= 0;
        $stiker_exp   = 0;
        $tanpa_stiker = 0;
        $akan_exp_list= [];
        $per_jenis    = [];

        foreach ($rows as $r) {
            $stiker = $stiker_map[$r->id_kendaraan] ?? null;
            $jenis  = $r->jenis_kendaraan ?? 'Lainnya';

            if (!isset($per_jenis[$jenis])) {
                $per_jenis[$jenis] = ['total' => 0, 'aktif' => 0, 'expired' => 0];
            }
            $per_jenis[$jenis]['total']++;

            if (!$stiker) {
                $tanpa_stiker++;
            } else {
                $sisa = (int) $stiker->sisa_hari;
                if ($sisa < 0) {
                    $stiker_exp++;
                    $per_jenis[$jenis]['expired']++;
                } elseif ($sisa <= 30) {
                    $stiker_hampir++;
                    $stiker_aktif++;
                    $per_jenis[$jenis]['aktif']++;
                    $akan_exp_list[] = [
                        'no_polisi'       => $r->no_polisi,
                        'nomor_unit'      => $r->nomor_unit ?: '-',
                        'jenis_kendaraan' => $r->jenis_kendaraan ?? '-',
                        'nomor_sticker'   => $stiker->nomor_sticker,
                        'tgl_expired'     => date('d/m/Y', strtotime($stiker->tgl_expired)),
                        'sisa_hari'       => $sisa,
                    ];
                } else {
                    $stiker_aktif++;
                    $per_jenis[$jenis]['aktif']++;
                }
            }
        }

        echo json_encode([
            'status' => 'success',
            'data'   => [
                'total'         => $total,
                'aktif'         => $stiker_aktif,
                'hampir_exp'    => $stiker_hampir,
                'expired'       => $stiker_exp,
                'tanpa_stiker'  => $tanpa_stiker,
                'akan_expired'  => $akan_exp_list,
                'per_jenis'     => $per_jenis,
            ],
            'csrf_hash' => $this->security->get_csrf_hash(),
        ]);
    }

    /**
     * Endpoint AJAX Ekspor Data Kendaraan Komisioning ke Excel.
     * Menggunakan query JOIN ter-optimasikan.
     * 
     * @return void Output JSON data ekspor
     */
    public function get_all_for_export()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $rows = $this->db->query("
            SELECT
                k.nomor_unit                    AS unit_no,
                DATE_FORMAT(j.tanggal_uji, '%d/%m/%Y %H:%i') AS date_schedule,
                DATE_FORMAT(uk.tanggal_uji, '%d/%m/%Y')       AS date_conducted,
                COALESCE(uk.nama_inspektor, mm.nama, '-')     AS mechanic_inspector,
                u_ins.nama                                    AS ohs_inspector,
                COALESCE(uk.catatan_temuan, '-')              AS finding,
                CASE
                    WHEN uk.hasil = 'lulus' THEN 'Closed'
                    WHEN uk.hasil = 'tidak_lulus' THEN 'Open'
                    ELSE '-'
                END AS finding_status,
                CASE
                    WHEN pu.status IN ('stiker_keluar','acc_ktt') THEN 'PASS'
                    WHEN pu.status = 'tidak_lulus_inspeksi'       THEN 'FAIL'
                    ELSE UPPER(pu.status)
                END AS status,
                DATE_FORMAT(sr.tgl_expired, '%d/%m/%Y')       AS due_date,
                COALESCE(u_verif.nama, '-')                   AS followed_up_by,
                DATE_FORMAT(pu_verif.updated_at, '%d/%m/%Y')  AS complete_date,
                COALESCE(u_ktt.nama, u_ohs.nama, '-')         AS verified_by,
                COALESCE(pa_last.catatan, '-')                AS remark,
                pu.tipe_pengajuan                             AS request_type,
                pu.tipe_akses                                 AS access_type,
                t.nama_tipe                                   AS unit_type,
                k.merk                                        AS unit_brand,
                k.model_unit                                  AS unit_model,
                k.perusahaan                                  AS department_user,
                k.perusahaan                                  AS company_owner,
                DATE_FORMAT(sr.tgl_expired, '%d/%m/%Y')       AS date_expired
            FROM kendaraan k
            INNER JOIN tipe_kendaraan t ON t.id_tipe_kendaraan = k.id_tipe_kendaraan
            INNER JOIN pengajuan_uji pu ON pu.id_kendaraan = k.id_kendaraan
                AND pu.status IN ('stiker_keluar','acc_ktt')
            LEFT JOIN (
                SELECT id_pengajuan, MAX(id_sticker) AS max_id
                FROM sticker_release GROUP BY id_pengajuan
            ) sl ON sl.id_pengajuan = pu.id_pengajuan
            LEFT JOIN sticker_release sr ON sr.id_sticker = sl.max_id
            LEFT JOIN (
                SELECT id_pengajuan, MAX(id_jadwal) AS max_id
                FROM jadwal_uji GROUP BY id_pengajuan
            ) jl ON jl.id_pengajuan = pu.id_pengajuan
            LEFT JOIN jadwal_uji j ON j.id_jadwal = jl.max_id
            LEFT JOIN mekanik_master mm ON mm.id_mekanik = j.id_mekanik_master
            LEFT JOIN users u_ins ON u_ins.id_user = j.id_inspektor
            LEFT JOIN (
                SELECT id_pengajuan, MAX(id_uji) AS max_id
                FROM uji_kelayakan GROUP BY id_pengajuan
            ) ul ON ul.id_pengajuan = pu.id_pengajuan
            LEFT JOIN uji_kelayakan uk ON uk.id_uji = ul.max_id
            LEFT JOIN (
                SELECT id_pengajuan, MAX(id_perbaikan) AS max_id
                FROM perbaikan_unit GROUP BY id_pengajuan
            ) pbl ON pbl.id_pengajuan = pu.id_pengajuan
            LEFT JOIN perbaikan_unit pu_verif ON pu_verif.id_perbaikan = pbl.max_id
            LEFT JOIN users u_verif ON u_verif.id_user = pu_verif.id_verifikator
            LEFT JOIN (
                SELECT id_pengajuan, MAX(id_approval) AS max_id
                FROM pengajuan_approval WHERE level_approval IN ('ktt','acc_ktt') GROUP BY id_pengajuan
            ) pal_ktt ON pal_ktt.id_pengajuan = pu.id_pengajuan
            LEFT JOIN pengajuan_approval pa_ktt ON pa_ktt.id_approval = pal_ktt.max_id
            LEFT JOIN users u_ktt ON u_ktt.id_user = pa_ktt.id_approver
            LEFT JOIN (
                SELECT id_pengajuan, MAX(id_approval) AS max_id
                FROM pengajuan_approval WHERE level_approval IN ('ohs_supt','ohs') GROUP BY id_pengajuan
            ) pal_ohs ON pal_ohs.id_pengajuan = pu.id_pengajuan
            LEFT JOIN pengajuan_approval pa_ohs ON pa_ohs.id_approval = pal_ohs.max_id
            LEFT JOIN users u_ohs ON u_ohs.id_user = pa_ohs.id_approver
            LEFT JOIN (
                SELECT id_pengajuan, MAX(id_approval) AS max_id
                FROM pengajuan_approval GROUP BY id_pengajuan
            ) pal_last ON pal_last.id_pengajuan = pu.id_pengajuan
            LEFT JOIN pengajuan_approval pa_last ON pa_last.id_approval = pal_last.max_id
            ORDER BY k.nomor_unit ASC, pu.id_pengajuan DESC
        ")->result();

        echo json_encode([
            'status'    => 'success',
            'data'      => $rows,
            'csrf_hash' => $this->security->get_csrf_hash(),
        ]);
    }

    /**
     * Endpoint AJAX Hapus Data Kendaraan (Khusus Super Admin Role ID = 1).
     * 
     * @return void Response JSON status hapus
     */
    public function delete()
    {
        if (!$this->input->is_ajax_request()) show_404();

        if ($this->session->userdata('role') != 1) {
            echo json_encode(['status' => 'error', 'message' => 'Akses ditolak. Hanya Administrator yang dapat menghapus kendaraan.', 'csrf_hash' => $this->security->get_csrf_hash()]);
            return;
        }

        $id = (int) $this->input->post('id_kendaraan');

        // Memeriksa keterikatan data pengajuan aktif
        if ($this->kendaraan_model->has_pengajuan($id)) {
            echo json_encode(['status' => 'error', 'message' => 'Kendaraan memiliki riwayat pengajuan uji, tidak dapat dihapus.', 'csrf_hash' => $this->security->get_csrf_hash()]);
            return;
        }

        $this->db->trans_start();
        $this->kendaraan_model->delete($id);

        $this->db->insert('audit_log', [
            'id_user'    => $this->session->userdata('id_user'),
            'aksi'       => 'Hapus Kendaraan',
            'tabel'      => 'kendaraan',
            'id_ref'     => $id,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->db->trans_complete();

        echo json_encode([
            'status'    => $this->db->trans_status() ? 'success' : 'error',
            'message'   => $this->db->trans_status() ? 'Kendaraan berhasil dihapus.' : 'Gagal menghapus kendaraan.',
            'csrf_hash' => $this->security->get_csrf_hash(),
        ]);
    }

    /**
     * Helper privat pembentuk elemen HTML badge status stiker.
     * 
     * @param object|null $stiker Data stiker
     * @return string HTML Badge Stiker
     */
    private function _badge_stiker($stiker)
    {
        if (!$stiker) {
            return '<span class="badge bg-secondary">Belum Ada Stiker</span>';
        }
        $sisa = (int) $stiker->sisa_hari;
        $no   = html_escape($stiker->nomor_sticker);
        $exp  = date('d/m/Y', strtotime($stiker->tgl_expired));

        if ($sisa < 0) {
            return '<span class="badge bg-danger" title="Expired pada ' . $exp . '"><i class="bi bi-x-circle me-1"></i>Expired (' . $no . ')</span>';
        }
        if ($sisa <= 30) {
            return '<span class="badge bg-warning text-dark" title="Expired pada ' . $exp . '"><i class="bi bi-exclamation-triangle me-1"></i>Hampir Expired (' . $sisa . ' hr)</span>';
        }
        return '<span class="badge bg-success" title="Expired pada ' . $exp . '"><i class="bi bi-check-circle me-1"></i>Aktif (' . $no . ')</span>';
    }
}
