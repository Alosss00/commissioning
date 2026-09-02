<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Hook SecurityHeaders
 * 
 * Menginjeksi HTTP Security Headers penting ke seluruh respon web:
 * - X-Frame-Options: SAMEORIGIN (Mencegah serangan Clickjacking)
 * - X-Content-Type-Options: nosniff (Mencegah MIME-sniffing)
 * - X-XSS-Protection: 1; mode=block
 * - Referrer-Policy: strict-origin-when-cross-origin
 * - Permissions-Policy: camera=(), microphone=(), geolocation=()
 */
class SecurityHeaders
{
    public function set_headers()
    {
        if (!headers_sent()) {
            header("X-Frame-Options: SAMEORIGIN");
            header("X-Content-Type-Options: nosniff");
            header("X-XSS-Protection: 1; mode=block");
            header("Referrer-Policy: strict-origin-when-cross-origin");
            header("Permissions-Policy: camera=(), microphone=(), geolocation=()");

            // Content-Security-Policy (CSP) untuk mencegah eksekusi skrip liar / XSS injection
            $csp = "default-src 'self' data: blob:; "
                 . "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; "
                 . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; "
                 . "font-src 'self' data: https://fonts.gstatic.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; "
                 . "img-src 'self' data: blob:; "
                 . "connect-src 'self'; "
                 . "frame-ancestors 'self';";
            header("Content-Security-Policy: " . $csp);
        }
    }
}
