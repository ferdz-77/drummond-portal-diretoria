<?php
// Incluir helpers de filtros
require_once __DIR__ . '/../includes/filter_helpers.php';

function getChamadosStatusFinanceiro() {
    try {
        $pdo = connectDB(getProductionDatabaseName('financeiro'));
        $stmt = $pdo->query("SELECT 
            CASE 
                WHEN " . COL_STATUS_FINANCEIRO . " = '' OR " . COL_STATUS_FINANCEIRO . " IS NULL THEN 'Não informado'
                ELSE " . COL_STATUS_FINANCEIRO . "
            END as status,
            COUNT(*) as count 
            FROM " . TABLE_FINANCEIRO . " 
            GROUP BY 
            CASE 
                WHEN " . COL_STATUS_FINANCEIRO . " = '' OR " . COL_STATUS_FINANCEIRO . " IS NULL THEN 'Não informado'
                ELSE " . COL_STATUS_FINANCEIRO . "
            END");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar array vazio
        return [];
    }
}

function getSLAMedioFinanceiro() {
    try {
        $pdo = connectDB(getProductionDatabaseName('financeiro'));
        $stmt = $pdo->query("SELECT AVG(DATEDIFF(" . COL_DATA_FECHAMENTO_FINANCEIRO . ", " . COL_DATA_ABERTURA_FINANCEIRO . ")) as sla_medio FROM " . TABLE_FINANCEIRO . " WHERE " . COL_DATA_FECHAMENTO_FINANCEIRO . " IS NOT NULL AND " . COL_STATUS_FINANCEIRO . " = 'finalizado'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['sla_medio'] ? round($result['sla_medio'], 1) : 0;
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar 0
        return 0;
    }
}

function getSLAMedioFinanceiroMesAnterior() {
    try {
        $pdo = connectDB(getProductionDatabaseName('financeiro'));
        $stmt = $pdo->query("SELECT AVG(DATEDIFF(" . COL_DATA_FECHAMENTO_FINANCEIRO . ", " . COL_DATA_ABERTURA_FINANCEIRO . ")) as sla_medio FROM " . TABLE_FINANCEIRO . " WHERE " . COL_DATA_FECHAMENTO_FINANCEIRO . " IS NOT NULL AND " . COL_STATUS_FINANCEIRO . " = 'finalizado' AND MONTH(" . COL_DATA_FECHAMENTO_FINANCEIRO . ") = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) AND YEAR(" . COL_DATA_FECHAMENTO_FINANCEIRO . ") = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['sla_medio'] ? round($result['sla_medio'], 1) : 0;
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar 0
        return 0;
    }
}

function getServicosSolicitadosFinanceiro() {
    try {
        $pdo = connectDB(getProductionDatabaseName('financeiro'));
        $stmt = $pdo->query("SELECT 
            CASE 
                WHEN " . COL_SERVICO_FINANCEIRO . " = '' OR " . COL_SERVICO_FINANCEIRO . " IS NULL THEN 'Categoria não informada'
                ELSE " . COL_SERVICO_FINANCEIRO . "
            END as categoria,
            COUNT(*) as count 
            FROM " . TABLE_FINANCEIRO . " 
            GROUP BY 
            CASE 
                WHEN " . COL_SERVICO_FINANCEIRO . " = '' OR " . COL_SERVICO_FINANCEIRO . " IS NULL THEN 'Categoria não informada'
                ELSE " . COL_SERVICO_FINANCEIRO . "
            END");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Se a coluna não existir, retornar array vazio
        return [];
    }
}

// Funções filtradas por período
function getChamadosStatusFinanceiroFiltered($periodo) {
    try {
        $pdo = connectDB(getProductionDatabaseName('financeiro'));
        $whereClause = getPeriodWhereClause($periodo, COL_DATA_ABERTURA_FINANCEIRO);
        $stmt = $pdo->query("SELECT 
            CASE 
                WHEN " . COL_STATUS_FINANCEIRO . " = '' OR " . COL_STATUS_FINANCEIRO . " IS NULL THEN 'Não informado'
                ELSE " . COL_STATUS_FINANCEIRO . "
            END as status,
            COUNT(*) as count 
            FROM " . TABLE_FINANCEIRO . " 
            WHERE 1=1" . $whereClause . " 
            GROUP BY 
            CASE 
                WHEN " . COL_STATUS_FINANCEIRO . " = '' OR " . COL_STATUS_FINANCEIRO . " IS NULL THEN 'Não informado'
                ELSE " . COL_STATUS_FINANCEIRO . "
            END");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function getSLAMedioFinanceiroFiltered($periodo) {
    try {
        $pdo = connectDB(getProductionDatabaseName('financeiro'));
        $whereClause = getPeriodWhereClause($periodo, COL_DATA_FECHAMENTO_FINANCEIRO);
        $stmt = $pdo->query("SELECT AVG(DATEDIFF(" . COL_DATA_FECHAMENTO_FINANCEIRO . ", " . COL_DATA_ABERTURA_FINANCEIRO . ")) as sla_medio FROM " . TABLE_FINANCEIRO . " WHERE " . COL_DATA_FECHAMENTO_FINANCEIRO . " IS NOT NULL AND " . COL_STATUS_FINANCEIRO . " = 'Fechado'" . $whereClause);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['sla_medio'] ? round($result['sla_medio'], 1) : 0;
    } catch (Exception $e) {
        return 0;
    }
}

function getServicosSolicitadosFinanceiroFiltered($periodo) {
    try {
        $pdo = connectDB(getProductionDatabaseName('financeiro'));
        $whereClause = getPeriodWhereClause($periodo, COL_DATA_ABERTURA_FINANCEIRO);
        $stmt = $pdo->query("SELECT 
            CASE 
                WHEN " . COL_SERVICO_FINANCEIRO . " = '' OR " . COL_SERVICO_FINANCEIRO . " IS NULL THEN 'Categoria não informada'
                ELSE " . COL_SERVICO_FINANCEIRO . "
            END as categoria,
            COUNT(*) as count 
            FROM " . TABLE_FINANCEIRO . " 
            WHERE 1=1" . $whereClause . " 
            GROUP BY 
            CASE 
                WHEN " . COL_SERVICO_FINANCEIRO . " = '' OR " . COL_SERVICO_FINANCEIRO . " IS NULL THEN 'Categoria não informada'
                ELSE " . COL_SERVICO_FINANCEIRO . "
            END");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function getTotalChamadosFinanceiro() {
    try {
        $pdo = connectDB(getProductionDatabaseName('financeiro'));
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM " . TABLE_FINANCEIRO);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar 0
        return 0;
    }
}
?>
