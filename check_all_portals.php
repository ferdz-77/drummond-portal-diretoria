<?php
$portais = [
    'portal_ead' => 'categoria',
    'portal_processo_seletivo' => 'tipo',
    'portal_secretaria_academica' => 'tipo',
    'portal_financeiro' => 'tipo',
    'portal_exaluno' => 'tipo'
];

foreach ($portais as $db => $col) {
    echo "\n🏦 $db (coluna: $col)\n";
    echo str_repeat("-", 30) . "\n";

    try {
        $pdo = new PDO("mysql:host=localhost;dbname=$db;charset=utf8", 'root', '');

        // Verificar se a coluna existe
        $stmt = $pdo->query("DESCRIBE chamados");
        $columns = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $columns[] = $row['Field'];
        }

        if (!in_array($col, $columns)) {
            echo "❌ Coluna '$col' não existe. Colunas disponíveis:\n";
            foreach ($columns as $c) {
                if (strpos($c, 'tipo') !== false || strpos($c, 'categoria') !== false || strpos($c, 'servico') !== false) {
                    echo "  - $c\n";
                }
            }
        } else {
            // Verificar se tem dados
            $stmt = $pdo->query("SELECT `$col`, COUNT(*) as count FROM chamados WHERE `$col` IS NOT NULL AND `$col` != '' GROUP BY `$col` ORDER BY count DESC LIMIT 5");
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($result)) {
                echo "⚠️ Coluna '$col' existe mas está vazia\n";
            } else {
                echo "✅ Dados encontrados:\n";
                foreach ($result as $row) {
                    echo "  {$row[$col]}: {$row['count']}\n";
                }
            }
        }

    } catch (Exception $e) {
        echo "❌ Erro: " . $e->getMessage() . "\n";
    }
}
?>