<?php

/**
 * application/controllers/Cron.php  (BARU)
 *
 * Tujuan   : Controller untuk job terjadwal (cron) sistem SIKUK
 * Caller   : CLI: php index.php cron notif_stiker
 *            atau HTTP: GET /cron/notif_stiker?key=CRON_SECRET_KEY
 * Dependen : Notif_stiker_helper, email library
 * Fungsi public:
 *   notif_stiker()  — kirim notif ekspirasi stiker bertahap
 *   mark_expired()  — tandai stiker yang sudah lewat tgl_expired
 * Side-effect:
 *   - Kirim email notifikasi ekspirasi
 *   - UPDATE sticker_release.is_expired = 1
 *   - INSERT notif_stiker
 *
 * SETUP CRON (jalankan setiap hari pukul 07:00):
 *   0 7 * * * php /var/www/html/index.php cron notif_stiker >> /var/log/sikuk_cron.log 2>&1
 *
 * PROTEKSI:
 *   Set di application/config/config.php:
 *   $config['cron_secret_key'] = 'ISI_DENGAN_STRING_RANDOM_PANJANG';
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Cron extends CI_Controller
{
    // Secret key untuk akses via HTTP (bukan CLI)
    private $_secret;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('email');
        $this->load->helper('notif_stiker');
        $this->_secret = $this->config->item('cron_secret_key') ?: 'SIKUK_CRON_KEY_CHANGE_ME';
    }

    // ── Notifikasi ekspirasi stiker bertahap ──────────────────────────
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

    // ── Tandai stiker expired ─────────────────────────────────────────
    // Jalankan bersamaan dengan notif_stiker atau pisah job
    public function mark_expired()
    {
        if (!$this->_is_authorized()) {
            show_404();
            return;
        }

        // Update sticker_release.is_expired = 1 untuk yang sudah lewat
        // Satu UPDATE set-based — efisien, tidak loop per baris
        $updated = $this->db->query("
            UPDATE sticker_release
            SET is_expired = 1
            WHERE tgl_expired < NOW()
              AND is_expired  = 0
              AND dicabut     = 0
        ");

        $count = $this->db->affected_rows();
        $log   = '[' . date('Y-m-d H:i:s') . '] mark_expired — updated: ' . $count;
        echo $log . PHP_EOL;
        log_message('info', $log);
    }

    // ── Proteksi akses ────────────────────────────────────────────────
    private function _is_authorized()
    {
        // CLI selalu diizinkan
        if (php_sapi_name() === 'cli' || defined('STDIN')) {
            return true;
        }
        // HTTP: cek secret key di query string
        $key = $this->input->get('key');
        return $key === $this->_secret;
    }
}
