<?php

function getChamadosStatusExAluno() {
    try {
        $pdo = connectDB('portal_exaluno');
        $stmt = $pdo->query("SELECT " . COL_STATUS_EXALUNO . ", COUNT(*) as count FROM " . TABLE_EXALUNO . " GROUP BY " . COL_STATUS_EXALUNO);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar array vazio
        return [];
    }
}

function getSLAMedioExAluno() {
    try {
        $pdo = connectDB('portal_exaluno');
        $stmt = $pdo->query("SELECT AVG(DATEDIFF(" . COL_DATA_FECHAMENTO_EXALUNO . ", " . COL_DATA_ABERTURA_EXALUNO . ")) as sla_medio FROM " . TABLE_EXALUNO . " WHERE " . COL_DATA_FECHAMENTO_EXALUNO . " IS NOT NULL");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['sla_medio'] ? round($result['sla_medio'], 1) : 0;
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar 0
        return 0;
    }
}

function getServicosSolicitadosExAluno() {
    $pdo = connectDB('portal_exaluno');
    try {
        $stmt = $pdo->query("SELECT " . COL_SERVICO_EXALUNO . ", COUNT(*) as count FROM " . TABLE_EXALUNO . " GROUP BY " . COL_SERVICO_EXALUNO);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Se a coluna não existir, retornar array vazio
        return [];
    }
}
?>