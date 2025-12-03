<?php
/**
 * File Provider for iOS Activation
 * Serves dynamically generated payload files to iOS during activation
 * Handles: sqlite, blwal, blshm, itunes file types
 */

function log_debug($msg, $level = 'INFO') {
    $timestamp = date('Y-m-d H:i:s');
    $line = "[$timestamp] [$level] $msg";
    error_log($line);
}

log_debug("=== FILE PROVIDER REQUEST ===");

$type = $_GET['type'] ?? '';
log_debug("File type requested: $type");

if (empty($type)) {
    http_response_code(400);
    die("Error: Missing 'type' parameter");
}

$basePath = __DIR__;
$fileToServe = null;
$mimeType = 'application/octet-stream';

// ✅ Locate the most recent payload file based on type
switch ($type) {
    case 'sqlite':
        $secondDir = "$basePath/2ndd";
        if (is_dir($secondDir)) {
            $files = glob("$secondDir/*/belliloveu.png");
            if ($files) {
                usort($files, function($a, $b) {
                    return filemtime($b) - filemtime($a);
                });
                $fileToServe = $files[0];
                $mimeType = 'application/octet-stream';
            }
        }
        log_debug("BLDatabase SQLite file: " . ($fileToServe ? basename(dirname($fileToServe)) . "/" . basename($fileToServe) : "NOT FOUND"));
        break;

    case 'blwal':
        $secondDir = "$basePath/2ndd";
        if (is_dir($secondDir)) {
            $files = glob("$secondDir/*/belliloveu.png-wal");
            if ($files) {
                usort($files, function($a, $b) {
                    return filemtime($b) - filemtime($a);
                });
                $fileToServe = $files[0];
                $mimeType = 'application/octet-stream';
            }
        }
        log_debug("BL WAL file: " . ($fileToServe ? basename(dirname($fileToServe)) . "/" . basename($fileToServe) : "NOT FOUND"));
        break;

    case 'blshm':
        $secondDir = "$basePath/2ndd";
        if (is_dir($secondDir)) {
            $files = glob("$secondDir/*/belliloveu.png-shm");
            if ($files) {
                usort($files, function($a, $b) {
                    return filemtime($b) - filemtime($a);
                });
                $fileToServe = $files[0];
                $mimeType = 'application/octet-stream';
            }
        }
        log_debug("BL SHM file: " . ($fileToServe ? basename(dirname($fileToServe)) . "/" . basename($fileToServe) : "NOT FOUND"));
        break;

    case 'itunes':
        $itunesFile = "$basePath/badfile.plist";
        if (file_exists($itunesFile)) {
            $fileToServe = $itunesFile;
            $mimeType = 'application/x-apple-plist';
        } else {
            $tempDir = sys_get_temp_dir();
            $itunesTmp = "$tempDir/iTunesMetadata.plist";
            if (file_exists($itunesTmp)) {
                $fileToServe = $itunesTmp;
                $mimeType = 'application/x-apple-plist';
            }
        }
        log_debug("iTunes file: " . ($fileToServe ? basename($fileToServe) : "NOT FOUND"));
        break;

    default:
        log_debug("Unknown file type: $type", "ERROR");
        http_response_code(400);
        die("Error: Unknown file type '$type'");
}

// ✅ Serve the file if found
if ($fileToServe && file_exists($fileToServe)) {
    $fileSize = filesize($fileToServe);
    
    // Set cache headers to prevent caching
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
    
    // Set content headers
    header('Content-Type: ' . $mimeType);
    header('Content-Length: ' . $fileSize);
    header('Content-Disposition: attachment; filename="' . basename($fileToServe) . '"');
    header('Accept-Ranges: bytes');
    header('Connection: close');
    
    // Fake Apache server header
    header('Server: Apache/2.4.41 (Unix)');
    
    log_debug("✅ Serving file: " . basename(dirname($fileToServe)) . "/" . basename($fileToServe) . " ($fileSize bytes)");
    
    // Read and output file
    readfile($fileToServe);
    exit;
} else {
    log_debug("❌ File not found for type: $type", "ERROR");
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['error' => "File type '$type' not found", 'type' => $type]);
    exit;
}
?>
