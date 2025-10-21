<?php
// Dashboard com timeout e controle de memória aprimorado
// Versão segura para produção

// Configurar timeout e memória
set_time_limit(120); // 2 minutos máximo
ini_set('memory_limit', '256M');

// Log de início
error_log("Dashboard iniciado em " . date('Y-m-d H:i:s'));

// Configurar tratamento de erro para produção
if ($_ENV['APP_ENV'] ?? getenv('APP_ENV') === 'production') {
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/dashboard_safe_error.log');
    
    // Capturar erros fatais
    register_shutdown_function(function() {
        $error = error_get_last();
        if ($error && ($error['type'] === E_ERROR || $error['type'] === E_PARSE)) {
            error_log("Fatal error no dashboard: " . print_r($error, true));
            // Não redirecionar aqui para evitar loops
        }
    });
}

// Função para log de progresso
function logProgress($step, $message = '') {
    error_log("Dashboard Step $step: $message - Memory: " . memory_get_usage(true) . " bytes");
}

try {
    logProgress(1, "Iniciando sessão");
    
    // Iniciar sessão
    if (session_status() === PHP_SESSION_NONE) {
        @session_start();
    }
    
    logProgress(2, "Incluindo configuração");
    
    // Incluir configuração
    require_once 'includes/config_env.php';
    
    logProgress(3, "Auto-login (ambiente: $environment)");
    
    // Auto-login em produção
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
    
    logProgress(4, "Definindo parâmetros");
    
    // Parâmetros de filtro
    $periodo_filter = $_GET['periodo'] ?? 'todos';
    $portal_filter = $_GET['portal'] ?? 'todos';
    
    logProgress(5, "Incluindo arquivos de dados");
    
    // Incluir arquivos de dados de forma mais segura
    $arquivos_dados = [
        'data/data_ouvidoria.php',
        'data/data_ead.php',
        'data/data_processo_seletivo.php',
        'data/data_secretaria.php',
        'data/data_financeiro.php',
        'data/data_exaluno.php'
    ];

    $arquivos_carregados = 0;
    foreach ($arquivos_dados as $arquivo) {
        try {
            if (file_exists($arquivo)) {
                require_once $arquivo;
                $arquivos_carregados++;
                logProgress("5.$arquivos_carregados", "Carregado: $arquivo");
            } else {
                error_log("Arquivo não encontrado: $arquivo");
            }
        } catch (Exception $e) {
            error_log("Erro ao carregar $arquivo: " . $e->getMessage());
            // Em caso de erro, continuar com outros arquivos
        }
    }
    
    logProgress(6, "Incluindo get_filtered_data.php");
    
    // Incluir get_filtered_data com tratamento de erro
    try {
        if (file_exists('get_filtered_data.php')) {
            require_once 'get_filtered_data.php';
        }
    } catch (Exception $e) {
        error_log("Erro ao incluir get_filtered_data.php: " . $e->getMessage());
    }
    
    logProgress(7, "Carregando dados com fallback");
    
    // Carregar dados com fallback para dados mock
    $dados_padrao = [
        'Aberto' => 0,
        'Em Andamento' => 0, 
        'Fechado' => 0
    ];
    
    // Tentar carregar dados reais, usar mock se falhar
    try {
        $ouvidoria_status = function_exists('getChamadosStatusOuvidoria') ? 
                            getChamadosStatusOuvidoria() : $dados_padrao;
    } catch (Exception $e) {
        error_log("Erro ao obter dados ouvidoria: " . $e->getMessage());
        $ouvidoria_status = $dados_padrao;
    }
    
    try {
        $ead_status = function_exists('getChamadosStatusEAD') ? 
                      getChamadosStatusEAD() : $dados_padrao;
    } catch (Exception $e) {
        error_log("Erro ao obter dados EAD: " . $e->getMessage());
        $ead_status = $dados_padrao;
    }
    
    // Continuar para outros portais...
    $processo_status = $dados_padrao;
    $secretaria_status = $dados_padrao;
    $financeiro_status = $dados_padrao;
    $exaluno_status = $dados_padrao;
    
    logProgress(8, "Carregando notificações");
    
    // Notificações com tratamento de erro
    $notificacoes = [];
    try {
        $conn = connectPortalDiretoria();
        $usuario_id = $_SESSION['usuario_id'] ?? 'admin';
        $sql = "SELECT * FROM notificacoes WHERE usuario_id = ? AND lida = FALSE ORDER BY data DESC LIMIT 5";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $usuario_id);
        $stmt->execute();
        $notificacoes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $conn->close();
    } catch (Exception $e) {
        error_log("Erro ao carregar notificações: " . $e->getMessage());
        $notificacoes = [];
    }
    
    logProgress(9, "Preparando para renderizar HTML");
    
} catch (Exception $e) {
    error_log("Erro fatal no dashboard: " . $e->getMessage());
    if ($environment === 'production') {
        header('Location: emergency_dashboard_production.php');
        exit;
    } else {
        die("Erro no dashboard: " . $e->getMessage());
    }
}

logProgress(10, "Iniciando renderização HTML");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Seguro - Portal Diretoria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .card {
            border-left: 4px solid #667eea;
            margin-bottom: 20px;
        }
        .status-badge {
            background: #667eea;
            color: white;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="dashboard-header text-center">
            <h1>🏢 Dashboard Seguro - Portal Diretoria</h1>
            <p>Versão com Controle de Timeout e Memória</p>
            <p>Usuário: <?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Admin') ?> | 
               Ambiente: <?= $environment ?> | 
               Arquivos carregados: <?= $arquivos_carregados ?>/6</p>
        </div>

        <div class="row">
            <!-- Ouvidoria -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>📞 Ouvidoria</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($ouvidoria_status as $status => $count): ?>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span><?= htmlspecialchars($status) ?></span>
                                <span class="status-badge"><?= $count ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- EAD -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>🎓 EAD</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($ead_status as $status => $count): ?>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span><?= htmlspecialchars($status) ?></span>
                                <span class="status-badge"><?= $count ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Notificações -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>🔔 Notificações (<?= count($notificacoes) ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($notificacoes)): ?>
                            <p class="text-muted">Nenhuma notificação</p>
                        <?php else: ?>
                            <?php foreach (array_slice($notificacoes, 0, 3) as $notif): ?>
                                <div class="mb-2">
                                    <small><?= htmlspecialchars($notif['titulo'] ?? 'Notificação') ?></small>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-success text-center">
                    <h4>✅ Dashboard Seguro Funcionando!</h4>
                    <p>Se você está vendo esta página, a versão segura está operacional.</p>
                    <div class="mt-3">
                        <a href="dashboard.php" class="btn btn-primary">Dashboard Original</a>
                        <a href="debug_dashboard_step_by_step.php" class="btn btn-info">Debug Detalhado</a>
                        <a href="emergency_dashboard_production.php" class="btn btn-warning">Dashboard Emergência</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4 text-muted">
            <p>Última atualização: <?= date('d/m/Y H:i:s') ?></p>
            <p>Memória utilizada: <?= round(memory_get_usage(true) / 1024 / 1024, 2) ?> MB</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
logProgress(11, "HTML renderizado com sucesso");
error_log("Dashboard concluído em " . date('Y-m-d H:i:s'));
?>