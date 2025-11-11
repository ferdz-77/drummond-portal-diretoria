<?php
// Diagnóstico específico para conflitos de função
// Para verificar se o problema de redeclaração foi resolvido

// Habilitar exibição de erros para diagnóstico
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Diagnóstico de Conflitos - Portal Diretoria</h1>";
echo "<p>Data/Hora: " . date('Y-m-d H:i:s') . "</p>";

// Verificar quais arquivos config existem
echo "<h2>1. Verificação de Arquivos de Configuração</h2>";
$configs = [
    'includes/config_env.php' => 'Principal (deve existir)',
    'includes/config.php' => 'Antigo (pode causar conflito)',
    'includes/config_backup_old.php' => 'Backup (seguro)'
];

foreach ($configs as $arquivo => $desc) {
    if (file_exists($arquivo)) {
        echo "<p style='color: green;'>✓ $arquivo existe - $desc</p>";
    } else {
        echo "<p style='color: gray;'>✗ $arquivo não existe - $desc</p>";
    }
}

// Testar inclusão do config_env.php
echo "<h2>2. Teste de Inclusão Principal</h2>";
try {
    require_once 'includes/config_env.php';
    echo "<p style='color: green;'>✓ config_env.php incluído com sucesso</p>";
    echo "<p>Ambiente: <strong>$environment</strong></p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Erro ao incluir config_env.php: " . $e->getMessage() . "</p>";
    exit;
}

// Verificar se as funções existem após a inclusão
echo "<h2>3. Verificação de Funções</h2>";
$funcoes = ['connectDB', 'connectPortalDiretoria'];
foreach ($funcoes as $funcao) {
    if (function_exists($funcao)) {
        echo "<p style='color: green;'>✓ Função $funcao existe</p>";
    } else {
        echo "<p style='color: red;'>✗ Função $funcao NÃO existe</p>";
    }
}

// Simular o que o dashboard faz - incluir arquivos de dados
echo "<h2>4. Teste de Inclusão dos Arquivos de Dados</h2>";
$arquivos_dados = [
    'data/data_ouvidoria.php',
    'data/data_ead.php',
    'data/data_processo_seletivo.php',
    'data/data_secretaria.php',
    'data/data_financeiro.php',
    'data/data_exaluno.php'
];

$sucesso_total = true;
foreach ($arquivos_dados as $arquivo) {
    try {
        if (file_exists($arquivo)) {
            require_once $arquivo;
            echo "<p style='color: green;'>✓ $arquivo incluído com sucesso</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ $arquivo não encontrado</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Erro ao incluir $arquivo: " . $e->getMessage() . "</p>";
        $sucesso_total = false;
    }
}

// Testar get_filtered_data.php
echo "<h2>5. Teste do get_filtered_data.php</h2>";
try {
    if (file_exists('get_filtered_data.php')) {
        require_once 'get_filtered_data.php';
        echo "<p style='color: green;'>✓ get_filtered_data.php incluído com sucesso</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ get_filtered_data.php não encontrado</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Erro ao incluir get_filtered_data.php: " . $e->getMessage() . "</p>";
    $sucesso_total = false;
}

// Teste final - simular início do dashboard
echo "<h2>6. Teste Completo (Simulação do Dashboard)</h2>";
try {
    session_start();
    $_SESSION['usuario_logado'] = true;
    $_SESSION['usuario_nome'] = 'Teste';
    $_SESSION['usuario_id'] = 'admin';
    
    // Tentar conectar
    $conn = connectPortalDiretoria();
    echo "<p style='color: green;'>✓ Conexão com Portal Diretoria OK</p>";
    $conn->close();
    
    echo "<p style='color: green; font-weight: bold;'>🎉 TESTE COMPLETO PASSOU! Dashboard deve funcionar agora.</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Erro no teste completo: " . $e->getMessage() . "</p>";
    $sucesso_total = false;
}

// Resultado final
echo "<h2>7. Resultado Final</h2>";
if ($sucesso_total) {
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; border: 1px solid #c3e6cb;'>";
    echo "<h3>✅ SUCESSO!</h3>";
    echo "<p>Todos os testes passaram. O dashboard deve funcionar corretamente em produção.</p>";
    echo "<p><strong>Próximos passos:</strong></p>";
    echo "<ul>";
    echo "<li>Fazer upload dos arquivos corrigidos para produção</li>";
    echo "<li>Renomear includes/config.php para includes/config_backup_old.php no servidor</li>";
    echo "<li>Testar o dashboard em produção</li>";
    echo "</ul>";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; border: 1px solid #f5c6cb;'>";
    echo "<h3>❌ FALHA</h3>";
    echo "<p>Ainda há problemas que precisam ser resolvidos.</p>";
    echo "</div>";
}

echo "<p><a href='dashboard.php'>Testar Dashboard</a> | <a href='emergency_dashboard_production.php'>Dashboard Emergência</a></p>";
?>