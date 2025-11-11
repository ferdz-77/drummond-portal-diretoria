<?php
echo "🔍 VERIFICANDO STATUS - SECRETARIA ACADÊMICA\n";
echo str_repeat("=", 45) . "\n";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=portal_secretaria_academica;charset=utf8', 'root', '');

    $stmt = $pdo->query('SELECT status, COUNT(*) as count FROM chamados GROUP BY status ORDER BY count DESC');
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Distribuição de status:\n";
    $total = 0;
    foreach ($result as $row) {
        echo "  {$row['status']}: {$row['count']}\n";
        $total += $row['count'];
    }

    echo "\n📊 Total de registros: $total\n";

    if (count($result) === 1) {
        echo "⚠️ Apenas 1 status encontrado. Isso pode indicar que todos os chamados estão no mesmo status.\n";
    } else {
        echo "✅ Múltiplos status encontrados - gráfico deve mostrar distribuição correta.\n";
    }

    // Verificar se há chamados recentes
    $stmt = $pdo->query('SELECT COUNT(*) as recentes FROM chamados WHERE data_abertura >= DATE_SUB(NOW(), INTERVAL 30 DAY)');
    $recentes = $stmt->fetch(PDO::FETCH_ASSOC)['recentes'];
    echo "📅 Chamados nos últimos 30 dias: $recentes\n";

} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
?>