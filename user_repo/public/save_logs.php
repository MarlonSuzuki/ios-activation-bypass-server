<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Create logs directory if not exists
$logsDir = __DIR__ . '/../logs';
if (!is_dir($logsDir)) {
    mkdir($logsDir, 0755, true);
}

// Get JSON data from request
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['debug_log']) || !isset($data['normal_log'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

// Generate filename with timestamp
$timestamp = date('Y-m-d_H-i-s');
$randomSuffix = bin2hex(random_bytes(4));
$filename = "client_logs_{$timestamp}_{$randomSuffix}";

try {
    // Save debug log
    $debugLogFile = "{$logsDir}/{$filename}_DEBUG.log";
    file_put_contents($debugLogFile, $data['debug_log']);
    
    // Save normal log
    $normalLogFile = "{$logsDir}/{$filename}_NORMAL.log";
    file_put_contents($normalLogFile, $data['normal_log']);
    
    // Save metadata
    $metadata = [
        'timestamp' => $timestamp,
        'device_info' => $data['device_info'] ?? 'N/A',
        'activation_type' => $data['activation_type'] ?? 'Unknown',
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
    ];
    $metadataFile = "{$logsDir}/{$filename}_METADATA.json";
    file_put_contents($metadataFile, json_encode($metadata, JSON_PRETTY_PRINT));
    
    http_response_code(200);
    echo json_encode([
        'status' => 'success', 
        'message' => 'Logs saved successfully',
        'filename' => $filename
    ]);
    
} catch (Exception $ex) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to save logs: ' . $ex->getMessage()]);
}
?>
