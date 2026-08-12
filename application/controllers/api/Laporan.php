<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controller API\Laporan
 *
 * Endpoint REST read-only untuk konsumsi eksternal (Excel Power Query, dsb.).
 * Autentikasi via header X-API-KEY — TIDAK menggunakan session browser.
 * Response selalu JSON, tidak pernah redirect ke halaman HTML.
 *
 * Route  : GET /api/laporan
 * Auth   : Header  X-API-KEY: <nilai laporan_api_key di api_env.php>
 * Format : { "status": "success"|"error", "meta": {...}, "data": [...] }
 */
class Laporan extends CI_Controller
{
    /**
     * Jumlah maksimum request per interval per IP (rate limiting sederhana).
     */
    const RATE_LIMIT      = 30;   // maks 30 request
    const RATE_WINDOW_SEC = 60;   // per 60 detik

    /**
     * @var string  API key yang valid (dibaca dari api_env.php via config)
     */
    private $_api_key;

    // -------------------------------------------------------------------------
    // CONSTRUCTOR
    // -------------------------------------------------------------------------

    public function __construct()
    {
        parent::__construct();

        // Muat model READ-ONLY khusus API ini
        $this->load->model('Api_laporan_model');

        // Muat API key dari konfigurasi (pola sama dengan cron_secret_key)
        $this->_api_key = $this->config->item('laporan_api_key') ?: '';
    }

    // -------------------------------------------------------------------------
    // PUBLIC ENDPOINT
    // -------------------------------------------------------------------------

