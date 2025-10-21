<?php
include 'includes/config.php';

echo "<h1>🔍 Verificação de Dados Históricos - Todos os Portais</h1>";

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
        $pdo = getConnection($db);

        // Verificar dados atuais
        $stmt = $pdo->query('SELECT COUNT(*) as total FROM chamados');
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        $stmt = $pdo->query('SELECT COUNT(*) as fechados FROM chamados WHERE data_encerramento IS NOT NULL');
        $fechados = $stmt->fetch(PDO::FETCH_ASSOC)['fechados'];

        echo "<p><strong>Dados atuais:</strong> $total total, $fechados fechados</p>";

        // Verificar dados do mês anterior
        $stmt = $pdo->query("SELECT COUNT(*) as hist FROM chamados WHERE data_encerramento IS NOT NULL AND MONTH(data_encerramento) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) AND YEAR(data_encerramento) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)");
        $hist = $stmt->fetch(PDO::FETCH_ASSOC)['hist'];

        echo "<p><strong>Dados mês anterior:</strong> $hist registros fechados</p>";

        if ($hist > 0) {
            $stmt = $pdo->query("SELECT AVG(DATEDIFF(data_encerramento, data_abertura)) as sla_ant FROM chamados WHERE data_encerramento IS NOT NULL AND MONTH(data_encerramento) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) AND YEAR(data_encerramento) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)");
            $sla_ant = $stmt->fetch(PDO::FETCH_ASSOC)['sla_ant'];
            echo "<p><strong>SLA mês anterior:</strong> " . round($sla_ant, 1) . " dias ✅</p>";
        } else {
            echo "<p style='color: red;'><strong>SLA mês anterior:</strong> SEM DADOS ❌</p>";
        }

        if ($fechados > 0) {
            $stmt = $pdo->query('SELECT AVG(DATEDIFF(data_encerramento, data_abertura)) as sla_atual FROM chamados WHERE data_encerramento IS NOT NULL');
            $sla_atual = $stmt->fetch(PDO::FETCH_ASSOC)['sla_atual'];
            echo "<p><strong>SLA atual:</strong> " . round($sla_atual, 1) . " dias</p>";
        }

    } catch (Exception $e) {
        echo "<p style='color: red;'><strong>Erro:</strong> " . $e->getMessage() . "</p>";
    }

    echo "<hr>";
}

echo "<p><a href='dashboard.php'>← Voltar ao Dashboard</a></p>";
?>