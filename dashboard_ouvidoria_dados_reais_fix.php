<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once 'includes/config_env.php';
    require_once 'data/data_ouvidoria.php';
} catch (Exception $e) {
    die("Erro ao carregar arquivos: " . $e->getMessage());
}

// Função simples para obter dados reais
function getDadosReaisSimples() {
    try {
        return [
            'status_chamados' => getChamadosStatusOuvidoria(),
            'sla_medio' => getSLAMedioOuvidoria(),
            'tipos_manifestacao' => getTiposManifestacaoOuvidoria()
        ];
    } catch (Exception $e) {
        return [
            'erro' => $e->getMessage(),
            'status_chamados' => [],
            'sla_medio' => 0,
            'tipos_manifestacao' => []
        ];
    }
}

$dados_reais = getDadosReaisSimples();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Ouvidoria - DADOS REAIS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Dashboard Ouvidoria - DADOS REAIS</h1>
                <div>
                    <a href="dashboard_ouvidoria_bancos.php" class="btn btn-outline-secondary me-2">← Dashboard Bancos</a>
                    <a href="dashboard.php" class="btn btn-outline-primary">← Dashboard Principal</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Alerta informativo -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-success">
                <h5><i class="fas fa-check-circle"></i> Dados 100% Reais</h5>
                <p><strong>Esta página mostra apenas dados extraídos diretamente do seu banco de dados de ouvidoria.</strong></p>
                <p>Não há dados fictícios ou simulados - tudo é baseado nos registros reais da sua tabela.</p>
            </div>
        </div>
    </div>
    
    <?php if (isset($dados_reais['erro'])): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-danger">
                    <h5>Erro ao Carregar Dados</h5>
                    <p><?php echo htmlspecialchars($dados_reais['erro']); ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Cards de Métricas Reais -->
    <div class="row mb-4">
        <?php
        $total_chamados = array_sum(array_column($dados_reais['status_chamados'], 'count'));
        $total_tipos = count($dados_reais['tipos_manifestacao']);
        $total_status = count($dados_reais['status_chamados']);
        ?>
        
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h5>Total de Chamados</h5>
                    <h2><?php echo $total_chamados; ?></h2>
                    <small>Registros reais no BD</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h5>SLA Médio</h5>
                    <h2><?php echo number_format($dados_reais['sla_medio'], 1); ?> dias</h2>
                    <small>Calculado dos dados reais</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h5>Tipos de Manifestação</h5>
                    <h2><?php echo $total_tipos; ?></h2>
                    <small>Categorias encontradas</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h5>Status Diferentes</h5>
                    <h2><?php echo $total_status; ?></h2>
                    <small>Estados dos chamados</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Gráficos -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Status dos Chamados (DADOS REAIS)</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($dados_reais['status_chamados'])): ?>
                        <canvas id="graficoStatus" width="400" height="300"></canvas>
                    <?php else: ?>
                        <div class="alert alert-warning">Nenhum dado de status encontrado</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Tipos de Manifestação (DADOS REAIS)</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($dados_reais['tipos_manifestacao'])): ?>
                        <canvas id="graficoTipos" width="400" height="300"></canvas>
                    <?php else: ?>
                        <div class="alert alert-warning">Nenhum dado de tipos encontrado</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tabelas Detalhadas -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Detalhamento por Status</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($dados_reais['status_chamados'])): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Status</th>
                                        <th>Quantidade</th>
                                        <th>Percentual</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dados_reais['status_chamados'] as $status): 
                                        $percentual = $total_chamados > 0 ? round(($status['count'] / $total_chamados) * 100, 1) : 0;
                                    ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($status[COL_STATUS_OUVIDORIA] ?: 'Não informado'); ?></td>
                                            <td><strong><?php echo $status['count']; ?></strong></td>
                                            <td><?php echo $percentual; ?>%</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">Nenhum dado de status disponível</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">Detalhamento por Tipo</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($dados_reais['tipos_manifestacao'])): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Quantidade</th>
                                        <th>Percentual</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $total_tipos_count = array_sum(array_column($dados_reais['tipos_manifestacao'], 'count'));
                                    foreach ($dados_reais['tipos_manifestacao'] as $tipo): 
                                        $percentual = $total_tipos_count > 0 ? round(($tipo['count'] / $total_tipos_count) * 100, 1) : 0;
                                    ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($tipo[COL_TIPO_OUVIDORIA] ?: 'Não informado'); ?></td>
                                            <td><strong><?php echo $tipo['count']; ?></strong></td>
                                            <td><?php echo $percentual; ?>%</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">Nenhum dado de tipos disponível</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Informações sobre Categorização -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Como Implementar Categorização Personalizada</h5>
                </div>
                <div class="card-body">
                    <p>Se você quiser categorizar os dados por entidades específicas (como bancos, setores, etc.), você pode:</p>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card border-primary h-100">
                                <div class="card-body">
                                    <h6 class="text-primary">Opção 1: Nova Coluna</h6>
                                    <p class="small">Adicionar uma coluna para categorização na tabela existente.</p>
                                    <code class="small">ALTER TABLE tabela ADD categoria VARCHAR(50)</code>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card border-success h-100">
                                <div class="card-body">
                                    <h6 class="text-success">Opção 2: Coluna Existente</h6>
                                    <p class="small">Usar uma coluna que já existe para criar agrupamentos.</p>
                                    <small>Analisar campos existentes que permitam categorização</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card border-warning h-100">
                                <div class="card-body">
                                    <h6 class="text-warning">Opção 3: Sistema de Tags</h6>
                                    <p class="small">Criar sistema de tags/categorias com tabela auxiliar.</p>
                                    <small>Mais flexível para múltiplas categorizações</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    <div class="alert alert-light">
                        <strong>Nota:</strong> Todos os dados mostrados nesta página são extraídos diretamente do seu banco de dados. 
                        Para criar visualizações específicas por categorias personalizadas, seria necessário primeiro 
                        implementar essa estrutura na base de dados.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Gráfico de Status
