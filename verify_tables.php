<?php
// Verificar tabelas e dados reais
require_once 'includes/config.php';
require_once 'includes/filter_helpers.php';

echo "<h1>Verificação de Tabelas e Dados</h1>";
echo "<pre>";

try {
    $pdo = connectDB('portal_ouvidoria');
    
    echo "=== TABELAS NO BANCO portal_ouvidoria ===\n";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        echo "  - $table\n";
    }
    
    echo "\n=== CONSTANTES DEFINIDAS ===\n";
    echo "TABLE_OUVIDORIA: " . TABLE_OUVIDORIA . "\n";
    echo "COL_STATUS_OUVIDORIA: " . COL_STATUS_OUVIDORIA . "\n";
    echo "COL_DATA_ABERTURA_OUVIDORIA: " . COL_DATA_ABERTURA_OUVIDORIA . "\n";
    
    // Verificar se a tabela definida existe
    if (in_array(TABLE_OUVIDORIA, $tables)) {
        echo "\n=== DADOS DA TABELA " . TABLE_OUVIDORIA . " ===\n";
        
        $sql = "SELECT COUNT(*) as total FROM " . TABLE_OUVIDORIA;
        $stmt = $pdo->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Total de registros: " . $result['total'] . "\n";
        
        // Verificar colunas
        $sql = "SHOW COLUMNS FROM " . TABLE_OUVIDORIA;
        $stmt = $pdo->query($sql);
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "\nColunas da tabela:\n";
        foreach ($columns as $col) {
            echo "  - " . $col['Field'] . " (" . $col['Type'] . ")\n";
        }
        
        // Teste com constantes
        echo "\n=== TESTE COM CONSTANTES ===\n";
        $sql = "SELECT " . COL_STATUS_OUVIDORIA . ", COUNT(*) as count FROM " . TABLE_OUVIDORIA . " GROUP BY " . COL_STATUS_OUVIDORIA . " LIMIT 5";
        echo "SQL: $sql\n";
        
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "Resultados:\n";
        foreach ($results as $row) {
            echo "  Status: " . $row[COL_STATUS_OUVIDORIA] . " | Count: " . $row['count'] . "\n";
        }
        
        // Teste com filtro usando constantes
        echo "\n=== TESTE COM FILTRO (SEMANA) ===\n";
        $whereClause = getPeriodWhereClause('semana', COL_DATA_ABERTURA_OUVIDORIA);
        $sql = "SELECT " . COL_STATUS_OUVIDORIA . ", COUNT(*) as count FROM " . TABLE_OUVIDORIA . " WHERE 1=1" . $whereClause . " GROUP BY " . COL_STATUS_OUVIDORIA;
        echo "SQL: $sql\n";
        
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "Resultados:\n";
        foreach ($results as $row) {
            echo "  Status: " . $row[COL_STATUS_OUVIDORIA] . " | Count: " . $row['count'] . "\n";
        }
        
    } else {
        echo "\n❌ TABELA " . TABLE_OUVIDORIA . " NÃO EXISTE!\n";
    }
    
    // Verificar também a tabela 'ouvidoria'
    if (in_array('ouvidoria', $tables)) {
        echo "\n=== DADOS DA TABELA ouvidoria ===\n";
        
        $sql = "SELECT COUNT(*) as total FROM ouvidoria";
        $stmt = $pdo->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Total de registros: " . $result['total'] . "\n";
    }
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

echo "</pre>";
?>