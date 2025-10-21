<?php
// Verificação final - Dashboard produção
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>✅ Verificação Final - Dashboard Produção</h1>";
echo "<p>Data/Hora: " . date('Y-m-d H:i:s') . "</p>";

// Verificar se config carrega
try {
    require_once 'includes/config_env.php';
    echo "<p>✅ Config carregado - Environment: $environment</p>";
} catch (Exception $e) {
    echo "<p>❌ Erro config: " . $e->getMessage() . "</p>";
    exit;
}

// Verificar conexões dos 6 portais
$portais = ['ouvidoria', 'ead', 'processo_seletivo', 'secretaria', 'financeiro', 'exaluno'];
$conexoes_ok = 0;

echo "<h2>Teste de Conexões por Portal</h2>";
foreach ($portais as $portal) {
    try {
        $conn = connectDBEnvironment($portal);
        if ($conn) {
            echo "<p>✅ $portal: Conexão OK</p>";
            $conexoes_ok++;
            
            // Testar consulta básica
            $db_name = getProductionDatabaseName($portal);
            $test_query = "SELECT 1 as test";
            $result = $conn->query($test_query);
            if ($result) {
                echo "<p>&nbsp;&nbsp;↳ Query teste: OK</p>";
            }
            
            $conn->close();
        }
    } catch (Exception $e) {
        echo "<p>❌ $portal: " . $e->getMessage() . "</p>";
    }
}

echo "<h2>Resumo Final</h2>";
echo "<p>Conexões funcionais: $conexoes_ok / " . count($portais) . "</p>";

if ($conexoes_ok === count($portais)) {
    echo '<div style="background: #d4edda; color: #155724; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px;">';
    echo '<h3>🎉 DASHBOARD PRONTO PARA PRODUÇÃO!</h3>';
    echo '<p>Todas as conexões estão funcionando corretamente.</p>';
    echo '</div>';
} else {
    echo '<div style="background: #f8d7da; color: #721c24; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px;">';
    echo '<h3>⚠️ Atenção: Algumas conexões falharam</h3>';
    echo '<p>Verifique as configurações dos portais com problema.</p>';
    echo '</div>';
}

echo "<h2>Links de Teste</h2>";
echo '<p><a href="dashboard.php" style="color: #007bff;">🔗 Dashboard Principal</a></p>';
echo '<p><a href="dashboard_safe_production.php" style="color: #28a745;">🔗 Dashboard Seguro</a></p>';
echo '<p><a href="diagnostico_producao.php" style="color: #ffc107;">🔗 Diagnóstico Completo</a></p>';
?>