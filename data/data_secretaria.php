<?php
// Incluir helpers de filtros
require_once __DIR__ . '/../includes/filter_helpers.php';

function getTempoMedioSecretariaMesAnterior() {
    try {
        $pdo = connectDB(getProductionDatabaseName('secretaria'));
        $stmt = $pdo->query("SELECT AVG(DATEDIFF(" . COL_DATA_CONCLUSAO_SECRETARIA . ", " . COL_DATA_ABERTURA_SECRETARIA . ")) as tempo_medio FROM " . TABLE_SECRETARIA . " WHERE " . COL_DATA_CONCLUSAO_SECRETARIA . " IS NOT NULL AND " . COL_STATUS_SECRETARIA . " = 'finalizado' AND MONTH(" . COL_DATA_CONCLUSAO_SECRETARIA . ") = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) AND YEAR(" . COL_DATA_CONCLUSAO_SECRETARIA . ") = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['tempo_medio'] ? round($result['tempo_medio'], 1) : 0;
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar 0
        return 0;
    }
}

function getStatusSecretaria() {
    try {
        $pdo = connectDB(getProductionDatabaseName('secretaria'));
        $stmt = $pdo->query("SELECT " . COL_STATUS_SECRETARIA . ", COUNT(*) as count FROM " . TABLE_SECRETARIA . " GROUP BY " . COL_STATUS_SECRETARIA);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar array vazio
        return [];
    }
}

function getSolicitacoesStatusSecretaria() {
    try {
        $pdo = connectDB(getProductionDatabaseName('secretaria'));
        $stmt = $pdo->query("SELECT " . COL_STATUS_SECRETARIA . ", COUNT(*) as count FROM " . TABLE_SECRETARIA . " GROUP BY " . COL_STATUS_SECRETARIA);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar array vazio
        return [];
    }
}

function getTempoMedioSecretaria() {
    try {
        $pdo = connectDB(getProductionDatabaseName('secretaria'));
        $stmt = $pdo->query("SELECT AVG(DATEDIFF(" . COL_DATA_CONCLUSAO_SECRETARIA . ", " . COL_DATA_ABERTURA_SECRETARIA . ")) as tempo_medio FROM " . TABLE_SECRETARIA . " WHERE " . COL_DATA_CONCLUSAO_SECRETARIA . " IS NOT NULL AND " . COL_STATUS_SECRETARIA . " = 'finalizado'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['tempo_medio'] ? round($result['tempo_medio'], 1) : 0;
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar 0
        return 0;
    }
}

function getServicosSolicitadosSecretaria() {
    try {
        $pdo = connectDB(getProductionDatabaseName('secretaria'));
        $stmt = $pdo->query("SELECT " . COL_SERVICO_SECRETARIA . ", COUNT(*) as count FROM " . TABLE_SECRETARIA . " GROUP BY " . COL_SERVICO_SECRETARIA);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar array vazio
        return [];
    }
}

function getChamadosFinalizadosSecretaria() {
    try {
        $pdo = connectDB(getProductionDatabaseName('secretaria'));
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM " . TABLE_SECRETARIA . " WHERE " . COL_STATUS_SECRETARIA . " IN ('finalizado', 'respondido', 'Fechado', 'Transferido')");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar 0
        return 0;
    }
}

// Funções para compatibilidade com o dashboard principal
function getChamadosStatusSecretaria() {
    return getStatusSecretaria();
}

function getSLAMedioSecretaria() {
    return getTempoMedioSecretaria();
}

// Funções filtradas por período
function getSolicitacoesStatusSecretariaFiltered($periodo) {
    try {
        $pdo = connectDB(getProductionDatabaseName('secretaria'));
        $whereClause = getPeriodWhereClause($periodo, COL_DATA_ABERTURA_SECRETARIA);
        $stmt = $pdo->query("SELECT " . COL_STATUS_SECRETARIA . ", COUNT(*) as count FROM " . TABLE_SECRETARIA . " WHERE 1=1" . $whereClause . " GROUP BY " . COL_STATUS_SECRETARIA);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function getTempoMedioSecretariaFiltered($periodo) {
    try {
        $pdo = connectDB(getProductionDatabaseName('secretaria'));
        $whereClause = getPeriodWhereClause($periodo, COL_DATA_CONCLUSAO_SECRETARIA);
        $stmt = $pdo->query("SELECT AVG(DATEDIFF(" . COL_DATA_CONCLUSAO_SECRETARIA . ", " . COL_DATA_ABERTURA_SECRETARIA . ")) as tempo_medio FROM " . TABLE_SECRETARIA . " WHERE " . COL_DATA_CONCLUSAO_SECRETARIA . " IS NOT NULL AND " . COL_STATUS_SECRETARIA . " = 'Fechado'" . $whereClause);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['tempo_medio'] ? round($result['tempo_medio'], 1) : 0;
    } catch (Exception $e) {
        return 0;
    }
}

function getServicosSolicitadosSecretariaFiltered($periodo) {
    try {
        $pdo = connectDB(getProductionDatabaseName('secretaria'));
        $whereClause = getPeriodWhereClause($periodo, COL_DATA_ABERTURA_SECRETARIA);
        $stmt = $pdo->query("SELECT " . COL_SERVICO_SECRETARIA . ", COUNT(*) as count FROM " . TABLE_SECRETARIA . " WHERE 1=1" . $whereClause . " GROUP BY " . COL_SERVICO_SECRETARIA);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function getTotalChamadosSecretaria() {
    try {
        $pdo = connectDB(getProductionDatabaseName('secretaria'));
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM " . TABLE_SECRETARIA);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    } catch (Exception $e) {
        // Se as colunas não existirem ou banco não existir, retornar 0
        return 0;
    }
}
?>
