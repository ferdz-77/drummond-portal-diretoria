<?php
echo "🔍 COLUNAS PORTAL_EAD\n";
echo str_repeat("=", 30) . "\n";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=portal_ead;charset=utf8', 'root', '');

    $stmt = $pdo->query('DESCRIBE chamados');
    echo "Colunas disponíveis:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  {$row['Field']}\n";
    }

    // Verificar algumas colunas que podem ter tipos
    $possibleColumns = ['tipo', 'servico', 'categoria', 'manifestacao'];
    echo "\nVerificando colunas possíveis para tipos:\n";

    foreach ($possibleColumns as $col) {
        try {
            $stmt = $pdo->query("SELECT DISTINCT `$col` FROM chamados WHERE `$col` IS NOT NULL AND `$col` != '' LIMIT 5");
            $values = $stmt->fetchAll(PDO::FETCH_COLUMN);
            if (!empty($values)) {
                echo "  $col: " . implode(', ', array_slice($values, 0, 3)) . (count($values) > 3 ? '...' : '') . "\n";
            } else {
                echo "  $col: vazia\n";
            }
        } catch (Exception $e) {
            echo "  $col: não existe\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
?>