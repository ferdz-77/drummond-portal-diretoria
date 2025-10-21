<?php

function getChamadosStatusEAD() {
    try {
        $pdo = connectDB('portal_ead');
        $stmt = $pdo->query("SELECT " . COL_STATUS_EAD . ", COUNT(*) as count FROM " . TABLE_EAD . " GROUP BY " . COL_STATUS_EAD);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar array vazio
        return [];
    }
}

function getSLAMedioEAD() {
    try {
        $pdo = connectDB('portal_ead');
        $stmt = $pdo->query("SELECT AVG(DATEDIFF(" . COL_DATA_FECHAMENTO_EAD . ", " . COL_DATA_ABERTURA_EAD . ")) as sla_medio FROM " . TABLE_EAD . " WHERE " . COL_DATA_FECHAMENTO_EAD . " IS NOT NULL");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['sla_medio'] ? round($result['sla_medio'], 1) : 0;
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar 0
        return 0;
    }
}

function getServicosSolicitadosEAD() {
    $pdo = connectDB('portal_ead');
    try {
        $stmt = $pdo->query("SELECT " . COL_SERVICO_EAD . ", COUNT(*) as count FROM " . TABLE_EAD . " GROUP BY " . COL_SERVICO_EAD);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Se a coluna não existir, retornar array vazio
        return [];
    }
}
?>