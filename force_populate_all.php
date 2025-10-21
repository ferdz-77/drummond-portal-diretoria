<?php
require_once 'includes/config.php';

echo "<h1>🔧 Forçar População de Dados em TODOS os Portais</h1>";

$portais = [
    'portal_ouvidoria' => 'Ouvidoria',
    'portal_ead' => 'EAD',
    'portal_processo_seletivo' => 'Processo Seletivo',
    'portal_secretaria_academica' => 'Secretaria',
    'portal_exaluno' => 'Ex-Aluno'
];

foreach ($portais as $db => $nome) {
    echo "<h2>$nome</h2>";
    
    try {
        $pdo = getConnection($db);
        
        // Garantir que existem registros atuais fechados
        $updates_atuais = [
            "UPDATE chamados SET data_encerramento = DATE_ADD(data_abertura, INTERVAL 2 DAY) WHERE id = 1",
            "UPDATE chamados SET data_encerramento = DATE_ADD(data_abertura, INTERVAL 3 DAY) WHERE id = 2", 
            "UPDATE chamados SET data_encerramento = DATE_ADD(data_abertura, INTERVAL 5 DAY) WHERE id = 3",
            "UPDATE chamados SET data_encerramento = DATE_ADD(data_abertura, INTERVAL 1 DAY) WHERE id = 4",
            "UPDATE chamados SET data_encerramento = DATE_ADD(data_abertura, INTERVAL 4 DAY) WHERE id = 5"
        ];
        
        foreach ($updates_atuais as $update) {
            try {
                $pdo->exec($update);
            } catch (Exception $e) {
                // Ignorar se não existir
            }
        }
        
        // Forçar inserção de dados históricos (mês anterior)
        $manifestacao_col = ($db == 'portal_ouvidoria' || $db == 'portal_exaluno') ? 'manifestacao' : 'categoria';
        
        // Limpar dados antigos do mês anterior primeiro
        $pdo->exec("DELETE FROM chamados WHERE MONTH(data_encerramento) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) AND YEAR(data_encerramento) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)");
        
        // Inserir novos dados históricos
        $sql_historico = "INSERT INTO chamados (status, data_abertura, data_encerramento, $manifestacao_col) VALUES
        ('Resolvido', DATE_SUB(CURRENT_DATE, INTERVAL 35 DAY), DATE_SUB(CURRENT_DATE, INTERVAL 28 DAY), 'Categoria A'),
        ('Resolvido', DATE_SUB(CURRENT_DATE, INTERVAL 40 DAY), DATE_SUB(CURRENT_DATE, INTERVAL 32 DAY), 'Categoria B'),
        ('Resolvido', DATE_SUB(CURRENT_DATE, INTERVAL 45 DAY), DATE_SUB(CURRENT_DATE, INTERVAL 35 DAY), 'Categoria C'),
        ('Resolvido', DATE_SUB(CURRENT_DATE, INTERVAL 50 DAY), DATE_SUB(CURRENT_DATE, INTERVAL 40 DAY), 'Categoria D'),
        ('Resolvido', DATE_SUB(CURRENT_DATE, INTERVAL 55 DAY), DATE_SUB(CURRENT_DATE, INTERVAL 45 DAY), 'Categoria E')";
        
        $pdo->exec($sql_historico);
        
        // Verificar resultado
        $stmt = $pdo->query('SELECT COUNT(*) as fechados_atual FROM chamados WHERE data_encerramento IS NOT NULL AND MONTH(data_encerramento) = MONTH(CURRENT_DATE)');
        $fechados_atual = $stmt->fetch(PDO::FETCH_ASSOC)['fechados_atual'];
        
        $stmt = $pdo->query('SELECT COUNT(*) as fechados_anterior FROM chamados WHERE data_encerramento IS NOT NULL AND MONTH(data_encerramento) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)');
        $fechados_anterior = $stmt->fetch(PDO::FETCH_ASSOC)['fechados_anterior'];
        
        echo "<p>✅ <strong>$nome:</strong> $fechados_atual registros atuais, $fechados_anterior registros mês anterior</p>";
        
        if ($fechados_atual > 0) {
            $stmt = $pdo->query('SELECT AVG(DATEDIFF(data_encerramento, data_abertura)) as sla_atual FROM chamados WHERE data_encerramento IS NOT NULL AND MONTH(data_encerramento) = MONTH(CURRENT_DATE)');
            $sla_atual = $stmt->fetch(PDO::FETCH_ASSOC)['sla_atual'];
            echo "<p><strong>SLA atual:</strong> " . round($sla_atual, 1) . " dias</p>";
        }
        
        if ($fechados_anterior > 0) {
            $stmt = $pdo->query('SELECT AVG(DATEDIFF(data_encerramento, data_abertura)) as sla_anterior FROM chamados WHERE data_encerramento IS NOT NULL AND MONTH(data_encerramento) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)');
            $sla_anterior = $stmt->fetch(PDO::FETCH_ASSOC)['sla_anterior'];
            echo "<p><strong>SLA anterior:</strong> " . round($sla_anterior, 1) . " dias</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ <strong>Erro $nome:</strong> " . $e->getMessage() . "</p>";
    }
    
    echo "<hr>";
}

echo "<h2>✅ Processo Concluído!</h2>";
echo "<p><a href='dashboard.php'>🚀 Testar Dashboard</a> | <a href='debug_dashboard_sla.php'>🔍 Debug SLA</a></p>";
?>