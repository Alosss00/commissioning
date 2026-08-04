<?php
// Boot full CodeIgniter framework to test Sikuk_email library directly
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_METHOD'] = 'GET';

define('ENVIRONMENT', 'development');

$system_path = __DIR__ . '/../system';
$application_folder = __DIR__ . '/../application';

define('SELF', 'index.php');
define('EXT', '.php');
define('BASEPATH', str_replace('\\', '/', realpath($system_path)).'/');
define('FCPATH', str_replace('\\', '/', realpath(__DIR__ . '/..')).'/');
define('SYSDIR', trim(strrchr(trim(BASEPATH, '/'), '/'), '/'));
define('APPPATH', str_replace('\\', '/', realpath($application_folder)).'/');
define('VIEWPATH', APPPATH.'views/');

require_once BASEPATH.'core/CodeIgniter.php';

$CI =& get_instance();
$CI->load->database();

echo "=== LATEST PENGAJUAN DATA IN DATABASE ===\n";
$latest = $CI->db->select('pu.id_pengajuan, pu.id_pemohon, pu.email_pemohon, pu.tanggal_pengajuan, pu.status, u.nama, u.email as user_email, k.no_polisi')
                 ->from('pengajuan_uji pu')
                 ->join('users u', 'u.id_user = pu.id_pemohon', 'left')
                 ->join('kendaraan k', 'k.id_kendaraan = pu.id_kendaraan', 'left')
                 ->order_by('pu.id_pengajuan', 'DESC')
                 ->limit(5)
                 ->get()->result_array();

foreach ($latest as $l) {
    echo "ID: #PU-" . str_pad($l['id_pengajuan'], 4, '0', STR_PAD_LEFT)
       . " | Nopol: " . ($l['no_polisi'] ?? '-')
       . " | Pemohon: " . ($l['nama'] ?? '-')
       . " | email_pemohon (DB): '" . ($l['email_pemohon'] ?? '') . "'"
       . " | user_email (users): '" . ($l['user_email'] ?? '') . "'"
       . " | Tanggal: " . ($l['tanggal_pengajuan'] ?? '-')
       . " | Status: " . ($l['status'] ?? '-') . "\n";
}

echo "\n=== DEPT MANAGERS (ROLE 6) EMAIL CHECK ===\n";
$CI->load->model('Approval_model');
$managers = $CI->db->select('u.id_user, u.nama, u.email, u.is_active')
                   ->from('users u')
                   ->join('user_roles ur', 'ur.id_user = u.id_user', 'left')
                   ->group_start()
                   ->where('ur.id_role', 6)
                   ->or_where('u.id_role', 6)
                   ->group_end()
                   ->where('u.is_active', 1)
                   ->group_by('u.id_user')
                   ->get()->result_array();

echo "Total Dept Managers found: " . count($managers) . "\n";
foreach ($managers as $m) {
    echo "Manager ID: {$m['id_user']} | Name: {$m['nama']} | Email: '{$m['email']}' | Active: {$m['is_active']}\n";
}

if (!empty($latest)) {
    $target_id = $latest[0]['id_pengajuan'];
    echo "\n=== TESTING SIKUK_EMAIL ON LATEST PENGAJUAN (#PU-" . str_pad($target_id, 4, '0', STR_PAD_LEFT) . ") ===\n";
    
    $CI->load->library('sikuk_email');
    
    // We will test sending directly and capture debug output
    $CI->load->library('email');
    
    // Test 1: Send to Applicant
    $p = $CI->db->select('pu.id_pengajuan, pu.email_pemohon, u.email as user_email, u.nama as nama_pemohon, k.no_polisi')
               ->from('pengajuan_uji pu')
               ->join('users u', 'u.id_user = pu.id_pemohon', 'left')
               ->join('kendaraan k', 'k.id_kendaraan = pu.id_kendaraan', 'left')
               ->where('pu.id_pengajuan', $target_id)
               ->get()->row();

    $to_applicant = !empty($p->email_pemohon) ? $p->email_pemohon : $p->user_email;
    echo "Applicant Email Target: '$to_applicant'\n";

    if (!empty($to_applicant)) {
        echo "Sending test email to applicant ($to_applicant)...\n";
        $CI->email->initialize([
            'protocol'    => 'smtp',
            'smtp_host'   => $CI->config->item('sikuk_smtp_host'),
            'smtp_port'   => $CI->config->item('sikuk_smtp_port'),
            'smtp_user'   => $CI->config->item('sikuk_smtp_user'),
            'smtp_pass'   => $CI->config->item('sikuk_smtp_pass'),
            'smtp_crypto' => $CI->config->item('sikuk_smtp_crypto'),
            'smtp_timeout'=> 60,
            'mailtype'    => 'html',
            'charset'     => 'utf-8',
            'newline'     => "\r\n",
            'crlf'        => "\r\n"
        ]);
        $CI->email->clear();
        $CI->email->from($CI->config->item('sikuk_email_from'), $CI->config->item('sikuk_email_name'));
        $CI->email->to($to_applicant);
        $CI->email->subject('[Test Debug] Notifikasi Pengajuan Baru — Unit ' . $p->no_polisi);
        $CI->email->message('<h3>Test Email TACTIC Debugger</h3><p>Pengajuan #' . $p->id_pengajuan . ' unit ' . $p->no_polisi . '</p>');
        
        $res = $CI->email->send(false);
        echo "Send Result: " . ($res ? "SUCCESS" : "FAILED") . "\n";
        if (!$res) {
            echo "--- CI EMAIL DEBUGGER TRACE ---\n";
            echo strip_tags($CI->email->print_debugger([])) . "\n";
            echo "-------------------------------\n";
        }
    } else {
        echo "WARNING: Applicant Email target is EMPTY! No email address to send to.\n";
    }
}
