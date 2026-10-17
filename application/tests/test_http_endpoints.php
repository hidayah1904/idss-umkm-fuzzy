<?php
/**
 * Test HTTP Endpoints via PHP Curl with Session
 */

$cookie_file = __DIR__ . '/test_cookie.txt';
if (file_exists($cookie_file)) @unlink($cookie_file);

function http_req($url, $method = 'GET', $data = array(), $follow = true) {
    global $cookie_file;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $follow);
    curl_setopt($ch, CURLOPT_HEADER, true);
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    }
    $response = curl_exec($ch);
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $header = substr($response, 0, $header_size);
    $body = substr($response, $header_size);
    curl_close($ch);
    return array('code' => $http_code, 'header' => $header, 'body' => $body);
}

echo "=== 1. Request Login Page ===\n";
$login_page = http_req("http://127.0.0.1:8080/index.php/login");
echo "Status: " . $login_page['code'] . "\n";

// Extract CSRF
preg_match('/name="csrf_idss_token"\s+value="([^"]+)"/', $login_page['body'], $matches);
$csrf = isset($matches[1]) ? $matches[1] : '';
echo "CSRF Token: " . ($csrf ? $csrf : 'NOT FOUND') . "\n";

echo "\n=== 2. Submit Login as Admin ===\n";
$post_login = http_req("http://127.0.0.1:8080/index.php/login", "POST", array(
    'csrf_idss_token' => $csrf,
    'username' => 'admin',
    'password' => 'Admin#2026!'
));
echo "Login Status: " . $post_login['code'] . "\n";
echo "Redirect / Dashboard Content Found: " . (strpos($post_login['body'], 'Total UMKM Terdaftar') !== false ? 'YES' : 'NO') . "\n";

echo "\n=== 3. Access All Authenticated Endpoints ===\n";
$endpoints = array(
    'Dashboard' => 'http://127.0.0.1:8080/index.php/dashboard',
    'Data UMKM' => 'http://127.0.0.1:8080/index.php/umkm',
    'Detail UMKM #1' => 'http://127.0.0.1:8080/index.php/umkm/detail/1',
    'Form Evaluasi' => 'http://127.0.0.1:8080/index.php/evaluasi',
    'Hasil Evaluasi' => 'http://127.0.0.1:8080/index.php/hasil_evaluasi',
    'Laporan' => 'http://127.0.0.1:8080/index.php/laporan',
    'Konfigurasi Fuzzy' => 'http://127.0.0.1:8080/index.php/konfigurasi_fuzzy',
    'Manajemen Pengguna' => 'http://127.0.0.1:8080/index.php/pengguna',
    'Log Aktivitas' => 'http://127.0.0.1:8080/index.php/log_aktivitas'
);

$all_ok = true;
foreach ($endpoints as $name => $url) {
    $res = http_req($url);
    $ok = ($res['code'] === 200);
    echo " [" . ($ok ? 'PASS' : 'FAIL') . "] $name ($url) -> HTTP {$res['code']}\n";
    if (!$ok) $all_ok = false;
}

if ($all_ok) {
    echo "\n>>> SELURUH ENDPOINT WEB RESPONSIVE & BERFUNGSI SEMPURNA (HTTP 200 OK) <<<\n";
} else {
    echo "\n>>> ADA ENDPOINT YANG GAGAL <<<\n";
}
