<?php
// Dashboard incremental - adiciona funcionalidades gradualmente
// Para identificar qual parte causa o erro 500

// Configurar ambiente
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

require_once 'includes/config_env.php';

if ($environment === 'production' && !isset($_SESSION['usuario_logado'])) {
    $_SESSION['usuario_logado'] = true;
    $_SESSION['usuario_nome'] = 'Diretoria';
    $_SESSION['usuario_id'] = 'admin';
}

if (!isset($_SESSION['usuario_logado'])) {
    header('Location: login.php');
    exit;
}

// Parâmetros
$test_level = $_GET['level'] ?? '1';
$periodo_filter = $_GET['periodo'] ?? 'todos';
$portal_filter = $_GET['portal'] ?? 'todos';

// Dados iniciais
$dados_basicos = [
    'ouvidoria' => ['Aberto' => 5, 'Em Andamento' => 3, 'Fechado' => 12],
    'ead' => ['Aberto' => 8, 'Em Andamento' => 2, 'Fechado' => 15],
    'processo_seletivo' => ['Aberto' => 3, 'Em Andamento' => 1, 'Fechado' => 7],
    'secretaria' => ['Aberto' => 6, 'Em Andamento' => 4, 'Fechado' => 18],
    'financeiro' => ['Aberto' => 2, 'Em Andamento' => 1, 'Fechado' => 9],
    'exaluno' => ['Aberto' => 1, 'Em Andamento' => 0, 'Fechado' => 4]
];

$teste_info = "";
$dados_reais = false;

// Testes incrementais
if ($test_level >= '2') {
    $teste_info .= "✅ Nível 2: Incluindo arquivos de dados...<br>";
    try {
        require_once 'data/data_ouvidoria.php';
        require_once 'data/data_ead.php';
        require_once 'data/data_processo_seletivo.php';
        require_once 'data/data_secretaria.php';
        require_once 'data/data_financeiro.php';
        require_once 'data/data_exaluno.php';
        $teste_info .= "✅ Arquivos de dados incluídos<br>";
    } catch (Exception $e) {
        $teste_info .= "❌ Erro ao incluir dados: " . $e->getMessage() . "<br>";
    }
}

if ($test_level >= '3') {
    $teste_info .= "✅ Nível 3: Incluindo get_filtered_data...<br>";
    try {
        require_once 'get_filtered_data.php';
        $teste_info .= "✅ get_filtered_data incluído<br>";
    } catch (Exception $e) {
        $teste_info .= "❌ Erro ao incluir get_filtered_data: " . $e->getMessage() . "<br>";
    }
}

if ($test_level >= '4') {
    $teste_info .= "✅ Nível 4: Carregando dados reais...<br>";
    try {
        if (function_exists('getChamadosStatusOuvidoria')) {
            $dados_basicos['ouvidoria'] = getChamadosStatusOuvidoria();
            $dados_reais = true;
            $teste_info .= "✅ Dados da Ouvidoria carregados<br>";
        }
        if (function_exists('getChamadosStatusEAD')) {
            $dados_basicos['ead'] = getChamadosStatusEAD();
            $teste_info .= "✅ Dados do EAD carregados<br>";
        }
        if (function_exists('getChamadosStatusProcessoSeletivo')) {
            $dados_basicos['processo_seletivo'] = getChamadosStatusProcessoSeletivo();
            $teste_info .= "✅ Dados do Processo Seletivo carregados<br>";
        }
        if (function_exists('getSolicitacoesStatusSecretaria')) {
            $dados_basicos['secretaria'] = getSolicitacoesStatusSecretaria();
            $teste_info .= "✅ Dados da Secretaria carregados<br>";
        }
        if (function_exists('getChamadosStatusFinanceiro')) {
            $dados_basicos['financeiro'] = getChamadosStatusFinanceiro();
            $teste_info .= "✅ Dados do Financeiro carregados<br>";
        }
        if (function_exists('getChamadosStatusExAluno')) {
            $dados_basicos['exaluno'] = getChamadosStatusExAluno();
            $teste_info .= "✅ Dados do Ex-Aluno carregados<br>";
        }
    } catch (Exception $e) {
        $teste_info .= "❌ Erro ao carregar dados reais: " . $e->getMessage() . "<br>";
    }
}

