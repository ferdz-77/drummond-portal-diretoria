<?php
require_once 'includes/config.php';

echo "<h2>Verificação de Dados para Filtros</h2>";

try {
    $pdo = connectDB('portal_ouvidoria');
    
    echo "<h3>Portal Ouvidoria</h3>";
    
    // Total de registros
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM chamados");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>Total de registros: {$result['total']}</p>";
    
    if ($result['total'] == 0) {
        echo "<p>❌ Não há dados na tabela chamados da ouvidoria!</p>";
        echo "<p>Você precisa inserir dados de teste primeiro.</p>";
    } else {
        // Registros por período
        echo "<h4>Registros por período:</h4>";
        
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM chamados WHERE YEARWEEK(data_abertura, 1) = YEARWEEK(CURDATE(), 1)");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>Esta semana: {$result['count']} registros</p>";
        
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM chamados WHERE MONTH(data_abertura) = MONTH(CURDATE()) AND YEAR(data_abertura) = YEAR(CURDATE())");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>Este mês: {$result['count']} registros</p>";
        
        // Mostrar alguns registros
        echo "<h4>Alguns registros:</h4>";
        $stmt = $pdo->query("SELECT status, data_abertura FROM chamados ORDER BY data_abertura DESC LIMIT 5");
        $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<ul>";
        foreach ($registros as $registro) {
            echo "<li>Status: {$registro['status']}, Data: {$registro['data_abertura']}</li>";
        }
        echo "</ul>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Erro ao conectar: {$e->getMessage()}</p>";
}
?>