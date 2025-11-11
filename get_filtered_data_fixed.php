<?php
// Incluir arquivo de configuração
require_once 'includes/config.php';

// Incluir todos os arquivos de dados
require_once 'data/data_ouvidoria.php';
require_once 'data/data_ead.php';
require_once 'data/data_processo_seletivo.php';
require_once 'data/data_secretaria.php';
require_once 'data/data_financeiro.php';
require_once 'data/data_exaluno.php';

// Função para gerar cláusula WHERE baseada no período
function getPeriodWhereClause($periodo, $dataAberturaCol, $dataFechamentoCol = null) {
    if ($periodo === 'todos') {
        return '';
    }

    $now = date('Y-m-d H:i:s');
    $where = '';

    switch ($periodo) {
        case 'hoje':
            $where = "DATE($dataAberturaCol) = CURDATE()";
            break;
        case 'semana':
            $where = "YEARWEEK($dataAberturaCol, 1) = YEARWEEK(CURDATE(), 1)";
            break;
        case 'mes':
            $where = "MONTH($dataAberturaCol) = MONTH(CURDATE()) AND YEAR($dataAberturaCol) = YEAR(CURDATE())";
            break;
        case 'trimestre':
            $quarter = ceil(date('n') / 3);
            $year = date('Y');
            $where = "QUARTER($dataAberturaCol) = $quarter AND YEAR($dataAberturaCol) = $year";
            break;
        case 'ano':
            $where = "YEAR($dataAberturaCol) = YEAR(CURDATE())";
            break;
    }

    return $where ? " AND $where" : '';
}

// Função principal para obter dados filtrados
function getFilteredPortalData($periodo = 'todos', $portal = 'todos') {
    $data = [];
    
    try {
        // Determinar quais portais processar
        $portais = [];
        if ($portal === 'todos') {
            $portais = ['ouvidoria', 'ead', 'processo_seletivo', 'secretaria', 'financeiro', 'exaluno'];
        } else {
            $portais = [$portal];
        }
        
        // Coletar dados de cada portal
        foreach ($portais as $portalName) {
            $portalData = getPortalData($portalName, $periodo);
            if ($portalData) {
                $data[$portalName] = $portalData;
            }
        }
    } catch (Exception $e) {
        error_log("Erro ao obter dados filtrados: " . $e->getMessage());
        return false;
    }
    
    return $data;
}

// Função para obter dados de um portal específico
function getPortalData($portal, $periodo) {
    global $conn_ouvidoria, $conn_ead, $conn_processo_seletivo, $conn_secretaria, $conn_financeiro, $conn_exaluno;
    
    $data = [];
    
    try {
        switch ($portal) {
            case 'ouvidoria':
                $data = [
                    'status' => getChamadosStatusOuvidoriaFiltered($periodo),
                    'sla' => getSLAMedioOuvidoriaFiltered($periodo),
                    'tipos' => getTipoManifestacaoOuvidoriaFiltered($periodo)
                ];
                break;
            case 'ead':
                $data = [
                    'status' => getChamadosStatusEadFiltered($periodo),
                    'sla' => getSLAMedioEadFiltered($periodo),
                    'servicos' => getServicosSolicitadosEadFiltered($periodo)
                ];
                break;
            case 'processo_seletivo':
                $data = [
                    'status' => getChamadosStatusProcessoSeletivoFiltered($periodo),
                    'sla' => getSLAMedioProcessoSeletivoFiltered($periodo),
                    'servicos' => getServicosSolicitadosProcessoSeletivoFiltered($periodo)
                ];
                break;
            case 'secretaria':
                $data = [
                    'status' => getChamadosStatusSecretariaFiltered($periodo),
                    'tempo' => getTempoMedioSecretariaFiltered($periodo),
                    'servicos' => getServicosSolicitadosSecretariaFiltered($periodo)
                ];
                break;
            case 'financeiro':
                $data = [
                    'status' => getChamadosStatusFinanceiroFiltered($periodo),
                    'sla' => getSLAMedioFinanceiroFiltered($periodo),
                    'servicos' => getServicosSolicitadosFinanceiroFiltered($periodo)
                ];
                break;
            case 'exaluno':
                $data = [
                    'status' => getChamadosStatusExAlunoFiltered($periodo),
                    'sla' => getSLAMedioExAlunoFiltered($periodo),
                    'servicos' => getServicosSolicitadosExAlunoFiltered($periodo)
                ];
                break;
        }
    } catch (Exception $e) {
        // Se houver erro, retornar dados vazios
        $data = [
            'status' => [],
            'sla' => 0,
            'tempo' => 0,
            'servicos' => [],
            'tipos' => []
        ];
    }

    return $data;
}

// Função para obter dados consolidados (todos os portais)
function getConsolidatedData($periodo) {
    $portals = ['ouvidoria', 'ead', 'processo_seletivo', 'secretaria', 'financeiro', 'exaluno'];
    $slaData = [];

    foreach ($portals as $portal) {
        $portalData = getPortalData($portal, $periodo);
        $sla = $portalData['sla'] ?? $portalData['tempo'] ?? 0;

        $slaData[] = [
            'portal' => ucfirst(str_replace('_', ' ', $portal)),
            'sla' => $sla
        ];
    }

    // Ordenar por SLA do menor para o maior
    usort($slaData, function($a, $b) {
        return $a['sla'] <=> $b['sla'];
    });

    return [
        'labels' => array_column($slaData, 'portal'),
        'data' => array_column($slaData, 'sla')
    ];
}

// Verificar se usuário está logado (apenas para requisições POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['usuario_logado'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Usuário não autenticado']);
        exit;
    }

    // Obter parâmetros dos filtros
    $periodo = $_POST['periodo'] ?? 'todos';
    $portal = $_POST['portal'] ?? 'todos';
    
    // Processar requisição
    $response = [];

    if ($portal === 'todos') {
        // Retornar dados consolidados de todos os portais
        $response['consolidado'] = getConsolidatedData($periodo);
    } else {
        // Retornar dados de um portal específico
        $response = array_merge($response, getFilteredPortalData($periodo, $portal));
    }

    // Retornar resposta JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>