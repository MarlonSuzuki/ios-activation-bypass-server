<?php
/**
 * iTunes Metadata Generator
 * Generates valid iTunesMetadata.plist for iOS activation bypass
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

$prd = $_GET['prd'] ?? '';
$guid = $_GET['guid'] ?? '';
$sn = $_GET['sn'] ?? '';

if (empty($prd) || empty($guid) || empty($sn)) {
    http_response_code(400);
    die("Missing parameters");
}

error_log("[Metadata] Generating for: prd=$prd, guid=$guid, sn=$sn");

// Generate a REAL XML plist that iOS recognizes
$plistContent = '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
	<key>bundleVersion</key>
	<integer>1</integer>
	<key>itemKind</key>
	<string>software</string>
	<key>softwareVersionBundleId</key>
	<string>com.apple.book</string>
	<key>artistName</key>
	<string>Apple</string>
	<key>bundleShortVersionString</key>
	<string>1.0</string>
	<key>bundleExecutable</key>
	<string>asset</string>
	<key>bundleIdentifier</key>
	<string>com.apple.eBook.asset</string>
	<key>softwareSupportedDevices</key>
	<array>
		<string>iPhone</string>
		<string>iPad</string>
	</array>
</dict>
</plist>';

// Headers
header('Content-Type: application/x-plist');
header('Content-Disposition: attachment; filename="iTunesMetadata.plist"');
header('Content-Length: ' . strlen($plistContent));
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

echo $plistContent;
error_log("[Metadata] Served: " . strlen($plistContent) . " bytes");
exit;
?>
