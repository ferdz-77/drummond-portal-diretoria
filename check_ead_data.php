<?php
// Verificar dados específicos do EAD
require_once 'includes/config.php';

echo "<h1>📊 Dados Específicos do Portal EAD</h1>";
echo "<pre>";

try {
    $pdo = connectDB('portal_ead');
    
    echo "=== DADOS COMPLETOS DA TABELA EAD ===\n";
    $stmt = $pdo->query("SELECT id, status, categoria, manifestacao, data_abertura FROM chamados");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($results as $row) {
        echo "ID: " . $row['id'] . "\n";
        echo "  Status: '" . $row['status'] . "' (length: " . strlen($row['status']) . ")\n";
        echo "  Categoria: '" . $row['categoria'] . "' (length: " . strlen($row['categoria']) . ")\n";
        echo "  Manifestação: '" . $row['manifestacao'] . "' (length: " . strlen($row['manifestacao']) . ")\n";
        echo "  Data: " . $row['data_abertura'] . "\n";
        echo "  ---\n";
    }
    
    echo "\n=== ANÁLISE DOS VALORES VAZIOS ===\n";
    
    // Verificar status vazios
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM chamados WHERE status = '' OR status IS NULL");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Registros com status vazio: " . $result['count'] . "\n";
    
    // Verificar categorias vazias
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM chamados WHERE categoria = '' OR categoria IS NULL");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Registros com categoria vazia: " . $result['count'] . "\n";
    
    // Verificar manifestações vazias
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM chamados WHERE manifestacao = '' OR manifestacao IS NULL");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Registros com manifestação vazia: " . $result['count'] . "\n";
    
    echo "\n=== POSSÍVEIS STATUS VÁLIDOS ===\n";
    $stmt = $pdo->query("SHOW COLUMNS FROM chamados WHERE Field = 'status'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Enum de status: " . $result['Type'] . "\n";
    
    echo "\n=== POSSÍVEIS CATEGORIAS VÁLIDAS ===\n";
    $stmt = $pdo->query("SHOW COLUMNS FROM chamados WHERE Field = 'categoria'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Enum de categoria: " . $result['Type'] . "\n";
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

echo "</pre>";
?>