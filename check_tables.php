<?php
include 'includes/config.php';

echo "<h1>🔍 Verificação da Estrutura das Tabelas</h1>";

$portais = [
    'portal_ead' => 'chamados',
    'portal_ouvidoria' => 'chamados',
    'portal_processo_seletivo' => 'chamados',
    'portal_secretaria_academica' => 'chamados',
    'portal_financeiro' => 'chamados',
    'portal_exaluno' => 'chamados'
];

foreach ($portais as $portal => $table) {
    try {
        $pdo = getConnection($portal);
        $stmt = $pdo->query('DESCRIBE ' . $table);
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<h3>$portal</h3>";
        echo "<p>Colunas relacionadas a data:</p><ul>";
        foreach ($columns as $col) {
            if (strpos($col['Field'], 'data') !== false) {
                echo "<li>{$col['Field']}</li>";
            }
        }
        echo "</ul>";

        // Verificar dados de fechamento
        $stmt2 = $pdo->query("SELECT COUNT(*) as fechados FROM $table WHERE data_encerramento IS NOT NULL");
        $fechados = $stmt2->fetch(PDO::FETCH_ASSOC)['fechados'];
        echo "<p>Registros com data_encerramento: <strong>$fechados</strong></p>";

    } catch (Exception $e) {
        echo "<h3>$portal</h3><p style='color:red'>ERRO: " . $e->getMessage() . "</p>";
    }
}
?>