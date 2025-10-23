<?php
// Incluir helpers de filtros
require_once __DIR__ . '/../includes/filter_helpers.php';

function getChamadosStatusOuvidoria() {
    try {
        $pdo = connectDB(getProductionDatabaseName('ouvidoria'));
        $stmt = $pdo->query("SELECT " . COL_STATUS_OUVIDORIA . ", COUNT(*) as count FROM " . TABLE_OUVIDORIA . " GROUP BY " . COL_STATUS_OUVIDORIA);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar array vazio
        return [];
    }
}

function getSLAMedioOuvidoria() {
    try {
        $pdo = connectDB(getProductionDatabaseName('ouvidoria'));
        $stmt = $pdo->query("SELECT AVG(DATEDIFF(" . COL_DATA_FECHAMENTO_OUVIDORIA . ", " . COL_DATA_ABERTURA_OUVIDORIA . ")) as sla_medio FROM " . TABLE_OUVIDORIA . " WHERE " . COL_DATA_FECHAMENTO_OUVIDORIA . " IS NOT NULL AND " . COL_STATUS_OUVIDORIA . " = 'Transferido'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['sla_medio'] ? round($result['sla_medio'], 1) : 0;
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar 0
        return 0;
    }
}

function getSLAMedioOuvidoriaMesAnterior() {
    try {
        $pdo = connectDB(getProductionDatabaseName('ouvidoria'));
        $stmt = $pdo->query("SELECT AVG(DATEDIFF(" . COL_DATA_FECHAMENTO_OUVIDORIA . ", " . COL_DATA_ABERTURA_OUVIDORIA . ")) as sla_medio FROM " . TABLE_OUVIDORIA . " WHERE " . COL_DATA_FECHAMENTO_OUVIDORIA . " IS NOT NULL AND " . COL_STATUS_OUVIDORIA . " = 'Fechado' AND MONTH(" . COL_DATA_FECHAMENTO_OUVIDORIA . ") = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) AND YEAR(" . COL_DATA_FECHAMENTO_OUVIDORIA . ") = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['sla_medio'] ? round($result['sla_medio'], 1) : 0;
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar 0
        return 0;
    }
}

function getTiposManifestacaoOuvidoria() {
    try {
        $pdo = connectDB(getProductionDatabaseName('ouvidoria'));
        $stmt = $pdo->query("SELECT " . COL_TIPO_OUVIDORIA . ", COUNT(*) as count FROM " . TABLE_OUVIDORIA . " GROUP BY " . COL_TIPO_OUVIDORIA);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar array vazio
        return [];
    }
}

// Funções filtradas por período
function getChamadosStatusOuvidoriaFiltered($periodo) {
    try {
        $pdo = connectDBEnvironment('ouvidoria');
        $whereClause = getPeriodWhereClause($periodo, COL_DATA_ABERTURA_OUVIDORIA);
        $stmt = $pdo->query("SELECT " . COL_STATUS_OUVIDORIA . ", COUNT(*) as count FROM " . TABLE_OUVIDORIA . " WHERE 1=1" . $whereClause . " GROUP BY " . COL_STATUS_OUVIDORIA);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function getSLAMedioOuvidoriaFiltered($periodo) {
    try {
        $pdo = connectDBEnvironment('ouvidoria');
        $whereClause = getPeriodWhereClause($periodo, COL_DATA_FECHAMENTO_OUVIDORIA);
        $stmt = $pdo->query("SELECT AVG(DATEDIFF(" . COL_DATA_FECHAMENTO_OUVIDORIA . ", " . COL_DATA_ABERTURA_OUVIDORIA . ")) as sla_medio FROM " . TABLE_OUVIDORIA . " WHERE " . COL_DATA_FECHAMENTO_OUVIDORIA . " IS NOT NULL AND " . COL_STATUS_OUVIDORIA . " = 'Fechado'" . $whereClause);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['sla_medio'] ? round($result['sla_medio'], 1) : 0;
    } catch (Exception $e) {
        return 0;
    }
}

function getTiposManifestacaoOuvidoriaFiltered($periodo) {
    try {
        $pdo = connectDBEnvironment('ouvidoria');
        $whereClause = getPeriodWhereClause($periodo, COL_DATA_ABERTURA_OUVIDORIA);
        $stmt = $pdo->query("SELECT " . COL_TIPO_OUVIDORIA . ", COUNT(*) as count FROM " . TABLE_OUVIDORIA . " WHERE 1=1" . $whereClause . " GROUP BY " . COL_TIPO_OUVIDORIA);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function getTotalChamadosOuvidoria() {
    try {
        $pdo = connectDB(getProductionDatabaseName('ouvidoria'));
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM " . TABLE_OUVIDORIA);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar 0
        return 0;
    }
}
?>