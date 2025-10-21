<?php
/**
 * Script de Verificação de Migração - Homologação Local
 * Verifica se os dados foram migrados corretamente para o ambiente local
 */

echo "🔍 VERIFICANDO MIGRAÇÃO PARA HOMOLOGAÇÃO\n";
echo str_repeat("=", 50) . "\n";
echo "📅 " . date('d/m/Y H:i:s') . "\n\n";

// Configurações locais (homologação)
$local_config = [
    'host' => 'localhost',
    'user' => 'root',
    'password' => '',
    'databases' => [
        'portal_ouvidoria',
        'portal_ead',
        'portal_processo_seletivo',
        'portal_secretaria_academica',
        'portal_financeiro',
        'portal_exaluno'
    ]
];

$totalRecords = 0;
$issues = [];

foreach ($local_config['databases'] as $dbName) {
    echo "🏦 Verificando banco: $dbName\n";

    try {
        $pdo = new PDO("mysql:host={$local_config['host']};dbname=$dbName;charset=utf8",
                      $local_config['user'], $local_config['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Conta registros na tabela principal (chamados)
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM chamados");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = $result['total'];

        echo "   ✅ Conectado - Registros em 'chamados': $count\n";
        $totalRecords += $count;

        // Verifica se há dados recentes (últimos 30 dias)
        $stmt = $pdo->query("SELECT COUNT(*) as recentes FROM chamados WHERE data_abertura >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
        $recentes = $stmt->fetch(PDO::FETCH_ASSOC)['recentes'];
        echo "   📊 Registros recentes (30 dias): $recentes\n";

        if ($count == 0) {
            $issues[] = "$dbName: Nenhum registro encontrado";
        }

    } catch (PDOException $e) {
        echo "   ❌ Erro: " . $e->getMessage() . "\n";
        $issues[] = "$dbName: Falha na conexão - " . $e->getMessage();
    }

    echo "\n";
}

echo "📊 RESUMO DA VERIFICAÇÃO\n";
echo str_repeat("=", 30) . "\n";
echo "📈 Total de registros migrados: $totalRecords\n";

if (empty($issues)) {
    echo "🎉 MIGRAÇÃO BEM-SUCEDIDA!\n";
    echo "✅ Todos os bancos estão acessíveis e contêm dados.\n";
    echo "💡 Próximo passo: Testar o dashboard com dados reais.\n";
} else {
    echo "⚠️ PROBLEMAS ENCONTRADOS:\n";
    foreach ($issues as $issue) {
        echo "   • $issue\n";
    }
    echo "\n🔧 Verifique a migração e tente novamente.\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
?>