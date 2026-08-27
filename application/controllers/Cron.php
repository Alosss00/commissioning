<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controller Cron
 * 
 * Pengelolaan tugas terjadwal (Background Cron Jobs).
 * Menyediakan:
 * - Pengiriman email notifikasi ekspirasi stiker komisioning secara bertahap (H-30, H-14, H-7, H-1)
 * - Pembaruan otomatis status stiker expired (set-based update)
 */
class Cron extends CI_Controller
{
    /**
     * Secret key untuk otorisasi akses via HTTP query string (jika bukan via CLI).
     */
    private $_secret;

    /**
     * Konstruktor Controller Cron
     * Memuat library email, helper notifikasi stiker, dan menginisialisasi secret key.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->library('email');
        $this->load->helper('notif_stiker');
        $this->_secret = $this->config->item('cron_secret_key') ?: 'SIKUK_CRON_KEY_CHANGE_ME';
    }

    /**
     * Pemicu Cron: Pengiriman Notifikasi Ekspirasi Stiker Bertahap.
     * 
     * @return void Output log eksekusi
     */
    public function notif_stiker()
    {
        if (!$this->_is_authorized()) {
            show_404();
            return;
        }

        $result = process_notif_stiker($this);

        $log = '[' . date('Y-m-d H:i:s') . '] notif_stiker '
            . '— sent: ' . $result['sent']
            . ', skipped: ' . $result['skipped'];

        echo $log . PHP_EOL;
        log_message('info', $log);
    }

    /**
     * Pemicu Cron: Menandai Stiker yang Telah Lewat Masa Expired.
     * Menggunakan query 1-kali set-based update untuk mengeliminasi N+1 overhead.
     * 
     * @return void Output log eksekusi
     */
    public function mark_expired()
    {
        if (!$this->_is_authorized()) {
            show_404();
            return;
        }

        // Ambil daftar stiker yang baru saja melewati tanggal expired
        $newly_expired = $this->db
            ->select('id_sticker, id_pengajuan, nomor_sticker')
            ->from('sticker_release')
            ->where('tgl_expired < NOW()')
            ->where('is_expired', 0)
            ->where('dicabut', 0)
            ->get()->result();

        $notif_sent = 0;
        if (!empty($newly_expired)) {
            $this->load->library('sikuk_email');
            foreach ($newly_expired as $stk) {
                try {
                    $sent = $this->sikuk_email->notif_stiker_expired_inspektor($stk->id_sticker);
                    if ($sent) {
                        $notif_sent += (int) $sent;
                    }
                } catch (Throwable $e) {
                    log_message('error', '[Cron mark_expired] Exception notif stiker expired #' . $stk->id_sticker . ': ' . $e->getMessage());
                }
            }
        }

        $this->db->query("
            UPDATE sticker_release
            SET is_expired = 1
            WHERE tgl_expired < NOW()
              AND is_expired  = 0
              AND dicabut     = 0
        ");

        $count = $this->db->affected_rows();
        $log   = '[' . date('Y-m-d H:i:s') . '] mark_expired — updated: ' . $count . ', emails sent: ' . $notif_sent;
        echo $log . PHP_EOL;
        log_message('info', $log);
    }

    /**
     * Helper privat verifikasi otorisasi akses pemicu cron (CLI vs HTTP secret key).
     * 
     * @return bool True jika diizinkan
     */
    private function _is_authorized()
    {
        // CLI selalu diizinkan
        if (php_sapi_name() === 'cli' || defined('STDIN')) {
            return true;
        }
        // HTTP: verifikasi match query string key
        $key = $this->input->get('key');
        return $key === $this->_secret;
    }
}
