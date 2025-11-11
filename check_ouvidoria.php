<?php
include 'includes/config.php';

echo "<h1>Verificação Rápida - Portal Ouvidoria</h1>";

try {
    $pdo = getConnection('portal_ouvidoria');

    $stmt = $pdo->query('SELECT COUNT(*) as total FROM chamados');
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "<p><strong>Total registros:</strong> $total</p>";

    if ($total > 0) {
        $stmt = $pdo->query('SELECT COUNT(*) as fechados FROM chamados WHERE data_encerramento IS NOT NULL');
        $fechados = $stmt->fetch(PDO::FETCH_ASSOC)['fechados'];
        echo "<p><strong>Registros fechados:</strong> $fechados</p>";

        if ($fechados > 0) {
            $stmt = $pdo->query('SELECT AVG(DATEDIFF(data_encerramento, data_abertura)) as sla FROM chamados WHERE data_encerramento IS NOT NULL');
            $sla = $stmt->fetch(PDO::FETCH_ASSOC)['sla'];
            echo "<p><strong>SLA médio:</strong> " . round($sla, 1) . " dias</p>";
        } else {
            echo "<p style='color: red;'>Nenhum registro fechado encontrado!</p>";
        }
    } else {
        echo "<p style='color: red;'>Nenhum registro encontrado na tabela!</p>";
    }

} catch (Exception $e) {
    echo "<p style='color: red;'><strong>Erro:</strong> " . $e->getMessage() . "</p>";
}
?>