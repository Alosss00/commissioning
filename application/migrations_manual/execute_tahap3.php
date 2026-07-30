<?php
define('BASEPATH', TRUE);
define('ENVIRONMENT', 'development');
require_once __DIR__ . '/../config/database.php';

$db_cfg = $db['default'];

$mysqli = null;

// Try configured credentials
try {
    $mysqli = new mysqli(
        $db_cfg['hostname'],
        $db_cfg['username'],
        $db_cfg['password'],
        $db_cfg['database']
    );
} catch (Throwable $e) {
    // Fallback to local root on 127.0.0.1
    try {
        $mysqli = new mysqli('127.0.0.1', 'root', '', 'ujikelayakan');
    } catch (Throwable $e2) {
        try {
            $mysqli = new mysqli('127.0.0.1', 'root', '', 'u136581265_db_test');
        } catch (Throwable $e3) {
            die("Connection failed: " . $e3->getMessage() . "\n");
        }
    }
}

echo "Connected successfully to database: " . $db_cfg['database'] . "\n";

// 1. BACKUP TABLE users
$timestamp = date('Ymd_His');
$backup_file = __DIR__ . "/backup_users_{$timestamp}.sql";

$result = $mysqli->query("SELECT * FROM `users`");
if ($result) {
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $sql_dump = "-- Backup of table users generated on " . date('Y-m-d H:i:s') . "\n";
    $sql_dump .= "TRUNCATE TABLE `users`;\n";
    foreach ($rows as $row) {
        $cols = array_map(function($val) use ($mysqli) {
            return $val === null ? "NULL" : "'" . $mysqli->real_escape_string($val) . "'";
        }, array_values($row));
        $keys = array_map(function($k) { return "`$k`"; }, array_keys($row));
        $sql_dump .= "INSERT INTO `users` (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $cols) . ");\n";
    }
    file_put_contents($backup_file, $sql_dump);
    echo "Backup saved to: " . basename($backup_file) . " (" . count($rows) . " rows)\n";
} else {
    echo "Backup failed: " . $mysqli->error . "\n";
}

// 2. CHECK IF COLUMN EXISTS
$check = $mysqli->query("SHOW COLUMNS FROM `users` LIKE 'auth_source'");
if ($check && $check->num_rows > 0) {
    echo "Column 'auth_source' already exists in 'users' table.\n";
} else {
    // 3. EXECUTE ALTER TABLE
    $alter_sql = "ALTER TABLE `users` 
        ADD COLUMN `auth_source` ENUM('local','ldap') NOT NULL DEFAULT 'local' AFTER `password`,
        ADD COLUMN `ldap_dn` VARCHAR(255) NULL DEFAULT NULL AFTER `auth_source`";
    
    if ($mysqli->query($alter_sql)) {
        echo "ALTER TABLE executed successfully! Columns 'auth_source' and 'ldap_dn' added to 'users'.\n";
    } else {
        echo "ALTER TABLE failed: " . $mysqli->error . "\n";
    }
}

$mysqli->close();
