<?php
require_once 'includes/config.php';

echo "<h1>🔍 Teste de Conexão e Dados</h1>";

$portais = [
    'portal_ouvidoria' => 'Ouvidoria',
    'portal_ead' => 'EAD',
    'portal_processo_seletivo' => 'Processo Seletivo',
    'portal_secretaria_academica' => 'Secretaria',
    'portal_financeiro' => 'Financeiro',
    'portal_exaluno' => 'Ex-Aluno'
];

foreach ($portais as $db => $nome) {
    echo "<h2>$nome ($db)</h2>";
    
    try {
        // Testar conexão
        $pdo = connectDB($db);
        echo "<p>✅ Conexão estabelecida</p>";
        
        // Verificar se tabela existe
        $stmt = $pdo->query("SHOW TABLES LIKE 'chamados'");
        if ($stmt->rowCount() > 0) {
            echo "<p>✅ Tabela 'chamados' existe</p>";
            
            // Verificar dados
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM chamados");
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            echo "<p>📊 Total de registros: $total</p>";
            
            if ($total == 0) {
                echo "<p style='color: orange;'>⚠️ Tabela vazia! Criando dados...</p>";
                
                // Criar dados de teste
                $manifestacao_col = ($db == 'portal_ouvidoria' || $db == 'portal_exaluno') ? 'manifestacao' : 'categoria';
                
                $sql = "INSERT INTO chamados (status, data_abertura, data_encerramento, $manifestacao_col) VALUES
                ('Resolvido', '2024-10-01', '2024-10-03', 'Teste A'),
                ('Resolvido', '2024-10-02', '2024-10-05', 'Teste B'),
                ('Resolvido', '2024-10-03', '2024-10-06', 'Teste C'),
                ('Resolvido', '2024-09-15', '2024-09-18', 'Histórico A'),
                ('Resolvido', '2024-09-16', '2024-09-20', 'Histórico B')";
                
                $pdo->exec($sql);
                echo "<p>✅ Dados criados!</p>";
                
                // Verificar SLA
                $stmt = $pdo->query("SELECT AVG(DATEDIFF(data_encerramento, data_abertura)) as sla FROM chamados WHERE data_encerramento IS NOT NULL");
                $sla = $stmt->fetch(PDO::FETCH_ASSOC)['sla'];
                echo "<p><strong>🎯 SLA Calculado:</strong> " . round($sla, 1) . " dias</p>";
            } else {
                $stmt = $pdo->query("SELECT COUNT(*) as fechados FROM chamados WHERE data_encerramento IS NOT NULL");
                $fechados = $stmt->fetch(PDO::FETCH_ASSOC)['fechados'];
                echo "<p>📊 Registros fechados: $fechados</p>";
                
                if ($fechados > 0) {
                    $stmt = $pdo->query("SELECT AVG(DATEDIFF(data_encerramento, data_abertura)) as sla FROM chamados WHERE data_encerramento IS NOT NULL");
                    $sla = $stmt->fetch(PDO::FETCH_ASSOC)['sla'];
                    echo "<p><strong>🎯 SLA Atual:</strong> " . round($sla, 1) . " dias</p>";
                } else {
                    echo "<p style='color: red;'>❌ Nenhum registro fechado!</p>";
                }
            }
        } else {
            echo "<p style='color: red;'>❌ Tabela 'chamados' não existe!</p>";
            
            // Criar tabela
            $manifestacao_col = ($db == 'portal_ouvidoria' || $db == 'portal_exaluno') ? 'manifestacao' : 'categoria';
            
            $sql_table = "CREATE TABLE chamados (
                id INT AUTO_INCREMENT PRIMARY KEY,
                status VARCHAR(50),
                data_abertura DATE,
                data_encerramento DATE,
                $manifestacao_col VARCHAR(100)
            )";
            
            $pdo->exec($sql_table);
            echo "<p>✅ Tabela criada!</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
    }
    
    echo "<hr>";
}

echo "<p><a href='dashboard.php'>🚀 Testar Dashboard</a></p>";
?>