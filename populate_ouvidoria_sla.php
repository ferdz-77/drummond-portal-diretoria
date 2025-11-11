<?php
require_once 'includes/config.php';

echo "<h1>🔧 Populando Dados de Fechamento - Portal Ouvidoria</h1>";

try {
    $pdo = getConnection('portal_ouvidoria');

    // Verificar quantos registros existem
    $stmt = $pdo->query('SELECT COUNT(*) as total FROM chamados');
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    if ($total == 0) {
        echo "<p style='color: red;'>Nenhum registro encontrado na tabela chamados do portal_ouvidoria!</p>";
        echo "<p>Execute primeiro o script de migração de dados.</p>";
        exit;
    }

    echo "<p><strong>Total de registros encontrados:</strong> $total</p>";

    // Verificar quantos já têm data de fechamento
    $stmt = $pdo->query('SELECT COUNT(*) as fechados FROM chamados WHERE data_encerramento IS NOT NULL');
    $fechados = $stmt->fetch(PDO::FETCH_ASSOC)['fechados'];

    echo "<p><strong>Registros já fechados:</strong> $fechados</p>";

    if ($fechados < $total) {
        // Popular dados de fechamento para registros sem data_encerramento
        $updates = [];

        // Fechar alguns registros com datas variadas
        for ($i = 1; $i <= min(10, $total); $i++) {
            $dias = rand(1, 7); // SLA entre 1 e 7 dias
            $updates[] = "UPDATE chamados SET data_encerramento = DATE_ADD(data_abertura, INTERVAL $dias DAY) WHERE id = $i AND data_encerramento IS NULL";
        }

        foreach ($updates as $update) {
            try {
                $pdo->exec($update);
                echo "<p>✓ Executado: " . substr($update, 0, 50) . "...</p>";
            } catch (Exception $e) {
                echo "<p style='color: orange;'>⚠ Erro ao executar update: " . $e->getMessage() . "</p>";
            }
        }

        // Verificar resultado final
        $stmt = $pdo->query('SELECT COUNT(*) as fechados_final FROM chamados WHERE data_encerramento IS NOT NULL');
        $fechados_final = $stmt->fetch(PDO::FETCH_ASSOC)['fechados_final'];

        echo "<p><strong>Resultado final - Registros fechados:</strong> $fechados_final</p>";

        if ($fechados_final > 0) {
            $stmt = $pdo->query('SELECT AVG(DATEDIFF(data_encerramento, data_abertura)) as sla_medio FROM chamados WHERE data_encerramento IS NOT NULL');
            $sla = $stmt->fetch(PDO::FETCH_ASSOC)['sla_medio'];
            echo "<p><strong>SLA Médio calculado:</strong> " . round($sla, 1) . " dias</p>";
        }
    } else {
        echo "<p style='color: green;'>Todos os registros já possuem data de fechamento!</p>";
    }

} catch (Exception $e) {
    echo "<p style='color: red;'><strong>Erro geral:</strong> " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='dashboard.php'>← Voltar ao Dashboard</a> | <a href='check_ouvidoria.php'>Verificar Dados</a></p>";
?>