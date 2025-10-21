<?php
require_once 'includes/config.php';

echo "<h1>📅 Populando Dados Históricos - Todos os Portais</h1>";

$portais = [
    'portal_ouvidoria' => 'Ouvidoria',
    'portal_ead' => 'EAD',
    'portal_processo_seletivo' => 'Processo Seletivo',
    'portal_secretaria_academica' => 'Secretaria',
    'portal_exaluno' => 'Ex-Aluno'
    // Financeiro já tem dados históricos
];

foreach ($portais as $db => $nome) {
    echo "<h2>$nome ($db)</h2>";

    try {
        $pdo = getConnection($db);

        // Verificar se já existem dados no mês anterior
        $stmt = $pdo->query("SELECT COUNT(*) as count_ant FROM chamados WHERE MONTH(data_encerramento) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) AND YEAR(data_encerramento) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)");
        $count_ant = $stmt->fetch(PDO::FETCH_ASSOC)['count_ant'];

        echo "<p><strong>Registros fechados no mês anterior:</strong> $count_ant</p>";

        if ($count_ant == 0) {
            echo "<p>Criando registros históricos...</p>";

            // Criar registros históricos com datas do mês anterior
            $manifestacao_col = ($db == 'portal_ouvidoria' || $db == 'portal_exaluno') ? 'manifestacao' : 'categoria';

            $inserir = [
                "INSERT INTO chamados (status, data_abertura, data_encerramento, $manifestacao_col) VALUES
                ('Resolvido', DATE_SUB(CURRENT_DATE, INTERVAL 35 DAY), DATE_SUB(CURRENT_DATE, INTERVAL 28 DAY), 'Reclamação'),
                ('Resolvido', DATE_SUB(CURRENT_DATE, INTERVAL 40 DAY), DATE_SUB(CURRENT_DATE, INTERVAL 32 DAY), 'Sugestão'),
                ('Resolvido', DATE_SUB(CURRENT_DATE, INTERVAL 45 DAY), DATE_SUB(CURRENT_DATE, INTERVAL 38 DAY), 'Elogio'),
                ('Resolvido', DATE_SUB(CURRENT_DATE, INTERVAL 50 DAY), DATE_SUB(CURRENT_DATE, INTERVAL 42 DAY), 'Reclamação'),
                ('Resolvido', DATE_SUB(CURRENT_DATE, INTERVAL 55 DAY), DATE_SUB(CURRENT_DATE, INTERVAL 48 DAY), 'Sugestão')"
            ];

            foreach ($inserir as $sql) {
                try {
                    $pdo->exec($sql);
                    echo "<p>✓ Registro histórico inserido</p>";
                } catch (Exception $e) {
                    echo "<p style='color: orange;'>⚠ Erro ao inserir: " . $e->getMessage() . "</p>";
                }
            }

            // Verificar resultado
            $stmt = $pdo->query("SELECT COUNT(*) as count_ant_final FROM chamados WHERE MONTH(data_encerramento) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) AND YEAR(data_encerramento) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)");
            $count_ant_final = $stmt->fetch(PDO::FETCH_ASSOC)['count_ant_final'];

            echo "<p><strong>Resultado final - Registros mês anterior:</strong> $count_ant_final</p>";

            if ($count_ant_final > 0) {
                $stmt = $pdo->query("SELECT AVG(DATEDIFF(data_encerramento, data_abertura)) as sla_ant FROM chamados WHERE MONTH(data_encerramento) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) AND YEAR(data_encerramento) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)");
                $sla_ant = $stmt->fetch(PDO::FETCH_ASSOC)['sla_ant'];
                echo "<p><strong>SLA mês anterior calculado:</strong> " . round($sla_ant, 1) . " dias</p>";
            }
        } else {
            echo "<p style='color: green;'>Dados históricos já existem!</p>";
        }

    } catch (Exception $e) {
        echo "<p style='color: red;'><strong>Erro:</strong> " . $e->getMessage() . "</p>";
    }

    echo "<hr>";
}

echo "<p><a href='dashboard.php'>← Voltar ao Dashboard</a> | <a href='check_all_history.php'>Verificar Todos</a></p>";
?>