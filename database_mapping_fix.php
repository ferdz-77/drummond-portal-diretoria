<?php
// Função helper para mapear nomes de bancos baseado no ambiente
// Adicionar no config_env.php

// Mapeamento de nomes de bancos por ambiente
function getProductionDatabaseName($development_name) {
    global $environment;
    
    if ($environment !== 'production') {
        return $development_name;
    }
    
    // Mapeamento desenvolvimento → produção
    $database_mapping = [
        'portal_ouvidoria' => 'bdsolicita_atendimento',
        'portal_ead' => 'dbead',
        'portal_processo_seletivo' => 'pseldb',
        'portal_secretaria_academica' => 'dbsecretacad',
        'portal_financeiro' => 'fini',
        'portal_exaluno' => 'bdsolicita_atendimento',
        'portal_diretoria' => 'dbgproto'
    ];
    
    return $database_mapping[$development_name] ?? $development_name;
}

// Wrapper para connectDB que considera o ambiente
function connectDBEnvironment($db_name) {
    $production_name = getProductionDatabaseName($db_name);
    return connectDB($production_name);
}
?>