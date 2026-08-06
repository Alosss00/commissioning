<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controller Errors
 * 
 * Penganganan halaman kesalahan sistem (Error 404 Not Found, etc).
 */
class Errors extends CI_Controller
{
    /**
     * Halaman Kesalahan 404 Not Found
     * 
     * @return void Render view errors/missing
     */
    public function missing()
    {
        $this->output->set_status_header(404);
        $this->load->view('errors/missing');
    }
}
