<?php
$cookie_file = __DIR__ . '/test_cookie_operator.txt';
if (file_exists($cookie_file)) @unlink($cookie_file);

function http_req($url, $method = 'GET', $data = array(), $follow = false) {
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

// 1. Get CSRF
$login_page = http_req("http://127.0.0.1:8080/index.php/login");
preg_match('/name="csrf_idss_token"\s+value="([^"]+)"/', $login_page['body'], $matches);
$csrf = isset($matches[1]) ? $matches[1] : '';

// 2. Login as Operator
$post_login = http_req("http://127.0.0.1:8080/index.php/login", "POST", array(
    'csrf_idss_token' => $csrf,
    'username' => 'operator',
    'password' => 'Operator#2026!'
));

// 3. Test Operator accessing /pengguna (Admin only)
$admin_page = http_req("http://127.0.0.1:8080/index.php/pengguna");
// Should return redirect (302/303) back to dashboard because require_admin() redirects non-admin
echo "Operator accessing /pengguna: HTTP " . $admin_page['code'] . " (Redirected)\n";
$is_redirected = in_array($admin_page['code'], array(301, 302, 303, 307, 403));
echo ($is_redirected ? "[PASS] Operator access to /pengguna successfully blocked." : "[FAIL] Operator accessed admin page!") . "\n";

// 4. Test Operator accessing /log_aktivitas (Admin only)
$log_page = http_req("http://127.0.0.1:8080/index.php/log_aktivitas");
$is_log_blocked = in_array($log_page['code'], array(301, 302, 303, 307, 403));
echo ($is_log_blocked ? "[PASS] Operator access to /log_aktivitas successfully blocked." : "[FAIL] Operator accessed log page!") . "\n";

// 5. Test Operator accessing allowed pages
curl_setopt($ch = curl_init("http://127.0.0.1:8080/index.php/evaluasi"), CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$eval_body = curl_exec($ch);
$eval_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo ($eval_code === 200 ? "[PASS] Operator can access /evaluasi (HTTP 200)" : "[FAIL] Operator failed to access /evaluasi") . "\n";
