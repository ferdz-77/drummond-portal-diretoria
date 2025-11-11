<?php
// Arquivo de diagnóstico para produção
// Acessar via: https://gestao-protocolos.drummond.com.br/production_diagnostic.php

// Habilitar exibição de erros para diagnóstico
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Diagnóstico de Produção</h1>";
echo "<p>Data/Hora: " . date('Y-m-d H:i:s') . "</p>";

session_start();

echo "<h2>1. Teste de Sessão</h2>";
// Auto-login para teste
$_SESSION['usuario_logado'] = true;
$_SESSION['usuario_nome'] = 'Diagnostico';
$_SESSION['usuario_id'] = 'admin';
echo "✓ Sessão configurada<br>";

echo "<h2>2. Teste de Inclusão de Arquivos</h2>";
try {
    require_once 'includes/config_env.php';
    echo "✓ config_env.php incluído com sucesso<br>";
    echo "Ambiente: " . $environment . "<br>";
} catch (Exception $e) {
    echo "✗ Erro ao incluir config_env.php: " . $e->getMessage() . "<br>";
    exit;
}

echo "<h2>3. Teste de Função connectPortalDiretoria</h2>";
if (function_exists('connectPortalDiretoria')) {
    echo "✓ Função connectPortalDiretoria existe<br>";
    
    try {
        $conn = connectPortalDiretoria();
        echo "✓ Conexão Portal Diretoria: OK<br>";
        $conn->close();
    } catch (Exception $e) {
        echo "✗ Erro na conexão Portal Diretoria: " . $e->getMessage() . "<br>";
    }
} else {
    echo "✗ Função connectPortalDiretoria NÃO existe<br>";
}

echo "<h2>4. Teste de Credenciais de Banco</h2>";
if (isset($db_credentials)) {
    echo "✓ Array de credenciais existe<br>";
    
    foreach ($db_credentials as $portal => $creds) {
        echo "<strong>$portal:</strong><br>";
        echo "- Host: " . $creds['host'] . "<br>";
        echo "- User: " . $creds['user'] . "<br>";
        echo "- DB: " . $creds['dbname'] . "<br>";
        
        // Testar conexão individual
        try {
            $test_conn = new mysqli($creds['host'], $creds['user'], $creds['pass'], $creds['dbname']);
            if ($test_conn->connect_error) {
                echo "✗ Erro: " . $test_conn->connect_error . "<br>";
            } else {
                echo "✓ Conexão OK<br>";
                $test_conn->close();
            }
        } catch (Exception $e) {
            echo "✗ Erro: " . $e->getMessage() . "<br>";
        }
        echo "<br>";
    }
} else {
    echo "✗ Array de credenciais NÃO existe<br>";
}

echo "<h2>5. Teste dos Arquivos de Dados</h2>";
$arquivos_dados = [
    'data/data_ouvidoria.php',
    'data/data_ead.php', 
    'data/data_processo_seletivo.php',
    'data/data_secretaria.php',
    'data/data_financeiro.php',
    'data/data_exaluno.php'
];

foreach ($arquivos_dados as $arquivo) {
    if (file_exists($arquivo)) {
        echo "✓ $arquivo existe<br>";
        try {
            require_once $arquivo;
            echo "✓ $arquivo incluído com sucesso<br>";
        } catch (Exception $e) {
            echo "✗ Erro ao incluir $arquivo: " . $e->getMessage() . "<br>";
        }
    } else {
        echo "✗ $arquivo NÃO existe<br>";
    }
}

echo "<h2>6. Teste do get_filtered_data.php</h2>";
if (file_exists('get_filtered_data.php')) {
    echo "✓ get_filtered_data.php existe<br>";
    try {
        require_once 'get_filtered_data.php';
        echo "✓ get_filtered_data.php incluído com sucesso<br>";
    } catch (Exception $e) {
        echo "✗ Erro ao incluir get_filtered_data.php: " . $e->getMessage() . "<br>";
    }
} else {
    echo "✗ get_filtered_data.php NÃO existe<br>";
}

echo "<h2>7. Informações do Sistema</h2>";
echo "PHP Version: " . PHP_VERSION . "<br>";
echo "Server: " . $_SERVER['SERVER_SOFTWARE'] ?? 'N/A' . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] ?? 'N/A' . "<br>";
echo "Current Directory: " . getcwd() . "<br>";

echo "<h2>8. Teste da Lista de Bancos</h2>";
if (isset($bancos)) {
    echo "✓ Lista de bancos existe<br>";
    foreach ($bancos as $banco) {
        echo "- " . $banco['portal'] . " (" . $banco['dsn'] . ")<br>";
    }
} else {
    echo "✗ Lista de bancos NÃO existe<br>";
}

echo "<p><strong>Diagnóstico concluído!</strong></p>";
?>