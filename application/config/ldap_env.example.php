<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Konfigurasi Private LDAP Password (Aman & Diabaikan oleh Git)
 * Salin file ini menjadi `ldap_env.php` di folder application/config/
 * lalu isi password service account LDAP di bawah.
 */
putenv('LDAP_BIND_PASSWORD=PasswordAkunServiceAndaDiSini');
