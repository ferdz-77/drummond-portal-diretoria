<?php
session_start();

// Configurar tratamento de erro para produção
if ($_ENV['APP_ENV'] ?? getenv('APP_ENV') === 'production') {
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

// Incluir arquivo de configuração com tratamento de erro
try {
    require_once 'includes/config_env.php';
} catch (Exception $e) {
    if ($environment === 'production') {
        header('Location: emergency_dashboard_production.php');
        exit;
    } else {
        die("Erro de configuração: " . $e->getMessage());
    }
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
            if ($environment === 'production') {
                // Em produção, redirecionar para modo emergência
                header('Location: emergency_dashboard_production.php');
                exit;
            }
        }
    } catch (Exception $e) {
        error_log("Erro ao carregar $arquivo: " . $e->getMessage());
        if ($environment === 'production') {
            header('Location: emergency_dashboard_production.php');
            exit;
        } else {
            die("Erro ao carregar $arquivo: " . $e->getMessage());
        }
    }
}

// Incluir arquivo de dados filtrados com tratamento de erro
try {
    if (file_exists('get_filtered_data.php')) {
        require_once 'get_filtered_data.php';
    }
} catch (Exception $e) {
    error_log("Erro ao carregar get_filtered_data.php: " . $e->getMessage());
    if ($environment === 'production') {
        header('Location: emergency_dashboard_production.php');
        exit;
    }
}

// Sempre calcular dados originais para a aba Resumos dos Portais
$ouvidoria_status_original = getChamadosStatusOuvidoria();
$ouvidoria_sla_original = getSLAMedioOuvidoria();
$ouvidoria_sla_anterior_original = getSLAMedioOuvidoriaMesAnterior();
$ouvidoria_tipos_original = getTiposManifestacaoOuvidoria();

$ead_status_original = getChamadosStatusEAD();
$ead_sla_original = getSLAMedioEAD();
$ead_sla_anterior_original = getSLAMedioEADMesAnterior();
$ead_servicos_original = getServicosSolicitadosEAD();

$processo_status_original = getChamadosStatusProcessoSeletivo();
$processo_sla_original = getSLAMedioProcessoSeletivo();
$processo_sla_anterior_original = getSLAMedioProcessoSeletivoMesAnterior();
$processo_servicos_original = getServicosSolicitadosProcessoSeletivo();

$secretaria_status_original = getChamadosStatusSecretaria();
$secretaria_tempo_original = getSLAMedioSecretaria();
$secretaria_tempo_anterior_original = getTempoMedioSecretariaMesAnterior();
$secretaria_servicos_original = getServicosSolicitadosSecretaria();

$financeiro_status_original = getChamadosStatusFinanceiro();
$financeiro_sla_original = getSLAMedioFinanceiro();
$financeiro_sla_anterior_original = getSLAMedioFinanceiroMesAnterior();
$financeiro_servicos_original = getServicosSolicitadosFinanceiro();

$exaluno_status_original = getChamadosStatusExAluno();
$exaluno_sla_original = getSLAMedioExAluno();
$exaluno_sla_anterior_original = getSLAMedioExAlunoMesAnterior();
$exaluno_servicos_original = getServicosSolicitadosExAluno();

