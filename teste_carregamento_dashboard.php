<?php
// Teste simples para verificar se o dashboard pode ser carregado
echo "Testando carregamento do dashboard...<br>";

try {
    // Incluir o dashboard sem executar a saída HTML
    ob_start();
    require_once 'dashboard.php';
    ob_end_clean();
    
    echo "✅ Dashboard carregado com sucesso!<br>";
} catch (Exception $e) {
    echo "❌ Erro ao carregar dashboard: " . $e->getMessage() . "<br>";
}
?>