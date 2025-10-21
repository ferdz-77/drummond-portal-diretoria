<?php
require_once 'includes/config.php';

$portais = [
    'portal_ouvidoria' => ['table' => TABLE_OUVIDORIA, 'status_col' => COL_STATUS_OUVIDORIA, 'fechamento_col' => COL_DATA_FECHAMENTO_OUVIDORIA, 'status_final' => 'Transferido'],
    'portal_ead' => ['table' => TABLE_EAD, 'status_col' => COL_STATUS_EAD, 'fechamento_col' => COL_DATA_FECHAMENTO_EAD, 'status_final' => 'finalizado'],
    'portal_processo_seletivo' => ['table' => TABLE_PROCESSO, 'status_col' => COL_STATUS_PROCESSO, 'fechamento_col' => COL_DATA_FECHAMENTO_PROCESSO, 'status_final' => 'finalizado'],
    'portal_secretaria_academica' => ['table' => TABLE_SECRETARIA, 'status_col' => COL_STATUS_SECRETARIA, 'fechamento_col' => COL_DATA_CONCLUSAO_SECRETARIA, 'status_final' => 'finalizado'],
    'portal_financeiro' => ['table' => TABLE_FINANCEIRO, 'status_col' => COL_STATUS_FINANCEIRO, 'fechamento_col' => COL_DATA_FECHAMENTO_FINANCEIRO, 'status_final' => 'finalizado'],
    'portal_exaluno' => ['table' => TABLE_EXALUNO, 'status_col' => COL_STATUS_EXALUNO, 'fechamento_col' => COL_DATA_FECHAMENTO_EXALUNO, 'status_final' => 'Transferido']
];

foreach ($portais as $db => $config) {
    echo "=== $db ===\n";
    try {
        $pdo = connectDB($db);

        // Total de registros
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM {$config['table']}");
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        echo "Total registros: $total\n";

        // Registros com status final
        $stmt = $pdo->query("SELECT COUNT(*) as finalizados FROM {$config['table']} WHERE {$config['status_col']} = '{$config['status_final']}'");
        $finalizados = $stmt->fetch(PDO::FETCH_ASSOC)['finalizados'];
        echo "Com status '{$config['status_final']}': $finalizados\n";

        // Registros com data de fechamento preenchida
        $stmt = $pdo->query("SELECT COUNT(*) as com_data FROM {$config['table']} WHERE {$config['fechamento_col']} IS NOT NULL");
        $com_data = $stmt->fetch(PDO::FETCH_ASSOC)['com_data'];
        echo "Com {$config['fechamento_col']} preenchida: $com_data\n";

        // Registros finalizados com data preenchida
        $stmt = $pdo->query("SELECT COUNT(*) as final_com_data FROM {$config['table']} WHERE {$config['status_col']} = '{$config['status_final']}' AND {$config['fechamento_col']} IS NOT NULL");
        $final_com_data = $stmt->fetch(PDO::FETCH_ASSOC)['final_com_data'];
        echo "Finalizados com data preenchida: $final_com_data\n";

        echo "\n";

    } catch (Exception $e) {
        echo "Erro: " . $e->getMessage() . "\n\n";
    }
}
?>