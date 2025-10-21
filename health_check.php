<?php
// Verificação rápida de saúde do sistema
// Acesse via: https://gestao-protocolos.drummond.com.br/health_check.php

header('Content-Type: application/json');

$status = [
    'timestamp' => date('Y-m-d H:i:s'),
    'status' => 'OK',
    'checks' => []
];

// 1. Verificar se config_env.php carrega
try {
    require_once 'includes/config_env.php';
    $status['checks']['config'] = 'OK';
    $status['checks']['environment'] = $environment;
} catch (Exception $e) {
    $status['checks']['config'] = 'ERRO: ' . $e->getMessage();
    $status['status'] = 'ERROR';
}

// 2. Verificar se a função connectPortalDiretoria existe
if (function_exists('connectPortalDiretoria')) {
    $status['checks']['function_exists'] = 'OK';
    
    // 3. Testar conexão
    try {
        $conn = connectPortalDiretoria();
        $status['checks']['db_connection'] = 'OK';
        $conn->close();
    } catch (Exception $e) {
        $status['checks']['db_connection'] = 'ERRO: ' . $e->getMessage();
        $status['status'] = 'WARNING';
    }
} else {
    $status['checks']['function_exists'] = 'ERRO: Função não existe';
    $status['status'] = 'ERROR';
}

// 4. Verificar arquivos críticos
$arquivos_criticos = [
    'data/data_ouvidoria.php',
    'data/data_ead.php',
    'get_filtered_data.php'
];

$status['checks']['files'] = [];
foreach ($arquivos_criticos as $arquivo) {
    $status['checks']['files'][$arquivo] = file_exists($arquivo) ? 'OK' : 'MISSING';
    if (!file_exists($arquivo)) {
        $status['status'] = 'WARNING';
    }
}

// 5. Verificar versão do PHP
$status['checks']['php_version'] = PHP_VERSION;

// 6. Verificar se session funciona
session_start();
$_SESSION['test'] = 'OK';
$status['checks']['session'] = isset($_SESSION['test']) ? 'OK' : 'ERRO';

echo json_encode($status, JSON_PRETTY_PRINT);
?>