<?php
/**
 * 🔍 Diagnóstico de Chamados - Portal Diretoria
 * Execute: php diagnostic_chamados.php
 */

// Configurar relatório de erros para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "🔍 DIAGNÓSTICO DE CHAMADOS - PORTAL DIRETORIA\n";
echo "==============================================\n\n";

require_once 'includes/config_env.php';

$portais = [
    'Ouvidoria' => [
        'db' => getProductionDatabaseName('ouvidoria'),
        'table' => 'chamados',
        'columns' => ['id', 'descricao', 'status', 'data_abertura']
    ],
    'EAD' => [
        'db' => getProductionDatabaseName('ead'),
        'table' => 'chamados',
        'columns' => ['id', 'categoria', 'status', 'data_abertura']
    ],
    'Processo Seletivo' => [
        'db' => getProductionDatabaseName('processo_seletivo'),
        'table' => 'chamados',
        'columns' => ['id', 'categoria', 'status', 'data_abertura']
    ],
    'Secretaria' => [
        'db' => getProductionDatabaseName('secretaria'),
        'table' => 'chamados',
        'columns' => ['id', 'categoria', 'status', 'data_abertura']
    ],
    'Financeiro' => [
        'db' => getProductionDatabaseName('financeiro'),
        'table' => 'chamados',
        'columns' => ['id', 'categoria', 'status', 'data_abertura']
    ],
    'Ex-Aluno' => [
        'db' => getProductionDatabaseName('exaluno'),
        'table' => 'chamados',
        'columns' => ['id', 'descricao', 'status', 'data_abertura']
    ]
];

$total_chamados = 0;

foreach ($portais as $nome => $config) {
    echo "📊 {$nome} ({$config['db']}.{$config['table']}):\n";
    echo "   🔍 Iniciando verificação...\n";

    try {
        // Tentar conectar ao banco específico
        $conn = connectDB($config['db']);
        echo "   ✅ Conexão estabelecida\n";

        // Método 1: Tentar SELECT direto na tabela
        $test_query = "SELECT 1 FROM {$config['table']} LIMIT 1";
        $test_result = $conn->query($test_query);
        if ($test_result !== false) {
            echo "   ✅ Tabela '{$config['table']}' acessível\n";

            // Contar registros
            $count_query = "SELECT COUNT(*) as total FROM {$config['table']}";
            try {
                $count_result = $conn->query($count_query);
                if ($count_result !== false) {
                    $count_row = $count_result->fetch(PDO::FETCH_ASSOC);
                    $count = $count_row['total'];
                    echo "   📈 Total de registros: {$count}\n";
                    $total_chamados += $count;

                    // Mostrar últimas 3 registros se houver
                    if ($count > 0) {
                        $order_column = end($config['columns']);
                        echo "   🔍 Tentando ordenar por: {$order_column}\n";
                        
                        // Verificar se a coluna de ordenação existe
                        $check_column_query = "SHOW COLUMNS FROM {$config['table']} LIKE '{$order_column}'";
                    $column_exists = $conn->query($check_column_query);
                    if ($column_exists !== false && $column_exists->rowCount() > 0) {
                        echo "   ✅ Coluna '{$order_column}' existe\n";
                    } else {
                        echo "   ❌ Coluna '{$order_column}' NÃO existe na tabela\n";
                        // Tentar usar 'id' como fallback
                        $order_column = 'id';
                        echo "   🔄 Usando 'id' como alternativa\n";
                    }                        $sample_query = "SELECT " . implode(', ', $config['columns']) . " FROM {$config['table']} ORDER BY {$order_column} DESC LIMIT 3";
                        echo "   🔍 Query: {$sample_query}\n";
                        try {
                            $sample_result = $conn->query($sample_query);
                        } catch (Exception $e) {
                            echo "   ❌ ERRO na query de amostra: " . $e->getMessage() . "\n";
                            $sample_result = false;
                        }
                        if ($sample_result) {
                            echo "   🔍 Últimos registros:\n";
                            while ($row = $sample_result->fetch(PDO::FETCH_ASSOC)) {
                                $data = end($row);
                                $titulo = $row[$config['columns'][1]] ?? $row[$config['columns'][0]] ?? 'N/A';
                                echo "      - ID {$row['id']}: " . substr($titulo, 0, 50) . "... ({$data})\n";
                            }
                        }
                    }
                } else {
                    echo "   ❌ Erro ao contar registros: " . $conn->error . "\n";
                }
            } catch (Exception $e) {
                echo "   ❌ ERRO na contagem: " . $e->getMessage() . "\n";
            }
        } else {
            echo "   ❌ Tabela '{$config['table']}' NÃO acessível: " . $conn->error . "\n";

            // Método 2: Tentar SHOW TABLES
            try {
                $tables_result = $conn->query("SHOW TABLES");
                if ($tables_result) {
                    echo "   📋 Tabelas disponíveis: ";
                    $table_list = [];
                    while ($row = $tables_result->fetch_array()) {
                        $table_list[] = $row[0];
                    }
                    echo implode(', ', $table_list) . "\n";
                } else {
                    echo "   ❌ Não foi possível listar tabelas: " . $conn->error . "\n";
                }
            } catch (Exception $e) {
                echo "   ❌ Erro ao listar tabelas: " . $e->getMessage() . "\n";
            }
        }

        $conn = null; // Fechar conexão PDO

    } catch (Exception $e) {
        echo "   ❌ ERRO GERAL: " . $e->getMessage() . "\n";
        echo "   📍 Continuando para próximo portal...\n";
    }

    echo "   ✅ Finalizado {$nome}\n";
    echo "\n";
}

echo "📊 DEBUG: Total de portais processados: " . count($portais) . "\n";
echo "📊 RESUMO:\n";
echo "==========\n";
echo "Total de chamados encontrados: {$total_chamados}\n";

if ($total_chamados == 0) {
    echo "\n🔍 POSSÍVEIS CAUSAS:\n";
    echo "- As tabelas podem ter nomes diferentes\n";
    echo "- Os bancos podem estar vazios\n";
    echo "- As estruturas das tabelas podem ser diferentes\n";
    echo "\n💡 PRÓXIMOS PASSOS:\n";
    echo "1. Verifique os nomes das tabelas em cada banco\n";
    echo "2. Confirme se há dados nas tabelas\n";
    echo "3. Ajuste os nomes das tabelas/colunas se necessário\n";
} else {
    echo "\n✅ Dados encontrados! O problema pode estar na query do dashboard.\n";
}

echo "\nData do diagnóstico: " . date('Y-m-d H:i:s') . "\n";
?>