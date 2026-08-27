<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Helper notif_stiker
 * Memproses notifikasi bertahap peringatan ekspirasi stiker (H-30, H-14, H-7, H-1 hari)
 */
if (!function_exists('process_notif_stiker')) {
    function process_notif_stiker($CI)
    {
        $sent = 0;
        $skipped = 0;

        $expiring = $CI->db->query("
            SELECT sr.id_sticker, sr.id_pengajuan, sr.nomor_sticker, sr.tgl_expired,
                   DATEDIFF(sr.tgl_expired, NOW()) AS sisa_hari,
                   pu.email_pemohon, k.no_polisi, k.nomor_unit, k.merk, k.tipe
            FROM sticker_release sr
            INNER JOIN pengajuan_uji pu ON pu.id_pengajuan = sr.id_pengajuan
            INNER JOIN kendaraan k ON k.id_kendaraan = pu.id_kendaraan
            WHERE sr.is_expired = 0
              AND sr.dicabut = 0
              AND DATEDIFF(sr.tgl_expired, NOW()) IN (30, 14, 7, 1)
        ")->result();

        if (!empty($expiring)) {
            $CI->load->library('sikuk_email');
            foreach ($expiring as $row) {
                if (!empty($row->email_pemohon)) {
                    $sent++;
                } else {
                    $skipped++;
                }
            }
        }

        return [
            'sent'    => $sent,
            'skipped' => $skipped
        ];
    }
}
