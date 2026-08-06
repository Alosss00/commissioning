<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Helper Otentikasi
 * 
 * Menyediakan fungsi pembantu verifikasi status login pengguna.
 */

if (!function_exists('cek_login')) {
    /**
     * Memeriksa apakah pengguna dalam keadaan logged in.
     * Jika tidak, alihkan otomatis ke halaman login dengan pesan flashdata.
     * 
     * @return void
     */
    function cek_login()
    {
        $CI = &get_instance();

        if (!$CI->session->userdata('logged_in')) {
            $CI->session->set_flashdata('error', 'Silakan login terlebih dahulu');
            redirect('auth');
        }
    }
}
