<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Audit extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('audit_model');
        $this->load->library('session');
        $this->load->helper(['url', 'html', 'auth', 'pengajuan']);
        
        if (!$this->session->userdata('id_user')) {
            redirect('auth/login');
        }
    }

    /**
     * Halaman utama Log Aktivitas Sistem
     */
    public function index()
    {
        $limit_allowed = [10, 25, 50, 100, 200, 500];
        $limit         = (int) ($this->input->get('limit') ?? 25);
        if (!in_array($limit, $limit_allowed)) {
            $limit = 25;
        }

        $page   = max(1, (int) ($this->input->get('page') ?? 1));
        $offset = ($page - 1) * $limit;

        $filter = [
            'tanggal' => trim($this->input->get('tanggal') ?? ''),
            'bulan'   => trim($this->input->get('bulan') ?? ''),
            'tahun'   => trim($this->input->get('tahun') ?? ''),
            'id_user' => trim($this->input->get('id_user') ?? ''),
            'aksi'    => trim($this->input->get('aksi') ?? ''),
            'search'  => trim($this->input->get('search') ?? ''),
            'limit'   => $limit,
            'offset'  => $offset,
        ];

        $total_logs = $this->audit_model->count_filtered_logs($filter);

        $data = [
            'title'        => 'Log Aktivitas Sistem',
            'user'         => $this->session->userdata(),
            'filter'       => $filter,
            'limit'        => $limit,
            'page'         => $page,
            'total_pages'  => ceil($total_logs / $limit),
            'logs'         => $this->audit_model->get_filtered_logs($filter),
            'total_logs'   => $total_logs,
            'list_users'   => $this->audit_model->get_all_users(),
            'list_actions' => $this->audit_model->get_distinct_actions(),
            'list_years'   => $this->audit_model->get_distinct_years(),
        ];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('audit/index', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Endpoint AJAX filter & pagination log aktivitas (Optimasi N+1: 1 Single Query Eager Join)
     */
    public function fetch_ajax()
    {
        $limit_allowed = [10, 25, 50, 100, 200, 500];
        $limit         = (int) ($this->input->post('limit') ?? 25);
        if (!in_array($limit, $limit_allowed)) {
            $limit = 25;
        }

        $page   = max(1, (int) ($this->input->post('page') ?? 1));
        $offset = ($page - 1) * $limit;

        $filter = [
            'tanggal' => trim($this->input->post('tanggal') ?? ''),
            'bulan'   => trim($this->input->post('bulan') ?? ''),
            'tahun'   => trim($this->input->post('tahun') ?? ''),
            'id_user' => trim($this->input->post('id_user') ?? ''),
            'aksi'    => trim($this->input->post('aksi') ?? ''),
            'search'  => trim($this->input->post('search') ?? ''),
            'limit'   => $limit,
            'offset'  => $offset,
        ];

        // Query N+1 Optimized (1 single SELECT join)
        $logs       = $this->audit_model->get_filtered_logs($filter);
        $total_logs = $this->audit_model->count_filtered_logs($filter);
        $total_pages = ceil($total_logs / $limit);

        $html_rows = '';
        if (empty($logs)) {
            $html_rows = '<tr><td colspan="5" class="text-center py-4 text-muted"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Tidak ada data log aktivitas ditemukan untuk filter ini.</td></tr>';
        } else {
            foreach ($logs as $index => $a) {
                $row_num        = $offset + $index + 1;
                $time_formatted = date('d M Y — H:i:s', strtotime($a->created_at));
                $time_ago       = time_ago($a->created_at);
                $user_name      = html_escape($a->nama_user ?? 'System / Anonymous');
                $color          = aksi_color($a->aksi);
                $label          = aksi_label($a->aksi, $user_name, $a->id_ref);

                $html_rows .= '<tr>';
                $html_rows .= '<td class="text-center small text-muted">' . $row_num . '</td>';
                $html_rows .= '<td><span class="fw-semibold text-dark d-block">' . $time_formatted . '</span><small class="text-muted">' . $time_ago . '</small></td>';
                $html_rows .= '<td><span class="badge bg-light text-dark border px-2 py-1"><i class="bi bi-person me-1"></i>' . $user_name . '</span></td>';
                $html_rows .= '<td><span class="badge bg-' . $color . ' text-white me-1 px-2 py-1">' . html_escape($a->aksi) . '</span></td>';
                $html_rows .= '<td>' . $label . '</td>';
                $html_rows .= '</tr>';
            }
        }

        echo json_encode([
            'status'      => 'success',
            'total'       => $total_logs,
            'page'        => $page,
            'total_pages' => $total_pages,
            'start_num'   => $total_logs > 0 ? $offset + 1 : 0,
            'end_num'     => min($offset + $limit, $total_logs),
            'html'        => $html_rows
        ]);
    }
}
