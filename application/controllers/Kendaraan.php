<?php

/**
 * Kendaraan Controller
 * Tujuan   : Mengelola data kendaraan yang SUDAH LULUS commissioning (stiker keluar/acc KTT)
 * Caller   : Route /kendaraan/*
 * Dependen : Kendaraan_model, tipe_kendaraan, sticker_release, pengajuan_uji, uji_kelayakan
 * Fungsi public:
 *   index()              — halaman daftar kendaraan lulus commissioning
 *   get_data()           — AJAX DataTables (server-side, filter stiker)
 *   get_by_id()          — AJAX detail kendaraan
 *   get_dropdown()       — AJAX dropdown form pengajuan
 *   get_tipe_list()      — AJAX list tipe untuk filter
 *   get_rekap()          — AJAX rekap summary + per jenis + akan expired
 *   get_all_for_export() — AJAX data lengkap untuk export Excel (format Commissioning)
 *   delete()             — AJAX hapus kendaraan (validasi pengajuan aktif)
 * Side effect:
 *   - READ: kendaraan, tipe_kendaraan, sticker_release, pengajuan_uji, uji_kelayakan
 *   - WRITE: kendaraan (delete), audit_log
 * Perubahan v2:
 *   - Tampilkan HANYA kendaraan yang lulus commissioning (status stiker_keluar / acc_ktt)
 *   - Export Excel mengikuti format Commissioning_2025-2026.xlsx:
 *     Unit No | Date Schedule | Date Conducted | Mechanic Inspector | OHS Inspector |
 *     Finding | Finding Status | Status | Due Date | Followed Up By | Complete Date |
 *     Verified By | Remark | Request Type | Access Type | Unit type | Unit Brand |
 *     Unit Model | Department User | Company Owner | Date Expired
 *   - Rekap & filter stiker tetap tersedia
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Kendaraan extends CI_Controller
{

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

    // =============================================
    // INDEX
    // =============================================
    public function index()
    {
        $data['title'] = 'Data Kendaraan Commissioning';
        $data['user']  = $this->session->userdata();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('kendaraan/index', $data);
        $this->load->view('templates/footer', $data);
    }

    // =============================================
    // AJAX — DataTables server-side
    // Hanya kendaraan yang pernah lulus commissioning (status stiker_keluar / acc_ktt)
    // =============================================
    public function get_data()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $draw   = $this->input->post('draw');
        $start  = $this->input->post('start');
        $length = $this->input->post('length');

        $filters = [
            'search'          => $this->input->post('search')['value'],
            'jenis_kendaraan' => $this->input->post('filter_jenis'),
            'is_unit_baru'    => $this->input->post('filter_unit'),
        ];
        $filter_stiker = $this->input->post('filter_stiker');

        // Hanya kendaraan lulus commissioning
        $total    = $this->kendaraan_model->count_all_lulus($filters);
        $filtered = $this->kendaraan_model->count_filtered_lulus($filters);
        $rows     = $this->kendaraan_model->get_datatable_lulus($start, $length, $filters);

        // Batch info stiker
        $stiker_map = $this->kendaraan_model->get_stiker_info_batch(
            array_column($rows, 'id_kendaraan')
        );

        // Post-filter stiker
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
                    case 'belum':
                        return !$stiker || empty($stiker->tgl_expired);
                    default:
                        return true;
                }
            });
            $rows = array_values($rows);
        }

        $data = [];
        $no   = $start + 1;

        foreach ($rows as $row) {
            $badge_unit = $row->is_unit_baru
                ? '<span class="badge bg-warning text-dark"><i class="bi bi-star-fill me-1"></i>Unit Baru</span>'
                : '<span class="badge bg-secondary text-white">Unit Lama</span>';

            $stiker    = $stiker_map[$row->id_kendaraan] ?? null;
            $sisa_html = $this->_render_sisa_stiker($stiker);

            $aksi =
                '<div class="d-flex gap-1 justify-content-center">'
                . '<button class="btn btn-sm btn-outline-primary py-0 btn-detail" data-id="' . $row->id_kendaraan . '" title="Detail"><i class="bi bi-eye"></i></button>'
                . '<button class="btn btn-sm btn-outline-danger py-0 btn-delete" data-id="' . $row->id_kendaraan . '" data-nopol="' . html_escape($row->no_polisi) . '" title="Hapus"><i class="bi bi-trash"></i></button>'
                . '</div>';

            $data[] = [
                'no'              => $no++,
                'no_polisi'       => '<span class="badge bg-dark font-monospace fs-6">' . html_escape($row->no_polisi) . '</span>',
                'nomor_unit'      => $row->nomor_unit
                    ? '<span class="badge bg-secondary font-monospace">' . html_escape($row->nomor_unit) . '</span>'
                    : '<span class="text-muted small">—</span>',
                'jenis_kendaraan' => html_escape($row->jenis_kendaraan),
                'merk_tipe'       => '<strong>' . html_escape($row->merk) . '</strong><br><small class="text-muted">' . html_escape($row->tipe) . '</small>',
                'tahun'           => $row->tahun,
                'unit'            => $badge_unit,
                'sisa_stiker'     => $sisa_html,
                'total_pengajuan' => '<span class="badge bg-primary rounded-pill">' . $row->total_pengajuan . '</span>',
                'tgl_lulus'       => $row->tgl_lulus ? date('d M Y', strtotime($row->tgl_lulus)) : '—',
                'aksi'            => $aksi,
            ];
        }

        $output = [
            'draw'            => (int) $draw,
            'recordsTotal'    => $total,
            'recordsFiltered' => $filtered,
            'data'            => $data,
        ];
        $output['csrf_hash'] = $this->security->get_csrf_hash();
        echo json_encode($output);
    }

    // Helper render badge stiker
    private function _render_sisa_stiker($stiker)
    {
        if (!$stiker || empty($stiker->tgl_expired)) {
            return '<span class="text-muted small">—</span>';
        }

        $sisa  = (int) $stiker->sisa_hari;
        $nomor = html_escape($stiker->nomor_sticker ?? '');
        $tgl   = date('d M Y', strtotime($stiker->tgl_expired));
        $nomor_tag = $nomor
            ? '<div class="text-muted mt-1" style="font-size:10px;">' . $nomor . '</div>'
            : '';

        if ($sisa < 0) {
            $hari_exp = abs($sisa);
            $warning_tag = ($hari_exp <= 7)
                ? '<div class="badge bg-dark text-white w-100 mt-1" style="font-size:9px;">'
                . '<i class="bi bi-bell-fill me-1"></i>Wajib Dicabut!</div>'
                : '';
            return '<span class="badge bg-danger text-white w-100 d-block">'
                . '<i class="bi bi-x-circle-fill me-1"></i>Expired ' . $hari_exp . ' hari lalu</span>'
                . $warning_tag . $nomor_tag;
        } elseif ($sisa === 0) {
            return '<span class="badge bg-danger text-white w-100 d-block">'
                . '<i class="bi bi-exclamation-octagon-fill me-1"></i>Jatuh Tempo Hari Ini</span>'
                . $nomor_tag;
        } elseif ($sisa <= 30) {
            return '<span class="badge bg-warning text-dark w-100 d-block">'
                . '<i class="bi bi-exclamation-triangle-fill me-1"></i>Sisa ' . $sisa . ' hari</span>'
                . $nomor_tag;
        } else {
            return '<span class="badge bg-success text-white w-100 d-block">'
                . '<i class="bi bi-check-circle-fill me-1"></i>Aktif — sisa ' . $sisa . ' hari</span>'
                . $nomor_tag;
        }
    }

    // =============================================
    // AJAX — Get detail
    // =============================================
    public function get_by_id()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $id  = (int) $this->input->post('id');
        $row = $this->kendaraan_model->get_by_id($id);
        if (!$row) {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
            return;
        }
        echo json_encode(['status' => 'success', 'data' => $row]);
    }

    // =============================================
    // AJAX — Rekap summary
    // =============================================
    public function get_rekap()
    {
        if (!$this->session->userdata('id_user')) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            return;
        }

        $detail = $this->input->get('detail') == '1';

        // Summary hanya kendaraan commissioning lulus
        $summary = $this->db->query("
            SELECT
                COUNT(DISTINCT k.id_kendaraan) AS total,
                SUM(k.is_unit_baru = 1) AS unit_baru,
                SUM(k.is_unit_baru = 0) AS unit_lama,
                (
                    SELECT COUNT(DISTINCT pu2.id_kendaraan)
                    FROM sticker_release sr2
                    INNER JOIN pengajuan_uji pu2 ON pu2.id_pengajuan = sr2.id_pengajuan
                    WHERE sr2.dicabut = 0 AND sr2.is_expired = 0 AND sr2.tgl_expired > NOW()
                ) AS stiker_aktif,
                (
                    SELECT COUNT(DISTINCT pu3.id_kendaraan)
                    FROM sticker_release sr3
                    INNER JOIN pengajuan_uji pu3 ON pu3.id_pengajuan = sr3.id_pengajuan
                    WHERE (sr3.is_expired = 1 OR sr3.tgl_expired <= NOW()) AND sr3.dicabut = 0
                ) AS stiker_expired,
                (
                    SELECT COUNT(DISTINCT pu4.id_kendaraan)
                    FROM pengajuan_uji pu4
                    WHERE pu4.status IN ('stiker_keluar','acc_ktt')
                      AND NOT EXISTS (
                          SELECT 1 FROM sticker_release sr5
                          INNER JOIN pengajuan_uji pu5 ON pu5.id_pengajuan = sr5.id_pengajuan
                          WHERE pu5.id_kendaraan = pu4.id_kendaraan
                      )
                ) AS belum_stiker
            FROM kendaraan k
            INNER JOIN (
                SELECT DISTINCT id_kendaraan FROM pengajuan_uji
                WHERE status IN ('stiker_keluar','acc_ktt')
            ) lulus ON lulus.id_kendaraan = k.id_kendaraan
        ")->row();

        $result = [
            'total'          => (int) ($summary->total          ?? 0),
            'unit_baru'      => (int) ($summary->unit_baru      ?? 0),
            'unit_lama'      => (int) ($summary->unit_lama      ?? 0),
            'stiker_aktif'   => (int) ($summary->stiker_aktif   ?? 0),
            'stiker_expired' => (int) ($summary->stiker_expired ?? 0),
            'belum_stiker'   => (int) ($summary->belum_stiker   ?? 0),
        ];

        if ($detail) {
            $per_jenis = $this->db->query("
                SELECT
                    COALESCE(t.nama_tipe,'Tidak Diketahui') AS jenis_kendaraan,
                    COUNT(DISTINCT k.id_kendaraan) AS total,
                    SUM(EXISTS(
                        SELECT 1 FROM sticker_release sr
                        INNER JOIN pengajuan_uji pu2 ON pu2.id_pengajuan = sr.id_pengajuan
                        WHERE pu2.id_kendaraan = k.id_kendaraan
                          AND sr.dicabut = 0 AND sr.is_expired = 0 AND sr.tgl_expired > NOW()
                    )) AS stiker_aktif,
                    SUM(EXISTS(
                        SELECT 1 FROM sticker_release sr2
                        INNER JOIN pengajuan_uji pu3 ON pu3.id_pengajuan = sr2.id_pengajuan
                        WHERE pu3.id_kendaraan = k.id_kendaraan
                          AND (sr2.is_expired = 1 OR sr2.tgl_expired <= NOW()) AND sr2.dicabut = 0
                    )) AS stiker_expired,
                    SUM(NOT EXISTS(
                        SELECT 1 FROM sticker_release sr3
                        INNER JOIN pengajuan_uji pu4 ON pu4.id_pengajuan = sr3.id_pengajuan
                        WHERE pu4.id_kendaraan = k.id_kendaraan
                    )) AS belum_ada_stiker
                FROM kendaraan k
                INNER JOIN (SELECT DISTINCT id_kendaraan FROM pengajuan_uji WHERE status IN ('stiker_keluar','acc_ktt')) lulus ON lulus.id_kendaraan = k.id_kendaraan
                LEFT JOIN tipe_kendaraan t ON t.id_tipe_kendaraan = k.id_tipe_kendaraan
                GROUP BY t.id_tipe_kendaraan, t.nama_tipe
                ORDER BY total DESC
            ")->result();

            $akan_expired = $this->db->query("
                SELECT k.no_polisi, t.nama_tipe AS jenis_kendaraan, k.merk, k.tipe, k.nomor_unit,
                       sr.nomor_sticker, sr.tgl_expired,
                       DATE_FORMAT(sr.tgl_expired,'%d %M %Y') AS tgl_expired_fmt,
                       DATEDIFF(sr.tgl_expired,NOW()) AS sisa_hari
                FROM sticker_release sr
                INNER JOIN pengajuan_uji pu ON pu.id_pengajuan = sr.id_pengajuan
                INNER JOIN kendaraan k ON k.id_kendaraan = pu.id_kendaraan
                INNER JOIN tipe_kendaraan t ON t.id_tipe_kendaraan = k.id_tipe_kendaraan
                INNER JOIN (
                    SELECT pu2.id_kendaraan, MAX(sr2.id_sticker) AS max_id
                    FROM sticker_release sr2
                    INNER JOIN pengajuan_uji pu2 ON pu2.id_pengajuan = sr2.id_pengajuan
                    GROUP BY pu2.id_kendaraan
                ) latest ON sr.id_sticker = latest.max_id AND pu.id_kendaraan = latest.id_kendaraan
                WHERE sr.tgl_expired BETWEEN NOW() AND DATE_ADD(NOW(),INTERVAL 30 DAY)
                  AND sr.dicabut = 0 AND sr.is_expired = 0
                ORDER BY sr.tgl_expired ASC
            ")->result();

            $result['per_jenis']    = $per_jenis;
            $result['akan_expired'] = $akan_expired;
        }

        echo json_encode(['status' => 'success', 'data' => $result]);
    }

    // =============================================
    // AJAX — Export data untuk format Commissioning Excel
    // Kolom: Unit No | Date Schedule | Date Conducted | Mechanic Inspector |
    //        OHS Inspector | Finding | Finding Status | Status | Due Date |
    //        Followed Up By | Complete Date | Verified By | Remark |
    //        Request Type | Access Type | Unit type | Unit Brand |
    //        Unit Model | Department User | Company Owner | Date Expired
    // =============================================
    public function get_all_for_export()
    {
        if (!$this->input->is_ajax_request()) show_404();
        if (!$this->session->userdata('id_user')) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            return;
        }

        // Query JOIN lengkap — 1 query, data commissioning lulus saja
        $rows = $this->db->query("
    SELECT
        k.nomor_unit,
        j.tanggal_uji          AS date_schedule,
        uk.tanggal_uji         AS date_conducted,
        mm.nama                AS mechanic_inspector,
        uk.nama_inspektor      AS ohs_inspector,
        uk.catatan_temuan      AS finding,
        CASE
            WHEN pu.status IN ('stiker_keluar','acc_ktt') THEN 'Closed'
            WHEN pu.status = 'tidak_lulus_inspeksi'       THEN 'Open'
            ELSE 'In Progress'
        END                    AS finding_status,
        CASE
            WHEN pu.status = 'stiker_keluar' THEN 'Stiker Keluar'
            WHEN pu.status = 'acc_ktt'       THEN 'ACC KTT'
            ELSE pu.status
        END                    AS status,
        pb.tgl_max_perbaikan   AS due_date,
        u_followup.nama        AS followed_up_by,
        pb.tgl_selesai         AS complete_date,
        u_verif.nama           AS verified_by,
        pu.tujuan              AS remark,
        pu.tipe_pengajuan      AS request_type,
        pu.tipe_akses          AS access_type,
        t.nama_tipe            AS unit_type,
        k.merk                 AS unit_brand,
        k.tipe                 AS unit_model,
        k.perusahaan           AS department_user,
        perus.nama_perusahaan  AS company_owner,
        sr.tgl_expired         AS date_expired
    FROM pengajuan_uji pu
    INNER JOIN kendaraan k        ON k.id_kendaraan       = pu.id_kendaraan
    INNER JOIN tipe_kendaraan t   ON t.id_tipe_kendaraan  = k.id_tipe_kendaraan
    LEFT JOIN (
        SELECT id_pengajuan, MAX(id_jadwal) AS id_jadwal
        FROM jadwal_uji GROUP BY id_pengajuan
    ) jl ON jl.id_pengajuan = pu.id_pengajuan
    LEFT JOIN jadwal_uji j        ON j.id_jadwal          = jl.id_jadwal
    LEFT JOIN mekanik_master mm   ON mm.id_mekanik        = j.id_mekanik_master
    LEFT JOIN (
        SELECT id_pengajuan, MAX(id_uji) AS id_uji
        FROM uji_kelayakan GROUP BY id_pengajuan
    ) ul ON ul.id_pengajuan = pu.id_pengajuan
    LEFT JOIN uji_kelayakan uk    ON uk.id_uji             = ul.id_uji
    LEFT JOIN (
        SELECT id_pengajuan, MAX(id_perbaikan) AS id_perbaikan
        FROM perbaikan_unit GROUP BY id_pengajuan
    ) pl ON pl.id_pengajuan = pu.id_pengajuan
    LEFT JOIN perbaikan_unit pb   ON pb.id_perbaikan       = pl.id_perbaikan
    LEFT JOIN users u_followup    ON u_followup.id_user    = pu.id_pemohon
    LEFT JOIN users u_verif       ON u_verif.id_user       = pb.id_verifikator
    LEFT JOIN (
        SELECT id_pengajuan, MAX(id_sticker) AS id_sticker
        FROM sticker_release GROUP BY id_pengajuan
    ) sl ON sl.id_pengajuan = pu.id_pengajuan
    LEFT JOIN sticker_release sr  ON sr.id_sticker         = sl.id_sticker
    LEFT JOIN perusahaan perus    ON perus.nama_perusahaan  = k.perusahaan
    WHERE pu.status IN ('stiker_keluar','acc_ktt')
    ORDER BY t.nama_tipe ASC, k.nomor_unit ASC, pu.id_pengajuan DESC
")->result();

        // Format nilai agar lebih bersih untuk Excel
        $clean = [];
        foreach ($rows as $r) {
            $clean[] = [
                'unit_no'           => $r->nomor_unit          ?? '',
                'date_schedule'     => $r->date_schedule       ? date('d/m/Y', strtotime($r->date_schedule))   : '',
                'date_conducted'    => $r->date_conducted      ? date('d/m/Y', strtotime($r->date_conducted))  : '',
                'mechanic_inspector' => $r->mechanic_inspector  ?? '',
                'ohs_inspector'     => $r->ohs_inspector       ?? '',
                'finding'           => $r->finding             ?? '',
                'finding_status'    => $r->finding_status      ?? '',
                'status'            => $r->status              ?? '',
                'due_date'          => $r->due_date            ? date('d/m/Y', strtotime($r->due_date))        : '',
                'followed_up_by'    => $r->followed_up_by      ?? '',
                'complete_date'     => $r->complete_date       ? date('d/m/Y', strtotime($r->complete_date))   : '',
                'verified_by'       => $r->verified_by         ?? '',
                'remark'            => $r->remark              ?? '',
                'request_type'      => $r->request_type === 'new_commissioning' ? 'New Commissioning' : 'Recommissioning',
                'access_type'       => ucfirst($r->access_type ?? ''),
                'unit_type'         => $r->unit_type           ?? '',
                'unit_brand'        => $r->unit_brand          ?? '',
                'unit_model'        => $r->unit_model          ?? '',
                'department_user'   => $r->department_user     ?? '',
                'company_owner'     => $r->company_owner       ?? '',
                'date_expired'      => $r->date_expired        ? date('d/m/Y', strtotime($r->date_expired))    : '',
            ];
        }

        echo json_encode(['status' => 'success', 'data' => $clean]);
    }

    // =============================================
    // AJAX — Delete
    // =============================================
    public function delete()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $id = (int) $this->input->post('id');

        if ($this->kendaraan_model->has_pengajuan($id)) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Kendaraan tidak dapat dihapus karena memiliki riwayat pengajuan.',
            ]);
            return;
        }

        $this->kendaraan_model->delete($id);
        $this->_audit('hapus_kendaraan', 'kendaraan', $id);
        echo json_encode(['status' => 'success', 'message' => 'Kendaraan berhasil dihapus.']);
    }

    // =============================================
    // AJAX — Dropdown (untuk form pengajuan recommissioning)
    // =============================================
    public function get_dropdown()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $rows = $this->kendaraan_model->get_all();
        $data = [];
        foreach ($rows as $row) {
            $data[] = [
                'id'   => $row->id_kendaraan,
                'text' => $row->no_polisi . ' — ' . $row->merk . ' ' . $row->tipe . ' (' . $row->jenis_kendaraan . ')',
            ];
        }
        echo json_encode(['results' => $data]);
    }

    // =============================================
    // AJAX — Get tipe list untuk filter
    // =============================================
    public function get_tipe_list()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $rows = $this->db
            ->select('t.id_tipe_kendaraan, t.nama_tipe AS jenis_kendaraan')
            ->from('tipe_kendaraan t')
            ->join('kendaraan k',      'k.id_tipe_kendaraan = t.id_tipe_kendaraan', 'inner')
            ->join('pengajuan_uji pu', 'pu.id_kendaraan = k.id_kendaraan',          'inner')
            ->where_in('pu.status', ['stiker_keluar', 'acc_ktt'])
            ->group_by('t.id_tipe_kendaraan')
            ->order_by('t.nama_tipe', 'ASC')
            ->get()->result();
        echo json_encode(['status' => 'success', 'data' => $rows]);
    }

    // =============================================
    // Helper: Audit Log
    // =============================================
    private function _audit($aksi, $tabel, $id_ref)
    {
        $this->db->insert('audit_log', [
            'id_user'    => $this->session->userdata('id_user'),
            'aksi'       => $aksi,
            'tabel'      => $tabel,
            'id_ref'     => $id_ref,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
