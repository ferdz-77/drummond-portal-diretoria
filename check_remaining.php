<?php
echo "🔍 VERIFICANDO FINANCEIRO E PROCESSO SELETIVO\n";
echo str_repeat("=", 40) . "\n";

$portais = [
    'portal_financeiro' => 'data_resposta',
    'portal_processo_seletivo' => 'data_resposta'
];

foreach ($portais as $db => $col) {
    echo "\n🏦 $db\n";
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=$db;charset=utf8", 'root', '');

        $stmt = $pdo->query("SELECT COUNT(*) as total, COUNT($col) as preenchidos FROM chamados");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Total registros: {$result['total']}\n";
        echo "Com $col preenchida: {$result['preenchidos']}\n";

        if ($result['preenchidos'] > 0) {
            $stmt = $pdo->query("SELECT AVG(DATEDIFF($col, data_abertura)) as sla FROM chamados WHERE $col IS NOT NULL");
            $sla = $stmt->fetch(PDO::FETCH_ASSOC)['sla'];
            echo "SLA calculado: " . round($sla, 1) . " dias\n";
        }
    } catch (Exception $e) {
        echo "❌ Erro: " . $e->getMessage() . "\n";
    }
}
?>