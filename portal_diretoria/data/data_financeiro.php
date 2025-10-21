<?php

function getChamadosStatusFinanceiro() {
    try {
        $pdo = connectDB('portal_financeiro');
        $stmt = $pdo->query("SELECT " . COL_STATUS_FINANCEIRO . ", COUNT(*) as count FROM " . TABLE_FINANCEIRO . " GROUP BY " . COL_STATUS_FINANCEIRO);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar array vazio
        return [];
    }
}

function getSLAMedioFinanceiro() {
    try {
        $pdo = connectDB('portal_financeiro');
        $stmt = $pdo->query("SELECT AVG(DATEDIFF(" . COL_DATA_FECHAMENTO_FINANCEIRO . ", " . COL_DATA_ABERTURA_FINANCEIRO . ")) as sla_medio FROM " . TABLE_FINANCEIRO . " WHERE " . COL_DATA_FECHAMENTO_FINANCEIRO . " IS NOT NULL");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['sla_medio'] ? round($result['sla_medio'], 1) : 0;
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar 0
        return 0;
    }
}
?>