    /**
     * GET /api/laporan
     *
     * Parameter GET opsional (semua disanitasi sebelum diteruskan ke model):
     *   status      — filter status pengajuan (misal: lulus, tidak_lulus, stiker_keluar)
     *   tgl_dari    — tanggal awal (Y-m-d)
     *   tgl_sampai  — tanggal akhir (Y-m-d)
     *   departemen  — nama perusahaan/departemen kendaraan
     *   jenis       — nama tipe kendaraan
     *   limit       — jumlah baris (default 500, maks 2000)
     *   offset      — offset paginasi (default 0)
     *
     * @return void  Output JSON
     */
    public function index()
    {
        // 1) Paksa Content-Type JSON & matikan CI output buffering tambahan
        $this->output->set_content_type('application/json');

        // 2) Hanya izinkan metode GET
        if ($this->input->method() !== 'get') {
            return $this->_json_error('Method not allowed. Gunakan GET.', 405);
        }

        // 3) Validasi API Key dari header X-API-KEY
        if (!$this->_validate_api_key()) {
            return $this->_json_error('Unauthorized. API key tidak valid atau tidak ditemukan.', 401);
        }

        // 4) Rate Limiting per IP
        if (!$this->_check_rate_limit()) {
            return $this->_json_error('Too Many Requests. Coba lagi dalam beberapa saat.', 429);
        }

        // 5) Sanitasi & normalisasi filter GET
        $filters = $this->_sanitize_filters();

        // 6) Ambil data dari model (read-only)
        $data  = $this->Api_laporan_model->get_laporan($filters);
        $total = $this->Api_laporan_model->count_laporan($filters);

        // 7) Konversi array objek menjadi array asosiatif murni untuk JSON encoding
        $rows = array_map(function ($row) {
            return (array) $row;
        }, $data);

        // 8) Kirim response
        $limit  = min((int) ($filters['limit']  ?? 500), 2000);
        $offset = max((int) ($filters['offset'] ?? 0),    0);

        $this->_json_success($rows, [
            'total'        => $total,
            'limit'        => $limit,
            'offset'       => $offset,
            'count'        => count($rows),
            'generated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    // -------------------------------------------------------------------------
    // PRIVATE HELPERS
    // -------------------------------------------------------------------------

    /**
     * Validasi API key dari header HTTP X-API-KEY.
     * Menggunakan hash_equals() agar tahan timing-attack.
     *
     * @return bool
     */
    private function _validate_api_key()
    {
        // Tangkap header X-API-KEY (CI3 belum punya getallheaders wrapper, pakai $_SERVER)
        $header_key = isset($_SERVER['HTTP_X_API_KEY']) ? trim($_SERVER['HTTP_X_API_KEY']) : '';

        if (empty($header_key) || empty($this->_api_key)) {
            return false;
        }

        // Validasi key yang sudah di-set di api_env.php
        if ($this->_api_key === 'GANTI_DENGAN_API_KEY_RAHASIA_ANDA') {
            // Konfigurasi belum diubah — tolak semua request
            log_message('error', '[API/Laporan] laporan_api_key belum dikonfigurasi di api_env.php');
            return false;
        }

        return hash_equals($this->_api_key, $header_key);
    }

    /**
     * Rate limiting sederhana berbasis file CI3 cache.
     * Menggunakan folder application/cache/ yang sudah ada di project.
     * Tidak membutuhkan Redis/Memcached.
     *
     * @return bool  TRUE jika request masih dalam batas
     */
    private function _check_rate_limit()
    {
        $ip      = $this->input->ip_address();
        $ip_safe = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $ip);
        $cache_key = 'ratelimit_api_laporan_' . $ip_safe;

        // Lokasi file cache — pakai folder cache standar CI3
        $cache_dir  = APPPATH . 'cache/';
        $cache_file = $cache_dir . $cache_key . '.json';

        $now    = time();
        $window = self::RATE_WINDOW_SEC;
        $limit  = self::RATE_LIMIT;

        // Baca data cache yang ada
        $bucket = [];
        if (file_exists($cache_file)) {
            $raw = @file_get_contents($cache_file);
            if ($raw !== false) {
                $bucket = json_decode($raw, true) ?: [];
            }
        }

        // Hapus entry yang sudah di luar window
        $bucket = array_filter($bucket, function ($ts) use ($now, $window) {
            return ($now - $ts) < $window;
        });

        if (count($bucket) >= $limit) {
            // Sudah melampaui batas — tetap simpan state yang ada
            @file_put_contents($cache_file, json_encode(array_values($bucket)), LOCK_EX);
            log_message('info', "[API/Laporan] Rate limit hit — IP: {$ip}");
            return false;
        }

        // Tambahkan timestamp request sekarang dan simpan
        $bucket[] = $now;
        @file_put_contents($cache_file, json_encode(array_values($bucket)), LOCK_EX);
        return true;
    }

    /**
     * Sanitasi & normalisasi parameter GET yang diterima.
     * Hanya field yang dikenal yang diteruskan ke model.
     *
     * @return array
     */
    private function _sanitize_filters()
    {
        $allowed_statuses = [
            'draft', 'pengajuan_baru', 'diterima_manager', 'ditolak_manager',
            'dijadwalkan', 'sedang_diuji', 'lulus', 'tidak_lulus',
            'perbaikan', 'siap_verifikasi', 'inspeksi_ulang',
            'tidak_lulus_inspeksi', 'diterima_ohs', 'diterima_ohs_supt',
            'acc_ktt', 'stiker_keluar', 'stiker_dicabut',
        ];

        $filters = [];

        // Status — whitelist nilai yang diizinkan
        $status = $this->input->get('status', TRUE);
        if ($status && in_array($status, $allowed_statuses, true)) {
            $filters['status'] = $status;
        }

        // Tanggal — validasi format Y-m-d
        $tgl_dari = $this->input->get('tgl_dari', TRUE);
        if ($tgl_dari && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tgl_dari)) {
            $filters['tgl_dari'] = $tgl_dari;
        }

        $tgl_sampai = $this->input->get('tgl_sampai', TRUE);
        if ($tgl_sampai && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tgl_sampai)) {
            $filters['tgl_sampai'] = $tgl_sampai;
        }

        // Departemen & jenis — strip tag, maks 100 karakter
        $departemen = $this->input->get('departemen', TRUE);
        if ($departemen) {
            $filters['departemen'] = substr(strip_tags($departemen), 0, 100);
        }

        $jenis = $this->input->get('jenis', TRUE);
        if ($jenis) {
            $filters['jenis'] = substr(strip_tags($jenis), 0, 100);
        }

        // Limit & offset
        $limit = (int) $this->input->get('limit', TRUE);
        $filters['limit']  = ($limit > 0) ? min($limit, 2000) : 500;

        $offset = (int) $this->input->get('offset', TRUE);
        $filters['offset'] = ($offset > 0) ? $offset : 0;

        return $filters;
    }

    // -------------------------------------------------------------------------
    // RESPONSE HELPERS
    // -------------------------------------------------------------------------

    /**
     * Kirim response JSON sukses dengan HTTP 200.
     *
     * @param  array $data
     * @param  array $meta
     * @return void
     */
    private function _json_success(array $data, array $meta = [])
    {
        $payload = ['status' => 'success', 'meta' => $meta, 'data' => $data];
        $this->output
            ->set_status_header(200)
            ->set_output(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    /**
     * Kirim response JSON error dengan HTTP status tertentu.
     *
     * @param  string $message  Pesan error
     * @param  int    $code     HTTP status code (401, 429, 405, dsb.)
     * @return void
     */
    private function _json_error($message, $code = 400)
    {
        $payload = ['status' => 'error', 'message' => $message];
        $this->output
            ->set_status_header($code)
            ->set_output(json_encode($payload, JSON_UNESCAPED_UNICODE));
    }
}
