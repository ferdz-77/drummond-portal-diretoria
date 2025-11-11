<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Auto-login simples
if (!isset($_SESSION['usuario_logado'])) {
    $_SESSION['usuario_logado'] = true;
    $_SESSION['usuario_nome'] = 'Diretoria';
    $_SESSION['usuario_id'] = 'admin';
}

// Carregamento seguro
try {
    require_once 'includes/config_env.php';
} catch (Exception $e) {
    die("Erro de configuração: " . $e->getMessage());
}

// Função para coletar dados de cada portal de forma segura
function coletarDadosPortal($portal) {
    try {
        switch ($portal) {
            case 'ouvidoria':
                require_once 'data/data_ouvidoria.php';
                $status = getChamadosStatusOuvidoria();
                $total = array_sum(array_column($status, 'count'));
                return [
                    'nome' => 'Portal Ouvidoria',
                    'total' => $total,
                    'sla' => getSLAMedioOuvidoria(),
                    'status' => $status,
                    'url' => 'dashboard_ouvidoria_dados_reais.php',
                    'cor' => '#007bff',
                    'ativo' => true
                ];
                
            case 'ead':
                require_once 'data/data_ead.php';
                $status = getChamadosStatusEAD();
                $total = array_sum(array_column($status, 'count'));
                return [
                    'nome' => 'Portal EAD',
                    'total' => $total,
                    'sla' => getSLAMedioEAD(),
                    'status' => $status,
                    'url' => '#',
                    'cor' => '#28a745',
                    'ativo' => true
                ];
                
            case 'financeiro':
                require_once 'data/data_financeiro.php';
                $status = getChamadosStatusFinanceiro();
                $total = array_sum(array_column($status, 'count'));
                return [
                    'nome' => 'Portal Financeiro',
                    'total' => $total,
                    'sla' => getSLAMedioFinanceiro(),
                    'status' => $status,
                    'url' => '#',
                    'cor' => '#ffc107',
                    'ativo' => true
                ];
                
            case 'processo_seletivo':
                require_once 'data/data_processo_seletivo.php';
                $status = getChamadosStatusProcessoSeletivo();
                $total = array_sum(array_column($status, 'count'));
                return [
                    'nome' => 'Portal Processo Seletivo',
                    'total' => $total,
                    'sla' => getSLAMedioProcessoSeletivo(),
                    'status' => $status,
                    'url' => '#',
                    'cor' => '#dc3545',
                    'ativo' => true
                ];
                
            case 'secretaria':
                require_once 'data/data_secretaria.php';
                $status = getChamadosStatusSecretaria();
                $total = array_sum(array_column($status, 'count'));
                return [
                    'nome' => 'Portal Secretaria',
                    'total' => $total,
                    'sla' => getSLAMedioSecretaria(),
                    'status' => $status,
                    'url' => '#',
                    'cor' => '#17a2b8',
                    'ativo' => true
                ];
                
            case 'exaluno':
                require_once 'data/data_exaluno.php';
                $status = getChamadosStatusExAluno();
                $total = array_sum(array_column($status, 'count'));
                return [
                    'nome' => 'Portal Ex-Aluno',
                    'total' => $total,
                    'sla' => getSLAMedioExAluno(),
                    'status' => $status,
                    'url' => '#',
                    'cor' => '#6f42c1',
                    'ativo' => true
                ];
        }
    } catch (Exception $e) {
        // Se der erro, retorna dados padrão
        return [
            'nome' => ucfirst(str_replace('_', ' ', $portal)),
            'total' => 0,
            'sla' => 0,
            'status' => [],
            'url' => '#',
            'cor' => '#6c757d',
            'ativo' => false,
            'erro' => $e->getMessage()
        ];
    }
}

// Coletar dados de todos os portais
$portais = ['ouvidoria', 'ead', 'financeiro', 'processo_seletivo', 'secretaria', 'exaluno'];
$dados_portais = [];
$total_geral = 0;
$portais_ativos = 0;

foreach ($portais as $portal) {
    $dados_portais[$portal] = coletarDadosPortal($portal);
    if ($dados_portais[$portal]['ativo']) {
        $total_geral += $dados_portais[$portal]['total'];
        $portais_ativos++;
    }
}

