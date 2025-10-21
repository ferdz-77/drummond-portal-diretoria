<?php
// Incluir helpers de filtros
require_once __DIR__ . '/../includes/filter_helpers.php';

function getChamadosStatusEAD() {
    try {
        $pdo = connectDBEnvironment('ead');
        $stmt = $pdo->query("SELECT 
            CASE 
                WHEN " . COL_STATUS_EAD . " = '' OR " . COL_STATUS_EAD . " IS NULL THEN 'Não informado'
                ELSE " . COL_STATUS_EAD . "
            END as status,
            COUNT(*) as count 
            FROM " . TABLE_EAD . " 
            GROUP BY 
            CASE 
                WHEN " . COL_STATUS_EAD . " = '' OR " . COL_STATUS_EAD . " IS NULL THEN 'Não informado'
                ELSE " . COL_STATUS_EAD . "
            END");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar array vazio
        return [];
    }
}

function getSLAMedioEAD() {
    try {
        $pdo = connectDBEnvironment('ead');
        $stmt = $pdo->query("SELECT AVG(DATEDIFF(" . COL_DATA_FECHAMENTO_EAD . ", " . COL_DATA_ABERTURA_EAD . ")) as sla_medio FROM " . TABLE_EAD . " WHERE " . COL_DATA_FECHAMENTO_EAD . " IS NOT NULL AND " . COL_STATUS_EAD . " = 'finalizado'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['sla_medio'] ? round($result['sla_medio'], 1) : 0;
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar 0
        return 0;
    }
}

function getSLAMedioEADMesAnterior() {
    try {
        $pdo = connectDBEnvironment('ead');
        $stmt = $pdo->query("SELECT AVG(DATEDIFF(" . COL_DATA_FECHAMENTO_EAD . ", " . COL_DATA_ABERTURA_EAD . ")) as sla_medio FROM " . TABLE_EAD . " WHERE " . COL_DATA_FECHAMENTO_EAD . " IS NOT NULL AND " . COL_STATUS_EAD . " = 'Fechado' AND MONTH(" . COL_DATA_FECHAMENTO_EAD . ") = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) AND YEAR(" . COL_DATA_FECHAMENTO_EAD . ") = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['sla_medio'] ? round($result['sla_medio'], 1) : 0;
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar 0
        return 0;
    }
}

function getServicosSolicitadosEAD() {
    $pdo = connectDBEnvironment('ead');
    try {
        $stmt = $pdo->query("SELECT 
            CASE 
                WHEN " . COL_SERVICO_EAD . " = '' OR " . COL_SERVICO_EAD . " IS NULL THEN 'Categoria não informada'
                ELSE " . COL_SERVICO_EAD . "
            END as categoria,
            COUNT(*) as count 
            FROM " . TABLE_EAD . " 
            GROUP BY 
            CASE 
                WHEN " . COL_SERVICO_EAD . " = '' OR " . COL_SERVICO_EAD . " IS NULL THEN 'Categoria não informada'
                ELSE " . COL_SERVICO_EAD . "
            END");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Se a coluna não existir, retornar array vazio
        return [];
    }
}

// Funções filtradas por período
function getChamadosStatusEADFiltered($periodo) {
    try {
        $pdo = connectDBEnvironment('ead');
        $whereClause = getPeriodWhereClause($periodo, COL_DATA_ABERTURA_EAD);
        $stmt = $pdo->query("SELECT 
            CASE 
                WHEN " . COL_STATUS_EAD . " = '' OR " . COL_STATUS_EAD . " IS NULL THEN 'Não informado'
                ELSE " . COL_STATUS_EAD . "
            END as status,
            COUNT(*) as count 
            FROM " . TABLE_EAD . " 
            WHERE 1=1" . $whereClause . " 
            GROUP BY 
            CASE 
                WHEN " . COL_STATUS_EAD . " = '' OR " . COL_STATUS_EAD . " IS NULL THEN 'Não informado'
                ELSE " . COL_STATUS_EAD . "
            END");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function getSLAMedioEADFiltered($periodo) {
    try {
        $pdo = connectDBEnvironment('ead');
        $whereClause = getPeriodWhereClause($periodo, COL_DATA_FECHAMENTO_EAD);
        $stmt = $pdo->query("SELECT AVG(DATEDIFF(" . COL_DATA_FECHAMENTO_EAD . ", " . COL_DATA_ABERTURA_EAD . ")) as sla_medio FROM " . TABLE_EAD . " WHERE " . COL_DATA_FECHAMENTO_EAD . " IS NOT NULL AND " . COL_STATUS_EAD . " = 'Fechado'" . $whereClause);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['sla_medio'] ? round($result['sla_medio'], 1) : 0;
    } catch (Exception $e) {
        return 0;
    }
}

function getServicosSolicitadosEADFiltered($periodo) {
    try {
        $pdo = connectDBEnvironment('ead');
        $whereClause = getPeriodWhereClause($periodo, COL_DATA_ABERTURA_EAD);
        $stmt = $pdo->query("SELECT 
            CASE 
                WHEN " . COL_SERVICO_EAD . " = '' OR " . COL_SERVICO_EAD . " IS NULL THEN 'Categoria não informada'
                ELSE " . COL_SERVICO_EAD . "
            END as categoria,
            COUNT(*) as count 
            FROM " . TABLE_EAD . " 
            WHERE 1=1" . $whereClause . " 
            GROUP BY 
            CASE 
                WHEN " . COL_SERVICO_EAD . " = '' OR " . COL_SERVICO_EAD . " IS NULL THEN 'Categoria não informada'
                ELSE " . COL_SERVICO_EAD . "
            END");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}
?>
