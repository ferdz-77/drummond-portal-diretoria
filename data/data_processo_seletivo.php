<?php
// Incluir helpers de filtros
require_once __DIR__ . '/../includes/filter_helpers.php';

function getSLAMedioProcessoSeletivoMesAnterior() {
    try {
        $pdo = connectDB(getProductionDatabaseName('processo_seletivo'));
        $stmt = $pdo->query("SELECT AVG(DATEDIFF(" . COL_DATA_FECHAMENTO_PROCESSO . ", " . COL_DATA_ABERTURA_PROCESSO . ")) as sla_medio FROM " . TABLE_PROCESSO . " WHERE " . COL_DATA_FECHAMENTO_PROCESSO . " IS NOT NULL AND " . COL_STATUS_PROCESSO . " = 'Fechado' AND MONTH(" . COL_DATA_FECHAMENTO_PROCESSO . ") = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) AND YEAR(" . COL_DATA_FECHAMENTO_PROCESSO . ") = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['sla_medio'] ? round($result['sla_medio'], 1) : 0;
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar 0
        return 0;
    }
}

function getChamadosStatusProcessoSeletivo() {
    try {
        $pdo = connectDB(getProductionDatabaseName('processo_seletivo'));
        $stmt = $pdo->query("SELECT 
            CASE 
                WHEN " . COL_STATUS_PROCESSO . " = '' OR " . COL_STATUS_PROCESSO . " IS NULL THEN 'Não informado'
                ELSE " . COL_STATUS_PROCESSO . "
            END as status,
            COUNT(*) as count 
            FROM " . TABLE_PROCESSO . " 
            GROUP BY 
            CASE 
                WHEN " . COL_STATUS_PROCESSO . " = '' OR " . COL_STATUS_PROCESSO . " IS NULL THEN 'Não informado'
                ELSE " . COL_STATUS_PROCESSO . "
            END");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar array vazio
        return [];
    }
}

function getSLAMedioProcessoSeletivo() {
    try {
        $pdo = connectDB(getProductionDatabaseName('processo_seletivo'));
        $stmt = $pdo->query("SELECT AVG(DATEDIFF(" . COL_DATA_FECHAMENTO_PROCESSO . ", " . COL_DATA_ABERTURA_PROCESSO . ")) as sla_medio FROM " . TABLE_PROCESSO . " WHERE " . COL_DATA_FECHAMENTO_PROCESSO . " IS NOT NULL AND " . COL_STATUS_PROCESSO . " = 'finalizado'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['sla_medio'] ? round($result['sla_medio'], 1) : 0;
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar 0
        return 0;
    }
}

function getServicosSolicitadosProcessoSeletivo() {
    $pdo = connectDB(getProductionDatabaseName('processo_seletivo'));
    try {
        $stmt = $pdo->query("SELECT 
            CASE 
                WHEN " . COL_SERVICO_PROCESSO . " = '' OR " . COL_SERVICO_PROCESSO . " IS NULL THEN 'Categoria não informada'
                ELSE " . COL_SERVICO_PROCESSO . "
            END as categoria,
            COUNT(*) as count 
            FROM " . TABLE_PROCESSO . " 
            GROUP BY 
            CASE 
                WHEN " . COL_SERVICO_PROCESSO . " = '' OR " . COL_SERVICO_PROCESSO . " IS NULL THEN 'Categoria não informada'
                ELSE " . COL_SERVICO_PROCESSO . "
            END");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Se a coluna não existir, retornar array vazio
        return [];
    }
}

// Funções filtradas por período
function getChamadosStatusProcessoSeletivoFiltered($periodo) {
    try {
        $pdo = connectDB(getProductionDatabaseName('processo_seletivo'));
        $whereClause = getPeriodWhereClause($periodo, COL_DATA_ABERTURA_PROCESSO);
        $stmt = $pdo->query("SELECT 
            CASE 
                WHEN " . COL_STATUS_PROCESSO . " = '' OR " . COL_STATUS_PROCESSO . " IS NULL THEN 'Não informado'
                ELSE " . COL_STATUS_PROCESSO . "
            END as status,
            COUNT(*) as count 
            FROM " . TABLE_PROCESSO . " 
            WHERE 1=1" . $whereClause . " 
            GROUP BY 
            CASE 
                WHEN " . COL_STATUS_PROCESSO . " = '' OR " . COL_STATUS_PROCESSO . " IS NULL THEN 'Não informado'
                ELSE " . COL_STATUS_PROCESSO . "
            END");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function getSLAMedioProcessoSeletivoFiltered($periodo) {
    try {
        $pdo = connectDB(getProductionDatabaseName('processo_seletivo'));
        $whereClause = getPeriodWhereClause($periodo, COL_DATA_FECHAMENTO_PROCESSO);
        $stmt = $pdo->query("SELECT AVG(DATEDIFF(" . COL_DATA_FECHAMENTO_PROCESSO . ", " . COL_DATA_ABERTURA_PROCESSO . ")) as sla_medio FROM " . TABLE_PROCESSO . " WHERE " . COL_DATA_FECHAMENTO_PROCESSO . " IS NOT NULL AND " . COL_STATUS_PROCESSO . " = 'Fechado'" . $whereClause);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['sla_medio'] ? round($result['sla_medio'], 1) : 0;
    } catch (Exception $e) {
        return 0;
    }
}

function getServicosSolicitadosProcessoSeletivoFiltered($periodo) {
    try {
        $pdo = connectDB(getProductionDatabaseName('processo_seletivo'));
        $whereClause = getPeriodWhereClause($periodo, COL_DATA_ABERTURA_PROCESSO);
        $stmt = $pdo->query("SELECT 
            CASE 
                WHEN " . COL_SERVICO_PROCESSO . " = '' OR " . COL_SERVICO_PROCESSO . " IS NULL THEN 'Categoria não informada'
                ELSE " . COL_SERVICO_PROCESSO . "
            END as categoria,
            COUNT(*) as count 
            FROM " . TABLE_PROCESSO . " 
            WHERE 1=1" . $whereClause . " 
            GROUP BY 
            CASE 
                WHEN " . COL_SERVICO_PROCESSO . " = '' OR " . COL_SERVICO_PROCESSO . " IS NULL THEN 'Categoria não informada'
                ELSE " . COL_SERVICO_PROCESSO . "
            END");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function getTotalChamadosProcessoSeletivo() {
    try {
        $pdo = connectDB(getProductionDatabaseName('processo_seletivo'));
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM " . TABLE_PROCESSO);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar 0
        return 0;
    }
}
?>
