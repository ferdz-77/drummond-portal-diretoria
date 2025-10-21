<?php
require_once 'includes/config.php';

echo "Corrigindo datas de encerramento para chamados finalizados...\n\n";

$portais = [
    'portal_ouvidoria' => [
        'table' => TABLE_OUVIDORIA,
        'status_col' => COL_STATUS_OUVIDORIA,
        'fechamento_col' => COL_DATA_FECHAMENTO_OUVIDORIA,
        'status_final' => 'Transferido',
        'dias_padrao' => 2
    ],
    'portal_ead' => [
        'table' => TABLE_EAD,
        'status_col' => COL_STATUS_EAD,
        'fechamento_col' => COL_DATA_FECHAMENTO_EAD,
        'status_final' => 'finalizado',
        'dias_padrao' => 3
    ],
    'portal_processo_seletivo' => [
        'table' => TABLE_PROCESSO,
        'status_col' => COL_STATUS_PROCESSO,
        'fechamento_col' => COL_DATA_FECHAMENTO_PROCESSO,
        'status_final' => 'finalizado',
        'dias_padrao' => 5
    ],
    'portal_secretaria_academica' => [
        'table' => TABLE_SECRETARIA,
        'status_col' => COL_STATUS_SECRETARIA,
        'fechamento_col' => COL_DATA_CONCLUSAO_SECRETARIA,
        'status_final' => 'finalizado',
        'dias_padrao' => 1
    ],
    'portal_financeiro' => [
        'table' => TABLE_FINANCEIRO,
        'status_col' => COL_STATUS_FINANCEIRO,
        'fechamento_col' => COL_DATA_FECHAMENTO_FINANCEIRO,
        'status_final' => 'finalizado',
        'dias_padrao' => 3
    ],
    'portal_exaluno' => [
        'table' => TABLE_EXALUNO,
        'status_col' => COL_STATUS_EXALUNO,
        'fechamento_col' => COL_DATA_FECHAMENTO_EXALUNO,
        'status_final' => 'Transferido',
        'dias_padrao' => 2
    ]
];

foreach ($portais as $db => $config) {
    echo "=== $db ===\n";
    try {
        $pdo = connectDB($db);

        // Contar quantos registros precisam ser atualizados
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM {$config['table']} WHERE {$config['status_col']} = ? AND {$config['fechamento_col']} IS NULL");
        $stmt->execute([$config['status_final']]);
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        if ($count > 0) {
            // Atualizar as datas de encerramento
            $sql = "UPDATE {$config['table']} SET {$config['fechamento_col']} = DATE_ADD(data_abertura, INTERVAL {$config['dias_padrao']} DAY) WHERE {$config['status_col']} = ? AND {$config['fechamento_col']} IS NULL";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$config['status_final']]);

            echo "Atualizados $count registros (adicionado {$config['dias_padrao']} dias à data de abertura)\n";
        } else {
            echo "Nenhum registro precisa ser atualizado\n";
        }

    } catch (Exception $e) {
        echo "Erro: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

echo "Correção concluída!\n";
?>