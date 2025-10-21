<?php
echo "🔍 ESTRUTURA DA TABELA - PORTAL OUVIDORIA\n";
echo str_repeat("=", 40) . "\n";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=portal_ouvidoria;charset=utf8', 'root', '');

    $stmt = $pdo->query('DESCRIBE chamados');
    echo "Colunas da tabela 'chamados':\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  {$row['Field']} - {$row['Type']}\n";
    }

    echo "\n📋 Primeiros 3 registros:\n";
    $stmt = $pdo->query('SELECT * FROM chamados LIMIT 3');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID {$row['id']}: " . json_encode($row) . "\n";
    }

} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
?>