<?php
// CONFIGURAÇÃO DE EMERGÊNCIA - FORÇA DESENVOLVIMENTO
// Use este arquivo temporariamente se produção estiver com problemas

// Forçar desenvolvimento
$_ENV['APP_ENV'] = 'development';

// Carregar configuração de desenvolvimento
require_once 'includes/config_env.php';

// Debug
echo "🚨 MODO EMERGÊNCIA ATIVADO\n";
echo "==========================\n\n";
echo "Ambiente forçado: $environment\n";
echo "Host: $host\n\n";

echo "✅ Sistema carregado em modo desenvolvimento\n";
echo "⚠️  NOTIFICAÇÕES NÃO FUNCIONARÃO (banco local)\n\n";

echo "💡 Para voltar ao normal:\n";
echo "- Corrija o host no config_env.php\n";
echo "- Remova ou renomeie este arquivo\n";
echo "- Teste com debug_production.php\n\n";

echo "<a href='dashboard.php'>Acessar Dashboard (Modo Emergência)</a>\n";
?>