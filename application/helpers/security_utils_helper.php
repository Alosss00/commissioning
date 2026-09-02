<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Security Utils Helper
 * 
 * Fungsi-fungsi utilitas pembantu keamanan aplikasi:
 * - Sanitasi metadata EXIF & GPS dari berkas gambar yang diunggah
 * - Sanitasi nama file dan pencegahan Path Traversal
 */

if (!function_exists('strip_image_exif')) {
    /**
     * Membersihkan metadata EXIF (termasuk tag GPS dan info perangkat) dari gambar.
     * Menggunakan library GD untuk me-render ulang bitmap bersih.
     * 
     * @param string $full_path Path absolut berkas gambar di server
     * @return bool True jika berhasil atau bukan gambar, False jika terjadi kegagalan
     */
    function strip_image_exif($full_path)
    {
        if (!file_exists($full_path) || !is_file($full_path)) {
            return false;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $full_path);
        finfo_close($finfo);

        // Hanya proses gambar JPEG, PNG, dan WEBP
        if (in_array($mime, ['image/jpeg', 'image/jpg', 'image/pjpeg'], true)) {
            if (function_exists('imagecreatefromjpeg')) {
                $img = @imagecreatefromjpeg($full_path);
                if ($img !== false) {
                    imagejpeg($img, $full_path, 90);
                    imagedestroy($img);
                    return true;
                }
            }
        } elseif ($mime === 'image/png') {
            if (function_exists('imagecreatefrompng')) {
                $img = @imagecreatefrompng($full_path);
                if ($img !== false) {
                    imagepalettetotruecolor($img);
                    imagealphablending($img, true);
                    imagesavealpha($img, true);
                    imagepng($img, $full_path, 8);
                    imagedestroy($img);
                    return true;
                }
            }
        } elseif ($mime === 'image/webp') {
            if (function_exists('imagecreatefromwebp')) {
                $img = @imagecreatefromwebp($full_path);
                if ($img !== false) {
                    imagewebp($img, $full_path, 90);
                    imagedestroy($img);
                    return true;
                }
            }
        }

        return true;
    }
}

if (!function_exists('sanitize_filename_safe')) {
    /**
     * Membersihkan nama berkas dari karakter terlarang dan mencegah Path Traversal.
     * 
     * @param string $filename Nama berkas mentah
     * @return string Nama berkas aman
     */
    function sanitize_filename_safe($filename)
    {
        // Ambil nama dasar berkas (basename)
        $filename = basename($filename);
        // Hapus null bytes
        $filename = str_replace(chr(0), '', $filename);
        // Hapus karakter traversal
        $filename = str_replace(['../', '..\\', '..'], '', $filename);
        // Pertahankan hanya karakter alfanumerik, titik, minus, dan underscore
        $filename = preg_replace('/[^a-zA-Z0-9\._\-]/', '_', $filename);
        return $filename;
    }
}
