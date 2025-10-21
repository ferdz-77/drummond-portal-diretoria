<?php

function getChamadosStatusProcessoSeletivo() {
    try {
        $pdo = connectDB('portal_processo_seletivo');
        $stmt = $pdo->query("SELECT " . COL_STATUS_PROCESSO . ", COUNT(*) as count FROM " . TABLE_PROCESSO . " GROUP BY " . COL_STATUS_PROCESSO);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar array vazio
        return [];
    }
}

function getSLAMedioProcessoSeletivo() {
    try {
        $pdo = connectDB('portal_processo_seletivo');
        $stmt = $pdo->query("SELECT AVG(DATEDIFF(" . COL_DATA_FECHAMENTO_PROCESSO . ", " . COL_DATA_ABERTURA_PROCESSO . ")) as sla_medio FROM " . TABLE_PROCESSO . " WHERE " . COL_DATA_FECHAMENTO_PROCESSO . " IS NOT NULL");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['sla_medio'] ? round($result['sla_medio'], 1) : 0;
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar 0
        return 0;
    }
}

function getServicosSolicitadosProcessoSeletivo() {
    $pdo = connectDB('portal_processo_seletivo');
    try {
        $stmt = $pdo->query("SELECT " . COL_SERVICO_PROCESSO . ", COUNT(*) as count FROM " . TABLE_PROCESSO . " GROUP BY " . COL_SERVICO_PROCESSO);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Se a coluna não existir, retornar array vazio
        return [];
    }
}
?>