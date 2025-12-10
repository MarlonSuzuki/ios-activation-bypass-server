<?php
/**
 * Parameter Validation Utilities
 * Provides comprehensive validation for device parameters
 */

class ParameterValidator {
    
    private static $guidPattern = '/^[0-9A-F]{8}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{12}$/i';
    
    private static $validDevicePrefixes = [
        'iPhone' => ['11', '12', '13', '14', '15', '16', '17', '18'],
        'iPad' => ['8', '11', '12', '13', '14', '15']
    ];
    
    public static function validateGUID($guid) {
        if (empty($guid)) {
            return ['valid' => false, 'error' => 'GUID is required'];
        }
        
        $guid = strtoupper(trim($guid));
        
        if (!preg_match(self::$guidPattern, $guid)) {
            return [
                'valid' => false, 
                'error' => 'Invalid GUID format. Expected: XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX'
            ];
        }
        
        $clean = str_replace(['0', '-'], '', $guid);
        if (strlen($clean) < 8) {
            return [
                'valid' => false,
                'error' => 'GUID appears to be mostly zeros - likely invalid'
            ];
        }
        
        return ['valid' => true, 'normalized' => $guid];
    }
    
    public static function validateProductType($prd) {
        if (empty($prd)) {
            return ['valid' => false, 'error' => 'Product type is required'];
        }
        
        $prd = str_replace(',', '-', trim($prd));
        
        if (!preg_match('/^(iPhone|iPad)\d+-\d+$/', $prd)) {
            return [
                'valid' => false,
                'error' => 'Invalid product type format. Expected: iPhone12-1 or iPad14-1'
            ];
        }
        
        return ['valid' => true, 'normalized' => $prd];
    }
    
    public static function validateSerialNumber($sn) {
        if (empty($sn)) {
            return ['valid' => false, 'error' => 'Serial number is required'];
        }
        
        $sn = strtoupper(trim($sn));
        
        if (strlen($sn) < 8 || strlen($sn) > 14) {
            return [
                'valid' => false,
                'error' => 'Serial number should be 8-14 characters'
            ];
        }
        
        if (!preg_match('/^[A-Z0-9]+$/', $sn)) {
            return [
                'valid' => false,
                'error' => 'Serial number should only contain letters and numbers'
            ];
        }
        
        return ['valid' => true, 'normalized' => $sn];
    }
    
    public static function validateAll($prd, $guid, $sn) {
        $errors = [];
        $normalized = [];
        
        $prdResult = self::validateProductType($prd);
        if (!$prdResult['valid']) {
            $errors['prd'] = $prdResult['error'];
        } else {
            $normalized['prd'] = $prdResult['normalized'];
        }
        
        $guidResult = self::validateGUID($guid);
        if (!$guidResult['valid']) {
            $errors['guid'] = $guidResult['error'];
        } else {
            $normalized['guid'] = $guidResult['normalized'];
        }
        
        $snResult = self::validateSerialNumber($sn);
        if (!$snResult['valid']) {
            $errors['sn'] = $snResult['error'];
        } else {
            $normalized['sn'] = $snResult['normalized'];
        }
        
        if (!empty($errors)) {
            return [
                'valid' => false,
                'errors' => $errors
            ];
        }
        
        return [
            'valid' => true,
            'normalized' => $normalized
        ];
    }
    
    public static function checkDeviceSupport($prd, $assetsDir) {
        $prd = str_replace(',', '-', $prd);
        $devicePath = $assetsDir . '/Maker/' . $prd;
        
        if (!is_dir($devicePath)) {
            return [
                'supported' => false,
                'error' => "Device $prd is not supported"
            ];
        }
        
        $plistPath = $devicePath . '/com.apple.MobileGestalt.plist';
        if (!file_exists($plistPath)) {
            return [
                'supported' => false,
                'error' => "Configuration file missing for device $prd"
            ];
        }
        
        return [
            'supported' => true,
            'plist_path' => $plistPath,
            'plist_size' => filesize($plistPath)
        ];
    }
}

if (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {
    header('Content-Type: application/json');
    
    if (!isset($_GET['prd']) && !isset($_GET['guid']) && !isset($_GET['sn'])) {
        echo json_encode([
            'endpoint' => 'Parameter Validator',
            'usage' => 'GET /validate.php?prd=iPhone12-1&guid=GUID&sn=SERIAL',
            'description' => 'Validates parameters before payload generation'
        ], JSON_PRETTY_PRINT);
        exit;
    }
    
    $result = ParameterValidator::validateAll(
        $_GET['prd'] ?? '',
        $_GET['guid'] ?? '',
        $_GET['sn'] ?? ''
    );
    
    if ($result['valid']) {
        $assetsDir = dirname(__DIR__) . '/assets';
        $deviceCheck = ParameterValidator::checkDeviceSupport(
            $result['normalized']['prd'],
            $assetsDir
        );
        $result['device_support'] = $deviceCheck;
    }
    
    echo json_encode($result, JSON_PRETTY_PRINT);
}
