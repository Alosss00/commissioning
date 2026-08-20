<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controller Seeder
 * 
 * Digunakan untuk meng-generate 20 data tester (dummy data) komisioning kendaraan.
 * Dapat dipanggil melalui browser: http://localhost/ujikelayakan/seeder/run
 * Atau via CLI: php index.php seeder run
 */
class Seeder extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Halaman index / petunjuk penggunaan seeder
     */
    public function index()
    {
        $this->run();
    }

    /**
     * Menjalankan pengisian 20 data tester ke dalam database
     */
    public function run()
    {
        $sql_file = APPPATH . '../database/seeder_20_data.sql';

        if (!file_exists($sql_file)) {
            $msg = "File seeder SQL tidak ditemukan di: " . $sql_file;
            if (is_cli()) {
                echo $msg . "\n";
            } else {
                show_error($msg);
            }
            return;
        }

        $sql_content = file_get_contents($sql_file);

        // Pisahkan statemen query berdasarkan delimiter semicolon
        $queries = array_filter(
            array_map('trim', explode(';', $sql_content)),
            'strlen'
        );

        $this->db->trans_start();

        $executed = 0;
        foreach ($queries as $query) {
            // Abaikan komentar atau statemen kosong
            if (strpos($query, '--') === 0 || strpos($query, '/*') === 0) {
                continue;
            }
            $this->db->query($query);
            $executed++;
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $response = "Gagal mengeksekusi seeder data tester.";
            log_message('error', '[Seeder] Gagal mengeksekusi seeder_20_data.sql');
        } else {
            $response = "SUCCESS: Berhasil membuat 20 data tester unit kendaraan & komisioning!";
        }

        if (is_cli()) {
            echo $response . "\n";
        } else {
            echo "<h2>Sistem Seeder Uji Kelayakan</h2>";
            echo "<p><strong>Status:</strong> " . html_escape($response) . "</p>";
            echo "<p>Total query dieksekusi: " . $executed . "</p>";
            echo "<hr><p><a href='" . site_url('kendaraan') . "'>&larr; Lihat Data Kendaraan</a> | <a href='" . site_url('pengajuan') . "'>Lihat Daftar Pengajuan &rarr;</a></p>";
        }
    }
}
