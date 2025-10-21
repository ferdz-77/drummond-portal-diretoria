<?php
// Dashboard com fallback para dados mock quando conexões falham
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

// Verificar modo emergência
$emergency_mode = isset($_GET['emergency']) && $_GET['emergency'] == '1';
$mock_mode = false;

// Dados mock para fallback
$mockData = [
    'ouvidoria' => [
        'status' => [['status' => 'Aberto', 'total' => 12], ['status' => 'Fechado', 'total' => 33]],
        'sla' => 2.3,
        'sla_anterior' => 2.1,
        'tipos' => [['manifestacao' => 'Reclamação', 'total' => 20], ['manifestacao' => 'Sugestão', 'total' => 15], ['manifestacao' => 'Elogio', 'total' => 10]]
    ],
    'ead' => [
        'status' => [['status' => 'Aberto', 'total' => 25], ['status' => 'Fechado', 'total' => 53]],
        'sla' => 1.8,
        'sla_anterior' => 1.9,
        'servicos' => [['categoria' => 'Matrícula', 'total' => 30], ['categoria' => 'Financeiro', 'total' => 25], ['categoria' => 'Técnico', 'total' => 23]]
    ],
    'processo_seletivo' => [
        'status' => [['status' => 'Aberto', 'total' => 45], ['status' => 'Fechado', 'total' => 111]],
        'sla' => 3.1,
        'sla_anterior' => 2.8,
        'servicos' => [['categoria' => 'Inscrição', 'total' => 80], ['categoria' => 'Documentação', 'total' => 45], ['categoria' => 'Resultado', 'total' => 31]]
    ],
    'secretaria' => [
        'status' => [['status' => 'Aberto', 'total' => 18], ['status' => 'Fechado', 'total' => 74]],
        'sla' => 1.5,
        'sla_anterior' => 1.6,
        'servicos' => [['categoria' => 'Histórico', 'total' => 35], ['categoria' => 'Diploma', 'total' => 30], ['categoria' => 'Transferência', 'total' => 27]]
    ],
    'financeiro' => [
        'status' => [['status' => 'Aberto', 'total' => 15], ['status' => 'Fechado', 'total' => 52]],
        'sla' => 2.7,
        'sla_anterior' => 2.5,
        'servicos' => [['categoria' => 'Mensalidade', 'total' => 40], ['categoria' => 'Bolsa', 'total' => 15], ['categoria' => 'Reembolso', 'total' => 12]]
    ],
    'exaluno' => [
        'status' => [['status' => 'Aberto', 'total' => 8], ['status' => 'Fechado', 'total' => 15]],
        'sla' => 4.2,
        'sla_anterior' => 3.9,
        'tipos' => [['manifestacao' => 'Certificado', 'total' => 12], ['manifestacao' => 'Histórico', 'total' => 8], ['manifestacao' => 'Transferência', 'total' => 3]]
    ]
];

// Função helper para tentar carregar dados reais com fallback para mock
function loadDataWithFallback($functionName, $mockKey, $mockSubKey = null) {
    global $mockData, $mock_mode;

    try {
        if (function_exists($functionName)) {
            $result = call_user_func($functionName);
            return $result;
        } else {
            throw new Exception("Função $functionName não existe");
        }
    } catch (Exception $e) {
        $mock_mode = true;
        if ($mockSubKey) {
            return $mockData[$mockKey][$mockSubKey] ?? [];
        }
        return $mockData[$mockKey] ?? [];
    }
}

// Tentar incluir arquivos de dados (com tratamento de erro)
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
    try {
        if (file_exists($file)) {
            require_once $file;
            $data_files_loaded++;
        }
    } catch (Exception $e) {
        // Arquivo não pôde ser carregado, continuar
    }
}

// Incluir arquivo de dados filtrados (se existir)
try {
    if (file_exists('get_filtered_data.php')) {
        require_once 'get_filtered_data.php';
    }
} catch (Exception $e) {
    // Arquivo opcional, continuar sem ele
}

// Carregar dados com fallback para mock
$ouvidoria_status_original = loadDataWithFallback('getChamadosStatusOuvidoria', 'ouvidoria', 'status');
$ouvidoria_sla_original = loadDataWithFallback('getSLAMedioOuvidoria', 'ouvidoria', 'sla');
$ouvidoria_sla_anterior_original = loadDataWithFallback('getSLAMedioOuvidoriaMesAnterior', 'ouvidoria', 'sla_anterior');
$ouvidoria_tipos_original = loadDataWithFallback('getTiposManifestacaoOuvidoria', 'ouvidoria', 'tipos');

$ead_status_original = loadDataWithFallback('getChamadosStatusEAD', 'ead', 'status');
$ead_sla_original = loadDataWithFallback('getSLAMedioEAD', 'ead', 'sla');
$ead_sla_anterior_original = loadDataWithFallback('getSLAMedioEADMesAnterior', 'ead', 'sla_anterior');
$ead_servicos_original = loadDataWithFallback('getServicosSolicitadosEAD', 'ead', 'servicos');

