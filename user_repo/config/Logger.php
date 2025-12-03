<?php
/**
 * Sistema de Logging
 * 
 * Registra todas as operações do servidor para debugging e auditoria
 */

class Logger {
    private $logFile;
    
    public function __construct() {
        $this->logFile = LOGS_DIR . '/server_' . date('Y-m-d') . '.log';
    }
    
    public function info($message) {
        $this->log('INFO', $message);
    }
    
    public function success($message) {
        $this->log('SUCCESS', $message);
    }
    
    public function warn($message) {
        $this->log('WARN', $message);
    }
    
    public function error($message) {
        $this->log('ERROR', $message);
    }
    
    public function detail($message) {
        $this->log('DETAIL', $message);
    }
    
    private function log($level, $message) {
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
        $logEntry = "[$timestamp] [$level] [$ip] $message" . PHP_EOL;
        
        // Escrever no arquivo
        @file_put_contents($this->logFile, $logEntry, FILE_APPEND);
        
        // Se debug mode, também exibir no error_log
        if (DEBUG_MODE) {
            error_log("[$level] $message");
        }
    }
}
