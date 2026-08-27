<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Library Rate_limiter
 * 
 * Modul rate limiting reusable berbasis file cache CI3 (APPPATH . 'cache/').
 * Mengontrol jumlah maksimum permintaan per interval waktu berdasarkan identifier kunci (misalnya id_user + endpoint).
 */
class Rate_limiter
{
    protected $CI;

    /**
     * Konstruktor Rate_limiter
     */
    public function __construct()
    {
        $this->CI =& get_instance();
    }

    /**
     * Memeriksa apakah permintaan untuk kunci tertentu masih dalam batas rate limit.
     * 
     * @param string $key Identifikasi unik (contoh: 'usermanagement_save_1')
     * @param int $limit Maksimum request yang diizinkan (default: 20)
     * @param int $window_sec Interval waktu dalam detik (default: 60)
     * @return bool TRUE jika masih diizinkan, FALSE jika melampaui batas
     */
    public function check($key, $limit = 20, $window_sec = 60)
    {
        $key_safe = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $key);
        $cache_dir = APPPATH . 'cache/';

        if (!is_dir($cache_dir)) {
            @mkdir($cache_dir, 0755, true);
        }

        $cache_file = $cache_dir . 'ratelimit_' . $key_safe . '.json';
        $now = time();

        // Baca data cache yang tersimpan
        $bucket = [];
        if (file_exists($cache_file)) {
            $raw = @file_get_contents($cache_file);
            if ($raw !== false) {
                $bucket = json_decode($raw, true) ?: [];
            }
        }

        // Hapus timestamp yang sudah berada di luar window
        $bucket = array_filter($bucket, function ($ts) use ($now, $window_sec) {
            return ($now - $ts) < $window_sec;
        });

        if (count($bucket) >= $limit) {
            // Sudah melampaui batas — simpan state dan log
            @file_put_contents($cache_file, json_encode(array_values($bucket)), LOCK_EX);
            log_message('info', "[RateLimiter] Terlampaui untuk kunci: {$key}");
            return false;
        }

        // Tambahkan timestamp request terkini dan simpan ke file cache
        $bucket[] = $now;
        @file_put_contents($cache_file, json_encode(array_values($bucket)), LOCK_EX);
        return true;
    }
}
