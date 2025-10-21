<?php
session_start();

// Verificar se usuário está logado
if (!isset($_SESSION['usuario_logado'])) {
    header('Location: login.php');
    exit;
}

// Verificar se foi passado o parâmetro do portal
if (!isset($_GET['portal']) || empty($_GET['portal'])) {
    header('Location: dashboard.php');
    exit;
}

$portal_atual = $_GET['portal'];

// Configurações específicas de cada portal
$portais_config = [
    'ouvidoria' => [
        'nome' => 'Portal da Ouvidoria',
        'icone' => 'fas fa-comments',
        'cor' => '#001830',
        'descricao' => 'Sistema de manifestações e reclamações dos alunos'
    ],
    'ead' => [
        'nome' => 'Portal EAD',
        'icone' => 'fas fa-graduation-cap',
        'cor' => '#ff5b00',
        'descricao' => 'Ensino a Distância e suporte acadêmico online'
    ],
    'processo' => [
        'nome' => 'Processo Seletivo',
        'icone' => 'fas fa-user-check',
        'cor' => '#28a745',
        'descricao' => 'Processos de seleção e admissão de alunos'
    ],
    'secretaria' => [
        'nome' => 'Secretaria Acadêmica',
        'icone' => 'fas fa-building',
        'cor' => '#6f42c1',
        'descricao' => 'Serviços administrativos e acadêmicos'
    ],
    'financeiro' => [
        'nome' => 'Portal Financeiro',
        'icone' => 'fas fa-dollar-sign',
        'cor' => '#dc3545',
        'descricao' => 'Gestão financeira e pagamentos'
    ],
    'exaluno' => [
        'nome' => 'Portal Ex-Aluno',
        'icone' => 'fas fa-user-graduate',
        'cor' => '#17a2b8',
        'descricao' => 'Serviços para ex-alunos e egressos'
    ]
];

// Verificar se o portal existe
if (!isset($portais_config[$portal_atual])) {
    header('Location: dashboard.php');
    exit;
}

$portal_info = $portais_config[$portal_atual];

// Incluir arquivo de configuração
require_once 'includes/config_env_simplificado.php';

// Incluir dados específicos do portal
switch ($portal_atual) {
    case 'ouvidoria':
        require_once 'data/data_ouvidoria.php';
        $status_data = getChamadosStatusOuvidoria();
        $sla_atual = getSLAMedioOuvidoria();
        $sla_anterior = getSLAMedioOuvidoriaMesAnterior();
        $tipos_data = getTiposManifestacaoOuvidoria();
        $titulo_sla = 'SLA Médio';
        break;

    case 'ead':
        require_once 'data/data_ead.php';
        $status_data = getChamadosStatusEAD();
        $sla_atual = getSLAMedioEAD();
        $sla_anterior = getSLAMedioEADMesAnterior();
        $tipos_data = getServicosSolicitadosEAD();
        $titulo_sla = 'SLA Médio';
        break;

    case 'processo':
        require_once 'data/data_processo_seletivo.php';
        $status_data = getChamadosStatusProcessoSeletivo();
        $sla_atual = getSLAMedioProcessoSeletivo();
        $sla_anterior = getSLAMedioProcessoSeletivoMesAnterior();
        $tipos_data = getServicosSolicitadosProcessoSeletivo();
        $titulo_sla = 'SLA Médio';
        break;

    case 'secretaria':
        require_once 'data/data_secretaria.php';
        $status_data = getSolicitacoesStatusSecretaria();
        $sla_atual = getTempoMedioSecretaria();
        $sla_anterior = getTempoMedioSecretariaMesAnterior();
        $tipos_data = getServicosSolicitadosSecretaria();
        $titulo_sla = 'Tempo Médio';
        break;

    case 'financeiro':
        require_once 'data/data_financeiro.php';
        $status_data = getChamadosStatusFinanceiro();
        $sla_atual = getSLAMedioFinanceiro();
        $sla_anterior = getSLAMedioFinanceiroMesAnterior();
        $tipos_data = getServicosSolicitadosFinanceiro();
        $titulo_sla = 'SLA Médio';
        break;

    case 'exaluno':
        require_once 'data/data_exaluno.php';
        $status_data = getChamadosStatusExAluno();
        $sla_atual = getSLAMedioExAluno();
        $sla_anterior = getSLAMedioExAlunoMesAnterior();
        $tipos_data = getServicosSolicitadosExAluno();
        $titulo_sla = 'SLA Médio';
        break;
}

