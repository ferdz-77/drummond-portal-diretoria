<?php
require_once 'includes/config.php';

echo "<h1>🔧 Populando Dados de Fechamento para SLA</h1>";

// Função para popular dados de fechamento
function popularFechamento($portal, $table) {
    try {
        $pdo = getConnection($portal);

        // Verificar quantos registros existem
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM $table");
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        if ($total == 0) {
            echo "<p>$portal: Nenhum registro encontrado</p>";
            return;
        }

        // Atualizar alguns registros com datas de fechamento
        $updates = [
            "UPDATE $table SET data_encerramento = DATE_SUB(data_abertura, INTERVAL -3 DAY) WHERE id = 1",
            "UPDATE $table SET data_encerramento = DATE_SUB(data_abertura, INTERVAL -1 DAY) WHERE id = 2",
            "UPDATE $table SET data_encerramento = DATE_SUB(data_abertura, INTERVAL -5 DAY) WHERE id = 3",
            "UPDATE $table SET data_encerramento = DATE_SUB(data_abertura, INTERVAL -2 DAY) WHERE id = 4",
            "UPDATE $table SET data_encerramento = DATE_SUB(data_abertura, INTERVAL -4 DAY) WHERE id = 5"
        ];

        foreach ($updates as $update) {
            try {
                $pdo->exec($update);
            } catch (Exception $e) {
                // Ignorar erros de registros que não existem
            }
        }

        // Verificar resultado
        $stmt2 = $pdo->query("SELECT COUNT(*) as fechados FROM $table WHERE data_encerramento IS NOT NULL");
        $fechados = $stmt2->fetch(PDO::FETCH_ASSOC)['fechados'];

        echo "<p><strong>$portal</strong>: $fechados registros com data de fechamento</p>";

    } catch (Exception $e) {
        echo "<p><strong>Erro $portal:</strong> " . $e->getMessage() . "</p>";
    }
}

// Popular todos os portais
$portais = [
    'portal_ead' => 'chamados',
    'portal_ouvidoria' => 'chamados',
    'portal_processo_seletivo' => 'chamados',
    'portal_secretaria_academica' => 'chamados',
    'portal_financeiro' => 'chamados',
    'portal_exaluno' => 'chamados'
];

foreach ($portais as $portal => $table) {
    popularFechamento($portal, $table);
}

echo "<p><strong>✅ Processo concluído!</strong></p>";
echo "<p><a href='dashboard.php'>Voltar ao Dashboard</a></p>";
?>