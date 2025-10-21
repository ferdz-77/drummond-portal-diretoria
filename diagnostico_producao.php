<?php
// Arquivo de diagnóstico para produção
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Diagnóstico Dashboard - Produção</h1>";
echo "<p>Data/Hora: " . date('Y-m-d H:i:s') . "</p>";

// Verificar arquivos essenciais
$arquivos_essenciais = [
    'includes/config_env.php',
    'config_env.php',
    '.env'
];

echo "<h2>Verificação de Arquivos</h2>";
foreach ($arquivos_essenciais as $arquivo) {
    $existe = file_exists($arquivo);
    $legivel = $existe ? is_readable($arquivo) : false;
    echo "<p>$arquivo: " . ($existe ? '✅ Existe' : '❌ Não existe') . 
         ($legivel ? ' - Legível' : ' - Não legível') . "</p>";
}

// Verificar variáveis de ambiente
echo "<h2>Variáveis de Ambiente</h2>";
echo "<p>APP_ENV (env): " . (getenv('APP_ENV') ?: 'não definida') . "</p>";
echo "<p>APP_ENV (\$_ENV): " . ($_ENV['APP_ENV'] ?? 'não definida') . "</p>";

// Testar inclusão do config
echo "<h2>Teste de Inclusão</h2>";
try {
    if (file_exists('includes/config_env.php')) {
        require_once 'includes/config_env.php';
        echo "<p>✅ includes/config_env.php carregado com sucesso</p>";
        echo "<p>Environment definido: " . ($environment ?? 'não definido') . "</p>";
    } else {
        echo "<p>❌ includes/config_env.php não encontrado</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Erro ao carregar config: " . $e->getMessage() . "</p>";
}

// Verificar função connectDBEnvironment
if (function_exists('connectDBEnvironment')) {
    echo "<p>✅ Função connectDBEnvironment disponível</p>";
    try {
        // Testar conexão com banco da ouvidoria
        $conn = connectDBEnvironment('ouvidoria');
        echo "<p>✅ Conexão de banco estabelecida (ouvidoria)</p>";
        if ($conn) {
            $conn->close();
        }
    } catch (Exception $e) {
        echo "<p>❌ Erro na conexão: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>❌ Função connectDBEnvironment não disponível</p>";
}

echo "<h2>Informações do Sistema</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Working Directory: " . getcwd() . "</p>";
echo "<p>Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'não definido') . "</p>";
?>