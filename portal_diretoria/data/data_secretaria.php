<?php

function getSolicitacoesStatusSecretaria() {
    try {
        $pdo = connectDB('portal_secretaria_academica');
        $stmt = $pdo->query("SELECT " . COL_STATUS_SECRETARIA . ", COUNT(*) as count FROM " . TABLE_SECRETARIA . " GROUP BY " . COL_STATUS_SECRETARIA);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar array vazio
        return [];
    }
}

function getTempoMedioSecretaria() {
    try {
        $pdo = connectDB('portal_secretaria_academica');
        $stmt = $pdo->query("SELECT AVG(DATEDIFF(" . COL_DATA_CONCLUSAO_SECRETARIA . ", " . COL_DATA_ABERTURA_SECRETARIA . ")) as tempo_medio FROM " . TABLE_SECRETARIA . " WHERE " . COL_DATA_CONCLUSAO_SECRETARIA . " IS NOT NULL");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['tempo_medio'] ? round($result['tempo_medio'], 1) : 0;
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar 0
        return 0;
    }
}

function getServicosSolicitadosSecretaria() {
    $pdo = connectDB('portal_secretaria_academica');
    try {
        $stmt = $pdo->query("SELECT " . COL_SERVICO_SECRETARIA . ", COUNT(*) as count FROM " . TABLE_SECRETARIA . " GROUP BY " . COL_SERVICO_SECRETARIA);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Se a coluna não existir, retornar array vazio
        return [];
    }
}
?>