// Função para calcular diferença percentual
function calcularDiferencaPercentual($atual, $anterior) {
    if ($anterior == 0) {
        return $atual > 0 ? "+∞%" : "0%";
    }

    $diferenca = (($atual - $anterior) / $anterior) * 100;
    $sinal = $diferenca >= 0 ? "+" : "";
    return $sinal . round($diferenca, 1) . "%";
}

// Função para determinar classe de alerta
function getSLAClass($sla) {
    if ($sla > 10) {
        return 'alert-critical';
    } elseif ($sla > 5) {
        return 'alert-warning';
    } else {
        return 'alert-good';
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $portal_info['nome']; ?> - Detalhes | Dashboard Drummond</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/detalhes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --portal-color: <?php echo $portal_info['cor']; ?>;
        }
    </style>
</head>
<body>
    <div class="portal-header">
        <a href="dashboard.php" class="back-button">
            <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
        </a>
        <h1>
            <i class="<?php echo $portal_info['icone']; ?> portal-icon"></i>
            <?php echo $portal_info['nome']; ?>
        </h1>
        <p><?php echo $portal_info['descricao']; ?></p>
    </div>

    <div class="container">
        <!-- Métrica Principal -->
        <div class="metric-card">
            <h3><?php echo $titulo_sla; ?> Atual</h3>
            <div class="metric-value"><?php echo number_format($sla_atual, 1); ?> dias</div>
            <?php if ($sla_anterior > 0): ?>
                <div class="metric-comparison <?php echo ($sla_atual < $sla_anterior) ? 'positivo' : 'negativo'; ?>">
                    <?php echo calcularDiferencaPercentual($sla_atual, $sla_anterior); ?> vs mês anterior
                </div>
            <?php endif; ?>
        </div>

        <!-- Gráficos -->
        <div class="charts-grid">
            <div class="chart-container">
                <h3>Status dos Chamados</h3>
                <canvas id="chartStatus"></canvas>
            </div>

            <div class="chart-container">
                <h3>Tipos de Solicitações</h3>
                <canvas id="chartTipos"></canvas>
            </div>
        </div>

        <!-- Visão Geral dos Status -->
        <div class="metric-card">
            <h3>Visão Geral dos Status</h3>
            <div class="status-overview" id="statusOverview">
                <!-- Será preenchido via JavaScript -->
            </div>
        </div>
    </div>

    <script>
        // Dados PHP para JavaScript
        const statusData = <?php echo json_encode($status_data); ?>;
        const tiposData = <?php echo json_encode($tipos_data); ?>;
        const portalColor = '<?php echo $portal_info['cor']; ?>';

        // Gráfico de Status
        const ctxStatus = document.getElementById('chartStatus');
        if (ctxStatus && statusData.length > 0) {
            new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: statusData.map(item => item.status || item[Object.keys(item)[0]]),
                    datasets: [{
                        data: statusData.map(item => item.count),
                        backgroundColor: [
                            portalColor,
                            '#ff5b00',
                            '#28a745',
                            '#dc3545',
                            '#6f42c1',
                            '#17a2b8'
                        ],
                        borderWidth: 2
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

        // Gráfico de Tipos
        const ctxTipos = document.getElementById('chartTipos');
        if (ctxTipos && tiposData.length > 0) {
            new Chart(ctxTipos, {
                type: 'bar',
                data: {
                    labels: tiposData.map(item => item[Object.keys(item)[0]] || 'N/A'),
                    datasets: [{
                        label: 'Quantidade',
                        data: tiposData.map(item => item.count),
                        backgroundColor: portalColor,
                        borderColor: portalColor,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        // Preencher visão geral dos status
        const statusOverview = document.getElementById('statusOverview');
        if (statusOverview && statusData.length > 0) {
            statusData.forEach(item => {
                const statusName = item.status || item[Object.keys(item)[0]];
                const count = item.count;

                const statusItem = document.createElement('div');
                statusItem.className = 'status-item';
                statusItem.innerHTML = `
                    <h4>${statusName}</h4>
                    <div class="count">${count}</div>
                `;

                statusOverview.appendChild(statusItem);
            });
        }
    </script>
</body>
</html>