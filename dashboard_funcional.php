<?php
session_start();

// Incluir arquivo de configuração primeiro
try {
    require_once 'includes/config_env.php';
} catch (Exception $e) {
    die("Erro de configuração: " . $e->getMessage());
}

// Configurar tratamento de erro para produção
if ($environment === 'production') {
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/production_error.log');
    
    // Capturar erros fatais
    register_shutdown_function(function() {
        $error = error_get_last();
        if ($error && $error['type'] === E_ERROR) {
            header('Location: emergency_dashboard_production.php');
            exit;
        }
    });
}

// Auto-login em produção para facilitar acesso direto
if ($environment === 'production' && !isset($_SESSION['usuario_logado'])) {
    $_SESSION['usuario_logado'] = true;
    $_SESSION['usuario_nome'] = 'Diretoria';
    $_SESSION['usuario_id'] = 'admin';
}

// Verificar se usuário está logado
if (!isset($_SESSION['usuario_logado'])) {
    header('Location: login.php');
    exit;
}

// Obter parâmetros de filtro da URL
$periodo_filter = $_GET['periodo'] ?? 'todos';
$portal_filter = $_GET['portal'] ?? 'todos';

// Verificar modo emergência
$emergency_mode = isset($_GET['emergency']) && $_GET['emergency'] == '1';
if ($emergency_mode) {
    echo "<div style='background: #ff6b6b; color: white; padding: 10px; margin: 10px; border-radius: 5px;'>";
    echo "🚨 MODO EMERGÊNCIA ATIVADO - Apenas notificações funcionam";
    echo "</div>";
}

// Incluir todos os arquivos de dados com tratamento de erro
$arquivos_dados = [
    'data/data_ouvidoria.php',
    'data/data_ead.php',
    'data/data_processo_seletivo.php',
    'data/data_secretaria.php',
    'data/data_financeiro.php',
    'data/data_exaluno.php'
];

foreach ($arquivos_dados as $arquivo) {
    try {
        if (file_exists($arquivo)) {
            require_once $arquivo;
        } else {
            error_log("Arquivo não encontrado: $arquivo");
        }
    } catch (Exception $e) {
        error_log("Erro ao carregar $arquivo: " . $e->getMessage());
    }
}

// Incluir arquivo de dados filtrados com tratamento de erro
try {
    if (file_exists('get_filtered_data.php')) {
        require_once 'get_filtered_data.php';
    }
} catch (Exception $e) {
    error_log("Erro ao carregar get_filtered_data.php: " . $e->getMessage());
}

// Função para coletar dados de forma segura
function coletarDadosSeguro($funcao, $valor_padrao = 0) {
    try {
        if (function_exists($funcao)) {
            return call_user_func($funcao);
        }
    } catch (Exception $e) {
        error_log("Erro ao chamar função $funcao: " . $e->getMessage());
    }
    return $valor_padrao;
}

// Sempre calcular dados originais para a aba Resumos dos Portais
$ouvidoria_status_original = coletarDadosSeguro('getChamadosStatusOuvidoria', []);
$ouvidoria_sla_original = coletarDadosSeguro('getSLAMedioOuvidoria', 0);
$ouvidoria_sla_anterior_original = coletarDadosSeguro('getSLAMedioOuvidoriaMesAnterior', 0);
$ouvidoria_tipos_original = coletarDadosSeguro('getTiposManifestacaoOuvidoria', []);

$ead_status_original = coletarDadosSeguro('getChamadosStatusEAD', []);
$ead_sla_original = coletarDadosSeguro('getSLAMedioEAD', 0);
$ead_sla_anterior_original = coletarDadosSeguro('getSLAMedioEADMesAnterior', 0);
$ead_servicos_original = coletarDadosSeguro('getServicosSolicitadosEAD', []);

$processo_status_original = coletarDadosSeguro('getChamadosStatusProcessoSeletivo', []);
$processo_sla_original = coletarDadosSeguro('getSLAMedioProcessoSeletivo', 0);
$processo_sla_anterior_original = coletarDadosSeguro('getSLAMedioProcessoSeletivoMesAnterior', 0);
$processo_servicos_original = coletarDadosSeguro('getServicosSolicitadosProcessoSeletivo', []);

