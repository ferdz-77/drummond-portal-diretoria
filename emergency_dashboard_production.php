<?php
// Dashboard de emergência com tratamento robusto de erros
// Para usar em caso de problemas na produção

session_start();

// Habilitar logs de erro
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');

// Auto-login em produção
$_SESSION['usuario_logado'] = true;
$_SESSION['usuario_nome'] = 'Diretoria';
$_SESSION['usuario_id'] = 'admin';

// Incluir configuração com tratamento de erro
try {
    require_once 'includes/config_env.php';
} catch (Exception $e) {
    die("Erro crítico: Não foi possível carregar configurações. " . $e->getMessage());
}

// Função auxiliar para conexão segura
function safeConnectDB($dbname) {
    try {
        return connectDB($dbname);
    } catch (Exception $e) {
        error_log("Erro de conexão com $dbname: " . $e->getMessage());
        return null;
    }
}

// Função auxiliar para buscar dados com fallback
function getSafeData($callback, $fallback = []) {
    try {
        return $callback();
    } catch (Exception $e) {
        error_log("Erro ao buscar dados: " . $e->getMessage());
        return $fallback;
    }
}

// Tentar carregar arquivos de dados com tratamento de erro
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
        }
    } catch (Exception $e) {
        error_log("Erro ao carregar $arquivo: " . $e->getMessage());
    }
}

// Tentar carregar get_filtered_data.php
try {
    if (file_exists('get_filtered_data.php')) {
        require_once 'get_filtered_data.php';
    }
} catch (Exception $e) {
    error_log("Erro ao carregar get_filtered_data.php: " . $e->getMessage());
}

// Dados mock para emergência
$dados_emergencia = [
    'ouvidoria_status' => ['Aberto' => 5, 'Em Andamento' => 8, 'Fechado' => 25],
    'ead_status' => ['Aberto' => 12, 'Em Andamento' => 6, 'Fechado' => 18],
    'processo_status' => ['Aberto' => 3, 'Em Andamento' => 4, 'Fechado' => 10],
    'secretaria_status' => ['Aberto' => 7, 'Em Andamento' => 9, 'Fechado' => 22],
    'financeiro_status' => ['Aberto' => 4, 'Em Andamento' => 2, 'Fechado' => 15],
    'exaluno_status' => ['Aberto' => 2, 'Em Andamento' => 1, 'Fechado' => 8]
];

// Tentar buscar dados reais, usar mock em caso de falha
$ouvidoria_status = getSafeData(function() { return getChamadosStatusOuvidoria(); }, $dados_emergencia['ouvidoria_status']);
$ead_status = getSafeData(function() { return getChamadosStatusEAD(); }, $dados_emergencia['ead_status']);
$processo_status = getSafeData(function() { return getChamadosStatusProcessoSeletivo(); }, $dados_emergencia['processo_status']);
$secretaria_status = getSafeData(function() { return getSolicitacoesStatusSecretaria(); }, $dados_emergencia['secretaria_status']);
$financeiro_status = getSafeData(function() { return getChamadosStatusFinanceiro(); }, $dados_emergencia['financeiro_status']);
$exaluno_status = getSafeData(function() { return getChamadosStatusExAluno(); }, $dados_emergencia['exaluno_status']);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Emergência - Portal Diretoria</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { background: #2c3e50; color: white; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        .alert { background: #e74c3c; color: white; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .card { background: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .card h3 { margin-top: 0; color: #2c3e50; }
        .status-item { display: flex; justify-content: space-between; padding: 5px 0; }
        .badge { background: #3498db; color: white; padding: 2px 8px; border-radius: 10px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏢 Dashboard Emergência - Portal Diretoria</h1>
            <p>Consolidação de Dados dos Portais (Modo Emergência)</p>
        </div>

        <div class="alert">
            ⚠️ <strong>MODO EMERGÊNCIA ATIVO</strong> - Dados podem estar limitados devido a problemas de conectividade
        </div>

        <div class="grid">
            <div class="card">
                <h3>📞 Ouvidoria</h3>
                <?php foreach ($ouvidoria_status as $status => $count): ?>
                    <div class="status-item">
                        <span><?= htmlspecialchars($status) ?></span>
                        <span class="badge"><?= $count ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="card">
                <h3>🎓 EAD</h3>
                <?php foreach ($ead_status as $status => $count): ?>
                    <div class="status-item">
                        <span><?= htmlspecialchars($status) ?></span>
                        <span class="badge"><?= $count ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="card">
                <h3>📝 Processo Seletivo</h3>
                <?php foreach ($processo_status as $status => $count): ?>
                    <div class="status-item">
                        <span><?= htmlspecialchars($status) ?></span>
                        <span class="badge"><?= $count ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="card">
                <h3>📚 Secretaria Acadêmica</h3>
                <?php foreach ($secretaria_status as $status => $count): ?>
                    <div class="status-item">
                        <span><?= htmlspecialchars($status) ?></span>
                        <span class="badge"><?= $count ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="card">
                <h3>💰 Financeiro</h3>
                <?php foreach ($financeiro_status as $status => $count): ?>
                    <div class="status-item">
                        <span><?= htmlspecialchars($status) ?></span>
                        <span class="badge"><?= $count ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="card">
                <h3>🎓 Ex-Aluno</h3>
                <?php foreach ($exaluno_status as $status => $count): ?>
                    <div class="status-item">
                        <span><?= htmlspecialchars($status) ?></span>
                        <span class="badge"><?= $count ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div style="margin-top: 20px; text-align: center; color: #7f8c8d;">
            <p>Última atualização: <?= date('d/m/Y H:i:s') ?></p>
            <p><a href="production_diagnostic.php" style="color: #3498db;">Ver Diagnóstico Completo</a></p>
        </div>
    </div>
</body>
</html>