<?php
// Dashboard simplificado para testar funcionalidade básica
// Se este funcionar, o problema está no dashboard completo

// Configurar tratamento de erro para produção
if ($_ENV['APP_ENV'] ?? getenv('APP_ENV') === 'production') {
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/dashboard_simple_error.log');
    
    // Capturar erros fatais
    register_shutdown_function(function() {
        $error = error_get_last();
        if ($error && $error['type'] === E_ERROR) {
            file_put_contents(__DIR__ . '/fatal_error.log', 
                date('Y-m-d H:i:s') . " - Fatal Error: " . print_r($error, true) . "\n", 
                FILE_APPEND);
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

// Dados mock simples para testar
$dados_mock = [
    'ouvidoria' => ['Aberto' => 5, 'Em Andamento' => 3, 'Fechado' => 12],
    'ead' => ['Aberto' => 8, 'Em Andamento' => 2, 'Fechado' => 15],
    'processo_seletivo' => ['Aberto' => 3, 'Em Andamento' => 1, 'Fechado' => 7],
    'secretaria' => ['Aberto' => 6, 'Em Andamento' => 4, 'Fechado' => 18],
    'financeiro' => ['Aberto' => 2, 'Em Andamento' => 1, 'Fechado' => 9],
    'exaluno' => ['Aberto' => 1, 'Em Andamento' => 0, 'Fechado' => 4]
];

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Simplificado - Portal Diretoria</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            background: #f8f9fa; 
        }
        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
        }
        .header { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white; 
            padding: 20px; 
            border-radius: 10px; 
            margin-bottom: 20px; 
            text-align: center;
        }
        .success-banner {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #c3e6cb;
            margin-bottom: 20px;
            text-align: center;
        }
        .grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); 
            gap: 20px; 
        }
        .card { 
            background: white; 
            padding: 20px; 
            border-radius: 10px; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-left: 4px solid #667eea;
        }
        .card h3 { 
            margin-top: 0; 
            color: #333; 
            font-size: 18px;
        }
        .status-item { 
            display: flex; 
            justify-content: space-between; 
            padding: 8px 0; 
            border-bottom: 1px solid #eee;
        }
        .status-item:last-child {
            border-bottom: none;
        }
        .badge { 
            background: #667eea; 
            color: white; 
            padding: 4px 12px; 
            border-radius: 15px; 
            font-size: 14px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #6c757d;
        }
        .test-links {
            background: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            text-align: center;
        }
        .test-links a {
            margin: 0 10px;
            color: #007bff;
            text-decoration: none;
        }
        .test-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏢 Dashboard Simplificado - Portal Diretoria</h1>
            <p>Consolidação de Dados dos Portais (Versão Teste)</p>
            <p>Usuário: <?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Admin') ?> | Ambiente: <?= $environment ?></p>
        </div>

        <div class="success-banner">
            <h3>✅ DASHBOARD SIMPLIFICADO FUNCIONANDO!</h3>
            <p>Se você está vendo esta página, significa que a estrutura básica está OK.</p>
        </div>

        <div class="grid">
            <?php foreach ($dados_mock as $portal => $status): ?>
                <div class="card">
                    <h3><?= ucfirst(str_replace('_', ' ', $portal)) ?></h3>
                    <?php foreach ($status as $tipo => $count): ?>
                        <div class="status-item">
                            <span><?= htmlspecialchars($tipo) ?></span>
                            <span class="badge"><?= $count ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="test-links">
            <h4>🔧 Links de Teste e Diagnóstico</h4>
            <a href="dashboard.php">Dashboard Original</a>
            <a href="debug_dashboard_step_by_step.php">Debug Passo a Passo</a>
            <a href="health_check.php">Health Check</a>
            <a href="emergency_dashboard_production.php">Dashboard Emergência</a>
        </div>

        <div class="footer">
            <p>Última atualização: <?= date('d/m/Y H:i:s') ?></p>
            <p>Versão Simplificada - Para teste de funcionalidade básica</p>
        </div>
    </div>
</body>
</html>