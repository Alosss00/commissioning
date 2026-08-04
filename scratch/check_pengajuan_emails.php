<?php
define('BASEPATH', __DIR__ . '/../system/');
define('APPPATH', __DIR__ . '/../application/');
define('ENVIRONMENT', 'development');

require_once APPPATH . 'config/database.php';
$db_cfg = $db['default'];

try {
    $dsn = "mysql:host={$db_cfg['hostname']};dbname={$db_cfg['database']};charset={$db_cfg['char_set']}";
    $pdo = new PDO($dsn, $db_cfg['username'], $db_cfg['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT pu.id_pengajuan, pu.id_pemohon, pu.email_pemohon, u.email as user_email, u.nama, pu.status, pu.tanggal_pengajuan 
                         FROM pengajuan_uji pu 
                         LEFT JOIN users u ON u.id_user = pu.id_pemohon 
                         ORDER BY pu.id_pengajuan DESC LIMIT 10");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "=== RECENT PENGAJUAN EMAIL ANALYSIS ===\n";
    foreach ($rows as $r) {
        echo "ID: #PU-" . str_pad($r['id_pengajuan'], 4, '0', STR_PAD_LEFT) 
           . " | Pemohon: {$r['nama']} (ID {$r['id_pemohon']})"
           . " | pu.email_pemohon: '" . ($r['email_pemohon'] ?? 'NULL') . "'"
           . " | u.email: '" . ($r['user_email'] ?? 'NULL') . "'"
           . " | Status: {$r['status']}\n";
    }

} catch (Exception $e) {
    echo "DB Error: " . $e->getMessage() . "\n";
}
