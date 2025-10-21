<?php
require_once 'includes/config.php';

echo "Verificando status disponíveis em cada portal:\n\n";

$portais = [
    'portal_ouvidoria' => ['nome' => 'Ouvidoria', 'tabela' => TABLE_OUVIDORIA, 'status_col' => COL_STATUS_OUVIDORIA, 'data_fechamento' => COL_DATA_FECHAMENTO_OUVIDORIA],
    'portal_ead' => ['nome' => 'EAD', 'tabela' => TABLE_EAD, 'status_col' => COL_STATUS_EAD, 'data_fechamento' => COL_DATA_FECHAMENTO_EAD],
    'portal_processo_seletivo' => ['nome' => 'Processo Seletivo', 'tabela' => TABLE_PROCESSO, 'status_col' => COL_STATUS_PROCESSO, 'data_fechamento' => COL_DATA_FECHAMENTO_PROCESSO],
    'portal_financeiro' => ['nome' => 'Financeiro', 'tabela' => TABLE_FINANCEIRO, 'status_col' => COL_STATUS_FINANCEIRO, 'data_fechamento' => COL_DATA_FECHAMENTO_FINANCEIRO],
    'portal_exaluno' => ['nome' => 'Ex-Aluno', 'tabela' => TABLE_EXALUNO, 'status_col' => COL_STATUS_EXALUNO, 'data_fechamento' => COL_DATA_FECHAMENTO_EXALUNO]
];

foreach ($portais as $db => $config) {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=$db", 'root', '');
        $stmt = $pdo->query("SELECT DISTINCT {$config['status_col']} FROM {$config['tabela']}");
        $statuses = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "{$config['nome']}: " . implode(', ', $statuses) . "\n";
    } catch (Exception $e) {
        echo "{$config['nome']}: Erro - " . $e->getMessage() . "\n";
    }
}

echo "\nVerificando SLA médio com status corretos:\n\n";

$status_finalizados = [
    'portal_ouvidoria' => 'Transferido', // baseado nos dados
    'portal_ead' => 'finalizado',
    'portal_processo_seletivo' => 'finalizado',
    'portal_financeiro' => 'finalizado',
    'portal_exaluno' => 'Transferido'
];

foreach ($portais as $db => $config) {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=$db", 'root', '');
        $status_final = $status_finalizados[$db];
        $stmt = $pdo->query("SELECT AVG(DATEDIFF({$config['data_fechamento']}, " . COL_DATA_ABERTURA_OUVIDORIA . ")) as sla_medio FROM {$config['tabela']} WHERE {$config['data_fechamento']} IS NOT NULL AND {$config['status_col']} = '$status_final'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $sla = $result['sla_medio'] ? round($result['sla_medio'], 1) : 0;
        echo "{$config['nome']} SLA (status '$status_final'): $sla dias\n";
    } catch (Exception $e) {
        echo "{$config['nome']} SLA: Erro - " . $e->getMessage() . "\n";
    }
}
?>