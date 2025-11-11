<?php
// Investigar datas reais nos dados de produção
require_once 'includes/config.php';

echo "<h1>Investigação das Datas nos Dados de Produção</h1>";
echo "<pre>";

echo "Data atual do servidor: " . date('Y-m-d H:i:s') . "\n";
echo "Primeiro dia do mês: " . date('Y-m-01') . "\n";
echo "Primeiro dia da semana: " . date('Y-m-d', strtotime('monday this week')) . "\n\n";

// Verificar datas na Ouvidoria
if ($conn_ouvidoria) {
    echo "=== OUVIDORIA ===\n";
    
    // Verificar estrutura da tabela
    $sql = "SHOW COLUMNS FROM ouvidoria";
    $result = $conn_ouvidoria->query($sql);
    if ($result) {
        echo "Colunas da tabela:\n";
        while ($row = $result->fetch_assoc()) {
            if (strpos(strtolower($row['Field']), 'data') !== false) {
                echo "  - " . $row['Field'] . " (" . $row['Type'] . ")\n";
            }
        }
    }
    echo "\n";
    
    // Verificar datas existentes
    $sql = "SELECT MIN(data_abertura) as min_date, MAX(data_abertura) as max_date, COUNT(*) as total FROM ouvidoria WHERE data_abertura IS NOT NULL";
    $result = $conn_ouvidoria->query($sql);
    if ($result && $row = $result->fetch_assoc()) {
        echo "Datas de abertura:\n";
        echo "  Min: " . ($row['min_date'] ?? 'NULL') . "\n";
        echo "  Max: " . ($row['max_date'] ?? 'NULL') . "\n";
        echo "  Total com data: " . $row['total'] . "\n";
    }
    
    // Verificar registros por data
    $sql = "SELECT DATE(data_abertura) as data, COUNT(*) as count FROM ouvidoria WHERE data_abertura IS NOT NULL GROUP BY DATE(data_abertura) ORDER BY data DESC LIMIT 10";
    $result = $conn_ouvidoria->query($sql);
    if ($result) {
        echo "\nÚltimos 10 dias com registros:\n";
        while ($row = $result->fetch_assoc()) {
            echo "  " . $row['data'] . ": " . $row['count'] . " registros\n";
        }
    }
}

echo "\n=== EAD ===\n";
if ($conn_ead) {
    // Verificar estrutura da tabela EAD
    $sql = "SHOW TABLES";
    $result = $conn_ead->query($sql);
    if ($result) {
        echo "Tabelas encontradas:\n";
        while ($row = $result->fetch_row()) {
            echo "  - " . $row[0] . "\n";
        }
    }
    
    // Tentar algumas tabelas comuns
    $tables = ['ead', 'chamados', 'tickets', 'solicitacoes'];
    foreach ($tables as $table) {
        $sql = "SHOW COLUMNS FROM $table";
        $result = $conn_ead->query($sql);
        if ($result) {
            echo "\nColunas da tabela $table:\n";
            while ($row = $result->fetch_assoc()) {
                if (strpos(strtolower($row['Field']), 'data') !== false) {
                    echo "  - " . $row['Field'] . " (" . $row['Type'] . ")\n";
                }
            }
            break; // Parar no primeiro que funcionar
        }
    }
}

echo "</pre>";
?>