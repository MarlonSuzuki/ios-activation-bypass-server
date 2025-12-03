<?php
/**
 * Configuração do Servidor
 * 
 * Este arquivo pode ser personalizado via variáveis de ambiente
 * para diferentes plataformas de hosting (Replit, Vercel, hospedagem tradicional)
 */

// Modo de debug (ativar para ver erros detalhados)
define('DEBUG_MODE', getenv('DEBUG_MODE') === 'true' || getenv('DEBUG_MODE') === '1');

// Configurar exibição de erros
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Base URL do servidor (auto-detecta ou usa variável de ambiente)
if (getenv('BASE_URL')) {
    define('BASE_URL', rtrim(getenv('BASE_URL'), '/'));
} else {
    // Auto-detectar URL base
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
    $baseUrl = $protocol . '://' . $host . ($scriptPath !== '/' ? $scriptPath : '');
    define('BASE_URL', rtrim($baseUrl, '/'));
}

// Diretórios
define('ROOT_DIR', dirname(__DIR__));
define('ASSETS_DIR', ROOT_DIR . '/assets');
define('CACHE_DIR', ROOT_DIR . '/cache');
define('LOGS_DIR', ROOT_DIR . '/logs');

// Criar diretórios se não existirem
$dirsToCreate = [CACHE_DIR, LOGS_DIR];
foreach ($dirsToCreate as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Configurações de cache
define('CACHE_LIFETIME', getenv('CACHE_LIFETIME') ?: 3600); // 1 hora padrão

// Timezone
date_default_timezone_set(getenv('TIMEZONE') ?: 'America/Sao_Paulo');

// Função helper para gerar nomes aleatórios
function generateRandomToken($length = 16) {
    return bin2hex(random_bytes($length / 2));
}

// Função helper para sanitizar nome de arquivo
function sanitizeFilename($filename) {
    return preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $filename);
}
