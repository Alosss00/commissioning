<?php
define('BASEPATH', __DIR__ . '/../system/');
define('APPPATH', __DIR__ . '/../application/');
define('ENVIRONMENT', 'development');

if (!defined('ICONV_ENABLED')) define('ICONV_ENABLED', TRUE);
if (!defined('MB_ENABLED')) define('MB_ENABLED', TRUE);
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SCRIPT_NAME'] = '/index.php';

require_once BASEPATH . 'core/Common.php';

if (file_exists(APPPATH . 'config/' . ENVIRONMENT . '/constants.php')) {
    require_once APPPATH . 'config/' . ENVIRONMENT . '/constants.php';
} else {
    require_once APPPATH . 'config/constants.php';
}

$GLOBALS['CFG'] =& load_class('Config', 'core');
$GLOBALS['UNI'] =& load_class('Utf8', 'core');
$GLOBALS['SEC'] =& load_class('Security', 'core');

// Load config.php
require_once APPPATH . 'config/config.php';

echo "=== EMAIL ENV CONFIG CHECK ===\n";
echo "Host: " . ($config['sikuk_smtp_host'] ?? 'N/A') . "\n";
echo "Port: " . ($config['sikuk_smtp_port'] ?? 'N/A') . "\n";
echo "Crypto: " . ($config['sikuk_smtp_crypto'] ?? 'N/A') . "\n";
echo "User: " . ($config['sikuk_smtp_user'] ?? 'N/A') . "\n";
echo "From: " . ($config['sikuk_email_from'] ?? 'N/A') . "\n";
echo "Pass length: " . strlen($config['sikuk_smtp_pass'] ?? '') . "\n\n";

// Test direct socket connection to smtp.gmail.com:587
echo "=== TESTING SOCKET CONNECTION TO SMTP HOST ===\n";
$timeout = 10;
$errno = 0;
$errstr = '';

$host = $config['sikuk_smtp_host'] ?? 'smtp.gmail.com';
$port = (int)($config['sikuk_smtp_port'] ?? 587);

$fp = @fsockopen($host, $port, $errno, $errstr, $timeout);
if (!$fp) {
    echo "FAILED to connect to $host:$port -> Error ($errno): $errstr\n";
} else {
    echo "SUCCESSfully connected socket to $host:$port!\n";
    $response = fgets($fp, 512);
    echo "SMTP Greeting: " . trim($response) . "\n";
    fclose($fp);
}

// Check database users & email addresses
echo "\n=== DATABASE USERS EMAIL CHECK ===\n";
require_once APPPATH . 'config/database.php';
$db_cfg = $db['default'];

try {
    $dsn = "mysql:host={$db_cfg['hostname']};dbname={$db_cfg['database']};charset={$db_cfg['char_set']}";
    $pdo = new PDO($dsn, $db_cfg['username'], $db_cfg['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT u.id_user, u.username, u.nama, u.email, u.is_active, u.id_role, r.nama_role 
                         FROM users u 
                         LEFT JOIN roles r ON r.id_role = u.id_role");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Total users: " . count($users) . "\n";
    foreach ($users as $u) {
        echo "ID: {$u['id_user']} | Name: {$u['nama']} | Role: {$u['nama_role']} ({$u['id_role']}) | Email: '{$u['email']}' | Active: {$u['is_active']}\n";
    }

    $stmt2 = $pdo->query("SELECT pu.id_pengajuan, pu.id_pemohon, pu.email_pemohon, pu.status, k.no_polisi 
                          FROM pengajuan_uji pu 
                          LEFT JOIN kendaraan k ON k.id_kendaraan = pu.id_kendaraan 
                          ORDER BY pu.id_pengajuan DESC LIMIT 5");
    $pengajuan = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    echo "\n=== RECENT PENGAJUAN EMAIL CHECK ===\n";
    foreach ($pengajuan as $p) {
        echo "ID Pengajuan: #PU-" . str_pad($p['id_pengajuan'], 4, '0', STR_PAD_LEFT) . " | Unit: {$p['no_polisi']} | Email Pemohon: '{$p['email_pemohon']}' | Status: {$p['status']}\n";
    }
} catch (Exception $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}
