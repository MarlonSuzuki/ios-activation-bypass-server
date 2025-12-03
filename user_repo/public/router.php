<?php
/**
 * Router for iOS Payload Delivery
 * Serves payload files with correct MIME types and cache headers
 */

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$file = __DIR__ . $path;

error_log("[ROUTER] Request: {$_SERVER['REQUEST_URI']}");

if (file_exists($file) && !is_dir($file)) {
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    
    // Set server headers to mimic legitimate Apple service
    header('Server: Apache/2.4.41 (Unix)');
    header('Content-Length: ' . filesize($file));
    header('Accept-Ranges: bytes');
    header('Connection: close');
    
    // ✅ CRITICAL: Disable caching for dynamic payloads
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
    
    // ✅ Set correct MIME types for iOS payload files
    if ($ext === 'epub') {
        header('Content-Type: application/epub+zip');
    } elseif ($ext === 'plist') {
        header('Content-Type: application/x-apple-plist');
    } elseif (in_array($ext, ['png', 'sqlitedb'])) {
        // Both extensions used for payload delivery
        header('Content-Type: application/octet-stream');
    } else {
        header('Content-Type: application/octet-stream');
    }
    
    // ✅ Serve file
    readfile($file);
    exit;
}

error_log("[ROUTER] 404 Not Found: $file");
http_response_code(404);
echo "404 Not Found";
exit;
?>
