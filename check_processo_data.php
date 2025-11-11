<?php
require_once 'includes/config.php';

function checkProcessoSeletivoData() {
    try {
        $pdo = connectDB('portal_processo_seletivo');

        // Verificar total de registros
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM chamados");
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        echo "Total de registros no Processo Seletivo: $total\n\n";

        // Verificar distribuição de status
        $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM chamados GROUP BY status");
        echo "Distribuição de status:\n";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "- {$row['status']}: {$row['count']}\n";
        }
        echo "\n";

        // Verificar registros com status 'finalizado'
        $stmt = $pdo->query("SELECT COUNT(*) as finalizados FROM chamados WHERE status = 'finalizado'");
        $finalizados = $stmt->fetch(PDO::FETCH_ASSOC)['finalizados'];
        echo "Registros com status 'finalizado': $finalizados\n\n";

        // Verificar se há datas preenchidas nos registros finalizados
        if ($finalizados > 0) {
            $stmt = $pdo->query("SELECT data_abertura, data_resposta FROM chamados WHERE status = 'finalizado' LIMIT 5");
            echo "Amostra de registros finalizados:\n";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "- Abertura: {$row['data_abertura']}, Resposta: {$row['data_resposta']}\n";
            }
        }

    } catch (Exception $e) {
        echo "Erro: " . $e->getMessage() . "\n";
    }
}

checkProcessoSeletivoData();
?>