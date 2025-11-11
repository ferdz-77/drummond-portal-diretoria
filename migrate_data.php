<?php
/**
 * Script de Migração de Dados - Produção para Desenvolvimento
 * Data: 19/09/2025
 *
 * Este script permite migrar dados dos bancos de produção para desenvolvimento
 * para testes mais realistas e validação de métricas.
 */

// ================= CONFIGURAÇÕES =================

// Configurações do banco de PRODUÇÃO
$prod_config = [
    'host' => 'SEU_SERVIDOR_PRODUCAO', // Ex: '192.168.1.100' ou 'producao.drummond.com.br'
    'user' => 'USUARIO_PRODUCAO',
    'password' => 'SENHA_PRODUCAO',
    'databases' => [
        'portal_ouvidoria' => 'portal_ouvidoria',
        'portal_ead' => 'portal_ead',
        'portal_processo_seletivo' => 'portal_processo_seletivo',
        'portal_secretaria_academica' => 'portal_secretaria_academica',
        'portal_financeiro' => 'portal_financeiro',
        'portal_exaluno' => 'portal_exaluno'
    ]
];

// Configurações do banco de DESENVOLVIMENTO (local)
$dev_config = [
    'host' => 'localhost',
    'user' => 'root',
    'password' => '',
    'databases' => [
        'portal_ouvidoria' => 'portal_ouvidoria',
        'portal_ead' => 'portal_ead',
        'portal_processo_seletivo' => 'portal_processo_seletivo',
        'portal_secretaria_academica' => 'portal_secretaria_academica',
        'portal_financeiro' => 'portal_financeiro',
        'portal_exaluno' => 'portal_exaluno'
    ]
];

// ================= FUNÇÕES DE MIGRAÇÃO =================

/**
 * Conecta a um banco de dados
 */
function connectDatabase($host, $user, $password, $dbname) {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Erro ao conectar ao banco '$dbname': " . $e->getMessage() . "\n");
    }
}

/**
 * Lista todas as tabelas de um banco
 */
function getTables($pdo) {
    $stmt = $pdo->query("SHOW TABLES");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

/**
 * Exporta estrutura e dados de uma tabela
 */
function exportTable($pdo, $tableName) {
    echo "📊 Exportando tabela: $tableName\n";

    // Exporta estrutura da tabela
    $stmt = $pdo->query("SHOW CREATE TABLE `$tableName`");
    $createTable = $stmt->fetch(PDO::FETCH_ASSOC)['Create Table'];

    // Exporta dados da tabela
    $stmt = $pdo->query("SELECT * FROM `$tableName`");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        'structure' => $createTable,
        'data' => $data,
        'count' => count($data)
    ];
}

/**
 * Importa estrutura e dados para uma tabela
 */
function importTable($pdo, $tableName, $tableData) {
    echo "📥 Importando tabela: $tableName ({$tableData['count']} registros)\n";

    try {
        // Remove tabela se existir
        $pdo->exec("DROP TABLE IF EXISTS `$tableName`");

        // Cria tabela com estrutura
        $pdo->exec($tableData['structure']);

        // Importa dados se houver
        if ($tableData['count'] > 0) {
            // Prepara statement de inserção
            $columns = array_keys($tableData['data'][0]);
            $placeholders = str_repeat('?,', count($columns) - 1) . '?';

            $stmt = $pdo->prepare("INSERT INTO `$tableName` (`" . implode('`,`', $columns) . "`) VALUES ($placeholders)");

            // Insere cada linha
            foreach ($tableData['data'] as $row) {
                $stmt->execute(array_values($row));
            }
        }

        echo "✅ Tabela $tableName importada com sucesso\n";

    } catch (Exception $e) {
        echo "❌ Erro ao importar tabela $tableName: " . $e->getMessage() . "\n";
    }
}

/**
 * Migra um banco de dados completo
 */
function migrateDatabase($prodConfig, $devConfig, $dbName) {
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "🚀 INICIANDO MIGRAÇÃO: $dbName\n";
    echo str_repeat("=", 60) . "\n";

    try {
        // Conecta aos bancos
        $prodPdo = connectDatabase($prodConfig['host'], $prodConfig['user'], $prodConfig['password'], $dbName);
        $devPdo = connectDatabase($devConfig['host'], $devConfig['user'], $devConfig['password'], $dbName);

        // Lista tabelas da produção
        $tables = getTables($prodPdo);
        echo "📋 Tabelas encontradas: " . implode(', ', $tables) . "\n\n";

        $totalRecords = 0;

        // Migra cada tabela
        foreach ($tables as $table) {
            $tableData = exportTable($prodPdo, $table);
            importTable($devPdo, $table, $tableData);
            $totalRecords += $tableData['count'];
        }

        echo "\n🎉 Migração concluída para $dbName!\n";
        echo "📊 Total de registros migrados: $totalRecords\n";

    } catch (Exception $e) {
        echo "❌ Erro na migração de $dbName: " . $e->getMessage() . "\n";
    }
}

/**
 * Migra todos os bancos
 */
function migrateAllDatabases($prodConfig, $devConfig) {
    echo "🎯 INICIANDO MIGRAÇÃO COMPLETA - PRODUÇÃO → DESENVOLVIMENTO\n";
    echo "📅 Data/Hora: " . date('d/m/Y H:i:s') . "\n\n";

    foreach ($prodConfig['databases'] as $dbName => $dbAlias) {
        migrateDatabase($prodConfig, $devConfig, $dbName);
    }

    echo "\n" . str_repeat("🎉", 30) . "\n";
    echo "✅ MIGRAÇÃO COMPLETA FINALIZADA!\n";
    echo "🎯 Todos os dados foram migrados da produção para desenvolvimento\n";
    echo str_repeat("🎉", 30) . "\n";
}

// ================= EXECUÇÃO =================

// ⚠️ IMPORTANTE: Configure as credenciais de produção antes de executar!

echo "⚠️  ATENÇÃO: Configure as credenciais de produção no arquivo antes de executar!\n\n";

// Para executar a migração, descomente a linha abaixo:
// migrateAllDatabases($prod_config, $dev_config);

echo "💡 Para executar a migração:\n";
echo "   1. Configure as credenciais de produção no topo deste arquivo\n";
echo "   2. Descomente a linha: migrateAllDatabases(\$prod_config, \$dev_config);\n";
echo "   3. Execute: php migrate_data.php\n\n";

echo "🔒 Lembre-se: Este script sobrescreverá os dados de desenvolvimento!\n";

?></content>
<parameter name="filePath">c:\xampp\htdocs\portal_diretoria\migrate_data.php