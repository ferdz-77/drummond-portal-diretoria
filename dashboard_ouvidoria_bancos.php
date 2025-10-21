<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Carregamento seguro dos arquivos
$erro_carregamento = null;
try {
    require_once 'includes/config_env.php';
    require_once 'data/data_ouvidoria.php';
} catch (Exception $e) {
    $erro_carregamento = $e->getMessage();
}

// Função para obter dados REAIS da ouvidoria (não simulados)
function getDadosReaisOuvidoria() {
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

$dados_reais = getDadosReaisOuvidoria();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Portal Ouvidoria - DADOS REAIS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container-fluid mt-4">
    <?php if ($erro_carregamento): ?>
        <div class="alert alert-danger">
            <h4>Erro de Carregamento</h4>
            <p><?php echo htmlspecialchars($erro_carregamento); ?></p>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Dashboard Portal Ouvidoria - DADOS REAIS</h1>
                <a href="dashboard.php" class="btn btn-outline-primary">← Dashboard Principal</a>
            </div>
        </div>
    </div>
    
    <!-- Alerta explicativo -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning">
                <h5><i class="fas fa-exclamation-triangle"></i> Dados Atualizados</h5>
                <p><strong>Anterior:</strong> Dados fictícios de bancos (BB, Itaú, Bradesco) que não existem no seu BD.</p>
                <p><strong>Atual:</strong> Dados REAIS extraídos diretamente do seu banco de dados de ouvidoria.</p>
                <p><small>Para criar categorias personalizadas como "bancos", seria necessário adicionar uma coluna específica na tabela.</small></p>
            </div>
        </div>
    </div>
    
    <!-- Cards de Resumo -->
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
                    <small>Dados reais do BD</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h5>SLA Médio</h5>
                    <h2><?php echo number_format($dados_reais['sla_medio'], 1); ?> dias</h2>
                    <small>Tempo médio real</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h5>Tipos Diferentes</h5>
                    <h2><?php echo $total_tipos; ?></h2>
                    <small>Categorias reais</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h5>Status Diferentes</h5>
                    <h2><?php echo $total_status; ?></h2>
                    <small>Estados reais</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Gráfico de Status REAIS -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Status dos Chamados (DADOS REAIS)</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($dados_reais['status_chamados'])): ?>
                        <canvas id="graficoStatus" width="400" height="300"></canvas>
                    <?php else: ?>
                        <div class="alert alert-info">Nenhum dado de status encontrado no banco</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Gráfico de Tipos REAIS -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Tipos de Manifestação (DADOS REAIS)</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($dados_reais['tipos_manifestacao'])): ?>
                        <canvas id="graficoTipos" width="400" height="300"></canvas>
                    <?php else: ?>
                        <div class="alert alert-info">Nenhum dado de tipos encontrado no banco</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tabelas com dados reais -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Detalhamento por Status (REAL)</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Quantidade</th>
                                <th>%</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dados_reais['status_chamados'] as $status): 
                                $perc = $total_chamados > 0 ? round(($status['count'] / $total_chamados) * 100, 1) : 0;
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($status[COL_STATUS_OUVIDORIA] ?: 'Não informado'); ?></td>
                                    <td><strong><?php echo $status['count']; ?></strong></td>
                                    <td><?php echo $perc; ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">Detalhamento por Tipo (REAL)</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Quantidade</th>
                                <th>%</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $total_tipos_count = array_sum(array_column($dados_reais['tipos_manifestacao'], 'count'));
                            foreach ($dados_reais['tipos_manifestacao'] as $tipo): 
                                $perc = $total_tipos_count > 0 ? round(($tipo['count'] / $total_tipos_count) * 100, 1) : 0;
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($tipo[COL_TIPO_OUVIDORIA] ?: 'Não informado'); ?></td>
                                    <td><strong><?php echo $tipo['count']; ?></strong></td>
                                    <td><?php echo $perc; ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Explicação sobre categorização -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Como Implementar Categorização Real (ex: por Bancos)</h5>
                </div>
                <div class="card-body">
                    <p>Se você quiser categorizar os dados reais (como era simulado com os bancos), você tem algumas opções:</p>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <h6 class="text-primary">Opção 1: Nova Coluna</h6>
                                    <p class="small">Adicionar uma coluna 'categoria' ou 'banco' na tabela de ouvidoria</p>
                                    <code>ALTER TABLE <?php echo TABLE_OUVIDORIA; ?> ADD categoria VARCHAR(50)</code>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card border-success">
                                <div class="card-body">
                                    <h6 class="text-success">Opção 2: Usar Coluna Existente</h6>
                                    <p class="small">Mapear uma coluna que já existe para categorias (ex: setor, origem)</p>
                                    <small>Analisar dados existentes para identificar padrões</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card border-warning">
                                <div class="card-body">
                                    <h6 class="text-warning">Opção 3: Tabela Auxiliar</h6>
                                    <p class="small">Criar tabela de categorias e relacionar com os chamados</p>
                                    <small>Mais flexível para múltiplas categorizações</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    <p class="text-muted">
                        <strong>Nota:</strong> Os dados mostrados acima são 100% reais do seu banco de dados. 
                        Para criar visualizações por "bancos" ou outras categorias específicas, 
                        seria necessário primeiro implementar essa classificação na estrutura de dados.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Gráfico de Status REAIS
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

// Gráfico de Tipos REAIS
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