// Buscar dados para cada portal
if ($portal_filter === 'todos') {
    if ($periodo_filter === 'todos') {
        // Usar dados originais
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
    } else {
        // Aplicar filtro de período a todos os portais
        $ouvidoria_filtered = getFilteredPortalData('ouvidoria', $periodo_filter);
        $ouvidoria_status = $ouvidoria_filtered['status'] ?? [];
        $ouvidoria_sla = $ouvidoria_filtered['sla'] ?? 0;
        $ouvidoria_sla_anterior = getSLAMedioOuvidoriaMesAnterior();
        $ouvidoria_tipos = $ouvidoria_filtered['tipos'] ?? [];

        $ead_filtered = getFilteredPortalData('ead', $periodo_filter);
        $ead_status = $ead_filtered['status'] ?? [];
        $ead_sla = $ead_filtered['sla'] ?? 0;
        $ead_sla_anterior = getSLAMedioEADMesAnterior();
        $ead_servicos = $ead_filtered['servicos'] ?? [];

        $processo_filtered = getFilteredPortalData('processo_seletivo', $periodo_filter);
        $processo_status = $processo_filtered['status'] ?? [];
        $processo_sla = $processo_filtered['sla'] ?? 0;
        $processo_sla_anterior = getSLAMedioProcessoSeletivoMesAnterior();
        $processo_servicos = $processo_filtered['servicos'] ?? [];

        $secretaria_filtered = getFilteredPortalData('secretaria', $periodo_filter);
        $secretaria_status = $secretaria_filtered['status'] ?? [];
        $secretaria_tempo = $secretaria_filtered['tempo'] ?? 0;
        $secretaria_tempo_anterior = getTempoMedioSecretariaMesAnterior();
        $secretaria_servicos = $secretaria_filtered['servicos'] ?? [];

        $financeiro_filtered = getFilteredPortalData('financeiro', $periodo_filter);
        $financeiro_status = $financeiro_filtered['status'] ?? [];
        $financeiro_sla = $financeiro_filtered['sla'] ?? 0;
        $financeiro_sla_anterior = getSLAMedioFinanceiroMesAnterior();
        $financeiro_servicos = $financeiro_filtered['servicos'] ?? [];

        $exaluno_filtered = getFilteredPortalData('exaluno', $periodo_filter);
        $exaluno_status = $exaluno_filtered['status'] ?? [];
        $exaluno_sla = $exaluno_filtered['sla'] ?? 0;
        $exaluno_sla_anterior = getSLAMedioExAlunoMesAnterior();
        $exaluno_servicos = $exaluno_filtered['servicos'] ?? [];
    }
} else {
    // Dados filtrados para portal específico
    $filtered_data = getFilteredPortalData($portal_filter, $periodo_filter);

    // Inicializar arrays vazios
    $ouvidoria_status = [];
    $ouvidoria_sla = 0;
    $ouvidoria_sla_anterior = 0;
    $ouvidoria_tipos = [];

    $ead_status = [];
    $ead_sla = 0;
    $ead_sla_anterior = 0;
    $ead_servicos = [];

    $processo_status = [];
    $processo_sla = 0;
    $processo_sla_anterior = 0;
    $processo_servicos = [];

    $secretaria_status = [];
    $secretaria_tempo = 0;
    $secretaria_tempo_anterior = 0;
    $secretaria_servicos = [];

    $financeiro_status = [];
    $financeiro_sla = 0;
    $financeiro_sla_anterior = 0;
    $financeiro_servicos = [];

    $exaluno_status = [];
    $exaluno_sla = 0;
    $exaluno_sla_anterior = 0;
    $exaluno_servicos = [];

    // Preencher dados do portal filtrado
    switch ($portal_filter) {
        case 'ouvidoria':
            $ouvidoria_status = $filtered_data['status'] ?? [];
            $ouvidoria_sla = $filtered_data['sla'] ?? 0;
            $ouvidoria_tipos = $filtered_data['tipos'] ?? [];
            break;
        case 'ead':
            $ead_status = $filtered_data['status'] ?? [];
            $ead_sla = $filtered_data['sla'] ?? 0;
            $ead_servicos = $filtered_data['servicos'] ?? [];
            break;
        case 'processo_seletivo':
            $processo_status = $filtered_data['status'] ?? [];
            $processo_sla = $filtered_data['sla'] ?? 0;
            $processo_servicos = $filtered_data['servicos'] ?? [];
            break;
        case 'secretaria':
            $secretaria_status = $filtered_data['status'] ?? [];
            $secretaria_tempo = $filtered_data['tempo'] ?? 0;
            $secretaria_servicos = $filtered_data['servicos'] ?? [];
            break;
        case 'financeiro':
            $financeiro_status = $filtered_data['status'] ?? [];
            $financeiro_sla = $filtered_data['sla'] ?? 0;
            $financeiro_servicos = $filtered_data['servicos'] ?? [];
            break;
        case 'exaluno':
            $exaluno_status = $filtered_data['status'] ?? [];
            $exaluno_sla = $filtered_data['sla'] ?? 0;
            $exaluno_servicos = $filtered_data['servicos'] ?? [];
            break;
    }
}

