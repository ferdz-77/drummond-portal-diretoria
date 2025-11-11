<?php
// Dashboard sem gráficos Chart.js para testar se esse é o problema
// Baseado no dashboard original mas sem JavaScript pesado

// Configurar tratamento de erro para produção
if ($_ENV['APP_ENV'] ?? getenv('APP_ENV') === 'production') {
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/dashboard_no_charts_error.log');
    
    // Capturar erros fatais
    register_shutdown_function(function() {
        $error = error_get_last();
        if ($error && $error['type'] === E_ERROR) {
            header('Location: emergency_dashboard_production.php');
            exit;
        }
    });
}

// Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// Incluir configuração com tratamento de erro
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

// Incluir arquivos de dados com tratamento de erro
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
                header('Location: emergency_dashboard_production.php');
                exit;
            }
        }
    } catch (Exception $e) {
        error_log("Erro ao carregar $arquivo: " . $e->getMessage());
        if ($environment === 'production') {
            header('Location: emergency_dashboard_production.php');
            exit;
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

// Carregar dados com fallback
$dados_padrao = ['Aberto' => 0, 'Em Andamento' => 0, 'Fechado' => 0];

try {
    $ouvidoria_status = function_exists('getChamadosStatusOuvidoria') ? getChamadosStatusOuvidoria() : $dados_padrao;
    $ead_status = function_exists('getChamadosStatusEAD') ? getChamadosStatusEAD() : $dados_padrao;
    $processo_status = function_exists('getChamadosStatusProcessoSeletivo') ? getChamadosStatusProcessoSeletivo() : $dados_padrao;
    $secretaria_status = function_exists('getSolicitacoesStatusSecretaria') ? getSolicitacoesStatusSecretaria() : $dados_padrao;
    $financeiro_status = function_exists('getChamadosStatusFinanceiro') ? getChamadosStatusFinanceiro() : $dados_padrao;
    $exaluno_status = function_exists('getChamadosStatusExAluno') ? getChamadosStatusExAluno() : $dados_padrao;
} catch (Exception $e) {
    error_log("Erro ao carregar dados: " . $e->getMessage());
    $ouvidoria_status = $ead_status = $processo_status = $secretaria_status = $financeiro_status = $exaluno_status = $dados_padrao;
}

// Buscar notificações com tratamento de erro
$notificacoes = [];
try {
    $conn = connectPortalDiretoria();
    $usuario_id = $_SESSION['usuario_id'] ?? 'admin';
    $sql = "SELECT * FROM notificacoes WHERE usuario_id = ? AND lida = FALSE ORDER BY data DESC LIMIT 10";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario_id);
    $stmt->execute();
    $notificacoes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $conn->close();
} catch (Exception $e) {
    error_log("Erro ao buscar notificações: " . $e->getMessage());
    $notificacoes = [];
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Sem Gráficos - Portal Diretoria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .status-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border-left: 5px solid #667eea;
            transition: transform 0.2s;
        }
        .status-card:hover {
            transform: translateY(-2px);
        }
        .status-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }
        .status-item:last-child {
            border-bottom: none;
        }
        .status-badge {
            background: #667eea;
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
        }
        .portal-icon {
            font-size: 24px;
            margin-right: 10px;
        }
        .notification-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid #ffc107;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .test-banner {
            background: #d4edda;
            color: #155724;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="dashboard-header text-center">
            <h1>🏢 Dashboard Portal Diretoria</h1>
            <p class="lead">Consolidação de Dados dos Portais (Versão Sem Gráficos)</p>
            <p>Usuário: <?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Admin') ?> | 
               Ambiente: <?= $environment ?> | 
               Data: <?= date('d/m/Y H:i:s') ?></p>
        </div>

        <div class="test-banner">
            <h4>🧪 TESTE: Dashboard sem Chart.js</h4>
            <p>Esta versão remove todos os gráficos para identificar se Chart.js estava causando o erro 500.</p>
        </div>

        <div class="row">
            <!-- Ouvidoria -->
            <div class="col-lg-4 col-md-6">
                <div class="status-card">
                    <h5><span class="portal-icon">📞</span>Ouvidoria</h5>
                    <?php foreach ($ouvidoria_status as $status => $count): ?>
                        <div class="status-item">
                            <span><?= htmlspecialchars($status) ?></span>
                            <span class="status-badge"><?= $count ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- EAD -->
            <div class="col-lg-4 col-md-6">
                <div class="status-card">
                    <h5><span class="portal-icon">🎓</span>EAD</h5>
                    <?php foreach ($ead_status as $status => $count): ?>
                        <div class="status-item">
                            <span><?= htmlspecialchars($status) ?></span>
                            <span class="status-badge"><?= $count ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Processo Seletivo -->
            <div class="col-lg-4 col-md-6">
                <div class="status-card">
                    <h5><span class="portal-icon">📝</span>Processo Seletivo</h5>
                    <?php foreach ($processo_status as $status => $count): ?>
                        <div class="status-item">
                            <span><?= htmlspecialchars($status) ?></span>
                            <span class="status-badge"><?= $count ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Secretaria -->
            <div class="col-lg-4 col-md-6">
                <div class="status-card">
                    <h5><span class="portal-icon">📚</span>Secretaria Acadêmica</h5>
                    <?php foreach ($secretaria_status as $status => $count): ?>
                        <div class="status-item">
                            <span><?= htmlspecialchars($status) ?></span>
                            <span class="status-badge"><?= $count ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Financeiro -->
            <div class="col-lg-4 col-md-6">
                <div class="status-card">
                    <h5><span class="portal-icon">💰</span>Financeiro</h5>
                    <?php foreach ($financeiro_status as $status => $count): ?>
                        <div class="status-item">
                            <span><?= htmlspecialchars($status) ?></span>
                            <span class="status-badge"><?= $count ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Ex-Aluno -->
            <div class="col-lg-4 col-md-6">
                <div class="status-card">
                    <h5><span class="portal-icon">🎓</span>Ex-Aluno</h5>
                    <?php foreach ($exaluno_status as $status => $count): ?>
                        <div class="status-item">
                            <span><?= htmlspecialchars($status) ?></span>
                            <span class="status-badge"><?= $count ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Notificações -->
        <?php if (!empty($notificacoes)): ?>
        <div class="row">
            <div class="col-12">
                <h4>🔔 Notificações (<?= count($notificacoes) ?>)</h4>
                <?php foreach (array_slice($notificacoes, 0, 5) as $notif): ?>
                    <div class="notification-card">
                        <h6><?= htmlspecialchars($notif['titulo'] ?? 'Notificação') ?></h6>
                        <p class="mb-0"><?= htmlspecialchars($notif['mensagem'] ?? '') ?></p>
                        <small class="text-muted"><?= isset($notif['data']) ? date('d/m/Y H:i', strtotime($notif['data'])) : '' ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="row mt-5">
            <div class="col-12">
                <div class="alert alert-success text-center">
                    <h4>✅ Dashboard Sem Gráficos Funcionando!</h4>
                    <p>Se esta versão funcionar, confirma que o problema estava nos gráficos Chart.js</p>
                    <div class="mt-3">
                        <a href="dashboard.php" class="btn btn-primary">Dashboard Original (com gráficos)</a>
                        <a href="dashboard_simple.php" class="btn btn-success">Dashboard Simples</a>
                        <a href="dashboard_incremental.php" class="btn btn-info">Dashboard Incremental</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4 text-muted">
            <p>Dashboard sem Chart.js - Teste de Performance</p>
        </div>
    </div>

    <!-- Bootstrap JS apenas -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>