<?php
// Script para verificar e corrigir configurações de produção
echo "<h1>🔧 Assistente de Configuração de Produção</h1>";

// Verificar ambiente atual
require_once 'includes/config_env.php';
echo "<h2>📋 Status Atual</h2>";
echo "<p><strong>Ambiente detectado:</strong> " . ($environment ?? 'não definido') . "</p>";

// Verificar .env
echo "<h2>📄 Arquivo .env</h2>";
if (file_exists('.env')) {
    $env_content = file_get_contents('.env');
    echo "<pre>" . htmlspecialchars($env_content) . "</pre>";
} else {
    echo "<p style='color: red;'>❌ Arquivo .env não encontrado!</p>";
}

// Testar conexões
echo "<h2>🔌 Teste de Conexões</h2>";
echo "<pre>";

if ($environment === 'production') {
    echo "=== MODO PRODUÇÃO ===\n";

    // Testar Portal Diretoria
    try {
        $conn = connectPortalDiretoria();
        echo "✅ Portal Diretoria: OK\n";
        $conn->close();
    } catch (Exception $e) {
        echo "❌ Portal Diretoria: " . $e->getMessage() . "\n";
    }

    // Testar outros portais
    $portais = ['ouvidoria', 'ead', 'processo_seletivo', 'secretaria', 'financeiro', 'exaluno'];
    foreach ($portais as $portal) {
        try {
            $pdo = connectDB($GLOBALS["dbname_$portal"]);
            echo "✅ $portal: OK\n";
        } catch (Exception $e) {
            echo "❌ $portal: " . $e->getMessage() . "\n";
        }
    }

} else {
    echo "=== MODO DESENVOLVIMENTO ===\n";
    echo "Para testar produção, configure APP_ENV=production no .env\n";
}

echo "</pre>";

// Instruções
echo "<h2>📝 Instruções para Produção</h2>";
echo "<ol>";
echo "<li>Faça backup do arquivo atual <code>.env</code></li>";
echo "<li>Edite o <code>.env</code> e mude <code>APP_ENV=production</code></li>";
echo "<li>Verifique se as credenciais no <code>config_env.php</code> estão corretas</li>";
echo "<li>Teste este script novamente</li>";
echo "<li>Se ainda houver erros, contate o administrador do banco de dados</li>";
echo "</ol>";

echo "<h2>🔄 Alternativa: Usar Dashboard sem Fallback</h2>";
echo "<p>Se preferir dados reais sem fallback, use o dashboard original:</p>";
echo "<p><code>https://gestao-protocolos.drummond.com.br/dashboard.php</code></p>";
echo "<p><em>Nota: Este pode apresentar erro 500 se as conexões falharem.</em></p>";
?>