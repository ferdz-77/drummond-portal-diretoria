<?php
// Dashboard sem fallback - usa apenas dados reais
session_start();

// Verificar se usuário está logado
if (!isset($_SESSION['usuario_logado'])) {
    header('Location: login.php');
    exit;
}

// Obter parâmetros de filtro da URL
$periodo_filter = $_GET['periodo'] ?? 'todos';
$portal_filter = $_GET['portal'] ?? 'todos';

// Incluir arquivo de configuração
require_once 'includes/config_env.php';

// Incluir arquivos de dados (sem fallback)
$data_files_loaded = 0;
$data_files = [
    'data/data_ouvidoria.php',
    'data/data_ead.php',
    'data/data_processo_seletivo.php',
    'data/data_secretaria.php',
    'data/data_financeiro.php',
    'data/data_exaluno.php'
];

foreach ($data_files as $file) {
    if (file_exists($file)) {
        require_once $file;
        $data_files_loaded++;
    }
}

// Incluir arquivo de dados filtrados (se existir)
if (file_exists('get_filtered_data.php')) {
    require_once 'get_filtered_data.php';
}

// Carregar dados diretamente (sem fallback)
$ouvidoria_status = getChamadosStatusOuvidoria();
$ouvidoria_sla = getSLAMedioOuvidoria();
$ouvidoria_sla_anterior = getSLAMedioOuvidoriaMesAnterior();
$ouvidoria_tipos = getTiposManifestacaoOuvidoria();

$ead_status = getChamadosStatusEAD();
$ead_sla = getSLAMedioEAD();
$ead_sla_anterior = getSLAMedioEADMesAnterior();
$ead_servicos = getServicosSolicitadosEAD();

$processo_status = getChamadosStatusProcessoSeletivo();
$processo_sla = getSLAMedioProcessoSeletivo();
$processo_sla_anterior = getSLAMedioProcessoSeletivoMesAnterior();
$processo_servicos = getServicosSolicitadosProcessoSeletivo();

$secretaria_status = getChamadosStatusSecretaria();
$secretaria_tempo = getTempoMedioSecretaria();
$secretaria_tempo_anterior = getTempoMedioSecretariaMesAnterior();
$secretaria_servicos = getServicosSolicitadosSecretaria();

$financeiro_status = getChamadosStatusFinanceiro();
$financeiro_sla = getSLAMedioFinanceiro();
$financeiro_sla_anterior = getSLAMedioFinanceiroMesAnterior();
$financeiro_servicos = getServicosSolicitadosFinanceiro();

$exaluno_status = getChamadosStatusExAluno();
$exaluno_sla = getSLAMedioExAluno();
$exaluno_sla_anterior = getSLAMedioExAlunoMesAnterior();
$exaluno_servicos = getServicosSolicitadosExAluno();

// Buscar notificações
$conn = connectPortalDiretoria();
$usuario_id = $_SESSION['usuario_id'] ?? 'admin';
$sql = "SELECT * FROM notificacoes WHERE usuario_id = ? AND lida = FALSE ORDER BY data DESC LIMIT 10";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usuario_id);
$stmt->execute();
$notificacoes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$conn->close();

// Função para calcular diferença percentual mês a mês
function calcularDiferencaPercentual($atual, $anterior) {
    if ($anterior == 0) {
        return $atual > 0 ? "+∞%" : "0%";
    }

    $diferenca = (($atual - $anterior) / $anterior) * 100;
    $sinal = $diferenca >= 0 ? "+" : "";
    return $sinal . round($diferenca, 1) . "%";
}