<?php if (!empty($dados_reais['status_chamados'])): ?>
const statusData = {
    labels: [<?php foreach ($dados_reais['status_chamados'] as $s): ?>'<?php echo addslashes($s[COL_STATUS_OUVIDORIA] ?: 'Não informado'); ?>',<?php endforeach; ?>],
    datasets: [{
        data: [<?php foreach ($dados_reais['status_chamados'] as $s): ?><?php echo $s['count']; ?>,<?php endforeach; ?>],
        backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6c757d', '#17a2b8', '#fd7e14', '#e83e8c']
    }]
};

new Chart(document.getElementById('graficoStatus'), {
    type: 'pie',
    data: statusData,
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' },
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
<?php endif; ?>

// Gráfico de Tipos
<?php if (!empty($dados_reais['tipos_manifestacao'])): ?>
const tiposData = {
    labels: [<?php foreach ($dados_reais['tipos_manifestacao'] as $t): ?>'<?php echo addslashes($t[COL_TIPO_OUVIDORIA] ?: 'Não informado'); ?>',<?php endforeach; ?>],
    datasets: [{
        data: [<?php foreach ($dados_reais['tipos_manifestacao'] as $t): ?><?php echo $t['count']; ?>,<?php endforeach; ?>],
        backgroundColor: ['#fd7e14', '#20c997', '#e83e8c', '#6f42c1', '#17a2b8', '#28a745', '#ffc107', '#dc3545']
    }]
};

new Chart(document.getElementById('graficoTipos'), {
    type: 'doughnut',
    data: tiposData,
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.label + ': ' + context.parsed + ' manifestações';
                    }
                }
            }
        }
    }
});
<?php endif; ?>
</script>
</body>
</html>