$secretaria_status_original = coletarDadosSeguro('getChamadosStatusSecretaria', []);
$secretaria_tempo_original = coletarDadosSeguro('getSLAMedioSecretaria', 0);
$secretaria_tempo_anterior_original = coletarDadosSeguro('getTempoMedioSecretariaMesAnterior', 0);
$secretaria_servicos_original = coletarDadosSeguro('getServicosSolicitadosSecretaria', []);

$financeiro_status_original = coletarDadosSeguro('getChamadosStatusFinanceiro', []);
$financeiro_sla_original = coletarDadosSeguro('getSLAMedioFinanceiro', 0);
$financeiro_sla_anterior_original = coletarDadosSeguro('getSLAMedioFinanceiroMesAnterior', 0);
$financeiro_servicos_original = coletarDadosSeguro('getServicosSolicitadosFinanceiro', []);

$exaluno_status_original = coletarDadosSeguro('getChamadosStatusExAluno', []);
$exaluno_sla_original = coletarDadosSeguro('getSLAMedioExAluno', 0);
$exaluno_sla_anterior_original = coletarDadosSeguro('getSLAMedioExAlunoMesAnterior', 0);
$exaluno_servicos_original = coletarDadosSeguro('getServicosSolicitadosExAluno', []);

// Usar dados originais por padrão
$ouvidoria_status = $ouvidoria_status_original;
$ouvidoria_sla = $ouvidoria_sla_original;
$ouvidoria_sla_anterior = $ouvidoria_sla_anterior_original;
$ouvidoria_tipos = $ouvidoria_tipos_original;

$ead_status = $ead_status_original;
$ead_sla = $ead_sla_original;
$ead_sla_anterior = $ead_sla_anterior_original;
$ead_servicos = $ead_servicos_original;

$processo_status = $processo_status_original;
$processo_sla = $processo_sla_original;
$processo_sla_anterior = $processo_sla_anterior_original;
$processo_servicos = $processo_servicos_original;

$secretaria_status = $secretaria_status_original;
$secretaria_tempo = $secretaria_tempo_original;
$secretaria_tempo_anterior = $secretaria_tempo_anterior_original;
$secretaria_servicos = $secretaria_servicos_original;

$financeiro_status = $financeiro_status_original;
$financeiro_sla = $financeiro_sla_original;
$financeiro_sla_anterior = $financeiro_sla_anterior_original;
$financeiro_servicos = $financeiro_servicos_original;

$exaluno_status = $exaluno_status_original;
$exaluno_sla = $exaluno_sla_original;
$exaluno_sla_anterior = $exaluno_sla_anterior_original;
$exaluno_servicos = $exaluno_servicos_original;

// Buscar notificações do usuário com tratamento de erro
$notificacoes = [];

// Função para classificar SLA
function getSLAClass($sla) {
    if ($sla > 10) return 'critico';
    if ($sla > 5) return 'atencao';
    return 'normal';
}

