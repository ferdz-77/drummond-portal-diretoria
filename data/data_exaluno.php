<?php
// Incluir helpers de filtros
require_once __DIR__ . '/../includes/filter_helpers.php';

function getSLAMedioExAlunoMesAnterior() {
    try {
        $pdo = connectDB(getProductionDatabaseName('exaluno'));
        $stmt = $pdo->query("SELECT AVG(DATEDIFF(" . COL_DATA_FECHAMENTO_EXALUNO . ", " . COL_DATA_ABERTURA_EXALUNO . ")) as sla_medio FROM " . TABLE_EXALUNO . " WHERE " . COL_DATA_FECHAMENTO_EXALUNO . " IS NOT NULL AND " . COL_STATUS_EXALUNO . " = 'Fechado' AND MONTH(" . COL_DATA_FECHAMENTO_EXALUNO . ") = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) AND YEAR(" . COL_DATA_FECHAMENTO_EXALUNO . ") = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['sla_medio'] ? round($result['sla_medio'], 1) : 0;
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar 0
        return 0;
    }
}

function getChamadosStatusExAluno() {
    try {
        $pdo = connectDB(getProductionDatabaseName('exaluno'));
        $stmt = $pdo->query("SELECT " . COL_STATUS_EXALUNO . ", COUNT(*) as count FROM " . TABLE_EXALUNO . " GROUP BY " . COL_STATUS_EXALUNO);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar array vazio
        return [];
    }
}

function getSLAMedioExAluno() {
    try {
        $pdo = connectDB(getProductionDatabaseName('exaluno'));
        $stmt = $pdo->query("SELECT AVG(DATEDIFF(" . COL_DATA_FECHAMENTO_EXALUNO . ", " . COL_DATA_ABERTURA_EXALUNO . ")) as sla_medio FROM " . TABLE_EXALUNO . " WHERE " . COL_DATA_FECHAMENTO_EXALUNO . " IS NOT NULL AND " . COL_STATUS_EXALUNO . " = 'Transferido'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['sla_medio'] ? round($result['sla_medio'], 1) : 0;
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar 0
        return 0;
    }
}

function getServicosSolicitadosExAluno() {
    $pdo = connectDB(getProductionDatabaseName('exaluno'));
    try {
        $stmt = $pdo->query("SELECT " . COL_SERVICO_EXALUNO . ", COUNT(*) as count FROM " . TABLE_EXALUNO . " GROUP BY " . COL_SERVICO_EXALUNO);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Se a coluna não existir, retornar array vazio
        return [];
    }
}

// Funções filtradas por período
function getChamadosStatusExAlunoFiltered($periodo) {
    try {
        $pdo = connectDB(getProductionDatabaseName('exaluno'));
        $whereClause = getPeriodWhereClause($periodo, COL_DATA_ABERTURA_EXALUNO);
        $stmt = $pdo->query("SELECT " . COL_STATUS_EXALUNO . ", COUNT(*) as count FROM " . TABLE_EXALUNO . " WHERE 1=1" . $whereClause . " GROUP BY " . COL_STATUS_EXALUNO);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function getSLAMedioExAlunoFiltered($periodo) {
    try {
        $pdo = connectDB(getProductionDatabaseName('exaluno'));
        $whereClause = getPeriodWhereClause($periodo, COL_DATA_FECHAMENTO_EXALUNO);
        $stmt = $pdo->query("SELECT AVG(DATEDIFF(" . COL_DATA_FECHAMENTO_EXALUNO . ", " . COL_DATA_ABERTURA_EXALUNO . ")) as sla_medio FROM " . TABLE_EXALUNO . " WHERE " . COL_DATA_FECHAMENTO_EXALUNO . " IS NOT NULL AND " . COL_STATUS_EXALUNO . " = 'Fechado'" . $whereClause);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['sla_medio'] ? round($result['sla_medio'], 1) : 0;
    } catch (Exception $e) {
        return 0;
    }
}

function getServicosSolicitadosExAlunoFiltered($periodo) {
    try {
        $pdo = connectDB(getProductionDatabaseName('exaluno'));
        $whereClause = getPeriodWhereClause($periodo, COL_DATA_ABERTURA_EXALUNO);
        $stmt = $pdo->query("SELECT " . COL_SERVICO_EXALUNO . ", COUNT(*) as count FROM " . TABLE_EXALUNO . " WHERE 1=1" . $whereClause . " GROUP BY " . COL_SERVICO_EXALUNO);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function getTotalChamadosExAluno() {
    try {
        $pdo = connectDB(getProductionDatabaseName('exaluno'));
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM " . TABLE_EXALUNO);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar 0
        return 0;
    }
}
?>
