<?php
require_once 'includes/config.php';

$databases = ['portal_ouvidoria', 'portal_ead', 'portal_processo_seletivo', 'portal_secretaria_academica', 'portal_financeiro', 'portal_exaluno'];

foreach ($databases as $db) {
    echo "=== $db ===\n";
    try {
        $pdo = connectDB($db);
        $stmt = $pdo->query('DESCRIBE chamados');
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($columns as $col) {
            echo "- {$col['Field']} ({$col['Type']})\n";
        }
    } catch (Exception $e) {
        echo "Erro: " . $e->getMessage() . "\n";
    }
    echo "\n";
}
?>