// Função para calcular diferença percentual
function calcularDiferencaPercentual($atual, $anterior) {
    if ($anterior == 0) return "N/A";
    $diferenca = (($atual - $anterior) / $anterior) * 100;
    if ($diferenca > 0) {
        return "+" . number_format($diferenca, 1) . "%";
    } else {
        return number_format($diferenca, 1) . "%";
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Executivo - Portais Drummond</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="js/chart.js"></script>
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo-header">
                <img src="https://drummond.com.br/wp-content/uploads//2020/06/logo-drumond.svg" alt="Logo Drummond" class="logo-img">
                <h3>Dashboard Executivo - Portais Drummond</h3>
            </div>
            <div class="user-info">
                <span>Bem-vindo, <?php echo $_SESSION['usuario_nome']; ?>!</span>
                <div class="notification-container">
                    <i class="fas fa-bell notification-icon" id="notification-icon">
                        <?php if (count($notificacoes) > 0): ?>
                            <span class="notification-badge"><?php echo count($notificacoes); ?></span>
                        <?php endif; ?>
                    </i>
                    <div class="notification-dropdown" id="notification-dropdown">
                        <h4>Notificações</h4>
                        <?php if (empty($notificacoes)): ?>
                            <p>Nenhuma notificação nova.</p>
                        <?php else: ?>
                            <ul>
                                <?php foreach ($notificacoes as $notif): ?>
                                    <li class="notification-item <?php echo $notif['tipo']; ?>" data-id="<?php echo $notif['id']; ?>">
                                        <p><?php echo htmlspecialchars($notif['mensagem']); ?></p>
                                        <small><?php echo date('d/m/Y H:i', strtotime($notif['data'])); ?></small>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
                <a href="logout.php" class="btn-logout">Sair</a>
            </div>
        </div>
    </header>

    <main>
        <!-- Barra de Filtros -->
        <div class="filters-bar">
            <div class="filter-group">
                <label for="periodFilter">Período:</label>
                <select id="periodFilter">
                    <option value="all" <?php echo ($periodo_filter === 'todos') ? 'selected' : ''; ?>>Todos os dados</option>
                    <option value="today" <?php echo ($periodo_filter === 'hoje') ? 'selected' : ''; ?>>Hoje</option>
                    <option value="7d" <?php echo ($periodo_filter === 'semana') ? 'selected' : ''; ?>>Últimos 7 dias</option>
                    <option value="30d" <?php echo ($periodo_filter === 'mes') ? 'selected' : ''; ?>>Último mês</option>
                    <option value="90d" <?php echo ($periodo_filter === 'trimestre') ? 'selected' : ''; ?>>Último trimestre</option>
                    <option value="365d" <?php echo ($periodo_filter === 'ano') ? 'selected' : ''; ?>>Último ano</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="portalFilter">Portal:</label>
                <select id="portalFilter">
                    <option value="all" <?php echo ($portal_filter === 'todos') ? 'selected' : ''; ?>>Todos os portais</option>
                    <option value="ouvidoria" <?php echo ($portal_filter === 'ouvidoria') ? 'selected' : ''; ?>>Ouvidoria</option>
                    <option value="ead" <?php echo ($portal_filter === 'ead') ? 'selected' : ''; ?>>EAD</option>
                    <option value="processo" <?php echo ($portal_filter === 'processo_seletivo') ? 'selected' : ''; ?>>Processo Seletivo</option>
                    <option value="secretaria" <?php echo ($portal_filter === 'secretaria') ? 'selected' : ''; ?>>Secretaria Acadêmica</option>
                    <option value="financeiro" <?php echo ($portal_filter === 'financeiro') ? 'selected' : ''; ?>>Financeiro</option>
                    <option value="exaluno" <?php echo ($portal_filter === 'exaluno') ? 'selected' : ''; ?>>Ex-Aluno</option>
                </select>
            </div>
            
            <button id="applyFilters" class="btn-primary">
                <i class="fas fa-filter"></i> Aplicar Filtros
            </button>
            
            <button id="resetFilters" class="btn-secondary">
                <i class="fas fa-undo"></i> Limpar Filtros
            </button>
        </div>

        <!-- Abas de navegação -->
        <div class="tabs">
            <button class="tab-button <?php echo (($_GET['tab'] ?? 'consolidado') === 'consolidado') ? 'active' : ''; ?>" data-tab="consolidado">Visão Consolidada</button>
            <button class="tab-button <?php echo (($_GET['tab'] ?? 'consolidado') === 'portais') ? 'active' : ''; ?>" data-tab="portais">Resumos dos Portais</button>
            <button class="tab-button <?php echo (($_GET['tab'] ?? 'consolidado') === 'chamados') ? 'active' : ''; ?>" data-tab="chamados">Lista de Chamados</button>
        </div>

        <!-- Conteúdo das abas -->
        <div id="consolidado" class="tab-content <?php echo (($_GET['tab'] ?? 'consolidado') === 'consolidado') ? 'active' : ''; ?>">
            <section id="visao-consolidada">
                <h2>Visão Consolidada</h2>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Total de Chamados por Portal</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="chartConsolidado"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>SLA Médio por Portal</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="chartSLA"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div id="portais" class="tab-content <?php echo (($_GET['tab'] ?? 'consolidado') === 'portais') ? 'active' : ''; ?>">
            <section id="ouvidoria" class="resumo-portal <?php echo getSLAClass($ouvidoria_sla); ?>">
                <h2>Portal da Ouvidoria</h2>
                <div class="row">
                    <div class="col-md-6">
                        <canvas id="chartOuvidoriaStatus"></canvas>
                    </div>
                    <div class="col-md-6">
                        <p><strong>SLA Médio:</strong> <?php echo $ouvidoria_sla; ?> dias</p>
                        <p class="comparativo-mes <?php echo ($ouvidoria_sla_anterior > 0 && $ouvidoria_sla < $ouvidoria_sla_anterior) ? 'positivo' : (($ouvidoria_sla_anterior > 0) ? 'negativo' : 'neutro'); ?>">
                            <?php if ($ouvidoria_sla_anterior > 0): ?>
                                <?php echo calcularDiferencaPercentual($ouvidoria_sla, $ouvidoria_sla_anterior); ?> vs mês anterior
                            <?php else: ?>
                                Sem dados do mês anterior
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                <div class="portal-actions">
                    <a href="dashboard_ouvidoria_dados_reais.php" class="btn btn-primary" target="_blank">Ver mais detalhes</a>
                </div>
            </section>

            <section id="ead" class="resumo-portal <?php echo getSLAClass($ead_sla); ?>">
                <h2>Portal do EAD</h2>
                <div class="row">
                    <div class="col-md-6">
                        <canvas id="chartEADStatus"></canvas>
                    </div>
                    <div class="col-md-6">
                        <p><strong>SLA Médio:</strong> <?php echo $ead_sla; ?> dias</p>
                        <p class="comparativo-mes <?php echo ($ead_sla_anterior > 0 && $ead_sla < $ead_sla_anterior) ? 'positivo' : (($ead_sla_anterior > 0) ? 'negativo' : 'neutro'); ?>">
                            <?php if ($ead_sla_anterior > 0): ?>
                                <?php echo calcularDiferencaPercentual($ead_sla, $ead_sla_anterior); ?> vs mês anterior
                            <?php else: ?>
                                Sem dados do mês anterior
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                <div class="portal-actions">
                    <a href="#" class="btn btn-primary">Ver mais detalhes</a>
                </div>
            </section>

            <section id="processo" class="resumo-portal <?php echo getSLAClass($processo_sla); ?>">
                <h2>Portal do Processo Seletivo</h2>
                <div class="row">
                    <div class="col-md-6">
                        <canvas id="chartProcessoStatus"></canvas>
                    </div>
                    <div class="col-md-6">
                        <p><strong>SLA Médio:</strong> <?php echo $processo_sla; ?> dias</p>
                        <p class="comparativo-mes <?php echo ($processo_sla_anterior > 0 && $processo_sla < $processo_sla_anterior) ? 'positivo' : (($processo_sla_anterior > 0) ? 'negativo' : 'neutro'); ?>">
                            <?php if ($processo_sla_anterior > 0): ?>
                                <?php echo calcularDiferencaPercentual($processo_sla, $processo_sla_anterior); ?> vs mês anterior
                            <?php else: ?>
                                Sem dados do mês anterior
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                <div class="portal-actions">
                    <a href="#" class="btn btn-primary">Ver mais detalhes</a>
                </div>
            </section>

            <section id="secretaria" class="resumo-portal <?php echo getSLAClass($secretaria_tempo); ?>">
                <h2>Portal da Secretaria Acadêmica</h2>
                <div class="row">
                    <div class="col-md-6">
                        <canvas id="chartSecretariaStatus"></canvas>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Tempo Médio:</strong> <?php echo $secretaria_tempo; ?> dias</p>
                        <p class="comparativo-mes <?php echo ($secretaria_tempo_anterior > 0 && $secretaria_tempo < $secretaria_tempo_anterior) ? 'positivo' : (($secretaria_tempo_anterior > 0) ? 'negativo' : 'neutro'); ?>">
                            <?php if ($secretaria_tempo_anterior > 0): ?>
                                <?php echo calcularDiferencaPercentual($secretaria_tempo, $secretaria_tempo_anterior); ?> vs mês anterior
                            <?php else: ?>
                                Sem dados do mês anterior
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                <div class="portal-actions">
                    <a href="#" class="btn btn-primary">Ver mais detalhes</a>
                </div>
            </section>

            <section id="financeiro" class="resumo-portal <?php echo getSLAClass($financeiro_sla); ?>">
                <h2>Portal Financeiro</h2>
                <div class="row">
                    <div class="col-md-6">
                        <canvas id="chartFinanceiroStatus"></canvas>
                    </div>
                    <div class="col-md-6">
                        <p><strong>SLA Médio:</strong> <?php echo $financeiro_sla; ?> dias</p>
                        <p class="comparativo-mes <?php echo ($financeiro_sla_anterior > 0 && $financeiro_sla < $financeiro_sla_anterior) ? 'positivo' : (($financeiro_sla_anterior > 0) ? 'negativo' : 'neutro'); ?>">
                            <?php if ($financeiro_sla_anterior > 0): ?>
                                <?php echo calcularDiferencaPercentual($financeiro_sla, $financeiro_sla_anterior); ?> vs mês anterior
                            <?php else: ?>
                                Sem dados do mês anterior
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                <div class="portal-actions">
                    <a href="#" class="btn btn-primary">Ver mais detalhes</a>
                </div>
            </section>

            <section id="exaluno" class="resumo-portal <?php echo getSLAClass($exaluno_sla); ?>">
                <h2>Portal Ex-Aluno</h2>
                <div class="row">
                    <div class="col-md-6">
                        <canvas id="chartExAlunoStatus"></canvas>
                    </div>
                    <div class="col-md-6">
                        <p><strong>SLA Médio:</strong> <?php echo $exaluno_sla; ?> dias</p>
                        <p class="comparativo-mes <?php echo ($exaluno_sla_anterior > 0 && $exaluno_sla < $exaluno_sla_anterior) ? 'positivo' : (($exaluno_sla_anterior > 0) ? 'negativo' : 'neutro'); ?>">
                            <?php if ($exaluno_sla_anterior > 0): ?>
                                <?php echo calcularDiferencaPercentual($exaluno_sla, $exaluno_sla_anterior); ?> vs mês anterior
                            <?php else: ?>
                                Sem dados do mês anterior
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                <div class="portal-actions">
                    <a href="#" class="btn btn-primary">Ver mais detalhes</a>
                </div>
            </section>
        </div>

        <div id="chamados" class="tab-content <?php echo (($_GET['tab'] ?? 'consolidado') === 'chamados') ? 'active' : ''; ?>">
            <section id="lista-chamados">
                <h2>Lista de Chamados</h2>
                <p>Em desenvolvimento - Lista detalhada de todos os chamados dos portais.</p>
            </section>
        </div>
    </main>

<script>
// Sistema de abas
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked button and corresponding content
            this.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
        });
    });

    // Aplicar filtros
    document.getElementById('applyFilters')?.addEventListener('click', function() {
        const periodo = document.getElementById('periodFilter').value;
        const portal = document.getElementById('portalFilter').value;
        
        const params = new URLSearchParams();
        if (periodo !== 'all') params.set('periodo', periodo);
        if (portal !== 'all') params.set('portal', portal);
        
        window.location.href = 'dashboard.php?' + params.toString();
    });

    // Limpar filtros
    document.getElementById('resetFilters')?.addEventListener('click', function() {
        window.location.href = 'dashboard.php';
    });

    // Notificações
    document.getElementById('notification-icon')?.addEventListener('click', function() {
        const dropdown = document.getElementById('notification-dropdown');
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    });

    // Criar gráficos se Chart.js estiver disponível
    if (typeof Chart !== 'undefined') {
        // Gráfico consolidado
        const ctxConsolidado = document.getElementById('chartConsolidado');
        if (ctxConsolidado) {
            new Chart(ctxConsolidado, {
                type: 'doughnut',
                data: {
                    labels: ['Ouvidoria', 'EAD', 'Processo Seletivo', 'Secretaria', 'Financeiro', 'Ex-Aluno'],
                    datasets: [{
                        data: [
                            <?php echo array_sum(array_column($ouvidoria_status, 'count')); ?>,
                            <?php echo array_sum(array_column($ead_status, 'count')); ?>,
                            <?php echo array_sum(array_column($processo_status, 'count')); ?>,
                            <?php echo array_sum(array_column($secretaria_status, 'count')); ?>,
                            <?php echo array_sum(array_column($financeiro_status, 'count')); ?>,
                            <?php echo array_sum(array_column($exaluno_status, 'count')); ?>
                        ],
                        backgroundColor: [
                            '#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6f42c1'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        // Gráfico SLA
        const ctxSLA = document.getElementById('chartSLA');
        if (ctxSLA) {
            new Chart(ctxSLA, {
                type: 'bar',
                data: {
                    labels: ['Ouvidoria', 'EAD', 'Processo Seletivo', 'Secretaria', 'Financeiro', 'Ex-Aluno'],
                    datasets: [{
                        label: 'SLA Médio (dias)',
                        data: [
                            <?php echo $ouvidoria_sla; ?>,
                            <?php echo $ead_sla; ?>,
                            <?php echo $processo_sla; ?>,
                            <?php echo $secretaria_tempo; ?>,
                            <?php echo $financeiro_sla; ?>,
                            <?php echo $exaluno_sla; ?>
                        ],
                        backgroundColor: [
                            '#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6f42c1'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    }
});
</script>

</body>
</html>