<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controller Dokumen (Secure Attachment Proxy)
 * 
 * Melayani akses pratinjau dan unduh dokumen fisik (lampiran pengajuan & bukti perbaikan)
 * secara aman dengan memverifikasi autentikasi sesi dan hak akses (RBAC).
 * Mencegah Path Traversal dan akses berkas tanpa izin.
 */
class Dokumen extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper(['url', 'file', 'download', 'security_utils']);
        $this->load->model('Pengajuan_model', 'pengajuan_model');

        // Wajib login untuk mengakses berkas lampiran
        if (!$this->session->userdata('logged_in')) {
            show_error('Akses ditolak. Silakan login terlebih dahulu.', 401, '401 Unauthorized');
        }
    }

    /**
     * Mengunduh atau menampilkan berkas lampiran pengajuan.
     * 
     * @param int $id_lampiran ID record pada pengajuan_lampiran
     * @return void Output streaming berkas
     */
    public function unduh_lampiran($id_lampiran = null)
    {
        $id_lampiran = (int) $id_lampiran;
        if (!$id_lampiran) {
            show_404();
        }

        $lampiran = $this->db->get_where('pengajuan_lampiran', ['id_lampiran' => $id_lampiran])->row();
        if (!$lampiran || empty($lampiran->file_path)) {
            show_404();
        }

        // Verifikasi kepemilikan dan hak akses
        $this->_authorize_pengajuan((int) $lampiran->id_pengajuan);

        $this->_serve_file($lampiran->file_path);
    }

    /**
     * Mengunduh atau menampilkan berkas bukti perbaikan unit.
     * 
     * @param int $id_lampiran_perbaikan ID record pada perbaikan_lampiran
     * @return void Output streaming berkas
     */
    public function unduh_perbaikan($id_lampiran_perbaikan = null)
    {
        $id_lampiran_perbaikan = (int) $id_lampiran_perbaikan;
        if (!$id_lampiran_perbaikan) {
            show_404();
        }

        $lampiran = $this->db->get_where('perbaikan_lampiran', ['id_lampiran' => $id_lampiran_perbaikan])->row();
        if (!$lampiran || empty($lampiran->file_path)) {
            show_404();
        }

        // Cari id_pengajuan dari id_perbaikan
        $perbaikan = $this->db->get_where('perbaikan_unit', ['id_perbaikan' => (int) $lampiran->id_perbaikan])->row();
        if ($perbaikan) {
            $this->_authorize_pengajuan((int) $perbaikan->id_pengajuan);
        }

        $this->_serve_file($lampiran->file_path);
    }

    /**
     * Memvalidasi otorisasi hak akses terhadap pengajuan berdasarkan peran pengguna.
     * 
     * @param int $id_pengajuan ID Pengajuan
     * @return void Menghentikan eksekusi jika tidak berwenang
     */
    private function _authorize_pengajuan($id_pengajuan)
    {
        $roles = $this->session->userdata('roles') ?: [$this->session->userdata('role')];
        $roles = array_map('intval', (array) $roles);

        // Role 1 (Super Admin), 2 (Admin OHS), 3 (OHS Supt), 4 (KTT), 6 (Inspector) berhak melihat semua dokumen
        $privileged_roles = [1, 2, 3, 4, 6];
        $is_privileged = !empty(array_intersect($roles, $privileged_roles));
        if ($is_privileged) {
            return;
        }

        // Jika bukan privileged (misal Role 7 Pemohon atau Role 5 Manager Departemen), periksa kepemilikan
        $pengajuan = $this->pengajuan_model->get_by_id($id_pengajuan);
        if (!$pengajuan) {
            show_404();
        }

        $id_user  = (int) $this->session->userdata('id_user');
        $user_dept = $this->session->userdata('departemen');

        $is_owner = ((int) $pengajuan->id_pemohon === $id_user);
        $is_same_dept = (!empty($user_dept) && !empty($pengajuan->perusahaan) && strcasecmp($user_dept, $pengajuan->perusahaan) === 0);

        if (!$is_owner && !$is_same_dept) {
            show_error('Anda tidak memiliki hak akses untuk melihat dokumen ini.', 403, '403 Forbidden');
        }
    }

    /**
     * Melayani berkas fisik dengan validasi Path Traversal dan MIME header yang aman.
     * 
     * @param string $relative_path Path relatif dari FCPATH
     * @return void Streaming berkas ke browser
     */
    private function _serve_file($relative_path)
    {
        // Bersihkan path dari traversal
        $relative_path = str_replace(['../', '..\\'], '', $relative_path);
        $full_path     = realpath(FCPATH . $relative_path);
        $allowed_base  = realpath(FCPATH . 'uploads');

        // Pastikan berkas berada di dalam direktori uploads/
        if (!$full_path || !$allowed_base || strpos($full_path, $allowed_base) !== 0 || !is_file($full_path)) {
            show_404();
        }

        $filename = basename($full_path);
        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mime     = finfo_file($finfo, $full_path) ?: 'application/octet-stream';
        finfo_close($finfo);

        // Kirim header keamanan respon
        header("X-Content-Type-Options: nosniff");
        header("Content-Type: " . $mime);
        header("Content-Length: " . filesize($full_path));
        header("Cache-Control: private, max-age=3600");

        // Jika file adalah gambar atau PDF, render inline; selain itu trigger download
        $disposition = (strpos($mime, 'image/') === 0 || $mime === 'application/pdf') ? 'inline' : 'attachment';
        header("Content-Disposition: {$disposition}; filename=\"" . addslashes($filename) . "\"");

        readfile($full_path);
        exit;
    }
}
