<?php
// Iniciar sessão apenas se não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    @session_start(); // Suprime warnings se sessão já existe
}

// Verificar se usuário está logado
if (!isset($_SESSION['usuario_logado'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit;
}

// Incluir arquivo de configuração
require_once 'includes/config_env_simplificado.php';

// Incluir helpers de filtros
require_once 'includes/filter_helpers.php';

// Incluir todos os arquivos de dados
require_once 'data/data_ouvidoria.php';
require_once 'data/data_ead.php';
require_once 'data/data_processo_seletivo.php';
require_once 'data/data_secretaria.php';
require_once 'data/data_financeiro.php';
require_once 'data/data_exaluno.php';

// Função principal para obter dados filtrados
function getFilteredPortalData($portal, $periodo = 'todos') {
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
                    'tipos' => getTiposManifestacaoOuvidoriaFiltered($periodo)
                ];
                break;
            case 'ead':
                $data = [
                    'status' => getChamadosStatusEADFiltered($periodo),
                    'sla' => getSLAMedioEADFiltered($periodo),
                    'servicos' => getServicosSolicitadosEADFiltered($periodo)
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
                    'status' => getSolicitacoesStatusSecretariaFiltered($periodo),
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

// Verificar se é uma requisição POST (API endpoint)
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obter parâmetros dos filtros
    $periodo = $_POST['periodo'] ?? 'todos';
    $portal = $_POST['portal'] ?? 'todos';
    $acao = $_POST['acao'] ?? 'dados';

    // Processar requisição
    $response = [];

    if ($acao === 'get_chamados') {
        $status = $_POST['status'] ?? 'todos';
        $pagina = intval($_POST['pagina'] ?? 1);
        $limite = intval($_POST['limite'] ?? 50);

        $response = getAllChamados($portal, $status, $pagina, $limite);
    } elseif ($portal === 'todos') {
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

// Função para obter todos os chamados com filtros
function getAllChamados($portal_filter = 'todos', $status_filter = 'todos', $pagina = 1, $limite = 50) {
    $chamados = [];
    $total = 0;

    try {
        // Definir portais a consultar
        $portais = [];
        if ($portal_filter === 'todos') {
            $portais = [
                'ouvidoria' => ['table' => TABLE_OUVIDORIA, 'conn' => 'portal_ouvidoria', 'id_col' => COL_ID_OUVIDORIA, 'status_col' => COL_STATUS_OUVIDORIA, 'abertura_col' => COL_DATA_ABERTURA_OUVIDORIA, 'fechamento_col' => COL_DATA_FECHAMENTO_OUVIDORIA, 'servico_col' => COL_TIPO_OUVIDORIA, 'desc_col' => COL_DESCRICAO_OUVIDORIA],
                'ead' => ['table' => TABLE_EAD, 'conn' => 'portal_ead', 'id_col' => COL_ID_EAD, 'status_col' => COL_STATUS_EAD, 'abertura_col' => COL_DATA_ABERTURA_EAD, 'fechamento_col' => COL_DATA_FECHAMENTO_EAD, 'servico_col' => COL_SERVICO_EAD, 'desc_col' => COL_DESCRICAO_EAD],
                'processo_seletivo' => ['table' => TABLE_PROCESSO, 'conn' => 'portal_processo_seletivo', 'id_col' => COL_ID_PROCESSO, 'status_col' => COL_STATUS_PROCESSO, 'abertura_col' => COL_DATA_ABERTURA_PROCESSO, 'fechamento_col' => null, 'servico_col' => COL_SERVICO_PROCESSO, 'desc_col' => COL_DESCRICAO_PROCESSO], // Sem coluna de fechamento
                'secretaria' => ['table' => TABLE_SECRETARIA, 'conn' => 'portal_secretaria_academica', 'id_col' => COL_ID_SECRETARIA, 'status_col' => COL_STATUS_SECRETARIA, 'abertura_col' => COL_DATA_ABERTURA_SECRETARIA, 'fechamento_col' => COL_DATA_CONCLUSAO_SECRETARIA, 'servico_col' => COL_SERVICO_SECRETARIA, 'desc_col' => COL_DESCRICAO_SECRETARIA],
                'financeiro' => ['table' => TABLE_FINANCEIRO, 'conn' => 'portal_financeiro', 'id_col' => COL_ID_FINANCEIRO, 'status_col' => COL_STATUS_FINANCEIRO, 'abertura_col' => COL_DATA_ABERTURA_FINANCEIRO, 'fechamento_col' => COL_DATA_FECHAMENTO_FINANCEIRO, 'servico_col' => COL_SERVICO_FINANCEIRO, 'desc_col' => COL_DESCRICAO_FINANCEIRO],
                'exaluno' => ['table' => TABLE_EXALUNO, 'conn' => 'portal_exaluno', 'id_col' => COL_ID_EXALUNO, 'status_col' => COL_STATUS_EXALUNO, 'abertura_col' => COL_DATA_ABERTURA_EXALUNO, 'fechamento_col' => COL_DATA_FECHAMENTO_EXALUNO, 'servico_col' => COL_SERVICO_EXALUNO, 'desc_col' => COL_DESCRICAO_EXALUNO]
            ];
        } else {
            $portal_map = [
                'ouvidoria' => ['table' => TABLE_OUVIDORIA, 'conn' => 'portal_ouvidoria', 'id_col' => COL_ID_OUVIDORIA, 'status_col' => COL_STATUS_OUVIDORIA, 'abertura_col' => COL_DATA_ABERTURA_OUVIDORIA, 'fechamento_col' => COL_DATA_FECHAMENTO_OUVIDORIA, 'servico_col' => COL_TIPO_OUVIDORIA, 'desc_col' => COL_DESCRICAO_OUVIDORIA],
                'ead' => ['table' => TABLE_EAD, 'conn' => 'portal_ead', 'id_col' => COL_ID_EAD, 'status_col' => COL_STATUS_EAD, 'abertura_col' => COL_DATA_ABERTURA_EAD, 'fechamento_col' => COL_DATA_FECHAMENTO_EAD, 'servico_col' => COL_SERVICO_EAD, 'desc_col' => COL_DESCRICAO_EAD],
                'processo_seletivo' => ['table' => TABLE_PROCESSO, 'conn' => 'portal_processo_seletivo', 'id_col' => COL_ID_PROCESSO, 'status_col' => COL_STATUS_PROCESSO, 'abertura_col' => COL_DATA_ABERTURA_PROCESSO, 'fechamento_col' => null, 'servico_col' => COL_SERVICO_PROCESSO, 'desc_col' => COL_DESCRICAO_PROCESSO], // Sem coluna de fechamento
                'secretaria' => ['table' => TABLE_SECRETARIA, 'conn' => 'portal_secretaria_academica', 'id_col' => COL_ID_SECRETARIA, 'status_col' => COL_STATUS_SECRETARIA, 'abertura_col' => COL_DATA_ABERTURA_SECRETARIA, 'fechamento_col' => COL_DATA_CONCLUSAO_SECRETARIA, 'servico_col' => COL_SERVICO_SECRETARIA, 'desc_col' => COL_DESCRICAO_SECRETARIA],
                'financeiro' => ['table' => TABLE_FINANCEIRO, 'conn' => 'portal_financeiro', 'id_col' => COL_ID_FINANCEIRO, 'status_col' => COL_STATUS_FINANCEIRO, 'abertura_col' => COL_DATA_ABERTURA_FINANCEIRO, 'fechamento_col' => COL_DATA_FECHAMENTO_FINANCEIRO, 'servico_col' => COL_SERVICO_FINANCEIRO, 'desc_col' => COL_DESCRICAO_FINANCEIRO],
                'exaluno' => ['table' => TABLE_EXALUNO, 'conn' => 'portal_exaluno', 'id_col' => COL_ID_EXALUNO, 'status_col' => COL_STATUS_EXALUNO, 'abertura_col' => COL_DATA_ABERTURA_EXALUNO, 'fechamento_col' => COL_DATA_FECHAMENTO_EXALUNO, 'servico_col' => COL_SERVICO_EXALUNO, 'desc_col' => COL_DESCRICAO_EXALUNO]
            ];
            if (isset($portal_map[$portal_filter])) {
                $portais[$portal_filter] = $portal_map[$portal_filter];
            }
        }

        // Calcular offset para paginação
        $offset = ($pagina - 1) * $limite;

        // Buscar dados de cada portal
        foreach ($portais as $portal_nome => $config) {
            try {
                // Usar o nome correto do banco de dados baseado no ambiente
                $db_name = getProductionDatabaseName($config['conn']);
                $pdo = connectDB($db_name);

                // Construir query
                $where_clauses = [];

                // Filtro de status
                if ($status_filter !== 'todos') {
                    $where_clauses[] = $config['status_col'] . " = '" . $status_filter . "'";
                }

                $where_sql = !empty($where_clauses) ? " WHERE " . implode(" AND ", $where_clauses) : "";

                // Query para contar total
                $count_sql = "SELECT COUNT(*) as total FROM " . $config['table'] . $where_sql;
                $count_stmt = $pdo->query($count_sql);
                $count_result = $count_stmt->fetch(PDO::FETCH_ASSOC);
                $total += $count_result['total'];

                // Query para buscar dados com limite
                $select_cols = $config['id_col'] . " as id, " . $config['status_col'] . " as status, " .
                               $config['abertura_col'] . " as data_abertura, " .
                               ($config['fechamento_col'] ? $config['fechamento_col'] . " as data_fechamento, " : "NULL as data_fechamento, ") .
                               $config['servico_col'] . " as servico, " . $config['desc_col'] . " as descricao ";

                $sql = "SELECT " . $select_cols .
                       "FROM " . $config['table'] . $where_sql .
                       " ORDER BY " . $config['abertura_col'] . " DESC LIMIT $limite OFFSET $offset";

                $stmt = $pdo->query($sql);
                $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Adicionar portal aos resultados
                foreach ($resultados as &$chamado) {
                    $chamado['portal'] = ucfirst(str_replace('_', ' ', $portal_nome));
                }

                $chamados = array_merge($chamados, $resultados);

            } catch (Exception $e) {
                // Se um portal falhar, continua com os outros
                error_log("Erro ao buscar chamados do portal $portal_nome: " . $e->getMessage());
            }
        }

        // Ordenar todos os chamados por data de abertura (mais recente primeiro)
        usort($chamados, function($a, $b) {
            return strtotime($b['data_abertura']) - strtotime($a['data_abertura']);
        });

        // Limitar o resultado final se necessário
        $chamados = array_slice($chamados, 0, $limite);

        return [
            'chamados' => $chamados,
            'total' => $total,
            'pagina' => $pagina,
            'limite' => $limite,
            'total_paginas' => ceil($total / $limite)
        ];

    } catch (Exception $e) {
        error_log("Erro ao buscar todos os chamados: " . $e->getMessage());
        return [
            'chamados' => [],
            'total' => 0,
            'pagina' => $pagina,
            'limite' => $limite,
            'total_paginas' => 0,
            'erro' => $e->getMessage()
        ];
    }
}
?>
                $where_clauses = [];
                
                // Filtro de status
                if ($status_filter !== 'todos') {
                    $where_clauses[] = $config['status_col'] . " = '" . $status_filter . "'";
                }
                
                $where_sql = !empty($where_clauses) ? " WHERE " . implode(" AND ", $where_clauses) : "";
                
                // Query para contar total
                $count_sql = "SELECT COUNT(*) as total FROM " . $config['table'] . $where_sql;
                $count_stmt = $pdo->query($count_sql);
                $count_result = $count_stmt->fetch(PDO::FETCH_ASSOC);
                $total += $count_result['total'];
                
                // Query para buscar dados com limite
                $select_cols = $config['id_col'] . " as id, " . $config['status_col'] . " as status, " . 
                               $config['abertura_col'] . " as data_abertura, " . 
                               ($config['fechamento_col'] ? $config['fechamento_col'] . " as data_fechamento, " : "NULL as data_fechamento, ") . 
                               $config['servico_col'] . " as servico, " . $config['desc_col'] . " as descricao ";
                
                $sql = "SELECT " . $select_cols . 
                       "FROM " . $config['table'] . $where_sql . 
                       " ORDER BY " . $config['abertura_col'] . " DESC LIMIT $limite OFFSET $offset";
                
                $stmt = $pdo->query($sql);
                $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Adicionar portal aos resultados
                foreach ($resultados as &$chamado) {
                    $chamado['portal'] = ucfirst(str_replace('_', ' ', $portal_nome));
                }
                
                $chamados = array_merge($chamados, $resultados);
                
            } catch (Exception $e) {
                // Se um portal falhar, continua com os outros
                error_log("Erro ao buscar chamados do portal $portal_nome: " . $e->getMessage());
            }
        }
        
        // Ordenar todos os chamados por data de abertura (mais recente primeiro)
        usort($chamados, function($a, $b) {
            return strtotime($b['data_abertura']) - strtotime($a['data_abertura']);
        });
        
        // Limitar o resultado final se necessário
        $chamados = array_slice($chamados, 0, $limite);
        
        return [
            'chamados' => $chamados,
            'total' => $total,
            'pagina' => $pagina,
            'limite' => $limite,
            'total_paginas' => ceil($total / $limite)
        ];
        
    } catch (Exception $e) {
        error_log("Erro ao buscar todos os chamados: " . $e->getMessage());
        return [
            'chamados' => [],
            'total' => 0,
            'pagina' => $pagina,
            'limite' => $limite,
            'total_paginas' => 0,
            'erro' => $e->getMessage()
        ];
    }
}
?>