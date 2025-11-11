<?php
echo "🔍 VERIFICANDO PORTAL EXALUNO\n";
echo str_repeat("=", 30) . "\n";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=portal_exaluno;charset=utf8', 'root', '');

    $stmt = $pdo->query('DESCRIBE chamados');
    echo "Colunas disponíveis:\n";
    $columns = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $columns[] = $row['Field'];
        echo "  {$row['Field']}\n";
    }

    echo "\nVerificando dados em colunas possíveis:\n";
    $possibleColumns = ['categoria', 'tipo', 'servico', 'manifestacao'];

    foreach ($possibleColumns as $col) {
        if (in_array($col, $columns)) {
            $stmt = $pdo->query("SELECT `$col`, COUNT(*) as count FROM chamados WHERE `$col` IS NOT NULL AND `$col` != '' GROUP BY `$col` ORDER BY count DESC");
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($result)) {
                echo "  ✅ $col: " . count($result) . " tipos encontrados\n";
                foreach (array_slice($result, 0, 3) as $row) {
                    echo "     - {$row[$col]}: {$row['count']}\n";
                }
            } else {
                echo "  ⚠️ $col: existe mas vazia\n";
            }
        } else {
            echo "  ❌ $col: não existe\n";
        }
    }

    echo "\nPrimeiro registro completo:\n";
    $stmt = $pdo->query('SELECT * FROM chamados LIMIT 1');
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    print_r($row);

} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
?>