<?php
$databases = [
    'portal_ouvidoria',
    'portal_ead',
    'portal_processo_seletivo',
    'portal_secretaria_academica',
    'portal_financeiro',
    'portal_exaluno'
];

foreach ($databases as $db) {
    echo "\n🏦 $db\n";
    echo str_repeat("-", 20) . "\n";

    try {
        $pdo = new PDO("mysql:host=localhost;dbname=$db;charset=utf8", 'root', '');

        $stmt = $pdo->query('DESCRIBE chamados');
        $columns = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $columns[] = $row['Field'];
        }

        // Verificar colunas importantes
        $data_abertura = in_array('data_abertura', $columns) ? '✅' : '❌';
        $data_encerramento = in_array('data_encerramento', $columns) ? '✅' : '❌';
        $data_resposta = in_array('data_resposta', $columns) ? '✅' : '❌';
        $status = in_array('status', $columns) ? '✅' : '❌';

        echo "data_abertura: $data_abertura\n";
        echo "data_encerramento: $data_encerramento\n";
        echo "data_resposta: $data_resposta\n";
        echo "status: $status\n";

        // Contar registros com data_encerramento
        if ($data_encerramento === '✅') {
            $stmt = $pdo->query('SELECT COUNT(*) as total, COUNT(data_encerramento) as encerrados FROM chamados');
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "Total registros: {$result['total']}\n";
            echo "Com data_encerramento: {$result['encerrados']}\n";
        }

    } catch (Exception $e) {
        echo "❌ Erro: " . $e->getMessage() . "\n";
    }
}
?>