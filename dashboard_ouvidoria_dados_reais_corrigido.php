<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once 'includes/config_env.php';
    require_once 'data/data_ouvidoria.php';
} catch (Exception $e) {
    die("Erro ao carregar arquivos: " . $e->getMessage());
}

// Função que força consulta direta para obter dados reais
function getDadosReaisComForcaTotal() {
    try {
        $dados = [
            'status_chamados' => getChamadosStatusOuvidoria(),
            'sla_medio' => getSLAMedioOuvidoria(),
            'tipos_manifestacao' => getTiposManifestacaoOuvidoria()
        ];
        
        // SEMPRE tentar obter total direto
        try {
            $pdo = connectDBEnvironment('portal_ouvidoria');
            
            // Total geral de registros
            $total_result = $pdo->query("SELECT COUNT(*) as total FROM " . TABLE_OUVIDORIA)->fetch(PDO::FETCH_ASSOC);
            $dados['total_real'] = $total_result['total'];
            
            // Se as funções não retornaram dados úteis, fazer consultas diretas
            if (empty($dados['status_chamados'])) {
                $status_query = "SELECT " . COL_STATUS_OUVIDORIA . " as status, COUNT(*) as count FROM " . TABLE_OUVIDORIA . " GROUP BY " . COL_STATUS_OUVIDORIA;
                $dados['status_chamados'] = $pdo->query($status_query)->fetchAll(PDO::FETCH_ASSOC);
            }
            
            if (empty($dados['tipos_manifestacao'])) {
                $tipos_query = "SELECT " . COL_TIPO_OUVIDORIA . " as tipo, COUNT(*) as count FROM " . TABLE_OUVIDORIA . " GROUP BY " . COL_TIPO_OUVIDORIA;
                $dados['tipos_manifestacao'] = $pdo->query($tipos_query)->fetchAll(PDO::FETCH_ASSOC);
            }
            
        } catch (Exception $e) {
            $dados['erro_consulta_direta'] = $e->getMessage();
            $dados['total_real'] = 0;
        }
        
        return $dados;
    } catch (Exception $e) {
        return [
            'erro' => $e->getMessage(),
            'status_chamados' => [],
            'sla_medio' => 0,
            'tipos_manifestacao' => [],
            'total_real' => 0
        ];
    }
}

$dados_reais = getDadosReaisComForcaTotal();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Ouvidoria - DADOS REAIS CORRIGIDO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Dashboard Ouvidoria - DADOS REAIS ✅</h1>
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
                <h5><i class="fas fa-check-circle"></i> Problema de Contagem Corrigido</h5>
                <p><strong>Agora mostrando o total real de <?php echo $dados_reais['total_real']; ?> registros</strong> extraídos diretamente da tabela.</p>
                <p>Esta versão força consultas diretas para garantir dados precisos.</p>
            </div>
        </div>
    </div>
    
    <!-- Cards de Métricas Reais -->
    <div class="row mb-4">
        <?php
        // Usar o total real obtido diretamente
        $total_chamados = $dados_reais['total_real'];
        $total_tipos = count($dados_reais['tipos_manifestacao']);
        $total_status = count($dados_reais['status_chamados']);
        ?>
        
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h5>Total de Chamados</h5>
                    <h2><?php echo $total_chamados; ?></h2>
                    <small>✅ Contagem direta da tabela</small>
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
            <div class="card bg-primary text-white">
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
                                        $count = isset($status['count']) ? $status['count'] : 0;
                                        $status_nome = isset($status['status']) ? $status['status'] : ($status[COL_STATUS_OUVIDORIA] ?? 'Não informado');
                                        $percentual = $total_chamados > 0 ? round(($count / $total_chamados) * 100, 1) : 0;
                                    ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($status_nome); ?></td>
                                            <td><strong><?php echo $count; ?></strong></td>
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
                                    <?php foreach ($dados_reais['tipos_manifestacao'] as $tipo): 
                                        $count = isset($tipo['count']) ? $tipo['count'] : 0;
                                        $tipo_nome = isset($tipo['tipo']) ? $tipo['tipo'] : ($tipo[COL_TIPO_OUVIDORIA] ?? 'Não informado');
                                        $percentual = $total_chamados > 0 ? round(($count / $total_chamados) * 100, 1) : 0;
                                    ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($tipo_nome); ?></td>
                                            <td><strong><?php echo $count; ?></strong></td>
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
    
    <!-- Informações sobre correção -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">✅ Problema Resolvido</h5>
                </div>
                <div class="card-body">
                    <p><strong>O que foi corrigido:</strong></p>
                    <ul>
                        <li>✅ Total agora mostra <strong><?php echo $total_chamados; ?> registros</strong> (antes mostrava 0)</li>
                        <li>✅ Consultas diretas forçadas para garantir dados precisos</li>
                        <li>✅ Tratamento robusto de estruturas de dados diferentes</li>
                        <li>✅ Fallbacks automáticos se as funções auxiliares falharem</li>
                    </ul>
                    
                    <?php if (isset($dados_reais['erro_consulta_direta'])): ?>
                        <div class="alert alert-warning">
                            <strong>Nota:</strong> Houve um problema com consulta direta: 
                            <?php echo htmlspecialchars($dados_reais['erro_consulta_direta']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Gráfico de Status
<?php if (!empty($dados_reais['status_chamados'])): ?>
const statusData = {
    labels: [<?php foreach ($dados_reais['status_chamados'] as $s): 
        $status_nome = isset($s['status']) ? $s['status'] : ($s[COL_STATUS_OUVIDORIA] ?? 'Não informado');
        ?>'<?php echo addslashes($status_nome); ?>',<?php endforeach; ?>],
    datasets: [{
        data: [<?php foreach ($dados_reais['status_chamados'] as $s): 
            $count = isset($s['count']) ? $s['count'] : 0;
            ?><?php echo $count; ?>,<?php endforeach; ?>],
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
    labels: [<?php foreach ($dados_reais['tipos_manifestacao'] as $t): 
        $tipo_nome = isset($t['tipo']) ? $t['tipo'] : ($t[COL_TIPO_OUVIDORIA] ?? 'Não informado');
        ?>'<?php echo addslashes($tipo_nome); ?>',<?php endforeach; ?>],
    datasets: [{
        data: [<?php foreach ($dados_reais['tipos_manifestacao'] as $t): 
            $count = isset($t['count']) ? $t['count'] : 0;
            ?><?php echo $count; ?>,<?php endforeach; ?>],
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