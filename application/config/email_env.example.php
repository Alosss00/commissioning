<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Templat Konfigurasi Email & SMTP (Public Example)
 * Salin berkas ini menjadi `email_env.php` dan isi dengan password asli Anda.
 */
$config['sikuk_email_from'] = 'alosjo123@gmail.com';
$config['sikuk_email_name'] = 'Commissioning Appointment System';

// ── SMTP Gmail Configuration ──────────────────────────────────────────
$config['sikuk_smtp_host']   = 'smtp.gmail.com';
$config['sikuk_smtp_port']   = 587;
$config['sikuk_smtp_crypto'] = 'tls';
$config['sikuk_smtp_user']   = 'alosjo123@gmail.com';
$config['sikuk_smtp_pass']   = 'YOUR_APP_PASSWORD_HERE';