// Buscar notificações do usuário com tratamento de erro
$notificacoes = [];
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
    error_log("Erro ao buscar notificações: " . $e->getMessage());
    // Continuar sem notificações em caso de erro
    $notificacoes = [];
}

// Gerar notificações reais baseadas em dados
$limite_critico = 10; // Dias - nível crítico
$limite_atencao = 5;  // Dias - nível de atenção

// Ouvidoria
if ($ouvidoria_sla > $limite_critico) {
    $mensagem = "🚨 SLA do Portal Ouvidoria CRÍTICO: {$ouvidoria_sla} dias (limite: {$limite_critico} dias).";
    inserirNotificacao($conn, $usuario_id, $mensagem, 'critical');
} elseif ($ouvidoria_sla > $limite_atencao) {
    $mensagem = "⚠️ SLA do Portal Ouvidoria em ATENÇÃO: {$ouvidoria_sla} dias (limite: {$limite_critico} dias).";
    inserirNotificacao($conn, $usuario_id, $mensagem, 'warning');
}

// EAD
if ($ead_sla > $limite_critico) {
    $mensagem = "🚨 SLA do Portal EAD CRÍTICO: {$ead_sla} dias (limite: {$limite_critico} dias).";
    inserirNotificacao($conn, $usuario_id, $mensagem, 'critical');
} elseif ($ead_sla > $limite_atencao) {
    $mensagem = "⚠️ SLA do Portal EAD em ATENÇÃO: {$ead_sla} dias (limite: {$limite_critico} dias).";
    inserirNotificacao($conn, $usuario_id, $mensagem, 'warning');
}

// Processo Seletivo
if ($processo_sla > $limite_critico) {
    $mensagem = "🚨 SLA do Portal Processo Seletivo CRÍTICO: {$processo_sla} dias (limite: {$limite_critico} dias).";
    inserirNotificacao($conn, $usuario_id, $mensagem, 'critical');
} elseif ($processo_sla > $limite_atencao) {
    $mensagem = "⚠️ SLA do Portal Processo Seletivo em ATENÇÃO: {$processo_sla} dias (limite: {$limite_critico} dias).";
    inserirNotificacao($conn, $usuario_id, $mensagem, 'warning');
}

// Secretaria Acadêmica
if ($secretaria_tempo > $limite_critico) {
    $mensagem = "🚨 Tempo do Portal Secretaria CRÍTICO: {$secretaria_tempo} dias (limite: {$limite_critico} dias).";
    inserirNotificacao($conn, $usuario_id, $mensagem, 'critical');
} elseif ($secretaria_tempo > $limite_atencao) {
    $mensagem = "⚠️ Tempo do Portal Secretaria em ATENÇÃO: {$secretaria_tempo} dias (limite: {$limite_critico} dias).";
    inserirNotificacao($conn, $usuario_id, $mensagem, 'warning');
}

// Financeiro
if ($financeiro_sla > $limite_critico) {
    $mensagem = "🚨 SLA do Portal Financeiro CRÍTICO: {$financeiro_sla} dias (limite: {$limite_critico} dias).";
    inserirNotificacao($conn, $usuario_id, $mensagem, 'critical');
} elseif ($financeiro_sla > $limite_atencao) {
    $mensagem = "⚠️ SLA do Portal Financeiro em ATENÇÃO: {$financeiro_sla} dias (limite: {$limite_critico} dias).";
    inserirNotificacao($conn, $usuario_id, $mensagem, 'warning');
}

