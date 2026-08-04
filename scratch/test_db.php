<?php
$hosts = ['127.0.0.1', 'localhost'];
$users = ['root', 'u136581265_DB_Stest'];
$passes = ['', 'g+**!>u2Y'];

foreach ($hosts as $h) {
    foreach ($users as $u) {
        foreach ($passes as $p) {
            try {
                $pdo = new PDO("mysql:host=$h", $u, $p, [PDO::ATTR_TIMEOUT => 2]);
                echo "SUCCESS: Connected to MySQL host=$h user=$u pass=" . ($p ? 'YES' : 'NO') . "\n";
                $stmt = $pdo->query("SHOW DATABASES");
                $dbs = $stmt->fetchAll(PDO::FETCH_COLUMN);
                echo "Databases found: " . implode(', ', $dbs) . "\n";
            } catch (Exception $e) {
                // ignore
            }
        }
    }
}