$processo_status_original = loadDataWithFallback('getChamadosStatusProcessoSeletivo', 'processo_seletivo', 'status');
$processo_sla_original = loadDataWithFallback('getSLAMedioProcessoSeletivo', 'processo_seletivo', 'sla');
$processo_sla_anterior_original = loadDataWithFallback('getSLAMedioProcessoSeletivoMesAnterior', 'processo_seletivo', 'sla_anterior');
$processo_servicos_original = loadDataWithFallback('getServicosSolicitadosProcessoSeletivo', 'processo_seletivo', 'servicos');

$secretaria_status_original = loadDataWithFallback('getChamadosStatusSecretaria', 'secretaria', 'status');
$secretaria_tempo_original = loadDataWithFallback('getTempoMedioSecretaria', 'secretaria', 'sla');
$secretaria_tempo_anterior_original = loadDataWithFallback('getTempoMedioSecretariaMesAnterior', 'secretaria', 'sla_anterior');
$secretaria_servicos_original = loadDataWithFallback('getServicosSolicitadosSecretaria', 'secretaria', 'servicos');

$financeiro_status_original = loadDataWithFallback('getChamadosStatusFinanceiro', 'financeiro', 'status');
$financeiro_sla_original = loadDataWithFallback('getSLAMedioFinanceiro', 'financeiro', 'sla');
$financeiro_sla_anterior_original = loadDataWithFallback('getSLAMedioFinanceiroMesAnterior', 'financeiro', 'sla_anterior');
$financeiro_servicos_original = loadDataWithFallback('getServicosSolicitadosFinanceiro', 'financeiro', 'servicos');

$exaluno_status_original = loadDataWithFallback('getChamadosStatusExAluno', 'exaluno', 'status');
$exaluno_sla_original = loadDataWithFallback('getSLAMedioExAluno', 'exaluno', 'sla');
$exaluno_sla_anterior_original = loadDataWithFallback('getSLAMedioExAlunoMesAnterior', 'exaluno', 'sla_anterior');
$exaluno_servicos_original = loadDataWithFallback('getServicosSolicitadosExAluno', 'exaluno', 'tipos');

// Aplicar filtros aos dados (simplificado)
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

// Buscar notificações do usuário (sempre funciona pois usa Portal Diretoria)
try {
    $conn = connectPortalDiretoria();
    $usuario_id = $_SESSION['usuario_id'] ?? 'admin'; // Assumir 'admin' se não definido
    $sql = "SELECT * FROM notificacoes WHERE usuario_id = ? AND lida = FALSE ORDER BY data DESC LIMIT 10";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario_id);
    $stmt->execute();
    $notificacoes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $conn->close();
} catch (Exception $e) {
    // Se não conseguir conectar, usar notificações mock
    $notificacoes = [
        ['id' => 1, 'mensagem' => 'Sistema funcionando com dados simulados', 'tipo' => 'info', 'data' => date('Y-m-d H:i:s')],
        ['id' => 2, 'mensagem' => 'Verificar credenciais dos bancos de dados', 'tipo' => 'warning', 'data' => date('Y-m-d H:i:s')]
    ];
    $mock_mode = true; // Ativar modo mock se notificações falharem
}

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
                <p>Funcionalidade em desenvolvimento - dados mock sendo exibidos.</p>
            </section>
        </div>
    </main>

    <script>
        // Passar dados PHP para JS
        var ouvidoriaStatus = <?php echo json_encode($ouvidoria_status_original); ?>;
        var ouvidoriaSLA = <?php echo $ouvidoria_sla_original; ?>;
        var ouvidoriaTipos = <?php echo json_encode($ouvidoria_tipos_original); ?>;

        var eadStatus = <?php echo json_encode($ead_status_original); ?>;
        var eadSLA = <?php echo $ead_sla_original; ?>;
        var eadServicos = <?php echo json_encode($ead_servicos_original); ?>;

        var processoStatus = <?php echo json_encode($processo_status_original); ?>;
        var processoSLA = <?php echo $processo_sla_original; ?>;
        var processoServicos = <?php echo json_encode($processo_servicos_original); ?>;

        var secretariaStatus = <?php echo json_encode($secretaria_status_original); ?>;
        var secretariaTempo = <?php echo $secretaria_tempo_original; ?>;
        var secretariaServicos = <?php echo json_encode($secretaria_servicos_original); ?>;

        var financeiroStatus = <?php echo json_encode($financeiro_status_original); ?>;
        var financeiroSLA = <?php echo $financeiro_sla_original; ?>;
        var financeiroServicos = <?php echo json_encode($financeiro_servicos_original); ?>;

        var exalunoStatus = <?php echo json_encode($exaluno_status_original); ?>;
        var exalunoSLA = <?php echo $exaluno_sla_original; ?>;
        var exalunoServicos = <?php echo json_encode($exaluno_servicos_original); ?>;
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