// Ex-Aluno
if ($exaluno_sla > $limite_critico) {
    $mensagem = "🚨 SLA do Portal Ex-Aluno CRÍTICO: {$exaluno_sla} dias (limite: {$limite_critico} dias).";
    inserirNotificacao($conn, $usuario_id, $mensagem, 'critical');
} elseif ($exaluno_sla > $limite_atencao) {
    $mensagem = "⚠️ SLA do Portal Ex-Aluno em ATENÇÃO: {$exaluno_sla} dias (limite: {$limite_critico} dias).";
    inserirNotificacao($conn, $usuario_id, $mensagem, 'warning');
}

$conn->close();

function inserirNotificacao($conn, $usuario_id, $mensagem, $tipo) {
    // Verificar se notificação já existe hoje
    $sql_check = "SELECT id, email_enviado FROM notificacoes WHERE usuario_id = ? AND mensagem = ? AND DATE(data) = CURDATE()";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ss", $usuario_id, $mensagem);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows == 0) {
        // Inserir nova notificação
        $sql_insert = "INSERT INTO notificacoes (usuario_id, mensagem, tipo, email_enviado) VALUES (?, ?, ?, FALSE)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("sss", $usuario_id, $mensagem, $tipo);
        $stmt_insert->execute();
        $notificacao_id = $conn->insert_id;
        $email_ja_enviado = false;
    } else {
        // Notificação já existe, verificar se e-mail já foi enviado
        $row = $result_check->fetch_assoc();
        $notificacao_id = $row['id'];
        $email_ja_enviado = $row['email_enviado'];
    }

    // Enviar e-mail apenas se for crítica e não foi enviado ainda
    if ($tipo === 'critical' && !$email_ja_enviado) {
        require_once 'send_email.php';
        $email_usuario = 'usuario@exemplo.com'; // Substitua pelo e-mail real do usuário
        $email_enviado = enviarEmail($email_usuario, 'Notificação Crítica - Dashboard Drummond', "<p>$mensagem</p>");

        // Atualizar status do envio de e-mail
        if ($email_enviado) {
            $sql_update = "UPDATE notificacoes SET email_enviado = TRUE WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("i", $notificacao_id);
            $stmt_update->execute();
        }
    }
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
            <!-- <h2>Resumos dos Portais</h2> -->
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
                <div class="portal-actions">
                    <a href="#" class="btn-detalhes" onclick="abrirDetalhesPortal('ouvidoria')">Ver mais detalhes</a>
                </div>
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
                <div class="portal-actions">
                    <a href="#" class="btn-detalhes" onclick="abrirDetalhesPortal('ead')">Ver mais detalhes</a>
                </div>
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
                <div class="portal-actions">
                    <a href="#" class="btn-detalhes" onclick="abrirDetalhesPortal('processo')">Ver mais detalhes</a>
                </div>
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
                <div class="portal-actions">
                    <a href="#" class="btn-detalhes" onclick="abrirDetalhesPortal('secretaria')">Ver mais detalhes</a>
                </div>
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
                <div class="portal-actions">
                    <a href="#" class="btn-detalhes" onclick="abrirDetalhesPortal('financeiro')">Ver mais detalhes</a>
                </div>
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
                <div class="portal-actions">
                    <a href="#" class="btn-detalhes" onclick="abrirDetalhesPortal('exaluno')">Ver mais detalhes</a>
                </div>
            </section>
        </div>

        <div id="chamados" class="tab-content <?php echo (($_GET['tab'] ?? 'consolidado') === 'chamados') ? 'active' : ''; ?>" aria-hidden="<?php echo (($_GET['tab'] ?? 'consolidado') === 'chamados') ? 'false' : 'true'; ?>">
            <section id="lista-chamados">
                <h2>Lista de Todos os Chamados</h2>
                
                <!-- Filtros para a lista -->
                <div class="filters-bar chamados-filters mb-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="portalFilterChamados" class="form-label fw-bold">
                                <i class="fas fa-building me-1"></i>Portal:
                            </label>
                            <select id="portalFilterChamados" class="form-select">
                                <option value="todos" selected>Todos os portais</option>
                                <option value="ouvidoria">Ouvidoria</option>
                                <option value="ead">EAD</option>
                                <option value="processo_seletivo">Processo Seletivo</option>
                                <option value="secretaria">Secretaria Acadêmica</option>
                                <option value="financeiro">Financeiro</option>
                                <option value="exaluno">Ex-Aluno</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="statusFilterChamados" class="form-label fw-bold">
                                <i class="fas fa-info-circle me-1"></i>Status:
                            </label>
                            <select id="statusFilterChamados" class="form-select">
                                <option value="todos" selected>Todos os status</option>
                                <option value="Aberto">Aberto</option>
                                <option value="Em Andamento">Em Andamento</option>
                                <option value="Fechado">Fechado</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="d-flex gap-2">
                                <button id="applyFiltersChamados" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i> Filtrar
                                </button>
                                
                                <button id="resetFiltersChamados" class="btn btn-outline-secondary">
                                    <i class="fas fa-undo me-1"></i> Limpar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabela de chamados -->
                <div class="chamados-table-container">
                    <div class="table-responsive">
                        <table id="chamadosTable" class="table table-striped table-hover table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th><i class="fas fa-building"></i> Portal</th>
                                    <th><i class="fas fa-info-circle"></i> Status</th>
                                    <th><i class="fas fa-calendar-plus"></i> Data Abertura</th>
                                    <th><i class="fas fa-calendar-check"></i> Data Fechamento</th>
                                    <th><i class="fas fa-cogs"></i> Serviço</th>
                                    <th><i class="fas fa-eye"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody id="chamadosTableBody">
                                <!-- Dados serão carregados via AJAX -->
                            </tbody>
                        </table>
                    </div>
                    
                    <div id="loadingIndicator" class="loading-indicator d-none">
                        <div class="d-flex justify-content-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Carregando...</span>
                            </div>
                            <span class="ms-2">Carregando chamados...</span>
                        </div>
                    </div>
                    
                    <div id="noDataMessage" class="no-data d-none">
                        <div class="alert alert-info text-center" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            Nenhum chamado encontrado para os filtros selecionados.
                        </div>
                    </div>
                </div>

                <!-- Modal de Detalhes do Chamado -->
                <div class="modal fade" id="chamadoModal" tabindex="-1" aria-labelledby="chamadoModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="chamadoModalLabel">
                                    <i class="fas fa-ticket-alt me-2"></i>Detalhes do Chamado
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                            </div>
                            <div class="modal-body">
                                <div id="chamadoModalContent">
                                    <div class="text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Carregando...</span>
                                        </div>
                                        <p class="mt-2">Carregando detalhes do chamado...</p>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-1"></i>Fechar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paginação -->
                <div class="pagination d-none" id="paginationContainer">
                    <nav aria-label="Navegação de páginas">
                        <ul class="pagination justify-content-center">
                            <li class="page-item">
                                <button id="prevPage" class="page-link btn-secondary">
                                    <i class="fas fa-chevron-left me-1"></i>Anterior
                                </button>
                            </li>
                            <li class="page-item disabled">
                                <span id="pageInfo" class="page-link">Página 1 de 1</span>
                            </li>
                            <li class="page-item">
                                <button id="nextPage" class="page-link btn-secondary">
                                    Próxima<i class="fas fa-chevron-right ms-1"></i>
                                </button>
                            </li>
                        </ul>
                    </nav>
                </div>
            </section>
        </div>
    </main>

    <script>
        // Passar dados PHP para JS (sempre usar dados originais para Visão Consolidada)
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
        var financeiroServicos = <?php echo json_encode($financeiro_servicos); ?>;

        var exalunoStatus = <?php echo json_encode($exaluno_status_original); ?>;
        var exalunoSLA = <?php echo $exaluno_sla_original; ?>;
        var exalunoServicos = <?php echo json_encode($exaluno_servicos_original); ?>;
    </script>
    <script src="js/dashboard.js?v=<?php echo time(); ?>"></script>

    <script>
        // Função para alternar entre abas sem depender de event.target
        function showTab(tabName) {
            // Esconder todas as abas
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => {
                content.classList.remove('active');
                content.setAttribute('aria-hidden', 'true');
            });

            // Remover classe active de todos os botões
            const tabButtons = document.querySelectorAll('.tab-button');
            tabButtons.forEach(button => button.classList.remove('active'));

            // Mostrar aba selecionada
            const selectedTab = document.getElementById(tabName);
            if (selectedTab) {
                selectedTab.classList.add('active');
                selectedTab.setAttribute('aria-hidden', 'false');
            }
        }

        // Inicializar comportamento das abas no carregamento do DOM
        document.addEventListener('DOMContentLoaded', function() {
            // Vincular clique para todos os botões com data-tab
            const tabButtons = document.querySelectorAll('.tab-button[data-tab]');
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const target = this.getAttribute('data-tab');
                    showTab(target);
                    this.classList.add('active');
                });
            });

            // Garantir que somente a aba com classe active esteja visível (controle por classes)
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => {
                content.classList.remove('active');
                content.setAttribute('aria-hidden', 'true');
            });
            // Restaurar a aba ativa conforme o botão marcado
            const initialActive = document.querySelector('.tab-button.active');
            if (initialActive) {
                const target = initialActive.getAttribute('data-tab');
                const selected = document.getElementById(target);
                if (selected) {
                    selected.classList.add('active');
                    selected.setAttribute('aria-hidden', 'false');
                }
            }
        });

        // Função para abrir detalhes de cada portal
        function abrirDetalhesPortal(portal) {
            // Redirecionar para página de detalhes interna
            window.location.href = 'detalhes.php?portal=' + portal;
        }

        // Sistema de notificações
        document.addEventListener('DOMContentLoaded', function() {
            const notificationIcon = document.getElementById('notification-icon');
            const notificationDropdown = document.getElementById('notification-dropdown');

            if (notificationIcon && notificationDropdown) {
                notificationIcon.addEventListener('click', function() {
                    notificationDropdown.style.display = notificationDropdown.style.display === 'block' ? 'none' : 'block';
                });

                // Fechar dropdown ao clicar fora
                document.addEventListener('click', function(event) {
                    if (!notificationIcon.contains(event.target) && !notificationDropdown.contains(event.target)) {
                        notificationDropdown.style.display = 'none';
                    }
                });
            }

            // Funcionalidade para marcar notificações como lidas
            const notificationItems = document.querySelectorAll('.notification-item');

            notificationItems.forEach(item => {
                item.addEventListener('click', function() {
                    const notificationId = this.getAttribute('data-id');
                    const notificationElement = this;

                    // Enviar requisição AJAX para marcar como lida
                    const formData = new FormData();
                    formData.append('notification_id', notificationId);

                    fetch('mark_notification_read.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Marcar visualmente como lida
                            notificationElement.classList.add('read');
                            notificationElement.style.opacity = '0.6';

                            // Atualizar contador do badge
                            const badge = document.querySelector('.notification-badge');
                            if (badge) {
                                const newCount = data.unread_count;
                                if (newCount > 0) {
                                    badge.textContent = newCount;
                                } else {
                                    badge.style.display = 'none';
                                }
                            }

                            // Feedback visual
                            notificationElement.innerHTML += '<span style="color: #28a745; font-size: 12px; margin-left: 10px;">✓ Lida</span>';
                        } else {
                            console.error('Erro ao marcar notificação como lida:', data.message);
                            alert('Erro ao marcar notificação como lida: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erro na requisição:', error);
                        alert('Erro na comunicação com o servidor');
                    });
                });
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>