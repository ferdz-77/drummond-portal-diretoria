<?php
// Script para verificar dados no banco de dados
require_once 'includes/config.php';

echo "Verificando dados no banco de dados...\n\n";

$databases = [
    'portal_ouvidoria' => 'chamados',
    'portal_ead' => 'chamados',
    'portal_processo_seletivo' => 'chamados',
    'portal_secretaria_academica' => 'chamados',
    'portal_financeiro' => 'chamados',
    'portal_exaluno' => 'chamados'
];

foreach ($databases as $db => $table) {
    try {
        $pdo = connectDB($db);
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM $table");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "$db ($table): {$result['total']} registros\n";

        // Verificar registros da semana atual
        $stmt = $pdo->query("SELECT COUNT(*) as semana FROM $table WHERE YEARWEEK(data_abertura, 1) = YEARWEEK(CURDATE(), 1)");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "  - Semana atual: {$result['semana']} registros\n";

        // Verificar registros do mês atual
        $stmt = $pdo->query("SELECT COUNT(*) as mes FROM $table WHERE MONTH(data_abertura) = MONTH(CURDATE()) AND YEAR(data_abertura) = YEAR(CURDATE())");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "  - Mês atual: {$result['mes']} registros\n";

    } catch (Exception $e) {
        echo "$db: ERRO - {$e->getMessage()}\n";
    }
    echo "\n";
}
?>