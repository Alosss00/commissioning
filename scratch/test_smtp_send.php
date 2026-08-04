<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Test sending an email via smtp.gmail.com:587 with TLS using raw socket / SMTP protocol

$smtp_host = 'smtp.gmail.com';
$smtp_port = 587;
$username  = 'alosjo123@gmail.com';
$password  = 'bubhmqwjuzrtvfop'; // App Password
$to_email  = 'alosjo123@gmail.com';

echo "Testing SMTP Connection to $smtp_host:$smtp_port...\n";

$socket = fsockopen($smtp_host, $smtp_port, $errno, $errstr, 15);
if (!$socket) {
    die("ERROR: Could not connect to $smtp_host:$smtp_port: ($errno) $errstr\n");
}

function read_smtp($socket) {
    $response = '';
    while ($str = fgets($socket, 512)) {
        $response .= $str;
        if (substr($str, 3, 1) === ' ') break;
    }
    return $response;
}

function send_cmd($socket, $cmd) {
    fputs($socket, $cmd . "\r\n");
    return read_smtp($socket);
}

echo "Server response: " . read_smtp($socket);

echo "EHLO localhost...\n";
$r = send_cmd($socket, "EHLO localhost");
echo $r;

echo "STARTTLS...\n";
$r = send_cmd($socket, "STARTTLS");
echo $r;

// Enable crypto
if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT)) {
    // Try generic TLS if specific method fails
    if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
        die("ERROR: Failed to enable TLS encryption.\n");
    }
}
echo "TLS encryption enabled successfully!\n";

echo "EHLO localhost (post-TLS)...\n";
$r = send_cmd($socket, "EHLO localhost");
echo $r;

echo "AUTH LOGIN...\n";
$r = send_cmd($socket, "AUTH LOGIN");
echo $r;

$r = send_cmd($socket, base64_encode($username));
echo "User sent: " . $r;

$r = send_cmd($socket, base64_encode($password));
echo "Pass sent: " . $r;

if (strpos($r, '235') === false) {
    die("SMTP Authentication FAILED!\n");
}

echo "\nSUCCESS! SMTP Authentication PASSED!\n";

echo "MAIL FROM: <$username>...\n";
$r = send_cmd($socket, "MAIL FROM: <$username>");
echo $r;

echo "RCPT TO: <$to_email>...\n";
$r = send_cmd($socket, "RCPT TO: <$to_email>");
echo $r;

echo "DATA...\n";
$r = send_cmd($socket, "DATA");
echo $r;

$subject = "Test Email from TACTIC System Debugger";
$body = "Subject: $subject\r\nTo: $to_email\r\nFrom: $username\r\nContent-Type: text/html; charset=UTF-8\r\n\r\n<h2>Test Email</h2><p>This is a test notification email from TACTIC System debugging script.</p>";

$r = send_cmd($socket, $body . "\r\n.");
echo $r;

echo "QUIT...\n";
send_cmd($socket, "QUIT");
fclose($socket);

echo "\nTEST EMAIL SENT SUCCESSFULLY!\n";
