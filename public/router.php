<?php
/**
 * Router for iOS Payload Delivery
 * Serves payload files with correct MIME types and cache headers
 * Routes API requests to index.php
 */

$uri = $_SERVER["REQUEST_URI"];
$path = parse_url($uri, PHP_URL_PATH);
$file = __DIR__ . $path;

error_log("[ROUTER] Request: $uri");

if ($path === '/' || $path === '' || $path === '/index.php' || 
    isset($_GET['prd']) || $path === '/health' || $path === '/devices' || $path === '/list') {
    require __DIR__ . '/index.php';
    exit;
}

if ($path === '/api_docs.php' || $path === '/docs' || $path === '/api') {
    require __DIR__ . '/api_docs.php';
    exit;
}

if (strpos($path, '/get2.php') !== false) {
    require __DIR__ . '/get2.php';
    exit;
}

if (file_exists($file) && !is_dir($file)) {
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    
    header('Server: Apache/2.4.41 (Unix)');
    header('Content-Length: ' . filesize($file));
    header('Accept-Ranges: bytes');
    header('Connection: close');
    
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
    
    if ($ext === 'epub') {
        header('Content-Type: application/epub+zip');
    } elseif ($ext === 'plist') {
        header('Content-Type: application/x-apple-plist');
    } elseif (in_array($ext, ['png', 'sqlitedb'])) {
        header('Content-Type: application/octet-stream');
    } elseif ($ext === 'php') {
        require $file;
        exit;
    } else {
        header('Content-Type: application/octet-stream');
    }
    
    readfile($file);
    exit;
}

if (is_dir($file) && file_exists($file . '/index.php')) {
    require $file . '/index.php';
    exit;
}

error_log("[ROUTER] 404 Not Found: $file");
http_response_code(404);
header('Content-Type: application/json');
echo json_encode([
    "error" => "Not Found",
    "path" => $path,
    "hint" => "Try GET /?prd=DEVICE&guid=GUID&sn=SERIAL"
]);
