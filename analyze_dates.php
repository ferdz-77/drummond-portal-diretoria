<?php
// Verificar datas na tabela correta
require_once 'includes/config.php';
require_once 'includes/filter_helpers.php';

echo "<h1>Análise de Datas na Tabela Chamados</h1>";
echo "<pre>";

try {
    $pdo = connectDB('portal_ouvidoria');
    
    echo "Data atual: " . date('Y-m-d H:i:s') . "\n";
    echo "Semana atual (YEARWEEK): " . date('o-W') . "\n\n";
    
    echo "=== DATAS NA TABELA CHAMADOS ===\n";
    $sql = "SELECT data_abertura, DATE(data_abertura) as data, YEARWEEK(data_abertura, 1) as semana, status FROM chamados ORDER BY data_abertura DESC";
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Registros encontrados:\n";
    foreach ($results as $row) {
        echo "  " . $row['data_abertura'] . " | Semana: " . $row['semana'] . " | Status: " . $row['status'] . "\n";
    }
    
    echo "\n=== COMPARAÇÃO COM SEMANA ATUAL ===\n";
    $semanaAtual = date('oW'); // Formato YYYYWW
    echo "Semana atual (formato oW): $semanaAtual\n";
    
    $sql = "SELECT YEARWEEK(CURDATE(), 1) as semana_atual_db";
    $stmt = $pdo->query($sql);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Semana atual (YEARWEEK DB): " . $result['semana_atual_db'] . "\n";
    
    echo "\nRegistros por semana:\n";
    $sql = "SELECT YEARWEEK(data_abertura, 1) as semana, COUNT(*) as count FROM chamados GROUP BY YEARWEEK(data_abertura, 1) ORDER BY semana DESC";
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($results as $row) {
        $isSemanaAtual = ($row['semana'] == $result['semana_atual_db']) ? " ← ATUAL" : "";
        echo "  Semana " . $row['semana'] . ": " . $row['count'] . " registros$isSemanaAtual\n";
    }
    
    echo "\n=== TESTE DOS FILTROS ===\n";
    $periodos = ['hoje', 'semana', 'mes'];
    
    foreach ($periodos as $periodo) {
        $whereClause = getPeriodWhereClause($periodo, 'data_abertura');
        $sql = "SELECT COUNT(*) as count FROM chamados WHERE 1=1$whereClause";
        
        $stmt = $pdo->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "Período '$periodo': " . $result['count'] . " registros\n";
        echo "  SQL: $sql\n\n";
    }
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

echo "</pre>";
?>