// Calcular médias
$sla_medio_geral = 0;
if ($portais_ativos > 0) {
    $slas = array_filter(array_column($dados_portais, 'sla'));
    $sla_medio_geral = array_sum($slas) / count($slas);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Diretoria - Dashboard Geral</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .portal-card {
            transition: transform 0.2s;
            cursor: pointer;
        }
        .portal-card:hover {
            transform: translateY(-2px);
        }
        .portal-inactive {
            opacity: 0.6;
        }
    </style>
</head>
<body>
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>🏢 Portal Diretoria - Visão Geral</h1>
                <div>
                    <span class="badge bg-success"><?php echo $portais_ativos; ?> portais ativos</span>
                    <span class="badge bg-primary"><?php echo $total_geral; ?> chamados total</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Cards de Resumo Geral -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h5>Total de Chamados</h5>
                    <h2><?php echo $total_geral; ?></h2>
                    <small>Todos os portais</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h5>Portais Ativos</h5>
                    <h2><?php echo $portais_ativos; ?>/<?php echo count($portais); ?></h2>
                    <small>Sistemas funcionais</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h5>SLA Médio Geral</h5>
                    <h2><?php echo number_format($sla_medio_geral, 1); ?> dias</h2>
                    <small>Tempo médio de resolução</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h5>Status Sistema</h5>
                    <h2>✅ Operacional</h2>
                    <small>Dashboard funcionando</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Cards dos Portais Individuais -->
    <div class="row mb-4">
        <div class="col-12">
            <h3>📊 Situação por Portal</h3>
        </div>
    </div>
    
    <div class="row">
        <?php foreach ($dados_portais as $codigo => $portal): ?>
            <div class="col-md-4 mb-4">
                <div class="card portal-card <?php echo !$portal['ativo'] ? 'portal-inactive' : ''; ?>" 
                     <?php if ($portal['ativo'] && $portal['url'] != '#'): ?>
                     onclick="window.open('<?php echo $portal['url']; ?>', '_blank')"
                     <?php endif; ?>>
                    <div class="card-header text-white" style="background-color: <?php echo $portal['cor']; ?>">
                        <h5 class="mb-0">
                            <?php echo $portal['nome']; ?>
                            <?php if (!$portal['ativo']): ?>
                                <span class="badge bg-dark ms-2">Indisponível</span>
                            <?php endif; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <h4 class="text-primary"><?php echo $portal['total']; ?></h4>
                                <small>Total de Chamados</small>
                            </div>
                            <div class="col-6">
                                <h4 class="text-info"><?php echo number_format($portal['sla'], 1); ?> dias</h4>
                                <small>SLA Médio</small>
                            </div>
                        </div>
                        
                        <?php if (!empty($portal['status'])): ?>
                            <hr>
                            <small class="text-muted">Status mais frequentes:</small>
                            <?php 
                            $top_status = array_slice($portal['status'], 0, 3);
                            foreach ($top_status as $status): 
                            ?>
                                <div class="d-flex justify-content-between">
                                    <span><?php echo htmlspecialchars($status[COL_STATUS_OUVIDORIA] ?? $status['status'] ?? 'N/A'); ?></span>
                                    <span class="badge bg-secondary"><?php echo $status['count']; ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <?php if (isset($portal['erro'])): ?>
                            <div class="alert alert-danger mt-2">
                                <small>⚠️ <?php echo htmlspecialchars($portal['erro']); ?></small>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if ($portal['ativo'] && $portal['url'] != '#'): ?>
                        <div class="card-footer text-center">
                            <small class="text-muted">Clique para ver detalhes</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Gráfico Geral -->
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Distribuição de Chamados por Portal</h5>
                </div>
                <div class="card-body">
                    <canvas id="graficoPortais" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Links Rápidos</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="dashboard_ouvidoria_dados_reais.php" class="btn btn-outline-primary">
                            📊 Dashboard Ouvidoria Detalhado
                        </a>
                        <a href="dashboard_ouvidoria_bancos.php" class="btn btn-outline-info">
                            🏦 Dashboard Ouvidoria por Bancos
                        </a>
                        <a href="debug_dashboard_principal.php" class="btn btn-outline-warning">
                            🔍 Debug Dashboard Principal
                        </a>
                        <hr>
                        <small class="text-muted">
                            <strong>Sobre o Portal Diretoria:</strong><br>
                            Sistema centralizado que monitora todos os portais da instituição,
                            fornecendo visão consolidada para tomada de decisões estratégicas.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Gráfico de distribuição por portais
const portaisData = {
    labels: [<?php foreach ($dados_portais as $portal): ?>'<?php echo addslashes($portal['nome']); ?>',<?php endforeach; ?>],
    datasets: [{
        data: [<?php foreach ($dados_portais as $portal): ?><?php echo $portal['total']; ?>,<?php endforeach; ?>],
        backgroundColor: [<?php foreach ($dados_portais as $portal): ?>'<?php echo $portal['cor']; ?>',<?php endforeach; ?>]
    }]
};

new Chart(document.getElementById('graficoPortais'), {
    type: 'doughnut',
    data: portaisData,
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.label + ': ' + context.parsed + ' chamados';
                    }
                }
            }
        }
    }
});
</script>
</body>
</html>