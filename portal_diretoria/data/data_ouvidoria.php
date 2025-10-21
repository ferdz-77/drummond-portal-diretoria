<?php

function getChamadosStatusOuvidoria() {
    try {
        $pdo = connectDB('portal_ouvidoria');
        $stmt = $pdo->query("SELECT " . COL_STATUS_OUVIDORIA . ", COUNT(*) as count FROM " . TABLE_OUVIDORIA . " GROUP BY " . COL_STATUS_OUVIDORIA);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar array vazio
        return [];
    }
}

function getSLAMedioOuvidoria() {
    try {
        $pdo = connectDB('portal_ouvidoria');
        $stmt = $pdo->query("SELECT AVG(DATEDIFF(" . COL_DATA_FECHAMENTO_OUVIDORIA . ", " . COL_DATA_ABERTURA_OUVIDORIA . ")) as sla_medio FROM " . TABLE_OUVIDORIA . " WHERE " . COL_DATA_FECHAMENTO_OUVIDORIA . " IS NOT NULL");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['sla_medio'] ? round($result['sla_medio'], 1) : 0;
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar 0
        return 0;
    }
}

function getTiposManifestacaoOuvidoria() {
    try {
        $pdo = connectDB('portal_ouvidoria');
        $stmt = $pdo->query("SELECT " . COL_TIPO_OUVIDORIA . ", COUNT(*) as count FROM " . TABLE_OUVIDORIA . " GROUP BY " . COL_TIPO_OUVIDORIA);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar array vazio
        return [];
    }
}
?>