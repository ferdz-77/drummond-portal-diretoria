<?php
// Verificação de sintaxe PHP
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Verificação de Sintaxe - Dashboard</h1>";

$arquivos_verificar = [
    'dashboard.php',
    'includes/config_env.php',
    'emergency_dashboard_production.php'
];

foreach ($arquivos_verificar as $arquivo) {
    if (!file_exists($arquivo)) {
        echo "<p>❌ $arquivo: Arquivo não encontrado</p>";
        continue;
    }
    
    $output = [];
    $return_var = 0;
    $comando = "php -l $arquivo";
    exec($comando, $output, $return_var);
    
    if ($return_var === 0) {
        echo "<p>✅ $arquivo: Sintaxe OK</p>";
    } else {
        echo "<p>❌ $arquivo: Erro de sintaxe</p>";
        echo "<pre>" . implode("\n", $output) . "</pre>";
    }
}

echo "<h2>Teste Direto de Inclusão</h2>";

// Testar inclusão direta
try {
    ob_start();
    require_once 'includes/config_env.php';
    ob_end_clean();
    echo "<p>✅ config_env.php incluído com sucesso</p>";
    
    if (function_exists('connectDBEnvironment')) {
        echo "<p>✅ Função connectDBEnvironment disponível</p>";
    } else {
        echo "<p>❌ Função connectDBEnvironment não disponível</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Erro ao incluir config: " . $e->getMessage() . "</p>";
} catch (Error $e) {
    echo "<p>❌ Erro fatal: " . $e->getMessage() . "</p>";
}
?>