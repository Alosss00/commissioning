<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * CONTOH konfigurasi API Key.
 * Salin file ini menjadi api_env.php dan isi dengan nilai asli.
 * File api_env.php sudah di-exclude di .gitignore.
 *
 * Generate key baru:
 *   php -r "echo bin2hex(random_bytes(24));"
 */

$config['laporan_api_key'] = 'GANTI_DENGAN_API_KEY_RAHASIA_ANDA';
