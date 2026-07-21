<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Security extends CI_Security {

    /**
     * Overrides csrf_set_cookie to make it accessible by Javascript.
     * Keeps httponly => FALSE specifically for CSRF cookies.
     */
    public function csrf_set_cookie()
    {
        // Prevent parallel GET requests from overwriting the CSRF cookie
        if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST' && isset($_COOKIE[$this->_csrf_cookie_name]))
        {
            return $this;
        }

        $expire = time() + $this->_csrf_expire;
        $secure_cookie = (bool) config_item('cookie_secure');

        if ($secure_cookie && ! is_https())
        {
            return FALSE;
        }

        if (is_php('7.3'))
        {
            setcookie(
                $this->_csrf_cookie_name,
                $this->_csrf_hash,
                array(
                    'expires'  => $expire,
                    'path'     => config_item('cookie_path'),
                    'domain'   => config_item('cookie_domain'),
                    'secure'   => $secure_cookie,
                    'httponly' => FALSE, // Allow Javascript to read CSRF token
                    'samesite' => config_item('cookie_samesite') ?: 'Lax'
                )
            );
        }
        else
        {
            $domain = trim(config_item('cookie_domain'));
            header('Set-Cookie: '.$this->_csrf_cookie_name.'='.$this->_csrf_hash
                    .'; Expires='.gmdate('D, d-M-Y H:i:s T', $expire)
                    .'; Max-Age='.$this->_csrf_expire
                    .'; Path='.rawurlencode(config_item('cookie_path'))
                    .($domain === '' ? '' : '; Domain='.$domain)
                    .($secure_cookie ? '; Secure' : '')
                    .'; SameSite='.(config_item('cookie_samesite') ?: 'Lax')
            );
        }

        if (!headers_sent()) {
            header('X-CSRF-TOKEN: ' . $this->_csrf_hash);
        }

        log_message('info', 'CSRF cookie sent (non-HttpOnly)');
        return $this;
    }

    public function csrf_show_error()
    {
        $post_token = isset($_POST[$this->_csrf_token_name]) ? $_POST[$this->_csrf_token_name] : 'NOT_SET';
        $cookie_token = isset($_COOKIE[$this->_csrf_cookie_name]) ? $_COOKIE[$this->_csrf_cookie_name] : 'NOT_SET';
        log_message('error', 'CSRF Mismatch details: POST [' . $post_token . '] vs COOKIE [' . $cookie_token . '] for URL [' . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'unknown') . ']');
        parent::csrf_show_error();
    }
}
//test