<?php
$dbPath = __DIR__ . '/public/downloads.28.sqlitedb';

if (!file_exists($dbPath)) {
    die("Arquivo não encontrado: $dbPath\n");
}

echo "=== ANÁLISE DO PAYLOAD ===\n";
echo "Arquivo: " . basename($dbPath) . "\n";
echo "Tamanho: " . filesize($dbPath) . " bytes\n";
echo "MD5: " . md5_file($dbPath) . "\n\n";

$db = new SQLite3($dbPath);

// Listar tabelas
echo "=== TABELAS ===\n";
$result = $db->query("SELECT name FROM sqlite_master WHERE type='table'");
$tables = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $tables[] = $row['name'];
    echo "- " . $row['name'] . "\n";
}
echo "\n";

// Analisar cada tabela principal
foreach ($tables as $table) {
    echo "=== TABELA: $table ===\n";
    
    // Contar linhas
    $countResult = $db->querySingle("SELECT COUNT(*) FROM $table");
    echo "Linhas: $countResult\n";
    
    // Mostrar estrutura
    $schemaResult = $db->query("PRAGMA table_info($table)");
    echo "Colunas:\n";
    while ($col = $schemaResult->fetchArray(SQLITE3_ASSOC)) {
        echo "  - {$col['name']} ({$col['type']})\n";
    }
    
    // Amostrar dados
    if ($countResult > 0) {
        echo "Dados (amostra):\n";
        $dataResult = $db->query("SELECT * FROM $table LIMIT 1");
        if ($row = $dataResult->fetchArray(SQLITE3_ASSOC)) {
            foreach ($row as $key => $value) {
                if (is_string($value) && strlen($value) > 100) {
                    echo "  $key: [BINÁRIO/LONGO - " . strlen($value) . " bytes]\n";
                } else {
                    echo "  $key: " . substr((string)$value, 0, 100) . "\n";
                }
            }
        }
    }
    echo "\n";
}

$db->close();
?>
