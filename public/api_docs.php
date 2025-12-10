<?php
/**
 * API Documentation Endpoint
 * Returns comprehensive API documentation in JSON format
 */

header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$baseUrl = $protocol . "://" . $host;

$assetsDir = dirname(__DIR__) . '/assets/Maker';
$supportedDevices = [];
if (is_dir($assetsDir)) {
    $supportedDevices = array_values(array_filter(scandir($assetsDir), function($d) use ($assetsDir) {
        return $d !== '.' && $d !== '..' && is_dir($assetsDir . '/' . $d);
    }));
}

$docs = [
    "api_name" => "iOS Activation Bypass API",
    "version" => "2.0.0",
    "description" => "Self-hosted iOS activation bypass server. 100% offline - no external dependencies.",
    "base_url" => $baseUrl,
    "security" => [
        "mode" => "offline_local",
        "external_connections" => false,
        "data_sent_externally" => false
    ],
    "endpoints" => [
        [
            "path" => "/",
            "method" => "GET",
            "description" => "Health check and server status",
            "parameters" => [],
            "example" => "$baseUrl/",
            "response" => [
                "status" => "healthy",
                "supported_devices" => count($supportedDevices)
            ]
        ],
        [
            "path" => "/?prd=DEVICE&guid=GUID&sn=SERIAL",
            "method" => "GET",
            "description" => "Generate activation payload (main endpoint)",
            "parameters" => [
                "prd" => "Device product type (e.g., iPhone12-1, iPad14-1)",
                "guid" => "SystemGroup GUID (8-4-4-4-12 format)",
                "sn" => "Device serial number"
            ],
            "example" => "$baseUrl/?prd=iPhone12-1&guid=2A22A82B-C342-444D-972F-5270FB5080DF&sn=XXXXX",
            "response" => [
                "success" => true,
                "downloadUrl" => "URL to final payload",
                "links" => [
                    "step1_fixedfile" => "Stage 1 URL",
                    "step2_bldatabase" => "Stage 2 URL", 
                    "step3_final" => "Stage 3 URL (final payload)"
                ]
            ]
        ],
        [
            "path" => "/get2.php?prd=DEVICE&guid=GUID&sn=SERIAL",
            "method" => "GET",
            "description" => "Alternative payload generation endpoint (same as main)",
            "parameters" => [
                "prd" => "Device product type",
                "guid" => "SystemGroup GUID",
                "sn" => "Device serial number"
            ]
        ],
        [
            "path" => "/devices",
            "method" => "GET",
            "description" => "List all supported devices",
            "parameters" => [],
            "example" => "$baseUrl/devices"
        ],
        [
            "path" => "/api_docs.php",
            "method" => "GET",
            "description" => "This documentation",
            "parameters" => []
        ]
    ],
    "supported_devices" => [
        "count" => count($supportedDevices),
        "list" => $supportedDevices
    ],
    "client_usage" => [
        "python" => "python3 client/activator.py --server $baseUrl",
        "curl" => "curl '$baseUrl/?prd=iPhone12-1&guid=YOUR-GUID&sn=SERIAL'"
    ],
    "workflow" => [
        "1" => "Start the server: php -S 0.0.0.0:5000 -t public public/router.php",
        "2" => "Run client or make API request with device info",
        "3" => "Download payload from returned URL",
        "4" => "Deploy to device via AFC",
        "5" => "Wait for device reboot",
        "6" => "Keep server running until asset.epub appears",
        "7" => "Process completes after final reboot"
    ],
    "guid_info" => [
        "format" => "XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX",
        "example" => "2A22A82B-C342-444D-972F-5270FB5080DF",
        "how_to_get" => "https://hanakim3945.github.io/posts/download28_sbx_escape/"
    ]
];

echo json_encode($docs, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
