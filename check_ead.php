<?php
echo "🔍 VERIFICANDO COLUNAS EAD\n";
echo str_repeat("=", 30) . "\n";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=portal_ead;charset=utf8', 'root', '');

    $stmt = $pdo->query('SELECT COUNT(*) as total, COUNT(data_resposta) as resposta, COUNT(data_encerramento) as encerramento FROM chamados');
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo 'Total: ' . $result['total'] . PHP_EOL;
    echo 'Com data_resposta: ' . $result['resposta'] . PHP_EOL;
    echo 'Com data_encerramento: ' . $result['encerramento'] . PHP_EOL;

    if ($result['resposta'] > 0) {
        $stmt = $pdo->query('SELECT AVG(DATEDIFF(data_resposta, data_abertura)) as sla FROM chamados WHERE data_resposta IS NOT NULL');
        $sla = $stmt->fetch(PDO::FETCH_ASSOC)['sla'];
        echo 'SLA com data_resposta: ' . round($sla, 1) . PHP_EOL;
    }

    if ($result['encerramento'] > 0) {
        $stmt = $pdo->query('SELECT AVG(DATEDIFF(data_encerramento, data_abertura)) as sla FROM chamados WHERE data_encerramento IS NOT NULL');
        $sla = $stmt->fetch(PDO::FETCH_ASSOC)['sla'];
        echo 'SLA com data_encerramento: ' . round($sla, 1) . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'Erro: ' . $e->getMessage() . PHP_EOL;
}
?>