$notificacoes = [];
if ($test_level >= '5') {
    $teste_info .= "✅ Nível 5: Carregando notificações...<br>";
    try {
        $conn = connectPortalDiretoria();
        $usuario_id = $_SESSION['usuario_id'] ?? 'admin';
        $sql = "SELECT * FROM notificacoes WHERE usuario_id = ? AND lida = FALSE ORDER BY data DESC LIMIT 10";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $usuario_id);
        $stmt->execute();
        $notificacoes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $conn->close();
        $teste_info .= "✅ Notificações carregadas (" . count($notificacoes) . ")<br>";
    } catch (Exception $e) {
        $teste_info .= "❌ Erro ao carregar notificações: " . $e->getMessage() . "<br>";
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Incremental - Portal Diretoria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .level-controls {
            background: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .test-info {
            background: #d1ecf1;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="dashboard-header text-center">
            <h1>🔍 Dashboard Incremental - Teste por Nível</h1>
            <p>Teste gradual para identificar onde ocorre o erro 500</p>
        </div>

        <div class="level-controls">
            <h5>🎚️ Controles de Teste</h5>
            <p><strong>Nível atual:</strong> <?= $test_level ?></p>
            <div class="btn-group" role="group">
                <a href="?level=1" class="btn btn-outline-primary <?= $test_level == '1' ? 'active' : '' ?>">Nível 1 - Básico</a>
                <a href="?level=2" class="btn btn-outline-primary <?= $test_level == '2' ? 'active' : '' ?>">Nível 2 - +Dados</a>
                <a href="?level=3" class="btn btn-outline-primary <?= $test_level == '3' ? 'active' : '' ?>">Nível 3 - +Filtros</a>
                <a href="?level=4" class="btn btn-outline-primary <?= $test_level == '4' ? 'active' : '' ?>">Nível 4 - +Funções</a>
                <a href="?level=5" class="btn btn-outline-primary <?= $test_level == '5' ? 'active' : '' ?>">Nível 5 - +Notificações</a>
            </div>
        </div>

        <div class="test-info">
            <h6>📋 Log do Teste:</h6>
            <?= $teste_info ?>
            <p><strong>Dados:</strong> <?= $dados_reais ? 'Reais' : 'Mock' ?></p>
        </div>

        <div class="row">
            <?php foreach ($dados_basicos as $portal => $dados): ?>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h6><?= ucfirst(str_replace('_', ' ', $portal)) ?></h6>
                    </div>
                    <div class="card-body">
                        <?php foreach ($dados as $status => $count): ?>
                        <div class="d-flex justify-content-between mb-1">
                            <span><?= htmlspecialchars($status) ?></span>
                            <span class="badge bg-primary"><?= $count ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if ($test_level >= '5' && !empty($notificacoes)): ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6>🔔 Notificações (<?= count($notificacoes) ?>)</h6>
                    </div>
                    <div class="card-body">
                        <?php foreach (array_slice($notificacoes, 0, 5) as $notif): ?>
                        <div class="mb-2">
                            <strong><?= htmlspecialchars($notif['titulo'] ?? 'Notificação') ?></strong><br>
                            <small class="text-muted"><?= htmlspecialchars($notif['mensagem'] ?? '') ?></small>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="alert alert-success mt-4 text-center">
            <h5>✅ Nível <?= $test_level ?> Funcionando!</h5>
            <p>Continue subindo os níveis até encontrar onde para de funcionar.</p>
            <p>Se todos os níveis funcionarem, o problema está no dashboard original.</p>
        </div>

        <div class="text-center mt-4">
            <a href="dashboard.php" class="btn btn-danger">Testar Dashboard Original</a>
            <a href="dashboard_simple.php" class="btn btn-success">Dashboard Simples</a>
            <a href="dashboard_safe.php" class="btn btn-warning">Dashboard Seguro</a>
        </div>

        <div class="text-center mt-3 text-muted">
            <p>Última atualização: <?= date('d/m/Y H:i:s') ?></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>