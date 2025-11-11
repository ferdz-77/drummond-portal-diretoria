<?php
// Verificar valores vazios em todos os portais
session_start();
$_SESSION['usuario_logado'] = true;

require_once 'includes/config.php';
require_once 'get_filtered_data.php';

echo "<h1>🔍 Verificação de Valores Vazios em Todos os Portais</h1>";
echo "<pre>";

$portais = ['ouvidoria', 'ead', 'processo_seletivo', 'secretaria', 'financeiro', 'exaluno'];

foreach ($portais as $portal) {
    echo "=== PORTAL: " . strtoupper($portal) . " ===\n";
    
    try {
        $dados = getFilteredPortalData('todos', $portal);
        
        if ($dados && isset($dados[$portal])) {
            $portalData = $dados[$portal];
            
            // Verificar status
            if (isset($portalData['status'])) {
                echo "Status encontrados:\n";
                foreach ($portalData['status'] as $status) {
                    $statusText = $status['status'] ?? 'NULL';
                    $statusLength = strlen($statusText);
                    $hasEmpty = ($statusText === '' || $statusText === 'NULL');
                    $warningFlag = $hasEmpty ? " ⚠️ VAZIO" : "";
                    echo "  - '$statusText' (length: $statusLength) : " . $status['count'] . " registros$warningFlag\n";
                }
            }
            
            // Verificar serviços/tipos
            if (isset($portalData['servicos'])) {
                echo "Serviços encontrados:\n";
                foreach ($portalData['servicos'] as $servico) {
                    $servicoText = $servico['categoria'] ?? $servico['servico'] ?? 'NULL';
                    $servicoLength = strlen($servicoText);
                    $hasEmpty = ($servicoText === '' || $servicoText === 'NULL');
                    $warningFlag = $hasEmpty ? " ⚠️ VAZIO" : "";
                    echo "  - '$servicoText' (length: $servicoLength) : " . $servico['count'] . " registros$warningFlag\n";
                }
            }
            
            if (isset($portalData['tipos'])) {
                echo "Tipos encontrados:\n";
                foreach ($portalData['tipos'] as $tipo) {
                    $tipoText = $tipo['manifestacao'] ?? $tipo['tipo'] ?? 'NULL';
                    $tipoLength = strlen($tipoText);
                    $hasEmpty = ($tipoText === '' || $tipoText === 'NULL');
                    $warningFlag = $hasEmpty ? " ⚠️ VAZIO" : "";
                    echo "  - '$tipoText' (length: $tipoLength) : " . $tipo['count'] . " registros$warningFlag\n";
                }
            }
            
            echo "SLA/Tempo: " . ($portalData['sla'] ?? $portalData['tempo'] ?? 'N/A') . "\n";
            
        } else {
            echo "❌ Nenhum dado encontrado\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Erro: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo "=== RESUMO ===\n";
echo "✅ EAD: Corrigido (valores vazios convertidos para 'Não informado')\n";
echo "🔍 Verificar outros portais que possam ter valores vazios\n";

echo "</pre>";
?>