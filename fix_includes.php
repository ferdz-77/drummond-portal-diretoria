<?php
// Script para corrigir todas as inclusões de config.php para config_env.php
// Execute este arquivo para verificar e corrigir problemas de inclusão

echo "<h1>Correção de Inclusões de Config</h1>";
echo "<p>Data/Hora: " . date('Y-m-d H:i:s') . "</p>";

// Lista de arquivos que podem ter inclusões problemáticas
$arquivos_verificar = [
    'get_filtered_data.php',
    'dashboard.php',
    'data/data_ouvidoria.php',
    'data/data_ead.php',
    'data/data_processo_seletivo.php',
    'data/data_secretaria.php',
    'data/data_financeiro.php',
    'data/data_exaluno.php',
    'includes/filter_helpers.php'
];

echo "<h2>1. Verificação de Inclusões</h2>";

$problemas_encontrados = [];
$arquivos_ok = [];

foreach ($arquivos_verificar as $arquivo) {
    if (file_exists($arquivo)) {
        $conteudo = file_get_contents($arquivo);
        
        // Verificar se contém inclusão do config.php antigo
        if (strpos($conteudo, "includes/config.php") !== false) {
            $problemas_encontrados[] = $arquivo;
            echo "<p style='color: red;'>❌ $arquivo - Contém inclusão do config.php antigo</p>";
        } else if (strpos($conteudo, "includes/config_env.php") !== false) {
            $arquivos_ok[] = $arquivo;
            echo "<p style='color: green;'>✅ $arquivo - Usando config_env.php correto</p>";
        } else {
            echo "<p style='color: gray;'>➖ $arquivo - Sem inclusão de config</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ $arquivo - Arquivo não encontrado</p>";
    }
}

echo "<h2>2. Resumo</h2>";
echo "<p><strong>Arquivos OK:</strong> " . count($arquivos_ok) . "</p>";
echo "<p><strong>Problemas encontrados:</strong> " . count($problemas_encontrados) . "</p>";

if (!empty($problemas_encontrados)) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; border: 1px solid #f5c6cb; margin: 10px 0;'>";
    echo "<h3>⚠️ AÇÃO NECESSÁRIA</h3>";
    echo "<p>Os seguintes arquivos precisam ser atualizados em produção:</p>";
    echo "<ul>";
    foreach ($problemas_encontrados as $arquivo) {
        echo "<li><code>$arquivo</code></li>";
    }
    echo "</ul>";
    echo "<p><strong>Solução:</strong> Fazer upload das versões locais atualizadas para o servidor de produção.</p>";
    echo "</div>";
} else {
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; border: 1px solid #c3e6cb; margin: 10px 0;'>";
    echo "<h3>✅ TUDO OK!</h3>";
    echo "<p>Todos os arquivos estão usando as inclusões corretas.</p>";
    echo "</div>";
}

echo "<h2>3. Verificação Específica do get_filtered_data.php</h2>";

if (file_exists('get_filtered_data.php')) {
    $conteudo = file_get_contents('get_filtered_data.php');
    $linhas = explode("\n", $conteudo);
    
    echo "<p>Verificando linha 15 especificamente:</p>";
    if (isset($linhas[14])) { // Linha 15 = índice 14
        $linha15 = trim($linhas[14]);
        echo "<p><strong>Linha 15:</strong> <code>" . htmlspecialchars($linha15) . "</code></p>";
        
        if (strpos($linha15, "config_env.php") !== false) {
            echo "<p style='color: green;'>✅ Linha 15 está correta localmente</p>";
            echo "<p style='color: orange;'>⚠️ O problema é que o arquivo em produção não foi atualizado</p>";
        } else if (strpos($linha15, "config.php") !== false) {
            echo "<p style='color: red;'>❌ Linha 15 contém inclusão antiga</p>";
        }
    }
} else {
    echo "<p style='color: red;'>❌ get_filtered_data.php não encontrado</p>";
}

echo "<h2>4. Teste Rápido</h2>";
echo "<p>Testando se as funções estão disponíveis após incluir config_env.php:</p>";

try {
    require_once 'includes/config_env.php';
    
    if (function_exists('connectDB')) {
        echo "<p style='color: green;'>✅ connectDB disponível</p>";
    } else {
        echo "<p style='color: red;'>❌ connectDB não disponível</p>";
    }
    
    if (function_exists('connectPortalDiretoria')) {
        echo "<p style='color: green;'>✅ connectPortalDiretoria disponível</p>";
    } else {
        echo "<p style='color: red;'>❌ connectPortalDiretoria não disponível</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro ao incluir config_env.php: " . $e->getMessage() . "</p>";
}

echo "<h2>5. Próximos Passos</h2>";
echo "<div style='background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; border: 1px solid #bee5eb; margin: 10px 0;'>";
echo "<h4>Para resolver em produção:</h4>";
echo "<ol>";
echo "<li><strong>Fazer upload do get_filtered_data.php atualizado</strong></li>";
echo "<li>Verificar se outros arquivos precisam ser atualizados</li>";
echo "<li>Testar novamente o conflict_diagnostic.php</li>";
echo "<li>Se tudo ok, testar o dashboard.php</li>";
echo "</ol>";
echo "</div>";

echo "<p><a href='conflict_diagnostic.php'>Executar Diagnóstico Novamente</a> | <a href='dashboard.php'>Testar Dashboard</a></p>";
?>