// Função para determinar classe de alerta baseada no SLA
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
    <title>Dashboard Executivo - Portais Drummond (Dados Reais)</title>
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
                <small style="color: #666; font-size: 12px;">Dados Reais</small>
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
        <div id="consolidado" class="tab-content <?php echo (($_GET['tab'] ?? 'consolidado') === 'consolidado') ? 'active' : ''; ?>" aria-hidden="<?php echo (($_GET['tab'] ?? 'consolidado') === 'consolidado') ? 'false' : 'true'; ?>">
            <section id="visao-consolidada">
                <h2>Visão Consolidada</h2>
                <canvas id="chartConsolidado"></canvas>
            </section>
        </div>

        <div id="portais" class="tab-content <?php echo (($_GET['tab'] ?? 'consolidado') === 'portais') ? 'active' : ''; ?>" aria-hidden="<?php echo (($_GET['tab'] ?? 'consolidado') === 'portais') ? 'false' : 'true'; ?>">
            <section id="ouvidoria" class="resumo-portal <?php echo getSLAClass($ouvidoria_sla); ?>">
                <h2>Portal da Ouvidoria</h2>
                <canvas id="chartOuvidoriaStatus"></canvas>
                <p>SLA Médio: <?php echo $ouvidoria_sla; ?> dias</p>
                <p class="comparativo-mes <?php echo ($ouvidoria_sla_anterior > 0 && $ouvidoria_sla < $ouvidoria_sla_anterior) ? 'positivo' : (($ouvidoria_sla_anterior > 0) ? 'negativo' : 'neutro'); ?>">
                    <?php if ($ouvidoria_sla_anterior > 0): ?>
                        <?php echo calcularDiferencaPercentual($ouvidoria_sla, $ouvidoria_sla_anterior); ?> vs mês anterior
                    <?php else: ?>
                        Sem dados do mês anterior
                    <?php endif; ?>
                </p>
                <canvas id="chartOuvidoriaTipos"></canvas>
            </section>

            <section id="ead" class="resumo-portal <?php echo getSLAClass($ead_sla); ?>">
                <h2>Portal do EAD</h2>
                <canvas id="chartEADStatus"></canvas>
                <p>SLA Médio: <?php echo $ead_sla; ?> dias</p>
                <p class="comparativo-mes <?php echo ($ead_sla_anterior > 0 && $ead_sla < $ead_sla_anterior) ? 'positivo' : (($ead_sla_anterior > 0) ? 'negativo' : 'neutro'); ?>">
                    <?php if ($ead_sla_anterior > 0): ?>
                        <?php echo calcularDiferencaPercentual($ead_sla, $ead_sla_anterior); ?> vs mês anterior
                    <?php else: ?>
                        Sem dados do mês anterior
                    <?php endif; ?>
                </p>
                <canvas id="chartEADServicos"></canvas>
            </section>

            <section id="processo" class="resumo-portal <?php echo getSLAClass($processo_sla); ?>">
                <h2>Portal do Processo Seletivo</h2>
                <canvas id="chartProcessoStatus"></canvas>
                <p>SLA Médio: <?php echo $processo_sla; ?> dias</p>
                <p class="comparativo-mes <?php echo ($processo_sla_anterior > 0 && $processo_sla < $processo_sla_anterior) ? 'positivo' : (($processo_sla_anterior > 0) ? 'negativo' : 'neutro'); ?>">
                    <?php if ($processo_sla_anterior > 0): ?>
                        <?php echo calcularDiferencaPercentual($processo_sla, $processo_sla_anterior); ?> vs mês anterior
                    <?php else: ?>
                        Sem dados do mês anterior
                    <?php endif; ?>
                </p>
                <canvas id="chartProcessoServicos"></canvas>
            </section>

            <section id="secretaria" class="resumo-portal <?php echo getSLAClass($secretaria_tempo); ?>">
                <h2>Portal da Secretaria Acadêmica</h2>
                <canvas id="chartSecretariaStatus"></canvas>
                <p>Tempo Médio: <?php echo $secretaria_tempo; ?> dias</p>
                <p class="comparativo-mes <?php echo ($secretaria_tempo_anterior > 0 && $secretaria_tempo < $secretaria_tempo_anterior) ? 'positivo' : (($secretaria_tempo_anterior > 0) ? 'negativo' : 'neutro'); ?>">
                    <?php if ($secretaria_tempo_anterior > 0): ?>
                        <?php echo calcularDiferencaPercentual($secretaria_tempo, $secretaria_tempo_anterior); ?> vs mês anterior
                    <?php else: ?>
                        Sem dados do mês anterior
                    <?php endif; ?>
                </p>
                <canvas id="chartSecretariaServicos"></canvas>
            </section>

            <section id="financeiro" class="resumo-portal <?php echo getSLAClass($financeiro_sla); ?>">
                <h2>Portal Financeiro</h2>
                <canvas id="chartFinanceiroStatus"></canvas>
                <p>SLA Médio: <?php echo $financeiro_sla; ?> dias</p>
                <p class="comparativo-mes <?php echo ($financeiro_sla_anterior > 0 && $financeiro_sla < $financeiro_sla_anterior) ? 'positivo' : (($financeiro_sla_anterior > 0) ? 'negativo' : 'neutro'); ?>">
                    <?php if ($financeiro_sla_anterior > 0): ?>
                        <?php echo calcularDiferencaPercentual($financeiro_sla, $financeiro_sla_anterior); ?> vs mês anterior
                    <?php else: ?>
                        Sem dados do mês anterior
                    <?php endif; ?>
                </p>
                <canvas id="chartFinanceiroServicos"></canvas>
            </section>

            <section id="exaluno" class="resumo-portal <?php echo getSLAClass($exaluno_sla); ?>">
                <h2>Portal do Ex-Aluno</h2>
                <canvas id="chartExAlunoStatus"></canvas>
                <p>SLA Médio: <?php echo $exaluno_sla; ?> dias</p>
                <p class="comparativo-mes <?php echo ($exaluno_sla_anterior > 0 && $exaluno_sla < $exaluno_sla_anterior) ? 'positivo' : (($exaluno_sla_anterior > 0) ? 'negativo' : 'neutro'); ?>">
                    <?php if ($exaluno_sla_anterior > 0): ?>
                        <?php echo calcularDiferencaPercentual($exaluno_sla, $exaluno_sla_anterior); ?> vs mês anterior
                    <?php else: ?>
                        Sem dados do mês anterior
                    <?php endif; ?>
                </p>
                <canvas id="chartExAlunoServicos"></canvas>
            </section>
        </div>

        <div id="chamados" class="tab-content <?php echo (($_GET['tab'] ?? 'consolidado') === 'chamados') ? 'active' : ''; ?>" aria-hidden="<?php echo (($_GET['tab'] ?? 'consolidado') === 'chamados') ? 'false' : 'true'; ?>">
            <section id="lista-chamados">
                <h2>Lista de Todos os Chamados</h2>
                <p>Funcionalidade em desenvolvimento.</p>
            </section>
        </div>
    </main>

    <script>
        // Passar dados PHP para JS
        var ouvidoriaStatus = <?php echo json_encode($ouvidoria_status); ?>;
        var ouvidoriaSLA = <?php echo $ouvidoria_sla; ?>;
        var ouvidoriaTipos = <?php echo json_encode($ouvidoria_tipos); ?>;

        var eadStatus = <?php echo json_encode($ead_status); ?>;
        var eadSLA = <?php echo $ead_sla; ?>;
        var eadServicos = <?php echo json_encode($ead_servicos); ?>;

        var processoStatus = <?php echo json_encode($processo_status); ?>;
        var processoSLA = <?php echo $processo_sla; ?>;
        var processoServicos = <?php echo json_encode($processo_servicos); ?>;

        var secretariaStatus = <?php echo json_encode($secretaria_status); ?>;
        var secretariaTempo = <?php echo $secretaria_tempo; ?>;
        var secretariaServicos = <?php echo json_encode($secretaria_servicos); ?>;

        var financeiroStatus = <?php echo json_encode($financeiro_status); ?>;
        var financeiroSLA = <?php echo $financeiro_sla; ?>;
        var financeiroServicos = <?php echo json_encode($financeiro_servicos); ?>;

        var exalunoStatus = <?php echo json_encode($exaluno_status); ?>;
        var exalunoSLA = <?php echo $exaluno_sla; ?>;
        var exalunoServicos = <?php echo json_encode($exaluno_servicos); ?>;
    </script>
    <script src="js/dashboard.js?v=<?php echo time(); ?>"></script>

    <script>
        // Sistema de notificações simplificado
        document.addEventListener('DOMContentLoaded', function() {
            const notificationIcon = document.getElementById('notification-icon');
            const notificationDropdown = document.getElementById('notification-dropdown');

            if (notificationIcon && notificationDropdown) {
                notificationIcon.addEventListener('click', function() {
                    notificationDropdown.style.display = notificationDropdown.style.display === 'block' ? 'none' : 'block';
                });

                document.addEventListener('click', function(event) {
                    if (!notificationIcon.contains(event.target) && !notificationDropdown.contains(event.target)) {
                        notificationDropdown.style.display = 'none';
                    }
                });
            }
        });
    </script>
</body>
</html>